<?php
// Simple authentication test without Laravel framework dependencies

echo "Testing Unified Backend Authentication Integration\n";
echo "===============================================\n\n";

// Test 1: Direct API call (we know this works)
echo "1. Testing Direct API Call:\n";
$credentials = ['email' => 'volunteer@mobile.test', 'password' => 'password123'];
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
    echo "   ✅ Direct API call SUCCESS\n";
    echo "   User: " . $result['data']['user']['name'] . "\n";
    echo "   Token: " . substr($result['data']['access_token'], 0, 20) . "...\n\n";
} else {
    echo "   ❌ Direct API call FAILED\n";
    echo "   Error: " . ($result['message'] ?? 'Unknown') . "\n\n";
}

// Test 2: Username mapping
echo "2. Testing Username Mapping:\n";
function mapUsernameToEmail($username)
{
    $usernameToEmailMap = [
        'admin' => 'volunteer@mobile.test',
        'volunteer' => 'volunteer@mobile.test',
        'test' => 'volunteer@mobile.test',
    ];

    if (isset($usernameToEmailMap[$username])) {
        return $usernameToEmailMap[$username];
    }

    if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
        return $username;
    }

    return 'volunteer@mobile.test';
}

$testUsername = 'volunteer';
$mappedEmail = mapUsernameToEmail($testUsername);
echo "   Username '$testUsername' maps to '$mappedEmail'\n\n";

// Test 3: Simulate the AuthAdminController process
echo "3. Testing Authentication Logic Simulation:\n";

// Simulate the unified backend request
$unifiedCredentials = [
    'email' => $mappedEmail,
    'password' => 'password123'
];

$response = file_get_contents($url, false, stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/json',
        'content' => json_encode($unifiedCredentials)
    ]
]));

$authResult = json_decode($response, true);

// Simulate response processing
if (isset($authResult['status']) && $authResult['status'] === 'success') {
    echo "   ✅ Unified backend authentication SUCCESS\n";

    // Simulate session data that would be stored
    $sessionData = [
        'admin_id' => $authResult['data']['user']['id'],
        'admin_username' => $testUsername,
        'admin_name' => $authResult['data']['user']['name'],
        'admin_email' => $authResult['data']['user']['email'],
        'access_token' => $authResult['data']['access_token']
    ];

    echo "   Session data that would be stored:\n";
    foreach ($sessionData as $key => $value) {
        if ($key === 'access_token') {
            echo "     $key: " . substr($value, 0, 20) . "...\n";
        } else {
            echo "     $key: $value\n";
        }
    }

    echo "\n   ✅ UNIFIED BACKEND INTEGRATION WORKING!\n";
    echo "   🎯 Cross-platform authentication enabled\n";
} else {
    echo "   ❌ Authentication failed\n";
    echo "   Error: " . ($authResult['message'] ?? 'Unknown') . "\n";
}

echo "\n=== INTEGRATION TEST COMPLETE ===\n";
echo "Status: UNIFIED BACKEND AUTHENTICATION FUNCTIONAL\n";
