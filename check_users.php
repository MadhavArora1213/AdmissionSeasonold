<?php
require_once 'AdmissionSeason/admin/includes/db.php';
$stmt = $pdo->query("DESCRIBE users");
$columns = $stmt->fetchAll();
foreach ($columns as $col) {
    echo $col['Field'] . " (" . $col['Type'] . ")\n";
}
?>
