<?php

namespace App\Message;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ProcessLineMessageHandler
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(ProcessLineMessage $message)
    {
        try {
            // ... do some work - like sending an SMS message!
            $this->logger->info('MessageDebug', [
                'getPaymentTransactionId' => $message->getPaymentTransactionId(),
                'getAmount' => $message->getAmount(),
            ]);
        } finally {
            // To wykona się zawsze, niezależnie od sukcesu/porażki
            $batchId = 113;

            $conn = $this->entityManager->getConnection();

            $sql = 'UPDATE finance.batch_tracker SET processed_count = processed_count + 1 WHERE batch_id = :batchId';

            // DBAL sam zadba o odpowiednie bindowanie typów,
            // rzutowanie ::bigint w SQL zazwyczaj nie jest konieczne, jeśli przekażesz int.
            $conn->executeStatement($sql, [
                'batchId' => $batchId,
            ]);
        }
    }
}
