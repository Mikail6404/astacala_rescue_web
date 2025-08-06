<?php

// Test script to debug TICKET #005 username update issue
echo "=== TICKET #005 Username Update Debug ===\n\n";

// Simulate the data that would be sent from the form
$adminFormData = [
    'username_akun_admin' => 'admin-updated@test.com',
    'nama_lengkap_admin' => 'Test Admin (UPDATED VIA BROWSER TEST 2)',
    'tanggal_lahir_admin' => '1985-01-31',
    'tempat_lahir_admin' => 'Jakarta',
    'no_handphone_admin' => '+628123456789',
    'no_anggota' => 'ADMIN-001',
    'password_akun_admin' => '', // Empty password (should not update)
];

echo "1. Original Form Data:\n";
print_r($adminFormData);

// Current mapping from GibranUserService
$mapping = [
    'nama_lengkap_admin' => 'name',
    'username_akun_admin' => 'email',  // ← THIS IS THE PROBLEM!
    'tanggal_lahir_admin' => 'birth_date',
    'tempat_lahir_admin' => 'place_of_birth',
    'no_handphone_admin' => 'phone',
    'password_akun_admin' => 'password',
];

// Apply mapping logic
$mappedData = [];
foreach ($adminFormData as $key => $value) {
    if (isset($mapping[$key]) && ! empty($value)) {
        $mappedData[$mapping[$key]] = $value;
    }
}

echo "\n2. Mapped Data for API:\n";
print_r($mappedData);

echo "\n3. CRITICAL ISSUE ANALYSIS:\n";
echo "Form field 'username_akun_admin': ".$adminFormData['username_akun_admin']."\n";
echo "Maps to API field 'email': ".($mappedData['email'] ?? 'NOT MAPPED')."\n";
echo "But backend might expect 'username' field instead!\n";

echo "\n4. POSSIBLE SOLUTIONS:\n";
echo "Option A: Map to 'username' instead of 'email'\n";
echo "Option B: Map to both 'email' AND 'username'\n";
echo "Option C: Backend treats username as email (check backend logic)\n";

echo "\n=== Debug Complete ===\n";
