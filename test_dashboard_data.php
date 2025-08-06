<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\AstacalaApiClient;
use App\Services\GibranDashboardService;
use Illuminate\Support\Facades\Session;

echo "=== TESTING DASHBOARD DATA FETCHING ===\n\n";

try {
    // Simulate a logged-in session (like what happens after web login)
    $apiClient = new AstacalaApiClient();
    $dashboardService = new GibranDashboardService($apiClient);

    echo "1. Checking current token status...\n";
    $currentToken = $apiClient->getStoredToken();
    echo "   Current stored token: " . ($currentToken ? "✅ Present (" . substr($currentToken, 0, 20) . "...)" : "❌ None") . "\n";

    if (!$currentToken) {
        echo "\n2. Simulating fresh login...\n";

        // Login to get a fresh token
        $loginUrl = $apiClient->getEndpoint('auth', 'login');
        $loginData = [
            'email' => 'mikailadmin@admin.astacala.local',
            'password' => 'mikailadmin'
        ];

        $loginResponse = $apiClient->publicRequest('POST', $loginUrl, $loginData);

        if (isset($loginResponse['data']['tokens']['accessToken'])) {
            $token = $loginResponse['data']['tokens']['accessToken'];
            $user = $loginResponse['data']['user'];
            $apiClient->storeToken($token, $user);
            echo "   ✅ Fresh token stored\n";
        } else {
            echo "   ❌ Failed to get token\n";
            return;
        }
    }

    echo "\n3. Testing dashboard data fetching...\n";

    // Test statistics
    echo "   Testing getStatistics()...\n";
    $stats = $dashboardService->getStatistics();
    echo "   Result: " . ($stats['success'] ? "✅ Success" : "❌ Failed") . "\n";
    if ($stats['success']) {
        echo "     Data: " . count($stats['data']) . " fields\n";
    } else {
        echo "     Error: " . $stats['message'] . "\n";
    }

    // Test berita bencana
    echo "\n   Testing getBeritaBencana()...\n";
    $berita = $dashboardService->getBeritaBencana();
    echo "   Result: " . ($berita['success'] ? "✅ Success" : "❌ Failed") . "\n";
    if (!$berita['success']) {
        echo "     Error: " . $berita['message'] . "\n";
    }

    // Test system overview
    echo "\n   Testing getSystemOverview()...\n";
    $overview = $dashboardService->getSystemOverview();
    echo "   Result: " . ($overview['success'] ? "✅ Success" : "❌ Failed") . "\n";
    if (!$overview['success']) {
        echo "     Error: " . $overview['message'] . "\n";
    }

    // Test individual API endpoints directly
    echo "\n4. Testing individual API endpoints...\n";

    $endpoints = [
        'users/statistics' => $apiClient->getEndpoint('users', 'statistics'),
        'users/admin_list' => $apiClient->getEndpoint('users', 'admin_list'),
        'gibran/dashboard_statistics' => $apiClient->getEndpoint('gibran', 'dashboard_statistics'),
    ];

    foreach ($endpoints as $name => $endpoint) {
        echo "   Testing $name ($endpoint)...\n";
        try {
            $response = $apiClient->authenticatedRequest('GET', $endpoint);
            $success = isset($response['success']) && $response['success'];
            echo "     Result: " . ($success ? "✅ Success" : "❌ Failed") . "\n";
            if (!$success && isset($response['message'])) {
                echo "     Error: " . $response['message'] . "\n";
            }
        } catch (Exception $e) {
            echo "     ❌ Exception: " . $e->getMessage() . "\n";
        }
    }

    echo "\n5. Session information...\n";
    echo "   Session ID: " . session_id() . "\n";
    echo "   JWT storage key: " . config('astacala_api.jwt.storage_key') . "\n";
    echo "   Session has JWT: " . (Session::has(config('astacala_api.jwt.storage_key')) ? "✅ Yes" : "❌ No") . "\n";

    $sessionJWT = Session::get(config('astacala_api.jwt.storage_key'));
    if ($sessionJWT) {
        echo "   Session JWT: " . substr($sessionJWT, 0, 20) . "...\n";
    }
} catch (Exception $e) {
    echo "❌ Test error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== DASHBOARD DATA TEST COMPLETE ===\n";
