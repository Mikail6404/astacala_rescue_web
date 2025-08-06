<?php

echo "=== Testing Status of Remaining Issues ===\n\n";

// Include Laravel bootstrap
require_once __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 ISSUE 1: Reports Controller 500 Error\n";
echo "===========================================\n";

// Test the problematic route
try {
    echo "Testing current /Pelaporan route configuration...\n";

    // Check what the current route is pointing to
    $routes = app('router')->getRoutes();
    $pelaporanRoute = null;

    foreach ($routes as $route) {
        if ($route->uri() === 'Pelaporan') {
            $pelaporanRoute = $route;
            break;
        }
    }

    if ($pelaporanRoute) {
        echo '   Current route action: '.$pelaporanRoute->getActionName()."\n";

        // Check if it's a closure (problematic) or controller method
        if (strpos($pelaporanRoute->getActionName(), 'Closure') !== false) {
            echo "❌ ISSUE CONFIRMED: Route uses closure, bypasses controller\n";
            echo "   Problem: View expects data that controller would provide\n";
            echo "   Solution needed: Change route to use PelaporanController::membacaDataPelaporan\n";
        } else {
            echo "✅ Route correctly points to controller method\n";
        }
    } else {
        echo "❓ No /Pelaporan route found\n";
    }
} catch (Exception $e) {
    echo '❌ Error checking route: '.$e->getMessage()."\n";
}

echo "\n🔍 ISSUE 2: Session Persistence\n";
echo "===============================\n";

try {
    // Test session configuration
    echo "Testing session configuration...\n";

    // Check session driver
    $sessionDriver = config('session.driver');
    echo "   Session driver: $sessionDriver\n";

    // Check session lifetime
    $sessionLifetime = config('session.lifetime');
    echo "   Session lifetime: $sessionLifetime minutes\n";

    // Check if session is working
    session_start();
    $_SESSION['test'] = 'session_working';

    if (isset($_SESSION['test']) && $_SESSION['test'] === 'session_working') {
        echo "✅ Basic session functionality working\n";

        // Test Laravel session
        session(['test_laravel' => 'laravel_session_working']);

        if (session('test_laravel') === 'laravel_session_working') {
            echo "✅ Laravel session helper working\n";
        } else {
            echo "❌ Laravel session helper not working\n";
        }
    } else {
        echo "❌ Basic PHP session not working\n";
    }
} catch (Exception $e) {
    echo '❌ Error testing session: '.$e->getMessage()."\n";
}

echo "\n🔍 ISSUE 3: Berita Bencana Backend Endpoint\n";
echo "============================================\n";

try {
    // Test the backend API endpoint
    echo "Testing backend API berita bencana endpoint...\n";

    $apiClient = new App\Services\AstacalaApiClient;

    // Test the endpoint that was causing 500 errors
    $beritaEndpoint = $apiClient->getEndpoint('gibran', 'berita_bencana');
    echo "   Testing endpoint: $beritaEndpoint\n";

    // First login to get authentication
    $credentials = [
        'email' => 'volunteer@mobile.test',
        'password' => 'password123',
    ];

    $loginEndpoint = $apiClient->getEndpoint('gibran', 'auth_login');
    $loginResponse = $apiClient->publicRequest('POST', $loginEndpoint, $credentials);

    if (isset($loginResponse['status']) && $loginResponse['status'] === 'success') {
        $token = $loginResponse['data']['access_token'];
        $apiClient->storeToken($token);
        echo "   ✅ Authentication successful\n";

        // Test the berita bencana endpoint
        $beritaResponse = $apiClient->authenticatedRequest('GET', $beritaEndpoint);

        if (isset($beritaResponse['status']) && $beritaResponse['status'] === 'success') {
            echo "✅ Berita bencana endpoint working correctly\n";
            echo '   Response contains: '.count($beritaResponse['data'] ?? [])." items\n";
        } else {
            echo "❌ Berita bencana endpoint returning error\n";
            echo '   Response: '.json_encode($beritaResponse, JSON_PRETTY_PRINT)."\n";
        }
    } else {
        echo "❌ Could not authenticate to test endpoint\n";
        echo '   Login response: '.json_encode($loginResponse, JSON_PRETTY_PRINT)."\n";
    }
} catch (Exception $e) {
    echo '❌ Error testing berita bencana endpoint: '.$e->getMessage()."\n";
}

echo "\n📋 SUMMARY OF ISSUE STATUS\n";
echo "==========================\n";
echo 'Issue 1 (Reports 500 error): ';
echo 'Issue 2 (Session persistence): ';
echo 'Issue 3 (Berita bencana endpoint): ';
echo "\nDetailed investigation completed.\n";
