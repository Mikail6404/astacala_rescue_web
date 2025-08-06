<?php

// Debug endpoint configuration
require_once __DIR__.'/vendor/autoload.php';

echo "=== ENDPOINT CONFIGURATION DEBUG ===\n\n";

// Initialize Laravel app to get config
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Available endpoints:\n";

$endpoints = config('astacala_api.endpoints');

echo "All endpoint categories:\n";
foreach ($endpoints as $category => $actions) {
    echo "- $category\n";
    foreach ($actions as $action => $endpoint) {
        echo "  - $action: $endpoint\n";
    }
    echo "\n";
}

echo "\nSpecifically checking 'users' category:\n";
if (isset($endpoints['users'])) {
    foreach ($endpoints['users'] as $action => $endpoint) {
        echo "- $action: $endpoint\n";
    }
} else {
    echo "❌ 'users' category not found!\n";
}

echo "\nLooking for our new endpoints:\n";
echo '- update_user_by_id: '.(isset($endpoints['users']['update_user_by_id']) ? '✅ FOUND' : '❌ NOT FOUND')."\n";
echo '- delete_user_by_id: '.(isset($endpoints['users']['delete_user_by_id']) ? '✅ FOUND' : '❌ NOT FOUND')."\n";

if (isset($endpoints['users']['update_user_by_id'])) {
    echo '  update_user_by_id value: '.$endpoints['users']['update_user_by_id']."\n";
}

if (isset($endpoints['users']['delete_user_by_id'])) {
    echo '  delete_user_by_id value: '.$endpoints['users']['delete_user_by_id']."\n";
}

// Test AstacalaApiClient directly
echo "\n=== Testing AstacalaApiClient ===\n";

use App\Services\AstacalaApiClient;

try {
    $apiClient = new AstacalaApiClient;

    echo "Testing existing endpoint (should work):\n";
    $existingEndpoint = $apiClient->getEndpoint('users', 'admin_list');
    echo "✅ admin_list: $existingEndpoint\n";

    echo "\nTesting new endpoints:\n";

    try {
        $updateEndpoint = $apiClient->getEndpoint('users', 'update_user_by_id', ['id' => 123]);
        echo "✅ update_user_by_id: $updateEndpoint\n";
    } catch (Exception $e) {
        echo '❌ update_user_by_id: '.$e->getMessage()."\n";
    }

    try {
        $deleteEndpoint = $apiClient->getEndpoint('users', 'delete_user_by_id', ['id' => 123]);
        echo "✅ delete_user_by_id: $deleteEndpoint\n";
    } catch (Exception $e) {
        echo '❌ delete_user_by_id: '.$e->getMessage()."\n";
    }
} catch (Exception $e) {
    echo '❌ ApiClient error: '.$e->getMessage()."\n";
}

echo "\n=== DEBUG COMPLETED ===\n";
