<?php

require_once __DIR__ . '/../../astacala_backend/astacala-rescue-api/vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

echo "🏗️ DATABASE SCHEMA ANALYSIS\n";
echo "==========================\n\n";

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
    // Get users table structure
    $columns = Capsule::select("DESCRIBE users");

    echo "📋 USERS TABLE COLUMNS:\n";
    echo "-----------------------\n";
    foreach ($columns as $column) {
        echo "   {$column->Field} ({$column->Type}) - {$column->Null} - Default: {$column->Default}\n";
    }

    // Check if birth_place column exists
    $birthPlaceExists = false;
    foreach ($columns as $column) {
        if ($column->Field === 'birth_place') {
            $birthPlaceExists = true;
            break;
        }
    }

    echo "\n🔍 BIRTH_PLACE COLUMN: " . ($birthPlaceExists ? "EXISTS ✅" : "MISSING ❌") . "\n";

    if (!$birthPlaceExists) {
        echo "\n🛠️ ADDING BIRTH_PLACE COLUMN...\n";
        try {
            Capsule::statement("ALTER TABLE users ADD COLUMN birth_place VARCHAR(255) NULL AFTER birth_date");
            echo "✅ birth_place column added successfully\n";
        } catch (Exception $e) {
            echo "❌ Failed to add birth_place column: " . $e->getMessage() . "\n";
        }
    }

    // Now populate the birth_place data
    echo "\n📝 POPULATING BIRTH_PLACE DATA...\n";

    $sampleBirthPlaces = [
        'Jakarta',
        'Surabaya',
        'Bandung',
        'Medan',
        'Semarang',
        'Makassar',
        'Palembang',
        'Tangerang',
        'Depok',
        'Bekasi',
        'Yogyakarta',
        'Malang',
        'Bogor',
        'Batam',
        'Pekanbaru'
    ];

    $usersWithoutBirthPlace = Capsule::table('users')
        ->whereNull('birth_place')
        ->get();

    $updatedCount = 0;
    foreach ($usersWithoutBirthPlace as $user) {
        $birthPlace = $sampleBirthPlaces[array_rand($sampleBirthPlaces)];

        Capsule::table('users')
            ->where('id', $user->id)
            ->update(['birth_place' => $birthPlace]);

        $updatedCount++;
        if ($updatedCount <= 5) {
            echo "   ✅ {$user->name}: {$birthPlace}\n";
        }
    }

    echo "🎉 Updated {$updatedCount} users with birth_place data\n";

    // Verify the fix
    $stats = Capsule::table('users')
        ->selectRaw('
            COUNT(*) as total_users,
            COUNT(birth_date) as users_with_birth_date,
            COUNT(birth_place) as users_with_birth_place,
            COUNT(phone) as users_with_phone,
            COUNT(organization) as users_with_organization
        ')
        ->first();

    echo "\n📊 FINAL VERIFICATION:\n";
    echo "   Total Users: {$stats->total_users}\n";
    echo "   Users with Birth Date: {$stats->users_with_birth_date}\n";
    echo "   Users with Birth Place: {$stats->users_with_birth_place}\n";
    echo "   Users with Phone: {$stats->users_with_phone}\n";
    echo "   Users with Organization: {$stats->users_with_organization}\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
