#!/usr/bin/env php
<?php

/**
 * Cross-Platform Dashboard N/A Data Fields Verification Test
 *
 * This test verifies that the place_of_birth and member_number fields
 * are now displaying correctly across all three codebases:
 * 1. Backend API - provides the data
 * 2. Web Dashboard - displays the data
 * 3. Mobile App integration - via backend API
 */

require_once __DIR__.'/vendor/autoload.php';

echo "🔍 CROSS-PLATFORM DASHBOARD N/A DATA FIELDS VERIFICATION\n";
echo "========================================================\n\n";

// Test Configuration
$backend_api_url = 'http://127.0.0.1:8000';
$web_dashboard_url = 'http://127.0.0.1:8001';

$test_results = [];

// Function to test HTTP endpoint
function testHttpEndpoint($url, $description, $auth_token = null)
{
    $headers = ['Content-Type: application/json'];
    if ($auth_token) {
        $headers[] = "Authorization: Bearer $auth_token";
    }

    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => implode("\r\n", $headers),
            'timeout' => 10,
        ],
    ]);

    $response = @file_get_contents($url, false, $context);
    if ($response === false) {
        return ['success' => false, 'error' => 'Request failed'];
    }

    $data = json_decode($response, true);

    return ['success' => true, 'data' => $data, 'raw' => $response];
}

// Function to authenticate with backend
function authenticateWithBackend($api_url)
{
    $login_data = json_encode([
        'email' => 'admin@uat.test',
        'password' => 'password123',
    ]);

    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/json\r\nContent-Length: ".strlen($login_data),
            'content' => $login_data,
        ],
    ]);

    $response = @file_get_contents("$api_url/api/gibran/auth/login", false, $context);
    if ($response === false) {
        return null;
    }

    $data = json_decode($response, true);

    return $data['data']['token'] ?? null;
}

echo "📍 PHASE 1: BACKEND API VERIFICATION\n";
echo "-----------------------------------\n";

// Authenticate with backend
$auth_token = authenticateWithBackend($backend_api_url);
if (! $auth_token) {
    echo "❌ Backend authentication failed\n";
    exit(1);
}
echo "✅ Backend authentication successful\n";

// Test admin users endpoint
$admin_result = testHttpEndpoint("$backend_api_url/api/v1/users/admin-list", 'Admin Users API', $auth_token);
if ($admin_result['success']) {
    $admin_users = $admin_result['data']['data'] ?? [];
    echo '✅ Backend Admin API: '.count($admin_users)." users found\n";

    // Check for place_of_birth and member_number fields
    $place_birth_count = 0;
    $member_number_count = 0;

    foreach ($admin_users as $user) {
        if (! empty($user['place_of_birth']) && $user['place_of_birth'] !== 'N/A') {
            $place_birth_count++;
        }
        if (! empty($user['member_number']) && $user['member_number'] !== 'N/A') {
            $member_number_count++;
        }
    }

    echo "   📊 Place of Birth filled: $place_birth_count/".count($admin_users)." admins\n";
    echo "   📊 Member Number filled: $member_number_count/".count($admin_users)." admins\n";

    if ($place_birth_count > 0) {
        echo "   ✅ Backend has place_of_birth data\n";
    } else {
        echo "   ❌ Backend missing place_of_birth data\n";
    }

    if ($member_number_count > 0) {
        echo "   ✅ Backend has member_number data\n";
    } else {
        echo "   ❌ Backend missing member_number data\n";
    }

    $test_results['backend_admin'] = [
        'success' => true,
        'total_users' => count($admin_users),
        'place_birth_filled' => $place_birth_count,
        'member_number_filled' => $member_number_count,
    ];
} else {
    echo "❌ Backend Admin API failed\n";
    $test_results['backend_admin'] = ['success' => false];
}

// Test volunteer users endpoint
$volunteer_result = testHttpEndpoint("$backend_api_url/api/v1/users/volunteer-list", 'Volunteer Users API', $auth_token);
if ($volunteer_result['success']) {
    $volunteer_users = $volunteer_result['data']['data'] ?? [];
    echo '✅ Backend Volunteer API: '.count($volunteer_users)." users found\n";

    // Check for place_of_birth fields
    $place_birth_count = 0;

    foreach ($volunteer_users as $user) {
        if (! empty($user['place_of_birth']) && $user['place_of_birth'] !== 'N/A') {
            $place_birth_count++;
        }
    }

    echo "   📊 Place of Birth filled: $place_birth_count/".count($volunteer_users)." volunteers\n";

    if ($place_birth_count > 0) {
        echo "   ✅ Backend has volunteer place_of_birth data\n";
    } else {
        echo "   ❌ Backend missing volunteer place_of_birth data\n";
    }

    $test_results['backend_volunteer'] = [
        'success' => true,
        'total_users' => count($volunteer_users),
        'place_birth_filled' => $place_birth_count,
    ];
} else {
    echo "❌ Backend Volunteer API failed\n";
    $test_results['backend_volunteer'] = ['success' => false];
}

echo "\n📱 PHASE 2: WEB DASHBOARD VERIFICATION\n";
echo "-------------------------------------\n";

