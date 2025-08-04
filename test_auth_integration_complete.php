<?php

echo "=== WEB APP AUTHENTICATION INTEGRATION TEST ===\n\n";

// Include Laravel autoloader
require_once __DIR__ . '/vendor/autoload.php';

// Test 1: Check if web app can reach backend API
echo "1. Testing backend API connectivity...\n";
try {
    $baseUrl = 'http://127.0.0.1:8000/api';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $baseUrl . '/health');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200) {
        echo "✅ Backend API is reachable (HTTP $httpCode)\n";
        $data = json_decode($response, true);
        if (isset($data['status']) && $data['status'] === 'OK') {
            echo "✅ Backend API health check passed\n";
        } else {
            echo "⚠️ Backend API returned unexpected response\n";
        }
    } else {
        echo "❌ Backend API not reachable (HTTP $httpCode)\n";
        echo "   Response: $response\n";
    }
} catch (Exception $e) {
    echo "❌ Backend API connection failed: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 2: Test Gibran authentication endpoint
echo "2. Testing Gibran authentication endpoint...\n";
try {
    $loginUrl = 'http://127.0.0.1:8000/api/gibran/auth/login';
    $testCredentials = [
        'email' => 'volunteer@mobile.test',
        'password' => 'wrongpassword' // Intentionally wrong to test endpoint exists
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $loginUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testCredentials));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json'
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 401) {
        echo "✅ Gibran auth endpoint exists and rejects invalid credentials (HTTP $httpCode)\n";
        $data = json_decode($response, true);
        if (isset($data['status']) && $data['status'] === 'error') {
            echo "✅ Gibran auth endpoint returns proper error format\n";
        }
    } elseif ($httpCode === 200) {
        echo "⚠️ Gibran auth endpoint accepted wrong credentials (security issue)\n";
    } elseif ($httpCode === 404) {
        echo "❌ Gibran auth endpoint not found (HTTP $httpCode)\n";
    } else {
        echo "⚠️ Gibran auth endpoint returned unexpected status (HTTP $httpCode)\n";
        echo "   Response: $response\n";
    }
} catch (Exception $e) {
    echo "❌ Gibran auth test failed: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 3: Check if models exist
echo "3. Testing model availability...\n";
try {
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

    if (class_exists('App\Models\Admin')) {
        echo "✅ Admin model exists\n";
    } else {
        echo "❌ Admin model missing\n";
    }

    if (class_exists('App\Models\Pengguna')) {
        echo "✅ Pengguna model exists\n";
    } else {
        echo "❌ Pengguna model missing\n";
    }

    if (class_exists('App\Services\GibranAuthService')) {
        echo "✅ GibranAuthService exists\n";
    } else {
        echo "❌ GibranAuthService missing\n";
    }
} catch (Exception $e) {
    echo "❌ Model test failed: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 4: Check database configuration
echo "4. Testing database configuration...\n";
try {
    $env = file_get_contents(__DIR__ . '/.env');
    if (strpos($env, 'DB_DATABASE=astacala_rescue') !== false) {
        echo "✅ Web app configured to use unified database\n";
    } else {
        echo "❌ Web app not configured for unified database\n";
    }

    if (strpos($env, 'DB_HOST=127.0.0.1') !== false) {
        echo "✅ Database host configured correctly\n";
    } else {
        echo "❌ Database host configuration issue\n";
    }
} catch (Exception $e) {
    echo "❌ Database config test failed: " . $e->getMessage() . "\n";
}

echo "\n=== AUTHENTICATION INTEGRATION TEST COMPLETE ===\n";
