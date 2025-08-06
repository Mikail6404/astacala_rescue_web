<?php

echo "=== ANALYZING V1 AUTH RESPONSE STRUCTURE ===\n";

$loginData = [
    'email' => 'admin@uat.test',
    'password' => 'password123'
];

echo "🔍 V1 Authentication Response Analysis\n";
echo "=====================================\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/api/v1/auth/login');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($loginData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Raw Response:\n";
echo $response . "\n\n";

if ($httpCode === 200) {
    $data = json_decode($response, true);
    echo "🔍 Response Structure Analysis:\n";

    function printStructure($array, $prefix = '')
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                echo "  {$prefix}$key: [array]\n";
                printStructure($value, $prefix . '  ');
            } else {
                $displayValue = is_string($value) && strlen($value) > 50 ?
                    substr($value, 0, 30) . '...' : $value;
                echo "  {$prefix}$key: $displayValue\n";
            }
        }
    }

    printStructure($data);

    // Try to extract token from all possible locations
    $possibleTokens = [
        'data.access_token' => $data['data']['access_token'] ?? null,
        'access_token' => $data['access_token'] ?? null,
        'data.token' => $data['data']['token'] ?? null,
        'token' => $data['token'] ?? null,
        'data.api_token' => $data['data']['api_token'] ?? null,
    ];

    echo "\n🔍 Token Extraction Attempts:\n";
    foreach ($possibleTokens as $path => $token) {
        if ($token) {
            echo "  ✅ Found at $path: " . substr($token, 0, 20) . "...\n";

            // Test this token
            echo "    Testing token with admin endpoint...\n";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/api/v1/users/admin-list');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Accept: application/json',
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token
            ]);

            $testResponse = curl_exec($ch);
            $testHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            echo "    HTTP Code: $testHttpCode\n";
            if ($testHttpCode === 200) {
                echo "    ✅ SUCCESS! This token works\n";
                break;
            } else {
                echo "    ❌ Failed\n";
            }
        } else {
            echo "  ❌ Not found at $path\n";
        }
    }
}

echo "\n" . str_repeat("=", 50) . "\n";
