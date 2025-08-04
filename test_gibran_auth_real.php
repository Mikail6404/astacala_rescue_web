<?php

echo "=== TESTING GIBRAN AUTH WITH REAL CREDENTIALS ===\n\n";

// Test with actual user credentials
$testCredentials = [
    'email' => 'volunteer@mobile.test',
    'password' => 'password123'
];

echo "Testing login for: " . $testCredentials['email'] . "\n";

$loginUrl = 'http://127.0.0.1:8000/api/gibran/auth/login';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $loginUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testCredentials));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status: $httpCode\n";
echo "Response: $response\n";

if ($httpCode === 200) {
    $data = json_decode($response, true);
    if (isset($data['status']) && $data['status'] === 'success') {
        echo "\n✅ Authentication successful!\n";
        echo "User: " . $data['data']['user']['name'] . "\n";
        echo "Email: " . $data['data']['user']['email'] . "\n";
        echo "Role: " . $data['data']['user']['role'] . "\n";
        echo "Token: " . substr($data['data']['access_token'], 0, 20) . "...\n";
    } else {
        echo "\n❌ Authentication failed\n";
    }
} else {
    echo "\n❌ HTTP Error: $httpCode\n";
}

echo "\n=== TEST COMPLETE ===\n";
