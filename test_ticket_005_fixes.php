<?php

// Test TICKET #005 fixes - both update (5a) and delete (5c)
require_once __DIR__.'/vendor/autoload.php';

use App\Services\ApiAuthService;
use App\Services\AstacalaApiClient;
use App\Services\GibranUserService;

echo "=== TICKET #005 FIXES VERIFICATION ===\n\n";

try {
    // Initialize services with full Laravel context
    $app = require_once __DIR__.'/bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

    $apiClient = new AstacalaApiClient;
    $authService = new ApiAuthService($apiClient);
    $userService = new GibranUserService($apiClient, $authService);

    echo "✅ Services initialized with Laravel context\n\n";

    // TEST 1: Authentication
    echo "TEST 1: Authentication\n";
    echo "===================\n";

    if ($authService->ensureAuthenticated()) {
        echo "✅ Authentication successful\n\n";
    } else {
        echo "❌ Authentication failed\n";
        exit;
    }

    // TEST 2: Get Admin Users for Testing
    echo "TEST 2: Get Admin Users\n";
    echo "====================\n";

    $result = $userService->getAdminUsers();

    if ($result['success']) {
        $admins = $result['data'];
        echo '✅ SUCCESS: Found '.count($admins)." admin users\n";

        if (! empty($admins)) {
            $testAdmin = $admins[0];
            $testAdminId = $testAdmin['id'];
            echo "Test admin ID: $testAdminId\n";
            echo 'Test admin name: '.($testAdmin['name'] ?? 'N/A')."\n";
            echo 'Test admin email: '.($testAdmin['email'] ?? 'N/A')."\n\n";
        } else {
            echo "❌ No admin users found for testing\n";
            exit;
        }
    } else {
        echo '❌ FAILED: '.$result['message']."\n";
        exit;
    }

    // TEST 3: Issue 5a - Update Function
    echo "TEST 3: Issue 5a - Update Function\n";
    echo "================================\n";

    $originalName = $testAdmin['name'] ?? 'Test Admin';
    $updateData = [
        'name' => $originalName.' (Updated)',
        'phone' => '+628123456789',
        'birth_date' => '1985-01-01',
        'place_of_birth' => 'Jakarta, Indonesia',
        'organization' => 'Astacala Rescue Updated',
    ];

    echo "Updating admin ID $testAdminId with data:\n";
    foreach ($updateData as $key => $value) {
        echo "- $key: $value\n";
    }
    echo "\n";

    $updateResult = $userService->updateUser($testAdminId, $updateData);

    if ($updateResult['success']) {
        echo "✅ SUCCESS: Issue 5a FIXED - Update function working\n";
        echo 'Response: '.$updateResult['message']."\n\n";
    } else {
        echo '❌ FAILED: Issue 5a NOT FIXED - '.$updateResult['message']."\n\n";
    }

    // TEST 4: Verify Update Applied
    echo "TEST 4: Verify Update Applied\n";
    echo "==========================\n";

    $getResult = $userService->getUser($testAdminId);

    if ($getResult['success']) {
        $updatedAdmin = $getResult['data'];
        echo "✅ Retrieved updated admin data:\n";
        echo '- Name: '.($updatedAdmin['name'] ?? 'N/A')."\n";
        echo '- Phone: '.($updatedAdmin['phone'] ?? 'N/A')."\n";
        echo '- Birth Date: '.($updatedAdmin['birth_date'] ?? 'N/A')."\n";
        echo '- Birth Place: '.($updatedAdmin['place_of_birth'] ?? 'N/A')."\n";

        // Verify the update worked
        if ($updatedAdmin['name'] === $updateData['name']) {
            echo "✅ Name update VERIFIED\n";
        } else {
            echo "❌ Name update NOT APPLIED\n";
        }

        if ($updatedAdmin['phone'] === $updateData['phone']) {
            echo "✅ Phone update VERIFIED\n";
        } else {
            echo "❌ Phone update NOT APPLIED\n";
        }

        echo "\n";
    } else {
        echo '❌ Failed to verify update: '.$getResult['message']."\n\n";
    }

    // TEST 5: Create Test User for Delete Test (don't delete existing admin)
    echo "TEST 5: Create Test User for Delete\n";
    echo "================================\n";

    // For safety, let's create a test user to delete instead of deleting existing admin
    $createTestUser = function () use ($apiClient) {
        try {
            $endpoint = $apiClient->getEndpoint('users', 'create_admin');
            $testUserData = [
                'name' => 'DELETE TEST USER',
                'email' => 'delete-test-'.time().'@test.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'role' => 'admin',
                'phone' => '+628999999999',
                'birth_date' => '1990-01-01',
                'place_of_birth' => 'Test City',
                'organization' => 'Test Org',
                'member_number' => 'TEST-DELETE-'.time(),
            ];

            $response = $apiClient->authenticatedRequest('POST', $endpoint, $testUserData);

            if ($response['success'] ?? false) {
                return [
                    'success' => true,
                    'data' => $response['data'],
                ];
            }

            return [
                'success' => false,
                'message' => $response['message'] ?? 'Failed to create test user',
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Exception: '.$e->getMessage(),
            ];
        }
    };

    $createResult = $createTestUser();

    if ($createResult['success']) {
        $testUser = $createResult['data'];
        $testUserId = $testUser['id'];
        echo "✅ Test user created for delete test\n";
        echo "Test user ID: $testUserId\n";
        echo 'Test user email: '.$testUser['email']."\n\n";
    } else {
        echo '❌ Failed to create test user: '.$createResult['message']."\n";
        echo "⚠️  Skipping delete test to avoid deleting real admin\n\n";
        exit;
    }

    // TEST 6: Issue 5c - Delete Function (Hard Delete)
    echo "TEST 6: Issue 5c - Delete Function (Hard Delete)\n";
    echo "==============================================\n";

    echo "Deleting test user ID $testUserId...\n";

    $deleteResult = $userService->deleteUser($testUserId);

    if ($deleteResult['success']) {
        echo "✅ SUCCESS: Issue 5c FIXED - Delete function working (hard delete)\n";
        echo 'Response: '.$deleteResult['message']."\n\n";
    } else {
        echo '❌ FAILED: Issue 5c NOT FIXED - '.$deleteResult['message']."\n\n";
    }

    // TEST 7: Verify Hard Delete (user should be completely gone)
    echo "TEST 7: Verify Hard Delete\n";
    echo "========================\n";

    $verifyResult = $userService->getUser($testUserId);

    if (! $verifyResult['success']) {
        echo "✅ SUCCESS: User completely deleted (not found in database)\n";
        echo "This confirms HARD DELETE is working correctly\n";
    } else {
        echo "❌ FAILED: User still exists - hard delete didn't work\n";
        echo 'User data: '.json_encode($verifyResult['data'])."\n";
    }

    echo "\n=== FINAL SUMMARY ===\n";
    echo 'Issue 5a (Update function): '.($updateResult['success'] ? '✅ FIXED' : '❌ NOT FIXED')."\n";
    echo 'Issue 5c (Delete function): '.($deleteResult['success'] ? '✅ FIXED' : '❌ NOT FIXED')."\n";
    echo 'Hard delete verification: '.(! $verifyResult['success'] ? '✅ WORKING' : '❌ NOT WORKING')."\n";
} catch (Exception $e) {
    echo '❌ CRITICAL ERROR: '.$e->getMessage()."\n";
    echo "Stack trace:\n".$e->getTraceAsString()."\n";
}

echo "\n=== TESTING COMPLETED ===\n";
