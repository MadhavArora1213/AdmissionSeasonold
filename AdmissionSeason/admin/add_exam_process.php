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
$required_fields = ['examName', 'examLevel'];
foreach ($required_fields as $field) {
    if (empty($data[$field])) {
        echo json_encode(['success' => false, 'message' => "Field '$field' is required"]);
        exit;
    }
}

try {
    // Check if exam already exists
    $checkStmt = $pdo->prepare("SELECT id FROM exams WHERE name = ? LIMIT 1");
    $checkStmt->execute([$data['examName']]);
    
    if ($checkStmt->rowCount() > 0) {
        echo json_encode(['success' => false, 'message' => 'Exam with this name already exists']);
        exit;
    }

    // Generate slug
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $data['examName']), '-'));
    
    // Check if slug already exists
    $slugCheck = $pdo->prepare("SELECT id FROM exams WHERE slug = ? LIMIT 1");
    $slugCheck->execute([$slug]);
    
    if ($slugCheck->rowCount() > 0) {
        $slug = $slug . '-' . time();
    }

    // Insert new exam
    $stmt = $pdo->prepare("INSERT INTO exams (name, slug, conducting_body, level, description, official_url, created_at) 
                           VALUES (?, ?, ?, ?, ?, ?, NOW())");
    
    $stmt->execute([
        $data['examName'],
        $slug,
        $data['conductingBody'] ?: null,
        $data['examLevel'],
        $data['description'] ?: null,
        $data['officialUrl'] ?: null
    ]);

    echo json_encode(['success' => true, 'message' => 'Exam added successfully!']);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
