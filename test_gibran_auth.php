<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\AstacalaApiClient;
use App\Services\GibranAuthService;

echo "=== TESTING GIBRAN AUTH SERVICE ===\n\n";

try {
    $apiClient = new AstacalaApiClient();
    $gibranAuthService = new GibranAuthService($apiClient);

    echo "1. Testing Gibran auth endpoint...\n";
    $gibranLoginEndpoint = $apiClient->getEndpoint('gibran', 'auth_login');
    echo "   Gibran login endpoint: $gibranLoginEndpoint\n";

    echo "\n2. Testing login with mikailadmin@admin.astacala.local...\n";
    $credentials = [
        'email' => 'mikailadmin@admin.astacala.local',
        'password' => 'mikailadmin'
    ];

    $result = $gibranAuthService->login($credentials);
    echo "   Login result: " . json_encode($result) . "\n";

    if ($result['success']) {
        echo "   ✅ Gibran login successful!\n";

        // Check if token was stored
        $storedToken = $apiClient->getStoredToken();
        if ($storedToken) {
            echo "   ✅ Token stored successfully: " . substr($storedToken, 0, 20) . "...\n";
        } else {
            echo "   ❌ Token not stored\n";
        }

        // Test authenticated request
        $userStatsEndpoint = $apiClient->getEndpoint('users', 'statistics');
        $authTest = $apiClient->authenticatedRequest('GET', $userStatsEndpoint);
        echo "   Authenticated test: " . (isset($authTest['success']) && $authTest['success'] ? "✅ Working" : "❌ Failed") . "\n";
    } else {
        echo "   ❌ Gibran login failed: " . ($result['message'] ?? 'Unknown error') . "\n";
    }

    echo "\n3. Testing direct Gibran endpoint with public request...\n";
    try {
        $directResponse = $apiClient->publicRequest('POST', $gibranLoginEndpoint, $credentials);
        echo "   Direct response: " . json_encode($directResponse) . "\n";
    } catch (Exception $e) {
        echo "   ❌ Direct request error: " . $e->getMessage() . "\n";
    }
} catch (Exception $e) {
    echo "❌ Test error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== GIBRAN AUTH TEST COMPLETE ===\n";
