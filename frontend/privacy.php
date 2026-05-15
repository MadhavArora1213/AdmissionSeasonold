<?php $page_title = "Privacy Policy | AdmissionSeason"; $updated = "15 May 2026"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    <meta name="description" content="Read AdmissionSeason's privacy policy to understand how we collect, use, and protect your personal information.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <style type="text/tailwindcss">
        <?php include 'assets/css/style.css'; ?>
        .prose h2 { @apply text-xl font-bold text-white mt-8 mb-3; }
        .prose h3 { @apply text-base font-semibold text-white mt-5 mb-2; }
        .prose p  { @apply text-[var(--text-secondary)] leading-relaxed mb-4 text-sm; }
        .prose ul { @apply list-disc list-inside space-y-1.5 text-[var(--text-secondary)] text-sm mb-4 ml-2; }
        .prose a  { @apply text-indigo-400 hover:underline; }
    </style>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="antialiased bg-[var(--bg-primary)] text-white font-['Inter']">
<?php include 'includes/navbar.php'; ?>

<div class="min-h-screen pt-16">
    <div class="bg-gradient-to-b from-[var(--bg-secondary)] to-[var(--bg-primary)] border-b border-[var(--border)] py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-indigo-500/20 border border-indigo-500/30 flex items-center justify-center">
                    <i data-lucide="shield" class="w-5 h-5 text-indigo-400"></i>
                </div>
                <span class="text-xs font-bold uppercase tracking-widest text-indigo-400">Legal</span>
            </div>
            <h1 class="text-3xl sm:text-4xl font-bold text-white mb-2">Privacy Policy</h1>
            <p class="text-[var(--text-muted)] text-sm">Last updated: <?= $updated ?></p>
        </div>
    </div>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 py-12">
        <div class="glass rounded-2xl border border-[var(--border)] p-8 prose">

            <p>Welcome to <strong class="text-white">AdmissionSeason</strong> ("we", "us", or "our"). This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you visit our website at <a href="index.php">admissionseason.in</a>.</p>

            <h2>1. Information We Collect</h2>
            <h3>1.1 Personal Information</h3>
            <ul>
                <li>Name, email address, phone number</li>
                <li>Academic details (class 10/12 marks, exam scores, stream)</li>
                <li>City, state, and budget preferences</li>
                <li>Documents you upload (marksheets, certificates)</li>
            </ul>
            <h3>1.2 Automatically Collected</h3>
            <ul>
                <li>Device information, browser type, IP address</li>
                <li>Pages visited, time spent, click patterns</li>
                <li>Search queries and filters used on our platform</li>
            </ul>

            <h2>2. How We Use Your Information</h2>
            <ul>
                <li>To match you with relevant colleges, courses, and scholarships</li>
                <li>To power our AI Counselor with personalized recommendations</li>
                <li>To send you exam date alerts and deadline reminders</li>
                <li>To improve our platform features and user experience</li>
                <li>To share (with your consent) with colleges you've expressed interest in</li>
                <li>To process applications submitted through our platform</li>
            </ul>

            <h2>3. Sharing Your Information</h2>
            <p>We <strong class="text-white">do not sell</strong> your personal data. We may share data with:</p>
            <ul>
                <li><strong class="text-white">Partner Colleges</strong> — only when you click "Apply Now" or express interest</li>
                <li><strong class="text-white">Service Providers</strong> — hosting, email, SMS vendors under strict NDAs</li>
                <li><strong class="text-white">Legal Authorities</strong> — if required by law or court order</li>
            </ul>

            <h2>4. Data Storage & Security</h2>
            <p>Your data is stored on secure servers in India. We use industry-standard encryption (TLS 1.3), hashed passwords, and regular security audits. While we implement strong safeguards, no method of internet transmission is 100% secure.</p>

            <h2>5. Cookies</h2>
            <p>We use cookies to maintain your session, remember preferences, and analyze traffic. You can disable cookies in your browser settings, but some features may not work properly.</p>

            <h2>6. Your Rights</h2>
            <ul>
                <li><strong class="text-white">Access</strong> — Request a copy of your personal data</li>
                <li><strong class="text-white">Correction</strong> — Update incorrect information via your dashboard</li>
                <li><strong class="text-white">Deletion</strong> — Request account and data deletion</li>
                <li><strong class="text-white">Opt-out</strong> — Unsubscribe from marketing emails at any time</li>
                <li><strong class="text-white">Portability</strong> — Request your data in a machine-readable format</li>
            </ul>

            <h2>7. Children's Privacy</h2>
            <p>Our platform is intended for users aged 16 and above. We do not knowingly collect data from children under 13. If you believe a child has provided us personal information, contact us immediately.</p>

            <h2>8. Third-Party Links</h2>
            <p>Our platform contains links to external college websites and portals. We are not responsible for the privacy practices of those websites. We encourage you to review their privacy policies.</p>

            <h2>9. Changes to This Policy</h2>
            <p>We may update this Privacy Policy from time to time. We will notify you of significant changes via email or a prominent notice on our platform. Continued use after changes means you accept the updated policy.</p>

            <h2>10. Contact Us</h2>
            <p>For privacy concerns, data requests, or any questions:</p>
            <ul>
                <li>Email: <a href="mailto:privacy@admissionseason.in">privacy@admissionseason.in</a></li>
                <li>Phone: +91 98765 43210</li>
                <li>Address: AdmissionSeason Pvt. Ltd., New Delhi, India — 110001</li>
            </ul>
        </div>

        <div class="mt-6 flex gap-4">
            <a href="terms.php" class="glass rounded-xl border border-[var(--border)] px-5 py-3 text-sm font-medium hover:bg-white/5 transition-colors flex items-center gap-2">
                <i data-lucide="file-text" class="w-4 h-4 text-indigo-400"></i> Terms of Service
            </a>
            <a href="index.php" class="glass rounded-xl border border-[var(--border)] px-5 py-3 text-sm font-medium hover:bg-white/5 transition-colors flex items-center gap-2">
                <i data-lucide="home" class="w-4 h-4"></i> Back to Home
            </a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
<script>lucide.createIcons();</script>
</body>
</html>
