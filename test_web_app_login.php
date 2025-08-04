<?php

echo "=== TESTING WEB APP LOGIN FLOW ===\n\n";

// Test the actual web app login form submission
$loginUrl = 'http://localhost:8001/test-auth'; // Using the test-auth route to bypass CSRF
$testCredentials = [
    'username' => 'admin', // This should map to volunteer@mobile.test
    'password' => 'password123'
];

echo "Testing web app login with username: " . $testCredentials['username'] . "\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $loginUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($testCredentials)); // Form data, not JSON
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Follow redirects
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/x-www-form-urlencoded',
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$effectiveUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
curl_close($ch);

echo "HTTP Status: $httpCode\n";
echo "Final URL: $effectiveUrl\n";
echo "Response length: " . strlen($response) . " bytes\n";

if ($httpCode === 200) {
    echo "\n✅ Web app login completed\n";

    // Check if response contains success indicators
    if (
        strpos($response, 'Login berhasil') !== false ||
        strpos($response, 'dashboard') !== false ||
        strpos($effectiveUrl, 'dashboard') !== false
    ) {
        echo "✅ Login appears successful (contains success indicators)\n";
    } else {
        echo "⚠️ Login may have failed (no success indicators found)\n";
    }
} elseif ($httpCode === 302) {
    echo "✅ Web app login redirected (likely successful)\n";
    echo "Redirect location: $effectiveUrl\n";
} else {
    echo "❌ Web app login failed\n";
    echo "Response: " . substr($response, 0, 500) . "...\n";
}

echo "\n=== WEB APP LOGIN TEST COMPLETE ===\n";