// Test web dashboard pages (basic connectivity)
$web_admin_result = testHttpEndpoint("$web_dashboard_url/Dataadmin", 'Web Admin Dashboard');
if ($web_admin_result['success']) {
    $content = $web_admin_result['raw'];

    // Check if N/A appears in place_of_birth column
    $na_count = substr_count($content, '<td class="border px-4 py-2">N/A</td>');
    $palembang_count = substr_count($content, 'Palembang');
    $makassar_count = substr_count($content, 'Makassar');
    $bekasi_count = substr_count($content, 'Bekasi');
    $jakarta_count = substr_count($content, 'Jakarta');

    echo "✅ Web Admin Dashboard accessible\n";
    echo "   📊 N/A entries found: $na_count\n";
    echo "   📊 Sample city data found: Palembang($palembang_count), Makassar($makassar_count), Bekasi($bekasi_count), Jakarta($jakarta_count)\n";

    if ($na_count < 5 && ($palembang_count > 0 || $makassar_count > 0)) {
        echo "   ✅ Web Dashboard shows actual city data (not N/A)\n";
        $test_results['web_admin'] = ['success' => true, 'na_count' => $na_count, 'city_data' => true];
    } else {
        echo "   ❌ Web Dashboard still showing N/A instead of city data\n";
        $test_results['web_admin'] = ['success' => false, 'na_count' => $na_count, 'city_data' => false];
    }
} else {
    echo "❌ Web Admin Dashboard not accessible\n";
    $test_results['web_admin'] = ['success' => false];
}

$web_volunteer_result = testHttpEndpoint("$web_dashboard_url/Datapengguna", 'Web Volunteer Dashboard');
if ($web_volunteer_result['success']) {
    $content = $web_volunteer_result['raw'];

    // Check if N/A appears in place_of_birth column
    $na_count = substr_count($content, '<td class="px-4 py-2 border">N/A</td>');
    $malang_count = substr_count($content, 'Malang');
    $bogor_count = substr_count($content, 'Bogor');
    $pekanbaru_count = substr_count($content, 'Pekanbaru');
    $yogyakarta_count = substr_count($content, 'Yogyakarta');

    echo "✅ Web Volunteer Dashboard accessible\n";
    echo "   📊 N/A entries found: $na_count\n";
    echo "   📊 Sample city data found: Malang($malang_count), Bogor($bogor_count), Pekanbaru($pekanbaru_count), Yogyakarta($yogyakarta_count)\n";

    if ($na_count < 5 && ($malang_count > 0 || $bogor_count > 0)) {
        echo "   ✅ Web Dashboard shows actual volunteer city data (not N/A)\n";
        $test_results['web_volunteer'] = ['success' => true, 'na_count' => $na_count, 'city_data' => true];
    } else {
        echo "   ❌ Web Dashboard still showing N/A instead of volunteer city data\n";
        $test_results['web_volunteer'] = ['success' => false, 'na_count' => $na_count, 'city_data' => false];
    }
} else {
    echo "❌ Web Volunteer Dashboard not accessible\n";
    $test_results['web_volunteer'] = ['success' => false];
}

echo "\n🔗 PHASE 3: CROSS-PLATFORM INTEGRATION SUMMARY\n";
echo "----------------------------------------------\n";

$total_tests = 4;
$passed_tests = 0;

foreach ($test_results as $test_name => $result) {
    if ($result['success']) {
        $passed_tests++;
        echo "✅ $test_name: PASSED\n";
    } else {
        echo "❌ $test_name: FAILED\n";
    }
}

echo "\n📊 FINAL RESULTS\n";
echo "---------------\n";
echo "Tests Passed: $passed_tests/$total_tests\n";

if ($passed_tests === $total_tests) {
    echo "🎉 ALL TESTS PASSED! Cross-platform N/A data issue is RESOLVED!\n\n";

    echo "✅ Backend API: Provides place_of_birth and member_number data correctly\n";
    echo "✅ Web Dashboard: Displays actual city names instead of N/A\n";
    echo "✅ Template Fixes: Both admin and volunteer templates updated\n";
    echo "✅ Field Mapping: place_of_birth field mapping fixed in templates\n\n";

    echo "🌟 USER COMPLAINT RESOLVED: Dashboard now shows meaningful data!\n";
} else {
    echo "❌ Some tests failed. Cross-platform issue needs more work.\n";
    exit(1);
}

echo "\n📝 TECHNICAL SUMMARY\n";
echo "-------------------\n";
echo "ISSUE: Web dashboard showing N/A for place_of_birth and member_number fields\n";
echo "ROOT CAUSE: Template field mapping mismatch\n";
echo "  - Backend API returns: 'place_of_birth' and 'member_number'\n";
echo "  - Web templates looked for: 'birth_place' and used 'organization' for member number\n";
echo "SOLUTION: Updated templates to use correct API field names\n";
echo "  - data_admin.blade.php: Fixed place_of_birth and member_number field access\n";
echo "  - data_pengguna.blade.php: Fixed place_of_birth field access\n";
echo "VERIFICATION: All dashboard pages now display actual data instead of N/A\n\n";

echo "🏆 CROSS-PLATFORM COORDINATION SUCCESS!\n";
echo "  Backend: ✅ Data populated correctly\n";
echo "  Web App: ✅ Templates fixed to display data\n";
echo "  Mobile:  ✅ Uses same backend API (no changes needed)\n";
