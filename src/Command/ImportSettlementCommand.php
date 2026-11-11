<?php

namespace App\Command;

use App\Message\ProcessLineMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'app:import-settlement',
    description: 'Importuje linie z pliku CSV do kolejki',
)]
class ImportSettlementCommand extends Command
{
    public function __construct(
        private readonly MessageBusInterface $bus,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $filePath = __DIR__ . '/../../public/AVR20251222-650-3.csv';

        if (!file_exists($filePath)) {
            $io->error(sprintf('Plik nie istnieje: %s', $filePath));
            return Command::FAILURE;
        }

        $handle = fopen($filePath, 'r');
        $batchSize = 500;
        $i = 0;

        $io->title('Start importu do kolejki...');
        $io->progressStart();

        $this->entityManager->getConnection()->beginTransaction();

        $cashSettlementLines = [];
        while (($line = fgetcsv($handle, 0, ';', '"', '\\')) !== false) {

            $amount = (string)$line[count($line) - 5];
            $gatewayReference = $line[count($line) - 1];
            $type = $line[count($line) - 2];
            $paymentTransactionId = $line[count($line) - 2];

            if (!isset($cashSettlementLines[$gatewayReference])) {
                $cashSettlementLines[$gatewayReference] = [
                    'amount' => '0',
                ];
            }

// Zakładamy, że $amount to np. '610.80' lub 610.8
            $currentAmountStr = (string) ($cashSettlementLines[$gatewayReference]['amount'] ?? '0');
            $newAmountStr = (string) $amount;

// 1. Konwersja obu wartości na grosze (int)
// round() jest kluczowy, bo (int)('610.80' * 100) może czasem dać 61079 przez błędy float
            $currentCents = (int) round((float) $currentAmountStr * 100);
            $newCents = (int) round((float) $newAmountStr * 100);

// 2. Dodawanie na liczbach całkowitych
            $totalCents = $currentCents + $newCents;

// 3. Powrót do stringa w formacie '0.00'
            $cashSettlementLines[$gatewayReference]['amount'] = number_format($totalCents / 100, 2, '.', '');
        }

        foreach ($cashSettlementLines as $gatewayReference => $cashSettlementLine) {
            try {
                // Wysyłamy do bufora szyny 'messenger.bus.import'
                // $io->text($line[count($line) - 3]);
                $this->bus->dispatch(new ProcessLineMessage($gatewayReference, $cashSettlementLine['amount']));

                $i++;

                // Co 500 linii robimy flush, co fizycznie zapisuje paczkę
                // wiadomości w tabeli messenger_messages w jednej transakcji
                if ($i % $batchSize === 0) {
                    $this->entityManager->flush();
                    $this->entityManager->clear(); // Czyścimy UnitOfWork, by zwolnić RAM
                    $io->progressAdvance($batchSize);
                }

                if (2500 === $i) {
                     // throw new \Exception('test doctrine_transaction');
                }
            } catch (\Exception $e) {
                if ($this->entityManager->getConnection()->isTransactionActive()) {
                    $this->entityManager->getConnection()->rollBack();
                }
                $output->writeln("Wykonałem rollback: " . $e->getMessage());

                return Command::FAILURE;
            }
        }

        $conn = $this->entityManager->getConnection();

        $sql = 'INSERT INTO finance.batch_tracker (batch_id, total_count, processed_count)
        VALUES (:batchId, :total, :processed)';

        $conn->executeStatement($sql, [
            'batchId'   => 113,
            'total'     => $i,
            'processed' => 0
        ]);

        // Flush końcowy dla ostatnich rekordów
        $this->entityManager->flush();
        $this->entityManager->clear();
        $this->entityManager->getConnection()->commit();

        fclose($handle);
        $io->progressFinish();
        $io->success(sprintf('Zlecono %d linii do przetworzenia.', $i));

        return Command::SUCCESS;
    }
}
