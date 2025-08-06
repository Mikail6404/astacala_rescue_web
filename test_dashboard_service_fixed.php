<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel for testing
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\AstacalaApiClient;
use App\Services\GibranAuthService;
use App\Services\GibranDashboardService;

echo "=== Dashboard Service Test (After Fix) ===\n";

try {
    $apiClient = new AstacalaApiClient;
    $authService = new GibranAuthService($apiClient);
    $dashboardService = new GibranDashboardService($apiClient);

    // Login first
    $credentials = [
        'email' => 'volunteer@mobile.test',
        'password' => 'password123',
    ];

    $authResult = $authService->login($credentials);

    if ($authResult['success']) {
        echo "✅ Authentication successful\n\n";

        // Test dashboard statistics
        echo "📊 Testing Dashboard Statistics Service:\n";
        $statisticsResult = $dashboardService->getStatistics();

        if ($statisticsResult['success']) {
            echo "✅ Dashboard statistics retrieved successfully!\n";
            echo '   Total Reports: '.$statisticsResult['data']['total_pelaporan']."\n";
            echo '   Pending Reports: '.$statisticsResult['data']['pelaporan_pending']."\n";
            echo '   Verified Reports: '.$statisticsResult['data']['pelaporan_verified']."\n";
            echo "   Today's Reports: ".$statisticsResult['data']['pelaporan_hari_ini']."\n";
            echo '   Total Victims: '.$statisticsResult['data']['total_korban']."\n";

            echo "   Severity Breakdown:\n";
            foreach ($statisticsResult['data']['breakdown_skala'] as $level => $count) {
                echo "     - $level: $count\n";
            }

            echo "   Recent Reports:\n";
            foreach ($statisticsResult['data']['pelaporan_terbaru'] as $report) {
                echo "     - ID {$report['id']}: {$report['judul']} in {$report['lokasi']} by {$report['pelapor']} ({$report['waktu']})\n";
            }
        } else {
            echo '❌ Dashboard statistics failed: '.$statisticsResult['message']."\n";
        }

        echo "\n📰 Testing News/Berita Bencana Service:\n";
        $newsResult = $dashboardService->getBeritaBencana();

        if ($newsResult['success']) {
            echo "✅ News data retrieved successfully!\n";
            echo '   Data: '.json_encode($newsResult['data'])."\n";
        } else {
            echo '❌ News data failed: '.$newsResult['message']."\n";
        }

        echo "\n🔍 Testing System Overview Service:\n";
        $overviewResult = $dashboardService->getSystemOverview();

        if ($overviewResult['success']) {
            echo "✅ System overview retrieved successfully!\n";
            echo '   Data: '.json_encode($overviewResult['data'])."\n";
        } else {
            echo '❌ System overview failed: '.$overviewResult['message']."\n";
        }
    } else {
        echo '❌ Authentication failed: '.$authResult['message']."\n";
    }
} catch (Exception $e) {
    echo '❌ Test failed: '.$e->getMessage()."\n";
}

echo "\n=== Test Complete ===\n";
