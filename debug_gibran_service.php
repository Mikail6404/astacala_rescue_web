<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\AstacalaApiClient;
use App\Services\GibranDashboardService;

echo "=== DEBUGGING GIBRAN DASHBOARD SERVICE ===\n\n";

try {
    $apiClient = new AstacalaApiClient();

    // Login
    $loginUrl = $apiClient->getEndpoint('auth', 'login');
    $loginData = ['email' => 'mikailadmin@admin.astacala.local', 'password' => 'mikailadmin'];
    $loginResponse = $apiClient->publicRequest('POST', $loginUrl, $loginData);
    $apiClient->storeToken($loginResponse['data']['tokens']['accessToken'], $loginResponse['data']['user']);

    echo "1. Testing direct API call to gibran endpoint...\n";
    $gibranEndpoint = $apiClient->getEndpoint('gibran', 'dashboard_statistics');
    $directResponse = $apiClient->authenticatedRequest('GET', $gibranEndpoint);
    echo "   Direct response status: " . ($directResponse['status'] ?? 'no status') . "\n";
    echo "   Direct response type: " . gettype($directResponse['status'] ?? null) . "\n";
    echo "   Is 'success'?: " . (($directResponse['status'] ?? null) === 'success' ? 'YES' : 'NO') . "\n";
    echo "   String comparison: " . var_export(($directResponse['status'] ?? null) === 'success', true) . "\n";

    echo "\n2. Testing GibranDashboardService...\n";
    $dashboardService = new GibranDashboardService($apiClient);

    // Let's add some debug to the service
    $stats = $dashboardService->getStatistics();
    echo "   Service result: " . json_encode($stats) . "\n";

    echo "\n3. Manual condition check...\n";
    if (isset($directResponse['status']) && $directResponse['status'] === 'success') {
        echo "   ✅ Condition 1 passed: status === 'success'\n";
    } else {
        echo "   ❌ Condition 1 failed\n";
        echo "       isset(status): " . var_export(isset($directResponse['status']), true) . "\n";
        echo "       status value: " . var_export($directResponse['status'] ?? null, true) . "\n";
        echo "       comparison: " . var_export(($directResponse['status'] ?? null) === 'success', true) . "\n";
    }

    $isSuccess = (isset($directResponse['status']) && $directResponse['status'] === 'success') ||
        (isset($directResponse['success']) && $directResponse['success'] === true);
    echo "   Combined condition result: " . ($isSuccess ? "✅ TRUE" : "❌ FALSE") . "\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== DEBUG COMPLETE ===\n";
