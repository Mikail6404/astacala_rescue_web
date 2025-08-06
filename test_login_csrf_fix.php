<?php

echo "=== TESTING LOGIN FUNCTIONALITY ===\n";
echo "Testing CSRF and login after configuration fixes\n\n";

// Test 1: Check if login page loads and generates CSRF token
echo "🔍 STEP 1: Testing Login Page CSRF Token\n";
echo "=========================================\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8001/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, 'test_cookies.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, 'test_cookies.txt');
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    echo "✅ Login page loads successfully (HTTP $httpCode)\n";

    // Extract CSRF token from response
    if (preg_match('/<input[^>]*name=["\']_token["\'][^>]*value=["\']([^"\']*)["\']/', $response, $matches)) {
        $csrfToken = $matches[1];
        echo "✅ CSRF token found: " . substr($csrfToken, 0, 20) . "...\n";

        // Test 2: Try to submit login form with credentials
        echo "\n🔍 STEP 2: Testing Login Form Submission\n";
        echo "========================================\n";

        $postData = [
            '_token' => $csrfToken,
            'username' => 'admin',
            'password' => 'password'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8001/login');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_COOKIEJAR, 'test_cookies.txt');
        curl_setopt($ch, CURLOPT_COOKIEFILE, 'test_cookies.txt');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded',
            'Referer: http://127.0.0.1:8001/login'
        ]);

        $loginResponse = curl_exec($ch);
        $loginHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $redirectUrl = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
        curl_close($ch);

        echo "Response HTTP Code: $loginHttpCode\n";

        if ($loginHttpCode === 302) {
            echo "✅ Login form submitted successfully (HTTP $loginHttpCode)\n";
            if ($redirectUrl) {
                echo "✅ Redirecting to: $redirectUrl\n";
            }
        } elseif ($loginHttpCode === 419) {
            echo "❌ CSRF token error (HTTP 419 - Page Expired)\n";
            echo "🔧 Check session configuration and CSRF middleware\n";
        } elseif ($loginHttpCode === 422) {
            echo "❌ Validation error (HTTP 422)\n";
            echo "🔧 Check login credentials or form validation\n";
        } else {
            echo "⚠️  Unexpected response (HTTP $loginHttpCode)\n";
            echo "Response preview: " . substr($loginResponse, 0, 200) . "...\n";
        }
    } else {
        echo "❌ CSRF token not found in login page\n";
        echo "🔧 Check if @csrf directive exists in login form\n";
    }
} else {
    echo "❌ Login page failed to load (HTTP $httpCode)\n";
}

// Test 3: Check session storage
echo "\n🔍 STEP 3: Testing Session Storage\n";
echo "==================================\n";

$sessionPath = 'storage/framework/sessions';
if (is_dir($sessionPath)) {
    $sessionFiles = glob($sessionPath . '/*');
    echo "✅ Session directory exists\n";
    echo "📁 Session files count: " . count($sessionFiles) . "\n";
} else {
    echo "❌ Session directory not found: $sessionPath\n";
    echo "🔧 Check SESSION_DRIVER configuration\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "LOGIN TEST COMPLETED\n";
echo str_repeat("=", 50) . "\n";

// Clean up
if (file_exists('test_cookies.txt')) {
    unlink('test_cookies.txt');
}
