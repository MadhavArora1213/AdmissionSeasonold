<?php
require_once 'includes/db.php';

$view = $_GET['view'] ?? 'dashboards';
$dashboard_id = $_GET['db'] ?? 1;

$dashboards = [
    1 => "VPS System Health",
    2 => "PostgreSQL Performance",
    3 => "Redis Cache Performance",
    4 => "Ollama AI Inference",
    5 => "MeiliSearch Engine",
    6 => "Nginx & Application",
    7 => "BullMQ Job Queue"
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Infrastructure Observability | EduSearch Admin</title>
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .infra-tabs { display: flex; gap: 1rem; border-bottom: 1px solid var(--border-color); margin-bottom: 2rem; }
        .inf-tab { padding: 10px 20px; cursor: pointer; color: var(--text-secondary); border-bottom: 2px solid transparent; }
        .inf-tab.active { color: var(--accent-primary); border-bottom-color: var(--accent-primary); font-weight: 700; }
        
        .db-selector { display: flex; flex-direction: column; gap: 5px; }
        .db-item { padding: 12px 15px; background: rgba(255,255,255,0.03); border-radius: 8px; cursor: pointer; border: 1px solid transparent; transition: 0.2s; }
        .db-item:hover { background: rgba(255,255,255,0.05); }
        .db-item.active { background: rgba(99, 102, 241, 0.1); border-color: var(--accent-primary); }
        
        .grafana-embed { width: 100%; height: 600px; border: 1px solid var(--border-color); border-radius: 12px; background: #111; display: flex; align-items: center; justify-content: center; }
        .sentry-group { background: rgba(0,0,0,0.2); padding: 1.2rem; border-radius: 12px; border-left: 4px solid var(--danger); margin-bottom: 1rem; }
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
                        <h1 class="page-title">Infrastructure Observability</h1>
                        <p class="page-subtitle">Real-time health metrics across the full AI and application stack.</p>
                    </div>
                </div>

                <div class="infra-tabs">
                    <a href="?view=dashboards" class="inf-tab <?php echo $view == 'dashboards' ? 'active' : ''; ?>">Grafana Hub</a>
                    <a href="?view=sentry" class="inf-tab <?php echo $view == 'sentry' ? 'active' : ''; ?>">Sentry Error Centre</a>
                    <a href="?view=logs" class="inf-tab <?php echo $view == 'logs' ? 'active' : ''; ?>">System Logs</a>
                </div>

                <?php if ($view == 'dashboards'): ?>
                <!-- Screen 6.2.1 — Grafana Dashboard Hub -->
                <div class="dashboard-grid">
                    <div class="widget w-third db-selector">
                        <h3 class="widget-title" style="margin-bottom: 1rem;">System Dashboards</h3>
                        <?php foreach($dashboards as $id => $name): ?>
                            <a href="?view=dashboards&db=<?php echo $id; ?>" style="text-decoration: none; color: inherit;">
                                <div class="db-item <?php echo $dashboard_id == $id ? 'active' : ''; ?>">
                                    <div style="font-weight: 700; font-size: 0.9rem;"><?php echo $name; ?></div>
                                    <div style="font-size: 0.7rem; color: var(--text-secondary); margin-top: 2px;">
                                        <?php if($id == 1) echo "CPU, RAM, Disk, Network"; ?>
                                        <?php if($id == 2) echo "Slow Queries, Connections, PPS"; ?>
                                        <?php if($id == 3) echo "Hit Rate, Memory, Evictions"; ?>
                                        <?php if($id == 4) echo "Ollama TPS, Queue, RAM"; ?>
                                        <?php if($id == 5) echo "Latency, Index Size, Lag"; ?>
                                        <?php if($id == 6) echo "Status Codes, Bandwidth"; ?>
                                        <?php if($id == 7) echo "Queue Depth, Failure Rate"; ?>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>

                        <div style="margin-top: 2rem; background: rgba(239, 68, 68, 0.1); padding: 1rem; border-radius: 8px; border: 1px solid var(--danger);">
                            <div style="font-size: 0.75rem; color: var(--danger); font-weight: 700;"><i class="fas fa-exclamation-triangle"></i> CRITICAL ALERT</div>
                            <p style="font-size: 0.7rem; color: var(--text-secondary); margin-top: 5px;">Lead Delivery queue depth > 100. P99 Latency exceeding SLA targets.</p>
                        </div>
                    </div>

                    <div class="widget w-two-thirds">
                        <div class="widget-header">
                            <h3 class="widget-title"><?php echo $dashboards[$dashboard_id]; ?></h3>
                            <button class="btn" style="background: var(--sidebar-bg); border: 1px solid var(--border-color); color: white; font-size: 0.75rem;"><i class="fas fa-external-link-alt"></i> Open in Grafana</button>
                        </div>
                        <div class="grafana-embed">
                            <div style="text-align: center;">
                                <i class="fas fa-chart-line" style="font-size: 3rem; color: var(--text-secondary); opacity: 0.2; margin-bottom: 1rem;"></i>
                                <p style="color: var(--text-secondary); font-size: 0.9rem;">Embedded Grafana IFrame Instance (Dashboard #<?php echo $dashboard_id; ?>)</p>
                                <p style="color: var(--text-secondary); font-size: 0.7rem; margin-top: 5px;">Connection: Secure WebSocket Tunnel Active</p>
                            </div>
                        </div>
                    </div>
                </div>

                <?php elseif ($view == 'sentry'): ?>
                <!-- Screen 6.2.2 — Sentry Error Centre -->
                <div class="widget w-full">
                    <div class="widget-header">
                        <h3 class="widget-title">Real-time Application Exceptions</h3>
                        <div style="display: flex; gap: 10px;">
                            <span class="status-badge" style="background: rgba(239, 68, 68, 0.1); color: var(--danger);">8 Unresolved Issues</span>
                        </div>
                    </div>
                    
                    <div class="sentry-group">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                            <div>
                                <h4 style="color: white; font-size: 1rem;">PostgreSQLConnectionError: Failed to connect to pool</h4>
                                <p style="font-size: 0.8rem; color: var(--text-secondary); margin-top: 5px;">Backend &bull; Fastify &bull; /api/counselor/query</p>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-size: 0.7rem; color: var(--text-secondary); margin-bottom: 5px;">First seen: 2h ago</div>
                                <div style="font-weight: 700; color: white;">47 Occurrences &bull; 12 Users</div>
                            </div>
                        </div>
                        <div style="font-family: monospace; font-size: 0.75rem; background: rgba(0,0,0,0.3); padding: 10px; border-radius: 4px; color: #ff8b8b; margin-bottom: 1.5rem;">
                            ConnectionTimeout: Connection request timed out after 5000ms. Max pool size (20) reached.
                        </div>
                        <div style="display: flex; gap: 10px;">
                            <button class="btn btn-primary" style="font-size: 0.75rem;">Assign to Developer</button>
                            <button class="btn" style="background: var(--sidebar-bg); border: 1px solid var(--border-color); color: white; font-size: 0.75rem;">Mark as Resolved</button>
                            <button class="btn" style="background: var(--sidebar-bg); border: 1px solid var(--border-color); color: white; font-size: 0.75rem;">View Stacktrace</button>
                        </div>
                    </div>

                    <div class="sentry-group" style="border-left-color: var(--warning);">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                            <div>
                                <h4 style="color: white; font-size: 1rem;">ChunkLoadError: Loading chunk 829 failed</h4>
                                <p style="font-size: 0.8rem; color: var(--text-secondary); margin-top: 5px;">Frontend &bull; Next.js &bull; /college/[slug]</p>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-size: 0.7rem; color: var(--text-secondary); margin-bottom: 5px;">First seen: 1h ago</div>
                                <div style="font-weight: 700; color: white;">12 Occurrences &bull; 8 Users</div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </main>
    </div>
</body>
</html>
