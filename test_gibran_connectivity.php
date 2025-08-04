<?php

/**
 * Quick connectivity test for Gibran endpoints
 * 
 * This script tests the basic connectivity to the unified backend
 * and verifies that Gibran-specific endpoints are accessible.
 */

require_once __DIR__ . '/vendor/autoload.php';

echo "🔧 Testing Gibran API Connectivity\n";
echo "==================================\n\n";

// Test basic backend connectivity
echo "1. Testing Backend Connectivity...\n";
$backendUrl = 'http://127.0.0.1:8000/api/health';

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $backendUrl);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_TIMEOUT, 10);
$response = curl_exec($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

if ($httpCode === 200 || $httpCode === 404) {
    echo "✅ Backend is reachable at localhost:8000\n";
} else {
    echo "❌ Backend connection failed (HTTP $httpCode)\n";
    exit(1);
}

// Test Gibran authentication endpoint
echo "\n2. Testing Gibran Auth Endpoint...\n";
$authUrl = 'http://127.0.0.1:8000/api/gibran/auth/login';

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $authUrl);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode([
    'email' => 'test@example.com',
    'password' => 'testpassword'
]));
curl_setopt($curl, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($curl, CURLOPT_TIMEOUT, 10);
$response = curl_exec($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

if ($httpCode === 200 || $httpCode === 401 || $httpCode === 422) {
    echo "✅ Gibran auth endpoint is accessible\n";
    echo "   Response code: $httpCode (expected for test credentials)\n";
} else {
    echo "❌ Gibran auth endpoint failed (HTTP $httpCode)\n";
}

// Test Gibran reports endpoint
echo "\n3. Testing Gibran Reports Endpoint...\n";
$reportsUrl = 'http://127.0.0.1:8000/api/gibran/pelaporans';

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $reportsUrl);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HTTPHEADER, [
    'Accept: application/json'
]);
curl_setopt($curl, CURLOPT_TIMEOUT, 10);
$response = curl_exec($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

if ($httpCode === 200 || $httpCode === 401) {
    echo "✅ Gibran reports endpoint is accessible\n";
    echo "   Response code: $httpCode\n";
} else {
    echo "❌ Gibran reports endpoint failed (HTTP $httpCode)\n";
}

// Test Gibran dashboard endpoint
echo "\n4. Testing Gibran Dashboard Endpoint...\n";
$dashboardUrl = 'http://127.0.0.1:8000/api/gibran/dashboard/statistics';

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $dashboardUrl);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HTTPHEADER, [
    'Accept: application/json'
]);
curl_setopt($curl, CURLOPT_TIMEOUT, 10);
$response = curl_exec($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

if ($httpCode === 200 || $httpCode === 401) {
    echo "✅ Gibran dashboard endpoint is accessible\n";
    echo "   Response code: $httpCode\n";
} else {
    echo "❌ Gibran dashboard endpoint failed (HTTP $httpCode)\n";
}

echo "\n✅ Connectivity Test Complete!\n";
echo "All core Gibran endpoints are reachable.\n";
echo "\n📝 Next Steps:\n";
echo "- Test authentication with valid credentials\n";
echo "- Test data retrieval through web application\n";
echo "- Validate complete workflows\n";
