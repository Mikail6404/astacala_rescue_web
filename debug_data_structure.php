<?php
require_once __DIR__ . '/vendor/autoload.php';

use App\Services\AstacalaApiClient;
use App\Services\GibranUserService;

// Test script to check data structure from API
$apiClient = new AstacalaApiClient();
$userService = new GibranUserService($apiClient);

echo "=== TESTING USER DATA STRUCTURE ===\n\n";

// Test user data
$response = $userService->getAllUsers();

echo "Success: " . ($response['success'] ? 'YES' : 'NO') . "\n";
echo "Message: " . $response['message'] . "\n";

if ($response['success'] && !empty($response['data'])) {
    echo "\nData structure for first user:\n";
    $firstUser = $response['data'][0];

    echo "Data type: " . gettype($firstUser) . "\n";

    if (is_array($firstUser)) {
        echo "Array keys: " . implode(', ', array_keys($firstUser)) . "\n";
        echo "Sample data: " . json_encode($firstUser, JSON_PRETTY_PRINT) . "\n";
    } else if (is_object($firstUser)) {
        echo "Object properties:\n";
        print_r(get_object_vars($firstUser));
    }
} else {
    echo "No data returned or error occurred\n";
    echo "Full response: " . json_encode($response, JSON_PRETTY_PRINT) . "\n";
}

echo "\n=== TESTING ADMIN AUTHENTICATION ===\n";

// Test admin session data
echo "Admin ID from session: " . (session('admin_id') ?? 'NULL') . "\n";
echo "Admin user from session: " . (session('admin_user') ?? 'NULL') . "\n";
echo "All session data:\n";
print_r(session()->all());
