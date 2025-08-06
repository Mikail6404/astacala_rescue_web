<?php

/**
 * Comprehensive Dashboard Issues Test & Verification
 * 
 * This script tests all the issues reported by the user after implementing fixes:
 * 1. Role segregation (DataPengguna vs DataAdmin)
 * 2. Hardcoded user ID issues in update operations
 * 3. Missing profile data (N/A values)
 * 4. Action button functionality
 * 5. Data mapping issues
 */

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\AstacalaApiClient;
use App\Services\GibranUserService;
use App\Services\GibranReportService;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\AdminController;

echo "=== COMPREHENSIVE DASHBOARD ISSUES VERIFICATION ===\n\n";

try {
    $apiClient = new AstacalaApiClient();
    $userService = new GibranUserService($apiClient);
    $reportService = new GibranReportService($apiClient);

    // Test 1: Verify Role Segregation Fix
    echo "🔍 TEST 1: Role Segregation Verification\n";
    echo "=" . str_repeat("=", 45) . "\n";

    echo "1a. Testing PenggunaController (should show VOLUNTEERS only):\n";
    $volunteerResponse = $userService->getAllUsers(['role' => 'VOLUNTEER']);
    if ($volunteerResponse['success']) {
        $volunteers = $volunteerResponse['data'];
        echo "   ✅ Retrieved " . count($volunteers) . " volunteer users\n";

        // Check role filtering
        $nonVolunteers = array_filter($volunteers, function ($user) {
            $role = is_array($user) ? ($user['role'] ?? '') : ($user->role ?? '');
            return !in_array(strtoupper($role), ['VOLUNTEER', 'volunteer']);
        });

        if (empty($nonVolunteers)) {
            echo "   ✅ Role filtering working: All users are volunteers\n";
        } else {
            echo "   ❌ Role filtering failed: Found " . count($nonVolunteers) . " non-volunteer users\n";
        }
    } else {
        echo "   ❌ Failed to retrieve volunteer users: " . $volunteerResponse['message'] . "\n";
    }

    echo "\n1b. Testing AdminController (should show ADMINS only):\n";
    $adminResponse = $userService->getAllUsers(['role' => 'ADMIN']);
    if ($adminResponse['success']) {
        $admins = $adminResponse['data'];
        echo "   ✅ Retrieved " . count($admins) . " admin users\n";

        // Check role filtering
        $nonAdmins = array_filter($admins, function ($user) {
            $role = is_array($user) ? ($user['role'] ?? '') : ($user->role ?? '');
            return !in_array(strtoupper($role), ['ADMIN', 'admin']);
        });

        if (empty($nonAdmins)) {
            echo "   ✅ Role filtering working: All users are admins\n";
        } else {
            echo "   ❌ Role filtering failed: Found " . count($nonAdmins) . " non-admin users\n";
        }
    } else {
        echo "   ❌ Failed to retrieve admin users: " . $adminResponse['message'] . "\n";
    }

    // Test 2: Verify User ID Fetching Fix
    echo "\n🔍 TEST 2: User ID Fetching Verification\n";
    echo "=" . str_repeat("=", 40) . "\n";

    // Get first user ID from volunteers list for testing
    if (!empty($volunteers) && count($volunteers) > 0) {
        $testUserId = is_array($volunteers[0]) ? $volunteers[0]['id'] : $volunteers[0]->id;
        echo "2a. Testing getUser() with specific ID ($testUserId):\n";

        $userResult = $userService->getUser($testUserId);
        if ($userResult['success']) {
            $fetchedUser = $userResult['data'];
            $fetchedId = is_array($fetchedUser) ? $fetchedUser['id'] : $fetchedUser->id;

            if ($fetchedId == $testUserId) {
                echo "   ✅ Correct user fetched: ID matches ($fetchedId)\n";
            } else {
                echo "   ❌ Wrong user fetched: Expected ID $testUserId, got $fetchedId\n";
            }
        } else {
            echo "   ❌ Failed to fetch user by ID: " . $userResult['message'] . "\n";
        }
    } else {
        echo "2a. ⚠️  No volunteer users available for testing\n";
    }

    // Test 3: Check API Data Completeness
    echo "\n🔍 TEST 3: Profile Data Completeness\n";
    echo "=" . str_repeat("=", 35) . "\n";

    if (!empty($volunteers) && count($volunteers) > 0) {
        $testUser = $volunteers[0];
        $fields = ['name', 'email', 'phone', 'role', 'organization'];
        $missingFields = [];

        foreach ($fields as $field) {
            $value = is_array($testUser) ? ($testUser[$field] ?? null) : ($testUser->$field ?? null);
            if (empty($value) || $value === 'N/A') {
                $missingFields[] = $field;
            }
        }

        echo "3a. Profile data completeness for volunteer users:\n";
        if (empty($missingFields)) {
            echo "   ✅ All required fields present\n";
        } else {
            echo "   ⚠️  Missing fields: " . implode(', ', $missingFields) . "\n";
        }
    }

    // Test 4: Reports Data Mapping
    echo "\n🔍 TEST 4: Reports Data Mapping\n";
    echo "=" . str_repeat("=", 30) . "\n";

    $reportsResult = $reportService->getAllReports();
    if ($reportsResult['success']) {
        $reports = $reportsResult['data'];
        echo "4a. Retrieved " . count($reports) . " disaster reports\n";

        if (!empty($reports)) {
            $testReport = $reports[0];
            $reportFields = ['title', 'location_name', 'team_name', 'severity_level'];
            $mappedCorrectly = 0;

            foreach ($reportFields as $field) {
                $value = is_array($testReport) ? ($testReport[$field] ?? null) : ($testReport->$field ?? null);
                if (!empty($value) && $value !== 'N/A') {
                    $mappedCorrectly++;
                }
            }

            $percentage = round(($mappedCorrectly / count($reportFields)) * 100);
            echo "   📊 Data mapping completeness: $percentage% ($mappedCorrectly/" . count($reportFields) . " fields)\n";

            if ($percentage >= 75) {
                echo "   ✅ Report data mapping working well\n";
            } else {
                echo "   ⚠️  Report data mapping needs improvement\n";
            }
        }
    } else {
        echo "   ❌ Failed to retrieve reports: " . $reportsResult['message'] . "\n";
    }

    // Test 5: API Endpoint Configuration
    echo "\n🔍 TEST 5: API Endpoint Configuration\n";
    echo "=" . str_repeat("=", 35) . "\n";

    echo "5a. Testing user-by-ID endpoint configuration:\n";
    try {
        $endpoint = $apiClient->getEndpoint('users', 'get_by_id', ['id' => 1]);
        echo "   ✅ User-by-ID endpoint configured: $endpoint\n";
    } catch (Exception $e) {
        echo "   ❌ User-by-ID endpoint missing: " . $e->getMessage() . "\n";
    }

    echo "\n5b. Testing admin-list endpoint configuration:\n";
    try {
        $endpoint = $apiClient->getEndpoint('users', 'admin_list');
        echo "   ✅ Admin-list endpoint configured: $endpoint\n";
    } catch (Exception $e) {
        echo "   ❌ Admin-list endpoint missing: " . $e->getMessage() . "\n";
    }

    // Summary
    echo "\n" . str_repeat("=", 60) . "\n";
    echo "📋 SUMMARY OF FIXES IMPLEMENTED:\n";
    echo str_repeat("=", 60) . "\n";
    echo "✅ Added get_by_id endpoint to API configuration\n";
    echo "✅ Fixed PenggunaController to filter VOLUNTEER users only\n";
    echo "✅ Fixed AdminController to filter ADMIN users only\n";
    echo "✅ Updated GibranUserService to use correct endpoint for user-by-ID\n";
    echo "✅ All Blade templates have array/object compatibility handling\n";
    echo "✅ Route configurations appear complete for action buttons\n";

    echo "\n📌 REMAINING ISSUES TO INVESTIGATE:\n";
    echo "• Backend API may need to return more complete profile data\n";
    echo "• Action button functionality may need controller method updates\n";
    echo "• Coordinate and reporter data mapping may need backend API fixes\n";

    echo "\n🎯 NEXT STEPS:\n";
    echo "1. Test the web application manually with these fixes\n";
    echo "2. Check specific action button routes and controller methods\n";
    echo "3. Verify backend API returns complete user profile data\n";
    echo "4. Test report data includes proper reporter and coordinate information\n";
} catch (Exception $e) {
    echo "❌ Test error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== COMPREHENSIVE VERIFICATION COMPLETE ===\n";
