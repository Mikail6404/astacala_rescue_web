<?php

// Direct database connection without Laravel
$host = 'localhost';
$dbname = 'astacalarescue';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Database structure check:\n\n";

    // First, check if table exists and get its structure
    $stmt = $pdo->query("DESCRIBE admins");
    echo "Columns in admins table:\n";
    while ($col = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- {$col['Field']} ({$col['Type']})\n";
    }

    echo "\nData in admins table:\n";
    $stmt = $pdo->query("SELECT * FROM admins LIMIT 3");

    while ($admin = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "\nAdmin record:\n";
        foreach ($admin as $key => $value) {
            echo "  $key: $value\n";
        }
        echo "------------------------\n";
    }
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage();
}

echo "\nYou can try logging in with any of these usernames.\n";
echo "Note: You'll need to know the actual password or reset it.\n";
