<?php
require_once 'includes/db.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid data provided.']);
    exit;
}

$ruleName = $data['rule_name'] ?? '';
$conditionText = $data['condition_text'] ?? '';
$ruleAction = $data['rule_action'] ?? 'APPROVE';

if (empty($ruleName) || empty($conditionText)) {
    echo json_encode(['success' => false, 'message' => 'Rule name and condition are required.']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO moderation_rules (rule_name, condition_text, action) VALUES (?, ?, ?)");
    $stmt->execute([$ruleName, $conditionText, $ruleAction]);
    echo json_encode(['success' => true, 'message' => 'Auto-moderation rule created and activated.']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
