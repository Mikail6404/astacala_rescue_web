<?php

echo "=== DETAILED WEB APP AUTHENTICATION DEBUG ===\n\n";

// Include Laravel autoloader and bootstrap
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

// Boot the application
$app->make(Illuminate\Contracts\Http\Kernel::class);

echo "1. Testing API client configuration...\n";
$apiConfig = config('astacala_api');
echo "Base URL: " . $apiConfig['base_url'] . "\n";
echo "Version: " . $apiConfig['version'] . "\n";
echo "Timeout: " . $apiConfig['timeout'] . "\n";

echo "\n2. Testing endpoint resolution...\n";
try {
    $apiClient = new App\Services\AstacalaApiClient();
    $endpoint = $apiClient->getEndpoint('gibran', 'auth_login');
    echo "Gibran auth endpoint: $endpoint\n";
} catch (Exception $e) {
    echo "❌ Endpoint resolution failed: " . $e->getMessage() . "\n";
}

echo "\n3. Testing direct API call via API client...\n";
try {
    $credentials = [
        'email' => 'volunteer@mobile.test',
        'password' => 'password123'
    ];

    $apiClient = new App\Services\AstacalaApiClient();
    $endpoint = $apiClient->getEndpoint('gibran', 'auth_login');
    echo "Making request to: $endpoint\n";

    $response = $apiClient->publicRequest('POST', $endpoint, $credentials);

    echo "API Response:\n";
    echo json_encode($response, JSON_PRETTY_PRINT) . "\n";

    if (isset($response['status']) && $response['status'] === 'success') {
        echo "✅ Direct API call successful!\n";
    } else {
        echo "❌ Direct API call failed\n";
    }
} catch (Exception $e) {
    echo "❌ Direct API call exception: " . $e->getMessage() . "\n";
}

echo "\n4. Testing GibranAuthService...\n";
try {
    $credentials = [
        'email' => 'volunteer@mobile.test',
        'password' => 'password123'
    ];

    $apiClient = new App\Services\AstacalaApiClient();
    $gibranAuth = new App\Services\GibranAuthService($apiClient);

    $result = $gibranAuth->login($credentials);

    echo "GibranAuthService Response:\n";
    echo json_encode($result, JSON_PRETTY_PRINT) . "\n";

    if ($result['success']) {
        echo "✅ GibranAuthService login successful!\n";
    } else {
        echo "❌ GibranAuthService login failed\n";
    }
} catch (Exception $e) {
    echo "❌ GibranAuthService exception: " . $e->getMessage() . "\n";
}

echo "\n5. Testing full controller flow...\n";
try {
    // Simulate the controller call
    $credentials = [
        'username' => 'admin',
        'password' => 'password123'
    ];

    $apiClient = new App\Services\AstacalaApiClient();
    $gibranAuth = new App\Services\GibranAuthService($apiClient);
    $controller = new App\Http\Controllers\AuthAdminController($gibranAuth);

    // Test the username mapping
    $reflection = new ReflectionClass($controller);
    $method = $reflection->getMethod('mapUsernameToEmail');
    $method->setAccessible(true);
    $mappedEmail = $method->invoke($controller, $credentials['username']);

    echo "Username mapping: '{$credentials['username']}' -> '$mappedEmail'\n";

    $unifiedCredentials = [
        'email' => $mappedEmail,
        'password' => $credentials['password'],
    ];

    echo "Testing authentication with mapped credentials...\n";
    $authResult = $gibranAuth->login($unifiedCredentials);

    echo "Controller auth result:\n";
    echo json_encode($authResult, JSON_PRETTY_PRINT) . "\n";

    if ($authResult['success']) {
        echo "✅ Full controller flow successful!\n";
    } else {
        echo "❌ Full controller flow failed\n";
    }
} catch (Exception $e) {
    echo "❌ Controller flow exception: " . $e->getMessage() . "\n";
}

echo "\n=== DEBUG COMPLETE ===\n";
