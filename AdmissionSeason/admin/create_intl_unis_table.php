<?php
require_once 'includes/db.php';

try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS `international_universities` (
        `id` VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
        `name` VARCHAR(255) NOT NULL,
        `country` VARCHAR(100) NOT NULL,
        `city` VARCHAR(100),
        `qs_rank` INT,
        `avg_tuition` DECIMAL(10,2),
        `currency` VARCHAR(10) DEFAULT 'USD',
        `ielts_score` DECIMAL(3,1),
        `status` ENUM('ACTIVE','INACTIVE') DEFAULT 'ACTIVE',
        `is_partner` BOOLEAN DEFAULT FALSE,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    echo "Table 'international_universities' created successfully.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
