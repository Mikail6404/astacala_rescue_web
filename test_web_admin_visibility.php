<?php

echo "=== WEB ADMIN DASHBOARD USER LIST TEST ===\n\n";

// Simulate accessing the web admin user list page
echo "Testing what users are visible in web admin dashboard...\n\n";

try {
    // Connect to web database
    $webPdo = new PDO('mysql:host=127.0.0.1;dbname=astacalarescue', 'root', '');

    echo "1. Users in web database (penggunas table):\n";
    $stmt = $webPdo->prepare("SELECT * FROM penggunas ORDER BY id");
    $stmt->execute();
    $webUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($webUsers) {
        foreach ($webUsers as $user) {
            echo "  - ID: {$user['id']}, Name: {$user['nama_lengkap_pengguna']}, Username: {$user['username_akun_pengguna']}\n";
        }
    } else {
        echo "  No users found in web database\n";
    }

    echo "\n";

    // Connect to backend database for comparison
    $backendPdo = new PDO('mysql:host=127.0.0.1;dbname=astacala_rescue', 'root', '');

    echo "2. Users in backend database (users table):\n";
    $stmt = $backendPdo->prepare("SELECT id, name, email, role FROM users ORDER BY id");
    $stmt->execute();
    $backendUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($backendUsers) {
        foreach ($backendUsers as $user) {
            echo "  - ID: {$user['id']}, Name: {$user['name']}, Email: {$user['email']}, Role: {$user['role']}\n";
        }
    } else {
        echo "  No users found in backend database\n";
    }

    echo "\n=== INTEGRATION REALITY ===\n";
    echo "Web admin dashboard shows users from: astacalarescue database\n";
    echo "Mobile app users are stored in: astacala_rescue database\n";
    echo "Result: WEB ADMIN CANNOT SEE MOBILE USERS\n";
    echo "Conclusion: NO CROSS-PLATFORM USER VISIBILITY\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
