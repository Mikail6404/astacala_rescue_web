<?php

echo "Testing Backend API Registration...\n";

$data = [
    'name' => 'Test Volunteer Web',
    'email' => 'testvolunteer@web.test',
    'password' => 'password123',
    'password_confirmation' => 'password123',
    'role' => 'VOLUNTEER'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/api/v1/auth/register');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response: $response\n";

// Parse and display the response
$decoded = json_decode($response, true);
if ($decoded) {
    echo "\nParsed Response:\n";
    print_r($decoded);
}
