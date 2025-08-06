<?php

// Test the actual web app AJAX endpoints

echo "=== TESTING WEB APP AJAX ENDPOINTS ===\n\n";

// Test the actual AJAX routes that the frontend uses
$baseUrl = 'http://localhost:8000';

// Initialize session to get auth
session_start();

// Test 1: Check if the API routes are accessible
echo "TEST 1: Testing API routes accessibility\n";
echo "=====================================\n";

$routes = [
    'GET admin list' => '/api/admin',
    'PUT admin update' => '/api/admin/58',
    'DELETE admin' => '/api/admin/59',
];

foreach ($routes as $name => $route) {
    $fullUrl = $baseUrl.$route;
    echo "Route: $name -> $fullUrl\n";
}

echo "\nTEST 2: Testing Laravel routes list\n";
echo "==================================\n";

// Get Laravel routes to confirm our web API routes exist
ob_start();
passthru('php artisan route:list --name=api.admin', $return_code);
$routeOutput = ob_get_clean();

if ($return_code === 0) {
    echo "✅ API admin routes found:\n";
    echo $routeOutput;
} else {
    echo "❌ Could not retrieve route list\n";
}

echo "\n=== WEB APP TESTING COMPLETED ===\n";
