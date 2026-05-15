<?php
session_start();
require_once '../AdmissionSeason/admin/includes/db.php';

// Auth guard — redirect to login if not logged in
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$page_title = "Student Dashboard | AdmissionSeason";
$userId = $_SESSION['user_id'];

// Fetch real student data
$uStmt = $pdo->prepare("SELECT name, email, phone FROM users WHERE id = ?");
$uStmt->execute([$userId]);
$user = $uStmt->fetch();

if (!$user) {
    session_destroy();
    header('Location: login.php');
    exit;
}

// Profile completion score
$pStmt = $pdo->prepare("SELECT * FROM student_profiles WHERE user_id = ?");
$pStmt->execute([$userId]);
$profile = $pStmt->fetch();
$completion = 30; // base for having an account
if ($user['phone'])               $completion += 10;
if ($profile)                     $completion += 20;
if ($profile && $profile['stream']) $completion += 10;
if ($profile && $profile['class_12_marks']) $completion += 15;
if ($profile && $profile['preferred_cities']) $completion += 15;

$student = [
    'name'               => $user['name'],
    'email'              => $user['email'],
    'phone'              => $user['phone'] ?? '',
    'profile_completion' => min(100, $completion),
    'counseling_points'  => $profile['counseling_points'] ?? 0,
];

