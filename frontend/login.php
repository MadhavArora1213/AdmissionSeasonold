<?php
session_start();
// Already logged in? Go to dashboard
if (!empty($_SESSION['user_id'])) { header('Location: dashboard.php'); exit; }
require_once '../AdmissionSeason/admin/includes/db.php';


$error   = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action   = $_POST['action'] ?? 'login';
    $email    = trim($_POST['email']    ?? '');
    $password = trim($_POST['password'] ?? '');
    $name     = trim($_POST['name']     ?? '');

    if (!$email || !$password) {
        $error = 'Email and password are required.';
    } elseif ($action === 'register') {
        if (!$name) { $error = 'Please enter your full name.'; }
        else {
            $exists = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $exists->execute([$email]);
            if ($exists->fetch()) {
                $error = 'An account with this email already exists. Please login.';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $pdo->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, 'STUDENT')")->execute([$name, $email, $hash]);
                $success = true;
            }
        }
    } else {
        $stmt = $pdo->prepare("SELECT id, name, password_hash FROM users WHERE email = ? AND role = 'STUDENT'");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Invalid email or password. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login / Sign Up | AdmissionSeason</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <style type="text/tailwindcss">
        <?php include 'assets/css/style.css'; ?>
    </style>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="antialiased bg-[var(--bg-primary)] text-white font-['Inter'] min-h-screen flex items-center justify-center p-4 relative overflow-hidden">
    <!-- Background glows -->
    <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-indigo-500/15 blur-[120px] rounded-full pointer-events-none"></div>
    <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-purple-500/15 blur-[120px] rounded-full pointer-events-none"></div>

    <div class="w-full max-w-md relative z-10">
        <!-- Logo -->
        <div class="text-center mb-8">
            <a href="index.php" class="inline-flex items-center gap-2">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg shadow-indigo-500/30">
                    <i data-lucide="graduation-cap" class="w-5 h-5 text-white"></i>
                </div>
                <span class="text-xl font-bold gradient-text">AdmissionSeason</span>
            </a>
        </div>

        <!-- Card -->
        <div class="glass rounded-3xl border border-[var(--border)] p-8 shadow-2xl shadow-indigo-500/10">
            <!-- Tab Toggle -->
            <div id="tab-login" class="block">
                <h2 class="text-2xl font-bold text-white mb-1 text-center">Welcome back</h2>
                <p class="text-sm text-[var(--text-secondary)] text-center mb-8">Sign in to track your applications</p>

                <?php if ($error && ($_POST['action'] ?? 'login') === 'login'): ?>
                    <div class="mb-4 p-3 bg-red-500/10 border border-red-500/20 rounded-xl text-red-400 text-sm flex items-center gap-2">
                        <i data-lucide="alert-circle" class="w-4 h-4 flex-shrink-0"></i> <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="login">
                    <div class="relative">
                        <i data-lucide="mail" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[var(--text-muted)]"></i>
                        <input type="email" name="email" placeholder="Email address" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required
                            class="w-full pl-10 pr-4 py-3 bg-[var(--bg-secondary)] border border-[var(--border)] rounded-xl text-sm text-white placeholder-[var(--text-muted)] outline-none focus:border-indigo-500/60 transition-all">
                    </div>
                    <div class="relative">
                        <i data-lucide="lock" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[var(--text-muted)]"></i>
                        <input type="password" name="password" placeholder="Password" required
                            class="w-full pl-10 pr-4 py-3 bg-[var(--bg-secondary)] border border-[var(--border)] rounded-xl text-sm text-white placeholder-[var(--text-muted)] outline-none focus:border-indigo-500/60 transition-all">
                    </div>
                    <button type="submit" class="w-full btn-primary py-3 rounded-xl font-semibold flex items-center justify-center gap-2">
                        Sign In <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </button>
                </form>

                <p class="text-center text-sm text-[var(--text-muted)] mt-6">
                    Don't have an account?
                    <button onclick="switchTab('register')" class="text-indigo-400 hover:text-indigo-300 font-medium ml-1">Create one free</button>
                </p>
            </div>

            <!-- Register Tab -->
            <div id="tab-register" class="hidden">
                <h2 class="text-2xl font-bold text-white mb-1 text-center">Create Account</h2>
                <p class="text-sm text-[var(--text-secondary)] text-center mb-8">Join lakhs of students on AdmissionSeason</p>

                <?php if ($error && ($_POST['action'] ?? '') === 'register'): ?>
                    <div class="mb-4 p-3 bg-red-500/10 border border-red-500/20 rounded-xl text-red-400 text-sm flex items-center gap-2">
                        <i data-lucide="alert-circle" class="w-4 h-4 flex-shrink-0"></i> <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="mb-4 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-emerald-400 text-sm text-center">
                        ✅ Account created! You can now <button onclick="switchTab('login')" class="underline font-medium">sign in</button>.
                    </div>
                <?php endif; ?>

                <form method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="register">
                    <div class="relative">
                        <i data-lucide="user" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[var(--text-muted)]"></i>
                        <input type="text" name="name" placeholder="Full Name" required
                            class="w-full pl-10 pr-4 py-3 bg-[var(--bg-secondary)] border border-[var(--border)] rounded-xl text-sm text-white placeholder-[var(--text-muted)] outline-none focus:border-indigo-500/60 transition-all">
                    </div>
                    <div class="relative">
                        <i data-lucide="mail" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[var(--text-muted)]"></i>
                        <input type="email" name="email" placeholder="Email address" required
                            class="w-full pl-10 pr-4 py-3 bg-[var(--bg-secondary)] border border-[var(--border)] rounded-xl text-sm text-white placeholder-[var(--text-muted)] outline-none focus:border-indigo-500/60 transition-all">
                    </div>
                    <div class="relative">
                        <i data-lucide="lock" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[var(--text-muted)]"></i>
                        <input type="password" name="password" placeholder="Password (min 8 chars)" required minlength="8"
                            class="w-full pl-10 pr-4 py-3 bg-[var(--bg-secondary)] border border-[var(--border)] rounded-xl text-sm text-white placeholder-[var(--text-muted)] outline-none focus:border-indigo-500/60 transition-all">
                    </div>
                    <button type="submit" class="w-full btn-primary py-3 rounded-xl font-semibold flex items-center justify-center gap-2">
                        Create Account <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </button>
                </form>

                <p class="text-center text-sm text-[var(--text-muted)] mt-6">
                    Already have an account?
                    <button onclick="switchTab('login')" class="text-indigo-400 hover:text-indigo-300 font-medium ml-1">Sign in</button>
                </p>
            </div>
        </div>

        <p class="text-center text-xs text-[var(--text-muted)] mt-6">
            By continuing, you agree to our <a href="terms.php" class="hover:text-white transition-colors">Terms</a> and <a href="privacy.php" class="hover:text-white transition-colors">Privacy Policy</a>.
        </p>
    </div>

<script>
lucide.createIcons();
function switchTab(tab) {
    document.getElementById('tab-login').classList.toggle('hidden', tab !== 'login');
    document.getElementById('tab-register').classList.toggle('hidden', tab !== 'register');
    document.getElementById('tab-login').classList.toggle('block', tab === 'login');
    document.getElementById('tab-register').classList.toggle('block', tab !== 'login');
}
// Auto-switch to register tab if register action had error/success
<?php if (($_POST['action'] ?? '') === 'register'): ?>
switchTab('register');
<?php endif; ?>
</script>
</body>
</html>
