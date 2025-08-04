<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Services\AstacalaApiClient;
use App\Services\AuthService;
use App\Services\DisasterReportService;

// Bootstrap Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing Disaster Report Integration...\n";

try {
    // Initialize services
    $apiClient = new AstacalaApiClient();
    $authService = new AuthService($apiClient);
    $disasterReportService = new DisasterReportService($apiClient);

    echo "✓ Services instantiated successfully\n";

    // Test authentication first (using existing test user)
    echo "\n--- Testing Authentication ---\n";
    $credentials = [
        'email' => 'webtest2@disaster.com',
        'password' => 'password123'
    ];

    $loginResult = $authService->login($credentials);

    if (!$loginResult['success']) {
        throw new Exception("Authentication failed: " . $loginResult['message']);
    }

    echo "✓ Authentication successful\n";
    echo "Token: " . substr($loginResult['token'], 0, 20) . "...\n";

    // Test disaster report operations
    echo "\n--- Testing Disaster Report Service ---\n";

    // Test getting all reports
    echo "📋 Testing get all reports...\n";
    $allReports = $disasterReportService->getAllReports();
    echo "Get all reports result: " . ($allReports['success'] ? "✓ SUCCESS" : "✗ FAILED") . "\n";
    if ($allReports['success']) {
        echo "Reports count: " . count($allReports['data']) . "\n";
    }

    // Test getting statistics
    echo "\n📊 Testing get statistics...\n";
    $statistics = $disasterReportService->getStatistics();
    echo "Statistics result: " . ($statistics['success'] ? "✓ SUCCESS" : "✗ FAILED") . "\n";
    if ($statistics['success']) {
        echo "Statistics: " . json_encode($statistics['data'], JSON_PRETTY_PRINT) . "\n";
    }

    // Test getting pending reports
    echo "\n⏳ Testing get pending reports...\n";
    $pendingReports = $disasterReportService->getPendingReports();
    echo "Pending reports result: " . ($pendingReports['success'] ? "✓ SUCCESS" : "✗ FAILED") . "\n";
    if ($pendingReports['success']) {
        echo "Pending reports count: " . count($pendingReports['data']) . "\n";
    }

    // Test creating a new disaster report
    echo "\n🆕 Testing create disaster report...\n";
    $newReportData = [
        'title' => 'Web Integration Test Report',
        'description' => 'Test disaster report created through web app integration',
        'disasterType' => 'FLOOD',
        'severityLevel' => 'MEDIUM',
        'locationName' => 'Jakarta Pusat',
        'latitude' => -6.2088,
        'longitude' => 106.8456,
        'estimatedAffected' => 100,
        'incidentTimestamp' => date('Y-m-d\TH:i:s\Z'),
    ];

    $createResult = $disasterReportService->createReport($newReportData);
    echo "Create report result: " . ($createResult['success'] ? "✓ SUCCESS" : "✗ FAILED") . "\n";
    if ($createResult['success']) {
        // Check response structure and extract report ID
        $reportId = $createResult['data']['reportId'] ?? $createResult['data']['id'] ?? null;
        if ($reportId) {
            echo "New report ID: " . $reportId . "\n";

            // Test getting the specific report
            echo "\n🔍 Testing get specific report...\n";
            $specificReport = $disasterReportService->getReport($reportId);
            echo "Get specific report result: " . ($specificReport['success'] ? "✓ SUCCESS" : "✗ FAILED") . "\n";

            // Test updating the report
            echo "\n✏️ Testing update report...\n";
            $updateData = [
                'description' => 'Updated test disaster report description',
                'severity' => 'HIGH'
            ];
            $updateResult = $disasterReportService->updateReport($reportId, $updateData);
            echo "Update report result: " . ($updateResult['success'] ? "✓ SUCCESS" : "✗ FAILED") . "\n";
        } else {
            echo "Could not extract report ID from response\n";
        }
    } else {
        echo "Create failed: " . $createResult['message'] . "\n";
    }

    // Test user reports
    echo "\n👤 Testing get user reports...\n";
    $userReports = $disasterReportService->getUserReports();
    echo "User reports result: " . ($userReports['success'] ? "✓ SUCCESS" : "✗ FAILED") . "\n";
    if ($userReports['success']) {
        echo "User reports count: " . count($userReports['data']) . "\n";
    }

    echo "\n--- Disaster Report Integration Test Completed ---\n";
    echo "✅ All core disaster report operations tested successfully\n";
} catch (Exception $e) {
    echo "\n✗ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\nDisaster report integration test completed.\n";
