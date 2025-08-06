<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\AstacalaApiClient;
use App\Services\GibranAuthService;

echo "=== DEBUGGING API RESPONSE FORMATS ===\n\n";

try {
    $apiClient = new AstacalaApiClient;
    $gibranAuthService = new GibranAuthService($apiClient);

    // Test authentication first
    echo "1. Testing Authentication...\n";
    $credentials = [
        'email' => 'mikailadmin@admin.astacala.local',
        'password' => 'mikailadmin',
    ];

    $authResult = $gibranAuthService->login($credentials);
    if (! $authResult['success']) {
        echo '   ❌ Authentication failed: '.$authResult['message']."\n";
        exit(1);
    }
    echo "   ✅ Authentication successful\n\n";

    // Test raw API responses to understand the actual format
    echo "2. Testing Raw API Responses...\n";

    // Test Reports endpoint
    echo "   a) Testing Reports Endpoint Raw Response...\n";
    try {
        $endpoint = $apiClient->getEndpoint('gibran', 'pelaporans_list');
        echo "      Endpoint: $endpoint\n";
        $response = $apiClient->authenticatedRequest('GET', $endpoint);
        echo '      Raw Response Type: '.gettype($response)."\n";
        echo '      Raw Response Keys: '.json_encode(array_keys($response))."\n";
        echo '      Raw Response Sample: '.json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)."\n\n";
    } catch (Exception $e) {
        echo '      Error: '.$e->getMessage()."\n\n";
    }

    // Test Publications endpoint
    echo "   b) Testing Publications Endpoint Raw Response...\n";
    try {
        $endpoint = $apiClient->getEndpoint('gibran', 'beritas_list');
        echo "      Endpoint: $endpoint\n";
        $response = $apiClient->authenticatedRequest('GET', $endpoint);
        echo '      Raw Response Type: '.gettype($response)."\n";
        echo '      Raw Response Keys: '.json_encode(array_keys($response))."\n";
        echo '      Raw Response Sample: '.json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)."\n\n";
    } catch (Exception $e) {
        echo '      Error: '.$e->getMessage()."\n\n";
    }

    // Test Dashboard statistics endpoint
    echo "   c) Testing Dashboard Statistics Endpoint Raw Response...\n";
    try {
        $endpoint = $apiClient->getEndpoint('gibran', 'dashboard_stats');
        echo "      Endpoint: $endpoint\n";
        $response = $apiClient->authenticatedRequest('GET', $endpoint);
        echo '      Raw Response Type: '.gettype($response)."\n";
        echo '      Raw Response Keys: '.json_encode(array_keys($response))."\n";
        echo '      Raw Response Sample: '.json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)."\n\n";
    } catch (Exception $e) {
        echo '      Error: '.$e->getMessage()."\n\n";
    }

    // Test Profile endpoint
    echo "   d) Testing Profile Endpoint Raw Response...\n";
    try {
        $endpoint = $apiClient->getEndpoint('gibran', 'profile');
        echo "      Endpoint: $endpoint\n";
        $response = $apiClient->authenticatedRequest('GET', $endpoint);
        echo '      Raw Response Type: '.gettype($response)."\n";
        echo '      Raw Response Keys: '.json_encode(array_keys($response))."\n";
        echo '      Raw Response Sample: '.json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)."\n\n";
    } catch (Exception $e) {
        echo '      Error: '.$e->getMessage()."\n\n";
    }
} catch (Exception $e) {
    echo '❌ Test error: '.$e->getMessage()."\n";
    echo "Stack trace:\n".$e->getTraceAsString()."\n";
}

echo "\n=== API RESPONSE DEBUG COMPLETE ===\n";
