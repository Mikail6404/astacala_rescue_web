<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\AstacalaApiClient;
use App\Services\GibranAuthService;

echo "=== TESTING BERITA BENCANA ENDPOINT ===\n\n";

try {
    $apiClient = new AstacalaApiClient();
    $gibranAuthService = new GibranAuthService($apiClient);

    // Test authentication first
    echo "1. Testing Authentication...\n";
    $credentials = [
        'email' => 'mikailadmin@admin.astacala.local',
        'password' => 'mikailadmin'
    ];

    $authResult = $gibranAuthService->login($credentials);
    if (!$authResult['success']) {
        echo "   ❌ Authentication failed: " . $authResult['message'] . "\n";
        exit(1);
    }
    echo "   ✅ Authentication successful\n\n";

    // Test berita_bencana endpoint
    echo "2. Testing Berita Bencana Endpoint...\n";
    try {
        $endpoint = $apiClient->getEndpoint('gibran', 'berita_bencana');
        echo "   Endpoint: $endpoint\n";
        $response = $apiClient->authenticatedRequest('GET', $endpoint);
        echo "   Raw Response Type: " . gettype($response) . "\n";
        echo "   Raw Response Keys: " . json_encode(array_keys($response)) . "\n";
        echo "   Raw Response: " . json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
    } catch (Exception $e) {
        echo "   Error: " . $e->getMessage() . "\n\n";
    }

    // Test dashboard_statistics endpoint
    echo "3. Testing Dashboard Statistics Endpoint...\n";
    try {
        $endpoint = $apiClient->getEndpoint('gibran', 'dashboard_statistics');
        echo "   Endpoint: $endpoint\n";
        $response = $apiClient->authenticatedRequest('GET', $endpoint);
        echo "   Raw Response Type: " . gettype($response) . "\n";
        echo "   Raw Response Keys: " . json_encode(array_keys($response)) . "\n";
        echo "   Raw Response: " . json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
    } catch (Exception $e) {
        echo "   Error: " . $e->getMessage() . "\n\n";
    }

    // Try using standard endpoints instead
    echo "4. Testing Standard Reports Statistics Endpoint...\n";
    try {
        $endpoint = $apiClient->getEndpoint('reports', 'statistics');
        echo "   Endpoint: $endpoint\n";
        $response = $apiClient->authenticatedRequest('GET', $endpoint);
        echo "   Raw Response Type: " . gettype($response) . "\n";
        echo "   Raw Response Keys: " . json_encode(array_keys($response)) . "\n";
        echo "   Raw Response: " . json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
    } catch (Exception $e) {
        echo "   Error: " . $e->getMessage() . "\n\n";
    }
} catch (Exception $e) {
    echo "❌ Test error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== ENDPOINT TESTING COMPLETE ===\n";
