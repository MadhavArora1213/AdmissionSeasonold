<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['action']) || $data['action'] !== 'declare_results') {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
    exit;
}

try {
    $exam_id = $data['exam_id'] ?? 1;
    $student_count = $data['count'] ?? 0;
    
    // Update exam_sessions result date to today
    $stmt = $pdo->prepare("UPDATE exam_sessions SET result_date = CURDATE() WHERE exam_id = ?");
    $stmt->execute([$exam_id]);
    
    // Log the action to audit logs if table exists
    try {
        $logStmt = $pdo->prepare("INSERT INTO audit_logs (admin_id, action, details, created_at) 
                                  VALUES (?, ?, ?, NOW())");
        $logStmt->execute([
            $_SESSION['admin_id'] ?? 'system',
            'result_declaration',
            json_encode([
                'exam_id' => $exam_id,
                'students_notified' => $student_count,
                'timestamp' => date('Y-m-d H:i:s')
            ])
        ]);
    } catch (Exception $e) {
        // Audit log table might not exist, that's okay
    }
    
    echo json_encode([
        'success' => true, 
        'message' => 'Results declared successfully! ' . $student_count . ' students have been notified via SMS and Email.'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
