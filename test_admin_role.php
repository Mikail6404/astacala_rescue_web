<?php

echo "=== TESTING ROLE MIDDLEWARE WITH ADMIN TOKEN ===\n";
echo "Testing role-based access with proper admin credentials\n\n";

// Use the admin token we created
$adminLoginData = [
    'email' => 'admin@uat.test',
    'password' => 'password123'
];

echo "🔍 STEP 1: Getting admin token\n";
echo "==============================\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/api/gibran/auth/login');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($adminLoginData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    echo "❌ Failed to get admin token\n";
    exit(1);
}

$authData = json_decode($response, true);
$adminToken = $authData['data']['access_token'];
$adminUser = $authData['data']['user'];

echo "✅ Admin token obtained\n";
echo "  👤 Name: " . $adminUser['name'] . "\n";
echo "  🔑 Role: " . $adminUser['role'] . "\n";
echo "  🆔 ID: " . $adminUser['id'] . "\n";

echo "\n🔍 STEP 2: Testing role-protected endpoints\n";
echo "==========================================\n";

// Test the exact endpoints that require admin role
$adminEndpoints = [
    'Admin List' => '/api/v1/users/admin-list',
    'User Statistics' => '/api/v1/users/statistics',
    'Create Admin' => '/api/v1/users/create-admin',
    'Publications Admin' => '/api/v1/publications',
];

foreach ($adminEndpoints as $name => $endpoint) {
    echo "\n🔍 Testing $name ($endpoint):\n";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000' . $endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        'Content-Type: application/json',
        'Authorization: Bearer ' . $adminToken
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    echo "  HTTP Code: $httpCode\n";

    if ($httpCode === 200) {
        echo "  ✅ SUCCESS - Admin access granted\n";
        $data = json_decode($response, true);
        if (isset($data['data']) && is_array($data['data'])) {
            echo "  📊 Data count: " . count($data['data']) . "\n";
        }
    } elseif ($httpCode === 403) {
        echo "  ❌ FORBIDDEN - Role middleware blocking access\n";
        $errorData = json_decode($response, true);
        echo "  📝 Message: " . ($errorData['message'] ?? 'No message') . "\n";
        echo "  🔑 User Role: " . ($errorData['user_role'] ?? 'Unknown') . "\n";
        echo "  📋 Required Roles: " . implode(', ', $errorData['required_roles'] ?? []) . "\n";
    } elseif ($httpCode === 404) {
        echo "  ❌ NOT FOUND - Endpoint doesn't exist\n";
    } else {
        echo "  ❌ ERROR - HTTP $httpCode\n";
        $errorData = json_decode($response, true);
        echo "  📝 Response: " . substr($response, 0, 200) . "\n";
    }
}

echo "\n🔍 STEP 3: Testing case sensitivity issue\n";
echo "=========================================\n";

// Check if the role case is the issue
echo "Admin user role from login: '" . $adminUser['role'] . "'\n";
echo "Middleware expects: 'admin' or 'super_admin'\n";
echo "Case match: " . (strtolower($adminUser['role']) === 'admin' ? 'YES' : 'NO') . "\n";

if (strtolower($adminUser['role']) !== 'admin') {
    echo "\n💡 ISSUE IDENTIFIED: Role case mismatch!\n";
    echo "  Backend stores: '" . $adminUser['role'] . "'\n";
    echo "  Middleware expects: 'admin'\n";
    echo "  Solution: Fix role case in backend or middleware\n";
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "ROLE MIDDLEWARE TESTING COMPLETED\n";
echo str_repeat("=", 60) . "\n";
