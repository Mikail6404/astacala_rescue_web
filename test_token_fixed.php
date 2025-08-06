<?php

echo "=== GETTING FRESH TOKEN AND TESTING ENDPOINTS ===\n";
echo "Getting new authentication token and testing user management\n\n";

// Step 1: Login to get fresh token
echo "🔍 STEP 1: Getting fresh authentication token\n";
echo "==============================================\n";

$loginData = [
    'email' => 'volunteer@mobile.test',
    'password' => 'password123'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/api/gibran/auth/login');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($loginData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    echo "❌ Failed to login (HTTP $httpCode)\n";
    echo "Response: $response\n";
    exit(1);
}

$authData = json_decode($response, true);
echo "📝 Login response structure:\n";
echo "  Status: " . ($authData['status'] ?? 'N/A') . "\n";
echo "  Message: " . ($authData['message'] ?? 'N/A') . "\n";

// Try different token paths
$token = $authData['data']['access_token'] ??
    $authData['data']['tokens']['accessToken'] ??
    $authData['access_token'] ??
    null;

if (!$token) {
    echo "❌ No token found in response\n";
    echo "Available keys in data: " . implode(', ', array_keys($authData['data'] ?? [])) . "\n";
    echo "Full response: $response\n";
    exit(1);
}

echo "✅ Login successful, token obtained: " . substr($token, 0, 20) . "...\n";

// Get user info from login response
$userInfo = $authData['data']['user'] ?? [];
echo "👤 User from login response:\n";
echo "  📧 Email: " . ($userInfo['email'] ?? 'N/A') . "\n";
echo "  👤 Name: " . ($userInfo['name'] ?? 'N/A') . "\n";
echo "  🔑 Role: " . ($userInfo['role'] ?? 'N/A') . "\n";
echo "  🆔 ID: " . ($userInfo['id'] ?? 'N/A') . "\n";

$userRole = $userInfo['role'] ?? 'UNKNOWN';
echo "\n📋 User Role Analysis:\n";
echo "Current role: $userRole\n";
echo "Admin endpoints require: admin or super_admin role\n";
echo "User has admin access: " . (in_array($userRole, ['admin', 'super_admin', 'ADMIN']) ? 'YES' : 'NO') . "\n";

echo "\n🔍 STEP 2: Testing problematic endpoints\n";
echo "========================================\n";

// Test endpoints that are failing
$endpoints = [
    'User Statistics' => '/api/v1/users/statistics',
    'Admin List' => '/api/v1/users/admin-list',
    'User Profile' => '/api/v1/users/profile',
    'Gibran Dashboard Stats' => '/api/gibran/dashboard/statistics',
    'Gibran Pelaporans' => '/api/gibran/pelaporans',
    'Gibran Berita' => '/api/gibran/berita-bencana'
];

foreach ($endpoints as $name => $endpoint) {
    echo "\n🔍 Testing $name ($endpoint):\n";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000' . $endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        'Content-Type: application/json',
        'Authorization: Bearer ' . $token
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    echo "  HTTP Code: $httpCode\n";

    if ($httpCode === 200) {
        echo "  ✅ SUCCESS\n";
        $data = json_decode($response, true);
        if (isset($data['data'])) {
            if (is_array($data['data'])) {
                echo "  📊 Data count: " . count($data['data']) . "\n";
            } else {
                echo "  📊 Data type: " . gettype($data['data']) . "\n";
            }
        }
    } elseif ($httpCode === 401) {
        echo "  ❌ UNAUTHORIZED - Token may be invalid\n";
    } elseif ($httpCode === 403) {
        echo "  ❌ FORBIDDEN - User lacks required permissions\n";
        echo "  💡 This endpoint requires admin role, user has: $userRole\n";
    } elseif ($httpCode === 404) {
        echo "  ❌ NOT FOUND - Endpoint doesn't exist\n";
    } else {
        echo "  ❌ ERROR - HTTP $httpCode\n";
        $errorData = json_decode($response, true);
        if (isset($errorData['message'])) {
            echo "  📝 Message: " . $errorData['message'] . "\n";
        }
        echo "  📝 Response: " . substr($response, 0, 200) . "\n";
    }
}

echo "\n🔍 STEP 3: Checking what endpoints ARE working\n";
echo "==============================================\n";

// Test some basic endpoints that should work
$workingEndpoints = [
    'Auth Me (v1)' => '/api/v1/auth/me',
    'Reports List (v1)' => '/api/v1/reports',
    'User Reports' => '/api/v1/reports/my-reports',
];

foreach ($workingEndpoints as $name => $endpoint) {
    echo "\n🔍 Testing $name ($endpoint):\n";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000' . $endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        'Content-Type: application/json',
        'Authorization: Bearer ' . $token
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    echo "  HTTP Code: $httpCode\n";

    if ($httpCode === 200) {
        echo "  ✅ SUCCESS\n";
    } else {
        echo "  ❌ FAILED\n";
        echo "  📝 Response: " . substr($response, 0, 100) . "\n";
    }
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "ENDPOINT TESTING COMPLETED\n";
echo "SUMMARY: User role is '$userRole' - admin features require admin/super_admin role\n";
echo str_repeat("=", 60) . "\n";
