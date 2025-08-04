<?php

require_once 'vendor/autoload.php';

// Simulate Laravel environment
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\AstacalaApiClient;
use App\Services\AuthService;

echo "Testing API Integration...\n";

try {
    // Test API client instantiation
    $apiClient = new AstacalaApiClient();
    echo "✓ AstacalaApiClient instantiated successfully\n";

    // Test Auth service instantiation
    $authService = new AuthService($apiClient);
    echo "✓ AuthService instantiated successfully\n";

    // Test basic API connectivity to backend
    $response = $apiClient->publicRequest('GET', '/api/health');

    echo "Response received: " . json_encode($response) . "\n";

    if (isset($response['success']) && $response['success']) {
        echo "✓ Backend API connection successful\n";
        echo "Backend Status: " . json_encode($response['data']) . "\n";
    } else {
        echo "✗ Backend API connection failed\n";
        if (isset($response['message'])) {
            echo "Error: " . $response['message'] . "\n";
        }
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\nIntegration test completed.\n";
