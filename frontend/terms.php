<?php $page_title = "Terms of Service | AdmissionSeason"; $updated = "15 May 2026"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    <meta name="description" content="Read AdmissionSeason's terms of service — rules, rights, and responsibilities for using our platform.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <style type="text/tailwindcss">
        <?php include 'assets/css/style.css'; ?>
        
        .terms-content h2 { @apply text-2xl font-extrabold text-slate-900 mt-12 mb-4 scroll-mt-24 flex items-center gap-3; }
        .terms-content h3 { @apply text-lg font-bold text-slate-800 mt-8 mb-3; }
        .terms-content p  { @apply text-slate-600 leading-relaxed mb-5 text-base; }
        .terms-content ul { @apply space-y-3 text-slate-600 text-base mb-6; }
        .terms-content li { @apply flex items-start gap-3; }
        .terms-content li::before { 
            content: '→'; 
            @apply text-amber-500 font-bold mt-0.5 flex-shrink-0; 
        }
        .terms-content strong { @apply text-slate-900 font-semibold; }
        .terms-content a { @apply text-amber-600 font-medium hover:underline decoration-2 underline-offset-4; }
        
        .toc-link { @apply block py-2 text-sm text-slate-500 hover:text-amber-600 hover:translate-x-1 transition-all border-l-2 border-transparent pl-4; }
        .toc-link.active { @apply text-amber-600 border-amber-500 font-semibold bg-amber-50/50; }

        .warning-box { @apply p-6 bg-amber-50 border border-amber-200 rounded-2xl text-amber-800 text-sm leading-relaxed mb-8; }
    </style>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="antialiased bg-[var(--bg-primary)] text-slate-900 font-['Inter']">
<?php include 'includes/navbar.php'; ?>

