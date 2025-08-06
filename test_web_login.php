<?php

echo "=== TESTING WEB APP LOGIN WITH UPDATED AUTHENTICATION ===\n";
echo "Testing login through web application with corrected credentials\n\n";

// Test 1: Get CSRF token from login page
echo "🔍 STEP 1: Getting CSRF token from login page\n";
echo "================================================\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8001/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies.txt');

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "GET /login HTTP Code: $httpCode\n";

// Extract CSRF token
preg_match('/name="_token" value="([^"]+)"/', $response, $matches);
$csrfToken = $matches[1] ?? null;

if ($csrfToken) {
    echo '✅ CSRF token extracted: '.substr($csrfToken, 0, 20)."...\n";
} else {
    echo "❌ Failed to extract CSRF token\n";
    echo 'Response snippet: '.substr($response, -500)."\n";
    exit(1);
}

// Test 2: Submit login form with test credentials
echo "\n🔍 STEP 2: Submitting login form\n";
echo "===================================\n";

$loginData = [
    '_token' => $csrfToken,
    'username' => 'admin',  // This should map to volunteer@mobile.test
    'password' => 'password',  // This should map to password123 via our new mapping
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8001/login');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($loginData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');
curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies.txt');
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false); // Don't follow redirects so we can see them

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "POST /login attempts:\n";
echo "  Username: {$loginData['username']}\n";
echo "  Password: {$loginData['password']}\n";
echo "  HTTP Code: $httpCode\n";

if ($httpCode === 302) {
    // Extract redirect location
    preg_match('/Location: (.+)/i', $response, $matches);
    $redirectLocation = trim($matches[1] ?? 'Not found');

    echo "✅ Form submitted successfully (HTTP 302 redirect)\n";
    echo "  Redirect to: $redirectLocation\n";

    if (strpos($redirectLocation, '/dashboard') !== false) {
        echo "🎉 SUCCESS: Login redirected to dashboard!\n";

        // Test 3: Try to access dashboard with session
        echo "\n🔍 STEP 3: Testing dashboard access with session\n";
        echo "=================================================\n";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8001/dashboard');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');

        $dashboardResponse = curl_exec($ch);
        $dashboardCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        echo "GET /dashboard HTTP Code: $dashboardCode\n";

        if ($dashboardCode === 200) {
            echo "🎉 DASHBOARD ACCESS SUCCESS: Authentication working correctly!\n";
            echo "🔧 UAT environment is now ready for testing\n";
        } else {
            echo "⚠️  Dashboard access issue (HTTP $dashboardCode)\n";
            if (strpos($dashboardResponse, 'Location: ') !== false) {
                preg_match('/Location: (.+)/i', $dashboardResponse, $dashMatches);
                echo '  Redirected to: '.trim($dashMatches[1] ?? 'Not found')."\n";
            }
        }
    } elseif (strpos($redirectLocation, '/login') !== false) {
        echo "❌ FAILED: Redirected back to login page (authentication failed)\n";
        echo "🔍 Check Laravel logs for authentication errors\n";
    } else {
        echo "⚠️  Unexpected redirect location\n";
    }
} elseif ($httpCode === 419) {
    echo "❌ CSRF Token Error (HTTP 419)\n";
} else {
    echo "❌ Unexpected response (HTTP $httpCode)\n";
    echo 'Response snippet: '.substr($response, -500)."\n";
}

echo "\n".str_repeat('=', 60)."\n";
echo "WEB APP LOGIN TEST COMPLETED\n";
echo str_repeat('=', 60)."\n";

// Cleanup
if (file_exists('cookies.txt')) {
    unlink('cookies.txt');
}
