<?php
require_once 'includes/db.php';

try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS `moderation_tasks` (
        `id` VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
        `task_type` VARCHAR(100) NOT NULL,
        `description` TEXT,
        `priority` ENUM('LOW','MEDIUM','HIGH','URGENT') NOT NULL DEFAULT 'MEDIUM',
        `status` ENUM('PENDING','IN_PROGRESS','COMPLETED') NOT NULL DEFAULT 'PENDING',
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    echo "Table 'moderation_tasks' created successfully.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
