<?php
session_start();
require_once '../AdmissionSeason/admin/includes/db.php';

$success = false;
$error = '';
$college = null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $college_id = trim($_POST['college_id'] ?? '');
    $name       = trim($_POST['name'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $phone      = trim($_POST['phone'] ?? '');
    $course_id  = trim($_POST['course'] ?? '') ?: null;

    // Basic validation
    if (!$college_id || !$name || !$email || !$phone) {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        // Fetch college name for confirmation
        $stmt = $pdo->prepare("SELECT id, name, city, state FROM colleges WHERE id = ?");
        $stmt->execute([$college_id]);
        $college = $stmt->fetch();

        if (!$college) {
            $error = 'Invalid college. Please try again.';
        } else {
            // Check for duplicate lead (same phone + college in last 30 days)
            $dup = $pdo->prepare("SELECT id FROM leads WHERE student_phone = ? AND college_id = ? AND created_at > NOW() - INTERVAL 30 DAY");
            $dup->execute([$phone, $college_id]);
            if ($dup->fetch()) {
                $error = 'You have already submitted an enquiry for this college recently. Our team will contact you soon!';
            } else {
                // Insert lead
                $insert = $pdo->prepare("
                    INSERT INTO leads (college_id, course_id, student_name, student_email, student_phone, source_page, quality_score, status)
                    VALUES (?, ?, ?, ?, ?, ?, 'MEDIUM', 'NEW')
                ");
                $insert->execute([
                    $college_id,
                    $course_id ?: null,
                    $name,
                    $email,
                    $phone,
                    $_SERVER['HTTP_REFERER'] ?? 'college.php'
                ]);

                // Also insert into 'applications' if user is logged in
                if (!empty($_SESSION['user_id'])) {
                    $appInsert = $pdo->prepare("
                        INSERT INTO applications (student_id, college_id, course_id, status, applied_at)
                        VALUES (?, ?, ?, 'PENDING', NOW())
                    ");
                    $appInsert->execute([
                        $_SESSION['user_id'],
                        $college_id,
                        $course_id ?: null
                    ]);
                }

                $success = true;
            }
        }
    }

    // If we don't have college yet (error case), try to fetch it
    if (!$college && $college_id) {
        $stmt = $pdo->prepare("SELECT id, name, city, state FROM colleges WHERE id = ?");
        $stmt->execute([$college_id]);
        $college = $stmt->fetch();
    }
}

// If accessed via GET (no form submission), redirect home
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $success ? 'Application Submitted!' : 'Apply Now' ?> | AdmissionSeason</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <style type="text/tailwindcss">
        <?php include 'assets/css/style.css'; ?>
        @keyframes scaleIn {
            from { opacity: 0; transform: scale(0.8); }
            to   { opacity: 1; transform: scale(1); }
        }
        .anim-scale { animation: scaleIn 0.5s cubic-bezier(.17,.67,.38,1.3) forwards; }
    </style>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="antialiased bg-[var(--bg-primary)] text-white font-['Inter'] min-h-screen flex items-center justify-center p-4">

<?php if ($success): ?>
    <!-- SUCCESS STATE -->
    <div class="text-center max-w-md w-full">
        <div class="glass rounded-3xl border border-emerald-500/30 p-10 shadow-2xl shadow-emerald-500/10 relative overflow-hidden">
            <!-- Glow -->
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-teal-500/5 pointer-events-none"></div>

            <div class="relative z-10">
                <!-- Checkmark Icon -->
                <div class="w-24 h-24 mx-auto rounded-full bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center mb-6 shadow-xl shadow-emerald-500/30 anim-scale">
                    <i data-lucide="check" class="w-12 h-12 text-white"></i>
                </div>

                <h1 class="text-2xl font-bold text-white mb-2">Application Submitted! 🎉</h1>
                <p class="text-[var(--text-secondary)] text-sm mb-1">
                    Your enquiry for
                </p>
                <p class="text-indigo-400 font-semibold mb-6">
                    <?= htmlspecialchars($college['name']) ?>
                </p>

                <div class="bg-[var(--bg-secondary)] rounded-xl p-4 text-left mb-8 border border-[var(--border)] text-sm space-y-2">
                    <div class="flex items-center gap-2 text-[var(--text-secondary)]">
                        <i data-lucide="user" class="w-4 h-4 text-indigo-400"></i>
                        <span class="text-white font-medium"><?= htmlspecialchars($name) ?></span>
                    </div>
                    <div class="flex items-center gap-2 text-[var(--text-secondary)]">
                        <i data-lucide="mail" class="w-4 h-4 text-indigo-400"></i>
                        <?= htmlspecialchars($email) ?>
                    </div>
                    <div class="flex items-center gap-2 text-[var(--text-secondary)]">
                        <i data-lucide="phone" class="w-4 h-4 text-indigo-400"></i>
                        <?= htmlspecialchars($phone) ?>
                    </div>
                </div>

                <p class="text-xs text-[var(--text-muted)] mb-8">
                    The college team will contact you within <span class="text-amber-400 font-medium">24–48 hours</span>. Check your email for confirmation.
                </p>

                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="colleges.php" class="flex-1 flex items-center justify-center gap-2 px-4 py-3 rounded-xl glass border border-[var(--border)] text-sm font-medium hover:bg-white/5 transition-colors">
                        <i data-lucide="search" class="w-4 h-4"></i> More Colleges
                    </a>
                    <a href="ai-counselor.php" class="flex-1 flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-sm font-medium transition-colors">
                        <i data-lucide="bot" class="w-4 h-4"></i> Talk to AI Counselor
                    </a>
                </div>
            </div>
        </div>
    </div>

<?php else: ?>
    <!-- ERROR STATE -->
    <div class="text-center max-w-md w-full">
        <div class="glass rounded-3xl border border-red-500/30 p-10 shadow-2xl shadow-red-500/10 relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-red-500/5 to-orange-500/5 pointer-events-none"></div>

            <div class="relative z-10">
                <div class="w-20 h-20 mx-auto rounded-full bg-gradient-to-br from-red-500 to-orange-500 flex items-center justify-center mb-6 shadow-xl shadow-red-500/30">
                    <i data-lucide="alert-circle" class="w-10 h-10 text-white"></i>
                </div>

                <h1 class="text-2xl font-bold text-white mb-3">Submission Failed</h1>
                <p class="text-red-400 text-sm mb-8 bg-red-500/10 border border-red-500/20 rounded-xl p-4">
                    <?= htmlspecialchars($error) ?>
                </p>

                <a href="javascript:history.back()" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-sm font-medium transition-colors">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i> Go Back & Retry
                </a>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
    lucide.createIcons();
</script>
</body>
</html>
