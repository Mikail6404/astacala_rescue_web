<?php

echo "=== TESTING V1 API AUTHENTICATION ===\n";
echo "Testing with v1 auth endpoints vs gibran endpoints\n\n";

// Test both authentication methods
$loginData = [
    'email' => 'admin@uat.test',
    'password' => 'password123',
];

echo "🔍 STEP 1: Testing V1 API Authentication\n";
echo "========================================\n";

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

echo "V1 Auth HTTP Code: $httpCode\n";

if ($httpCode === 200) {
    $v1AuthData = json_decode($response, true);
    $v1Token = $v1AuthData['data']['access_token'] ?? $v1AuthData['access_token'] ?? null;
    $v1User = $v1AuthData['data']['user'] ?? $v1AuthData['user'] ?? [];

    echo "✅ V1 Auth successful\n";
    echo '  👤 Name: '.($v1User['name'] ?? 'N/A')."\n";
    echo '  🔑 Role: '.($v1User['role'] ?? 'N/A')."\n";
    echo '  🎫 Token: '.($v1Token ? substr($v1Token, 0, 20).'...' : 'Not found')."\n";

    if ($v1Token) {
        echo "\n🔍 Testing admin endpoints with V1 token:\n";

        $endpoints = [
            'Admin List' => '/api/v1/users/admin-list',
            'User Statistics' => '/api/v1/users/statistics',
        ];

        foreach ($endpoints as $name => $endpoint) {
            echo "\n🔍 Testing $name ($endpoint):\n";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000'.$endpoint);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Accept: application/json',
                'Content-Type: application/json',
                'Authorization: Bearer '.$v1Token,
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            echo "  HTTP Code: $httpCode\n";

            if ($httpCode === 200) {
                echo "  ✅ SUCCESS with V1 token\n";
                $data = json_decode($response, true);
                if (isset($data['data']) && is_array($data['data'])) {
                    echo '  📊 Data count: '.count($data['data'])."\n";
                }
            } elseif ($httpCode === 403) {
                echo "  ❌ FORBIDDEN\n";
                $errorData = json_decode($response, true);
                echo '  📝 Message: '.($errorData['message'] ?? 'No message')."\n";
            } else {
                echo "  ❌ ERROR - HTTP $httpCode\n";
                echo '  📝 Response: '.substr($response, 0, 200)."\n";
            }
        }
    }
} else {
    echo "❌ V1 Auth failed\n";
    echo "Response: $response\n";
}

echo "\n🔍 STEP 2: Comparing with Gibran Authentication\n";
echo "===============================================\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/api/gibran/auth/login');
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

echo "Gibran Auth HTTP Code: $httpCode\n";

if ($httpCode === 200) {
    $gibranAuthData = json_decode($response, true);
    $gibranToken = $gibranAuthData['data']['access_token'] ?? null;

    echo "✅ Gibran Auth successful\n";
    echo '  🎫 Token: '.($gibranToken ? substr($gibranToken, 0, 20).'...' : 'Not found')."\n";

    // Compare token formats
    if (isset($v1Token) && isset($gibranToken)) {
        echo "\n📊 Token Comparison:\n";
        echo '  V1 Token:     '.substr($v1Token, 0, 30)."...\n";
        echo '  Gibran Token: '.substr($gibranToken, 0, 30)."...\n";
        echo '  Tokens match: '.($v1Token === $gibranToken ? 'YES' : 'NO')."\n";
    }
} else {
    echo "❌ Gibran Auth failed\n";
}

echo "\n".str_repeat('=', 60)."\n";
echo "AUTHENTICATION COMPARISON COMPLETED\n";
echo str_repeat('=', 60)."\n";
