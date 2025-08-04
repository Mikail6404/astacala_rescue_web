<?php

// Direct database connection without Laravel
$host = 'localhost';
$dbname = 'astacalarescue';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $testPassword = password_hash('test123', PASSWORD_BCRYPT);

    $stmt = $pdo->prepare("INSERT INTO admins (username_akun_admin, password_akun_admin, nama_lengkap_admin, tanggal_lahir_admin, tempat_lahir_admin, no_anggota, no_handphone_admin, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");

    $stmt->execute([
        'testadmin',
        $testPassword,
        'Test Administrator',
        '1990-01-01',
        'Jakarta',
        'T-001-KH',
        '08123456789'
    ]);

    echo "Test user created successfully!\n";
    echo "Username: testadmin\n";
    echo "Password: test123\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
