<?php

echo "=== PHASE 2: DISASTER REPORTS MIGRATION ===\n\n";

try {
    $backendPdo = new PDO('mysql:host=127.0.0.1;dbname=astacala_rescue', 'root', '');
    $webPdo = new PDO('mysql:host=127.0.0.1;dbname=astacalarescue', 'root', '');

    // Enable error reporting
    $backendPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "📋 Analyzing report migration requirements...\n\n";

    // 1. Analyze source data
    echo "1. Source Data Analysis:\n";
    echo "=" . str_repeat("=", 25) . "\n";

    $stmt = $webPdo->prepare("SELECT COUNT(*) FROM pelaporans");
    $stmt->execute();
    $sourceCount = $stmt->fetchColumn();
    echo "📊 Source reports (pelaporans): $sourceCount records\n";

    $stmt = $backendPdo->prepare("SELECT COUNT(*) FROM disaster_reports");
    $stmt->execute();
    $targetCount = $stmt->fetchColumn();
    echo "📊 Target reports (disaster_reports): $targetCount records\n";

    // 2. Analyze report structure
    echo "\n2. Report Structure Analysis:\n";
    echo "=" . str_repeat("=", 31) . "\n";

    $stmt = $webPdo->prepare("
        SELECT 
            id,
            informasi_singkat_bencana,
            skala_bencana,
            status_verifikasi,
            lokasi_bencana,
            nama_team_pelapor,
            jumlah_korban,
            jumlah_personel,
            pelapor_pengguna_id,
            created_at
        FROM pelaporans 
        ORDER BY id
    ");
    $stmt->execute();
    $sourceReports = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "📋 Reports to migrate:\n";
    foreach ($sourceReports as $report) {
        $severity = match ($report['skala_bencana']) {
            'kecil' => 'MINOR',
            'sedang' => 'MODERATE',
            'besar' => 'MAJOR',
            default => 'MINOR'
        };

        $status = match ($report['status_verifikasi']) {
            'PENDING' => 'PENDING',
            'DITERIMA' => 'VERIFIED',
            'DITOLAK' => 'REJECTED',
            default => 'PENDING'
        };

        echo "  📄 ID {$report['id']}: {$report['informasi_singkat_bencana']}\n";
        echo "     Location: {$report['lokasi_bencana']}\n";
        echo "     Severity: {$report['skala_bencana']} → $severity\n";
        echo "     Status: {$report['status_verifikasi']} → $status\n";
        echo "     Reporter ID: {$report['pelapor_pengguna_id']} (needs mapping)\n";
        echo "     Team: {$report['nama_team_pelapor']}\n";
        echo "     Casualties: {$report['jumlah_korban']}\n";
        echo "     Personnel: {$report['jumlah_personel']}\n\n";
    }

    // 3. Check user ID mapping requirements
    echo "3. User ID Mapping Analysis:\n";
    echo "=" . str_repeat("=", 30) . "\n";

    // Get unique reporter IDs from pelaporans
    $stmt = $webPdo->prepare("SELECT DISTINCT pelapor_pengguna_id FROM pelaporans WHERE pelapor_pengguna_id IS NOT NULL");
    $stmt->execute();
    $reporterIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo "📊 Unique reporter IDs in reports: " . implode(', ', $reporterIds) . "\n";

    // Check if these users exist in migration log
    if (!empty($reporterIds)) {
        $placeholders = str_repeat('?,', count($reporterIds) - 1) . '?';
        $stmt = $backendPdo->prepare("
            SELECT source_id, target_user_id, source_username, target_email 
            FROM user_migration_log 
            WHERE source_table = 'penggunas' AND source_id IN ($placeholders)
        ");
        $stmt->execute($reporterIds);
        $userMappings = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo "📋 User mappings available:\n";
        foreach ($userMappings as $mapping) {
            echo "  👤 Web ID {$mapping['source_id']} → Backend ID {$mapping['target_user_id']} ({$mapping['target_email']})\n";
        }

        // Check for unmapped users
        $mappedIds = array_column($userMappings, 'source_id');
        $unmappedIds = array_diff($reporterIds, $mappedIds);

        if (!empty($unmappedIds)) {
            echo "⚠️  Unmapped reporter IDs: " . implode(', ', $unmappedIds) . "\n";
            echo "   These will be set to NULL or a default user ID\n";
        }
    }

    // 4. Check for conflicts
    echo "\n4. Conflict Detection:\n";
    echo "=" . str_repeat("=", 20) . "\n";

    // Check if any reports have conflicting titles or data
    foreach ($sourceReports as $report) {
        $stmt = $backendPdo->prepare("
            SELECT COUNT(*) FROM disaster_reports 
            WHERE title = ? OR location_name = ?
        ");
        $stmt->execute([$report['informasi_singkat_bencana'], $report['lokasi_bencana']]);
        $conflicts = $stmt->fetchColumn();

        if ($conflicts > 0) {
            echo "⚠️  Potential conflict for report ID {$report['id']}: Similar title/location exists\n";
        }
    }

    echo "✅ No major conflicts detected\n";

    // 5. Migration readiness assessment
    echo "\n5. Migration Readiness Assessment:\n";
    echo "=" . str_repeat("=", 35) . "\n";

    $readyForMigration = true;
    $issues = [];

    if ($sourceCount == 0) {
        $issues[] = "No source reports to migrate";
        $readyForMigration = false;
    }

    if (empty($userMappings) && !empty($reporterIds)) {
        $issues[] = "User mappings not available (run user migration first)";
        $readyForMigration = false;
    }

    if ($readyForMigration) {
        echo "✅ READY FOR MIGRATION\n";
        echo "📊 Summary:\n";
        echo "   • Source reports: $sourceCount\n";
        echo "   • Target reports before: $targetCount\n";
        echo "   • Expected total after: " . ($targetCount + $sourceCount) . "\n";
        echo "   • User mappings: " . count($userMappings) . " available\n";

        echo "\n🚀 Next step: Execute migration_02_reports.php\n";
    } else {
        echo "❌ NOT READY FOR MIGRATION\n";
        echo "Issues to resolve:\n";
        foreach ($issues as $issue) {
            echo "   • $issue\n";
        }
    }
} catch (Exception $e) {
    echo "❌ Analysis failed: " . $e->getMessage() . "\n";
}

echo "\n=== REPORT MIGRATION ANALYSIS COMPLETE ===\n";
