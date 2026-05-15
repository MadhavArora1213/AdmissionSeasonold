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
$required_fields = ['collegeName', 'collegeType', 'state', 'city', 'admissionsStatus'];
foreach ($required_fields as $field) {
    if (empty($data[$field])) {
        echo json_encode(['success' => false, 'message' => "Field '$field' is required"]);
        exit;
    }
}

try {
    // Check if college already exists
    $checkStmt = $pdo->prepare("SELECT id FROM colleges WHERE name = ? LIMIT 1");
    $checkStmt->execute([$data['collegeName']]);
    
    if ($checkStmt->rowCount() > 0) {
        echo json_encode(['success' => false, 'message' => 'College with this name already exists']);
        exit;
    }

    // Generate slug from college name
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $data['collegeName']), '-'));
    
    // Check if slug already exists
    $slugCheck = $pdo->prepare("SELECT id FROM colleges WHERE slug = ? LIMIT 1");
    $slugCheck->execute([$slug]);
    
    if ($slugCheck->rowCount() > 0) {
        $slug = $slug . '-' . time();
    }

    // Insert new college with actual schema fields
    $stmt = $pdo->prepare("INSERT INTO colleges (name, slug, type, state, city, nirf_rank, about_description, is_verified) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->execute([
        $data['collegeName'],
        $slug,
        $data['collegeType'],
        $data['state'],
        $data['city'],
        $data['nirfRank'] ?: null,
        $data['description'] ?: null,
        true  // is_verified
    ]);

    echo json_encode(['success' => true, 'message' => 'College added successfully!', 'collegeId' => $pdo->lastInsertId()]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
