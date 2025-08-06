<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\AuthAdminController;
use App\Services\AstacalaApiClient;
use App\Services\GibranAuthService;
use Illuminate\Http\Request;

echo "=== TESTING FIXED LOGIN PROCESS ===\n\n";

try {
    $apiClient = new AstacalaApiClient;

    echo "1. Testing username-to-email mapping...\n";
    $authController = new AuthAdminController(new GibranAuthService($apiClient));

    // Use reflection to access private method
    $reflection = new ReflectionClass($authController);
    $mapMethod = $reflection->getMethod('mapUsernameToEmail');
    $mapMethod->setAccessible(true);

    // Test various username inputs
    $testUsernames = ['mikailadmin', 'admin', 'mikailadmin@admin.astacala.local', 'test@example.com'];

    foreach ($testUsernames as $username) {
        $email = $mapMethod->invoke($authController, $username);
        echo "   '$username' → '$email'\n";
    }

    echo "\n2. Testing login with username 'mikailadmin'...\n";

    $processMethod = $reflection->getMethod('processCredentialsForAuth');
    $processMethod->setAccessible(true);

    $processedCreds = $processMethod->invoke($authController, 'mikailadmin', 'mikailadmin');
    echo '   Processed credentials: '.json_encode($processedCreds)."\n";

    // Test direct authentication with GibranAuthService
    $gibranAuthService = new GibranAuthService($apiClient);
    $authResult = $gibranAuthService->login($processedCreds);

    echo '   Auth result: '.json_encode([
        'success' => $authResult['success'],
        'message' => $authResult['message'],
        'user_email' => $authResult['user']['email'] ?? null,
    ])."\n";

    if ($authResult['success']) {
        echo "   ✅ Username login successful!\n";

        // Check token storage
        $storedToken = $apiClient->getStoredToken();
        echo '   ✅ Token stored: '.($storedToken ? 'Yes' : 'No')."\n";

        // Test authenticated request
        $userStatsEndpoint = $apiClient->getEndpoint('users', 'statistics');
        $authTest = $apiClient->authenticatedRequest('GET', $userStatsEndpoint);
        echo '   ✅ Data fetching: '.(isset($authTest['success']) && $authTest['success'] ? 'Working' : 'Failed')."\n";
    } else {
        echo "   ❌ Username login failed\n";
    }

    echo "\n3. Testing with common 'admin' username...\n";
    $adminCreds = $processMethod->invoke($authController, 'admin', 'admin');
    echo '   Admin credentials: '.json_encode($adminCreds)."\n";

    $adminAuth = $gibranAuthService->login($adminCreds);
    echo '   Admin auth: '.($adminAuth['success'] ? '✅ Success' : '❌ Failed')."\n";
} catch (Exception $e) {
    echo '❌ Test error: '.$e->getMessage()."\n";
    echo "Stack trace:\n".$e->getTraceAsString()."\n";
}

echo "\n=== LOGIN PROCESS TEST COMPLETE ===\n";
