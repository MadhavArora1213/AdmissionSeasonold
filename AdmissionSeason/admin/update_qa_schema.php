<?php
require_once 'includes/db.php';

try {
    $pdo->exec("ALTER TABLE `college_qa` ADD COLUMN `report_count` INT NOT NULL DEFAULT 0;");
    echo "Column 'report_count' added successfully to 'college_qa'.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
