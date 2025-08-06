<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel for testing
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\AstacalaApiClient;

echo "=== Dashboard Endpoint Direct Test ===\n";

try {
    $apiClient = new AstacalaApiClient();

    // First login to get token
    $credentials = [
        'email' => 'volunteer@mobile.test',
        'password' => 'password123'
    ];

    $loginEndpoint = $apiClient->getEndpoint('gibran', 'auth_login');
    $loginResponse = $apiClient->publicRequest('POST', $loginEndpoint, $credentials);

    if (isset($loginResponse['status']) && $loginResponse['status'] === 'success') {
        $token = $loginResponse['data']['access_token'];
        $apiClient->storeToken($token);

        echo "✅ Login successful, testing dashboard endpoints...\n\n";

        // Test dashboard statistics endpoint
        echo "🔍 Testing /api/gibran/dashboard/statistics:\n";
        $dashboardEndpoint = $apiClient->getEndpoint('gibran', 'dashboard_statistics');
        echo "   Endpoint URL: $dashboardEndpoint\n";

        $dashboardResponse = $apiClient->authenticatedRequest('GET', $dashboardEndpoint);
        echo "   Raw Response: " . json_encode($dashboardResponse, JSON_PRETTY_PRINT) . "\n\n";

        // Test berita bencana endpoint
        echo "🔍 Testing /api/gibran/berita-bencana:\n";
        $beritaEndpoint = $apiClient->getEndpoint('gibran', 'berita_bencana');
        echo "   Endpoint URL: $beritaEndpoint\n";

        $beritaResponse = $apiClient->authenticatedRequest('GET', $beritaEndpoint);
        echo "   Raw Response: " . json_encode($beritaResponse, JSON_PRETTY_PRINT) . "\n\n";

        // Test other gibran endpoints
        $gibranEndpoints = [
            'pelaporans_list' => '/api/gibran/pelaporans',
            'notifikasi_send' => '/api/gibran/notifikasi/send'
        ];

        foreach ($gibranEndpoints as $name => $endpoint) {
            echo "🔍 Testing $endpoint:\n";
            try {
                $response = $apiClient->authenticatedRequest('GET', $endpoint);
                echo "   Raw Response: " . json_encode($response, JSON_PRETTY_PRINT) . "\n\n";
            } catch (Exception $e) {
                echo "   Error: " . $e->getMessage() . "\n\n";
            }
        }
    } else {
        echo "❌ Login failed: " . json_encode($loginResponse) . "\n";
    }
} catch (Exception $e) {
    echo "❌ Test failed: " . $e->getMessage() . "\n";
}

echo "=== Test Complete ===\n";
