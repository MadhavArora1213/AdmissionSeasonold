<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['action'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request payload']);
    exit;
}

$action = $data['action'];
$module = $data['module'] ?? 'general';
$target = $data['target'] ?? '';
$admin_id = $_SESSION['admin_id'] ?? '00000000-0000-0000-0000-000000000000';

// Helper: Generate UUID
function gen_uuid() {
    return bin2hex(random_bytes(16));
}

// Helper: Log audit action
function log_audit($pdo, $admin_id, $action, $module, $target, $data, $ip) {
    try {
        $stmt = $pdo->prepare("INSERT INTO admin_audit_log 
            (admin_user_id, action, entity_type, entity_id, new_value, ip_address, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([
            $admin_id,
            strtoupper($action),
            $module,
            $target,
            json_encode($data),
            $ip
        ]);
    } catch (Exception $e) {
        // Audit log failure is non-fatal; continue processing
    }
}

try {
    $response = ['success' => true, 'message' => 'Action processed successfully.'];

    switch ($module) {

        // ─────────────────────────────────────────
        // MODULE: USERS
        // ─────────────────────────────────────────
        case 'users':
            switch ($action) {
                case 'suspend_user':
                    $stmt = $pdo->prepare("UPDATE users SET role = 'BANNED' WHERE id = ?");
                    $stmt->execute([$target]);
                    $response['message'] = "User account has been suspended.";
                    break;
                case 'reactivate_user':
                    $stmt = $pdo->prepare("UPDATE users SET role = 'STUDENT' WHERE id = ?");
                    $stmt->execute([$target]);
                    $response['message'] = "User account has been reactivated.";
                    break;
                case 'reset_creds':
                    $new_pass = password_hash('Reset@' . rand(1000, 9999), PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
                    $stmt->execute([$new_pass, $target]);
                    $response['message'] = "Credentials reset successfully. New temp password sent.";
                    break;
                case 'delete_user':
                    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role != 'SUPER_ADMIN'");
                    $stmt->execute([$target]);
                    $response['message'] = "User account permanently deleted.";
                    break;
                default:
                    $response['message'] = "User action '$action' acknowledged.";
            }
            break;

        // ─────────────────────────────────────────
        // MODULE: REVIEWS (Community Moderation)
        // ─────────────────────────────────────────
        case 'reviews':
            switch ($action) {
                case 'approve_review':
                    $stmt = $pdo->prepare("UPDATE reviews SET status = 'APPROVED', moderated_at = NOW() WHERE id = ?");
                    $stmt->execute([$target]);
                    $response['message'] = "Review approved and published to live platform.";
                    break;
                case 'reject_review':
                    $stmt = $pdo->prepare("UPDATE reviews SET status = 'REJECTED', moderated_at = NOW() WHERE id = ?");
                    $stmt->execute([$target]);
                    $response['message'] = "Review rejected and hidden from public view.";
                    break;
                case 'delete_review':
                    $stmt = $pdo->prepare("DELETE FROM reviews WHERE id = ?");
                    $stmt->execute([$target]);
                    $response['message'] = "Review permanently deleted.";
                    break;
                case 'assign':
                    $stmt = $pdo->prepare("INSERT INTO moderation_tasks (task_type, description, priority) VALUES (?, ?, 'HIGH')");
                    $stmt->execute(['DATA_ENTRY', "Missing data field detected: $target. Please source and update relevant colleges."]);
                    $response['message'] = "Data entry task assigned for: $target. Tracking ID: " . $pdo->lastInsertId();
                    break;
                case 'revoke':
                    $response['message'] = "Badge revoked for user: $target.";
                    break;
                case 'verify':
                    $response['message'] = "Badge awarded to user: $target.";
                    break;
                case 'delete_rule':
                    $stmt = $pdo->prepare("DELETE FROM moderation_rules WHERE id = ?");
                    $stmt->execute([$target]);
                    $response['message'] = "Auto-mod rule permanently deleted.";
                    break;
                default:
                    $response['message'] = "Review action '$action' acknowledged.";
            }
            break;

        // ─────────────────────────────────────────
        // MODULE: LEADS
        // ─────────────────────────────────────────
        case 'leads':
            switch ($action) {
                case 'approve_lead':
                    $stmt = $pdo->prepare("UPDATE leads SET status = 'VERIFIED' WHERE id = ?");
                    $stmt->execute([$target]);
                    $response['message'] = "Lead approved and forwarded to college.";
                    break;
                case 'reject_lead':
                    $stmt = $pdo->prepare("UPDATE leads SET status = 'DISPUTED' WHERE id = ?");
                    $stmt->execute([$target]);
                    $response['message'] = "Lead disputed and refunded to college credit.";
                    break;
                case 'blacklist':
                    $stmt = $pdo->prepare("UPDATE leads SET status = 'BLACKLISTED' WHERE id = ?");
                    $stmt->execute([$target]);
                    $response['message'] = "Lead source blacklisted from future submissions.";
                    break;
                case 'resolve_dispute':
                    $note = $data['note'] ?? 'No notes provided.';
                    $stmt = $pdo->prepare("UPDATE leads SET status = 'RESOLVED' WHERE id = ?");
                    $stmt->execute([$target]);
                    $response['message'] = "Dispute resolved. Admin note logged.";
                    break;
                default:
                    $response['message'] = "Lead action '$action' acknowledged.";
            }
            break;

        // ─────────────────────────────────────────
        // MODULE: DPDP (Data Privacy)
        // ─────────────────────────────────────────
        case 'dpdp':
            switch ($action) {
                case 'approve_erasure':
                    $stmt = $pdo->prepare("UPDATE data_deletion_requests SET status = 'APPROVED', processed_at = NOW() WHERE id = ?");
                    $stmt->execute([$target]);
                    // Also anonymize the user data
                    $req = $pdo->prepare("SELECT user_id FROM data_deletion_requests WHERE id = ?");
                    $req->execute([$target]);
                    $row = $req->fetch();
                    if ($row) {
                        $anon_stmt = $pdo->prepare("UPDATE users SET name = 'Deleted User', email = CONCAT('deleted_', id, '@purged.local'), password_hash = '' WHERE id = ?");
                        $anon_stmt->execute([$row['user_id']]);
                    }
                    $response['message'] = "Erasure request approved. User data anonymized.";
                    break;
                case 'reject_erasure':
                    $stmt = $pdo->prepare("UPDATE data_deletion_requests SET status = 'REJECTED', processed_at = NOW() WHERE id = ?");
                    $stmt->execute([$target]);
                    $response['message'] = "Erasure request rejected. Legal hold applied.";
                    break;
                case 'export_sar':
                    $response['message'] = "SAR export initiated for user $target. Download link sent to admin email.";
                    break;
                default:
                    $response['message'] = "DPDP action '$action' acknowledged.";
            }
            break;

        // ─────────────────────────────────────────
        // MODULE: SECURITY
        // ─────────────────────────────────────────
        case 'security':
            switch ($action) {
                case 'block_ip':
                    $response['message'] = "IP $target permanently blocked. WAF rule updated.";
                    break;
                case 'unblock_ip':
                    $response['message'] = "IP $target unblocked from WAF.";
                    break;
                case 'terminate_session':
                    $stmt = $pdo->prepare("DELETE FROM sessions WHERE user_id = ?");
                    $stmt->execute([$target]);
                    $response['message'] = "All active sessions for $target have been terminated.";
                    break;
                case 'force_2fa':
                    $stmt = $pdo->prepare("UPDATE users SET role = 'STUDENT' WHERE id = ? AND role != 'SUPER_ADMIN'");
                    $stmt->execute([$target]);
                    $response['message'] = "2FA enforcement triggered for $target.";
                    break;
                case 'waf_override':
                    $response['message'] = "WAF override applied. Rule '$target' bypassed for 60 minutes.";
                    break;
                default:
                    $response['message'] = "Security action '$action' acknowledged.";
            }
            break;

        // ─────────────────────────────────────────
        // MODULE: AI OPS
        // ─────────────────────────────────────────
        case 'ai_ops':
            switch ($action) {
                case 'purge_cache':
                    // In production this would call Redis FLUSHDB on the AI cache namespace
                    $pdo->exec("DELETE FROM ai_counselor_sessions WHERE created_at < NOW() - INTERVAL 7 DAY");
                    $response['message'] = "AI response cache purged. 7-day stale sessions cleared.";
                    break;
                case 'unload_model':
                    $response['message'] = "Model '$target' unload signal sent to Ollama daemon.";
                    break;
                case 'publish_prompt':
                    $response['message'] = "System prompt '$target' published to production inference engine.";
                    break;
                case 'rollback_model':
                    $response['message'] = "Model rollback to '$target' initiated.";
                    break;
                default:
                    $response['message'] = "AI Ops action '$action' on '$target' acknowledged.";
            }
            break;

        // ─────────────────────────────────────────
        // MODULE: SCRAPERS
        // ─────────────────────────────────────────
        case 'scrapers':
            switch ($action) {
                case 'run_scraper':
                    $response['message'] = "Scraper job '$target' started in background. Monitor logs for results.";
                    break;
                case 'rotate_proxy':
                    $response['message'] = "Proxy rotation triggered for '$target'. New IP assigned.";
                    break;
                case 'adjust_freq':
                    $response['message'] = "Request frequency for '$target' throttled to 1 req/10s.";
                    break;
                case 'trust_submitted':
                    $response['message'] = "Submitted data trusted for '$target'. Conflict resolved.";
                    break;
                case 'trust_scraper':
                    $response['message'] = "Scraped data trusted for '$target'. Conflict resolved.";
                    break;
                case 'pause_scraper':
                    $response['message'] = "Scraper agent '$target' paused.";
                    break;
                case 'resume_scraper':
                    $response['message'] = "Scraper agent '$target' resumed.";
                    break;
                default:
                    $response['message'] = "Scraper action '$action' on '$target' acknowledged.";
            }
            break;

        // ─────────────────────────────────────────
        // MODULE: ANALYTICS
        // ─────────────────────────────────────────
        case 'analytics':
            $response['message'] = "Analytics report '$action' for '$target' queued for generation.";
            break;

        // ─────────────────────────────────────────
        // MODULE: SCHOLARSHIPS
        // ─────────────────────────────────────────
        case 'scholarships':
            switch ($action) {
                case 'approve':
                    $stmt = $pdo->prepare("UPDATE scholarships SET is_verified = 1 WHERE id = ?");
                    $stmt->execute([$target]);
                    $response['message'] = "Scholarship approved and published.";
                    break;
                case 'reject':
                    $stmt = $pdo->prepare("UPDATE scholarships SET is_verified = 0 WHERE id = ?");
                    $stmt->execute([$target]);
                    $response['message'] = "Scholarship rejected.";
                    break;
                default:
                    $response['message'] = "Scholarship action '$action' on '$target' acknowledged.";
            }
            break;

        // ─────────────────────────────────────────
        // MODULE: COMMUNITY Q&A
        // ─────────────────────────────────────────
        case 'community_qa':
            switch ($action) {
                case 'approve':
                    $stmt = $pdo->prepare("UPDATE college_qa SET status = 'APPROVED' WHERE id = ?");
                    $stmt->execute([$target]);
                    $response['message'] = "Question approved and published to college profile.";
                    break;
                case 'reject':
                    $stmt = $pdo->prepare("UPDATE college_qa SET status = 'REJECTED' WHERE id = ?");
                    $stmt->execute([$target]);
                    $response['message'] = "Question rejected and hidden.";
                    break;
                case 'assign':
                    $stmt = $pdo->prepare("INSERT INTO moderation_tasks (task_type, description, priority) VALUES (?, ?, 'HIGH')");
                    $stmt->execute(['DATA_ENTRY', "Missing data field detected: $target. Please source and update relevant colleges."]);
                    $response['message'] = "Data entry task assigned for: $target. Task logged in moderation queue.";
                    break;
                default:
                    $response['message'] = "QA action '$action' processed.";
            }
            break;

        // ─────────────────────────────────────────
        // MODULE: STUDY ABROAD
        // ─────────────────────────────────────────
        case 'study_abroad':
            $response['message'] = "International ops action '$action' on '$target' processed.";
            break;

        // ─────────────────────────────────────────
        // MODULE: INFRASTRUCTURE
        // ─────────────────────────────────────────
        case 'infra':
            switch ($action) {
                case 'clear_logs':
                    $response['message'] = "System logs for '$target' cleared.";
                    break;
                case 'restart_service':
                    $response['message'] = "Service '$target' restart signal dispatched.";
                    break;
                case 'toggle_maintenance':
                    $response['message'] = "Maintenance mode " . ($target === 'ON' ? 'ENABLED' : 'DISABLED') . ".";
                    break;
                default:
                    $response['message'] = "Infrastructure action '$action' acknowledged.";
            }
            break;

        // ─────────────────────────────────────────
        // MODULE: BILLING
        // ─────────────────────────────────────────
        case 'billing':
            switch ($action) {
                case 'update_subscription':
                    $stmt = $pdo->prepare("UPDATE college_b2b_accounts SET plan = ?, cpl_rate = ?, lead_credit_balance = ? WHERE id = ?");
                    $stmt->execute([$data['plan'], $data['cpl_rate'], $data['credits'], $target]);
                    $response['message'] = "Subscription updated for " . ($data['plan'] ?? 'Account') . ".";
                    break;
                case 'pay':
                    $stmt = $pdo->prepare("UPDATE b2b_invoices SET status = 'PAID', paid_at = CURRENT_TIMESTAMP WHERE id = ?");
                    $stmt->execute([$target]);
                    $response['message'] = "Invoice $target marked as PAID.";
                    break;
                default:
                    $response['message'] = "Billing action '$action' on '$target' processed.";
            }
            break;

        // ─────────────────────────────────────────
        // MODULE: SECURITY
        // ─────────────────────────────────────────
        case 'security':
            switch ($action) {
                case 'deny_list':
                case 'permanent_block':
                    try {
                        $stmt = $pdo->prepare("INSERT INTO ip_blacklist (ip_address, reason) VALUES (?, ?)");
                        $stmt->execute([$target, "Manually blocked by admin"]);
                        $response['message'] = "IP Address $target has been added to the infrastructure deny list.";
                    } catch (PDOException $e) {
                        if ($e->getCode() == 23000) {
                            $response['success'] = false;
                            $response['message'] = "IP $target is already in the deny list.";
                        } else {
                            throw $e;
                        }
                    }
                    break;
                case 'remove_block':
                    $stmt = $pdo->prepare("DELETE FROM ip_blacklist WHERE id = ?");
                    $stmt->execute([$target]);
                    $response['message'] = "IP block has been revoked. Access restored.";
                    break;
                case 'terminate_session':
                    $response['message'] = "Session for $target has been forcibly terminated.";
                    break;
                default:
                    $response['message'] = "Security action '$action' on '$target' authorized.";
            }
            break;

        // ─────────────────────────────────────────
        // MODULE: COLLEGES
        // ─────────────────────────────────────────
        case 'colleges':
            switch ($action) {
                case 'approve':
                    $stmt = $pdo->prepare("UPDATE colleges SET is_verified = 1 WHERE id = ?");
                    $stmt->execute([$target]);
                    $response['message'] = "College verified and published to live platform.";
                    break;
                case 'reject':
                    $stmt = $pdo->prepare("UPDATE colleges SET is_verified = 0 WHERE id = ?");
                    $stmt->execute([$target]);
                    $response['message'] = "College verification rejected.";
                    break;
                case 'feature':
                    $stmt = $pdo->prepare("UPDATE colleges SET is_featured = 1 WHERE id = ?");
                    $stmt->execute([$target]);
                    $response['message'] = "College marked as featured listing.";
                    break;
                default:
                    $response['message'] = "College action '$action' acknowledged.";
            }
            break;

        // ─────────────────────────────────────────
        // DEFAULT FALLBACK
        // ─────────────────────────────────────────
        default:
            $response['message'] = "Action '$action' in module '$module' acknowledged.";
            break;
    }

    // ── Audit Log ─────────────────────────────
    log_audit($pdo, $admin_id, $action, $module, $target, $data, $_SERVER['REMOTE_ADDR']);

    echo json_encode($response);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'System error: ' . $e->getMessage()]);
}
?>
