<?php $page_title = "Privacy Policy | AdmissionSeason"; $updated = "15 May 2026"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    <meta name="description" content="Read AdmissionSeason's privacy policy to understand how we collect, use, and protect your personal information.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <style type="text/tailwindcss">
        <?php include 'assets/css/style.css'; ?>
        
        .privacy-content h2 { @apply text-2xl font-extrabold text-slate-900 mt-12 mb-4 scroll-mt-24 flex items-center gap-3; }
        .privacy-content h3 { @apply text-lg font-bold text-slate-800 mt-8 mb-3; }
        .privacy-content p  { @apply text-slate-600 leading-relaxed mb-5 text-base; }
        .privacy-content ul { @apply space-y-3 text-slate-600 text-base mb-6; }
        .privacy-content li { @apply flex items-start gap-3; }
        .privacy-content li::before { 
            content: '✓'; 
            @apply text-indigo-600 font-bold mt-0.5 flex-shrink-0; 
        }
        .privacy-content strong { @apply text-slate-900 font-semibold; }
        .privacy-content a { @apply text-indigo-600 font-medium hover:underline decoration-2 underline-offset-4; }
        
        .toc-link { @apply block py-2 text-sm text-slate-500 hover:text-indigo-600 hover:translate-x-1 transition-all border-l-2 border-transparent pl-4; }
        .toc-link.active { @apply text-indigo-600 border-indigo-600 font-semibold bg-indigo-50/50; }
    </style>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="antialiased bg-[var(--bg-primary)] text-slate-900 font-['Inter']">
<?php include 'includes/navbar.php'; ?>

