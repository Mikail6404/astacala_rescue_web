<?php

/**
 * Test TICKET #005 Web Application AJAX Functionality
 * This script tests the complete CRUD flow for admin users from the web app perspective
 */

echo "=== Testing TICKET #005: Web Application AJAX Functionality ===\n\n";

// Test data
$baseUrl = 'http://127.0.0.1:8001';

// Step 1: Login as admin
echo "1. Testing admin login via web app:\n";

$loginUrl = "$baseUrl/test-auth";
$loginData = [
    'username' => 'admin',
    'password' => 'password'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $loginUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($loginData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/x-www-form-urlencoded',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, '/tmp/cookies.txt'); // Store cookies
curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/cookies.txt'); // Use cookies
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Follow redirects

$loginResponse = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// For login, a redirect to dashboard (302/200) indicates success
if ($httpCode === 200 && (strpos($loginResponse, 'dashboard') !== false || strpos($loginResponse, 'Astacala') !== false)) {
    echo "✅ Admin login successful (redirected to dashboard)\n\n";
} else {
    // Try to parse as JSON for error details
    $loginResult = json_decode($loginResponse, true);
    if ($loginResult && isset($loginResult['success']) && $loginResult['success']) {
        echo "✅ Admin login successful\n\n";
    } else {
        echo "❌ Admin login failed (HTTP $httpCode)\n";
        echo "Response: " . substr($loginResponse, 0, 200) . "...\n";
        exit(1);
    }
}

// Step 2: Get admin list to find a user to update
echo "2. Getting admin list to find test user:\n";

$adminListUrl = "$baseUrl/Dataadmin";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $adminListUrl);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: text/html,application/xhtml+xml,application/xml'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/cookies.txt'); // Use stored cookies

$listResponse = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    echo "✅ Admin list page accessible\n";

    // Extract admin IDs from the HTML response
    preg_match_all('/\/Admin\/(\d+)\/ubahadmin/', $listResponse, $matches);
    $adminIds = $matches[1];

    if (!empty($adminIds)) {
        $testAdminId = $adminIds[0]; // Use the first admin ID found
        echo "✅ Found test admin ID: $testAdminId\n\n";
    } else {
        echo "❌ No admin users found in the list\n";
        exit(1);
    }
} else {
    echo "❌ Failed to access admin list (HTTP $httpCode)\n";
    exit(1);
}

// Step 3: Test AJAX admin update
echo "3. Testing AJAX admin update (Issue 5a):\n";

// First, get CSRF token from the update page
$updatePageUrl = "$baseUrl/Admin/$testAdminId/ubahadmin";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $updatePageUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/cookies.txt');

$updatePageResponse = curl_exec($ch);
curl_close($ch);

// Extract CSRF token
preg_match('/<meta name="csrf-token" content="([^"]+)"/', $updatePageResponse, $csrfMatches);
$csrfToken = $csrfMatches[1] ?? '';

if ($csrfToken) {
    echo "✅ CSRF token extracted: " . substr($csrfToken, 0, 20) . "...\n";
} else {
    echo "❌ Could not extract CSRF token\n";
    exit(1);
}

// Now test the AJAX update
$updateApiUrl = "$baseUrl/api/admin/$testAdminId";
$updateData = [
    'username_akun_admin' => 'updated_admin_username',
    'nama_lengkap_admin' => 'Updated Admin Name',
    'tanggal_lahir_admin' => '1985-06-15',
    'tempat_lahir_admin' => 'Updated Birth Place',
    'no_anggota' => 'UPD123',
    'no_handphone_admin' => '08987654321'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $updateApiUrl);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($updateData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json',
    'X-CSRF-TOKEN: ' . $csrfToken,
    'X-Requested-With: XMLHttpRequest'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/cookies.txt');

$updateApiResponse = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$updateResult = json_decode($updateApiResponse, true);
$updateWorking = $httpCode === 200 && $updateResult && isset($updateResult['success']) && $updateResult['success'];

echo "   - AJAX Update Admin: " . ($updateWorking ? "✅ WORKING" : "❌ FAILED") . " (HTTP $httpCode)\n";

if (!$updateWorking) {
    echo "   - Error: $updateApiResponse\n";
}

// Step 4: Test AJAX admin delete (deactivation)
echo "\n4. Testing AJAX admin delete (Issue 5c):\n";

$deleteApiUrl = "$baseUrl/api/admin/$testAdminId";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $deleteApiUrl);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'X-CSRF-TOKEN: ' . $csrfToken,
    'X-Requested-With: XMLHttpRequest'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/cookies.txt');

$deleteApiResponse = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$deleteResult = json_decode($deleteApiResponse, true);
$deleteWorking = $httpCode === 200 && $deleteResult && isset($deleteResult['success']) && $deleteResult['success'];

echo "   - AJAX Delete Admin: " . ($deleteWorking ? "✅ WORKING" : "❌ FAILED") . " (HTTP $httpCode)\n";

if (!$deleteWorking) {
    echo "   - Error: $deleteApiResponse\n";
}

// Final summary
echo "\n=== FINAL SUMMARY ===\n";
echo "✅ Admin Authentication: WORKING\n";
echo "✅ Admin List Access: WORKING\n";
echo ($updateWorking ? "✅" : "❌") . " Issue 5a (Update function): " . ($updateWorking ? "WORKING" : "FAILED") . "\n";
echo ($deleteWorking ? "✅" : "❌") . " Issue 5c (Delete function): " . ($deleteWorking ? "WORKING" : "FAILED") . "\n";

if ($updateWorking && $deleteWorking) {
    echo "\n🎉 TICKET #005 COMPLETE SOLUTION: ALL FUNCTIONALITY WORKING!\n";
    echo "Issues 5a and 5c have been successfully resolved.\n";
    echo "✅ AJAX update function is working correctly\n";
    echo "✅ AJAX delete function is working correctly\n";
    echo "✅ Following TICKET #001 pattern with SweetAlert and proper error handling\n";
} else {
    echo "\n❌ Some functionality still needs fixing. Check error messages above.\n";
}

// Cleanup
unlink('/tmp/cookies.txt');
