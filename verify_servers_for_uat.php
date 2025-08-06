<?php

echo "=== SERVER STATUS VERIFICATION ===\n";
echo "Checking if both servers are ready for manual testing\n\n";

// Test Backend API
echo "🔍 Testing Backend API (Port 8000):\n";
$backendUrl = 'http://127.0.0.1:8000/api/v1/health';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $backendUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    echo "   ✅ Backend API is RUNNING (HTTP $httpCode)\n";
    echo "   📍 URL: $backendUrl\n";
} else {
    echo "   ❌ Backend API is NOT RUNNING (HTTP $httpCode)\n";
    echo "   🚀 Start with: php artisan serve --port=8000\n";
}

echo "\n🔍 Testing Web Application (Port 8001):\n";
$webUrl = 'http://127.0.0.1:8001/login';  // Test actual login page
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $webUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false); // Don't follow redirects
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200 || $httpCode === 302) {
    echo "   ✅ Web Application is RUNNING (HTTP $httpCode)\n";
    echo "   📍 Login URL: $webUrl\n";
    if ($httpCode === 302) {
        echo "   ℹ️  Redirect detected (normal for authentication)\n";
    }
} else {
    echo "   ❌ Web Application is NOT RUNNING (HTTP $httpCode)\n";
    echo "   🚀 Start with: cmd /c \"cd /d D:\\astacala_rescue_mobile\\astacala_resque-main\\astacala_rescue_web && php -S 127.0.0.1:8001 -t public\"\n";
}

echo "\n📋 TEST CREDENTIALS:\n";
echo "   Username: admin\n";
echo "   Password: password\n";

echo "\n📄 MANUAL TESTING GUIDE:\n";
echo "   Open file: MANUAL_USER_ACCEPTANCE_TESTING_GUIDE.md\n";

echo "\n🎯 READY FOR MANUAL UAT:\n";
if (($httpCode === 200 || $httpCode === 302)) {
    echo "   ✅ System is ready for User Acceptance Testing\n";
    echo "   🌐 Start testing at: http://127.0.0.1:8001/login\n";
} else {
    echo "   ❌ Fix server issues before starting UAT\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
