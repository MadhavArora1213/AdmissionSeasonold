<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

$widget = $_GET['widget'] ?? 'leads';

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $widget . '_export_' . date('Y-m-d') . '.csv"');

$output = fopen('php://output', 'w');

if ($widget === 'lead-velocity' || $widget === 'leads') {
    fputcsv($output, ['ID', 'College', 'Student Name', 'Email', 'Phone', 'Status', 'Created At']);
    $stmt = $pdo->query("SELECT l.*, c.name as college_name FROM leads l LEFT JOIN colleges c ON l.college_id = c.id ORDER BY l.created_at DESC");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, [
            $row['id'],
            $row['college_name'],
            $row['student_name'],
            $row['student_email'],
            $row['student_phone'],
            $row['status'],
            $row['created_at']
        ]);
    }
} elseif ($widget === 'student-activity') {
    fputcsv($output, ['ID', 'Name', 'Email', 'Joined At']);
    $stmt = $pdo->query("SELECT id, name, email, created_at FROM users ORDER BY created_at DESC");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, $row);
    }
} elseif ($widget === 'revenue-trend') {
    fputcsv($output, ['ID', 'College', 'Plan', 'CPL', 'Balance']);
    $stmt = $pdo->query("SELECT b.id, c.name, b.plan, b.cpl_rate, b.lead_credit_balance FROM college_b2b_accounts b JOIN colleges c ON b.college_id = c.id");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, $row);
    }
} else {
    fputcsv($output, ['Error', 'Widget not found or export not supported for this type.']);
}

fclose($output);
exit;
