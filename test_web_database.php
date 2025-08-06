<?php

echo "=== Web Application Database Connection Test ===\n";

try {
    // Test database connection
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=astacala_rescue', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "✅ Database connection successful!\n\n";

    // List all tables
    echo "📋 Tables in database:\n";
    $stmt = $pdo->query('SHOW TABLES');
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        echo '  - '.$row[0]."\n";
    }

    echo "\n";

    // Check specific web app tables
    $webAppTables = ['admins', 'penggunas', 'pelaporans', 'berita_bencanas'];

    foreach ($webAppTables as $table) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) FROM $table");
            $count = $stmt->fetchColumn();
            echo "✅ Table '$table': $count records\n";
        } catch (Exception $e) {
            echo "❌ Table '$table': Error - ".$e->getMessage()."\n";
        }
    }

    echo "\n";

    // Check backend API tables integration
    $backendTables = ['users', 'disaster_reports', 'personal_access_tokens'];

    echo "🔗 Backend integration tables:\n";
    foreach ($backendTables as $table) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) FROM $table");
            $count = $stmt->fetchColumn();
            echo "✅ Table '$table': $count records\n";
        } catch (Exception $e) {
            echo "❌ Table '$table': Error - ".$e->getMessage()."\n";
        }
    }
} catch (Exception $e) {
    echo '❌ Database connection failed: '.$e->getMessage()."\n";
}

echo "\n=== Test Complete ===\n";
