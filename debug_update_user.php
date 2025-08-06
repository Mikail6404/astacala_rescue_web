<?php

require_once __DIR__.'/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Set up the application
$app->boot();

use App\Services\GibranUserService;

try {
    echo "=== Debug Update User Issue ===\n";

    // Initialize the service
    $userService = app(GibranUserService::class);

    // Test getting user ID 55 (the one we tried to update)
    $userId = 55;
    echo "Testing getUser($userId)...\n";

    $response = $userService->getUser($userId);

    echo "Response:\n";
    print_r($response);

    if ($response['success']) {
        echo "\nUser data found:\n";
        print_r($response['data']);
    } else {
        echo "\nError: ".$response['message']."\n";
    }
} catch (Exception $e) {
    echo 'Exception: '.$e->getMessage()."\n";
    echo 'Trace: '.$e->getTraceAsString()."\n";
}
