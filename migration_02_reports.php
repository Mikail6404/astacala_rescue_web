<?php

echo "=== EXECUTING DISASTER REPORTS MIGRATION - Phase 2 ===\n\n";

try {
    $backendPdo = new PDO('mysql:host=127.0.0.1;dbname=astacala_rescue', 'root', '');
    $webPdo = new PDO('mysql:host=127.0.0.1;dbname=astacalarescue', 'root', '');

    // Enable error reporting
    $backendPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "🔄 Starting disaster reports migration...\n\n";

    // 1. Create backup
    echo "1. Creating backup...\n";
    $backendPdo->exec("CREATE TABLE IF NOT EXISTS disaster_reports_backup_pre_migration AS SELECT * FROM disaster_reports");
    echo "✅ Backup created\n\n";

    // 2. Create migration log table for reports
    echo "2. Creating report migration log table...\n";
    $sql = "CREATE TABLE IF NOT EXISTS report_migration_log (
        id INT AUTO_INCREMENT PRIMARY KEY,
        source_table ENUM('pelaporans') NOT NULL,
        source_id INT NOT NULL,
        target_report_id BIGINT,
        source_title VARCHAR(500),
        target_title VARCHAR(500),
        source_reporter_id INT,
        target_reporter_id BIGINT,
        migration_status ENUM('success', 'failed', 'duplicate') NOT NULL,
        error_message TEXT,
        migrated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_source (source_table, source_id),
        INDEX idx_target (target_report_id)
    )";
    $backendPdo->exec($sql);
    echo "✅ Report migration log table ready\n\n";

    // 3. Get user ID mappings
    echo "3. Loading user ID mappings...\n";
    $stmt = $backendPdo->prepare("
        SELECT source_id, target_user_id 
        FROM user_migration_log 
        WHERE source_table = 'penggunas' AND migration_status = 'success'
    ");
    $stmt->execute();
    $userMappings = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $userMappings[$row['source_id']] = $row['target_user_id'];
    }
    echo "✅ User mappings loaded: " . count($userMappings) . " mappings\n\n";

    // 4. Migrate pelaporans to disaster_reports
    echo "4. Migrating pelaporans (disaster reports)...\n";

    $stmt = $webPdo->prepare("SELECT * FROM pelaporans ORDER BY id");
    $stmt->execute();
    $pelaporans = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($pelaporans as $pelaporan) {
        try {
            // Map severity level
            $severityLevel = match (strtolower($pelaporan['skala_bencana'])) {
                'kecil' => 'MINOR',
                'sedang' => 'MODERATE',
                'besar' => 'MAJOR',
                default => 'MINOR'
            };

            // Map status
            $status = match ($pelaporan['status_verifikasi']) {
                'PENDING' => 'PENDING',
                'DITERIMA' => 'VERIFIED',
                'DITOLAK' => 'REJECTED',
                default => 'PENDING'
            };

            // Map reporter ID (use mapped user ID or set to NULL if not found)
            $reportedBy = null;
            if ($pelaporan['pelapor_pengguna_id'] && isset($userMappings[$pelaporan['pelapor_pengguna_id']])) {
                $reportedBy = $userMappings[$pelaporan['pelapor_pengguna_id']];
            }

            // Create comprehensive description
            $description = $pelaporan['informasi_singkat_bencana'];
            if ($pelaporan['deskripsi_terkait_data_lainya']) {
                $description .= ' - ' . $pelaporan['deskripsi_terkait_data_lainya'];
            }

            $insertStmt = $backendPdo->prepare("
                INSERT INTO disaster_reports (
                    title, description, disaster_type, severity_level, status,
                    location_name, team_name, estimated_affected, 
                    personnel_count, casualty_count, reported_by, 
                    created_at, updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $insertStmt->execute([
                $pelaporan['informasi_singkat_bencana'],                    // title
                $description,                                               // description
                'GENERAL',                                                  // disaster_type (default)
                $severityLevel,                                            // severity_level
                $status,                                                   // status
                $pelaporan['lokasi_bencana'],                              // location_name
                $pelaporan['nama_team_pelapor'],                           // team_name
                $pelaporan['jumlah_korban'] ?: 0,                          // estimated_affected
                $pelaporan['jumlah_personel'] ?: 0,                        // personnel_count
                $pelaporan['jumlah_korban'] ?: 0,                          // casualty_count
                $reportedBy,                                               // reported_by
                $pelaporan['created_at'] ?: 'NOW()',                       // created_at
                $pelaporan['updated_at'] ?: 'NOW()'                        // updated_at
            ]);

            $newReportId = $backendPdo->lastInsertId();

            // Log success
            $logStmt = $backendPdo->prepare("
                INSERT INTO report_migration_log (
                    source_table, source_id, target_report_id, source_title, target_title,
                    source_reporter_id, target_reporter_id, migration_status
                ) VALUES ('pelaporans', ?, ?, ?, ?, ?, ?, 'success')
            ");
            $logStmt->execute([
                $pelaporan['id'],
                $newReportId,
                $pelaporan['informasi_singkat_bencana'],
                $pelaporan['informasi_singkat_bencana'],
                $pelaporan['pelapor_pengguna_id'],
                $reportedBy
            ]);

            $reporterInfo = $reportedBy ? "(Reporter: $reportedBy)" : "(Reporter: NULL)";
            echo "  ✅ Migrated: {$pelaporan['informasi_singkat_bencana']} → ID: $newReportId $reporterInfo\n";
            echo "     Location: {$pelaporan['lokasi_bencana']}\n";
            echo "     Severity: {$pelaporan['skala_bencana']} → $severityLevel\n";
            echo "     Status: {$pelaporan['status_verifikasi']} → $status\n";
            echo "     Team: {$pelaporan['nama_team_pelapor']}\n\n";
        } catch (Exception $e) {
            // Log failure
            $logStmt = $backendPdo->prepare("
                INSERT INTO report_migration_log (
                    source_table, source_id, source_title, source_reporter_id, migration_status, error_message
                ) VALUES ('pelaporans', ?, ?, ?, 'failed', ?)
            ");
            $logStmt->execute([
                $pelaporan['id'],
                $pelaporan['informasi_singkat_bencana'],
                $pelaporan['pelapor_pengguna_id'],
                $e->getMessage()
            ]);

            echo "  ❌ Failed: {$pelaporan['informasi_singkat_bencana']} → Error: " . $e->getMessage() . "\n\n";
        }
    }

    echo "\n5. Migration Summary:\n";
    echo "=" . str_repeat("=", 18) . "\n";

    // Get migration statistics
    $stmt = $backendPdo->prepare("
        SELECT migration_status, COUNT(*) as count 
        FROM report_migration_log 
        GROUP BY migration_status
    ");
    $stmt->execute();
    $stats = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($stats as $stat) {
        $status_icon = $stat['migration_status'] === 'success' ? '✅' : '❌';
        echo "$status_icon pelaporans: {$stat['count']} {$stat['migration_status']}\n";
    }

    // Final report count
    $stmt = $backendPdo->prepare("SELECT COUNT(*) FROM disaster_reports");
    $stmt->execute();
    $finalCount = $stmt->fetchColumn();
    echo "\n📊 Total disaster reports in backend database: $finalCount\n";

    // Show reports by status
    $stmt = $backendPdo->prepare("SELECT status, COUNT(*) as count FROM disaster_reports GROUP BY status");
    $stmt->execute();
    $statusStats = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "\n📋 Reports by status:\n";
    foreach ($statusStats as $status) {
        echo "  {$status['status']}: {$status['count']}\n";
    }

    // Show reports by severity
    $stmt = $backendPdo->prepare("SELECT severity_level, COUNT(*) as count FROM disaster_reports GROUP BY severity_level");
    $stmt->execute();
    $severityStats = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "\n⚡ Reports by severity:\n";
    foreach ($severityStats as $severity) {
        echo "  {$severity['severity_level']}: {$severity['count']}\n";
    }

    echo "\n🎉 DISASTER REPORTS MIGRATION COMPLETED SUCCESSFULLY!\n";
    echo "\nNext steps:\n";
    echo "1. Test cross-platform report visibility\n";
    echo "2. Validate report data integrity\n";
    echo "3. Proceed to Phase 3: Service Layer Migration\n";
} catch (Exception $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
    echo "\nRollback may be required. Check backup tables.\n";
}

echo "\n=== DISASTER REPORTS MIGRATION COMPLETE ===\n";
