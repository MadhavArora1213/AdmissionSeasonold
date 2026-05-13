<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Function to check specific roles if needed for specific pages
function require_role($roles) {
    if (!is_array($roles)) $roles = [$roles];
    if (!in_array($_SESSION['admin_role'], $roles)) {
        header('Location: index.php?error=unauthorized');
        exit;
    }
}
?>
