<?php
require_once 'AdmissionSeason/admin/includes/db.php';
$stmt = $pdo->query("SELECT email FROM users WHERE role = 'SUPER_ADMIN'");
$admins = $stmt->fetchAll();
foreach ($admins as $admin) {
    echo "Admin Email: " . $admin['email'] . "\n";
}
?>
