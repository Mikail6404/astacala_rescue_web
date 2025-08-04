<?php

echo "=== SIMPLE AUTHENTICATION TEST ===\n\n";

// Test direct HTTP call to backend API
echo "1. Testing direct backend API call...\n";

$credentials = [
    'email' => 'volunteer@mobile.test',
    'password' => 'password123'
];

// Use cURL to make the request
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/api/gibran/auth/login');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($credentials));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "HTTP Status: $httpCode\n";
echo "cURL Error: " . ($error ?: 'None') . "\n";
echo "Response: $response\n";

if ($httpCode === 200) {
    $data = json_decode($response, true);
    if (isset($data['status']) && $data['status'] === 'success') {
        echo "✅ Direct backend API call successful!\n";
        echo "User: " . $data['data']['user']['name'] . "\n";
        echo "Token: " . substr($data['data']['access_token'], 0, 20) . "...\n";

        // Now test if we can make a request to the web app with this info
        echo "\n2. Testing username mapping...\n";
        $usernameMapping = [
            'admin' => 'volunteer@mobile.test',
            'volunteer' => 'volunteer@mobile.test'
        ];

        foreach ($usernameMapping as $username => $email) {
            echo "Username '$username' maps to '$email'\n";
        }

        echo "\n✅ Authentication system is working at the API level\n";
        echo "❌ Issue is likely in the web app integration layer\n";
    } else {
        echo "❌ Backend API authentication failed\n";
    }
} else {
    echo "❌ Backend API not reachable\n";
}

echo "\n=== SIMPLE TEST COMPLETE ===\n";
