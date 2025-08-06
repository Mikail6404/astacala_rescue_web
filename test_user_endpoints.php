<?php

echo "=== TESTING USER MANAGEMENT ENDPOINTS ===\n";
echo "Diagnosing why admin and user management features are failing\n\n";

// Get the current session token
session_start();
$token = $_SESSION['access_token'] ?? null;

if (!$token) {
    echo "❌ No access token found in session\n";
    exit(1);
}

echo "✅ Access token found: " . substr($token, 0, 20) . "...\n\n";

// Test endpoints that are failing
$endpoints = [
    'User Statistics' => '/api/v1/users/statistics',
    'Admin List' => '/api/v1/users/admin-list',
    'User Profile' => '/api/v1/users/profile',
    'Gibran Dashboard Stats' => '/api/gibran/dashboard/statistics',
    'Gibran Pelaporans' => '/api/gibran/pelaporans',
    'Gibran Berita' => '/api/gibran/berita-bencana'
];

foreach ($endpoints as $name => $endpoint) {
    echo "🔍 Testing $name ($endpoint):\n";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000' . $endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        'Content-Type: application/json',
        'Authorization: Bearer ' . $token
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    echo "  HTTP Code: $httpCode\n";

    if ($httpCode === 200) {
        echo "  ✅ SUCCESS\n";
        $data = json_decode($response, true);
        if (isset($data['data'])) {
            if (is_array($data['data'])) {
                echo "  📊 Data count: " . count($data['data']) . "\n";
            } else {
                echo "  📊 Data type: " . gettype($data['data']) . "\n";
            }
        }
    } elseif ($httpCode === 401) {
        echo "  ❌ UNAUTHORIZED - Token may be invalid\n";
    } elseif ($httpCode === 403) {
        echo "  ❌ FORBIDDEN - User lacks required permissions\n";
    } elseif ($httpCode === 404) {
        echo "  ❌ NOT FOUND - Endpoint doesn't exist\n";
    } else {
        echo "  ❌ ERROR - HTTP $httpCode\n";
        $errorData = json_decode($response, true);
        if (isset($errorData['message'])) {
            echo "  📝 Message: " . $errorData['message'] . "\n";
        }
    }
    echo "\n";
}

// Test current user info
echo "🔍 Testing current user info (/api/v1/auth/me):\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/api/v1/auth/me');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Content-Type: application/json',
    'Authorization: Bearer ' . $token
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "  HTTP Code: $httpCode\n";
if ($httpCode === 200) {
    $userData = json_decode($response, true);
    echo "  ✅ User Info Retrieved:\n";
    echo "  📧 Email: " . ($userData['data']['email'] ?? 'N/A') . "\n";
    echo "  👤 Name: " . ($userData['data']['name'] ?? 'N/A') . "\n";
    echo "  🔑 Role: " . ($userData['data']['role'] ?? 'N/A') . "\n";
    echo "  🆔 ID: " . ($userData['data']['id'] ?? 'N/A') . "\n";
} else {
    echo "  ❌ Failed to get user info\n";
    echo "  Response: $response\n";
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "USER MANAGEMENT ENDPOINT TESTING COMPLETED\n";
echo str_repeat("=", 60) . "\n";
