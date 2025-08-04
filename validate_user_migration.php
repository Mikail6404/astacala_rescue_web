<?php

echo "=== DATABASE MIGRATION VALIDATION - USER MIGRATION ===\n\n";

try {
    // Connect to both databases
    $backendPdo = new PDO('mysql:host=127.0.0.1;dbname=astacala_rescue', 'root', '');
    $webPdo = new PDO('mysql:host=127.0.0.1;dbname=astacalarescue', 'root', '');

    echo "1. PRE-MIGRATION STATUS CHECK:\n";
    echo "=" . str_repeat("=", 40) . "\n";

    // Check current user counts
    $stmt = $backendPdo->prepare("SELECT COUNT(*) FROM users");
    $stmt->execute();
    $backendUserCount = $stmt->fetchColumn();
    echo "Backend users (before): $backendUserCount\n";

    $stmt = $webPdo->prepare("SELECT COUNT(*) FROM penggunas");
    $stmt->execute();
    $penggunaCount = $stmt->fetchColumn();
    echo "Web penggunas to migrate: $penggunaCount\n";

    $stmt = $webPdo->prepare("SELECT COUNT(*) FROM admins");
    $stmt->execute();
    $adminCount = $stmt->fetchColumn();
    echo "Web admins to migrate: $adminCount\n";

    echo "\n2. DETAILED SOURCE DATA ANALYSIS:\n";
    echo "=" . str_repeat("=", 40) . "\n";

    // Show penggunas data
    echo "PENGGUNAS DATA:\n";
    $stmt = $webPdo->prepare("SELECT id, nama_lengkap_pengguna, username_akun_pengguna, no_handphone_pengguna, created_at FROM penggunas");
    $stmt->execute();
    $penggunas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($penggunas as $pengguna) {
        echo "  ID: {$pengguna['id']}\n";
        echo "  Name: {$pengguna['nama_lengkap_pengguna']}\n";
        echo "  Username: {$pengguna['username_akun_pengguna']}\n";
        echo "  Phone: {$pengguna['no_handphone_pengguna']}\n";
        echo "  Created: {$pengguna['created_at']}\n";
        echo "  Target Email: {$pengguna['username_akun_pengguna']}@web.local\n";
        echo "  ---\n";
    }

    // Show admins data
    echo "\nADMINS DATA:\n";
    $stmt = $webPdo->prepare("SELECT id, nama_lengkap_admin, username_akun_admin, no_handphone_admin, created_at FROM admins");
    $stmt->execute();
    $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($admins as $admin) {
        echo "  ID: {$admin['id']}\n";
        echo "  Name: {$admin['nama_lengkap_admin']}\n";
        echo "  Username: {$admin['username_akun_admin']}\n";
        echo "  Phone: {$admin['no_handphone_admin']}\n";
        echo "  Created: {$admin['created_at']}\n";
        echo "  Target Email: {$admin['username_akun_admin']}@admin.local\n";
        echo "  ---\n";
    }

    echo "\n3. CONFLICT DETECTION:\n";
    echo "=" . str_repeat("=", 40) . "\n";

    // Check for email conflicts
    $conflicts = 0;
    foreach ($penggunas as $pengguna) {
        $targetEmail = $pengguna['username_akun_pengguna'] . '@web.local';
        $stmt = $backendPdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute([$targetEmail]);
        if ($stmt->fetchColumn() > 0) {
            echo "❌ CONFLICT: Email $targetEmail already exists in backend\n";
            $conflicts++;
        }
    }

    foreach ($admins as $admin) {
        $targetEmail = $admin['username_akun_admin'] . '@admin.local';
        $stmt = $backendPdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute([$targetEmail]);
        if ($stmt->fetchColumn() > 0) {
            echo "❌ CONFLICT: Email $targetEmail already exists in backend\n";
            $conflicts++;
        }
    }

    if ($conflicts === 0) {
        echo "✅ No email conflicts detected - safe to proceed\n";
    } else {
        echo "⚠️ $conflicts conflicts detected - review before migration\n";
    }

    echo "\n4. MIGRATION READINESS ASSESSMENT:\n";
    echo "=" . str_repeat("=", 40) . "\n";

    $totalToMigrate = $penggunaCount + $adminCount;
    echo "Total records to migrate: $totalToMigrate\n";
    echo "Expected backend user count after migration: " . ($backendUserCount + $totalToMigrate - $conflicts) . "\n";

    if ($conflicts === 0 && $totalToMigrate > 0) {
        echo "✅ READY FOR MIGRATION\n";
        echo "\nNext steps:\n";
        echo "1. Run migration_01_users.sql script\n";
        echo "2. Validate migration results\n";
        echo "3. Test cross-platform authentication\n";
        echo "4. Proceed to Phase 2 (Report Migration)\n";
    } else {
        echo "❌ NOT READY - Resolve conflicts first\n";
    }

    echo "\n5. RECOMMENDED MIGRATION SEQUENCE:\n";
    echo "=" . str_repeat("=", 40) . "\n";
    echo "1. Backup both databases\n";
    echo "2. Run migration script in test environment\n";
    echo "3. Validate all migrated data\n";
    echo "4. Test authentication for migrated users\n";
    echo "5. Apply to production during maintenance window\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== VALIDATION COMPLETE ===\n";
