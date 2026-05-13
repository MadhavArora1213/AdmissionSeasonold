<?php
require_once 'includes/db.php';

$name = "Super Admin";
$email = "admin@edusearch.in";
$password = "Admin@123";
$role = "SUPER_ADMIN";

$password_hash = password_hash($password, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $email, $password_hash, $role]);
    echo "Super Admin user created successfully!\n";
    echo "Email: $email\n";
    echo "Password: $password\n";
} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        echo "User already exists.\n";
    } else {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
?>
