<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);
ini_set('display_errors', 0);
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Ensure all required session variables are set
if (!isset($_SESSION['admin_name'])) {
    $_SESSION['admin_name'] = 'Admin';
}

if (!isset($_SESSION['admin_role'])) {
    $_SESSION['admin_role'] = 'SUPER_ADMIN';
}

if (!isset($_SESSION['admin_email'])) {
    $_SESSION['admin_email'] = 'admin@example.com';
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
