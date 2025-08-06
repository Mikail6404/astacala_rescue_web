<?php

// Add the backend API directory to the include path
require_once 'D:\astacala_rescue_mobile\astacala_backend\astacala-rescue-api\vendor\autoload.php';

// Create Laravel application instance for backend
$app = require_once 'D:\astacala_rescue_mobile\astacala_backend\astacala-rescue-api\bootstrap\app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== CHECKING BACKEND USERS ===\n";
echo "Connecting to backend database to check users\n\n";

try {
    // Check if User model exists and get users
    $userModel = 'App\Models\User';

    if (class_exists($userModel)) {
        $users = $userModel::take(10)->get(['id', 'name', 'email']);

        echo "📊 Found " . $users->count() . " users in backend database:\n";
        echo str_repeat("-", 50) . "\n";

        foreach ($users as $user) {
            echo "ID: {$user->id} | Name: {$user->name} | Email: {$user->email}\n";
        }

        // Check specifically for volunteer user
        $volunteerUser = $userModel::where('email', 'volunteer@mobile.test')->first();

        echo "\n🔍 Checking for volunteer@mobile.test user:\n";
        if ($volunteerUser) {
            echo "✅ Found volunteer user:\n";
            echo "  ID: {$volunteerUser->id}\n";
            echo "  Name: {$volunteerUser->name}\n";
            echo "  Email: {$volunteerUser->email}\n";
            echo "  Created: {$volunteerUser->created_at}\n";
        } else {
            echo "❌ volunteer@mobile.test user NOT found\n";
            echo "🔧 This user needs to be created for web authentication to work\n";
        }
    } else {
        echo "❌ User model not found\n";
    }
} catch (Exception $e) {
    echo "❌ Error accessing backend database: " . $e->getMessage() . "\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
