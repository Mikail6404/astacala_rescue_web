<?php

echo "=== CREATING BACKEND USER FOR WEB AUTH ===\n";
echo "Creating volunteer@mobile.test user for web authentication\n\n";

// Test 1: Try to register the user via API
echo "🔍 STEP 1: Creating User via Backend API\n";
echo "=========================================\n";

$registerData = [
    'name' => 'Admin User',
    'email' => 'volunteer@mobile.test',
    'password' => 'password',
    'password_confirmation' => 'password'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/api/auth/register');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($registerData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Registration attempt:\n";
echo "  Name: {$registerData['name']}\n";
echo "  Email: {$registerData['email']}\n";
echo "  Password: {$registerData['password']}\n";
echo "  HTTP Code: $httpCode\n";
echo "  Response: $response\n";

if ($httpCode === 201 || $httpCode === 200) {
    echo "✅ User created successfully!\n";
} elseif ($httpCode === 422) {
    $responseData = json_decode($response, true);
    if (isset($responseData['message']) && strpos($responseData['message'], 'already been taken') !== false) {
        echo "ℹ️  User already exists - this is good!\n";
    } else {
        echo "❌ Validation error during user creation\n";
    }
} else {
    echo "❌ Failed to create user\n";
}

// Test 2: Now try to login with the user
echo "\n🔍 STEP 2: Testing Login with Created User\n";
echo "==========================================\n";

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

$loginResponse = curl_exec($ch);
$loginHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Login attempt:\n";
echo "  Email: {$loginData['email']}\n";
echo "  Password: {$loginData['password']}\n";
echo "  HTTP Code: $loginHttpCode\n";
echo "  Response: $loginResponse\n";

if ($loginHttpCode === 200) {
    $loginResponseData = json_decode($loginResponse, true);
    if (isset($loginResponseData['success']) && $loginResponseData['success']) {
        echo "✅ Backend authentication now working!\n";
        echo "🎉 Web login should now work with admin/password credentials\n";
    } else {
        echo "❌ Login still failing\n";
    }
} else {
    echo "❌ Login failed (HTTP $loginHttpCode)\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "USER CREATION TEST COMPLETED\n";
echo str_repeat("=", 50) . "\n";
