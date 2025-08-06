<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel for testing
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Debugging Web Application Session Issue ===\n";

// Test 1: Check if authentication works in isolation
echo "🔧 Testing authentication in isolation...\n";

use App\Services\GibranAuthService;
use App\Services\AstacalaApiClient;

$apiClient = new AstacalaApiClient();
$authService = new GibranAuthService($apiClient);

$credentials = [
    'email' => 'volunteer@mobile.test',
    'password' => 'password123'
];

$authResult = $authService->login($credentials);

if ($authResult['success']) {
    echo "✅ Direct authentication successful\n";
    echo "   User ID: " . $authResult['user']['id'] . "\n";
    echo "   User Name: " . $authResult['user']['name'] . "\n";
    echo "   Token stored: " . ($apiClient->isAuthenticated() ? 'Yes' : 'No') . "\n";

    // Test 2: Test session storage
    echo "\n📝 Testing session storage...\n";

    // Start session
    session_start();

    // Store session data like the AuthAdminController does
    session([
        'admin_id' => $authResult['user']['id'],
        'admin_username' => 'admin', // The mapped username
        'admin_name' => $authResult['user']['name'],
        'admin_email' => $authResult['user']['email'],
        'access_token' => $authResult['token']
    ]);

    echo "   Session admin_id: " . session('admin_id') . "\n";
    echo "   Session admin_name: " . session('admin_name') . "\n";
    echo "   Session access_token: " . (session('access_token') ? 'Present' : 'Missing') . "\n";

    // Test 3: Test ReportService with authentication
    echo "\n📊 Testing Report Service with authentication...\n";

    $reportService = new App\Services\GibranReportService($apiClient);

    $reportsResult = $reportService->getAllReports();

    if ($reportsResult['success']) {
        echo "✅ Report service successful\n";
        echo "   Reports count: " . count($reportsResult['data']) . "\n";
    } else {
        echo "❌ Report service failed: " . $reportsResult['message'] . "\n";
    }
} else {
    echo "❌ Direct authentication failed: " . $authResult['message'] . "\n";
}

// Test 4: Check specific error from PelaporanController
echo "\n🔍 Testing PelaporanController directly...\n";

try {
    // Simulate the PelaporanController behavior
    $reportService = new App\Services\GibranReportService($apiClient);
    $authService = new App\Services\GibranAuthService($apiClient);

    $controller = new App\Http\Controllers\PelaporanController($reportService, $authService);

    // Try to call the method that's causing 500 error
    $result = $controller->membacaDataPelaporan();
    echo "✅ PelaporanController method executed successfully\n";
} catch (Exception $e) {
    echo "❌ PelaporanController error: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . "\n";
    echo "   Line: " . $e->getLine() . "\n";
}

echo "\n=== Debug Complete ===\n";
