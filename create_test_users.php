<?php

// Create test admin user for testing
require_once __DIR__.'/../astacala_backend/astacala-rescue-api/vendor/autoload.php';

$app = require_once __DIR__.'/../astacala_backend/astacala-rescue-api/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "Creating test admin user for TICKET #006 testing...\n\n";

try {
    // Check if admin already exists
    $existingAdmin = User::where('email', 'admin@test.com')->first();

    if ($existingAdmin) {
        echo "✅ Test admin user already exists\n";
        echo "Email: admin@test.com\n";
        echo "Password: password123\n";
        echo 'Role: '.$existingAdmin->role."\n";
        echo 'Status: '.($existingAdmin->is_active ? 'Active' : 'Inactive')."\n\n";
    } else {
        // Create new admin user
        $admin = User::create([
            'name' => 'Test Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_active' => true,
            'email_verified_at' => now(),
            'phone' => '+628123456789',
            'birth_date' => '1985-01-01',
            'place_of_birth' => 'Jakarta',
            'member_number' => 'ADMIN-001',
            'organization' => 'Astacala Rescue',
        ]);

        echo "✅ Test admin user created successfully\n";
        echo "Email: admin@test.com\n";
        echo "Password: password123\n";
        echo "Role: admin\n";
        echo 'ID: '.$admin->id."\n\n";
    }

    // Also create some test volunteer users for testing
    echo "Checking for test volunteer users...\n";

    $volunteerCount = User::whereIn('role', ['volunteer', 'VOLUNTEER'])->count();
    echo "Found $volunteerCount volunteer users\n";

    if ($volunteerCount < 2) {
        echo "Creating test volunteer users...\n";

        for ($i = 1; $i <= 3; $i++) {
            $volunteerEmail = "volunteer$i@test.com";

            if (! User::where('email', $volunteerEmail)->exists()) {
                $volunteer = User::create([
                    'name' => "Test Volunteer $i",
                    'email' => $volunteerEmail,
                    'password' => Hash::make('password123'),
                    'role' => 'volunteer',
                    'is_active' => true,
                    'email_verified_at' => now(),
                    'phone' => '+62812345678'.$i,
                    'birth_date' => '199'.$i.'-0'.$i.'-15',
                    'place_of_birth' => 'Test City '.$i,
                    'member_number' => 'VOL-00'.$i,
                    'organization' => 'Test Organization',
                ]);

                echo '✅ Created volunteer: '.$volunteer->email."\n";
            }
        }
    }

    echo "\n✅ Test users ready for TICKET #006 testing\n";
} catch (Exception $e) {
    echo '❌ Error creating test users: '.$e->getMessage()."\n";
}
