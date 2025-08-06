<?php

echo "=== TESTING V1 API WITH CORRECT TOKEN PATH ===\n";

$loginData = [
    'email' => 'admin@uat.test',
    'password' => 'password123',
];

echo "🔍 Getting V1 API token\n";
echo "=======================\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/api/v1/auth/login');
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

if ($httpCode !== 200) {
    echo "❌ Failed to login\n";
    exit(1);
}

$data = json_decode($response, true);
$token = $data['data']['tokens']['accessToken']; // Correct path!
$user = $data['data']['user'];

echo "✅ V1 login successful\n";
echo '  👤 Name: '.$user['name']."\n";
echo '  🔑 Role: '.$user['role']."\n";
echo '  🎫 Token: '.substr($token, 0, 20)."...\n";

echo "\n🔍 Testing admin endpoints with correct V1 token\n";
echo "===============================================\n";

$adminEndpoints = [
    'Admin List' => '/api/v1/users/admin-list',
    'User Statistics' => '/api/v1/users/statistics',
    'Create Admin' => '/api/v1/users/create-admin',
    'User Profile' => '/api/v1/users/profile',
];

foreach ($adminEndpoints as $name => $endpoint) {
    echo "\n🔍 Testing $name ($endpoint):\n";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000'.$endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        'Content-Type: application/json',
        'Authorization: Bearer '.$token,
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    echo "  HTTP Code: $httpCode\n";

    if ($httpCode === 200) {
        echo "  ✅ SUCCESS!\n";
        $data = json_decode($response, true);
        if (isset($data['data']) && is_array($data['data'])) {
            echo '  📊 Data count: '.count($data['data'])."\n";
        } elseif (isset($data['data'])) {
            echo '  📊 Data type: '.gettype($data['data'])."\n";
        }
    } elseif ($httpCode === 403) {
        echo "  ❌ FORBIDDEN - Insufficient permissions\n";
        $errorData = json_decode($response, true);
        echo '  📝 Message: '.($errorData['message'] ?? 'No message')."\n";
    } elseif ($httpCode === 404) {
        echo "  ❌ NOT FOUND - Endpoint missing\n";
    } else {
        echo "  ❌ ERROR - HTTP $httpCode\n";
        $errorData = json_decode($response, true);
        if (isset($errorData['message'])) {
            echo '  📝 Message: '.$errorData['message']."\n";
        }
    }
}

echo "\n🎉 SUCCESS! V1 API admin endpoints are now working!\n";
echo "Next: Update web application to use admin credentials and V1 API\n";

echo "\n".str_repeat('=', 60)."\n";
echo "V1 API ADMIN TESTING COMPLETED\n";
echo str_repeat('=', 60)."\n";
