<?php

namespace App\Repository\External;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class PgNetChartOfAccountsRepository
{
    private Connection $connection;

    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly EntityManagerInterface $entityManager,
    ) {
        $this->connection = $this->entityManager->getConnection();
    }

    public function getLatestChartOfAccounts(): ?array
    {
        // 1. Pobierz ID żądania (return_message) z logów pg_cron
        $cronSql = "
            SELECT return_message
            FROM cron.job_run_details
            WHERE jobid = (SELECT jobid FROM cron.job WHERE jobname = :job_name)
              AND status = 'succeeded'
            ORDER BY end_time DESC
            LIMIT 1
        ";

        $requestIdRaw = $this->connection->fetchOne($cronSql, ['job_name' => 'fetch-pleo-accounts']);

        // Sprawdź, czy mamy ID (Yoda condition)
        if (null === $requestIdRaw || false === $requestIdRaw) {
            return null;
        }

        // return_message zawiera np. "123" lub "SELECT 123" w zależności od wersji/wywołania
        // rzutujemy na int dla bezpieczeństwa
        $requestId = (int) filter_var($requestIdRaw, FILTER_SANITIZE_NUMBER_INT);

        if (0 === $requestId) {
            return null;
        }

        // 2. Pobierz konkretną odpowiedź z pg_net używając ID
        $netSql = "
            SELECT content
            FROM net.http_response
            WHERE id = :request_id
              AND status_code = 200
        ";

        $result = $this->connection->fetchOne($netSql, ['request_id' => $requestId]);

        if (null === $result || false === $result) {
            return null;
        }

        return json_decode($result, true);
    }
}
