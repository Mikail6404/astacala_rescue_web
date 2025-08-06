<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel for testing
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\GibranAuthService;
use App\Services\AstacalaApiClient;
use App\Services\GibranDashboardService;

echo "=== Web Application API Integration Test ===\n";

try {
    // Test 1: API Client Configuration
    echo "\n🔧 Testing API Client Configuration...\n";
    $apiClient = new AstacalaApiClient();

    // Test endpoint building
    $testEndpoint = $apiClient->getEndpoint('auth', 'login');
    echo "✅ Auth login endpoint: $testEndpoint\n";

    $dashboardEndpoint = $apiClient->getEndpoint('gibran', 'dashboard_statistics');
    echo "✅ Dashboard statistics endpoint: $dashboardEndpoint\n";

    // Test 2: Public API Request (Health Check)
    echo "\n🏥 Testing Backend API Health Check...\n";
    try {
        $healthResponse = $apiClient->publicRequest('GET', '/api/v1/health');
        echo "✅ Health check successful: " . json_encode($healthResponse) . "\n";
    } catch (Exception $e) {
        echo "❌ Health check failed: " . $e->getMessage() . "\n";
    }

    // Test 3: Authentication Test
    echo "\n🔐 Testing Authentication...\n";
    $authService = new GibranAuthService($apiClient);

    // Test with known working credentials
    $credentials = [
        'email' => 'volunteer@mobile.test',
        'password' => 'password123'
    ];

    $authResult = $authService->login($credentials);

    if ($authResult['success']) {
        echo "✅ Authentication successful!\n";
        echo "   User ID: " . $authResult['user']['id'] . "\n";
        echo "   User Name: " . $authResult['user']['name'] . "\n";
        echo "   User Role: " . $authResult['user']['role'] . "\n";
        echo "   Token: " . substr($authResult['token'], 0, 20) . "...\n";

        // Test 4: Authenticated Request
        echo "\n🔒 Testing Authenticated Request (Dashboard Statistics)...\n";
        $dashboardService = new GibranDashboardService($apiClient);
        $statisticsResult = $dashboardService->getStatistics();

        if ($statisticsResult['success']) {
            echo "✅ Dashboard statistics retrieved successfully!\n";
            echo "   Data: " . json_encode($statisticsResult['data']) . "\n";
        } else {
            echo "❌ Dashboard statistics failed: " . $statisticsResult['message'] . "\n";
        }

        // Test 5: User Profile Request
        echo "\n👤 Testing User Profile Request...\n";
        try {
            $profileEndpoint = $apiClient->getEndpoint('auth', 'me');
            $profileResponse = $apiClient->authenticatedRequest('GET', $profileEndpoint);
            echo "✅ Profile request successful: " . json_encode($profileResponse) . "\n";
        } catch (Exception $e) {
            echo "❌ Profile request failed: " . $e->getMessage() . "\n";
        }
    } else {
        echo "❌ Authentication failed: " . $authResult['message'] . "\n";
        echo "   Error details: " . ($authResult['error'] ?? 'No additional error info') . "\n";
    }

    // Test 6: Reports API Test
    echo "\n📊 Testing Reports API...\n";
    try {
        $reportsEndpoint = $apiClient->getEndpoint('reports', 'index');
        $reportsResponse = $apiClient->authenticatedRequest('GET', $reportsEndpoint);
        echo "✅ Reports request successful: " . json_encode($reportsResponse) . "\n";
    } catch (Exception $e) {
        echo "❌ Reports request failed: " . $e->getMessage() . "\n";
    }
} catch (Exception $e) {
    echo "❌ Test failed with exception: " . $e->getMessage() . "\n";
    echo "   Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== Test Complete ===\n";
