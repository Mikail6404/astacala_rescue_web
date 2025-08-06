<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\AstacalaApiClient;
use App\Services\GibranDashboardService;
use Illuminate\Support\Facades\Auth;

echo "=== WEB APP API CONNECTIVITY TEST ===\n\n";

try {
    // Test 1: API Client Instantiation
    echo "1. Testing API client instantiation...\n";
    $apiClient = new AstacalaApiClient();
    echo "   ✅ API client created successfully\n\n";

    // Test 2: Check endpoint generation
    echo "2. Testing endpoint generation...\n";
    $userStatsEndpoint = $apiClient->getEndpoint('users', 'statistics');
    echo "   Users statistics endpoint: $userStatsEndpoint\n";

    $adminListEndpoint = $apiClient->getEndpoint('users', 'admin_list');
    echo "   Admin list endpoint: $adminListEndpoint\n";

    $gibranStatsEndpoint = $apiClient->getEndpoint('gibran', 'dashboard_statistics');
    echo "   Gibran dashboard stats endpoint: $gibranStatsEndpoint\n\n";

    // Test 3: Direct API calls without authentication
    echo "3. Testing direct API calls (no auth)...\n";
    try {
        $response = $apiClient->publicRequest('GET', $userStatsEndpoint);
        echo "   User stats response: " . json_encode($response) . "\n";
    } catch (Exception $e) {
        echo "   ❌ User stats error: " . $e->getMessage() . "\n";
    }

    // Test 4: Test with admin token
    echo "\n4. Testing with admin authentication...\n";

    // Get admin token for mikailadmin@admin.astacala.local
    $loginUrl = $apiClient->getEndpoint('auth', 'login');
    echo "   Login endpoint: $loginUrl\n";

    $loginData = [
        'email' => 'mikailadmin@admin.astacala.local',
        'password' => 'mikailadmin'
    ];

    try {
        $loginResponse = $apiClient->publicRequest('POST', $loginUrl, $loginData);
        echo "   Login response: " . json_encode($loginResponse) . "\n";

        if (isset($loginResponse['data']['tokens']['accessToken'])) {
            $token = $loginResponse['data']['tokens']['accessToken'];
            $user = $loginResponse['data']['user'];
            echo "   ✅ Got admin token: " . substr($token, 0, 20) . "...\n";

            // Store token using the proper API client method
            $apiClient->storeToken($token, $user);

            // Test authenticated user stats
            $authUserStats = $apiClient->authenticatedRequest('GET', $userStatsEndpoint);
            echo "   Authenticated user stats: " . json_encode($authUserStats) . "\n";

            // Test authenticated admin list
            $authAdminList = $apiClient->authenticatedRequest('GET', $adminListEndpoint);
            echo "   Authenticated admin list: " . json_encode($authAdminList) . "\n";
        } else {
            echo "   ❌ No access token in login response\n";
        }
    } catch (Exception $e) {
        echo "   ❌ Login error: " . $e->getMessage() . "\n";
    }

    // Test 5: Dashboard Service
    echo "\n5. Testing Dashboard Service...\n";
    $dashboardService = new GibranDashboardService($apiClient);

    try {
        $stats = $dashboardService->getStatistics();
        echo "   Dashboard statistics: " . json_encode($stats) . "\n";
    } catch (Exception $e) {
        echo "   ❌ Dashboard stats error: " . $e->getMessage() . "\n";
    }
} catch (Exception $e) {
    echo "❌ Fatal error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== API CONNECTIVITY TEST COMPLETE ===\n";
