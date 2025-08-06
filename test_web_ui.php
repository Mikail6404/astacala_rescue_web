<?php

echo "=== Web Application Login Form Test ===\n";

// Test the login page and authentication process
$baseUrl = 'http://127.0.0.1:8001';

// Test 1: Check if web app is accessible
echo "🌐 Testing web application accessibility...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    echo "✅ Web application is accessible (HTTP $httpCode)\n";
    echo "   Page length: " . strlen($response) . " characters\n";
} else {
    echo "❌ Web application not accessible (HTTP $httpCode)\n";
    exit(1);
}

// Test 2: Check login page
echo "\n🔐 Testing login page...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/Login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$loginPageResponse = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    echo "✅ Login page accessible (HTTP $httpCode)\n";
    echo "   Page contains login form: " . (strpos($loginPageResponse, 'username') !== false ? 'Yes' : 'No') . "\n";
    echo "   Page contains password field: " . (strpos($loginPageResponse, 'password') !== false ? 'Yes' : 'No') . "\n";
} else {
    echo "❌ Login page not accessible (HTTP $httpCode)\n";
}

// Test 3: Test login form submission with CSRF protection
echo "\n📝 Testing login form submission...\n";

// First, get the login page to extract CSRF token
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/Login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');
$loginPage = curl_exec($ch);
curl_close($ch);

// Extract CSRF token
preg_match('/<meta name="csrf-token" content="([^"]+)"/', $loginPage, $csrfMatches);
preg_match('/<input[^>]*name="_token"[^>]*value="([^"]+)"/', $loginPage, $tokenMatches);

$csrfToken = $csrfMatches[1] ?? ($tokenMatches[1] ?? null);

if ($csrfToken) {
    echo "✅ CSRF token extracted: " . substr($csrfToken, 0, 20) . "...\n";

    // Attempt login with test credentials
    $loginData = [
        'username' => 'admin',  // Using the username mapping we saw in the code
        'password' => 'password123',
        '_token' => $csrfToken
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $baseUrl . '/login');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($loginData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies.txt');
    curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/x-www-form-urlencoded',
        'X-CSRF-TOKEN: ' . $csrfToken
    ]);

    $loginResponse = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    curl_close($ch);

    echo "   Login attempt result: HTTP $httpCode\n";
    echo "   Final URL: $finalUrl\n";

    // Check if login was successful (redirect to dashboard)
    if (strpos($finalUrl, 'dashboard') !== false || strpos($finalUrl, 'Home') !== false) {
        echo "✅ Login successful - redirected to dashboard!\n";

        // Test dashboard page access
        echo "\n🏠 Testing dashboard access...\n";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $baseUrl . '/Home');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');
        $dashboardResponse = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200) {
            echo "✅ Dashboard accessible after login (HTTP $httpCode)\n";
            echo "   Contains dashboard content: " . (strpos($dashboardResponse, 'dashboard') !== false ? 'Yes' : 'No') . "\n";
        } else {
            echo "❌ Dashboard not accessible after login (HTTP $httpCode)\n";
        }
    } else {
        echo "❌ Login failed - no redirect to dashboard\n";
        echo "   Response preview: " . substr($loginResponse, 0, 200) . "...\n";
    }
} else {
    echo "❌ Could not extract CSRF token from login page\n";
}

// Cleanup
if (file_exists('cookies.txt')) {
    unlink('cookies.txt');
}

echo "\n=== Test Complete ===\n";
