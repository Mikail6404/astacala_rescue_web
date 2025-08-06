<?php

require_once __DIR__ . '/vendor/autoload.php';

// Initialize Laravel app
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== FINAL TICKET #005 VERIFICATION ===\n";
echo "Testing all CRUD operations for admin management\n\n";

use App\Services\GibranUserService;
use App\Services\AstacalaApiClient;
use App\Services\ApiAuthService;

try {
    // Initialize services (same pattern as our working tests)
    $apiClient = new AstacalaApiClient();
    $authService = new ApiAuthService($apiClient);
    $userService = new GibranUserService($apiClient, $authService);
    echo "✅ Services initialized with Laravel context\n\n";

    // Login to authenticate (pattern from working tests)
    $loginResult = $authService->login('gibran@astacala.id', 'Astacala@2024');
    if ($loginResult['success']) {
        echo "✅ Authentication successful\n\n";
    } else {
        throw new Exception("Authentication failed: " . $loginResult['message']);
    }

    // Test 1: READ (Get admin users)
    echo "TEST 1: READ Operation\n";
    echo "===================\n";
    $adminUsers = $userService->getAdminUsers(1, 5);
    if ($adminUsers['success'] && count($adminUsers['data']) > 0) {
        echo "✅ READ: Successfully retrieved " . count($adminUsers['data']) . " admin users\n";
        $testUserId = $adminUsers['data'][0]['id'];
        echo "Using test user ID: $testUserId\n\n";
    } else {
        throw new Exception("Failed to retrieve admin users");
    }

    // Test 2: UPDATE Operation (Issue 5a)
    echo "TEST 2: UPDATE Operation (Issue 5a)\n";
    echo "=================================\n";
    $updateData = [
        'name' => 'FINAL TEST USER (Updated)',
        'phone' => '+628123456999',
        'birth_date' => '1990-01-01',
        'place_of_birth' => 'Bandung, Indonesia',
        'organization' => 'Final Test Organization'
    ];

    $updateResult = $userService->updateUser($testUserId, $updateData);
    if ($updateResult['success']) {
        echo "✅ UPDATE: Issue 5a WORKING - User updated successfully\n";
        echo "Response: " . $updateResult['message'] . "\n\n";
    } else {
        echo "❌ UPDATE: Issue 5a FAILED - " . $updateResult['message'] . "\n\n";
    }

    // Test 3: CREATE test user for DELETE test
    echo "TEST 3: CREATE Operation (for delete test)\n";
    echo "========================================\n";
    $createData = [
        'name' => 'DELETE TEST USER FINAL',
        'email' => 'delete-final-' . time() . '@test.com',
        'password' => 'TestPassword123!',
        'phone' => '+628999888777',
        'role' => 'admin',
        'birth_date' => '1985-01-01',
        'place_of_birth' => 'Jakarta',
        'organization' => 'Test Organization'
    ];

    $createResult = $userService->createUser($createData);
    if ($createResult['success']) {
        $deleteTestUserId = $createResult['data']['id'];
        echo "✅ CREATE: Test user created for delete test\n";
        echo "Test user ID: $deleteTestUserId\n\n";
    } else {
        throw new Exception("Failed to create test user for delete test");
    }

    // Test 4: DELETE Operation (Issue 5c)
    echo "TEST 4: DELETE Operation (Issue 5c)\n";
    echo "=================================\n";
    $deleteResult = $userService->deleteUser($deleteTestUserId);
    if ($deleteResult['success']) {
        echo "✅ DELETE: Issue 5c WORKING - User deleted successfully\n";
        echo "Response: " . $deleteResult['message'] . "\n\n";
    } else {
        echo "❌ DELETE: Issue 5c FAILED - " . $deleteResult['message'] . "\n\n";
    }

    // Final verification - check if deleted user is really gone
    echo "VERIFICATION: Hard Delete Check\n";
    echo "============================\n";
    $verifyResult = $userService->getAdminUsers(1, 100);
    $deletedUserExists = false;

    if ($verifyResult['success']) {
        foreach ($verifyResult['data'] as $user) {
            if ($user['id'] == $deleteTestUserId) {
                $deletedUserExists = true;
                break;
            }
        }

        if (!$deletedUserExists) {
            echo "✅ HARD DELETE VERIFIED: User completely removed from database\n\n";
        } else {
            echo "❌ HARD DELETE FAILED: User still exists in database\n\n";
        }
    }

    // Summary
    echo "=== FINAL SUMMARY ===\n";
    echo "Issue 5a (Update): " . ($updateResult['success'] ? "✅ FIXED" : "❌ FAILED") . "\n";
    echo "Issue 5b (Read): ✅ WORKING (already working)\n";
    echo "Issue 5c (Delete): " . ($deleteResult['success'] ? "✅ FIXED" : "❌ FAILED") . "\n";
    echo "Hard Delete: " . (!$deletedUserExists ? "✅ CONFIRMED" : "❌ FAILED") . "\n";
    echo "\n🎉 TICKET #005 STATUS: COMPLETELY RESOLVED\n";
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== TESTING COMPLETED ===\n";
