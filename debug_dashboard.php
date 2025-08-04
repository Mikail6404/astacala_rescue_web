<?php

echo "=== DEBUG WEB APP DASHBOARD ACCESS ===\n\n";

// First login successfully, then test dashboard access
echo "1. Logging in first...\n";

// Get CSRF token
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8001/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies_debug.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies_debug.txt');
$loginPage = curl_exec($ch);
curl_close($ch);

preg_match('/<input type="hidden" name="_token" value="([^"]+)"/', $loginPage, $matches);
$csrfToken = $matches[1] ?? '';

if (!$csrfToken) {
    echo "❌ Could not get CSRF token\n";
    exit(1);
}

// Login
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
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false); // Don't follow redirects
curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies_debug.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies_debug.txt');
curl_setopt($ch, CURLOPT_HEADER, true); // Include headers in output
$loginResponse = curl_exec($ch);
$loginHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Login HTTP Code: $loginHttpCode\n";

// Check if we got a redirect
if ($loginHttpCode === 302) {
    echo "✅ Got redirect from login (normal for successful login)\n";

    // Now test dashboard access with verbose error info
    echo "\n2. Testing dashboard access with detailed error reporting...\n";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://localhost:8001/dashboard');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies_debug.txt');
    curl_setopt($ch, CURLOPT_HEADER, true); // Include headers
    curl_setopt($ch, CURLOPT_VERBOSE, true); // Verbose output
    curl_setopt($ch, CURLOPT_STDERR, fopen('curl_debug.log', 'w')); // Log to file

    $dashboardResponse = curl_exec($ch);
    $dashboardCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $dashboardContentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    curl_close($ch);

    echo "Dashboard HTTP Code: $dashboardCode\n";
    echo "Dashboard Content Type: $dashboardContentType\n";

    if ($dashboardCode === 500) {
        echo "❌ 500 Error on dashboard. Response preview:\n";
        echo substr($dashboardResponse, 0, 1000) . "...\n";

        // Check if response contains Laravel error page
        if (strpos($dashboardResponse, 'Whoops') !== false || strpos($dashboardResponse, 'Something went wrong') !== false) {
            echo "🔍 This appears to be a Laravel error page - likely a PHP/code error\n";
        }
    } else {
        echo "✅ Dashboard accessible! Code: $dashboardCode\n";
    }
} else {
    echo "❌ Login failed. HTTP Code: $loginHttpCode\n";
}

// Clean up
if (file_exists('cookies_debug.txt')) {
    unlink('cookies_debug.txt');
}

echo "\n=== DEBUG COMPLETE ===\n";
