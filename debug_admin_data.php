<?php

require_once __DIR__ . '/../../astacala_backend/astacala-rescue-api/vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

echo "🔍 ADMIN DATA ANALYSIS\n";
echo "====================\n\n";

// Database configuration
$capsule = new Capsule;
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => 'localhost',
    'database' => 'astacala_rescue',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

try {
    // Get sample admin users to see what data they actually have
    $adminUsers = Capsule::table('users')
        ->where('role', 'ADMIN')
        ->orderBy('id')
        ->limit(5)
        ->get();

    echo "📊 SAMPLE ADMIN USERS DATA:\n";
    echo "===========================\n";

    foreach ($adminUsers as $user) {
        echo "👤 User ID: {$user->id}\n";
        echo "   Email: {$user->email}\n";
        echo "   Name: {$user->name}\n";
        echo "   Birth Date: " . ($user->birth_date ?: 'NULL') . "\n";
        echo "   Birth Place: " . ($user->birth_place ?: 'NULL') . "\n";
        echo "   Phone: " . ($user->phone ?: 'NULL') . "\n";
        echo "   Organization: " . ($user->organization ?: 'NULL') . "\n";
        echo "   Role: {$user->role}\n";
        echo "   Created: {$user->created_at}\n";
        echo "   Updated: {$user->updated_at}\n\n";
    }

    // Test the web app's admin endpoint
    echo "🌐 TESTING WEB APP ADMIN API INTEGRATION:\n";
    echo "==========================================\n";

    // Simulate the web app's API call to backend
    $backendUrl = 'http://127.0.0.1:8000/api/v1/users/admin-list';

    echo "🔗 Testing Backend Admin API: {$backendUrl}\n";

    // We need to authenticate first to test the API
    $loginUrl = 'http://127.0.0.1:8000/api/auth/login';
    $loginData = [
        'email' => 'mikailadmin@admin.astacala.local',
        'password' => 'mikailadmin'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $loginUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($loginData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $loginResponse = curl_exec($ch);
    $loginHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($loginHttpCode === 200) {
        $loginData = json_decode($loginResponse, true);
        if (isset($loginData['access_token'])) {
            $token = $loginData['access_token'];
            echo "✅ Authentication successful\n";

            // Now test the admin-list endpoint
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $backendUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $token,
                'Accept: application/json',
                'Content-Type: application/json'
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200) {
                $data = json_decode($response, true);
                echo "✅ Admin API endpoint working\n";
                echo "📊 Response contains " . count($data['data']) . " admin users\n";

                // Show first admin user from API
                if (!empty($data['data'])) {
                    $firstAdmin = $data['data'][0];
                    echo "\n📋 FIRST ADMIN FROM API:\n";
                    foreach ($firstAdmin as $key => $value) {
                        echo "   {$key}: " . ($value ?: 'NULL') . "\n";
                    }
                }
            } else {
                echo "❌ Admin API failed: HTTP {$httpCode}\n";
                echo "Response: {$response}\n";
            }
        } else {
            echo "❌ Login response doesn't contain access_token\n";
            echo "Response: {$loginResponse}\n";
        }
    } else {
        echo "❌ Authentication failed: HTTP {$loginHttpCode}\n";
        echo "Response: {$loginResponse}\n";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
