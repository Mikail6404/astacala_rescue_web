<?php

// Direct API testing using the correct backend API endpoint
echo "=== TICKET #006 - DATAPENGGUNA CRUD OPERATIONS TESTING ===\n\n";

// Test credentials and configuration (using backend API)
$baseUrl = 'http://127.0.0.1:8000/api/v1';  // Backend API
$loginData = [
    'email' => 'admin@test.com',
    'password' => 'password123'
];

echo "Testing Datapengguna (Volunteer Users) CRUD Operations via Backend API...\n\n";

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
    echo "STEP 1: Authentication (Backend API)\n";
    echo "==================================\n";

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

    // STEP 2: Get Volunteer Users List (equivalent to Datapengguna)
    echo "STEP 2: Get Volunteer Users List (Datapengguna)\n";
    echo "==============================================\n";

    $volunteersResponse = makeApiCall('GET', "$baseUrl/users/volunteer-list", null, $token);

    if ($volunteersResponse['http_code'] === 200) {
        $volunteers = $volunteersResponse['data']['data'] ?? [];
        echo "✅ SUCCESS: Found " . count($volunteers) . " volunteer users\n";

        if (!empty($volunteers)) {
            $firstVolunteer = $volunteers[0];
            echo "Sample volunteer data (for Datapengguna display):\n";
            echo "- ID: " . ($firstVolunteer['id'] ?? 'N/A') . "\n";
            echo "- Name: " . ($firstVolunteer['name'] ?? 'N/A') . "\n";
            echo "- Email: " . ($firstVolunteer['email'] ?? 'N/A') . "\n";
            echo "- Phone: " . ($firstVolunteer['phone'] ?? 'N/A') . "\n";
            echo "- Birth Date: " . ($firstVolunteer['birth_date'] ?? 'N/A') . "\n";
            echo "- Birth Place: " . ($firstVolunteer['place_of_birth'] ?? 'N/A') . "\n";
            echo "- Member Number: " . ($firstVolunteer['member_number'] ?? 'N/A') . "\n";
            echo "- Role: " . ($firstVolunteer['role'] ?? 'N/A') . "\n";
            echo "- Status: " . (($firstVolunteer['is_active'] ?? false) ? 'Active' : 'Inactive') . "\n";

            // Check for TICKET #006 issues (missing fields for edit form)
            $issues = [];
            if (empty($firstVolunteer['birth_date'])) $issues[] = "Missing birth_date field (tanggal_lahir_pengguna)";
            if (empty($firstVolunteer['place_of_birth'])) $issues[] = "Missing place_of_birth field (tempat_lahir_pengguna)";
            if (empty($firstVolunteer['phone'])) $issues[] = "Missing phone field (no_handphone_pengguna)";
            if (empty($firstVolunteer['member_number'])) $issues[] = "Missing member_number field (no_anggota)";

            if (!empty($issues)) {
                echo "\n🔍 ISSUE 6b DETECTED - Missing fields for edit form:\n";
                foreach ($issues as $issue) {
                    echo "   - " . $issue . "\n";
                }
            } else {
                echo "\n✅ All required fields for edit form are populated\n";
            }
        } else {
            echo "⚠️  No volunteer users found\n";
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

        // STEP 3: Get Single Volunteer User (simulates loading edit form)
        echo "STEP 3: Get Single Volunteer User (Edit Form Data)\n";
        echo "===============================================\n";

        $userResponse = makeApiCall('GET', "$baseUrl/users/$testUserId", null, $token);

        if ($userResponse['http_code'] === 200) {
            $user = $userResponse['data']['data'] ?? $userResponse['data'];
            echo "✅ SUCCESS: Retrieved volunteer user for editing\n";
            echo "Edit form fields (mapped to web form):\n";
            echo "- username_akun_pengguna: " . ($user['email'] ?? 'N/A') . "\n";
            echo "- nama_lengkap_pengguna: " . ($user['name'] ?? 'N/A') . "\n";
            echo "- tanggal_lahir_pengguna: " . ($user['birth_date'] ?? 'N/A') . "\n";
            echo "- tempat_lahir_pengguna: " . ($user['place_of_birth'] ?? 'N/A') . "\n";
            echo "- no_handphone_pengguna: " . ($user['phone'] ?? 'N/A') . "\n";
            echo "- password_akun_pengguna: ****\n";

            // ISSUE 6b: Missing data in update form fields
            $missingFields = [];
            if (empty($user['birth_date'])) $missingFields[] = "tanggal_lahir_pengguna (birth_date)";
            if (empty($user['place_of_birth'])) $missingFields[] = "tempat_lahir_pengguna (place_of_birth)";
            if (empty($user['phone'])) $missingFields[] = "no_handphone_pengguna (phone)";
            if (empty($user['member_number'])) $missingFields[] = "no_anggota (member_number)";

            if (!empty($missingFields)) {
                echo "\n❌ ISSUE 6b CONFIRMED: Missing data in edit form fields:\n";
                foreach ($missingFields as $field) {
                    echo "   - " . $field . "\n";
                }
                echo "   Users will see empty fields when editing volunteer data!\n";
            } else {
                echo "\n✅ All edit form fields have data\n";
            }
        } else {
            echo "❌ FAILED: HTTP " . $userResponse['http_code'] . "\n";
            echo "Response: " . $userResponse['body'] . "\n";
        }

        echo "\n";

        // STEP 4: Test Update Function (Issue 6a)
        echo "STEP 4: Test Update Function (Issue 6a)\n";
        echo "=====================================\n";

        $updateData = [
            'name' => ($user['name'] ?? 'Test User') . ' (Updated)',
            'phone' => '+628123456789',
            'birth_date' => '1990-05-15',
            'place_of_birth' => 'Jakarta, Indonesia'
        ];

        // First test: Try updating via profile endpoint (this might be the issue)
        $updateResponse = makeApiCall('PUT', "$baseUrl/users/profile", $updateData, $token);

        if ($updateResponse['http_code'] === 200) {
            echo "✅ SUCCESS: Update function working via profile endpoint\n";
        } else {
            echo "❌ ISSUE 6a CONFIRMED: Update function non-functional via profile endpoint\n";
            echo "HTTP Code: " . $updateResponse['http_code'] . "\n";
            echo "Response: " . $updateResponse['body'] . "\n";

            // Try alternative: Direct user update (admin endpoint)
            echo "\nTrying alternative admin update endpoint...\n";
            $adminUpdateResponse = makeApiCall('PUT', "$baseUrl/users/$testUserId", $updateData, $token);

            if ($adminUpdateResponse['http_code'] === 200) {
                echo "✅ SUCCESS: Update works via admin endpoint\n";
                $updateResponse = $adminUpdateResponse; // Use this for verification
            } else {
                echo "❌ Both update methods failed\n";
                echo "Admin endpoint HTTP Code: " . $adminUpdateResponse['http_code'] . "\n";
                echo "Admin endpoint response: " . $adminUpdateResponse['body'] . "\n";
            }
        }

        echo "\n";

        // STEP 5: Verify Update (if update worked)
        if (isset($updateResponse) && $updateResponse['http_code'] === 200) {
            echo "STEP 5: Verify Update Applied\n";
            echo "===========================\n";

            $verifyResponse = makeApiCall('GET', "$baseUrl/users/$testUserId", null, $token);

            if ($verifyResponse['http_code'] === 200) {
                $updatedUser = $verifyResponse['data']['data'] ?? $verifyResponse['data'];
                echo "Updated user data:\n";
                echo "- Name: " . ($updatedUser['name'] ?? 'N/A') . "\n";
                echo "- Phone: " . ($updatedUser['phone'] ?? 'N/A') . "\n";
                echo "- Birth Date: " . ($updatedUser['birth_date'] ?? 'N/A') . "\n";
                echo "- Birth Place: " . ($updatedUser['place_of_birth'] ?? 'N/A') . "\n";

                // Verify changes
                $changes = [];
                if ($updatedUser['name'] === $updateData['name']) $changes[] = "Name updated ✅";
                else $changes[] = "Name NOT updated ❌";

                if ($updatedUser['phone'] === $updateData['phone']) $changes[] = "Phone updated ✅";
                else $changes[] = "Phone NOT updated ❌";

                echo "\nUpdate verification:\n";
                foreach ($changes as $change) {
                    echo "   - " . $change . "\n";
                }
            }

            echo "\n";
        }

        // STEP 6: Test Delete Function (Issue 6c)
        echo "STEP 6: Test Delete Function (Issue 6c)\n";
        echo "=====================================\n";

        // Test deactivation (proper delete for users)
        $deleteResponse = makeApiCall('PUT', "$baseUrl/users/$testUserId/status", ['is_active' => false], $token);

        if ($deleteResponse['http_code'] === 200) {
            echo "✅ SUCCESS: Delete function working (user deactivated)\n";

            // Verify deactivation
            echo "Verifying deactivation...\n";
            $verifyDeleteResponse = makeApiCall('GET', "$baseUrl/users/$testUserId", null, $token);

            if ($verifyDeleteResponse['http_code'] === 200) {
                $verifiedUser = $verifyDeleteResponse['data']['data'] ?? $verifyDeleteResponse['data'];
                $isActive = $verifiedUser['is_active'] ?? true;

                if (!$isActive) {
                    echo "✅ SUCCESS: User is properly deactivated (is_active: false)\n";
                } else {
                    echo "❌ ISSUE 6c CONFIRMED: Delete shows success but user is still active\n";
                    echo "   This is the 'false success' issue - delete appears to work but doesn't actually deactivate the user\n";
                }
            } else {
                echo "✅ SUCCESS: User no longer accessible (properly deleted/deactivated)\n";
            }
        } else {
            echo "❌ ISSUE 6c CONFIRMED: Delete function not working\n";
            echo "HTTP Code: " . $deleteResponse['http_code'] . "\n";
            echo "Response: " . $deleteResponse['body'] . "\n";
        }
    } else {
        echo "❌ SKIPPING CRUD TESTS: No volunteer users available for testing\n";
    }

    echo "\n=== TICKET #006 SUMMARY ===\n";
    echo "Issues identified for Datapengguna (Volunteer Users) CRUD:\n\n";

    // Issue 6a: Update function
    if (isset($updateResponse)) {
        if ($updateResponse['http_code'] === 200) {
            echo "✅ Issue 6a (Update function): WORKING\n";
        } else {
            echo "❌ Issue 6a (Update function): NOT WORKING - Update function non-functional\n";
        }
    } else {
        echo "❓ Issue 6a (Update function): NOT TESTED - No users available\n";
    }

    // Issue 6b: Missing edit form fields
    if (isset($missingFields)) {
        if (!empty($missingFields)) {
            echo "❌ Issue 6b (Missing form fields): CONFIRMED - " . count($missingFields) . " fields missing data\n";
            echo "   Fields affected: " . implode(', ', $missingFields) . "\n";
        } else {
            echo "✅ Issue 6b (Missing form fields): NOT PRESENT - All fields have data\n";
        }
    } else {
        echo "❓ Issue 6b (Missing form fields): NOT TESTED - No users available\n";
    }

    // Issue 6c: Delete false success
    if (isset($isActive)) {
        if ($isActive) {
            echo "❌ Issue 6c (Delete false success): CONFIRMED - Delete shows success but doesn't deactivate user\n";
        } else {
            echo "✅ Issue 6c (Delete false success): NOT PRESENT - Delete properly deactivates user\n";
        }
    } else {
        echo "❓ Issue 6c (Delete false success): NOT TESTED - No users available or delete failed\n";
    }
} catch (Exception $e) {
    echo "❌ CRITICAL ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== TESTING COMPLETED ===\n";
