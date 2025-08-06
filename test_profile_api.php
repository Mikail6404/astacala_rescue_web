<?php

require_once 'vendor/autoload.php';

use App\Services\AstacalaApiClient;

echo "=== TESTING PROFILE API ENDPOINTS ===\n\n";

$apiClient = new AstacalaApiClient();

// Test 1: Check endpoint configuration
echo "1. Testing endpoint configuration:\n";
$profileEndpoint = $apiClient->getEndpoint('users', 'profile');
$updateEndpoint = $apiClient->getEndpoint('users', 'update_profile');

echo "   - Profile endpoint: $profileEndpoint\n";
echo "   - Update endpoint: $updateEndpoint\n\n";

// Test 2: Test API call
echo "2. Testing profile API call:\n";
try {
    $response = $apiClient->authenticatedRequest('GET', $profileEndpoint);
    echo "   - Response received: " . json_encode($response, JSON_PRETTY_PRINT) . "\n\n";

    if (isset($response['success']) && $response['success']) {
        echo "   - ✅ API call successful\n";
        if (isset($response['user'])) {
            echo "   - ✅ User data present\n";
            echo "   - User data: " . json_encode($response['user'], JSON_PRETTY_PRINT) . "\n";
        } else {
            echo "   - ❌ No user data in response\n";
        }
    } else {
        echo "   - ❌ API call failed\n";
        echo "   - Error: " . ($response['message'] ?? 'Unknown error') . "\n";
    }
} catch (Exception $e) {
    echo "   - ❌ Exception occurred: " . $e->getMessage() . "\n";
}

echo "\n=== TEST COMPLETE ===\n";
