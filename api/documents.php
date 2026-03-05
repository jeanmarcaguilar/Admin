<?php
/**
 * MICROFINANCIAL MANAGEMENT SYSTEM I
 * API: Document Management (Archiving) Module
 * 
 * Endpoints (via ?action=...):
 *   GET  list_documents      – All documents (filterable)
 *   GET  list_categories     – Document categories
 *   GET  dashboard_stats     – Summary counts
 *   POST create_document     – Upload/register new document
 *   POST update_document     – Update document metadata
 *   POST send_archive_pin    – Generate & email 4-digit security PIN
 *   POST verify_archive_pin  – Verify the 4-digit PIN
 */

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

session_start();
if (empty($_SESSION['authenticated'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/audit.php';
require_once __DIR__ . '/../config/mail.php';
require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/Exception.php';
require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$action = $_GET['action'] ?? '';

switch ($action) {

    case 'list_documents':
        $cat = $_GET['category_id'] ?? null;
        $status = $_GET['status'] ?? null;
        $sql = "SELECT d.*, c.name AS category_name,
                       CONCAT(u.first_name,' ',u.last_name) AS uploaded_by_name
                FROM documents d
                LEFT JOIN document_categories c ON d.category_id = c.category_id
                JOIN users u ON d.uploaded_by = u.user_id WHERE 1=1";
        $params = [];
        if ($cat) { $sql .= " AND d.category_id = ?"; $params[] = $cat; }
        if ($status) { $sql .= " AND d.status = ?"; $params[] = $status; }
        $sql .= " ORDER BY d.created_at DESC";
        $stmt = getDocDB()->prepare($sql);
        $stmt->execute($params);
        jsonResponse(['data' => $stmt->fetchAll()]);

    case 'list_categories':
        $rows = getDocDB()->query("SELECT * FROM document_categories WHERE is_active=1 ORDER BY sort_order")->fetchAll();
        jsonResponse(['data' => $rows]);

    case 'create_document':
        $d = readJsonBody();
        $code = 'DOC-' . date('Y') . '-' . str_pad(random_int(1,999999),6,'0',STR_PAD_LEFT);
        $stmt = getDocDB()->prepare("INSERT INTO documents 
            (document_code, title, category_id, document_type, description, file_path, file_name,
             file_size, file_type, uploaded_by, department, confidentiality, status)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute([
            $code, $d['title'], $d['category_id'] ?? null, $d['document_type'] ?? 'other',
            $d['description'] ?? null, $d['file_path'] ?? '/uploads/documents/', $d['file_name'] ?? 'unknown',
            $d['file_size'] ?? 0, $d['file_type'] ?? 'application/pdf',
            $d['uploaded_by'] ?? 1, $d['department'] ?? null,
            $d['confidentiality'] ?? 'internal', $d['status'] ?? 'active'
        ]);
        logAudit('documents', 'CREATE_DOCUMENT', 'documents', null, null, ['document_code' => $code, 'title' => $d['title']]);

        // ── Auto-Reserve Training Hall for Training Management documents ──
        $autoReservation = null;
        $dept = $d['department'] ?? '';
        $titleLower = strtolower($d['title'] ?? '');
        $isTrainingDoc = ($dept === 'HR 2' && (
            strpos($titleLower, 'training') !== false ||
            strpos($titleLower, 'training management') !== false ||
            strpos($titleLower, 'training catalog') !== false ||
            strpos($titleLower, 'training needs') !== false ||
            strpos($titleLower, 'training completion') !== false ||
            strpos($titleLower, 'training materials') !== false ||
            strpos($titleLower, 'training evaluation') !== false ||
            strpos($titleLower, 'training room') !== false
        ));

        if ($isTrainingDoc) {
            try {
                $facDb = getFacilitiesDB();
                // Find available Training Hall (Level 2 · VIP)
                $hallStmt = $facDb->query("SELECT facility_id, name, room_level FROM facilities 
                    WHERE type = 'training_hall' AND status = 'available' 
                    ORDER BY facility_id ASC LIMIT 1");
                $hall = $hallStmt->fetch();
                if ($hall) {
                    $resCode = 'RES-' . date('Y') . '-' . str_pad(random_int(1,999999),6,'0',STR_PAD_LEFT);
                    $userId = $_SESSION['user']['user_id'] ?? 1;
                    $now = date('Y-m-d H:i:s');
                    $startDt = date('Y-m-d 09:00:00', strtotime('+1 day'));
                    $endDt   = date('Y-m-d 12:00:00', strtotime('+1 day'));
                    $purpose = 'Auto-reserved for Training Management document: ' . ($d['title'] ?? $code);
                    $eventTitle = '📋 Training Doc: ' . ($d['title'] ?? 'Training Document');

                    $resStmt = $facDb->prepare("INSERT INTO facility_reservations 
                        (reservation_code, facility_id, reserved_by, department, purpose, event_title,
                         reservation_type, priority, start_datetime, end_datetime, status, is_auto_tagged)
                        VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
                    $resStmt->execute([
                        $resCode, $hall['facility_id'], $userId, 'HR 2',
                        $purpose, $eventTitle,
                        'regular', 'normal', $startDt, $endDt, 'approved', 1
                    ]);
                    $autoReservation = [
                        'reservation_code' => $resCode,
                        'room_name' => $hall['name'],
                        'room_level' => intval($hall['room_level'] ?? 2),
                        'start' => $startDt,
                        'end' => $endDt,
                        'status' => 'approved'
                    ];
                    logAudit('facilities', 'AUTO_RESERVE_TRAINING', 'facility_reservations', null, null, [
                        'reservation_code' => $resCode, 'document_code' => $code, 'room' => $hall['name']
                    ]);
                }
            } catch (Exception $e) {
                // Non-blocking: log but don't fail the document creation
                error_log('Auto-reserve Training Hall failed: ' . $e->getMessage());
            }
        }

        jsonResponse(['success' => true, 'document_code' => $code, 'auto_reservation' => $autoReservation], 201);

    case 'update_document':
        $d = readJsonBody();
        $stmt = getDocDB()->prepare("UPDATE documents SET title=?, category_id=?, description=?,
            confidentiality=?, status=? WHERE document_id=?");
        $stmt->execute([$d['title'], $d['category_id'], $d['description'] ?? null,
            $d['confidentiality'] ?? 'internal', $d['status'] ?? 'active', $d['document_id']]);
        logAudit('documents', 'UPDATE_DOCUMENT', 'documents', intval($d['document_id']), null, ['title' => $d['title']]);
        jsonResponse(['success' => true]);

    case 'dashboard_stats':
        $db = getDocDB();
        $stats = [];
        $stats['total_documents'] = $db->query("SELECT COUNT(*) FROM documents")->fetchColumn();
        $stats['active_documents'] = $db->query("SELECT COUNT(*) FROM documents WHERE created_at > DATE_SUB(NOW(), INTERVAL 6 MONTH)")->fetchColumn();
        $stats['archived_documents'] = $db->query("SELECT COUNT(*) FROM documents WHERE created_at <= DATE_SUB(NOW(), INTERVAL 6 MONTH) AND created_at > DATE_SUB(NOW(), INTERVAL 3 YEAR)")->fetchColumn();
        $stats['retained_documents'] = $db->query("SELECT COUNT(*) FROM documents WHERE created_at <= DATE_SUB(NOW(), INTERVAL 3 YEAR)")->fetchColumn();
        $stats['departments'] = $db->query("SELECT COUNT(DISTINCT department) FROM documents WHERE department IS NOT NULL")->fetchColumn();
        jsonResponse($stats);

    case 'list_by_department':
        $dept = $_GET['department'] ?? null;
        $folder = $_GET['folder'] ?? null;
        $sql = "SELECT d.*, c.name AS category_name FROM documents d
                LEFT JOIN document_categories c ON d.category_id = c.category_id WHERE 1=1";
        $params = [];
        if ($dept) { $sql .= " AND d.department = ?"; $params[] = $dept; }
        if ($folder) { $sql .= " AND d.folder_name = ?"; $params[] = $folder; }
        $sql .= " ORDER BY d.created_at DESC";
        $stmt = getDocDB()->prepare($sql);
        $stmt->execute($params);
        jsonResponse(['data' => $stmt->fetchAll()]);

    case 'list_folders':
        $rows = getDocDB()->query("SELECT department, folder_name,
            COUNT(*) as doc_count,
            SUM(CASE WHEN created_at > DATE_SUB(NOW(), INTERVAL 6 MONTH) THEN 1 ELSE 0 END) as active_count,
            SUM(CASE WHEN created_at <= DATE_SUB(NOW(), INTERVAL 6 MONTH) AND created_at > DATE_SUB(NOW(), INTERVAL 3 YEAR) THEN 1 ELSE 0 END) as archived_count,
            SUM(CASE WHEN created_at <= DATE_SUB(NOW(), INTERVAL 3 YEAR) THEN 1 ELSE 0 END) as retained_count
            FROM documents GROUP BY department, folder_name ORDER BY department")->fetchAll();
        jsonResponse(['data' => $rows]);

    case 'search_documents':
        $q = $_GET['q'] ?? '';
        $stmt = getDocDB()->prepare("SELECT d.*, c.name AS category_name FROM documents d
            LEFT JOIN document_categories c ON d.category_id = c.category_id
            WHERE (d.title LIKE ? OR d.description LIKE ? OR d.folder_name LIKE ? OR d.department LIKE ?)
            ORDER BY d.created_at DESC");
        $like = "%{$q}%";
        $stmt->execute([$like, $like, $like, $like]);
        jsonResponse(['data' => $stmt->fetchAll()]);

    // ═══════════════════════════════════════════
    // ARCHIVING
    // ═══════════════════════════════════════════

    case 'archiving_stats':
        $db = getDocDB();
        $stats = [];
        $stats['total_active'] = $db->query("SELECT COUNT(*) FROM documents WHERE created_at > DATE_SUB(NOW(), INTERVAL 6 MONTH)")->fetchColumn();
        $stats['total_archived'] = $db->query("SELECT COUNT(*) FROM documents WHERE created_at <= DATE_SUB(NOW(), INTERVAL 6 MONTH) AND created_at > DATE_SUB(NOW(), INTERVAL 3 YEAR)")->fetchColumn();
        $stats['total_retained'] = $db->query("SELECT COUNT(*) FROM documents WHERE created_at <= DATE_SUB(NOW(), INTERVAL 3 YEAR)")->fetchColumn();
        $stats['pending_archive'] = $db->query("SELECT COUNT(*) FROM documents WHERE status='active' AND created_at <= DATE_SUB(NOW(), INTERVAL 6 MONTH)")->fetchColumn();
        $stats['auto_archived_today'] = $db->query("SELECT COUNT(*) FROM documents WHERE archived_at IS NOT NULL AND DATE(archived_at) = CURDATE()")->fetchColumn();
        $stats['oldest_document'] = $db->query("SELECT MIN(created_at) FROM documents")->fetchColumn();
        jsonResponse(['data' => $stats]);

    case 'list_archive_timeline':
        $sql = "SELECT 
                  DATE_FORMAT(created_at, '%Y-%m') AS month,
                  COUNT(*) AS total,
                  SUM(CASE WHEN created_at > DATE_SUB(NOW(), INTERVAL 6 MONTH) THEN 1 ELSE 0 END) AS active,
                  SUM(CASE WHEN created_at <= DATE_SUB(NOW(), INTERVAL 6 MONTH) AND created_at > DATE_SUB(NOW(), INTERVAL 3 YEAR) THEN 1 ELSE 0 END) AS archived,
                  SUM(CASE WHEN created_at <= DATE_SUB(NOW(), INTERVAL 3 YEAR) THEN 1 ELSE 0 END) AS retained
                FROM documents 
                GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                ORDER BY month DESC LIMIT 24";
        $rows = getDocDB()->query($sql)->fetchAll();
        jsonResponse(['data' => $rows]);

    case 'run_archive_cycle':
        $db = getDocDB();
        // Verify archive PIN first
        if (empty($_SESSION['archive_pin_verified']) || (time() - ($_SESSION['archive_pin_verified'] ?? 0)) > 300) {
            http_response_code(403);
            jsonResponse(['error' => 'Security PIN verification required to run archive cycle.']);
        }
        // Auto-archive documents older than 6 months that are still active
        $stmt = $db->prepare("UPDATE documents SET status='archived', archived_at=NOW() WHERE status='active' AND created_at <= DATE_SUB(NOW(), INTERVAL 6 MONTH)");
        $stmt->execute();
        $archivedCount = $stmt->rowCount();
        // Auto-retain documents older than 3 years
        $stmt = $db->prepare("UPDATE documents SET status='retained', retained_at=NOW() WHERE status='archived' AND created_at <= DATE_SUB(NOW(), INTERVAL 3 YEAR)");
        $stmt->execute();
        $retainedCount = $stmt->rowCount();
        // Clear PIN verification after use
        unset($_SESSION['archive_pin_verified']);
        logAudit('documents', 'RUN_ARCHIVE_CYCLE', 'documents', null, null, ['archived' => $archivedCount, 'retained' => $retainedCount]);
        jsonResponse(['success' => true, 'archived' => $archivedCount, 'retained' => $retainedCount]);

    // ═══════════════════════════════════════════
    // ARCHIVE SECURITY PIN (4-digit)
    // ═══════════════════════════════════════════

    case 'send_archive_pin':
        // Generate 4-digit PIN
        $pin = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        $_SESSION['archive_pin'] = $pin;
        $_SESSION['archive_pin_created'] = time();

        $sent = sendArchivePinEmail($pin);

        if ($sent === true) {
            jsonResponse([
                'success' => true,
                'message' => 'Security PIN sent to your email.',
                'expires_in' => 120
            ]);
        } else {
            // Dev fallback if mail fails
            jsonResponse([
                'success' => true,
                'mail_failed' => true,
                'fallback_pin' => $pin,
                'message' => 'Email could not be sent. Use the PIN shown on screen.',
                'expires_in' => 120
            ]);
        }

    case 'verify_archive_pin':
        $d = readJsonBody();
        $userPin = trim($d['pin'] ?? '');

        if (empty($userPin)) {
            http_response_code(400);
            jsonResponse(['success' => false, 'message' => 'PIN is required.']);
        }

        // Check expiry (120 seconds)
        $elapsed = time() - ($_SESSION['archive_pin_created'] ?? 0);
        if ($elapsed > 120) {
            unset($_SESSION['archive_pin'], $_SESSION['archive_pin_created']);
            http_response_code(410);
            jsonResponse(['success' => false, 'expired' => true, 'message' => 'PIN has expired. Please request a new one.']);
        }

        // Verify
        if ($userPin === ($_SESSION['archive_pin'] ?? '')) {
            $_SESSION['archive_pin_verified'] = time();
            unset($_SESSION['archive_pin'], $_SESSION['archive_pin_created']);
            logAudit('documents', 'VERIFY_ARCHIVE_PIN', 'documents', null, null, ['verified' => true]);
            jsonResponse(['success' => true, 'message' => 'PIN verified. Archive access granted.']);
        } else {
            http_response_code(401);
            jsonResponse(['success' => false, 'message' => 'Invalid PIN. Please try again.']);
        }

    case 'check_archive_access':
        $verified = !empty($_SESSION['archive_pin_verified']) && (time() - ($_SESSION['archive_pin_verified'] ?? 0)) <= 300;
        jsonResponse(['verified' => $verified]);

    // ═══════════════════════════════════════════
    // CONFIDENTIAL DOCUMENT SECURITY PIN
    // ═══════════════════════════════════════════

    case 'check_document_access':
        $docId = intval($_GET['document_id'] ?? 0);
        $userId = $_SESSION['user']['user_id'] ?? 0;

        // Check 1: Is confidential PIN already verified this session? (5 min window)
        $pinVerified = !empty($_SESSION['confidential_pin_verified']) && (time() - ($_SESSION['confidential_pin_verified'] ?? 0)) <= 300;

        // Check 2: Does user have an active grant in document_access?
        $hasGrant = false;
        if ($docId && $userId) {
            $db = getDocDB();
            $stmt = $db->prepare("SELECT COUNT(*) FROM document_access WHERE document_id=? AND user_id=? AND (expires_at IS NULL OR expires_at > NOW())");
            $stmt->execute([$docId, $userId]);
            $hasGrant = $stmt->fetchColumn() > 0;
        }

        jsonResponse(['access_granted' => $pinVerified || $hasGrant, 'via_pin' => $pinVerified, 'via_grant' => $hasGrant]);

    case 'send_confidential_pin':
        $pin = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        $_SESSION['confidential_pin'] = $pin;
        $_SESSION['confidential_pin_created'] = time();

        $sent = sendConfidentialPinEmail($pin);

        if ($sent === true) {
            jsonResponse([
                'success' => true,
                'message' => 'Security PIN sent to your email.',
                'expires_in' => 120
            ]);
        } else {
            jsonResponse([
                'success' => true,
                'mail_failed' => true,
                'fallback_pin' => $pin,
                'message' => 'Email could not be sent. Use the PIN shown on screen.',
                'expires_in' => 120
            ]);
        }

    case 'verify_confidential_pin':
        $d = readJsonBody();
        $userPin = trim($d['pin'] ?? '');

        if (empty($userPin)) {
            http_response_code(400);
            jsonResponse(['success' => false, 'message' => 'PIN is required.']);
        }

        $elapsed = time() - ($_SESSION['confidential_pin_created'] ?? 0);
        if ($elapsed > 120) {
            unset($_SESSION['confidential_pin'], $_SESSION['confidential_pin_created']);
            http_response_code(410);
            jsonResponse(['success' => false, 'expired' => true, 'message' => 'PIN has expired. Please request a new one.']);
        }

        if ($userPin === ($_SESSION['confidential_pin'] ?? '')) {
            $_SESSION['confidential_pin_verified'] = time();
            unset($_SESSION['confidential_pin'], $_SESSION['confidential_pin_created']);
            logAudit('documents', 'VERIFY_CONFIDENTIAL_PIN', 'documents', null, null, ['verified' => true]);
            jsonResponse(['success' => true, 'message' => 'PIN verified. Confidential access granted.']);
        } else {
            http_response_code(401);
            jsonResponse(['success' => false, 'message' => 'Invalid PIN. Please try again.']);
        }

    // ═══════════════════════════════════════════
    // ACCESS CONTROL
    // ═══════════════════════════════════════════

    case 'access_control_stats':
        $db = getDocDB();
        $stats = [];
        $stats['total_grants'] = $db->query("SELECT COUNT(*) FROM document_access")->fetchColumn();
        $stats['active_grants'] = $db->query("SELECT COUNT(*) FROM document_access WHERE expires_at IS NULL OR expires_at > NOW()")->fetchColumn();
        $stats['expired_grants'] = $db->query("SELECT COUNT(*) FROM document_access WHERE expires_at IS NOT NULL AND expires_at <= NOW()")->fetchColumn();
        $stats['view_only'] = $db->query("SELECT COUNT(*) FROM document_access WHERE permission='view'")->fetchColumn();
        $stats['download'] = $db->query("SELECT COUNT(*) FROM document_access WHERE permission='download'")->fetchColumn();
        $stats['edit'] = $db->query("SELECT COUNT(*) FROM document_access WHERE permission='edit'")->fetchColumn();
        $stats['admin'] = $db->query("SELECT COUNT(*) FROM document_access WHERE permission='admin'")->fetchColumn();
        $stats['users_with_access'] = $db->query("SELECT COUNT(DISTINCT user_id) FROM document_access")->fetchColumn();
        jsonResponse(['data' => $stats]);

    case 'list_access_grants':
        $docId = $_GET['document_id'] ?? null;
        $userId = $_GET['user_id'] ?? null;
        $sql = "SELECT da.access_id, da.document_id, da.user_id, da.permission, da.expires_at, da.created_at,
                       d.document_code, d.title AS document_title, d.confidentiality,
                       CONCAT(u.first_name,' ',u.last_name) AS user_name, u.department AS user_department, u.role AS user_role,
                       CONCAT(g.first_name,' ',g.last_name) AS granted_by_name
                FROM document_access da
                JOIN documents d ON da.document_id = d.document_id
                JOIN users u ON da.user_id = u.user_id
                LEFT JOIN users g ON da.granted_by = g.user_id
                WHERE 1=1";
        $params = [];
        if ($docId) { $sql .= " AND da.document_id = ?"; $params[] = $docId; }
        if ($userId) { $sql .= " AND da.user_id = ?"; $params[] = $userId; }
        $sql .= " ORDER BY da.created_at DESC";
        $stmt = getDocDB()->prepare($sql);
        $stmt->execute($params);
        jsonResponse(['data' => $stmt->fetchAll()]);

    case 'grant_access':
        $d = readJsonBody();
        $db = getDocDB();
        $stmt = $db->prepare("INSERT INTO document_access (document_id, user_id, permission, granted_by, expires_at) 
            VALUES (?,?,?,?,?) ON DUPLICATE KEY UPDATE permission=VALUES(permission), granted_by=VALUES(granted_by), expires_at=VALUES(expires_at)");
        $stmt->execute([
            $d['document_id'], $d['user_id'],
            $d['permission'] ?? 'view',
            $_SESSION['user']['user_id'] ?? null,
            $d['expires_at'] ?? null
        ]);
        logAudit('documents', 'GRANT_ACCESS', 'document_access', intval($d['document_id']), null, [
            'user_id' => $d['user_id'], 'permission' => $d['permission'] ?? 'view'
        ]);
        jsonResponse(['success' => true], 201);

    case 'revoke_access':
        $d = readJsonBody();
        $db = getDocDB();
        $stmt = $db->prepare("DELETE FROM document_access WHERE access_id=?");
        $stmt->execute([$d['access_id']]);
        logAudit('documents', 'REVOKE_ACCESS', 'document_access', intval($d['access_id']), null, null);
        jsonResponse(['success' => true]);

    case 'list_users':
        $rows = getDocDB()->query("SELECT user_id, employee_id, first_name, last_name, email, role, department FROM users WHERE is_active=1 ORDER BY first_name")->fetchAll();
        jsonResponse(['data' => $rows]);

    // ═══════════════════════════════════════════
    // DOCUMENT VIEWER — RBAC + AUDIT
    // ═══════════════════════════════════════════

    case 'get_user_info':
        $u = $_SESSION['user'] ?? [];
        jsonResponse([
            'user_id'    => $u['user_id'] ?? 0,
            'name'       => trim(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? $u['name'] ?? '')),
            'role'       => $u['role'] ?? 'staff',
            'department' => $u['department'] ?? '',
            'email'      => $u['email'] ?? '',
        ]);

    case 'check_viewer_access':
        // Determines what the current user can do with a given document
        // Since all folders are secured with passcode, grant full access to all authenticated users
        $docId = intval($_GET['document_id'] ?? 0);
        $userId = intval($_SESSION['user']['user_id'] ?? 0);
        $userRole = $_SESSION['user']['role'] ?? 'staff';

        $PRIVILEGED_ROLES = ['super_admin', 'admin', 'manager', 'head_department'];
        $isPrivileged = in_array($userRole, $PRIVILEGED_ROLES, true);

        // Get document details
        $db = getDocDB();
        $doc = null;
        if ($docId) {
            $stmt = $db->prepare("SELECT document_id, document_code, title, confidentiality, status, department, file_path, file_type FROM documents WHERE document_id=?");
            $stmt->execute([$docId]);
            $doc = $stmt->fetch();
        }

        $conf = $doc['confidentiality'] ?? 'internal';

        // All authenticated users get full access (folder passcode already secures access)
        $canView = true;
        $canDownload = true;
        $accessMethod = $isPrivileged ? 'role_based' : 'folder_secured';
        $requiresRequest = false;

        jsonResponse([
            'can_view'         => $canView,
            'can_download'     => $canDownload,
            'access_method'    => $accessMethod,
            'requires_request' => $requiresRequest,
            'user_role'        => $userRole,
            'is_privileged'    => $isPrivileged,
            'confidentiality'  => $conf,
        ]);

    case 'log_document_action':
        // Log a view or download event
        $d = readJsonBody();
        $db = getDocDB();
        $userId = intval($_SESSION['user']['user_id'] ?? 0);
        $userName = trim(($_SESSION['user']['first_name'] ?? '') . ' ' . ($_SESSION['user']['last_name'] ?? $_SESSION['user']['name'] ?? ''));
        $userRole = $_SESSION['user']['role'] ?? 'staff';
        $userDept = $_SESSION['user']['department'] ?? '';

        $stmt = $db->prepare("INSERT INTO document_view_logs 
            (document_id, document_code, document_title, department, source_system, action, 
             user_id, user_name, user_role, user_department, ip_address, user_agent, 
             file_type, file_size, access_method)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute([
            $d['document_id'] ?? null,
            $d['document_code'] ?? null,
            $d['document_title'] ?? null,
            $d['department'] ?? null,
            $d['source_system'] ?? null,
            $d['action'] ?? 'view',
            $userId, $userName, $userRole, $userDept,
            $_SERVER['REMOTE_ADDR'] ?? null,
            substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500),
            $d['file_type'] ?? null,
            $d['file_size'] ?? null,
            $d['access_method'] ?? 'direct',
        ]);
        jsonResponse(['success' => true, 'log_id' => $db->lastInsertId()]);

    case 'list_view_logs':
        $db = getDocDB();
        $docId   = $_GET['document_id'] ?? null;
        $action  = $_GET['action_filter'] ?? null;
        $userId  = $_GET['user_id'] ?? null;
        $dateFrom = $_GET['date_from'] ?? null;
        $dateTo  = $_GET['date_to'] ?? null;

        $sql = "SELECT * FROM document_view_logs WHERE 1=1";
        $params = [];
        if ($docId)    { $sql .= " AND document_id = ?";            $params[] = $docId; }
        if ($action)   { $sql .= " AND action = ?";                 $params[] = $action; }
        if ($userId)   { $sql .= " AND user_id = ?";                $params[] = $userId; }
        if ($dateFrom) { $sql .= " AND created_at >= ?";            $params[] = $dateFrom . ' 00:00:00'; }
        if ($dateTo)   { $sql .= " AND created_at <= ?";            $params[] = $dateTo . ' 23:59:59'; }
        $sql .= " ORDER BY created_at DESC LIMIT 500";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        jsonResponse(['success' => true, 'data' => $stmt->fetchAll()]);

    case 'view_log_stats':
        $db = getDocDB();
        $stats = [];
        $stats['total_views']     = $db->query("SELECT COUNT(*) FROM document_view_logs WHERE action='view'")->fetchColumn() ?: 0;
        $stats['total_downloads'] = $db->query("SELECT COUNT(*) FROM document_view_logs WHERE action='download'")->fetchColumn() ?: 0;
        $stats['total_previews']  = $db->query("SELECT COUNT(*) FROM document_view_logs WHERE action='preview'")->fetchColumn() ?: 0;
        $stats['unique_users']    = $db->query("SELECT COUNT(DISTINCT user_id) FROM document_view_logs")->fetchColumn() ?: 0;
        $stats['unique_docs']     = $db->query("SELECT COUNT(DISTINCT COALESCE(document_id, document_code)) FROM document_view_logs")->fetchColumn() ?: 0;
        $stats['today_views']     = $db->query("SELECT COUNT(*) FROM document_view_logs WHERE DATE(created_at)=CURDATE()")->fetchColumn() ?: 0;
        // Most viewed document
        $row = $db->query("SELECT document_title, COUNT(*) as cnt FROM document_view_logs WHERE document_title IS NOT NULL GROUP BY document_title ORDER BY cnt DESC LIMIT 1")->fetch();
        $stats['most_viewed_doc'] = $row ? $row['document_title'] : '—';
        $stats['most_viewed_count'] = $row ? intval($row['cnt']) : 0;
        jsonResponse(['success' => true, 'data' => $stats]);

    // ═══════════════════════════════════════════
    // ACCESS REQUESTS (approval workflow)
    // ═══════════════════════════════════════════

    case 'submit_access_request':
        $d = readJsonBody();
        $db = getDocDB();
        $userId = intval($_SESSION['user']['user_id'] ?? 0);
        $userName = trim(($_SESSION['user']['first_name'] ?? '') . ' ' . ($_SESSION['user']['last_name'] ?? $_SESSION['user']['name'] ?? ''));
        $userRole = $_SESSION['user']['role'] ?? 'staff';
        $userDept = $_SESSION['user']['department'] ?? '';

        // Check for existing pending request
        $stmt = $db->prepare("SELECT request_id FROM document_access_requests WHERE document_id=? AND requested_by=? AND status='pending'");
        $stmt->execute([$d['document_id'], $userId]);
        if ($stmt->fetch()) {
            jsonResponse(['success' => false, 'message' => 'You already have a pending request for this document.'], 409);
        }

        // Get document info
        $stmt = $db->prepare("SELECT document_code, title FROM documents WHERE document_id=?");
        $stmt->execute([$d['document_id']]);
        $docInfo = $stmt->fetch();

        $stmt = $db->prepare("INSERT INTO document_access_requests 
            (document_id, document_code, document_title, requested_by, requester_name, requester_role, requester_dept, permission_requested, reason)
            VALUES (?,?,?,?,?,?,?,?,?)");
        $stmt->execute([
            $d['document_id'],
            $docInfo['document_code'] ?? null,
            $docInfo['title'] ?? $d['document_title'] ?? null,
            $userId, $userName, $userRole, $userDept,
            $d['permission'] ?? 'view',
            $d['reason'] ?? null,
        ]);
        logAudit('documents', 'SUBMIT_ACCESS_REQUEST', 'document_access_requests', $db->lastInsertId(), null, [
            'document_id' => $d['document_id'], 'permission' => $d['permission'] ?? 'view'
        ]);
        jsonResponse(['success' => true, 'request_id' => $db->lastInsertId()], 201);

    case 'list_access_requests':
        $db = getDocDB();
        $status = $_GET['status'] ?? null;
        $sql = "SELECT * FROM document_access_requests WHERE 1=1";
        $params = [];
        if ($status) { $sql .= " AND status = ?"; $params[] = $status; }
        $sql .= " ORDER BY created_at DESC LIMIT 200";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        jsonResponse(['success' => true, 'data' => $stmt->fetchAll()]);

    case 'review_access_request':
        // Only privileged roles can approve/deny
        $userRole = $_SESSION['user']['role'] ?? 'staff';
        $PRIVILEGED_ROLES = ['super_admin', 'admin', 'manager', 'head_department'];
        if (!in_array($userRole, $PRIVILEGED_ROLES, true)) {
            http_response_code(403);
            jsonResponse(['success' => false, 'message' => 'Only Head Department, Admin, or Manager can review access requests.']);
        }
        $d = readJsonBody();
        $db = getDocDB();
        $reviewerId = intval($_SESSION['user']['user_id'] ?? 0);
        $reviewerName = trim(($_SESSION['user']['first_name'] ?? '') . ' ' . ($_SESSION['user']['last_name'] ?? $_SESSION['user']['name'] ?? ''));
        $newStatus = $d['status']; // 'approved' or 'denied'
        $notes = $d['review_notes'] ?? null;

        // Update request
        $stmt = $db->prepare("UPDATE document_access_requests SET status=?, reviewed_by=?, reviewer_name=?, review_notes=?, reviewed_at=NOW(), 
            expires_at = CASE WHEN ? = 'approved' THEN DATE_ADD(NOW(), INTERVAL 30 DAY) ELSE NULL END
            WHERE request_id=?");
        $stmt->execute([$newStatus, $reviewerId, $reviewerName, $notes, $newStatus, $d['request_id']]);

        // If approved, also create a document_access grant
        if ($newStatus === 'approved') {
            $stmt = $db->prepare("SELECT document_id, requested_by, permission_requested FROM document_access_requests WHERE request_id=?");
            $stmt->execute([$d['request_id']]);
            $req = $stmt->fetch();
            if ($req) {
                $stmt = $db->prepare("INSERT INTO document_access (document_id, user_id, permission, granted_by, expires_at) 
                    VALUES (?,?,?,?,DATE_ADD(NOW(), INTERVAL 30 DAY)) 
                    ON DUPLICATE KEY UPDATE permission=VALUES(permission), granted_by=VALUES(granted_by), expires_at=VALUES(expires_at)");
                $stmt->execute([$req['document_id'], $req['requested_by'], $req['permission_requested'], $reviewerId]);
            }
        }

        logAudit('documents', 'REVIEW_ACCESS_REQUEST', 'document_access_requests', intval($d['request_id']), null, [
            'status' => $newStatus, 'notes' => $notes
        ]);
        jsonResponse(['success' => true]);

    case 'access_request_stats':
        $db = getDocDB();
        $stats = [];
        $stats['total_requests']  = $db->query("SELECT COUNT(*) FROM document_access_requests")->fetchColumn() ?: 0;
        $stats['pending']         = $db->query("SELECT COUNT(*) FROM document_access_requests WHERE status='pending'")->fetchColumn() ?: 0;
        $stats['approved']        = $db->query("SELECT COUNT(*) FROM document_access_requests WHERE status='approved'")->fetchColumn() ?: 0;
        $stats['denied']          = $db->query("SELECT COUNT(*) FROM document_access_requests WHERE status='denied'")->fetchColumn() ?: 0;
        jsonResponse(['success' => true, 'data' => $stats]);

    case 'serve_file':
        // Serve the actual file for viewing in the browser
        $docId = intval($_GET['document_id'] ?? 0);
        $userId = intval($_SESSION['user']['user_id'] ?? 0);
        $userRole = $_SESSION['user']['role'] ?? 'staff';

        if (!$docId) { http_response_code(400); echo 'Missing document_id'; exit; }

        $db = getDocDB();
        $stmt = $db->prepare("SELECT * FROM documents WHERE document_id=?");
        $stmt->execute([$docId]);
        $doc = $stmt->fetch();
        if (!$doc) { http_response_code(404); echo 'Document not found'; exit; }

        // Build absolute path
        $filePath = __DIR__ . '/..' . $doc['file_path'];
        if (!file_exists($filePath)) {
            // Fallback: maybe file_path is relative from uploads
            $filePath = __DIR__ . '/../uploads/documents/' . basename($doc['file_name']);
        }

        // Determine MIME type
        $mime = $doc['file_type'] ?: 'application/octet-stream';
        if (stripos($mime, '/') === false) {
            // file_type might just be 'pdf', 'png' etc.
            $extMap = [
                'pdf'  => 'application/pdf',
                'png'  => 'image/png',
                'jpg'  => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'gif'  => 'image/gif',
                'webp' => 'image/webp',
                'svg'  => 'image/svg+xml',
                'bmp'  => 'image/bmp',
            ];
            $ext = strtolower(trim($mime, '.'));
            $mime = $extMap[$ext] ?? 'application/octet-stream';
        }

        if (file_exists($filePath)) {
            header('Content-Type: ' . $mime);
            header('Content-Disposition: inline; filename="' . basename($doc['file_name']) . '"');
            header('Content-Length: ' . filesize($filePath));
            header('Cache-Control: private, max-age=300');
            readfile($filePath);
        } else {
            // File doesn't exist on disk — return a placeholder
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode(['error' => 'File not available on disk', 'file_path' => $doc['file_path']]);
        }
        exit;

    case 'download_file':
        // Force download with Content-Disposition: attachment
        $docId = intval($_GET['document_id'] ?? 0);
        $userId = intval($_SESSION['user']['user_id'] ?? 0);
        $userRole = $_SESSION['user']['role'] ?? 'staff';

        if (!$docId) { http_response_code(400); echo 'Missing document_id'; exit; }

        $db = getDocDB();
        $stmt = $db->prepare("SELECT * FROM documents WHERE document_id=?");
        $stmt->execute([$docId]);
        $doc = $stmt->fetch();
        if (!$doc) { http_response_code(404); echo 'Document not found'; exit; }

        $filePath = __DIR__ . '/..' . $doc['file_path'];
        if (!file_exists($filePath)) {
            $filePath = __DIR__ . '/../uploads/documents/' . basename($doc['file_name']);
        }

        $mime = $doc['file_type'] ?: 'application/octet-stream';

        if (file_exists($filePath)) {
            header('Content-Type: ' . $mime);
            header('Content-Disposition: attachment; filename="' . basename($doc['file_name']) . '"');
            header('Content-Length: ' . filesize($filePath));
            readfile($filePath);
        } else {
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'File not available for download']);
        }
        exit;

    // ─── Folder Security PIN (Email-based, session-stored) ──────────────────

    case 'send_folder_pin':
        // Generate 4-digit PIN & email it (same pattern as archive PIN)
        $pin = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        $_SESSION['folder_pin'] = $pin;
        $_SESSION['folder_pin_created'] = time();

        $d = readJsonBody();
        $folder = $d['folder'] ?? $_GET['folder'] ?? 'Department';
        $sent = sendFolderPinEmail($pin, $folder);

        if ($sent === true) {
            jsonResponse([
                'success' => true,
                'message' => 'Security PIN sent to your email.',
                'expires_in' => 120
            ]);
        } else {
            // Dev fallback if mail fails
            jsonResponse([
                'success' => true,
                'mail_failed' => true,
                'fallback_pin' => $pin,
                'message' => 'Email could not be sent. Use the PIN shown on screen.',
                'expires_in' => 120
            ]);
        }

    case 'verify_folder_pin':
        $d = readJsonBody();
        $userPin = trim($d['pin'] ?? '');
        $folder  = $d['folder'] ?? '';
        $userId  = $_SESSION['user']['user_id'] ?? 0;
        $role    = $_SESSION['user']['role'] ?? 'staff';

        if (empty($userPin)) {
            http_response_code(400);
            jsonResponse(['success' => false, 'message' => 'PIN is required.']);
        }

        // Check expiry (120 seconds)
        $elapsed = time() - ($_SESSION['folder_pin_created'] ?? 0);
        if ($elapsed > 120) {
            unset($_SESSION['folder_pin'], $_SESSION['folder_pin_created']);
            http_response_code(410);
            jsonResponse(['success' => false, 'expired' => true, 'message' => 'PIN has expired. Please request a new one.']);
        }

        // Verify against session PIN
        if ($userPin === ($_SESSION['folder_pin'] ?? '')) {
            // Per-folder verified tracking
            if (!isset($_SESSION['folder_pins_verified'])) $_SESSION['folder_pins_verified'] = [];
            $_SESSION['folder_pins_verified'][$folder] = time();
            unset($_SESSION['folder_pin'], $_SESSION['folder_pin_created']);
            try {
                logAudit('documents', 'VERIFY_FOLDER_PIN', 'department_folders', null, null, [
                    'folder' => $folder,
                    'user_id' => $userId,
                    'role' => $role,
                    'verified' => true
                ]);
            } catch (\Exception $e) {}
            jsonResponse(['success' => true, 'message' => 'PIN verified. Folder access granted.']);
        } else {
            try {
                logAudit('documents', 'FOLDER_ACCESS_DENIED', 'department_folders', null, null, [
                    'folder' => $folder,
                    'user_id' => $userId,
                    'role' => $role,
                    'reason' => 'invalid_pin'
                ]);
            } catch (\Exception $e) {}
            http_response_code(401);
            jsonResponse(['success' => false, 'message' => 'Invalid PIN. Please try again.']);
        }

    case 'check_folder_access':
        $folder = $_GET['folder'] ?? '';
        $verified = false;
        if ($folder && isset($_SESSION['folder_pins_verified'][$folder])) {
            $elapsed = time() - $_SESSION['folder_pins_verified'][$folder];
            $verified = $elapsed <= 300; // 5-minute window per folder
        }
        jsonResponse(['verified' => $verified, 'folder' => $folder]);

    default:
        jsonResponse(['error' => 'Invalid action'], 400);
}

// ═══════════════════════════════════════════
// HELPER: Send Archive Security PIN Email
// ═══════════════════════════════════════════

function sendArchivePinEmail(string $pin) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = MAIL_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = MAIL_USERNAME;
        $mail->Password   = MAIL_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = MAIL_PORT;
        $mail->Timeout    = 10;
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer'       => false,
                'verify_peer_name'  => false,
                'allow_self_signed' => true,
            ],
        ];

        $mail->setFrom(MAIL_FROM_EMAIL, MAIL_FROM_NAME);
        $mail->addAddress(OTP_RECIPIENT);

        $logoPath = __DIR__ . '/../assets/images/logo.png';
        if (file_exists($logoPath)) {
            $mail->addEmbeddedImage($logoPath, 'company_logo', 'logo.png');
        }

        $mail->isHTML(true);
        $mail->Subject = 'Archive Security PIN - Microfinancial Admin';
        $mail->Body    = getArchivePinEmailTemplate($pin);
        $mail->AltBody = "Your Archive Security PIN is: {$pin}\nThis PIN expires in 120 seconds.\nDo not share this PIN with anyone.";

        $mail->send();
        return true;
    } catch (\Throwable $e) {
        return $mail->ErrorInfo ?: $e->getMessage();
    }
}

function getArchivePinEmailTemplate(string $pin): string {
    $year = date('Y');
    $userName = $_SESSION['user']['first_name'] ?? 'User';

    $digits = str_split($pin);
    $digitBoxes = '';
    foreach ($digits as $d) {
        $digitBoxes .= "<td style=\"padding:0 6px;\"><div style=\"width:52px;height:60px;line-height:60px;text-align:center;font-size:32px;font-weight:700;color:#D97706;background:#FFFBEB;border:2px solid #FDE68A;border-radius:12px;font-family:'Segoe UI',Arial,sans-serif;\">{$d}</div></td>";
    }

    return "
    <div style=\"background-color:#f3f4f6;padding:40px 20px;font-family:'Segoe UI',Roboto,Arial,sans-serif;\">
      <div style=\"max-width:520px;margin:0 auto;background:#ffffff;border-radius:20px;overflow:hidden;box-shadow:0 8px 40px rgba(0,0,0,0.10);\">

        <!-- Header -->
        <div style=\"background:linear-gradient(135deg,#D97706 0%,#B45309 50%,#92400E 100%);padding:36px 24px 28px;text-align:center;\">
          <img src=\"cid:company_logo\" alt=\"Microfinancial\" style=\"width:72px;height:72px;margin:0 auto 12px;display:block;border-radius:50%;background:#fff;padding:4px;box-shadow:0 4px 12px rgba(0,0,0,0.15);\" />
          <h1 style=\"margin:0;color:#ffffff;font-size:24px;font-weight:700;letter-spacing:0.5px;\">Archive Security PIN</h1>
          <p style=\"margin:6px 0 0;color:rgba(255,255,255,0.80);font-size:13px;\">Document Management &mdash; Archiving Module</p>
        </div>

        <!-- Body -->
        <div style=\"padding:36px 32px 28px;text-align:center;\">
          <div style=\"width:56px;height:56px;margin:0 auto 16px;background:#FEF3C7;border-radius:50%;text-align:center;line-height:56px;\">
            <span style=\"font-size:28px;\">&#128274;</span>
          </div>
          <h2 style=\"margin:0 0 8px;color:#1F2937;font-size:22px;font-weight:700;\">Archive Access Verification</h2>
          <p style=\"color:#6B7280;font-size:15px;margin:0 0 28px;line-height:1.6;\">
            Hi <strong style=\"color:#1F2937;\">{$userName}</strong>, use the 4-digit security PIN below to access the archive management functions.
          </p>

          <!-- PIN Code -->
          <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" style=\"margin:0 auto 28px;\">
            <tr>{$digitBoxes}</tr>
          </table>

          <!-- Warning -->
          <div style=\"background:linear-gradient(135deg,#FFFBEB,#FEF3C7);border:1px solid #FDE68A;border-radius:12px;padding:14px 20px;margin:0 0 24px;text-align:left;\">
            <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"100%\">
              <tr>
                <td style=\"width:28px;vertical-align:top;padding-top:2px;\"><span style=\"font-size:18px;\">&#9200;</span></td>
                <td style=\"color:#92400E;font-size:13px;line-height:1.5;\">
                  This PIN expires in <strong>120 seconds</strong>.<br/>
                  Do not share this PIN with anyone. It grants access to archive management operations.
                </td>
              </tr>
            </table>
          </div>

          <!-- Security Note -->
          <div style=\"border-top:1px solid #E5E7EB;padding-top:20px;\">
            <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"100%\">
              <tr>
                <td style=\"width:28px;vertical-align:top;padding-top:2px;\"><span style=\"font-size:16px;\">&#128737;</span></td>
                <td style=\"color:#9CA3AF;font-size:12px;line-height:1.5;\">
                  This PIN was requested for the Document Archiving module. If you did not request this, please contact your system administrator.
                </td>
              </tr>
            </table>
          </div>
        </div>

        <!-- Footer -->
        <div style=\"background:#F9FAFB;padding:20px 24px;text-align:center;border-top:1px solid #E5E7EB;\">
          <p style=\"margin:0 0 4px;color:#6B7280;font-size:12px;font-weight:600;\">Microfinancial Management System</p>
          <p style=\"margin:0;color:#9CA3AF;font-size:11px;\">&copy; {$year} All Rights Reserved &mdash; This is an automated message.</p>
        </div>

      </div>
    </div>";
}

// ═══════════════════════════════════════════
// HELPER: Send Confidential Document Security PIN Email
// ═══════════════════════════════════════════

function sendConfidentialPinEmail(string $pin) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = MAIL_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = MAIL_USERNAME;
        $mail->Password   = MAIL_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = MAIL_PORT;
        $mail->Timeout    = 10;
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer'       => false,
                'verify_peer_name'  => false,
                'allow_self_signed' => true,
            ],
        ];

        $mail->setFrom(MAIL_FROM_EMAIL, MAIL_FROM_NAME);
        $mail->addAddress(OTP_RECIPIENT);

        $logoPath = __DIR__ . '/../assets/images/logo.png';
        if (file_exists($logoPath)) {
            $mail->addEmbeddedImage($logoPath, 'company_logo', 'logo.png');
        }

        $mail->isHTML(true);
        $mail->Subject = 'Confidential Document PIN - Microfinancial Admin';
        $mail->Body    = getConfidentialPinEmailTemplate($pin);
        $mail->AltBody = "Your Confidential Document Security PIN is: {$pin}\nThis PIN expires in 120 seconds.\nDo not share this PIN with anyone.";

        $mail->send();
        return true;
    } catch (\Throwable $e) {
        return $mail->ErrorInfo ?: $e->getMessage();
    }
}

function getConfidentialPinEmailTemplate(string $pin): string {
    $year = date('Y');
    $userName = $_SESSION['user']['first_name'] ?? 'User';

    $digits = str_split($pin);
    $digitBoxes = '';
    foreach ($digits as $d) {
        $digitBoxes .= "<td style=\"padding:0 6px;\"><div style=\"width:52px;height:60px;line-height:60px;text-align:center;font-size:32px;font-weight:700;color:#DC2626;background:#FEF2F2;border:2px solid #FECACA;border-radius:12px;font-family:'Segoe UI',Arial,sans-serif;\">{$d}</div></td>";
    }

    return "
    <div style=\"background-color:#f3f4f6;padding:40px 20px;font-family:'Segoe UI',Roboto,Arial,sans-serif;\">
      <div style=\"max-width:520px;margin:0 auto;background:#ffffff;border-radius:20px;overflow:hidden;box-shadow:0 8px 40px rgba(0,0,0,0.10);\">

        <!-- Header -->
        <div style=\"background:linear-gradient(135deg,#DC2626 0%,#B91C1C 50%,#991B1B 100%);padding:36px 24px 28px;text-align:center;\">
          <img src=\"cid:company_logo\" alt=\"Microfinancial\" style=\"width:72px;height:72px;margin:0 auto 12px;display:block;border-radius:50%;background:#fff;padding:4px;box-shadow:0 4px 12px rgba(0,0,0,0.15);\" />
          <h1 style=\"margin:0;color:#ffffff;font-size:24px;font-weight:700;letter-spacing:0.5px;\">Confidential Document PIN</h1>
          <p style=\"margin:6px 0 0;color:rgba(255,255,255,0.80);font-size:13px;\">Document Management &mdash; Access Verification</p>
        </div>

        <!-- Body -->
        <div style=\"padding:36px 32px 28px;text-align:center;\">
          <div style=\"width:56px;height:56px;margin:0 auto 16px;background:#FEE2E2;border-radius:50%;text-align:center;line-height:56px;\">
            <span style=\"font-size:28px;\">&#128274;</span>
          </div>
          <h2 style=\"margin:0 0 8px;color:#1F2937;font-size:22px;font-weight:700;\">Confidential Access Verification</h2>
          <p style=\"color:#6B7280;font-size:15px;margin:0 0 28px;line-height:1.6;\">
            Hi <strong style=\"color:#1F2937;\">{$userName}</strong>, use the 4-digit security PIN below to access restricted and confidential documents.
          </p>

          <!-- PIN Code -->
          <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" style=\"margin:0 auto 28px;\">
            <tr>{$digitBoxes}</tr>
          </table>

          <!-- Warning -->
          <div style=\"background:linear-gradient(135deg,#FEF2F2,#FEE2E2);border:1px solid #FECACA;border-radius:12px;padding:14px 20px;margin:0 0 24px;text-align:left;\">
            <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"100%\">
              <tr>
                <td style=\"width:28px;vertical-align:top;padding-top:2px;\"><span style=\"font-size:18px;\">&#9200;</span></td>
                <td style=\"color:#991B1B;font-size:13px;line-height:1.5;\">
                  This PIN expires in <strong>120 seconds</strong>.<br/>
                  Do not share this PIN with anyone. It grants access to confidential documents.
                </td>
              </tr>
            </table>
          </div>

          <!-- Security Note -->
          <div style=\"border-top:1px solid #E5E7EB;padding-top:20px;\">
            <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"100%\">
              <tr>
                <td style=\"width:28px;vertical-align:top;padding-top:2px;\"><span style=\"font-size:16px;\">&#128737;</span></td>
                <td style=\"color:#9CA3AF;font-size:12px;line-height:1.5;\">
                  This PIN was requested for accessing restricted/confidential documents. If you did not request this, please contact your system administrator.
                </td>
              </tr>
            </table>
          </div>
        </div>

        <!-- Footer -->
        <div style=\"background:#F9FAFB;padding:20px 24px;text-align:center;border-top:1px solid #E5E7EB;\">
          <p style=\"margin:0 0 4px;color:#6B7280;font-size:12px;font-weight:600;\">Microfinancial Management System</p>
          <p style=\"margin:0;color:#9CA3AF;font-size:11px;\">&copy; {$year} All Rights Reserved &mdash; This is an automated message.</p>
        </div>

      </div>
    </div>";
}

// ═══════════════════════════════════════════
// HELPER: Send Folder Security PIN Email
// ═══════════════════════════════════════════

function sendFolderPinEmail(string $pin, string $folder) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = MAIL_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = MAIL_USERNAME;
        $mail->Password   = MAIL_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = MAIL_PORT;
        $mail->Timeout    = 10;
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer'       => false,
                'verify_peer_name'  => false,
                'allow_self_signed' => true,
            ],
        ];

        $mail->setFrom(MAIL_FROM_EMAIL, MAIL_FROM_NAME);
        $mail->addAddress(OTP_RECIPIENT);

        $logoPath = __DIR__ . '/../assets/images/logo.png';
        if (file_exists($logoPath)) {
            $mail->addEmbeddedImage($logoPath, 'company_logo', 'logo.png');
        }

        $mail->isHTML(true);
        $mail->Subject = 'Folder Security PIN - ' . $folder . ' - Microfinancial Admin';
        $mail->Body    = getFolderPinEmailTemplate($pin, $folder);
        $mail->AltBody = "Your Folder Security PIN for {$folder} is: {$pin}\nThis PIN expires in 120 seconds.\nDo not share this PIN with anyone.";

        $mail->send();
        return true;
    } catch (\Throwable $e) {
        return $mail->ErrorInfo ?: $e->getMessage();
    }
}

