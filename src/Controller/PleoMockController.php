<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[Route('/v2', name: 'pleo_mock_')]
class PleoMockController extends AbstractController
{
    private string $dataDir;

    public function __construct()
    {
        // Ścieżka do folderu z plikami JSON
        $this->dataDir = __DIR__ . '/../../public/data';
    }

    #[Route('/export-jobs/{exportJobId}', name: 'get_export_job', methods: ['GET'])]
    public function getExportJob(string $exportJobId): JsonResponse
    {
        $filePath = $this->dataDir . '/export_job.json';

        return $this->createJsonResponseFromFile($filePath);
    }

    #[Route('/export-jobs/{exportJobId}/items', name: 'get_export_items', methods: ['GET'])]
    public function getExportItems(string $exportJobId): JsonResponse
    {
        $filePath = $this->dataDir . '/export_items.json';

        return $this->createJsonResponseFromFile($filePath);
    }

    /**
     * Pomocnicza metoda do wczytywania pliku i zwracania JSON
     */
    private function createJsonResponseFromFile(string $path): JsonResponse
    {
        if (!file_exists($path)) {
            return new JsonResponse(['error' => 'Data file not found'], 404);
        }

        $content = file_get_contents($path);

        // Dekodujemy i kodujemy ponownie, aby upewnić się, że to poprawny JSON
        // i Symfony mogło ustawić odpowiednie nagłówki Content-Type
        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return new JsonResponse(['error' => 'Invalid JSON format in file'], 500);
        }

        return new JsonResponse($data);
    }
}
