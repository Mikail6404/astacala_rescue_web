<?php

require_once __DIR__.'/vendor/autoload.php';

use Illuminate\Support\Facades\App;

echo "===============================================\n";
echo "  COMPREHENSIVE TICKET #003 DEBUGGING SCRIPT  \n";
echo "===============================================\n";

// Initialize Laravel app
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "\n1. TESTING PELAPORAN CONTROLLER DATA VARIABLE...\n";

try {
    $reportService = app('App\Services\GibranReportService');
    $result = $reportService->getAllReports();

    echo "GibranReportService::getAllReports() result:\n";
    echo 'Success: '.($result['success'] ? 'YES' : 'NO')."\n";
    echo 'Data count: '.(isset($result['data']) ? count($result['data']) : '0')."\n";
    echo 'Message: '.($result['message'] ?? 'none')."\n";
} catch (Exception $e) {
    echo 'ERROR in GibranReportService: '.$e->getMessage()."\n";
}

echo "\n2. TESTING VIEW AVAILABILITY...\n";

$views_to_check = [
    'data_pelaporan',
    'notifikasi',
    'notifikasi_pelaporan_masuk',
    'detail_pelaporan',
    'detail_notifikasi',
];

foreach ($views_to_check as $view) {
    try {
        $viewPath = view()->getFinder()->find($view);
        echo "✓ View '$view' found at: $viewPath\n";
    } catch (Exception $e) {
        echo "✗ View '$view' NOT FOUND: ".$e->getMessage()."\n";
    }
}

echo "\n3. TESTING ADMIN DELETE VALIDATION...\n";

try {
    $userService = app('App\Services\GibranUserService');
    echo "GibranUserService loaded successfully\n";

    // Test deleteUser method existence
    if (method_exists($userService, 'deleteUser')) {
        echo "✓ deleteUser method exists\n";

        // Get reflection of the method to analyze the code
        $reflection = new ReflectionMethod($userService, 'deleteUser');
        $method_body = file_get_contents($reflection->getFileName());
        $start_line = $reflection->getStartLine();
        $end_line = $reflection->getEndLine();
        $lines = array_slice(explode("\n", $method_body), $start_line - 1, $end_line - $start_line + 1);

        echo "deleteUser method body:\n";
        foreach ($lines as $i => $line) {
            echo ($start_line + $i).': '.$line."\n";
        }
    } else {
        echo "✗ deleteUser method NOT FOUND\n";
    }
} catch (Exception $e) {
    echo 'ERROR in GibranUserService: '.$e->getMessage()."\n";
}

echo "\n4. TESTING API CLIENT CONFIGURATION...\n";

try {
    $apiClient = app('App\Services\AstacalaApiClient');
    echo "AstacalaApiClient loaded successfully\n";

    // Test endpoint generation
    $endpoint = $apiClient->getEndpoint('users', 'update_status', ['id' => 123]);
    echo "Generated endpoint: $endpoint\n";
} catch (Exception $e) {
    echo 'ERROR in AstacalaApiClient: '.$e->getMessage()."\n";
}

echo "\n5. CHECKING ROUTE DEFINITIONS...\n";

$routes = app('router')->getRoutes();
$pelaporan_routes = [];
$admin_routes = [];

foreach ($routes as $route) {
    $uri = $route->uri();
    if (strpos($uri, 'pelaporan') !== false || strpos($uri, 'notifikasi') !== false) {
        $pelaporan_routes[] = $route->methods()[0].' '.$uri.' -> '.$route->getActionName();
    }
    if (strpos($uri, 'admin') !== false || strpos($uri, 'Dataadmin') !== false) {
        $admin_routes[] = $route->methods()[0].' '.$uri.' -> '.$route->getActionName();
    }
}

echo "Pelaporan/Notifikasi routes:\n";
foreach ($pelaporan_routes as $route) {
    echo "  $route\n";
}

echo "\nAdmin routes:\n";
foreach ($admin_routes as $route) {
    echo "  $route\n";
}

echo "\n===============================================\n";
echo "  DEBUGGING SCRIPT COMPLETED  \n";
echo "===============================================\n";
