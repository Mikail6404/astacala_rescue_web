<?php

$pdo = new PDO('mysql:host=127.0.0.1;dbname=astacala_rescue', 'root', '');

echo "Admin count: ";
$stmt = $pdo->query('SELECT COUNT(*) FROM admins');
echo $stmt->fetchColumn() . "\n";

echo "Pengguna count: ";
$stmt = $pdo->query('SELECT COUNT(*) FROM penggunas');
echo $stmt->fetchColumn() . "\n";

echo "Backend user count: ";
$stmt = $pdo->query('SELECT COUNT(*) FROM users WHERE role IN ("ADMIN", "VOLUNTEER")');
echo $stmt->fetchColumn() . "\n";

echo "\n=== ALL BACKEND USERS ===\n";
$stmt = $pdo->query('SELECT id, name, email, role, place_of_birth, member_number FROM users WHERE role IN ("ADMIN", "VOLUNTEER") ORDER BY role, id');
while ($row = $stmt->fetch()) {
    echo "ID: {$row['id']}, Name: {$row['name']}, Email: {$row['email']}, Role: {$row['role']}, Birth: " . ($row['place_of_birth'] ?? 'N/A') . ", Member#: " . ($row['member_number'] ?? 'N/A') . "\n";
}
