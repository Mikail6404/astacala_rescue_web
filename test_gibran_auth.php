<?php
require __DIR__ . '/vendor/autoload.php';

// Load Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->boot();

use App\Services\GibranAuthService;
use App\Services\AstacalaApiClient;

echo "Testing Gibran Authentication Service Integration\n";
echo "================================================\n\n";

try {
    // Create the API client and auth service
    $apiClient = new AstacalaApiClient();
    $authService = new GibranAuthService($apiClient);

    // Test credentials
    $credentials = [
        'email' => 'volunteer@mobile.test',
        'password' => 'password123'
    ];

    echo "Testing authentication with:\n";
    echo "Email: " . $credentials['email'] . "\n";
    echo "Password: [hidden]\n\n";

    // Attempt login
    $result = $authService->login($credentials);

    echo "Authentication Result:\n";
    echo "Success: " . ($result['success'] ? 'YES' : 'NO') . "\n";
    echo "Message: " . $result['message'] . "\n";

    if ($result['success']) {
        echo "User ID: " . $result['user']['id'] . "\n";
        echo "User Name: " . $result['user']['name'] . "\n";
        echo "User Email: " . $result['user']['email'] . "\n";
        echo "Token: " . substr($result['token'], 0, 20) . "...\n";
    } else {
        echo "Error: " . ($result['error'] ?? 'Unknown error') . "\n";
    }
} catch (Exception $e) {
    echo "Exception occurred: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

echo "\n=== Test Complete ===\n";
