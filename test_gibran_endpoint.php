<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\AstacalaApiClient;

echo "=== TESTING GIBRAN DASHBOARD ENDPOINT ===\n\n";

try {
    $apiClient = new AstacalaApiClient;

    // Login first
    $loginUrl = $apiClient->getEndpoint('auth', 'login');
    $loginData = [
        'email' => 'mikailadmin@admin.astacala.local',
        'password' => 'mikailadmin',
    ];

    $loginResponse = $apiClient->publicRequest('POST', $loginUrl, $loginData);
    if (isset($loginResponse['data']['tokens']['accessToken'])) {
        $apiClient->storeToken($loginResponse['data']['tokens']['accessToken'], $loginResponse['data']['user']);
        echo "✅ Logged in successfully\n\n";
    }

    echo "Testing Gibran dashboard statistics endpoint...\n";
    $gibranEndpoint = $apiClient->getEndpoint('gibran', 'dashboard_statistics');
    echo "Endpoint: $gibranEndpoint\n";

    $response = $apiClient->authenticatedRequest('GET', $gibranEndpoint);
    echo 'Raw response: '.json_encode($response, JSON_PRETTY_PRINT)."\n";

    // Check response format
    if (isset($response['status']) && $response['status'] === 'success') {
        echo "✅ API response has correct status\n";
        echo 'Message: '.($response['message'] ?? 'No message')."\n";
        echo 'Data keys: '.(isset($response['data']) ? implode(', ', array_keys($response['data'])) : 'No data')."\n";
    } else {
        echo "❌ API response format issue\n";
        echo 'Available keys: '.implode(', ', array_keys($response))."\n";
    }
} catch (Exception $e) {
    echo '❌ Error: '.$e->getMessage()."\n";
}

echo "\n=== GIBRAN ENDPOINT TEST COMPLETE ===\n";
