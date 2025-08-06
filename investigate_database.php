<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== ASTACALA RESCUE WEB DATABASE INVESTIGATION ===\n\n";

try {
    // Test database connection
    echo "1. Testing database connection...\n";
    $connection = DB::connection();
    $pdo = $connection->getPdo();
    echo "✅ Database connection successful!\n";
    echo "   Database name: " . $connection->getDatabaseName() . "\n\n";

    // List all tables
    echo "2. Listing all tables in database...\n";
    $tables = DB::select('SHOW TABLES');
    $tableNames = [];
    foreach ($tables as $table) {
        $tableName = array_values((array) $table)[0];
        $tableNames[] = $tableName;
        echo "   - $tableName\n";
    }
    echo "\n";

    // Check users table specifically
    echo "3. Checking users table structure...\n";
    if (in_array('users', $tableNames)) {
        $users = DB::table('users')->get();
        echo "   Users found: " . $users->count() . "\n";
        if ($users->count() > 0) {
            echo "   Sample user data:\n";
            foreach ($users->take(3) as $user) {
                echo "     - ID: {$user->id}, Email: {$user->email}, Name: " . ($user->name ?? 'N/A') . "\n";
            }
        }

        // Check if mikailadmin user exists
        $mikailadmin = DB::table('users')->where('email', 'mikailadmin')->orWhere('name', 'mikailadmin')->first();
        if ($mikailadmin) {
            echo "   ✅ mikailadmin user found: " . json_encode($mikailadmin) . "\n";
        } else {
            echo "   ❌ mikailadmin user NOT found\n";
        }
    } else {
        echo "   ❌ users table does not exist!\n";
    }
    echo "\n";

    // Check penggunas table (web app specific)
    echo "4. Checking penggunas table...\n";
    if (in_array('penggunas', $tableNames)) {
        $penggunas = DB::table('penggunas')->get();
        echo "   Penggunas found: " . $penggunas->count() . "\n";
        if ($penggunas->count() > 0) {
            echo "   Sample pengguna data:\n";
            foreach ($penggunas->take(3) as $pengguna) {
                echo "     - ID: {$pengguna->id}, Username: " . ($pengguna->username ?? 'N/A') . ", Email: " . ($pengguna->email ?? 'N/A') . "\n";
            }
        }
    } else {
        echo "   ❌ penggunas table does not exist!\n";
    }
    echo "\n";

    // Check admins table
    echo "5. Checking admins table...\n";
    if (in_array('admins', $tableNames)) {
        $admins = DB::table('admins')->get();
        echo "   Admins found: " . $admins->count() . "\n";
        if ($admins->count() > 0) {
            echo "   Sample admin data:\n";
            foreach ($admins->take(3) as $admin) {
                echo "     - ID: {$admin->id}, Username: " . ($admin->username ?? 'N/A') . ", Email: " . ($admin->email ?? 'N/A') . "\n";
            }
        }
    } else {
        echo "   ❌ admins table does not exist!\n";
    }
    echo "\n";

    // Check pelaporan table
    echo "6. Checking pelaporan table...\n";
    if (in_array('pelaporan', $tableNames)) {
        $reports = DB::table('pelaporan')->get();
        echo "   Reports found: " . $reports->count() . "\n";
        if ($reports->count() > 0) {
            echo "   Sample report data:\n";
            foreach ($reports->take(3) as $report) {
                echo "     - ID: {$report->id}, Title: " . ($report->judul ?? 'N/A') . ", Status: " . ($report->status ?? 'N/A') . "\n";
            }
        }
    } else {
        echo "   ❌ pelaporan table does not exist!\n";
    }
    echo "\n";

    // Check migrations table to understand migration status
    echo "7. Checking migrations table...\n";
    if (in_array('migrations', $tableNames)) {
        $migrations = DB::table('migrations')->orderBy('batch', 'desc')->get();
        echo "   Total migrations: " . $migrations->count() . "\n";
        echo "   Last 5 migrations:\n";
        foreach ($migrations->take(5) as $migration) {
            echo "     - {$migration->migration} (Batch: {$migration->batch})\n";
        }
    }
    echo "\n";

    // Mark sessions migration as completed
    echo "8. Fixing sessions migration status...\n";
    $sessionsMigration = DB::table('migrations')->where('migration', '2025_08_04_122010_create_sessions_table')->first();
    if (!$sessionsMigration) {
        DB::table('migrations')->insert([
            'migration' => '2025_08_04_122010_create_sessions_table',
            'batch' => 4
        ]);
        echo "   ✅ Sessions migration marked as completed\n";
    } else {
        echo "   ✅ Sessions migration already marked as completed\n";
    }
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== INVESTIGATION COMPLETE ===\n";
