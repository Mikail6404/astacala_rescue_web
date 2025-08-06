<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\GibranReportService;
use App\Services\AstacalaApiClient;
use App\Services\GibranAuthService;

try {
    $apiClient = new AstacalaApiClient();
    $authService = new GibranAuthService($apiClient);
    $reportService = new GibranReportService($apiClient, $authService);

    echo "Testing GibranReportService::getAllReports()..." . PHP_EOL;
    $result = $reportService->getAllReports();

    echo 'getAllReports() - Result keys: ' . implode(', ', array_keys($result)) . PHP_EOL;
    echo 'getAllReports() - Success type: ' . gettype($result['success']) . PHP_EOL;
    echo 'getAllReports() - Success value: ' . ($result['success'] ? 'TRUE' : 'FALSE') . PHP_EOL;

    echo PHP_EOL . "Testing GibranReportService::deleteReport()..." . PHP_EOL;
    // This will fail because we need a valid ID, but let's see the error format
    try {
        $deleteResult = $reportService->deleteReport(999999);
        echo 'deleteReport() - Result keys: ' . implode(', ', array_keys($deleteResult)) . PHP_EOL;
        if (isset($deleteResult['success'])) {
            echo 'deleteReport() - Success type: ' . gettype($deleteResult['success']) . PHP_EOL;
        }
        if (isset($deleteResult['status'])) {
            echo 'deleteReport() - Status type: ' . gettype($deleteResult['status']) . PHP_EOL;
        }
    } catch (Exception $e) {
        echo 'deleteReport() - Exception: ' . $e->getMessage() . PHP_EOL;
    }

    if (isset($result['data']) && is_array($result['data']) && count($result['data']) > 0) {
        echo 'First item keys: ' . implode(', ', array_keys($result['data'][0])) . PHP_EOL;
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
    echo 'File: ' . $e->getFile() . ':' . $e->getLine() . PHP_EOL;
}
