<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\AstacalaApiClient;
use App\Services\GibranAuthService;
use App\Services\GibranContentService;
use App\Services\GibranDashboardService;
use App\Services\GibranReportService;
use App\Services\GibranUserService;

echo "=== FINAL COMPREHENSIVE DASHBOARD TEST ===\n\n";

try {
    $apiClient = new AstacalaApiClient;
    $gibranAuthService = new GibranAuthService($apiClient);

    // Test authentication first
    echo "1. Testing Authentication...\n";
    $credentials = [
        'email' => 'mikailadmin@admin.astacala.local',
        'password' => 'mikailadmin',
    ];

    $authResult = $gibranAuthService->login($credentials);
    if (! $authResult['success']) {
        echo '   ❌ Authentication failed: '.$authResult['message']."\n";
        exit(1);
    }
    echo "   ✅ Authentication successful\n\n";

    // Initialize all services
    $userService = new GibranUserService($apiClient);
    $reportService = new GibranReportService($apiClient);
    $contentService = new GibranContentService($apiClient);
    $dashboardService = new GibranDashboardService($apiClient);

    // Test all dashboard pages data fetching
    echo "2. Testing Dashboard Pages Data Fetching...\n";

    // Test User Data (Datapengguna)
    echo "   a) Testing User Data Service...\n";
    $usersResult = $userService->getAllUsers();
    if ($usersResult['success']) {
        $userCount = count($usersResult['data']);
        echo "      ✅ Users data fetched: $userCount users found\n";

        // Test single user fetch for edit functionality
        if ($userCount > 0) {
            $firstUserId = is_array($usersResult['data'][0]) ? $usersResult['data'][0]['id'] : $usersResult['data'][0]->id;
            $singleUserResult = $userService->getUser($firstUserId);
            if ($singleUserResult['success']) {
                echo "      ✅ Single user fetch working (for edit functionality)\n";
            } else {
                echo '      ❌ Single user fetch failed: '.$singleUserResult['message']."\n";
            }
        }
    } else {
        echo '      ❌ Users data fetch failed: '.$usersResult['message']."\n";
    }

    // Test Admin Data (Dataadmin)
    echo "   b) Testing Admin Data Service...\n";
    $adminsResult = $userService->getAllUsers(['role' => 'admin']);
    if ($adminsResult['success']) {
        $adminCount = count($adminsResult['data']);
        echo "      ✅ Admin data fetched: $adminCount admins found\n";
    } else {
        echo '      ❌ Admin data fetch failed: '.$adminsResult['message']."\n";
    }

    // Test Reports Data (Pelaporan)
    echo "   c) Testing Reports Data Service...\n";
    $reportsResult = $reportService->getAllReports();
    if ($reportsResult['success']) {
        $reportCount = count($reportsResult['data']);
        echo "      ✅ Reports data fetched: $reportCount reports found\n";
    } else {
        echo '      ❌ Reports data fetch failed: '.$reportsResult['message']."\n";
    }

    // Test Notifications Data (pending reports)
    echo "   d) Testing Notifications Data Service...\n";
    $notificationsResult = $reportService->getPendingReports();
    if ($notificationsResult['success']) {
        $notificationCount = count($notificationsResult['data']);
        echo "      ✅ Notifications data fetched: $notificationCount notifications found\n";
    } else {
        echo '      ❌ Notifications data fetch failed: '.$notificationsResult['message']."\n";
    }

    // Test Publications Data
    echo "   e) Testing Publications Data Service...\n";
    $publicationsResult = $contentService->getAllPublications();
    if ($publicationsResult['success']) {
        $publicationCount = count($publicationsResult['data']);
        echo "      ✅ Publications data fetched: $publicationCount publications found\n";
    } else {
        echo '      ❌ Publications data fetch failed: '.$publicationsResult['message']."\n";
    }

    // Test Dashboard Statistics
    echo "   f) Testing Dashboard Statistics Service...\n";
    $statsResult = $dashboardService->getStatistics();
    if ($statsResult['success']) {
        echo "      ✅ Dashboard statistics fetched successfully\n";
        if (isset($statsResult['data'])) {
            $stats = $statsResult['data'];
            echo "      📊 Statistics Overview:\n";
            echo '         - Total Reports: '.($stats['total_pelaporan'] ?? 'N/A')."\n";
            echo '         - Pending Reports: '.($stats['pelaporan_pending'] ?? 'N/A')."\n";
            echo '         - Verified Reports: '.($stats['pelaporan_verified'] ?? 'N/A')."\n";
            echo '         - Total Victims: '.($stats['total_korban'] ?? 'N/A')."\n";
        }
    } else {
        echo '      ❌ Dashboard statistics fetch failed: '.$statsResult['message']."\n";
    }

    echo "\n3. Testing Profile Admin Functionality...\n";

    // Test profile fetching (using users endpoint)
    $profileEndpoint = $apiClient->getEndpoint('users', 'profile');
    $profileResponse = $apiClient->authenticatedRequest('GET', $profileEndpoint);

    if (isset($profileResponse['success']) && $profileResponse['success']) {
        echo "   ✅ Profile data fetch successful\n";
        if (isset($profileResponse['user'])) {
            $user = $profileResponse['user'];
            echo "   📄 Profile Data Available:\n";
            echo '      - Name: '.($user['name'] ?? 'N/A')."\n";
            echo '      - Date of Birth: '.($user['date_of_birth'] ?? 'N/A')."\n";
            echo '      - Place of Birth: '.($user['place_of_birth'] ?? 'N/A')."\n";
            echo '      - Phone: '.($user['phone'] ?? 'N/A')."\n";
            echo '      - Member Number: '.($user['member_number'] ?? 'N/A')."\n";
        }
    } else {
        echo '   ❌ Profile data fetch failed: '.($profileResponse['message'] ?? 'Unknown error')."\n";
    }

    echo "\n4. Testing Data Structure Compatibility...\n";

    // Check if the data returned is in array format (which should work with our fixed blade templates)
    if ($usersResult['success'] && ! empty($usersResult['data'])) {
        $firstUser = $usersResult['data'][0];
        $isArray = is_array($firstUser);
        echo '   📋 User data structure: '.($isArray ? 'Array ✅' : 'Object ⚠️')."\n";

        if ($isArray && isset($firstUser['id'])) {
            echo "   ✅ Array data structure compatible with fixed blade templates\n";
        } else {
            echo "   ⚠️  Data structure may need additional handling\n";
        }
    }

    if ($reportsResult['success'] && ! empty($reportsResult['data'])) {
        $firstReport = $reportsResult['data'][0];
        $isArray = is_array($firstReport);
        echo '   📋 Report data structure: '.($isArray ? 'Array ✅' : 'Object ⚠️')."\n";
    }

    if ($publicationsResult['success'] && ! empty($publicationsResult['data'])) {
        $firstPublication = $publicationsResult['data'][0];
        $isArray = is_array($firstPublication);
        echo '   📋 Publication data structure: '.($isArray ? 'Array ✅' : 'Object ⚠️')."\n";
    }

    echo "\n5. Summary of Dashboard Functionality:\n";
    echo "   ✅ Authentication: Working\n";
    echo '   '.($usersResult['success'] ? '✅' : '❌').' Datapengguna: '.($usersResult['success'] ? 'Working' : 'Failed')."\n";
    echo '   '.($adminsResult['success'] ? '✅' : '❌').' Dataadmin: '.($adminsResult['success'] ? 'Working' : 'Failed')."\n";
    echo '   '.($reportsResult['success'] ? '✅' : '❌').' Pelaporan: '.($reportsResult['success'] ? 'Working' : 'Failed')."\n";
    echo '   '.($notificationsResult['success'] ? '✅' : '❌').' Notifikasi: '.($notificationsResult['success'] ? 'Working' : 'Failed')."\n";
    echo '   '.($publicationsResult['success'] ? '✅' : '❌').' Publikasi: '.($publicationsResult['success'] ? 'Working' : 'Failed')."\n";
    echo '   '.($statsResult['success'] ? '✅' : '❌').' Dashboard Statistics: '.($statsResult['success'] ? 'Working' : 'Failed')."\n";

    $successCount = 0;
    $totalTests = 6;
    if ($usersResult['success']) {
        $successCount++;
    }
    if ($adminsResult['success']) {
        $successCount++;
    }
    if ($reportsResult['success']) {
        $successCount++;
    }
    if ($notificationsResult['success']) {
        $successCount++;
    }
    if ($publicationsResult['success']) {
        $successCount++;
    }
    if ($statsResult['success']) {
        $successCount++;
    }

    echo "\n📊 OVERALL DASHBOARD FUNCTIONALITY: $successCount/$totalTests tests passed\n";

    if ($successCount == $totalTests) {
        echo "🎉 ALL DASHBOARD PAGES SHOULD NOW DISPLAY DATA CORRECTLY!\n";
        echo "\n6. Next Steps for User:\n";
        echo "   ✨ Test the following dashboard pages:\n";
        echo "      • http://127.0.0.1:8001/Dataadmin (Admin Management)\n";
        echo "      • http://127.0.0.1:8001/pelaporan (Reports Management)\n";
        echo "      • http://127.0.0.1:8001/notifikasi (Notifications)\n";
        echo "      • http://127.0.0.1:8001/publikasi (Publications)\n";
        echo "   ✨ Try Update/Delete buttons to verify they work without errors\n";
        echo "   ✨ Check that missing data shows 'N/A' instead of errors\n";
    } else {
        echo "⚠️  Some dashboard pages may still have issues. Check individual page errors above.\n";
    }
} catch (Exception $e) {
    echo '❌ Test error: '.$e->getMessage()."\n";
    echo "Stack trace:\n".$e->getTraceAsString()."\n";
}

echo "\n=== FINAL COMPREHENSIVE DASHBOARD TEST COMPLETE ===\n";