<div class="min-h-screen pt-16">
    <!-- Hero Header -->
    <div class="bg-white border-b border-slate-200 py-16 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-1/3 h-full bg-gradient-to-l from-amber-50/50 to-transparent pointer-events-none"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 relative z-10">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 rounded-2xl bg-amber-500 flex items-center justify-center shadow-xl shadow-amber-100">
                    <i data-lucide="file-text" class="w-6 h-6 text-white"></i>
                </div>
                <div>
                    <span class="text-xs font-bold uppercase tracking-widest text-amber-600">User Agreement</span>
                    <h1 class="text-4xl sm:text-5xl font-black text-slate-900 tracking-tight mt-1">Terms of Service</h1>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row sm:items-center gap-4 mt-6">
                <p class="text-slate-500 text-sm flex items-center gap-2">
                    <i data-lucide="refresh-cw" class="w-4 h-4"></i> Last Revised: <?= $updated ?>
                </p>
                <span class="hidden sm:inline text-slate-300">•</span>
                <p class="text-slate-500 text-sm flex items-center gap-2">
                    <i data-lucide="eye" class="w-4 h-4"></i> Public Transparency
                </p>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-16">
        <div class="flex flex-col lg:flex-row gap-12">
            
            <!-- Table of Contents Sidebar -->
            <aside class="lg:w-64 flex-shrink-0">
                <div class="sticky top-24 space-y-8">
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-4 px-4">Sections</h4>
                        <nav class="space-y-1">
                            <a href="#intro" class="toc-link active">Acceptance</a>
                            <a href="#services" class="toc-link">1. Services</a>
                            <a href="#accounts" class="toc-link">2. User Accounts</a>
                            <a href="#conduct" class="toc-link">3. User Conduct</a>
                            <a href="#accuracy" class="toc-link">4. Data Accuracy</a>
                            <a href="#liability" class="toc-link">5. Liability</a>
                            <a href="#legal" class="toc-link">6. Legal</a>
                        </nav>
                    </div>
                    
                    <div class="p-6 rounded-2xl bg-amber-50/30 border border-amber-100">
                        <h5 class="font-bold text-slate-900 mb-2 flex items-center gap-2 text-sm">
                            <i data-lucide="alert-triangle" class="w-4 h-4 text-amber-600"></i>
                            Key Point
                        </h5>
                        <p class="text-xs text-slate-600 leading-relaxed mb-4">
                            By using this site, you agree to these rules. Please read carefully.
                        </p>
                        <a href="privacy.php" class="text-xs font-bold text-amber-700 hover:underline">View Privacy Policy →</a>
                    </div>
                </div>
            </aside>

            <!-- Main Content Area -->
            <main class="flex-1 max-w-3xl terms-content">
                
                <section id="intro" class="mb-12">
                    <div class="warning-box">
                        <strong>Important:</strong> These Terms of Service constitute a legally binding agreement between you and AdmissionSeason Pvt. Ltd. If you do not agree to these terms, you must not access or use our services.
                    </div>
                    <p class="text-lg text-slate-700 font-medium leading-relaxed">
                        Welcome to AdmissionSeason. These terms govern your use of our website, AI counseling tools, and rank prediction services.
                    </p>
                </section>

                <hr class="border-slate-100 my-12">

                <section id="services">
                    <h2><i data-lucide="layers" class="w-6 h-6 text-amber-500"></i> 1. Scope of Services</h2>
                    <p>AdmissionSeason provides a comprehensive digital ecosystem for higher education discovery in India:</p>
                    <ul>
                        <li>AI-powered college matching and career counseling.</li>
                        <li>Exam information, cutoff analysis, and rank prediction.</li>
                        <li>Direct application processing for partner universities.</li>
                        <li>Scholarship discovery and ROI calculators.</li>
                    </ul>
                </section>

                <section id="accounts">
                    <h2><i data-lucide="user-plus" class="w-6 h-6 text-amber-500"></i> 2. User Accounts</h2>
                    <h3>2.1 Eligibility</h3>
                    <p>You must be at least 16 years of age to create an account. Users under 16 must have explicit parental consent.</p>
                    <h3>2.2 Responsibility</h3>
                    <p>You are responsible for safeguarding your login credentials. Any activity occurring under your account is your legal responsibility. If you suspect a breach, notify us immediately.</p>
                </section>

                <section id="conduct">
                    <h2><i data-lucide="shield-alert" class="w-6 h-6 text-amber-500"></i> 3. Acceptable Use & Conduct</h2>
                    <p>To maintain a high-quality community, you agree NOT to:</p>
                    <ul>
                        <li>Submit fraudulent academic scores or forged documents.</li>
                        <li>Use automated tools to scrape college data or search results.</li>
                        <li>Engage in any activity that interferes with platform performance.</li>
                        <li>Post defamatory or commercially biased reviews.</li>
                        <li>Impersonate college officials or AdmissionSeason representatives.</li>
                    </ul>
                </section>

                <section id="accuracy">
                    <h2><i data-lucide="check-circle-2" class="w-6 h-6 text-amber-500"></i> 4. Information Accuracy Disclaimer</h2>
                    <p>
                        While we use advanced algorithms and official sources, <strong>college fees, admission dates, and seat availability</strong> are subject to change by the respective institutions. 
                    </p>
                    <p>
                        AdmissionSeason provides information on an "as-is" basis. We strongly recommend students verify critical admission details directly with the university's official website before making financial commitments.
                    </p>
                </section>

                <section id="liability">
                    <h2><i data-lucide="scale" class="w-6 h-6 text-amber-500"></i> 5. Limitation of Liability</h2>
                    <p>
                        AdmissionSeason shall not be liable for any missed admission deadlines, application rejections, or loss of data. Our role is limited to being an information facilitator.
                    </p>
                    <p>
                        Our aggregate liability for any claim arising out of these terms shall be limited to the total amount paid by you to AdmissionSeason (if any) during the preceding 6 months.
                    </p>
                </section>

                <section id="legal">
                    <h2><i data-lucide="gavel" class="w-6 h-6 text-amber-500"></i> 6. Governing Law & Jurisdiction</h2>
                    <p>
                        These Terms are governed by the laws of India. Any dispute arising out of your use of the platform shall be subject to the exclusive jurisdiction of the courts in <strong>New Delhi, India</strong>.
                    </p>
                </section>

                <div class="mt-16 pt-12 border-t border-slate-100 flex flex-wrap gap-4">
                    <a href="privacy.php" class="bg-white border border-slate-200 px-6 py-3 rounded-xl text-sm font-bold text-slate-700 hover:bg-slate-50 transition-colors flex items-center gap-2">
                        <i data-lucide="shield" class="w-4 h-4 text-amber-600"></i> Read Privacy Policy
                    </a>
                    <a href="index.php" class="btn-primary px-8 py-3.5 rounded-xl text-sm font-bold text-white flex items-center gap-2">
                        <i data-lucide="home" class="w-4 h-4"></i> Back to Homepage
                    </a>
                </div>
            </main>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
<script>
    lucide.createIcons();
    
    // Simple TOC scroll spy
    window.addEventListener('scroll', () => {
        let current = '';
        const sections = document.querySelectorAll('section');
        const navLinks = document.querySelectorAll('.toc-link');
        
        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            if (pageYOffset >= sectionTop - 120) {
                current = section.getAttribute('id');
            }
        });

        navLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href') === '#' + current) {
                link.classList.add('active');
            }
        });
    });
</script>
</body>
</html>
