<?php

require_once __DIR__.'/vendor/autoload.php';

use App\Services\ApiAuthService;
use App\Services\AstacalaApiClient;
use App\Services\GibranUserService;

echo "=== TICKET #006 - DATAPENGGUNA CRUD OPERATIONS TESTING ===\n\n";

try {
    // Initialize services
    $apiClient = new AstacalaApiClient;
    $authService = new ApiAuthService($apiClient);
    $userService = new GibranUserService($apiClient, $authService);

    echo "✅ Services initialized successfully\n\n";

    // TEST 1: Get Volunteer Users (Datapengguna)
    echo "TEST 1: Get Volunteer Users\n";
    echo "=============================\n";

    $result = $userService->getVolunteerUsers();

    if ($result['success']) {
        $volunteers = $result['data'];
        echo '✅ SUCCESS: Found '.count($volunteers)." volunteer users\n";

        if (! empty($volunteers)) {
            $firstVolunteer = $volunteers[0];
            echo "Sample volunteer data:\n";
            echo '- ID: '.($firstVolunteer['id'] ?? 'N/A')."\n";
            echo '- Name: '.($firstVolunteer['name'] ?? 'N/A')."\n";
            echo '- Email: '.($firstVolunteer['email'] ?? 'N/A')."\n";
            echo '- Phone: '.($firstVolunteer['phone'] ?? 'N/A')."\n";
            echo '- Birth Date: '.($firstVolunteer['birth_date'] ?? 'N/A')."\n";
            echo '- Birth Place: '.($firstVolunteer['place_of_birth'] ?? 'N/A')."\n";
            echo '- Role: '.($firstVolunteer['role'] ?? 'N/A')."\n";
            echo '- Status: '.($firstVolunteer['is_active'] ? 'Active' : 'Inactive')."\n";
        }
    } else {
        echo '❌ FAILED: '.$result['message']."\n";
    }

    echo "\n";

    // TEST 2: Get Single Volunteer User Details
    if (! empty($volunteers)) {
        $testUserId = $firstVolunteer['id'];

        echo "TEST 2: Get Single Volunteer User ($testUserId)\n";
        echo "=============================================\n";

        $result = $userService->getUser($testUserId);

        if ($result['success']) {
            $user = $result['data'];
            echo "✅ SUCCESS: Retrieved volunteer user details\n";
            echo "User details:\n";
            echo '- ID: '.($user['id'] ?? 'N/A')."\n";
            echo '- Name: '.($user['name'] ?? 'N/A')."\n";
            echo '- Email: '.($user['email'] ?? 'N/A')."\n";
            echo '- Phone: '.($user['phone'] ?? 'N/A')."\n";
            echo '- Birth Date: '.($user['birth_date'] ?? 'N/A')."\n";
            echo '- Birth Place: '.($user['place_of_birth'] ?? 'N/A')."\n";
            echo '- Member Number: '.($user['member_number'] ?? 'N/A')."\n";
            echo '- Organization: '.($user['organization'] ?? 'N/A')."\n";

            // Check for issues from TICKET #006
            $issues = [];
            if (empty($user['birth_date'])) {
                $issues[] = 'Missing birth_date field';
            }
            if (empty($user['place_of_birth'])) {
                $issues[] = 'Missing place_of_birth field';
            }
            if (empty($user['phone'])) {
                $issues[] = 'Missing phone field';
            }
            if (empty($user['member_number'])) {
                $issues[] = 'Missing member_number field';
            }

            if (! empty($issues)) {
                echo "\n⚠️  POTENTIAL ISSUES DETECTED:\n";
                foreach ($issues as $issue) {
                    echo '   - '.$issue."\n";
                }
            } else {
                echo "\n✅ All required fields are populated\n";
            }
        } else {
            echo '❌ FAILED: '.$result['message']."\n";
        }

        echo "\n";

        // TEST 3: Update Volunteer User
        echo "TEST 3: Update Volunteer User ($testUserId)\n";
        echo "========================================\n";

        $updateData = [
            'name' => $user['name'].' (Updated)',
            'phone' => '+628123456789',
            'birth_date' => '1990-05-15',
            'place_of_birth' => 'Jakarta, Indonesia',
        ];

        $result = $userService->updateUser($testUserId, $updateData);

        if ($result['success']) {
            echo "✅ SUCCESS: Volunteer user updated successfully\n";
            echo "Updated data:\n";
            echo '- Name: '.$updateData['name']."\n";
            echo '- Phone: '.$updateData['phone']."\n";
            echo '- Birth Date: '.$updateData['birth_date']."\n";
            echo '- Birth Place: '.$updateData['place_of_birth']."\n";
        } else {
            echo '❌ FAILED: '.$result['message']."\n";
        }

        echo "\n";

        // TEST 4: Verify Update by Re-fetching User
        echo "TEST 4: Verify Update\n";
        echo "===================\n";

        $result = $userService->getUser($testUserId);

        if ($result['success']) {
            $updatedUser = $result['data'];
            echo "✅ SUCCESS: Retrieved updated volunteer user\n";
            echo "Verified data:\n";
            echo '- Name: '.($updatedUser['name'] ?? 'N/A')."\n";
            echo '- Phone: '.($updatedUser['phone'] ?? 'N/A')."\n";
            echo '- Birth Date: '.($updatedUser['birth_date'] ?? 'N/A')."\n";
            echo '- Birth Place: '.($updatedUser['place_of_birth'] ?? 'N/A')."\n";

            // Check if updates were applied
            if ($updatedUser['name'] === $updateData['name']) {
                echo "✅ Name update verified\n";
            } else {
                echo "❌ Name update NOT applied\n";
            }

            if ($updatedUser['phone'] === $updateData['phone']) {
                echo "✅ Phone update verified\n";
            } else {
                echo "❌ Phone update NOT applied\n";
            }
        } else {
            echo '❌ FAILED: '.$result['message']."\n";
        }

        echo "\n";

        // TEST 5: Delete (Deactivate) Volunteer User
        echo "TEST 5: Delete (Deactivate) Volunteer User ($testUserId)\n";
        echo "===================================================\n";

        $result = $userService->deleteUser($testUserId);

        if ($result['success']) {
            echo "✅ SUCCESS: Volunteer user deactivated successfully\n";
        } else {
            echo '❌ FAILED: '.$result['message']."\n";
        }

        echo "\n";

        // TEST 6: Verify Deactivation
        echo "TEST 6: Verify Deactivation\n";
        echo "=========================\n";

        $result = $userService->getUser($testUserId);

        if ($result['success']) {
            $deactivatedUser = $result['data'];
            $isActive = $deactivatedUser['is_active'] ?? true;

            if (! $isActive) {
                echo "✅ SUCCESS: User is properly deactivated (is_active: false)\n";
            } else {
                echo "❌ FAILED: User is still active (is_active: true) - Delete didn't work properly\n";
            }
        } else {
            echo "✅ SUCCESS: User no longer accessible (properly deleted/deactivated)\n";
        }
    } else {
        echo "❌ SKIPPING TESTS 2-6: No volunteer users found to test with\n";
    }
} catch (Exception $e) {
    echo '❌ CRITICAL ERROR: '.$e->getMessage()."\n";
    echo "Stack trace:\n".$e->getTraceAsString()."\n";
}

echo "\n=== TESTING COMPLETED ===\n";
