<?php $page_title = "Terms of Service | AdmissionSeason"; $updated = "15 May 2026"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    <meta name="description" content="Read AdmissionSeason's terms of service — rules, rights, and responsibilities for using our platform.">
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
                <div class="w-10 h-10 rounded-xl bg-amber-500/20 border border-amber-500/30 flex items-center justify-center">
                    <i data-lucide="file-text" class="w-5 h-5 text-amber-400"></i>
                </div>
                <span class="text-xs font-bold uppercase tracking-widest text-amber-400">Legal</span>
            </div>
            <h1 class="text-3xl sm:text-4xl font-bold text-white mb-2">Terms of Service</h1>
            <p class="text-[var(--text-muted)] text-sm">Last updated: <?= $updated ?></p>
        </div>
    </div>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 py-12">
        <div class="glass rounded-2xl border border-[var(--border)] p-8 prose">

            <p>Please read these Terms of Service ("Terms") carefully before using the AdmissionSeason platform. By accessing or using our service, you agree to be bound by these Terms.</p>

            <h2>1. Acceptance of Terms</h2>
            <p>By creating an account or using any part of AdmissionSeason, you confirm that you are at least 16 years old and agree to these Terms and our <a href="privacy.php">Privacy Policy</a>. If you disagree with any part, please discontinue use immediately.</p>

            <h2>2. Description of Service</h2>
            <p>AdmissionSeason provides an online platform that helps students in India:</p>
            <ul>
                <li>Discover and compare colleges and courses</li>
                <li>Access exam information, dates, and syllabus</li>
                <li>Find scholarships and financial aid</li>
                <li>Get AI-powered counseling and guidance</li>
                <li>Submit applications to partner institutions</li>
                <li>Calculate rank predictions and ROI for education</li>
            </ul>

            <h2>3. User Accounts</h2>
            <h3>3.1 Registration</h3>
            <p>To access certain features, you must register with accurate, current information. You are responsible for maintaining the confidentiality of your credentials and all activities under your account.</p>
            <h3>3.2 Account Termination</h3>
            <p>We reserve the right to suspend or terminate accounts that violate these Terms, provide false information, or engage in fraudulent activity.</p>

            <h2>4. Acceptable Use</h2>
            <p>You agree NOT to:</p>
            <ul>
                <li>Provide false academic credentials or misleading information</li>
                <li>Spam, harass, or abuse other users or college representatives</li>
                <li>Attempt to scrape, reverse-engineer, or copy our platform data</li>
                <li>Use automated bots or scripts to access our services</li>
                <li>Post reviews that are fake, paid, or defamatory</li>
                <li>Violate any applicable Indian or international laws</li>
            </ul>

            <h2>5. College Information & Accuracy</h2>
            <p>While we strive to keep college details, fees, seat counts, and exam dates accurate, this information may change. <strong class="text-white">Always verify critical information directly with the institution before making decisions.</strong> AdmissionSeason is not liable for decisions based on outdated or incorrect data.</p>

            <h2>6. Applications & Admissions</h2>
            <p>Submitting an application through AdmissionSeason does not guarantee admission. Final admission decisions rest solely with the respective institution. We act as a facilitator and are not a party to any agreement between you and a college.</p>

            <h2>7. AI Counselor Disclaimer</h2>
            <p>Our AI Counselor provides guidance based on publicly available data and algorithms. It does not replace professional counseling. Recommendations are for informational purposes only and should not be the sole basis for major educational or financial decisions.</p>

            <h2>8. Intellectual Property</h2>
            <p>All content on AdmissionSeason — including text, design, logos, code, and data — is the property of AdmissionSeason Pvt. Ltd. and protected by copyright laws. You may not reproduce or distribute our content without written permission.</p>

            <h2>9. Limitation of Liability</h2>
            <p>AdmissionSeason shall not be liable for any indirect, incidental, or consequential damages arising from your use of the platform, including but not limited to loss of data, missed deadlines, or admission rejections. Our maximum liability to you shall not exceed the amount you paid us in the 12 months prior to the claim.</p>

            <h2>10. Governing Law</h2>
            <p>These Terms are governed by the laws of India. Any disputes shall be subject to the exclusive jurisdiction of courts in New Delhi, India.</p>

            <h2>11. Changes to Terms</h2>
            <p>We may update these Terms at any time. We will notify you of significant changes via email or platform notification. Continued use of the platform after changes constitutes acceptance of the updated Terms.</p>

            <h2>12. Contact</h2>
            <p>For legal inquiries or Terms-related questions:</p>
            <ul>
                <li>Email: <a href="mailto:legal@admissionseason.in">legal@admissionseason.in</a></li>
                <li>Phone: +91 98765 43210</li>
                <li>Address: AdmissionSeason Pvt. Ltd., New Delhi, India — 110001</li>
            </ul>
        </div>

        <div class="mt-6 flex gap-4">
            <a href="privacy.php" class="glass rounded-xl border border-[var(--border)] px-5 py-3 text-sm font-medium hover:bg-white/5 transition-colors flex items-center gap-2">
                <i data-lucide="shield" class="w-4 h-4 text-indigo-400"></i> Privacy Policy
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
