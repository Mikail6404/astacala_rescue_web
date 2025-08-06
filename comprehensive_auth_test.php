<?php

echo "=== COMPREHENSIVE CROSS-PLATFORM AUTHENTICATION TEST ===\n\n";

// Test 1: Backend API Direct Authentication
echo "🔧 TEST 1: Backend API Direct Authentication\n";
$apiResult = testBackendAPI();
echo $apiResult ? "✅ Backend API: WORKING\n" : "❌ Backend API: FAILED\n";

// Test 2: Mobile App Authentication (via backend)
echo "\n📱 TEST 2: Mobile App Authentication\n";
$mobileResult = testMobileAppAuth();
echo $mobileResult ? "✅ Mobile App: WORKING\n" : "❌ Mobile App: FAILED\n";

// Test 3: Web App Authentication
echo "\n🌐 TEST 3: Web App Authentication\n";
$webResult = testWebAppAuth();
echo $webResult ? "✅ Web App: WORKING\n" : "❌ Web App: FAILED\n";

// Summary
echo "\n".str_repeat('=', 50)."\n";
echo "🎯 CROSS-PLATFORM AUTHENTICATION SUMMARY:\n";
echo 'Backend API: '.($apiResult ? '✅ WORKING' : '❌ FAILED')."\n";
echo 'Mobile App:  '.($mobileResult ? '✅ WORKING' : '❌ FAILED')."\n";
echo 'Web App:     '.($webResult ? '✅ WORKING' : '❌ FAILED')."\n";

$totalWorking = ($apiResult ? 1 : 0) + ($mobileResult ? 1 : 0) + ($webResult ? 1 : 0);
$percentage = round(($totalWorking / 3) * 100);

echo "\n🚀 INTEGRATION STATUS: $totalWorking/3 platforms ($percentage% complete)\n";

if ($totalWorking === 3) {
    echo "🎉 ALL PLATFORMS AUTHENTICATED SUCCESSFULLY!\n";
    echo "🌟 Cross-platform integration: COMPLETE\n";
} else {
    echo "⚠️  Cross-platform integration: INCOMPLETE\n";
}

echo str_repeat('=', 50)."\n";

function testBackendAPI()
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/api/auth/login');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        'email' => 'volunteer@mobile.test',
        'password' => 'password123',
    ]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json',
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200) {
        $data = json_decode($response, true);

        return isset($data['success']) && $data['success'] === true &&
            isset($data['data']['tokens']['accessToken']) &&
            ! empty($data['data']['tokens']['accessToken']);
    }

    return false;
}

function testMobileAppAuth()
{
    // Test mobile app by testing the auth endpoint it uses
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/api/auth/login');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        'email' => 'volunteer@mobile.test',
        'password' => 'password123',
    ]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json',
        'User-Agent: AstacalaRescueMobile/1.0',
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200) {
        $data = json_decode($response, true);

        return isset($data['success']) && $data['success'] === true &&
            isset($data['data']['tokens']['accessToken']) &&
            isset($data['data']['user']);
    }

    return false;
}

function testWebAppAuth()
{
    // Get CSRF token
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://localhost:8001/login');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, 'test_cookies.txt');
    curl_setopt($ch, CURLOPT_COOKIEFILE, 'test_cookies.txt');
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $loginPage = curl_exec($ch);
    curl_close($ch);

    if (! $loginPage) {
        return false;
    }

    preg_match('/<input type="hidden" name="_token" value="([^"]+)"/', $loginPage, $matches);
    $csrfToken = $matches[1] ?? '';

    if (! $csrfToken) {
        return false;
    }

    // Test login
    $loginData = [
        '_token' => $csrfToken,
        'username' => 'admin',
        'password' => 'password123',
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://localhost:8001/login');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($loginData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, 'test_cookies.txt');
    curl_setopt($ch, CURLOPT_COOKIEFILE, 'test_cookies.txt');
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    curl_close($ch);

    // Clean up
    if (file_exists('test_cookies.txt')) {
        unlink('test_cookies.txt');
    }

    return $httpCode === 200 && strpos($finalUrl, 'dashboard') !== false;
}
