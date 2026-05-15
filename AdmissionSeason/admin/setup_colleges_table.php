<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

header('Content-Type: application/json');

try {
    // Check if colleges table exists and has required columns
    $result = $pdo->query("SHOW TABLES LIKE 'colleges'");
    
    if ($result->rowCount() == 0) {
        // Create colleges table
        $pdo->exec("CREATE TABLE colleges (
            id INT PRIMARY KEY AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL UNIQUE,
            type VARCHAR(100),
            state VARCHAR(100),
            city VARCHAR(100),
            location VARCHAR(255),
            nirf_rank INT,
            admissions_status VARCHAR(50),
            website VARCHAR(255),
            description LONGTEXT,
            status VARCHAR(50) DEFAULT 'Verified',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_state (state),
            INDEX idx_city (city),
            INDEX idx_type (type)
        )");
        
        echo json_encode(['success' => true, 'message' => 'Colleges table created successfully']);
    } else {
        // Verify all columns exist
        $columns = $pdo->query("SHOW COLUMNS FROM colleges")->fetchAll(PDO::FETCH_COLUMN);
        $required_columns = ['name', 'type', 'state', 'city', 'location', 'nirf_rank', 'admissions_status', 'website', 'description', 'status'];
        
        $missing_columns = array_diff($required_columns, $columns);
        
        if (!empty($missing_columns)) {
            $message = 'Missing columns: ' . implode(', ', $missing_columns);
            echo json_encode(['success' => false, 'message' => $message]);
        } else {
            echo json_encode(['success' => true, 'message' => 'Colleges table structure is valid']);
        }
    }
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
