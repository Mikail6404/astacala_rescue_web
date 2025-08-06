<?php

echo "=== TESTING AUTHENTICATION AND API INTEGRATION ===\n\n";

// Test 1: Login to backend API to get authentication token
echo "1. Testing Backend API Authentication:\n";

$loginData = [
    'email' => 'test-admin@astacala.test',
    'password' => 'testpassword123',
];

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => 'http://127.0.0.1:8000/api/v1/auth/login',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($loginData),
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Accept: application/json',
    ],
]);

$response = curl_exec($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

echo "Login Response Code: $httpCode\n";
echo "Login Response: $response\n\n";

if ($httpCode === 200) {
    $loginResponse = json_decode($response, true);
    if (isset($loginResponse['data']['tokens']['accessToken'])) {
        $token = $loginResponse['data']['tokens']['accessToken'];
        echo '✅ Authentication successful! Token: '.substr($token, 0, 20)."...\n\n";

        // Test 2: Test admin-list endpoint with authentication
        echo "2. Testing Admin List API:\n";

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'http://127.0.0.1:8000/api/v1/users/admin-list',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json',
                'Authorization: Bearer '.$token,
            ],
        ]);

        $adminResponse = curl_exec($curl);
        $adminHttpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        echo "Admin List Response Code: $adminHttpCode\n";
        echo "Admin List Response: $adminResponse\n\n";

        // Test 3: Test getting single user
        if ($adminHttpCode === 200) {
            $adminData = json_decode($adminResponse, true);
            if (! empty($adminData['data'])) {
                $firstAdmin = $adminData['data'][0];
                $userId = $firstAdmin['id'];

                echo "3. Testing Get Single User API (ID: $userId):\n";

                $curl = curl_init();
                curl_setopt_array($curl, [
                    CURLOPT_URL => "http://127.0.0.1:8000/api/v1/users/$userId",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_HTTPHEADER => [
                        'Content-Type: application/json',
                        'Accept: application/json',
                        'Authorization: Bearer '.$token,
                    ],
                ]);

                $userResponse = curl_exec($curl);
                $userHttpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                curl_close($curl);

                echo "User Response Code: $userHttpCode\n";
                echo "User Response: $userResponse\n\n";

                // Test 4: Test profile update (the one used in web app)
                echo "4. Testing Profile Update API:\n";

                $updateData = [
                    'name' => $firstAdmin['name'] ?? 'Test Admin',
                    'email' => $firstAdmin['email'] ?? 'admin@test.com',
                    'birth_date' => $firstAdmin['birth_date'] ?? '1990-01-01',
                    'place_of_birth' => $firstAdmin['place_of_birth'] ?? 'Jakarta',
                    'phone' => $firstAdmin['phone'] ?? '081234567890',
                    'member_number' => $firstAdmin['member_number'] ?? 'AG001',
                ];

                $curl = curl_init();
                curl_setopt_array($curl, [
                    CURLOPT_URL => 'http://127.0.0.1:8000/api/v1/users/profile',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_CUSTOMREQUEST => 'PUT',
                    CURLOPT_POSTFIELDS => json_encode($updateData),
                    CURLOPT_HTTPHEADER => [
                        'Content-Type: application/json',
                        'Accept: application/json',
                        'Authorization: Bearer '.$token,
                    ],
                ]);

                $updateResponse = curl_exec($curl);
                $updateHttpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                curl_close($curl);

                echo "Profile Update Response Code: $updateHttpCode\n";
                echo "Profile Update Response: $updateResponse\n\n";

                // Test 5: Test status update (for admin management)
                echo "5. Testing Status Update API:\n";

                $statusData = [
                    'is_active' => $firstAdmin['is_active'] ?? 1,
                ];

                $curl = curl_init();
                curl_setopt_array($curl, [
                    CURLOPT_URL => "http://127.0.0.1:8000/api/v1/users/$userId/status",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_CUSTOMREQUEST => 'PUT',
                    CURLOPT_POSTFIELDS => json_encode($statusData),
                    CURLOPT_HTTPHEADER => [
                        'Content-Type: application/json',
                        'Accept: application/json',
                        'Authorization: Bearer '.$token,
                    ],
                ]);

                $statusResponse = curl_exec($curl);
                $statusHttpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                curl_close($curl);

                echo "Status Update Response Code: $statusHttpCode\n";
                echo "Status Update Response: $statusResponse\n\n";

                echo "✅ All API tests completed!\n";
                echo "Token for GibranUserService: Bearer $token\n";
            } else {
                echo "❌ No admin users found in response\n";
            }
        } else {
            echo "❌ Admin list request failed\n";
        }
    } else {
        echo "❌ No access token in login response\n";
        print_r($loginResponse);
    }
} else {
    echo "❌ Login failed with code $httpCode\n";
    echo "Response: $response\n";
}