<div class="min-h-screen pt-16">
    <!-- Hero Header -->
    <div class="bg-white border-b border-slate-200 py-16 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-1/3 h-full bg-gradient-to-l from-indigo-50/50 to-transparent pointer-events-none"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 relative z-10">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 rounded-2xl bg-indigo-600 flex items-center justify-center shadow-xl shadow-indigo-200">
                    <i data-lucide="shield-check" class="w-6 h-6 text-white"></i>
                </div>
                <div>
                    <span class="text-xs font-bold uppercase tracking-widest text-indigo-600">Legal Center</span>
                    <h1 class="text-4xl sm:text-5xl font-black text-slate-900 tracking-tight mt-1">Privacy Policy</h1>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row sm:items-center gap-4 mt-6">
                <p class="text-slate-500 text-sm flex items-center gap-2">
                    <i data-lucide="calendar" class="w-4 h-4"></i> Effective Date: <?= $updated ?>
                </p>
                <span class="hidden sm:inline text-slate-300">•</span>
                <p class="text-slate-500 text-sm flex items-center gap-2">
                    <i data-lucide="clock" class="w-4 h-4"></i> Est. Reading Time: 8 mins
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
                        <h4 class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-4 px-4">Navigation</h4>
                        <nav class="space-y-1">
                            <a href="#intro" class="toc-link active">Introduction</a>
                            <a href="#collection" class="toc-link">1. Data Collection</a>
                            <a href="#usage" class="toc-link">2. How We Use Data</a>
                            <a href="#sharing" class="toc-link">3. Sharing Policy</a>
                            <a href="#security" class="toc-link">4. Security</a>
                            <a href="#rights" class="toc-link">5. Your Rights</a>
                            <a href="#contact" class="toc-link">6. Contact</a>
                        </nav>
                    </div>
                    
                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200">
                        <h5 class="font-bold text-slate-900 mb-2 flex items-center gap-2">
                            <i data-lucide="help-circle" class="w-4 h-4 text-indigo-600"></i>
                            Questions?
                        </h5>
                        <p class="text-xs text-slate-500 leading-relaxed mb-4">
                            We're here to help you understand your data rights.
                        </p>
                        <a href="mailto:privacy@admissionseason.in" class="text-xs font-bold text-indigo-600 hover:underline">Email Support →</a>
                    </div>
                </div>
            </aside>

            <!-- Main Content Area -->
            <main class="flex-1 max-w-3xl privacy-content">
                
                <section id="intro" class="mb-12">
                    <p class="text-lg text-slate-700 font-medium leading-relaxed">
                        At <strong class="text-indigo-600">AdmissionSeason</strong>, your trust is our most valuable asset. We are committed to being transparent about how we collect, use, and protect your information as you navigate your educational journey with us.
                    </p>
                    <p>
                        This policy applies to all services provided via <a href="index.php">admissionseason.in</a> and our related mobile applications and AI counseling tools.
                    </p>
                </section>

                <hr class="border-slate-100 my-12">

                <section id="collection">
                    <h2 id="collection"><i data-lucide="database" class="w-6 h-6 text-indigo-600"></i> 1. Information We Collect</h2>
                    <p>To provide personalized college recommendations, we collect data in two primary ways:</p>
                    
                    <h3>1.1 Information You Provide Directly</h3>
                    <ul>
                        <li><strong>Identity Data:</strong> Full name, email address, and verified phone number.</li>
                        <li><strong>Academic Profile:</strong> Class 10/12 results, entrance exam scores (JEE, NEET, CAT), and target streams.</li>
                        <li><strong>Preferences:</strong> Preferred cities, budget constraints, and specific college interests.</li>
                        <li><strong>Verification Docs:</strong> Optional upload of marksheets for fast-track applications.</li>
                    </ul>

                    <h3>1.2 Automatically Collected Data</h3>
                    <ul>
                        <li><strong>Device Info:</strong> IP address, browser type, and operating system.</li>
                        <li><strong>Usage Patterns:</strong> Colleges viewed, time spent on pages, and search history.</li>
                        <li><strong>Cookies:</strong> Standard tracking to keep you logged in and remember your filters.</li>
                    </ul>
                </section>

                <section id="usage">
                    <h2><i data-lucide="activity" class="w-6 h-6 text-indigo-600"></i> 2. How We Use Your Information</h2>
                    <p>We use the data we collect to create a hyper-personalized experience for every student:</p>
                    <ul>
                        <li>Powering the <strong>AI Counselor</strong> to match you with top-fit institutions.</li>
                        <li>Sending real-time <strong>Admission Alerts</strong> and exam deadline reminders.</li>
                        <li>Calculating your <strong>Admission Probability</strong> using historical cutoff data.</li>
                        <li>Improving our platform security and preventing fraudulent account creation.</li>
                        <li>With your explicit permission, sharing your profile with colleges you choose to apply to.</li>
                    </ul>
                </section>

                <section id="sharing">
                    <h2><i data-lucide="share-2" class="w-6 h-6 text-indigo-600"></i> 3. Sharing Your Information</h2>
                    <p>We follow a <strong>"No Sale"</strong> policy. Your personal data is never sold to third-party marketing agencies. We only share data with:</p>
                    <ul>
                        <li><strong>Partner Institutions:</strong> Only when you click "Apply" or "Request Callback" for a specific college.</li>
                        <li><strong>Technology Partners:</strong> Trusted vendors (AWS, Twilio, SendGrid) who help us operate our platform under strict privacy contracts.</li>
                        <li><strong>Legal Compliance:</strong> When required by Indian law or to protect our users' safety.</li>
                    </ul>
                </section>

                <section id="security">
                    <h2><i data-lucide="lock" class="w-6 h-6 text-indigo-600"></i> 4. Data Storage & Security</h2>
                    <p>
                        Your data is stored in ISO-certified data centers located in <strong>India</strong>. We employ military-grade encryption (AES-256) for data at rest and TLS 1.3 for all data in transit. 
                    </p>
                    <p>
                        Our engineering team conducts weekly security audits and penetration tests to ensure your academic and personal records remain private and secure.
                    </p>
                </section>

                <section id="rights">
                    <h2><i data-lucide="user-check" class="w-6 h-6 text-indigo-600"></i> 5. Your Data Rights</h2>
                    <p>As a user, you have full control over your information:</p>
                    <ul>
                        <li><strong>Access & Portability:</strong> Request a full export of all data we hold about you.</li>
                        <li><strong>Correction:</strong> Instantly update your scores or preferences via your Dashboard.</li>
                        <li><strong>The Right to be Forgotten:</strong> You can request full deletion of your account and all associated data at any time.</li>
                        <li><strong>Communication Control:</strong> Opt-out of SMS or Email alerts with a single click.</li>
                    </ul>
                </section>

                <section id="contact">
                    <h2><i data-lucide="mail" class="w-6 h-6 text-indigo-600"></i> 6. Contact Our Privacy Team</h2>
                    <p>If you have questions about this policy or want to exercise your data rights, please contact our Data Protection Officer:</p>
                    
                    <div class="bg-slate-50 border border-slate-200 rounded-3xl p-8 mt-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <h4 class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-2">Email Address</h4>
                                <a href="mailto:privacy@admissionseason.in" class="text-lg font-bold text-slate-900 hover:text-indigo-600">privacy@admissionseason.in</a>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-2">Corporate Office</h4>
                                <p class="text-slate-600 font-medium">AdmissionSeason Pvt. Ltd.<br>Level 4, Statesman House<br>New Delhi, India — 110001</p>
                            </div>
                        </div>
                    </div>
                </section>

                <div class="mt-16 pt-12 border-t border-slate-100 flex flex-wrap gap-4">
                    <a href="terms.php" class="bg-white border border-slate-200 px-6 py-3 rounded-xl text-sm font-bold text-slate-700 hover:bg-slate-50 transition-colors flex items-center gap-2">
                        <i data-lucide="file-text" class="w-4 h-4 text-indigo-600"></i> Read Terms of Service
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
