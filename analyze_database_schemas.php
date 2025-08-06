<?php

echo "=== COMPREHENSIVE DATABASE SCHEMA ANALYSIS ===\n\n";

try {
    // Backend Database Analysis (astacala_rescue)
    echo "1. BACKEND DATABASE SCHEMA (astacala_rescue) - Target Database:\n";
    echo '='.str_repeat('=', 60)."\n";

    $backendPdo = new PDO('mysql:host=127.0.0.1;dbname=astacala_rescue', 'root', '');

    // Get all tables
    $stmt = $backendPdo->prepare('SHOW TABLES');
    $stmt->execute();
    $backendTables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($backendTables as $table) {
        echo "\nTable: $table\n";
        echo str_repeat('-', 30)."\n";

        // Get table structure
        $stmt = $backendPdo->prepare("DESCRIBE $table");
        $stmt->execute();
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($columns as $column) {
            echo "  {$column['Field']} ({$column['Type']}) {$column['Null']} {$column['Key']} {$column['Default']}\n";
        }

        // Get row count
        $stmt = $backendPdo->prepare("SELECT COUNT(*) FROM $table");
        $stmt->execute();
        $count = $stmt->fetchColumn();
        echo "  → Records: $count\n";
    }

    echo "\n\n";

    // Web Database Analysis (astacalarescue)
    echo "2. WEB DATABASE SCHEMA (astacalarescue) - Source Database:\n";
    echo '='.str_repeat('=', 60)."\n";

    $webPdo = new PDO('mysql:host=127.0.0.1;dbname=astacalarescue', 'root', '');

    // Get all tables
    $stmt = $webPdo->prepare('SHOW TABLES');
    $stmt->execute();
    $webTables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($webTables as $table) {
        echo "\nTable: $table\n";
        echo str_repeat('-', 30)."\n";

        // Get table structure
        $stmt = $webPdo->prepare("DESCRIBE $table");
        $stmt->execute();
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($columns as $column) {
            echo "  {$column['Field']} ({$column['Type']}) {$column['Null']} {$column['Key']} {$column['Default']}\n";
        }

        // Get row count
        $stmt = $webPdo->prepare("SELECT COUNT(*) FROM $table");
        $stmt->execute();
        $count = $stmt->fetchColumn();
        echo "  → Records: $count\n";
    }

    echo "\n\n";

    // Cross-reference analysis
    echo "3. CROSS-REFERENCE ANALYSIS:\n";
    echo '='.str_repeat('=', 40)."\n";

    echo "\nBackend Tables: ".count($backendTables)." total\n";
    foreach ($backendTables as $table) {
        echo "  - $table\n";
    }

    echo "\nWeb Tables: ".count($webTables)." total\n";
    foreach ($webTables as $table) {
        echo "  - $table\n";
    }

    echo "\nCommon Tables:\n";
    $commonTables = array_intersect($backendTables, $webTables);
    foreach ($commonTables as $table) {
        echo "  ✅ $table (exists in both)\n";
    }

    echo "\nBackend-only Tables:\n";
    $backendOnly = array_diff($backendTables, $webTables);
    foreach ($backendOnly as $table) {
        echo "  📱 $table (backend only)\n";
    }

    echo "\nWeb-only Tables:\n";
    $webOnly = array_diff($webTables, $backendTables);
    foreach ($webOnly as $table) {
        echo "  🌐 $table (web only - NEEDS MIGRATION)\n";
    }
} catch (Exception $e) {
    echo '❌ Error: '.$e->getMessage()."\n";
}

echo "\n=== MIGRATION PLANNING REQUIRED ===\n";
echo "Web-only tables need to be migrated or mapped to backend equivalents.\n";
echo "Focus on data preservation and schema alignment.\n";
