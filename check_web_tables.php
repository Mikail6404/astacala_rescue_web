<?php

$pdo = new PDO('mysql:host=127.0.0.1;dbname=astacala_rescue', 'root', '');

echo "=== ADMINS TABLE STRUCTURE ===\n";
$stmt = $pdo->query('DESCRIBE admins');
while ($row = $stmt->fetch()) {
    echo $row['Field'].' - '.$row['Type']."\n";
}

echo "\n=== SAMPLE ADMIN DATA ===\n";
$stmt = $pdo->query('SELECT id, nama_lengkap_admin, username_akun_admin, tempat_lahir_admin, no_anggota FROM admins LIMIT 3');
while ($row = $stmt->fetch()) {
    print_r($row);
}

echo "\n=== PENGGUNAS TABLE STRUCTURE ===\n";
$stmt = $pdo->query('DESCRIBE penggunas');
while ($row = $stmt->fetch()) {
    echo $row['Field'].' - '.$row['Type']."\n";
}

echo "\n=== SAMPLE PENGGUNA DATA ===\n";
$stmt = $pdo->query('SELECT id, nama_lengkap_pengguna, username_akun_pengguna, tempat_lahir_pengguna FROM penggunas LIMIT 3');
while ($row = $stmt->fetch()) {
    print_r($row);
}

echo "\n=== BACKEND USERS EMAIL SAMPLES ===\n";
$stmt = $pdo->query('SELECT id, name, email, role FROM users WHERE role IN ("ADMIN", "VOLUNTEER") LIMIT 5');
while ($row = $stmt->fetch()) {
    print_r($row);
}
