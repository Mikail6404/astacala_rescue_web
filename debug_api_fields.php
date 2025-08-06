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

echo "=== DEBUGGING API RESPONSE FIELDS ===\n\n";

try {
    // Create service instances
    $apiClient = new AstacalaApiClient;
    $authService = new ApiAuthService($apiClient);
    $userService = new GibranUserService($apiClient, $authService);

    // Get admin list to see what fields are available
    $result = $userService->getAdminUsers();
    if ($result['success'] && ! empty($result['data'])) {
        echo "Admin List Response (first user):\n";
        print_r($result['data'][0]);

        $testUserId = $result['data'][0]['id'];

        echo "\n=== Single User API Response ===\n";

        // Make direct API call to see raw response
        $authService->ensureAuthenticated();
        $endpoint = $apiClient->getEndpoint('users', 'get_by_id', ['id' => $testUserId]);
        $rawResponse = $apiClient->authenticatedRequest('GET', $endpoint);

        if ($rawResponse['success']) {
            echo "Single user data:\n";
            print_r($rawResponse['data']);
        }

        echo "\n=== COMPARING FIELD AVAILABILITY ===\n";
        $adminData = $result['data'][0];
        $singleUserData = $rawResponse['data'] ?? [];

        echo "Fields available in admin list:\n";
        foreach ($adminData as $key => $value) {
            echo "- $key: ".(is_string($value) ? $value : json_encode($value))."\n";
        }

        echo "\nFields available in single user call:\n";
        foreach ($singleUserData as $key => $value) {
            echo "- $key: ".(is_string($value) ? $value : json_encode($value))."\n";
        }
    }
} catch (Exception $e) {
    echo '❌ ERROR: '.$e->getMessage()."\n";
}
