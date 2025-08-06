<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Http\Controllers\PelaporanController;
use App\Services\AstacalaApiClient;
use App\Services\GibranAuthService;
use App\Services\GibranReportService;

try {
    echo 'Testing PelaporanController::membacaDataPelaporan()...'.PHP_EOL;

    // Create dependencies
    $apiClient = new AstacalaApiClient;
    $authService = new GibranAuthService($apiClient);
    $reportService = new GibranReportService($apiClient, $authService);

    // Create controller
    $controller = new PelaporanController($reportService, $authService);

    // Test the method
    $response = $controller->membacaDataPelaporan();

    if ($response instanceof \Illuminate\View\View) {
        echo '✅ SUCCESS: membacaDataPelaporan() returns a View'.PHP_EOL;
        echo 'View name: '.$response->name().PHP_EOL;
        $data = $response->getData();
        echo 'Data variable exists: '.(isset($data['data']) ? 'YES' : 'NO').PHP_EOL;
        if (isset($data['data'])) {
            echo 'Data count: '.count($data['data']).PHP_EOL;
        }
    } else {
        echo '❌ FAIL: membacaDataPelaporan() returns: '.get_class($response).PHP_EOL;
    }

    echo PHP_EOL.'Testing PelaporanController::menampilkanNotifikasiPelaporanMasuk()...'.PHP_EOL;

    $response2 = $controller->menampilkanNotifikasiPelaporanMasuk();

    if ($response2 instanceof \Illuminate\View\View) {
        echo '✅ SUCCESS: menampilkanNotifikasiPelaporanMasuk() returns a View'.PHP_EOL;
        echo 'View name: '.$response2->name().PHP_EOL;
        $data2 = $response2->getData();
        echo 'Data variable exists: '.(isset($data2['data']) ? 'YES' : 'NO').PHP_EOL;
        if (isset($data2['data'])) {
            echo 'Data count: '.count($data2['data']).PHP_EOL;
        }
    } else {
        echo '❌ FAIL: menampilkanNotifikasiPelaporanMasuk() returns: '.get_class($response2).PHP_EOL;
    }
} catch (Exception $e) {
    echo 'Error: '.$e->getMessage().PHP_EOL;
    echo 'File: '.$e->getFile().':'.$e->getLine().PHP_EOL;
}
