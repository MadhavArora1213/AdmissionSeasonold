<?php
session_start();
require_once 'includes/db.php';

// If already logged in, redirect to dashboard
if (isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($email && $password) {
        $stmt = $pdo->prepare("SELECT id, name, email, password_hash, role FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            // Check if role is allowed in admin panel
            $allowed_roles = ['SUPER_ADMIN', 'MODERATOR', 'DATA_ENTRY'];
            if (in_array($user['role'], $allowed_roles)) {
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_name'] = $user['name'];
                $_SESSION['admin_role'] = $user['role'];
                
                header('Location: index.php');
                exit;
            } else {
                $error = "Access Denied: You do not have administrative privileges.";
            }
        } else {
            $error = "Invalid email or password.";
        }
    } else {
        $error = "Please fill in all fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | EduSearch</title>
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: radial-gradient(circle at top left, #1e1b4b, #0f172a);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            overflow: hidden;
        }
        .login-card {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 3rem;
            border-radius: 24px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            text-align: center;
        }
        .login-logo {
            font-size: 2.5rem;
            font-weight: 900;
            background: linear-gradient(to right, #6366f1, #a855f7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
        }
        .login-subtitle {
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin-bottom: 2.5rem;
        }
        .form-group {
            text-align: left;
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            color: var(--text-secondary);
            font-size: 0.8rem;
            margin-bottom: 8px;
            font-weight: 600;
        }
        .form-input {
            width: 100%;
            background: rgba(15, 23, 42, 0.5);
            border: 1px solid var(--border-color);
            padding: 12px 16px;
            border-radius: 12px;
            color: white;
            font-size: 1rem;
            transition: 0.3s;
        }
        .form-input:focus {
            outline: none;
            border-color: var(--accent-primary);
            box-shadow: 0 0 15px rgba(99, 102, 241, 0.2);
        }
        .error-msg {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
            padding: 10px;
            border-radius: 8px;
            font-size: 0.8rem;
            margin-bottom: 1.5rem;
            border: 1px solid var(--danger);
        }
        .login-btn {
            width: 100%;
            background: var(--accent-primary);
            color: white;
            border: none;
            padding: 14px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: 0.3s;
            box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.3);
        }
        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(99, 102, 241, 0.4);
        }
        .footer-links {
            margin-top: 2rem;
            font-size: 0.8rem;
            color: var(--text-secondary);
        }
        .footer-links a {
            color: var(--accent-primary);
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-logo">EduSearch</div>
        <p class="login-subtitle">Administrative Command Centre</p>

        <?php if ($error): ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Work Email Address</label>
                <input type="email" name="email" class="form-input" placeholder="admin@edusearch.in" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-input" placeholder="••••••••" required>
            </div>
            <button type="submit" class="login-btn">Authenticate & Access</button>
        </form>

        <div class="footer-links">
            Secure session via 256-bit encryption. <br>
            <a href="#">Forgot credentials?</a> &bull; <a href="../">Back to Platform</a>
        </div>
    </div>
</body>
</html>
