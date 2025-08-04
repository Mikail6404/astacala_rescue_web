<?php

require_once 'vendor/autoload.php';

// Simulate Laravel environment
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\AstacalaApiClient;
use App\Services\AuthService;

echo "Testing Auth Integration...\n";

try {
    // Test API client and auth service
    $apiClient = new AstacalaApiClient();
    $authService = new AuthService($apiClient);

    echo "✓ Services instantiated successfully\n";

    // Test user registration
    echo "\n--- Testing User Registration ---\n";
    $userData = [
        'name' => 'Web Test User',
        'email' => 'webtest2@disaster.com', // Use a new email to avoid conflicts
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role' => 'VOLUNTEER' // Backend expects uppercase roles: VOLUNTEER, COORDINATOR, ADMIN
    ];

    $registerResult = $authService->register($userData);

    if ($registerResult['success']) {
        echo "✓ User registration successful\n";
    } else {
        echo "Registration result: " . json_encode($registerResult) . "\n";
    }

    // Test user login
    echo "\n--- Testing User Login ---\n";
    $credentials = [
        'email' => 'webtest2@disaster.com', // Use the same email as registration
        'password' => 'password123'
    ];

    $loginResult = $authService->login($credentials);

    if ($loginResult['success']) {
        echo "✓ User login successful\n";
        echo "Token: " . substr($loginResult['token'], 0, 20) . "...\n";
        echo "User: " . json_encode($loginResult['user']) . "\n";

        // Test user retrieval
        echo "\n--- Testing Get User ---\n";
        $user = $authService->getUser();
        if ($user) {
            echo "✓ User retrieved successfully: " . $user['name'] . "\n";
        } else {
            echo "✗ Failed to retrieve user\n";
        }

        // Test logout
        echo "\n--- Testing User Logout ---\n";
        $logoutResult = $authService->logout();
        if ($logoutResult['success']) {
            echo "✓ User logout successful\n";
        } else {
            echo "✗ Logout failed: " . $logoutResult['message'] . "\n";
        }
    } else {
        echo "✗ Login failed: " . json_encode($loginResult) . "\n";
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\nAuth integration test completed.\n";
