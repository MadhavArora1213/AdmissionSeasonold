<?php
require_once 'includes/db.php';

try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS `moderation_rules` (
        `id` VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
        `rule_name` VARCHAR(255) NOT NULL,
        `condition_text` TEXT,
        `action` ENUM('APPROVE','REJECT','ESCALATE') NOT NULL,
        `is_active` BOOLEAN DEFAULT TRUE,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    echo "Table 'moderation_rules' created successfully.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
