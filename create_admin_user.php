<?php

echo "=== CREATING ADMIN USER FOR UAT TESTING ===\n";
echo "Creating admin user to enable full dashboard functionality\n\n";

// Step 1: Create admin user via backend API
echo "🔍 STEP 1: Creating admin user\n";
echo "==============================\n";

$adminData = [
    'name' => 'UAT Admin',
    'email' => 'admin@uat.test',
    'password' => 'password123',
    'password_confirmation' => 'password123',
    'phone' => '+62812345678',
    'role' => 'ADMIN',
    'organization' => 'Astacala Rescue Team'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/api/v1/auth/register');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($adminData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Registration attempt:\n";
echo "  Email: {$adminData['email']}\n";
echo "  Role: {$adminData['role']}\n";
echo "  HTTP Code: $httpCode\n";

if ($httpCode === 201 || $httpCode === 200) {
    echo "✅ Admin user created successfully\n";
    $regData = json_decode($response, true);
    echo "  User ID: " . ($regData['data']['user']['id'] ?? 'N/A') . "\n";
} elseif ($httpCode === 422) {
    echo "⚠️  User may already exist\n";
    $errorData = json_decode($response, true);
    echo "  Message: " . ($errorData['message'] ?? 'Validation error') . "\n";
} else {
    echo "❌ Failed to create admin user\n";
    echo "  Response: $response\n";
}

// Step 2: Try to login with admin user
echo "\n🔍 STEP 2: Testing admin login\n";
echo "==============================\n";

$adminLoginData = [
    'email' => 'admin@uat.test',
    'password' => 'password123'
];

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

if ($httpCode === 200) {
    $authData = json_decode($response, true);
    $adminToken = $authData['data']['access_token'] ?? null;
    $adminUser = $authData['data']['user'] ?? [];

    echo "✅ Admin login successful\n";
    echo "  👤 Name: " . ($adminUser['name'] ?? 'N/A') . "\n";
    echo "  🔑 Role: " . ($adminUser['role'] ?? 'N/A') . "\n";
    echo "  🆔 ID: " . ($adminUser['id'] ?? 'N/A') . "\n";

    if ($adminToken) {
        echo "  🎫 Token: " . substr($adminToken, 0, 20) . "...\n";

        // Step 3: Test admin endpoints
        echo "\n🔍 STEP 3: Testing admin endpoints with admin token\n";
        echo "==================================================\n";

        $adminEndpoints = [
            'User Statistics' => '/api/v1/users/statistics',
            'Admin List' => '/api/v1/users/admin-list',
            'User Profile' => '/api/v1/users/profile',
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
                echo "  ✅ SUCCESS\n";
            } elseif ($httpCode === 404) {
                echo "  ❌ NOT FOUND - Endpoint doesn't exist in backend\n";
            } elseif ($httpCode === 403) {
                echo "  ❌ FORBIDDEN - Still lacks permissions\n";
            } else {
                echo "  ❌ ERROR - HTTP $httpCode\n";
                $errorData = json_decode($response, true);
                if (isset($errorData['message'])) {
                    echo "  📝 Message: " . $errorData['message'] . "\n";
                }
            }
        }
    }
} else {
    echo "❌ Admin login failed (HTTP $httpCode)\n";
    echo "  Response: $response\n";
}

// Step 4: Alternative - upgrade existing user
echo "\n🔍 STEP 4: Alternative - Check if we can upgrade existing user\n";
echo "=============================================================\n";

echo "💡 For UAT purposes, we could:\n";
echo "1. Create admin user with proper role\n";
echo "2. Update existing volunteer@mobile.test to admin role\n";
echo "3. Use gibran endpoints that work with VOLUNTEER role\n";
echo "4. Fix missing backend endpoints\n";

echo "\n" . str_repeat("=", 60) . "\n";
echo "ADMIN USER CREATION TESTING COMPLETED\n";
echo str_repeat("=", 60) . "\n";
