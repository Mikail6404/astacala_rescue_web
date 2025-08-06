<?php

// Direct API testing without Laravel facades
echo "=== TICKET #006 - DATAPENGGUNA CRUD OPERATIONS TESTING ===\n\n";

// Test credentials and configuration
$baseUrl = 'http://localhost:8001/api/v1';
$loginData = [
    'email' => 'admin@test.com',
    'password' => 'password123'
];

echo "Testing Datapengguna (Volunteer Users) CRUD Operations...\n\n";

try {
    // Initialize cURL for API calls
    function makeApiCall($method, $url, $data = null, $token = null)
    {
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => array_filter([
                'Content-Type: application/json',
                'Accept: application/json',
                $token ? "Authorization: Bearer $token" : null
            ])
        ]);

        if ($data && in_array($method, ['POST', 'PUT', 'PATCH'])) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new Exception("cURL Error: $error");
        }

        return [
            'http_code' => $httpCode,
            'body' => $response,
            'data' => json_decode($response, true)
        ];
    }

    // STEP 1: Authenticate
    echo "STEP 1: Authentication\n";
    echo "===================\n";

    $authResponse = makeApiCall('POST', "$baseUrl/auth/login", $loginData);

    if ($authResponse['http_code'] === 200 && isset($authResponse['data']['token'])) {
        $token = $authResponse['data']['token'];
        echo "✅ Authentication successful\n";
        echo "Token: " . substr($token, 0, 20) . "...\n\n";
    } else {
        echo "❌ Authentication failed\n";
        echo "Response: " . $authResponse['body'] . "\n";
        exit;
    }

    // STEP 2: Get Volunteer Users List  
    echo "STEP 2: Get Volunteer Users List\n";
    echo "==============================\n";

    $volunteersResponse = makeApiCall('GET', "$baseUrl/users/volunteer-list", null, $token);

    if ($volunteersResponse['http_code'] === 200) {
        $volunteers = $volunteersResponse['data']['data'] ?? [];
        echo "✅ SUCCESS: Found " . count($volunteers) . " volunteer users\n";

        if (!empty($volunteers)) {
            $firstVolunteer = $volunteers[0];
            echo "Sample volunteer data:\n";
            echo "- ID: " . ($firstVolunteer['id'] ?? 'N/A') . "\n";
            echo "- Name: " . ($firstVolunteer['name'] ?? 'N/A') . "\n";
            echo "- Email: " . ($firstVolunteer['email'] ?? 'N/A') . "\n";
            echo "- Phone: " . ($firstVolunteer['phone'] ?? 'N/A') . "\n";
            echo "- Birth Date: " . ($firstVolunteer['birth_date'] ?? 'N/A') . "\n";
            echo "- Birth Place: " . ($firstVolunteer['place_of_birth'] ?? 'N/A') . "\n";
            echo "- Member Number: " . ($firstVolunteer['member_number'] ?? 'N/A') . "\n";
            echo "- Role: " . ($firstVolunteer['role'] ?? 'N/A') . "\n";
            echo "- Status: " . (($firstVolunteer['is_active'] ?? false) ? 'Active' : 'Inactive') . "\n";

            // Check for TICKET #006 issues (missing fields in edit form)
            $issues = [];
            if (empty($firstVolunteer['birth_date'])) $issues[] = "Missing birth_date field (Issue 6b)";
            if (empty($firstVolunteer['place_of_birth'])) $issues[] = "Missing place_of_birth field (Issue 6b)";
            if (empty($firstVolunteer['phone'])) $issues[] = "Missing phone field (Issue 6b)";
            if (empty($firstVolunteer['member_number'])) $issues[] = "Missing member_number field (Issue 6b)";

            if (!empty($issues)) {
                echo "\n🔍 DETECTED ISSUES:\n";
                foreach ($issues as $issue) {
                    echo "   - " . $issue . "\n";
                }
            } else {
                echo "\n✅ All required fields are populated\n";
            }
        } else {
            echo "⚠️  No volunteer users found - will create test user for testing\n";
        }
    } else {
        echo "❌ FAILED: HTTP " . $volunteersResponse['http_code'] . "\n";
        echo "Response: " . $volunteersResponse['body'] . "\n";
        exit;
    }

    echo "\n";

    // If we have volunteers, test CRUD operations
    if (!empty($volunteers)) {
        $testUserId = $firstVolunteer['id'];

        // STEP 3: Get Single Volunteer User (for edit form)
        echo "STEP 3: Get Single Volunteer User (Edit Form Data)\n";
        echo "===============================================\n";

        $userResponse = makeApiCall('GET', "$baseUrl/users/$testUserId", null, $token);

        if ($userResponse['http_code'] === 200) {
            $user = $userResponse['data']['data'] ?? $userResponse['data'];
            echo "✅ SUCCESS: Retrieved volunteer user for editing\n";
            echo "Edit form data:\n";
            echo "- ID: " . ($user['id'] ?? 'N/A') . "\n";
            echo "- Name: " . ($user['name'] ?? 'N/A') . "\n";
            echo "- Email: " . ($user['email'] ?? 'N/A') . "\n";
            echo "- Phone: " . ($user['phone'] ?? 'N/A') . "\n";
            echo "- Birth Date: " . ($user['birth_date'] ?? 'N/A') . "\n";
            echo "- Birth Place: " . ($user['place_of_birth'] ?? 'N/A') . "\n";
            echo "- Member Number: " . ($user['member_number'] ?? 'N/A') . "\n";

            // ISSUE 6b: Missing data in update form fields
            $missingFields = [];
            if (empty($user['birth_date'])) $missingFields[] = "birth_date (tanggal_lahir_pengguna)";
            if (empty($user['place_of_birth'])) $missingFields[] = "place_of_birth (tempat_lahir_pengguna)";
            if (empty($user['phone'])) $missingFields[] = "phone (no_handphone_pengguna)";
            if (empty($user['member_number'])) $missingFields[] = "member_number (no_anggota)";

            if (!empty($missingFields)) {
                echo "\n❌ ISSUE 6b CONFIRMED: Missing data in edit form fields:\n";
                foreach ($missingFields as $field) {
                    echo "   - " . $field . "\n";
                }
            } else {
                echo "\n✅ All edit form fields have data\n";
            }
        } else {
            echo "❌ FAILED: HTTP " . $userResponse['http_code'] . "\n";
            echo "Response: " . $userResponse['body'] . "\n";
        }

        echo "\n";

        // STEP 4: Test Update Function
        echo "STEP 4: Test Update Function\n";
        echo "==========================\n";

        $updateData = [
            'name' => ($user['name'] ?? 'Test User') . ' (Updated)',
            'phone' => '+628123456789',
            'birth_date' => '1990-05-15',
            'place_of_birth' => 'Jakarta, Indonesia',
            'member_number' => 'VOL-2025-001'
        ];

        $updateResponse = makeApiCall('PUT', "$baseUrl/users/profile", $updateData, $token);

        if ($updateResponse['http_code'] === 200) {
            echo "✅ SUCCESS: Update function working\n";
            echo "Updated data sent:\n";
            foreach ($updateData as $key => $value) {
                echo "- $key: $value\n";
            }
        } else {
            echo "❌ ISSUE 6a CONFIRMED: Update function non-functional\n";
            echo "HTTP Code: " . $updateResponse['http_code'] . "\n";
            echo "Response: " . $updateResponse['body'] . "\n";
        }

        echo "\n";

        // STEP 5: Test Delete Function
        echo "STEP 5: Test Delete Function\n";
        echo "==========================\n";

        $deleteResponse = makeApiCall('PUT', "$baseUrl/users/$testUserId/status", ['is_active' => false], $token);

        if ($deleteResponse['http_code'] === 200) {
            echo "✅ SUCCESS: Delete function working (user deactivated)\n";

            // Verify deactivation
            $verifyResponse = makeApiCall('GET', "$baseUrl/users/$testUserId", null, $token);
            if ($verifyResponse['http_code'] === 200) {
                $verifiedUser = $verifyResponse['data']['data'] ?? $verifyResponse['data'];
                $isActive = $verifiedUser['is_active'] ?? true;

                if (!$isActive) {
                    echo "✅ Verified: User is properly deactivated\n";
                } else {
                    echo "❌ ISSUE 6c CONFIRMED: Delete shows success but user is still active\n";
                }
            }
        } else {
            echo "❌ ISSUE 6c CONFIRMED: Delete function showing false success or not working\n";
            echo "HTTP Code: " . $deleteResponse['http_code'] . "\n";
            echo "Response: " . $deleteResponse['body'] . "\n";
        }
    } else {
        echo "❌ SKIPPING CRUD TESTS: No volunteer users available for testing\n";
    }

    echo "\n=== SUMMARY ===\n";
    echo "Based on testing, the following issues were identified:\n";
    echo "- Issue 6a: Update function status - " . (isset($updateResponse) && $updateResponse['http_code'] === 200 ? "✅ WORKING" : "❌ NOT WORKING") . "\n";
    echo "- Issue 6b: Missing edit form fields - " . (isset($missingFields) && !empty($missingFields) ? "❌ CONFIRMED" : "✅ OK") . "\n";
    echo "- Issue 6c: Delete function integrity - " . (isset($isActive) && !$isActive ? "✅ WORKING" : "❌ NEEDS FIX") . "\n";
} catch (Exception $e) {
    echo "❌ CRITICAL ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== TESTING COMPLETED ===\n";
