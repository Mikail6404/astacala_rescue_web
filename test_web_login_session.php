<?php

echo "=== TESTING WEB APP LOGIN WITH SESSION PERSISTENCE ===\n\n";

// Step 1: Get CSRF token from login page
echo "1. Getting CSRF token from login page...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8001/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies.txt'); // Save cookies
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt'); // Use cookies
$loginPage = curl_exec($ch);
curl_close($ch);

// Extract CSRF token
preg_match('/<input type="hidden" name="_token" value="([^"]+)"/', $loginPage, $matches);
$csrfToken = $matches[1] ?? '';

if ($csrfToken) {
    echo "✅ CSRF token obtained: " . substr($csrfToken, 0, 10) . "...\n";
} else {
    echo "❌ Could not extract CSRF token\n";
    exit(1);
}

// Step 2: Submit login form with CSRF token and cookies
echo "\n2. Submitting login form with CSRF and session...\n";
$loginData = [
    '_token' => $csrfToken,
    'username' => 'admin',
    'password' => 'password123'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8001/login');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($loginData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies.txt'); // Save cookies
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt'); // Use cookies
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/x-www-form-urlencoded'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
curl_close($ch);

echo "HTTP Status: $httpCode\n";
echo "Final URL: $finalUrl\n";

if (strpos($finalUrl, 'dashboard') !== false) {
    echo "✅ Login successful! Redirected to dashboard\n";

    // Step 3: Test dashboard access with same session
    echo "\n3. Testing dashboard access with session...\n";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://localhost:8001/dashboard');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt'); // Use saved cookies
    $dashboardResponse = curl_exec($ch);
    $dashboardCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $dashboardUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    curl_close($ch);

    echo "Dashboard HTTP Status: $dashboardCode\n";
    echo "Dashboard Final URL: $dashboardUrl\n";

    if (strpos($dashboardUrl, 'dashboard') !== false && $dashboardCode === 200) {
        echo "✅ Dashboard access successful! Authentication persisting\n";
        echo "📊 Web app authentication is FULLY WORKING!\n";
    } else {
        echo "❌ Dashboard access failed - session not persisting\n";
    }
} else {
    echo "❌ Login failed or redirected elsewhere\n";
    echo "Response preview: " . substr($response, 0, 200) . "...\n";
}

// Clean up
if (file_exists('cookies.txt')) {
    unlink('cookies.txt');
}

echo "\n=== WEB APP LOGIN TEST COMPLETE ===\n";
