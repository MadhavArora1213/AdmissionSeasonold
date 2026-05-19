<?php
require_once 'AdmissionSeason/admin/includes/db.php';
$stmt = $pdo->query("SHOW TABLES");
$tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
echo "Tables: " . implode(", ", $tables) . "\n";
?>
