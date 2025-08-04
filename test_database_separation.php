<?php

echo "=== DATABASE COMPARISON TEST ===\n\n";

// Test Backend Database (astacala_rescue) - where we created the user
echo "1. Checking Backend Database (astacala_rescue) for user testvolunteer@web.test:\n";

try {
    $backendPdo = new PDO('mysql:host=127.0.0.1;dbname=astacala_rescue', 'root', '');
    $stmt = $backendPdo->prepare("SELECT id, name, email, role FROM users WHERE email = ?");
    $stmt->execute(['testvolunteer@web.test']);
    $backendUser = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($backendUser) {
        echo "✅ FOUND in backend database:\n";
        print_r($backendUser);
    } else {
        echo "❌ NOT FOUND in backend database\n";
    }
} catch (Exception $e) {
    echo "❌ Backend database error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test Web Database (astacalarescue) - where web app looks for users  
echo "2. Checking Web Database (astacalarescue) for user testvolunteer@web.test:\n";

try {
    $webPdo = new PDO('mysql:host=127.0.0.1;dbname=astacalarescue', 'root', '');

    // Check penggunas table (if it exists)
    $stmt = $webPdo->prepare("SHOW TABLES LIKE 'penggunas'");
    $stmt->execute();
    $penggunasExists = $stmt->fetch();

    if ($penggunasExists) {
        $stmt = $webPdo->prepare("SELECT * FROM penggunas WHERE email = ? OR username_akun_pengguna = ?");
        $stmt->execute(['testvolunteer@web.test', 'testvolunteer@web.test']);
        $webUser = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($webUser) {
            echo "✅ FOUND in web database (penggunas table):\n";
            print_r($webUser);
        } else {
            echo "❌ NOT FOUND in web database (penggunas table)\n";
        }
    }

    // Also check users table if it exists
    $stmt = $webPdo->prepare("SHOW TABLES LIKE 'users'");
    $stmt->execute();
    $usersExists = $stmt->fetch();

    if ($usersExists) {
        $stmt = $webPdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute(['testvolunteer@web.test']);
        $webUser2 = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($webUser2) {
            echo "✅ FOUND in web database (users table):\n";
            print_r($webUser2);
        } else {
            echo "❌ NOT FOUND in web database (users table)\n";
        }
    }
} catch (Exception $e) {
    echo "❌ Web database error: " . $e->getMessage() . "\n";
}

echo "\n";

// Show table structures to understand the difference
echo "3. Database table comparison:\n";

try {
    echo "Backend database tables:\n";
    $stmt = $backendPdo->prepare("SHOW TABLES");
    $stmt->execute();
    $backendTables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach ($backendTables as $table) {
        echo "  - $table\n";
    }

    echo "\nWeb database tables:\n";
    $stmt = $webPdo->prepare("SHOW TABLES");
    $stmt->execute();
    $webTables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach ($webTables as $table) {
        echo "  - $table\n";
    }
} catch (Exception $e) {
    echo "❌ Table comparison error: " . $e->getMessage() . "\n";
}

echo "\n=== CONCLUSION ===\n";
echo "If the user is found in backend database but NOT in web database,\n";
echo "this proves the databases are separate and the integration is NOT unified.\n";
