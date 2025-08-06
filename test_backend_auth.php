<?php

echo "=== TESTING BACKEND AUTHENTICATION ===\n";
echo "Checking if admin credentials work with backend API\n\n";

// Test 1: Direct backend API authentication
echo "🔍 STEP 1: Testing Backend API Authentication\n";
echo "==============================================\n";

$loginData = [
    'email' => 'volunteer@mobile.test',
    'password' => 'password'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/api/auth/login');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($loginData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "Backend login attempt:\n";
echo "  Email: {$loginData['email']}\n";
echo "  Password: {$loginData['password']}\n";
echo "  HTTP Code: $httpCode\n";

if ($error) {
    echo "❌ cURL Error: $error\n";
} else {
    echo "Response: $response\n";

    if ($httpCode === 200) {
        $responseData = json_decode($response, true);
        if (isset($responseData['success']) && $responseData['success']) {
            echo "✅ Backend authentication successful!\n";
            echo "  User ID: " . ($responseData['user']['id'] ?? 'N/A') . "\n";
            echo "  User Name: " . ($responseData['user']['name'] ?? 'N/A') . "\n";
            echo "  User Email: " . ($responseData['user']['email'] ?? 'N/A') . "\n";
        } else {
            echo "❌ Backend authentication failed\n";
            echo "  Error: " . ($responseData['error'] ?? 'Unknown error') . "\n";
        }
    } else {
        echo "❌ Backend API request failed (HTTP $httpCode)\n";
    }
}

// Test 2: Check what users exist in backend
echo "\n🔍 STEP 2: Checking Available Users in Backend\n";
echo "==============================================\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/api/gibran/users/list');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json'
]);

$usersResponse = curl_exec($ch);
$usersHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Users endpoint HTTP Code: $usersHttpCode\n";

if ($usersHttpCode === 200) {
    echo "✅ Users endpoint accessible\n";
    $usersData = json_decode($usersResponse, true);
    if (isset($usersData['users']) && is_array($usersData['users'])) {
        echo "📊 Available users:\n";
        foreach ($usersData['users'] as $index => $user) {
            echo "  " . ($index + 1) . ". ID: " . ($user['id'] ?? 'N/A');
            echo " | Email: " . ($user['email'] ?? 'N/A');
            echo " | Name: " . ($user['name'] ?? 'N/A') . "\n";
        }
    } else {
        echo "⚠️  Users data format unexpected\n";
        echo "Response: " . substr($usersResponse, 0, 300) . "...\n";
    }
} else {
    echo "❌ Users endpoint not accessible\n";
    echo "Response: " . substr($usersResponse, 0, 300) . "...\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "BACKEND AUTHENTICATION TEST COMPLETED\n";
echo str_repeat("=", 50) . "\n";
