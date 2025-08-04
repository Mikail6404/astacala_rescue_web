<?php
echo "Testing Direct API Communication\n";
echo "==================================\n\n";

// Test credentials
$credentials = [
    'email' => 'volunteer@mobile.test',
    'password' => 'password123'
];

echo "Testing authentication with:\n";
echo "Email: " . $credentials['email'] . "\n";
echo "Password: [hidden]\n\n";

// Make direct API call
$url = 'http://localhost:8000/api/gibran/auth/login';
$data = json_encode($credentials);

$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/json',
        'content' => $data
    ]
]);

$response = file_get_contents($url, false, $context);
$result = json_decode($response, true);

echo "Raw API Response:\n";
echo $response . "\n\n";

echo "Parsed Result:\n";
echo "Status: " . ($result['status'] ?? 'unknown') . "\n";
echo "Message: " . ($result['message'] ?? 'no message') . "\n";

if (isset($result['data']['user'])) {
    $user = $result['data']['user'];
    echo "User ID: " . $user['id'] . "\n";
    echo "User Name: " . $user['name'] . "\n";
    echo "User Email: " . $user['email'] . "\n";
    echo "User Role: " . $user['role'] . "\n";
}

if (isset($result['data']['access_token'])) {
    $token = $result['data']['access_token'];
    echo "Token: " . substr($token, 0, 30) . "...\n";
}

echo "\n=== Test Complete ===\n";
