<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing GibranUserService API integration...\n";

$apiClient = new App\Services\AstacalaApiClient();
$userService = new App\Services\GibranUserService($apiClient);

// Test 1: Get admin users
echo "1. Testing getAdminUsers()...\n";
$result = $userService->getAdminUsers();
echo json_encode($result, JSON_PRETTY_PRINT) . "\n";

// Test 2: Get a specific user (if any users exist)
if (isset($result['data']) && !empty($result['data'])) {
    $firstUser = $result['data'][0];
    $userId = is_array($firstUser) ? $firstUser['id'] : $firstUser->id;

    echo "2. Testing getUser($userId)...\n";
    $userResult = $userService->getUser($userId);
    echo json_encode($userResult, JSON_PRETTY_PRINT) . "\n";
}

// Test 3: Check API endpoints configuration
echo "3. Checking API endpoints...\n";
$endpoints = [
    'admin_list' => $apiClient->getEndpoint('users', 'admin_list'),
    'get_by_id' => $apiClient->getEndpoint('users', 'get_by_id', ['id' => 1]),
    'update_profile' => $apiClient->getEndpoint('users', 'update_profile'),
    'update_status' => $apiClient->getEndpoint('users', 'update_status', ['id' => 1])
];

foreach ($endpoints as $name => $endpoint) {
    echo "  $name: $endpoint\n";
}