// Real applications
$stmt = $pdo->prepare("
    SELECT a.status, a.applied_at, c.name as college_name, cr.name as course_name, c.id as college_id
    FROM applications a 
    JOIN colleges c ON a.college_id = c.id 
    JOIN courses cr ON a.course_id = cr.id
    WHERE a.student_id = ?
    ORDER BY a.applied_at DESC
    LIMIT 5
");
$stmt->execute([$userId]);
$applications = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Logout handler
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS (v4) -->
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <style type="text/tailwindcss">
        <?php include 'assets/css/style.css'; ?>
    </style>
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="antialiased bg-[var(--bg-primary)] text-white font-['Inter']">

<?php include 'includes/navbar.php'; ?>

<div class="min-h-screen pt-16 flex">
    <!-- Sidebar -->
    <aside class="w-64 hidden lg:flex flex-col bg-[var(--bg-secondary)] border-r border-[var(--border)] fixed h-[calc(100vh-64px)] overflow-y-auto">
        <div class="p-6 border-b border-[var(--border)]">
            <div class="w-16 h-16 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-2xl font-bold text-white mb-3 shadow-lg">
                <?= substr($student['name'], 0, 1) ?>
            </div>
            <h2 class="font-bold text-white"><?= htmlspecialchars($student['name']) ?></h2>
            <p class="text-xs text-[var(--text-muted)]"><?= htmlspecialchars($student['email']) ?></p>
            
            <div class="mt-4">
                <div class="flex justify-between text-xs mb-1">
                    <span class="text-[var(--text-secondary)]">Profile Completion</span>
                    <span class="text-indigo-400 font-medium"><?= $student['profile_completion'] ?>%</span>
                </div>
                <div class="w-full h-1.5 bg-[var(--bg-primary)] rounded-full overflow-hidden">
                    <div class="h-full bg-indigo-500 rounded-full" style="width: <?= $student['profile_completion'] ?>%"></div>
                </div>
            </div>
        </div>
        
        <nav class="flex-1 p-4 space-y-1">
            <a href="dashboard.php" class="flex items-center gap-3 px-4 py-3 bg-indigo-500/10 text-indigo-400 rounded-xl font-medium transition-colors">
                <i data-lucide="layout-dashboard" class="w-5 h-5"></i> Dashboard
            </a>
            <a href="apply.php" class="flex items-center gap-3 px-4 py-3 text-[var(--text-secondary)] hover:text-white hover:bg-white/5 rounded-xl font-medium transition-colors">
                <i data-lucide="file-text" class="w-5 h-5"></i> Applications
            </a>
            <a href="colleges.php" class="flex items-center gap-3 px-4 py-3 text-[var(--text-secondary)] hover:text-white hover:bg-white/5 rounded-xl font-medium transition-colors">
                <i data-lucide="bookmark" class="w-5 h-5"></i> Saved Colleges
            </a>
            <a href="scholarships.php" class="flex items-center gap-3 px-4 py-3 text-[var(--text-secondary)] hover:text-white hover:bg-white/5 rounded-xl font-medium transition-colors">
                <i data-lucide="award" class="w-5 h-5"></i> Scholarships
            </a>
            <a href="ai-counselor.php" class="flex items-center gap-3 px-4 py-3 text-[var(--text-secondary)] hover:text-white hover:bg-white/5 rounded-xl font-medium transition-colors">
                <i data-lucide="bot" class="w-5 h-5"></i> AI Counselor
            </a>
        </nav>
        
        <div class="p-4 border-t border-[var(--border)]">
            <a href="?logout=1" class="flex items-center gap-3 px-4 py-3 text-red-400 hover:bg-red-500/10 rounded-xl font-medium transition-colors">
                <i data-lucide="log-out" class="w-5 h-5"></i> Sign Out
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 lg:ml-64 p-6 lg:p-10 overflow-y-auto">
        <div class="max-w-5xl mx-auto">
            
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-white mb-1">Welcome back, <?= explode(' ', $student['name'])[0] ?>! 👋</h1>
                    <p class="text-[var(--text-secondary)] text-sm">Here's what's happening with your college applications.</p>
                </div>
                <a href="colleges.php" class="btn-primary inline-flex items-center gap-2">
                    <i data-lucide="search" class="w-4 h-4"></i> Explore Colleges
                </a>
            </div>
            
            <!-- Stats Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
                <div class="glass p-6 rounded-2xl border border-[var(--border)] relative overflow-hidden">
                    <div class="absolute -right-4 -bottom-4 opacity-10"><i data-lucide="file-check" class="w-24 h-24 text-indigo-500"></i></div>
                    <div class="relative z-10">
                        <div class="text-[var(--text-secondary)] text-sm font-medium mb-1">Total Applications</div>
                        <div class="text-3xl font-bold text-white"><?= count($applications) ?></div>
                    </div>
                </div>
                
                <div class="glass p-6 rounded-2xl border border-[var(--border)] relative overflow-hidden">
                    <div class="absolute -right-4 -bottom-4 opacity-10"><i data-lucide="bookmark" class="w-24 h-24 text-purple-500"></i></div>
                    <div class="relative z-10">
                        <div class="text-[var(--text-secondary)] text-sm font-medium mb-1">Shortlisted</div>
                        <div class="text-3xl font-bold text-white">4</div>
                    </div>
                </div>
                
                <div class="glass p-6 rounded-2xl border border-[var(--border)] relative overflow-hidden border-indigo-500/30">
                    <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 to-purple-500/5"></div>
                    <div class="absolute -right-4 -bottom-4 opacity-10"><i data-lucide="award" class="w-24 h-24 text-amber-500"></i></div>
                    <div class="relative z-10">
                        <div class="flex items-center gap-2 text-[var(--text-secondary)] text-sm font-medium mb-1">
                            Reward Points <i data-lucide="info" class="w-3.5 h-3.5 text-[var(--text-muted)]" title="Earn points by reviewing colleges"></i>
                        </div>
                        <div class="text-3xl font-bold text-amber-400"><?= number_format($student['counseling_points']) ?></div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column -->
                <div class="lg:col-span-2 space-y-8">
                    
                    <!-- Recent Applications -->
                    <div class="glass rounded-2xl border border-[var(--border)] overflow-hidden">
                        <div class="px-6 py-5 border-b border-[var(--border)] flex items-center justify-between">
                            <h2 class="text-lg font-bold text-white flex items-center gap-2">
                                <i data-lucide="file-text" class="w-5 h-5 text-indigo-400"></i> Recent Applications
                            </h2>
                            <a href="apply.php" class="text-sm text-indigo-400 hover:text-indigo-300 transition-colors">View All</a>
                        </div>
                        
                        <div class="divide-y divide-[var(--border)]">
                            <?php if (empty($applications)): ?>
                                <div class="p-8 text-center text-[var(--text-secondary)]">
                                    <i data-lucide="inbox" class="w-10 h-10 mx-auto mb-3 opacity-30"></i>
                                    <p>You haven't submitted any applications yet.</p>
                                    <a href="colleges.php" class="text-indigo-400 hover:underline text-sm mt-2 inline-block">Browse Colleges</a>
                                </div>
                            <?php else: ?>
                                <?php foreach ($applications as $app): ?>
                                    <?php
                                    $statusColors = [
                                        'SUBMITTED' => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
                                        'UNDER_REVIEW' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                                        'SHORTLISTED' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                                        'REJECTED' => 'bg-red-500/10 text-red-400 border-red-500/20'
                                    ];
                                    $color = $statusColors[$app['status']] ?? 'bg-gray-500/10 text-gray-400 border-gray-500/20';
                                    ?>
                                    <div class="p-6 hover:bg-white/[0.02] transition-colors">
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                            <div class="flex items-start gap-4">
                                                <div class="w-12 h-12 rounded-xl bg-[var(--bg-secondary)] border border-[var(--border)] flex items-center justify-center flex-shrink-0 text-xl shadow-inner">
                                                    🎓
                                                </div>
                                                <div>
                                                    <h3 class="font-bold text-white text-base mb-1"><?= htmlspecialchars($app['college_name']) ?></h3>
                                                    <p class="text-xs text-[var(--text-secondary)]"><?= htmlspecialchars($app['course_name']) ?></p>
                                                    <p class="text-[10px] text-[var(--text-muted)] mt-2">Applied on <?= date('d M Y', strtotime($app['applied_at'])) ?></p>
                                                </div>
                                            </div>
                                            <div class="flex flex-row sm:flex-col items-center sm:items-end justify-between gap-2">
                                                <span class="px-3 py-1 text-[10px] font-bold uppercase tracking-wider rounded-md border <?= $color ?>">
                                                    <?= str_replace('_', ' ', $app['status']) ?>
                                                </span>
                                                <button class="text-xs text-[var(--text-secondary)] hover:text-white transition-colors flex items-center gap-1">
                                                    Track <i data-lucide="arrow-right" class="w-3 h-3"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Recommended Actions -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <a href="ai-counselor.php" class="p-5 rounded-2xl bg-gradient-to-br from-indigo-500/10 to-purple-500/10 border border-indigo-500/30 hover:border-indigo-500/60 transition-colors group">
                            <div class="w-10 h-10 rounded-full bg-indigo-500 text-white flex items-center justify-center mb-4 group-hover:scale-110 transition-transform shadow-lg shadow-indigo-500/30">
                                <i data-lucide="bot" class="w-5 h-5"></i>
                            </div>
                            <h3 class="font-bold text-white mb-1">Talk to AI Counselor</h3>
                            <p class="text-xs text-indigo-200/70">Get instant answers to your admission queries.</p>
                        </a>
                        
                        <div class="p-5 rounded-2xl glass border border-[var(--border)] hover:border-white/20 transition-colors cursor-pointer group">
                            <div class="w-10 h-10 rounded-full bg-[var(--bg-secondary)] text-[var(--text-secondary)] border border-[var(--border)] flex items-center justify-center mb-4 group-hover:text-white transition-colors">
                                <i data-lucide="upload-cloud" class="w-5 h-5"></i>
                            </div>
                            <h3 class="font-bold text-white mb-1">Upload Documents</h3>
                            <p class="text-xs text-[var(--text-secondary)]">Complete your document vault for 1-click apply.</p>
                        </div>
                    </div>

                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    
                    <!-- Profile Completion -->
                    <div class="glass p-6 rounded-2xl border border-[var(--border)]">
                        <h3 class="font-bold text-white mb-4 flex items-center gap-2">
                            <i data-lucide="check-circle" class="w-4 h-4 text-emerald-400"></i> Next Steps
                        </h3>
                        <ul class="space-y-4">
                            <li class="flex gap-3">
                                <i data-lucide="check-square" class="w-5 h-5 text-emerald-500 flex-shrink-0"></i>
                                <div>
                                    <p class="text-sm font-medium text-white line-through opacity-70">Verify Email Address</p>
                                </div>
                            </li>
                            <li class="flex gap-3">
                                <i data-lucide="check-square" class="w-5 h-5 text-emerald-500 flex-shrink-0"></i>
                                <div>
                                    <p class="text-sm font-medium text-white line-through opacity-70">Add Academic Details</p>
                                </div>
                            </li>
                            <li class="flex gap-3">
                                <i data-lucide="square" class="w-5 h-5 text-[var(--text-muted)] flex-shrink-0"></i>
                                <div>
                                    <p class="text-sm font-medium text-indigo-400 cursor-pointer hover:underline">Upload 12th Marksheet</p>
                                    <p class="text-xs text-[var(--text-secondary)] mt-0.5">Required for college applications</p>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <!-- Important Dates -->
                    <div class="glass p-6 rounded-2xl border border-[var(--border)]">
                        <h3 class="font-bold text-white mb-4 flex items-center gap-2">
                            <i data-lucide="calendar-clock" class="w-4 h-4 text-amber-400"></i> Upcoming Deadlines
                        </h3>
                        <div class="space-y-4">
                            <div class="flex gap-4">
                                <div class="w-12 h-12 rounded-lg bg-[var(--bg-secondary)] border border-[var(--border)] flex flex-col items-center justify-center flex-shrink-0 shadow-inner">
                                    <span class="text-[10px] text-[var(--text-muted)] font-bold uppercase">Jul</span>
                                    <span class="text-sm font-bold text-white leading-none mt-1">15</span>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-white">JEE Main Session 2 Result</p>
                                    <p class="text-xs text-[var(--text-secondary)] mt-0.5">Check official portal</p>
                                </div>
                            </div>
                            <div class="flex gap-4">
                                <div class="w-12 h-12 rounded-lg bg-[var(--bg-secondary)] border border-[var(--border)] flex flex-col items-center justify-center flex-shrink-0 shadow-inner">
                                    <span class="text-[10px] text-red-400 font-bold uppercase">Jul</span>
                                    <span class="text-sm font-bold text-white leading-none mt-1">20</span>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-white">VITEEE Application Close</p>
                                    <p class="text-xs text-red-400 mt-0.5">5 days remaining</p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </main>
</div>

<script>
    lucide.createIcons();
</script>
</body>
</html>
