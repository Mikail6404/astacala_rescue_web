<?php

require_once __DIR__.'/../../astacala_backend/astacala-rescue-api/vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

echo "🔧 ADMIN PROFILE DATA POPULATION\n";
echo "===============================\n\n";

// Database configuration for backend
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
    // Get all admin users that need profile data
    $adminUsers = Capsule::table('users')
        ->where('role', 'ADMIN')
        ->whereNull('birth_date')
        ->get();

    echo '📊 Found '.count($adminUsers)." admin users needing profile data\n\n";

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
    ];

    $sampleOrganizations = [
        'PMI Jakarta',
        'SAR Nasional',
        'Basarnas',
        'TNI',
        'Polri',
        'Damkar Jakarta',
        'BPBD DKI',
        'Relawan Nusantara',
        'ACT',
        'Dompet Dhuafa',
    ];

    $updatedCount = 0;

    foreach ($adminUsers as $user) {
        // Generate sample data for admin
        $birthDate = date('Y-m-d', strtotime('-'.rand(25, 50).' years'));
        $birthPlace = $sampleBirthPlaces[array_rand($sampleBirthPlaces)];
        $phone = '08'.rand(10000000, 99999999);
        $organization = $sampleOrganizations[array_rand($sampleOrganizations)];

        Capsule::table('users')
            ->where('id', $user->id)
            ->update([
                'birth_date' => $birthDate,
                'birth_place' => $birthPlace,
                'phone' => $phone,
                'organization' => $organization,
                'updated_at' => now(),
            ]);

        echo "✅ Updated admin: {$user->name} (ID: {$user->id})\n";
        echo "   Birth Date: {$birthDate}\n";
        echo "   Birth Place: {$birthPlace}\n";
        echo "   Phone: {$phone}\n";
        echo "   Organization: {$organization}\n\n";

        $updatedCount++;
    }

    echo "🎉 Successfully updated {$updatedCount} admin users with profile data\n";

    // Verify the updates
    $verifyStats = Capsule::table('users')
        ->where('role', 'ADMIN')
        ->selectRaw('
            COUNT(*) as total_admins,
            COUNT(birth_date) as admins_with_birth_date,
            COUNT(phone) as admins_with_phone,
            COUNT(organization) as admins_with_organization
        ')
        ->first();

    echo "\n📈 VERIFICATION RESULTS:\n";
    echo "   Total Admin Users: {$verifyStats->total_admins}\n";
    echo "   Admins with Birth Date: {$verifyStats->admins_with_birth_date}\n";
    echo "   Admins with Phone: {$verifyStats->admins_with_phone}\n";
    echo "   Admins with Organization: {$verifyStats->admins_with_organization}\n";
} catch (Exception $e) {
    echo '❌ Error: '.$e->getMessage()."\n";
}
