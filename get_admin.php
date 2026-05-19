<?php
require_once 'AdmissionSeason/admin/includes/db.php';
$stmt = $pdo->query("SELECT username, password FROM admins");
$admins = $stmt->fetchAll();
foreach ($admins as $admin) {
    echo "Username: " . $admin['username'] . " | Password: " . $admin['password'] . "\n";
}
?>
