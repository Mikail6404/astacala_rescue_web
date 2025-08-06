<?php

require_once __DIR__.'/vendor/autoload.php';

echo "🧪 COMPREHENSIVE CROSS-PLATFORM DASHBOARD TEST\n";
echo "===============================================\n\n";

// Test 1: Verify backend data population
echo "📊 PHASE 1: Backend Data Verification\n";
echo "------------------------------------\n";

try {
    $backendConfig = [
        'driver' => 'mysql',
        'host' => 'localhost',
        'port' => '3306',
        'database' => 'astacala_rescue',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
    ];

    $backendPdo = new PDO(
        "mysql:host={$backendConfig['host']};port={$backendConfig['port']};dbname={$backendConfig['database']};charset={$backendConfig['charset']}",
        $backendConfig['username'],
        $backendConfig['password']
    );
    $backendPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check users table data completeness
    $stmt = $backendPdo->query('
        SELECT 
            COUNT(*) as total_users,
            COUNT(birth_date) as users_with_birth_date,
            COUNT(phone) as users_with_phone,
            COUNT(organization) as users_with_organization
        FROM users
    ');
    $userStats = $stmt->fetch(PDO::FETCH_ASSOC);

    echo "👥 USERS TABLE ANALYSIS:\n";
    echo "   Total Users: {$userStats['total_users']}\n";
    echo "   Users with Birth Date: {$userStats['users_with_birth_date']}\n";
    echo "   Users with Phone: {$userStats['users_with_phone']}\n";
    echo "   Users with Organization: {$userStats['users_with_organization']}\n";

    // Check role distribution
    $stmt = $backendPdo->query('
        SELECT role, COUNT(*) as count 
        FROM users 
        GROUP BY role
    ');
    $roleStats = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "\n📊 ROLE DISTRIBUTION:\n";
    foreach ($roleStats as $role) {
        echo "   {$role['role']}: {$role['count']} users\n";
    }

    // Check disaster reports data completeness
    $stmt = $backendPdo->query('
        SELECT 
            COUNT(*) as total_reports,
            COUNT(personnel_count) as reports_with_personnel,
            COUNT(contact_phone) as reports_with_contact,
            COUNT(coordinate_string) as reports_with_coordinates
        FROM disaster_reports
    ');
    $reportStats = $stmt->fetch(PDO::FETCH_ASSOC);

    echo "\n🚨 DISASTER REPORTS ANALYSIS:\n";
    echo "   Total Reports: {$reportStats['total_reports']}\n";
    echo "   Reports with Personnel Count: {$reportStats['reports_with_personnel']}\n";
    echo "   Reports with Contact Phone: {$reportStats['reports_with_contact']}\n";
    echo "   Reports with Coordinates: {$reportStats['reports_with_coordinates']}\n";

    // Check publications data
    $stmt = $backendPdo->query('
        SELECT 
            COUNT(*) as total_publications,
            COUNT(author_id) as publications_with_author
        FROM publications
    ');
    $pubStats = $stmt->fetch(PDO::FETCH_ASSOC);

    echo "\n📰 PUBLICATIONS ANALYSIS:\n";
    echo "   Total Publications: {$pubStats['total_publications']}\n";
    echo "   Publications with Author: {$pubStats['publications_with_author']}\n";
} catch (Exception $e) {
    echo '❌ Backend database connection failed: '.$e->getMessage()."\n";
}

echo "\n\n📡 PHASE 2: API Endpoint Testing\n";
echo "-------------------------------\n";

// Test API endpoints
function testApiEndpoint($url, $description)
{
    echo "🔗 Testing: $description\n";
    echo "   URL: $url\n";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        'Content-Type: application/json',
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200) {
        $data = json_decode($response, true);
        if (is_array($data) && isset($data['data'])) {
            $count = count($data['data']);
            echo "   ✅ Success: $count records returned\n";
        } elseif (is_array($data)) {
            $count = count($data);
            echo "   ✅ Success: $count records returned\n";
        } else {
            echo "   ⚠️  Success but unexpected format\n";
        }
    } else {
        echo "   ❌ Failed: HTTP $httpCode\n";
    }
    echo "\n";
}

// Test backend API endpoints
$backendBaseUrl = 'http://127.0.0.1:8000/api/v1';

testApiEndpoint($backendBaseUrl.'/users/admin-list', 'Backend Admin List');
testApiEndpoint($backendBaseUrl.'/users/volunteer-list', 'Backend Volunteer List');
testApiEndpoint($backendBaseUrl.'/disaster-reports', 'Backend Disaster Reports');
testApiEndpoint($backendBaseUrl.'/publications', 'Backend Publications');

echo "\n🌐 PHASE 3: Web App Service Layer Testing\n";
echo "----------------------------------------\n";

// Test web app authentication
echo "🔐 Testing Web App Authentication...\n";

$webLoginUrl = 'http://127.0.0.1:8001/login';
$webApiUrl = 'http://127.0.0.1:8001';

// Try to get web app data pages directly (they should redirect if not authenticated)
function testWebPage($url, $description)
{
    echo "🌐 Testing: $description\n";
    echo "   URL: $url\n";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200) {
        if (strpos($response, 'N/A') !== false) {
            $naCount = substr_count($response, 'N/A');
            echo "   ⚠️  Page loads but contains $naCount N/A values\n";
        } else {
            echo "   ✅ Page loads successfully\n";
        }
    } elseif ($httpCode === 302) {
        echo "   🔒 Redirected (likely authentication required)\n";
    } else {
        echo "   ❌ Failed: HTTP $httpCode\n";
    }
    echo "\n";
}

testWebPage($webApiUrl.'/Dataadmin', 'Web Admin Data Page');
testWebPage($webApiUrl.'/Datapengguna', 'Web User Data Page');
testWebPage($webApiUrl.'/pelaporan', 'Web Disaster Reports Page');
testWebPage($webApiUrl.'/publikasi', 'Web Publications Page');

echo "\n📋 SUMMARY AND RECOMMENDATIONS\n";
echo "=============================\n";

echo "✅ COMPLETED FIXES:\n";
echo "   • Backend volunteer-list endpoint created\n";
echo "   • Sample data populated with realistic values\n";
echo "   • Web service layer updated with role-based methods\n";
echo "   • Controllers updated to use appropriate endpoints\n\n";

echo "🎯 VERIFICATION NEEDED:\n";
echo "   • Login to web app to test actual data display\n";
echo "   • Test CRUD operations (create, update, delete)\n";
echo "   • Verify button functionality\n";
echo "   • Check cross-platform data consistency\n\n";

echo "🔧 NEXT STEPS:\n";
echo "   1. Test web app login with: mikailadmin@admin.astacala.local / mikailadmin\n";
echo "   2. Navigate to each dashboard page to verify data display\n";
echo "   3. Test update, delete, and create operations\n";
echo "   4. Verify role-based data filtering is working\n";
