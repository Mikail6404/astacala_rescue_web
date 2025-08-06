<?php

require_once __DIR__.'/vendor/autoload.php';

echo "🔍 WEB APP API RESPONSE ANALYSIS\n";
echo "===============================\n\n";

// Simulate the web app's admin data request
use App\Services\AstacalaApiClient;
use App\Services\GibranAuthService;
use App\Services\GibranUserService;

try {
    // Initialize services like the web app would
    $apiClient = new AstacalaApiClient;
    $userService = new GibranUserService($apiClient);
    $authService = new GibranAuthService($apiClient);

    // Simulate admin login
    echo "🔐 Simulating admin login...\n";
    $loginData = [
        'username' => 'mikailadmin@admin.astacala.local',
        'password' => 'mikailadmin',
    ];

    $loginResult = $authService->login($loginData);

    if ($loginResult['success']) {
        echo "✅ Login successful\n\n";

        // Test the getAdminUsers method
        echo "📊 Testing getAdminUsers() method...\n";
        $adminResponse = $userService->getAdminUsers();

        if ($adminResponse['success']) {
            echo "✅ getAdminUsers() successful\n";
            echo "📋 Response structure:\n";
            echo '   Success: '.($adminResponse['success'] ? 'true' : 'false')."\n";
            echo '   Message: '.$adminResponse['message']."\n";
            echo '   Data count: '.count($adminResponse['data'])."\n\n";

            // Show the first admin user structure
            if (! empty($adminResponse['data'])) {
                echo "🎯 FIRST ADMIN USER STRUCTURE:\n";
                echo "==============================\n";
                $firstAdmin = $adminResponse['data'][0];

                if (is_array($firstAdmin)) {
                    foreach ($firstAdmin as $key => $value) {
                        echo "   {$key}: ".(is_null($value) ? 'NULL' : $value)."\n";
                    }
                } else {
                    echo "   Data is object, converting to array...\n";
                    $adminArray = (array) $firstAdmin;
                    foreach ($adminArray as $key => $value) {
                        echo "   {$key}: ".(is_null($value) ? 'NULL' : $value)."\n";
                    }
                }

                echo "\n🔍 FIELD MAPPING ANALYSIS:\n";
                echo "==========================\n";
                echo "VIEW EXPECTS          → API PROVIDES\n";
                echo 'date_of_birth         → '.(isset($firstAdmin['birth_date']) ? 'birth_date ✅' : 'NOT FOUND ❌')."\n";
                echo 'place_of_birth        → '.(isset($firstAdmin['birth_place']) ? 'birth_place ✅' : 'NOT FOUND ❌')."\n";
                echo 'phone                 → '.(isset($firstAdmin['phone']) ? 'phone ✅' : 'NOT FOUND ❌')."\n";
                echo 'member_number         → '.(isset($firstAdmin['organization']) ? 'organization (mapped) ✅' : 'NOT FOUND ❌')."\n";
            }
        } else {
            echo '❌ getAdminUsers() failed: '.$adminResponse['message']."\n";
        }
    } else {
        echo '❌ Login failed: '.$loginResult['message']."\n";
    }
} catch (Exception $e) {
    echo '❌ Error: '.$e->getMessage()."\n";
    echo "Stack trace:\n".$e->getTraceAsString()."\n";
}
