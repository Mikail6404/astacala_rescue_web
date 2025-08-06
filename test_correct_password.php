<?php

echo "=== TESTING CORRECT BACKEND CREDENTIALS ===\n";
echo "Testing login with correct password: password123\n\n";

// Test 1: Try backend login with correct password
echo "🔍 STEP 1: Testing Backend Login with password123\n";
echo "==================================================\n";

$loginData = [
    'email' => 'volunteer@mobile.test',
    'password' => 'password123',
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/api/auth/login');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($loginData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json',
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Backend login attempt:\n";
echo "  Email: {$loginData['email']}\n";
echo "  Password: {$loginData['password']}\n";
echo "  HTTP Code: $httpCode\n";
echo "  Response: $response\n";

if ($httpCode === 200) {
    $responseData = json_decode($response, true);
    if (isset($responseData['success']) && $responseData['success']) {
        echo "✅ Backend authentication successful with password123!\n";
        echo "🔧 Need to update web app to use password123 instead of password\n";
    } else {
        echo "❌ Backend authentication failed\n";
    }
} else {
    echo "❌ Backend API request failed (HTTP $httpCode)\n";
}

echo "\n".str_repeat('=', 50)."\n";
echo "CORRECT PASSWORD TEST COMPLETED\n";
echo str_repeat('=', 50)."\n";
