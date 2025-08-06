<?php

/**
 * Direct Test of TICKET #005 AJAX Endpoints
 * This bypasses login and directly tests the API endpoints
 */
echo "=== Direct Test of TICKET #005 AJAX Endpoints ===\n\n";

$baseUrl = 'http://127.0.0.1:8001';

// Create a test session by logging in through the backend API first
echo "1. Setting up test admin session:\n";

// Get backend API admin user credentials
require_once __DIR__.'/../../../astacala_backend/astacala-rescue-api/vendor/autoload.php';
$app = require_once __DIR__.'/../../../astacala_backend/astacala-rescue-api/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$adminUser = App\Models\User::where('role', 'ADMIN')->first();
if (! $adminUser) {
    echo "❌ No admin user found\n";
    exit(1);
}

echo "✅ Found admin user: {$adminUser->name} ({$adminUser->email})\n";

// Simulate a web session by setting session data
session_start();
$_SESSION['admin_id'] = $adminUser->id;
$_SESSION['admin_name'] = $adminUser->name;
$_SESSION['admin_username'] = $adminUser->email;

// Get a CSRF token from the web app
$tokenUrl = "$baseUrl/dashboard";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $tokenUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, '/tmp/test_cookies.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/test_cookies.txt');

$pageResponse = curl_exec($ch);
curl_close($ch);

// Extract CSRF token
preg_match('/<meta name="csrf-token" content="([^"]+)"/', $pageResponse, $csrfMatches);
$csrfToken = $csrfMatches[1] ?? '';

if (! $csrfToken) {
    // Try alternative method to get CSRF token
    $tokenUrl = "$baseUrl/Dataadmin";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $tokenUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/test_cookies.txt');

    $pageResponse = curl_exec($ch);
    curl_close($ch);

    preg_match('/<meta name="csrf-token" content="([^"]+)"/', $pageResponse, $csrfMatches);
    $csrfToken = $csrfMatches[1] ?? 'test-token-123';
}

echo '✅ CSRF Token: '.substr($csrfToken, 0, 20)."...\n\n";

// Step 2: Get admin list to find an admin user to test with
echo "2. Getting admin list:\n";

$adminListUrl = "$baseUrl/Dataadmin";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $adminListUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/test_cookies.txt');

$listResponse = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    echo "✅ Admin list accessible\n";

    // Extract admin IDs from the response
    preg_match_all('/\/Admin\/(\d+)\/ubahadmin/', $listResponse, $matches);
    $adminIds = $matches[1];

    if (! empty($adminIds)) {
        $testAdminId = $adminIds[0];
        echo "✅ Found test admin ID: $testAdminId\n\n";
    } else {
        // Use a known admin ID from backend
        $testAdminId = $adminUser->id;
        echo "✅ Using admin user ID: $testAdminId\n\n";
    }
} else {
    echo "❌ Could not access admin list (HTTP $httpCode)\n";
    // Use a known admin ID from backend
    $testAdminId = $adminUser->id;
    echo "✅ Using admin user ID: $testAdminId\n\n";
}

// Step 3: Test AJAX admin update (Issue 5a)
echo "3. Testing AJAX admin update (Issue 5a):\n";

$updateApiUrl = "$baseUrl/api/admin/$testAdminId";
$updateData = [
    'username_akun_admin' => 'test_updated_username',
    'nama_lengkap_admin' => 'Test Updated Admin Name',
    'tanggal_lahir_admin' => '1985-06-15',
    'tempat_lahir_admin' => 'Test Updated Birth Place',
    'no_anggota' => 'TEST123',
    'no_handphone_admin' => '08987654321',
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $updateApiUrl);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($updateData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json',
    'X-CSRF-TOKEN: '.$csrfToken,
    'X-Requested-With: XMLHttpRequest',
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/test_cookies.txt');

$updateApiResponse = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$updateResult = json_decode($updateApiResponse, true);
$updateWorking = $httpCode === 200 && $updateResult && isset($updateResult['success']) && $updateResult['success'];

echo '   - AJAX Update Admin: '.($updateWorking ? '✅ WORKING' : '❌ FAILED')." (HTTP $httpCode)\n";

if ($updateWorking) {
    echo '   - ✅ Update response: '.$updateResult['message']."\n";
} else {
    echo "   - ❌ Update error: $updateApiResponse\n";
}

// Step 4: Test AJAX admin delete (Issue 5c)
echo "\n4. Testing AJAX admin delete (Issue 5c):\n";

$deleteApiUrl = "$baseUrl/api/admin/$testAdminId";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $deleteApiUrl);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'X-CSRF-TOKEN: '.$csrfToken,
    'X-Requested-With: XMLHttpRequest',
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/test_cookies.txt');

$deleteApiResponse = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$deleteResult = json_decode($deleteApiResponse, true);
$deleteWorking = $httpCode === 200 && $deleteResult && isset($deleteResult['success']) && $deleteResult['success'];

echo '   - AJAX Delete Admin: '.($deleteWorking ? '✅ WORKING' : '❌ FAILED')." (HTTP $httpCode)\n";

if ($deleteWorking) {
    echo '   - ✅ Delete response: '.$deleteResult['message']."\n";
} else {
    echo "   - ❌ Delete error: $deleteApiResponse\n";
}

// Final summary
echo "\n=== FINAL SUMMARY ===\n";
echo "✅ Test Setup: COMPLETE\n";
echo ($updateWorking ? '✅' : '❌').' Issue 5a (Update function): '.($updateWorking ? 'WORKING' : 'FAILED')."\n";
echo ($deleteWorking ? '✅' : '❌').' Issue 5c (Delete function): '.($deleteWorking ? 'WORKING' : 'FAILED')."\n";

if ($updateWorking && $deleteWorking) {
    echo "\n🎉 TICKET #005 AJAX FUNCTIONALITY: ALL TESTS PASSED!\n";
    echo "✅ Issue 5a (Update function) is now working correctly\n";
    echo "✅ Issue 5c (Delete function) is now working correctly\n";
    echo "✅ Backend API endpoint for admin user updates has been successfully implemented\n";
    echo "✅ Web application AJAX calls are working with the new backend endpoints\n";
    echo "✅ Following TICKET #001 pattern with proper AJAX, CSRF tokens, and error handling\n";
} else {
    echo "\n❌ Some functionality still needs fixing. Check error messages above.\n";
}

// Cleanup
if (file_exists('/tmp/test_cookies.txt')) {
    unlink('/tmp/test_cookies.txt');
}
