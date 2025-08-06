<?php

echo "🔍 DIRECT API ENDPOINT ANALYSIS\n";
echo "==============================\n\n";

// First, get authentication token
echo "🔐 Step 1: Getting authentication token...\n";

$loginUrl = 'http://127.0.0.1:8000/api/auth/login';
$loginData = [
    'email' => 'mikailadmin@admin.astacala.local',
    'password' => 'mikailadmin',
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $loginUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($loginData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json',
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$loginResponse = curl_exec($ch);
$loginHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($loginHttpCode === 200) {
    $loginData = json_decode($loginResponse, true);
    echo "✅ Authentication successful\n";

    // Extract token from nested structure
    $token = $loginData['data']['tokens']['accessToken'] ?? null;

    if ($token) {
        echo "✅ Token extracted successfully\n\n";

        // Now test the admin-list endpoint
        echo "📊 Step 2: Testing admin-list endpoint...\n";

        $adminUrl = 'http://127.0.0.1:8000/api/v1/users/admin-list';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $adminUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer '.$token,
            'Accept: application/json',
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200) {
            $data = json_decode($response, true);
            echo "✅ Admin API endpoint working\n";
            echo '📊 Response contains '.count($data['data'])." admin users\n\n";

            // Show first admin user structure
            if (! empty($data['data'])) {
                echo "🎯 FIRST ADMIN USER API RESPONSE:\n";
                echo "==================================\n";
                $firstAdmin = $data['data'][0];

                foreach ($firstAdmin as $key => $value) {
                    echo "   {$key}: ".(is_null($value) ? 'NULL' : $value)."\n";
                }

                echo "\n🔍 VIEW FIELD MAPPING ANALYSIS:\n";
                echo "===============================\n";
                echo "WHAT VIEW LOOKS FOR        → WHAT API PROVIDES\n";
                echo "------------------------------------------------------------\n";
                echo 'date_of_birth              → '.(isset($firstAdmin['birth_date']) ? "birth_date: {$firstAdmin['birth_date']} ✅" : 'NOT FOUND ❌')."\n";
                echo 'place_of_birth             → '.(isset($firstAdmin['birth_place']) ? "birth_place: {$firstAdmin['birth_place']} ✅" : 'NOT FOUND ❌')."\n";
                echo 'phone                      → '.(isset($firstAdmin['phone']) ? "phone: {$firstAdmin['phone']} ✅" : 'NOT FOUND ❌')."\n";
                echo 'member_number              → '.(isset($firstAdmin['organization']) ? "organization: {$firstAdmin['organization']} ✅" : 'NOT FOUND ❌')."\n";

                echo "\n🛠️ REQUIRED VIEW FIXES:\n";
                echo "======================\n";
                echo "1. Change 'date_of_birth' to 'birth_date'\n";
                echo "2. Change 'place_of_birth' to 'birth_place'\n";
                echo "3. 'phone' field is correct\n";
                echo "4. Map 'member_number' to 'organization'\n";
            }
        } else {
            echo "❌ Admin API failed: HTTP {$httpCode}\n";
            echo "Response: {$response}\n";
        }
    } else {
        echo "❌ Token not found in login response\n";
        echo "Login response structure:\n";
        print_r($loginData);
    }
} else {
    echo "❌ Authentication failed: HTTP {$loginHttpCode}\n";
    echo "Response: {$loginResponse}\n";
}
