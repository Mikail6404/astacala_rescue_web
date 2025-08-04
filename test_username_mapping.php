<?php
echo "Testing Username to Email Mapping\n";
echo "==================================\n\n";

// Map test username to email (same logic as controller)
function mapUsernameToEmail($username)
{
    $usernameToEmailMap = [
        'admin' => 'admin@web.test',
        'volunteer' => 'volunteer@mobile.test',
        'test' => 'test@example.com',
    ];

    if (isset($usernameToEmailMap[$username])) {
        return $usernameToEmailMap[$username];
    }

    if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
        return $username;
    }

    return $username . '@web.local';
}

// Test mapping
$testUsernames = ['volunteer', 'admin', 'test@email.com', 'unknown'];

foreach ($testUsernames as $username) {
    $email = mapUsernameToEmail($username);
    echo "Username '$username' maps to email '$email'\n";

    // Test authentication with unified backend
    $credentials = [
        'email' => $email,
        'password' => 'password123'
    ];

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

    if (isset($result['status']) && $result['status'] === 'success') {
        echo "  ✓ Authentication SUCCESS for $email\n";
        echo "  User: " . $result['data']['user']['name'] . " (ID: " . $result['data']['user']['id'] . ")\n";
    } else {
        echo "  ✗ Authentication FAILED for $email\n";
        echo "  Error: " . ($result['message'] ?? 'Unknown error') . "\n";
    }
    echo "\n";
}

echo "=== Test Complete ===\n";
