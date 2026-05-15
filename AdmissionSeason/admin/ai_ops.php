<?php
require_once 'includes/db.php';

$view = $_GET['view'] ?? 'models';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Operations Panel | EduSearch Admin</title>
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .ai-tabs { display: flex; gap: 1rem; border-bottom: 1px solid var(--border-color); margin-bottom: 2rem; overflow-x: auto; padding-bottom: 5px; }
        .ai-tab { padding: 10px 20px; cursor: pointer; color: var(--text-secondary); border-bottom: 2px solid transparent; white-space: nowrap; }
        .ai-tab.active { color: var(--accent-primary); border-bottom-color: var(--accent-primary); font-weight: 700; }
        
        .ai-card { background: rgba(30, 41, 59, 0.4); border: 1px solid var(--border-color); padding: 1.5rem; border-radius: 12px; margin-bottom: 1rem; position: relative; overflow: hidden; }
        .ai-card::before { content: ''; position: absolute; top: 0; left: 0; width: 4px; height: 100%; background: var(--accent-primary); }
        .ai-card.offline::before { background: var(--danger); }
        
        .prompt-editor { font-family: 'Fira Code', monospace; background: #0f172a; color: #cbd5e1; border: 1px solid var(--border-color); padding: 1rem; border-radius: 8px; width: 100%; min-height: 300px; resize: vertical; line-height: 1.6; font-size: 0.85rem; }
        .diff-panel { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 1rem; }
        .diff-side { font-size: 0.75rem; background: rgba(0,0,0,0.2); padding: 10px; border-radius: 4px; overflow-x: auto; }
        
        .hit-gauge { width: 100%; height: 10px; background: rgba(255,255,255,0.05); border-radius: 5px; margin: 10px 0; overflow: hidden; }
        .hit-fill { height: 100%; background: var(--accent-primary); box-shadow: 0 0 10px var(--accent-primary); transition: 0.5s; }
    </style>
</head>
<body>
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="main-content">
            <?php include 'includes/header.php'; ?>
            
            <div class="content-area">
                <div class="page-header">
                    <div>
                        <h1 class="page-title">AI Operations & Intelligence</h1>
                        <p class="page-subtitle">Managing self-hosted Ollama models, counselor quality signals, and inference caching.</p>
                    </div>
                </div>

                <div class="ai-tabs">
                    <a href="?view=models" class="ai-tab <?php echo $view == 'models' ? 'active' : ''; ?>">Ollama Models</a>
                    <a href="?view=quality" class="ai-tab <?php echo $view == 'quality' ? 'active' : ''; ?>">Counselor Quality</a>
                    <a href="?view=prompts" class="ai-tab <?php echo $view == 'prompts' ? 'active' : ''; ?>">System Prompts</a>
                    <a href="?view=cache" class="ai-tab <?php echo $view == 'cache' ? 'active' : ''; ?>">Response Cache</a>
                </div>

                <?php if ($view == 'models'): ?>
                <!-- Screen 8.1.1 — Model Status & Control -->
                <div class="dashboard-grid">
                    <div class="widget w-two-thirds">
                        <div class="widget-header">
                            <h3 class="widget-title">Active Inference Status</h3>
                            <div style="display: flex; gap: 10px;">
                                <button class="btn btn-secondary" style="font-size: 0.75rem;" onclick="handleAIAction('unload_model', 'Llama 3.1 8B')">Unload Model</button>
                                <button class="btn btn-primary" style="font-size: 0.75rem;" onclick="handleAIAction('force_reload', 'Llama 3.1 8B')">Force Reload</button>
                            </div>
                        </div>
                        <div class="ai-card" style="margin-top: 1.5rem;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                <div>
                                    <div style="font-size: 0.75rem; color: var(--text-secondary);">Currently Loaded</div>
                                    <h2 style="color: white; margin: 5px 0;">Llama 3.1 8B (Q4_K_M)</h2>
                                    <div style="font-size: 0.8rem; color: var(--success);"><i class="fas fa-check-circle"></i> Running on CUDA (GPU Acceleration)</div>
                                </div>
                                <div style="text-align: right;">
                                    <div style="font-size: 0.75rem; color: var(--text-secondary);">RAM Consumption</div>
                                    <div style="font-size: 1.5rem; font-weight: 700; color: var(--accent-primary);">5.2 GB / 12 GB</div>
                                </div>
                            </div>
                            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 2rem; margin-top: 2rem; border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
                                <div>
                                    <div style="font-size: 0.7rem; color: var(--text-secondary);">Avg Response Time</div>
                                    <div style="font-size: 1.2rem; font-weight: 700;">8.4s</div>
                                </div>
                                <div>
                                    <div style="font-size: 0.7rem; color: var(--text-secondary);">Live Queue Depth</div>
                                    <div style="font-size: 1.2rem; font-weight: 700; color: var(--warning);">2 Requests</div>
                                </div>
                                <div>
                                    <div style="font-size: 0.7rem; color: var(--text-secondary);">Inference Errors (24h)</div>
                                    <div style="font-size: 1.2rem; font-weight: 700; color: var(--danger);">0.42%</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="widget w-third">
                        <h3 class="widget-title">Model Version Manager</h3>
                        <div style="margin-top: 1.5rem;">
                            <div style="background: rgba(0,0,0,0.2); padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                                <div style="font-weight: 700; font-size: 0.85rem;">Mistral-7B-Instruct</div>
                                <div style="font-size: 0.7rem; color: var(--text-secondary);">Downloaded: 12 May 2026 &bull; 4.1 GB</div>
                                <div style="display: flex; gap: 10px; margin-top: 10px;">
                                    <button class="btn" style="font-size: 0.65rem; background: var(--accent-primary);">A/B Test</button>
                                    <button class="btn" style="font-size: 0.65rem; background: var(--sidebar-bg); color: white; border: 1px solid var(--border-color);">Delete</button>
                                </div>
                            </div>
                            <div style="background: rgba(0,0,0,0.2); padding: 1rem; border-radius: 8px;">
                                <div style="font-weight: 700; font-size: 0.85rem;">EduCounselor-v1 (Fine-tuned)</div>
                                <div style="font-size: 0.7rem; color: var(--text-secondary);">Downloaded: 08 May 2026 &bull; 6.4 GB</div>
                                <span class="status-badge status-approved" style="font-size: 0.6rem; margin-top: 5px;">Shadow Testing</span>
                            </div>
                        </div>
                        <div style="margin-top: 2rem; font-size: 0.75rem; color: var(--text-secondary);">
                            <i class="fas fa-hdd"></i> Disk Usage: 42.4 GB / 100 GB
                        </div>
                    </div>
                </div>

                <?php elseif ($view == 'quality'): ?>
                <!-- Screen 8.2.1 — Counselor Performance Dashboard -->
                <div class="dashboard-grid">
                    <div class="widget w-full glass-card">
                        <div class="widget-header">
                            <h3 class="widget-title">Student Feedback Ratio (Thumbs Up/Down)</h3>
                        </div>
                        <canvas id="feedbackChart" height="80"></canvas>
                    </div>

                    <div class="widget w-two-thirds glass-card">
                        <h3 class="widget-title">Low-Quality Response Samples (Last 24h)</h3>
                        <div style="margin-top: 1.5rem;">
                            <div class="ai-card offline" style="padding: 1rem;">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
                                    <div style="font-weight: 700; font-size: 0.85rem;">Subject: IIT Delhi vs VIT Vellore</div>
                                    <span class="status-badge status-rejected">THUMBS DOWN</span>
                                </div>
                                <div style="font-size: 0.8rem; line-height: 1.5; color: var(--text-secondary);">
                                    <strong>Counselor:</strong> "IIT Delhi is in Mumbai. You should apply for VIT as it is closer to Delhi."
                                    <div style="color: var(--danger); font-weight: 700; margin-top: 5px;">Root Cause: Hallucinated Location</div>
                                </div>
                                <button class="btn btn-primary" style="font-size: 0.7rem; margin-top: 10px;" onclick="handleAIAction('view_context', 'IIT Delhi hallu')">View Full Context</button>
                            </div>
                        </div>
                    </div>

                    <div class="widget w-third glass-card">
                        <h3 class="widget-title">Failure Taxonomy</h3>
                        <div style="margin-top: 1.5rem;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 0.8rem;">
                                <span>Hallucinated Data</span>
                                <span style="font-weight: 700;">42%</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 0.8rem;">
                                <span>Ignored Preference</span>
                                <span style="font-weight: 700;">18%</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 0.8rem;">
                                <span>Wrong Fee Quote</span>
                                <span style="font-weight: 700;">12%</span>
                            </div>
                        </div>
                    </div>
                </div>

                <?php elseif ($view == 'prompts'): ?>
                <!-- Screen 8.2.2 — System Prompt Editor -->
                <div class="widget w-full glass-card">
                    <div class="widget-header">
                        <h3 class="widget-title">Active System Prompt (Counselor Persona)</h3>
                        <div style="display: flex; gap: 10px;">
                            <button class="btn btn-secondary" style="font-size: 0.75rem;" onclick="handleAIAction('shadow_test', 'v4.8')">Shadow Test (10% Traffic)</button>
                            <button class="btn btn-primary" style="font-size: 0.75rem;" onclick="handleAIAction('publish_prompt', 'v4.8')">Publish Version 4.8</button>
                        </div>
                    </div>
                    
                    <div style="margin-top: 1.5rem;">
                        <textarea class="prompt-editor">You are the EduSearch AI Counselor. Your goal is to recommend the best colleges in India based on student requirements.
    
- Use NIRF 2026 and NAAC data from the provided database context.
- Be precise about fees. If unknown, state 'Data under verification'.
- Never recommend more than 5 colleges in one response.
- Prioritize student city preferences.
    
Database Context: [COLLEGE_SEARCH_RESULTS]</textarea>
                    </div>

                    <div style="margin-top: 2rem;">
                        <h3 class="widget-title">Version History & Diff</h3>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Version</th>
                                    <th>Changed By</th>
                                    <th>Timestamp</th>
                                    <th>Quality Impact</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>v4.7 (Current)</strong></td>
                                    <td>Ankit V.</td>
                                    <td>12 May 2026, 14:00</td>
                                    <td><span style="color: var(--success);">+12% Upvotes</span></td>
                                    <td><button class="action-btn" title="View Diff"><i class="fas fa-columns"></i></button></td>
                                </tr>
                                <tr>
                                    <td>v4.6</td>
                                    <td>Ankit V.</td>
                                    <td>08 May 2026, 09:30</td>
                                    <td>-</td>
                                    <td><button class="btn" style="font-size: 0.6rem; background: var(--accent-primary);">Rollback</button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <?php elseif ($view == 'cache'): ?>
                <!-- Screen 8.2.3 — Redis Cache Analytics -->
                <div class="dashboard-grid">
                    <div class="widget w-third glass-card">
                        <h3 class="widget-title">Response Cache Hit Rate</h3>
                        <div style="text-align: center; margin-top: 2rem;">
                            <div style="font-size: 3rem; font-weight: 700; color: var(--accent-primary);">68%</div>
                            <div class="hit-gauge"><div class="hit-fill" style="width: 68%;"></div></div>
                            <p style="font-size: 0.75rem; color: var(--success); margin-top: 10px;"><i class="fas fa-check-circle"></i> Target Exceeded (> 60%)</p>
                        </div>
                        <div style="margin-top: 3rem; font-size: 0.75rem; color: var(--text-secondary);">
                            <i class="fas fa-memory"></i> Redis Usage: 1.2 GB / 4 GB
                        </div>
                    </div>

                    <div class="widget w-two-thirds glass-card">
                        <h3 class="widget-title">Top 20 Cached Query Patterns</h3>
                        <table class="data-table" style="margin-top: 1rem;">
                            <thead>
                                <tr>
                                    <th>Query Pattern (Regex)</th>
                                    <th>Cache Hits</th>
                                    <th>Last Served</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><code>/^engineering.*maharashtra.*fees$/i</code></td>
                                    <td><strong>4,240</strong></td>
                                    <td>Just now</td>
                                    <td><button class="action-btn" style="color: var(--danger);" onclick="handleAIAction('purge_pattern', 'engineering')"><i class="fas fa-trash"></i></button></td>
                                </tr>
                                <tr>
                                    <td><code>/^mba.*cat.*percentile.*pune$/i</code></td>
                                    <td><strong>1,822</strong></td>
                                    <td>2m ago</td>
                                    <td><button class="action-btn" onclick="handleAIAction('view_pattern', 'mba')"><i class="fas fa-eye"></i></button></td>
                                </tr>
                            </tbody>
                        </table>
                        <button class="btn btn-secondary" style="width: 100%; margin-top: 1.5rem; font-size: 0.8rem;" onclick="handleAIAction('purge_all_cache', 'all')">Purge Entire AI Cache Namespace</button>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </main>
    </div>

    <script>
        if (document.getElementById('feedbackChart')) {
            const ctx = document.getElementById('feedbackChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                    datasets: [
                        { label: 'Thumbs Up', data: [85, 88, 92, 84, 95, 98, 96], borderColor: '#10b981', backgroundColor: 'rgba(16, 185, 129, 0.1)', fill: true, tension: 0.4 },
                        { label: 'Thumbs Down', data: [15, 12, 8, 16, 5, 2, 4], borderColor: '#ef4444', backgroundColor: 'rgba(239, 68, 68, 0.1)', fill: true, tension: 0.4 }
                    ]
                },
                options: { responsive: true, plugins: { legend: { labels: { color: '#94a3b8' } } }, scales: { y: { beginAtZero: true, max: 100 } } }
            });
        }
    </script>
    <script>
        async function handleAIAction(action, target) {
            Swal.fire({
                title: 'AI Orchestration',
                text: 'Interfacing with Ollama & Vector Store...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            try {
                const response = await fetch('admin_api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action, target, module: 'ai_ops' })
                });
                const result = await response.json();
                
                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: result.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: result.message });
                }
            } catch (error) {
                console.error("AI API Error:", error);
                Swal.fire({ icon: 'error', title: 'Connection Failure', text: 'AI Inference server or Admin API is unreachable.' });
            }
        }
    </script>
</body>
</html>
