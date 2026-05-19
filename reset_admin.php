<?php
require_once 'AdmissionSeason/admin/includes/db.php';
$password = 'admin123';
$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE email = 'admin@edusearch.in'");
if ($stmt->execute([$hash])) {
    echo "Password reset to: admin123\n";
} else {
    echo "Failed to reset password.\n";
}
?>
