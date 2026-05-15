<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON data']);
    exit;
}

// Validate required fields
if (empty($data['name']) || empty($data['country'])) {
    echo json_encode(['success' => false, 'message' => 'University name and country are required']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO international_universities (name, country, city, qs_rank, avg_tuition, currency, ielts_score, is_partner) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->execute([
        $data['name'],
        $data['country'],
        $data['city'] ?: null,
        $data['qs_rank'] ?: null,
        $data['avg_tuition'] ?: null,
        $data['currency'] ?: 'USD',
        $data['ielts_score'] ?: null,
        $data['is_partner'] ?: 0
    ]);

    echo json_encode(['success' => true, 'message' => 'International university added successfully!']);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
