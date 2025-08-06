<?php

echo "=== EXECUTING USER MIGRATION - Phase 1 ===\n\n";

try {
    $backendPdo = new PDO('mysql:host=127.0.0.1;dbname=astacala_rescue', 'root', '');
    $webPdo = new PDO('mysql:host=127.0.0.1;dbname=astacalarescue', 'root', '');

    // Enable error reporting
    $backendPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "🔄 Starting migration process...\n\n";

    // 1. Create backup
    echo "1. Creating backup...\n";
    $backendPdo->exec('CREATE TABLE IF NOT EXISTS users_backup_pre_migration AS SELECT * FROM users');
    echo "✅ Backup created\n\n";

    // 2. Create migration log table
    echo "2. Creating migration log table...\n";
    $sql = "CREATE TABLE IF NOT EXISTS user_migration_log (
        id INT AUTO_INCREMENT PRIMARY KEY,
        source_table ENUM('penggunas', 'admins') NOT NULL,
        source_id INT NOT NULL,
        target_user_id BIGINT,
        source_username VARCHAR(255),
        target_email VARCHAR(255),
        migration_status ENUM('success', 'failed', 'duplicate') NOT NULL,
        error_message TEXT,
        migrated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_source (source_table, source_id),
        INDEX idx_target (target_user_id)
    )";
    $backendPdo->exec($sql);
    echo "✅ Migration log table ready\n\n";

    // 3. Migrate penggunas (volunteers)
    echo "3. Migrating penggunas (volunteers)...\n";

    $stmt = $webPdo->prepare('SELECT * FROM penggunas');
    $stmt->execute();
    $penggunas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($penggunas as $pengguna) {
        $targetEmail = $pengguna['username_akun_pengguna'].'@web.local';

        try {
            $insertStmt = $backendPdo->prepare("
                INSERT INTO users (name, email, phone, role, is_active, created_at, updated_at)
                VALUES (?, ?, ?, 'VOLUNTEER', 1, COALESCE(?, NOW()), COALESCE(?, NOW()))
            ");

            $insertStmt->execute([
                $pengguna['nama_lengkap_pengguna'] ?: 'Web User',
                $targetEmail,
                $pengguna['no_handphone_pengguna'] ?: null,
                $pengguna['created_at'],
                $pengguna['updated_at'],
            ]);

            $newUserId = $backendPdo->lastInsertId();

            // Log success
            $logStmt = $backendPdo->prepare("
                INSERT INTO user_migration_log (source_table, source_id, target_user_id, source_username, target_email, migration_status)
                VALUES ('penggunas', ?, ?, ?, ?, 'success')
            ");
            $logStmt->execute([$pengguna['id'], $newUserId, $pengguna['username_akun_pengguna'], $targetEmail]);

            echo "  ✅ Migrated: {$pengguna['nama_lengkap_pengguna']} → $targetEmail (ID: $newUserId)\n";
        } catch (Exception $e) {
            // Log failure
            $logStmt = $backendPdo->prepare("
                INSERT INTO user_migration_log (source_table, source_id, source_username, target_email, migration_status, error_message)
                VALUES ('penggunas', ?, ?, ?, 'failed', ?)
            ");
            $logStmt->execute([$pengguna['id'], $pengguna['username_akun_pengguna'], $targetEmail, $e->getMessage()]);

            echo "  ❌ Failed: {$pengguna['nama_lengkap_pengguna']} → Error: ".$e->getMessage()."\n";
        }
    }

    echo "\n4. Migrating admins...\n";

    $stmt = $webPdo->prepare('SELECT * FROM admins');
    $stmt->execute();
    $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($admins as $admin) {
        $targetEmail = $admin['username_akun_admin'].'@admin.local';

        try {
            $insertStmt = $backendPdo->prepare("
                INSERT INTO users (name, email, phone, role, is_active, created_at, updated_at)
                VALUES (?, ?, ?, 'ADMIN', 1, COALESCE(?, NOW()), COALESCE(?, NOW()))
            ");

            $insertStmt->execute([
                $admin['nama_lengkap_admin'] ?: 'Web Admin',
                $targetEmail,
                $admin['no_handphone_admin'] ?: null,
                $admin['created_at'],
                $admin['updated_at'],
            ]);

            $newUserId = $backendPdo->lastInsertId();

            // Log success
            $logStmt = $backendPdo->prepare("
                INSERT INTO user_migration_log (source_table, source_id, target_user_id, source_username, target_email, migration_status)
                VALUES ('admins', ?, ?, ?, ?, 'success')
            ");
            $logStmt->execute([$admin['id'], $newUserId, $admin['username_akun_admin'], $targetEmail]);

            echo "  ✅ Migrated: {$admin['nama_lengkap_admin']} → $targetEmail (ID: $newUserId)\n";
        } catch (Exception $e) {
            // Log failure
            $logStmt = $backendPdo->prepare("
                INSERT INTO user_migration_log (source_table, source_id, source_username, target_email, migration_status, error_message)
                VALUES ('admins', ?, ?, ?, 'failed', ?)
            ");
            $logStmt->execute([$admin['id'], $admin['username_akun_admin'], $targetEmail, $e->getMessage()]);

            echo "  ❌ Failed: {$admin['nama_lengkap_admin']} → Error: ".$e->getMessage()."\n";
        }
    }

    echo "\n5. Migration Summary:\n";
    echo '='.str_repeat('=', 30)."\n";

    // Get migration statistics
    $stmt = $backendPdo->prepare('
        SELECT source_table, migration_status, COUNT(*) as count 
        FROM user_migration_log 
        GROUP BY source_table, migration_status
    ');
    $stmt->execute();
    $stats = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($stats as $stat) {
        $status_icon = $stat['migration_status'] === 'success' ? '✅' : '❌';
        echo "$status_icon {$stat['source_table']}: {$stat['count']} {$stat['migration_status']}\n";
    }

    // Final user count
    $stmt = $backendPdo->prepare('SELECT COUNT(*) FROM users');
    $stmt->execute();
    $finalCount = $stmt->fetchColumn();
    echo "\n📊 Total users in backend database: $finalCount\n";

    // Show new users by role
    $stmt = $backendPdo->prepare('SELECT role, COUNT(*) as count FROM users GROUP BY role');
    $stmt->execute();
    $roleStats = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "\n👥 Users by role:\n";
    foreach ($roleStats as $role) {
        echo "  {$role['role']}: {$role['count']}\n";
    }

    echo "\n🎉 USER MIGRATION COMPLETED SUCCESSFULLY!\n";
    echo "\nNext steps:\n";
    echo "1. Test cross-platform user visibility\n";
    echo "2. Validate authentication for migrated users\n";
    echo "3. Proceed to Phase 2: Report Migration\n";
} catch (Exception $e) {
    echo '❌ Migration failed: '.$e->getMessage()."\n";
    echo "\nRollback may be required. Check backup tables.\n";
}

echo "\n=== MIGRATION EXECUTION COMPLETE ===\n";
