<?php
require_once 'includes/db.php';

try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS `ip_blacklist` (
        `id` VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
        `ip_address` VARCHAR(45) NOT NULL UNIQUE,
        `reason` TEXT,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    echo "Table 'ip_blacklist' created successfully.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