function getFolderPinEmailTemplate(string $pin, string $folder): string {
    $year = date('Y');
    $userName = $_SESSION['user']['first_name'] ?? 'User';

    $digits = str_split($pin);
    $digitBoxes = '';
    foreach ($digits as $d) {
        $digitBoxes .= "<td style=\"padding:0 6px;\"><div style=\"width:52px;height:60px;line-height:60px;text-align:center;font-size:32px;font-weight:700;color:#059669;background:#ECFDF5;border:2px solid #A7F3D0;border-radius:12px;font-family:'Segoe UI',Arial,sans-serif;\">{$d}</div></td>";
    }

    return "
    <div style=\"background-color:#f3f4f6;padding:40px 20px;font-family:'Segoe UI',Roboto,Arial,sans-serif;\">
      <div style=\"max-width:520px;margin:0 auto;background:#ffffff;border-radius:20px;overflow:hidden;box-shadow:0 8px 40px rgba(0,0,0,0.10);\">

        <!-- Header -->
        <div style=\"background:linear-gradient(135deg,#059669 0%,#047857 50%,#065F46 100%);padding:36px 24px 28px;text-align:center;\">
          <img src=\"cid:company_logo\" alt=\"Microfinancial\" style=\"width:72px;height:72px;margin:0 auto 12px;display:block;border-radius:50%;background:#fff;padding:4px;box-shadow:0 4px 12px rgba(0,0,0,0.15);\" />
          <h1 style=\"margin:0;color:#ffffff;font-size:24px;font-weight:700;letter-spacing:0.5px;\">Folder Security PIN</h1>
          <p style=\"margin:6px 0 0;color:rgba(255,255,255,0.80);font-size:13px;\">Department Folders &mdash; {$folder}</p>
        </div>

        <!-- Body -->
        <div style=\"padding:36px 32px 28px;text-align:center;\">
          <div style=\"width:56px;height:56px;margin:0 auto 16px;background:#ECFDF5;border-radius:50%;text-align:center;line-height:56px;\">
            <span style=\"font-size:28px;\">&#128274;</span>
          </div>
          <h2 style=\"margin:0 0 8px;color:#1F2937;font-size:22px;font-weight:700;\">Folder Access Verification</h2>
          <p style=\"color:#6B7280;font-size:15px;margin:0 0 28px;line-height:1.6;\">
            Hi <strong style=\"color:#1F2937;\">{$userName}</strong>, use the 4-digit security PIN below to access the <strong style=\"color:#059669;\">{$folder}</strong> department folder.
          </p>

          <!-- PIN Code -->
          <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" style=\"margin:0 auto 28px;\">
            <tr>{$digitBoxes}</tr>
          </table>

          <!-- Warning -->
          <div style=\"background:linear-gradient(135deg,#ECFDF5,#D1FAE5);border:1px solid #A7F3D0;border-radius:12px;padding:14px 20px;margin:0 0 24px;text-align:left;\">
            <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"100%\">
              <tr>
                <td style=\"width:28px;vertical-align:top;padding-top:2px;\"><span style=\"font-size:18px;\">&#9200;</span></td>
                <td style=\"color:#065F46;font-size:13px;line-height:1.5;\">
                  This PIN expires in <strong>120 seconds</strong>.<br/>
                  Do not share this PIN with anyone. It grants access to secured department folders.
                </td>
              </tr>
            </table>
          </div>

          <!-- Security Note -->
          <div style=\"border-top:1px solid #E5E7EB;padding-top:20px;\">
            <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"100%\">
              <tr>
                <td style=\"width:28px;vertical-align:top;padding-top:2px;\"><span style=\"font-size:16px;\">&#128737;</span></td>
                <td style=\"color:#9CA3AF;font-size:12px;line-height:1.5;\">
                  This PIN was requested for accessing the {$folder} department folder. If you did not request this, please contact your system administrator.
                </td>
              </tr>
            </table>
          </div>
        </div>

        <!-- Footer -->
        <div style=\"background:#F9FAFB;padding:20px 24px;text-align:center;border-top:1px solid #E5E7EB;\">
          <p style=\"margin:0 0 4px;color:#6B7280;font-size:12px;font-weight:600;\">Microfinancial Management System</p>
          <p style=\"margin:0;color:#9CA3AF;font-size:11px;\">&copy; {$year} All Rights Reserved &mdash; This is an automated message.</p>
        </div>

      </div>
    </div>";
}
