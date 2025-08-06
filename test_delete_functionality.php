<?php

// Include the necessary Laravel bootstrap
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Load environment
$app->loadEnvironmentFrom('.env');

use App\Services\ApiAuthService;
use App\Services\AstacalaApiClient;
use App\Services\GibranUserService;

echo "=== TESTING DELETE (DEACTIVATE) FUNCTIONALITY ===\n\n";

try {
    // Create service instances
    $apiClient = new AstacalaApiClient;
    $authService = new ApiAuthService($apiClient);
    $userService = new GibranUserService($apiClient, $authService);

    // Find a test user (not the main admin)
    $result = $userService->getAdminUsers();
    if ($result['success'] && ! empty($result['data'])) {
        // Look for a test user that's not the main one
        $testUser = null;
        foreach ($result['data'] as $user) {
            if ($user['email'] !== 'test-admin@astacala.test' && $user['is_active']) {
                $testUser = $user;
                break;
            }
        }

        if ($testUser) {
            echo "Found test user: {$testUser['name']} ({$testUser['email']})\n";
            echo 'Current status: '.($testUser['is_active'] ? 'active' : 'inactive')."\n\n";

            // Test delete/deactivate
            echo "Testing deactivate user (ID: {$testUser['id']}):\n";
            $deleteResult = $userService->deleteUser($testUser['id']);

            echo 'Result: '.($deleteResult['success'] ? '✅ SUCCESS' : '❌ FAILED')."\n";
            echo "Message: {$deleteResult['message']}\n\n";

            if ($deleteResult['success']) {
                // Verify the user is now inactive
                echo "Verifying user is now deactivated:\n";
                $verifyResult = $userService->getUser($testUser['id']);

                if ($verifyResult['success']) {
                    $updatedUser = $verifyResult['data'];
                    echo 'User status after deactivation: '.($updatedUser['status'] ?? 'unknown')."\n";

                    if (isset($updatedUser['status']) && $updatedUser['status'] === 'inactive') {
                        echo "✅ DELETE/DEACTIVATE FUNCTIONALITY WORKING CORRECTLY!\n";
                        echo "- User is properly deactivated instead of deleted\n";
                        echo "- Status correctly shows as 'inactive'\n";
                        echo "- Success message is accurate\n";
                    } else {
                        echo "⚠️  User may not be properly deactivated\n";
                    }
                } else {
                    echo "❌ Could not verify user status: {$verifyResult['message']}\n";
                }
            }
        } else {
            echo "No suitable test user found for delete testing\n";
            echo "All users are either the main admin or already inactive\n";
        }
    } else {
        echo "❌ Could not load admin users: {$result['message']}\n";
    }

    echo "\n=== DELETE TESTING COMPLETED ===\n";
} catch (Exception $e) {
    echo '❌ ERROR: '.$e->getMessage()."\n";
    echo 'Trace: '.$e->getTraceAsString()."\n";
}
