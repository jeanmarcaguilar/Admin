<?php
/**
 * MICROFINANCIAL MANAGEMENT SYSTEM I
 * API: Visitor Management Module
 * 
 * Endpoints (via ?action=...):
 *   GET  list_visitors          – Visitor registry
 *   GET  list_logs              – Visit logs
 *   GET  list_preregistrations  – Pre-registrations
 *   GET  dashboard_stats        – Summary counts
 *   POST register_visitor       – New visitor
 *   POST check_in               – Check-in a visitor
 *   POST check_out              – Check-out a visitor
 *   POST preregister            – Pre-register a visitor
 *   POST approve_prereg         – Approve and auto-register a pre-registration
 *   POST reject_prereg          – Reject a pre-registration
 */

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

// Ensure all errors return JSON instead of raw HTML
set_error_handler(function ($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});
set_exception_handler(function ($e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
    exit;
});

session_start();
if (empty($_SESSION['authenticated'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/audit.php';

// ── Auto-migrate VIP columns if they don't exist ──
(function() {
    $db = getDB();
    try {
        $cols = $db->query("SHOW COLUMNS FROM visitors")->fetchAll(PDO::FETCH_COLUMN);
        if (!in_array('visitor_type', $cols)) {
            $db->exec("ALTER TABLE `visitors` ADD COLUMN `visitor_type` ENUM('regular','vip','contractor','government_official') NOT NULL DEFAULT 'regular' AFTER `company`");
            $db->exec("ALTER TABLE `visitors` ADD INDEX `idx_visitor_type` (`visitor_type`)");
        }
    } catch (Exception $e) { /* ignore */ }
    try {
        $cols = $db->query("SHOW COLUMNS FROM visitor_preregistrations")->fetchAll(PDO::FETCH_COLUMN);
        if (!in_array('visitor_type', $cols)) {
            $db->exec("ALTER TABLE `visitor_preregistrations` ADD COLUMN `visitor_type` ENUM('regular','vip','contractor','government_official') NOT NULL DEFAULT 'regular' AFTER `visitor_company`");
            $db->exec("ALTER TABLE `visitor_preregistrations` ADD COLUMN `security_level` ENUM('standard','elevated','high','executive') NOT NULL DEFAULT 'standard' AFTER `visitor_type`");
            $db->exec("ALTER TABLE `visitor_preregistrations` ADD COLUMN `parking_required` TINYINT(1) NOT NULL DEFAULT 0 AFTER `security_level`");
            $db->exec("ALTER TABLE `visitor_preregistrations` ADD COLUMN `escort_required` TINYINT(1) NOT NULL DEFAULT 0 AFTER `parking_required`");
        }
        // Link pre-reg to the registered visitor_code once approved
        if (!in_array('visitor_code', $cols)) {
            $db->exec("ALTER TABLE `visitor_preregistrations` ADD COLUMN `visitor_code` VARCHAR(30) NULL DEFAULT NULL AFTER `prereg_code`");
        }
    } catch (Exception $e) { /* ignore */ }
    try {
        $cols = $db->query("SHOW COLUMNS FROM visitor_logs")->fetchAll(PDO::FETCH_COLUMN);
        if (!in_array('visitor_type', $cols)) {
            $db->exec("ALTER TABLE `visitor_logs` ADD COLUMN `visitor_type` ENUM('regular','vip','contractor','government_official') NOT NULL DEFAULT 'regular' AFTER `purpose_details`");
            $db->exec("ALTER TABLE `visitor_logs` ADD COLUMN `security_level` ENUM('standard','elevated','high','executive') NOT NULL DEFAULT 'standard' AFTER `visitor_type`");
            $db->exec("ALTER TABLE `visitor_logs` ADD COLUMN `escort_required` TINYINT(1) NOT NULL DEFAULT 0 AFTER `security_level`");
            $db->exec("ALTER TABLE `visitor_logs` ADD COLUMN `id_verified` TINYINT(1) NOT NULL DEFAULT 0 AFTER `escort_required`");
            $db->exec("ALTER TABLE `visitor_logs` ADD COLUMN `access_level` ENUM('lobby_only','general','executive_floor','all_access') NOT NULL DEFAULT 'general' AFTER `id_verified`");
            $db->exec("ALTER TABLE `visitor_logs` ADD INDEX `idx_log_visitor_type` (`visitor_type`)");
        }
    } catch (Exception $e) { /* ignore */ }
})();

$action = $_GET['action'] ?? '';

switch ($action) {

    case 'list_hosts':
        $rows = getDB()->query("SELECT user_id, first_name, last_name, role, department FROM users ORDER BY first_name ASC")->fetchAll();
        jsonResponse(['data' => $rows]);

    case 'list_visitors':
        $rows = getDB()->query("SELECT * FROM visitors ORDER BY created_at DESC")->fetchAll();
        jsonResponse(['data' => $rows]);

    case 'list_logs':
        $status = $_GET['status'] ?? null;
        $sql = "SELECT vl.*, CONCAT(v.first_name,' ',v.last_name) AS visitor_name, v.company,
                       CONCAT(u.first_name,' ',u.last_name) AS host_user_name
                FROM visitor_logs vl
                JOIN visitors v ON vl.visitor_id = v.visitor_id
                LEFT JOIN users u ON vl.host_user_id = u.user_id";
        $params = [];
        if ($status) { $sql .= " WHERE vl.status = ?"; $params[] = $status; }
        $sql .= " ORDER BY vl.check_in_time DESC";
        $stmt = getDB()->prepare($sql);
        $stmt->execute($params);
        jsonResponse(['data' => $stmt->fetchAll()]);

    case 'list_preregistrations':
        $rows = getDB()->query("SELECT p.*, CONCAT(u.first_name,' ',u.last_name) AS host_name
            FROM visitor_preregistrations p
            JOIN users u ON p.host_user_id = u.user_id
            ORDER BY p.expected_date DESC")->fetchAll();
        jsonResponse(['data' => $rows]);

    case 'register_visitor':
        $d = readJsonBody();
        $code = 'VIS-' . str_pad(random_int(1,999999),6,'0',STR_PAD_LEFT);
        
        // Handle photo upload if provided
        $photoUrl = null;
        if (!empty($d['photo_base64'])) {
            $photoUrl = saveVisitorPhoto($d['photo_base64'], $code);
        }
        
        $visitorType = $d['visitor_type'] ?? 'regular';
        $stmt = getDB()->prepare("INSERT INTO visitors 
            (visitor_code, first_name, last_name, email, phone, company, visitor_type, id_type, id_number, photo_url)
            VALUES (?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute([
            $code, $d['first_name'], $d['last_name'], $d['email'] ?? null,
            $d['phone'] ?? null, $d['company'] ?? null,
            $visitorType,
            $d['id_type'] ?? null, $d['id_number'] ?? null,
            $photoUrl
        ]);
        logAudit('visitors', 'REGISTER_VISITOR', 'visitors', null, null, ['visitor_code' => $code, 'name' => ($d['first_name'] ?? '') . ' ' . ($d['last_name'] ?? ''), 'visitor_type' => $visitorType]);
        jsonResponse(['success' => true, 'visitor_code' => $code, 'visitor_type' => $visitorType], 201);

    case 'check_in':
        $d = readJsonBody();
        $visitCode = 'VL-' . date('Y') . '-' . str_pad(random_int(1,999999),6,'0',STR_PAD_LEFT);
        // Look up visitor type from visitor record
        $vtStmt = getDB()->prepare("SELECT visitor_type FROM visitors WHERE visitor_id = ?");
        $vtStmt->execute([$d['visitor_id']]);
        $vtRow = $vtStmt->fetch();
        $visitorType = $vtRow['visitor_type'] ?? ($d['visitor_type'] ?? 'regular');
        // VIP auto-escalation: executive access + escort
        $securityLevel = $d['security_level'] ?? 'standard';
        $escortRequired = intval($d['escort_required'] ?? 0);
        $idVerified = intval($d['id_verified'] ?? 0);
        $accessLevel = $d['access_level'] ?? 'general';
        if ($visitorType === 'vip' || $visitorType === 'government_official') {
            if ($securityLevel === 'standard') $securityLevel = 'elevated';
            if ($accessLevel === 'general') $accessLevel = 'executive_floor';
        }
        $stmt = getDB()->prepare("INSERT INTO visitor_logs 
            (visit_code, visitor_id, host_user_id, host_name, host_department, purpose,
             purpose_details, visitor_type, security_level, escort_required, id_verified, access_level,
             check_in_time, badge_number, status, created_by)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,NOW(),?,'checked_in',?)");
        $stmt->execute([
            $visitCode, $d['visitor_id'], $d['host_user_id'] ?? null,
            $d['host_name'] ?? null, $d['host_department'] ?? null,
            $d['purpose'], $d['purpose_details'] ?? null,
            $visitorType, $securityLevel, $escortRequired, $idVerified, $accessLevel,
            $d['badge_number'] ?? null, $d['created_by'] ?? 1
        ]);
        // Update visit count
        getDB()->prepare("UPDATE visitors SET visit_count = visit_count + 1 WHERE visitor_id = ?")->execute([$d['visitor_id']]);
        logAudit('visitors', 'CHECK_IN', 'visitor_logs', null, null, ['visit_code' => $visitCode, 'visitor_id' => $d['visitor_id'], 'visitor_type' => $visitorType]);
        jsonResponse(['success' => true, 'visit_code' => $visitCode, 'visitor_type' => $visitorType], 201);

    case 'check_out':
        $d = readJsonBody();
        $stmt = getDB()->prepare("UPDATE visitor_logs SET check_out_time = NOW(), status = 'checked_out' WHERE log_id = ?");
        $stmt->execute([$d['log_id']]);
        logAudit('visitors', 'CHECK_OUT', 'visitor_logs', intval($d['log_id']), null, null);
        jsonResponse(['success' => true]);

    case 'preregister':
        $d = readJsonBody();
        $code = 'PR-' . date('Y') . '-' . str_pad(random_int(1,999999),6,'0',STR_PAD_LEFT);
        $visitorType = $d['visitor_type'] ?? 'regular';
        $securityLevel = $d['security_level'] ?? 'standard';
        $parkingRequired = intval($d['parking_required'] ?? 0);
        $escortRequired = intval($d['escort_required'] ?? 0);
        // VIP auto-escalation
        if (($visitorType === 'vip' || $visitorType === 'government_official') && $securityLevel === 'standard') {
            $securityLevel = 'elevated';
        }
        
        try {
            $stmt = getDB()->prepare("INSERT INTO visitor_preregistrations 
                (prereg_code, visitor_name, visitor_email, visitor_phone, visitor_company,
                 visitor_type, security_level, parking_required, escort_required,
                 host_user_id, purpose, expected_date, expected_time, status)
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,'pending')");
            $stmt->execute([
                $code, $d['visitor_name'], $d['visitor_email'] ?? null,
                $d['visitor_phone'] ?? null, $d['visitor_company'] ?? null,
                $visitorType, $securityLevel, $parkingRequired, $escortRequired,
                $d['host_user_id'], $d['purpose'], $d['expected_date'],
                $d['expected_time'] ?? null
            ]);
            logAudit('visitors', 'PREREGISTER', 'visitor_preregistrations', null, null, ['prereg_code' => $code, 'visitor_name' => $d['visitor_name'], 'visitor_type' => $visitorType]);
            jsonResponse(['success' => true, 'prereg_code' => $code, 'visitor_type' => $visitorType], 201);
        } catch (PDOException $e) {
            jsonResponse(['error' => 'Pre-registration failed: ' . $e->getMessage()], 500);
        }

    case 'lookup_visitor':
        $code = $_GET['code'] ?? '';
        if (!$code) jsonResponse(['error' => 'No code provided'], 400);
        // Try visitor_code first
        $stmt = getDB()->prepare("SELECT * FROM visitors WHERE visitor_code = ?");
        $stmt->execute([$code]);
        $visitor = $stmt->fetch();
        if ($visitor) {
            // Get active log
            $logStmt = getDB()->prepare("SELECT * FROM visitor_logs WHERE visitor_id = ? AND status = 'checked_in' ORDER BY check_in_time DESC LIMIT 1");
            $logStmt->execute([$visitor['visitor_id']]);
            $activeLog = $logStmt->fetch();
            jsonResponse(['found' => true, 'type' => 'visitor', 'visitor' => $visitor, 'active_log' => $activeLog ?: null]);
        }
        // Try prereg_code
        $stmt = getDB()->prepare("SELECT p.*, CONCAT(u.first_name,' ',u.last_name) AS host_name FROM visitor_preregistrations p JOIN users u ON p.host_user_id = u.user_id WHERE p.prereg_code = ?");
        $stmt->execute([$code]);
        $prereg = $stmt->fetch();
        if ($prereg) {
            jsonResponse(['found' => true, 'type' => 'prereg', 'prereg' => $prereg]);
        }
        // Try visit_code
        $stmt = getDB()->prepare("SELECT vl.*, CONCAT(v.first_name,' ',v.last_name) AS visitor_name, v.company, v.visitor_code FROM visitor_logs vl JOIN visitors v ON vl.visitor_id = v.visitor_id WHERE vl.visit_code = ?");
        $stmt->execute([$code]);
        $log = $stmt->fetch();
        if ($log) {
            jsonResponse(['found' => true, 'type' => 'visit_log', 'log' => $log]);
        }
        jsonResponse(['found' => false, 'error' => 'No visitor found with code: ' . $code], 404);

    case 'approve_prereg':
        $d = readJsonBody();
        $code = $d['prereg_code'] ?? '';
        if (!$code) jsonResponse(['error' => 'No prereg_code provided'], 400);
        // Fetch the pre-registration
        $stmt = getDB()->prepare("SELECT * FROM visitor_preregistrations WHERE prereg_code = ? AND status = 'pending'");
        $stmt->execute([$code]);
        $pr = $stmt->fetch();
        if (!$pr) jsonResponse(['error' => 'Pre-registration not found or already processed'], 404);
        // Update status to approved
        getDB()->prepare("UPDATE visitor_preregistrations SET status = 'approved' WHERE prereg_code = ?")->execute([$code]);
        // Auto-register as a visitor
        $nameParts = explode(' ', $pr['visitor_name'], 2);
        $firstName = $nameParts[0];
        $lastName  = $nameParts[1] ?? '';
        $visitorCode = 'VIS-' . str_pad(random_int(1,999999),6,'0',STR_PAD_LEFT);
        $vType = $pr['visitor_type'] ?? 'regular';
        $ins = getDB()->prepare("INSERT INTO visitors (visitor_code, first_name, last_name, email, phone, company, visitor_type, id_type, id_number) VALUES (?,?,?,?,?,?,?,?,?)");
        $ins->execute([$visitorCode, $firstName, $lastName, $pr['visitor_email'], $pr['visitor_phone'], $pr['visitor_company'], $vType, $pr['id_type'] ?? null, $pr['id_number'] ?? null]);
        // Save visitor_code back onto the pre-registration so the upcoming list can filter precisely
        getDB()->prepare("UPDATE visitor_preregistrations SET visitor_code = ? WHERE prereg_code = ?")->execute([$visitorCode, $code]);
        logAudit('visitors', 'APPROVE_PREREG', 'visitor_preregistrations', null, ['status' => 'pending'], ['status' => 'approved', 'visitor_code' => $visitorCode, 'visitor_type' => $vType]);
        jsonResponse(['success' => true, 'visitor_code' => $visitorCode, 'visitor_type' => $vType, 'message' => 'Pre-registration approved. Visitor registered as ' . $visitorCode]);

    case 'reject_prereg':
        $d = readJsonBody();
        $code = $d['prereg_code'] ?? '';
        if (!$code) jsonResponse(['error' => 'No prereg_code provided'], 400);
        $stmt = getDB()->prepare("UPDATE visitor_preregistrations SET status = 'rejected' WHERE prereg_code = ? AND status = 'pending'");
        $stmt->execute([$code]);
        if ($stmt->rowCount() === 0) jsonResponse(['error' => 'Pre-registration not found or already processed'], 404);
        logAudit('visitors', 'REJECT_PREREG', 'visitor_preregistrations', null, ['status' => 'pending'], ['status' => 'rejected']);
        jsonResponse(['success' => true]);

    case 'dashboard_stats':
        $db = getDB();
        $stats = [];
        $stats['total_visitors'] = $db->query("SELECT COUNT(*) FROM visitors v WHERE EXISTS (SELECT 1 FROM visitor_logs vl WHERE vl.visitor_id = v.visitor_id AND vl.status = 'checked_in') OR NOT EXISTS (SELECT 1 FROM visitor_logs vl WHERE vl.visitor_id = v.visitor_id)")->fetchColumn();
        $stats['checked_in_now'] = $db->query("SELECT COUNT(*) FROM visitor_logs WHERE status='checked_in'")->fetchColumn();
        $stats['today_visits'] = $db->query("SELECT COUNT(*) FROM visitor_logs WHERE DATE(check_in_time)=CURDATE()")->fetchColumn();
        $stats['pending_preregs'] = $db->query("SELECT COUNT(*) FROM visitor_preregistrations WHERE status='pending'")->fetchColumn();
        $stats['blacklisted'] = $db->query("SELECT COUNT(*) FROM visitors WHERE is_blacklisted=1")->fetchColumn();
        $stats['total_visits_month'] = $db->query("SELECT COUNT(*) FROM visitor_logs WHERE MONTH(check_in_time)=MONTH(CURDATE()) AND YEAR(check_in_time)=YEAR(CURDATE())")->fetchColumn();
        $stats['avg_duration_min'] = $db->query("SELECT COALESCE(ROUND(AVG(TIMESTAMPDIFF(MINUTE, check_in_time, check_out_time)),0),0) FROM visitor_logs WHERE check_out_time IS NOT NULL AND MONTH(check_in_time)=MONTH(CURDATE())")->fetchColumn();
        $stats['top_company'] = $db->query("SELECT v.company FROM visitor_logs vl JOIN visitors v ON vl.visitor_id=v.visitor_id WHERE v.company IS NOT NULL GROUP BY v.company ORDER BY COUNT(*) DESC LIMIT 1")->fetchColumn() ?: 'N/A';
        // VIP stats
        $stats['vip_inside'] = $db->query("SELECT COUNT(*) FROM visitor_logs vl JOIN visitors v ON vl.visitor_id=v.visitor_id WHERE vl.status='checked_in' AND v.visitor_type='vip'")->fetchColumn();
        $stats['vip_today'] = $db->query("SELECT COUNT(*) FROM visitor_logs vl JOIN visitors v ON vl.visitor_id=v.visitor_id WHERE DATE(vl.check_in_time)=CURDATE() AND v.visitor_type='vip'")->fetchColumn();
        $stats['officials_inside'] = $db->query("SELECT COUNT(*) FROM visitor_logs vl JOIN visitors v ON vl.visitor_id=v.visitor_id WHERE vl.status='checked_in' AND v.visitor_type='government_official'")->fetchColumn();
        // VIP visitors currently inside (with details + pre-reg link)
        $vipInside = $db->query("SELECT v.first_name, v.last_name, v.company, v.visitor_type, v.photo_url,
            vl.purpose, vl.check_in_time, vl.host_name, vl.security_level, vl.access_level, vl.escort_required,
            EXISTS (
                SELECT 1 FROM visitor_preregistrations p
                WHERE p.visitor_code = v.visitor_code AND p.status = 'approved'
            ) AS was_preregistered
            FROM visitor_logs vl JOIN visitors v ON vl.visitor_id=v.visitor_id
            WHERE vl.status='checked_in' AND v.visitor_type IN ('vip','government_official')
            ORDER BY vl.check_in_time DESC")->fetchAll();
        $stats['vip_visitors_inside'] = $vipInside;
        // Upcoming VIP pre-registrations — exclude anyone already checked in
        $vipUpcoming = $db->query("SELECT p.visitor_name, p.visitor_company, p.visitor_type, p.purpose,
            p.expected_date, p.expected_time, p.security_level, p.escort_required, p.parking_required,
            p.visitor_code AS prereg_visitor_code,
            CONCAT(u.first_name,' ',u.last_name) AS host_name
            FROM visitor_preregistrations p
            JOIN users u ON p.host_user_id = u.user_id
            WHERE p.status IN ('pending','approved') AND p.visitor_type IN ('vip','government_official')
            AND p.expected_date >= CURDATE()
            AND NOT EXISTS (
                SELECT 1 FROM visitor_logs vl
                JOIN visitors v ON vl.visitor_id = v.visitor_id
                WHERE vl.status IN ('checked_in', 'checked_out')
                AND (
                    -- Precise match via stored visitor_code (set when pre-reg is approved)
                    (p.visitor_code IS NOT NULL AND v.visitor_code = p.visitor_code)
                    OR
                    -- Fallback: name match for pending pre-regs not yet linked
                    (p.visitor_code IS NULL AND CONCAT(v.first_name,' ',v.last_name) = p.visitor_name)
                )
            )
            ORDER BY p.expected_date ASC, p.expected_time ASC LIMIT 10")->fetchAll();
        $stats['vip_upcoming'] = $vipUpcoming;
        jsonResponse($stats);

    case 'upload_photo':
        $d = readJsonBody();
        $visitorId = intval($d['visitor_id'] ?? 0);
        if (!$visitorId) jsonResponse(['error' => 'No visitor_id provided'], 400);
        if (empty($d['photo_base64'])) jsonResponse(['error' => 'No photo data provided'], 400);
        
        // Check visitor exists
        $stmt = getDB()->prepare("SELECT visitor_code, photo_url FROM visitors WHERE visitor_id = ?");
        $stmt->execute([$visitorId]);
        $visitor = $stmt->fetch();
        if (!$visitor) jsonResponse(['error' => 'Visitor not found'], 404);
        
        // Delete old photo if exists
        if ($visitor['photo_url']) {
            $oldPath = __DIR__ . '/../' . $visitor['photo_url'];
            if (file_exists($oldPath)) unlink($oldPath);
        }
        
        // Save new photo
        $photoUrl = saveVisitorPhoto($d['photo_base64'], $visitor['visitor_code']);
        if (!$photoUrl) jsonResponse(['error' => 'Failed to save photo'], 500);
        
        // Update database
        getDB()->prepare("UPDATE visitors SET photo_url = ? WHERE visitor_id = ?")->execute([$photoUrl, $visitorId]);
        logAudit('visitors', 'UPLOAD_PHOTO', 'visitors', $visitorId, null, ['photo_url' => $photoUrl]);
        jsonResponse(['success' => true, 'photo_url' => $photoUrl]);

    default:
        jsonResponse(['error' => 'Invalid action'], 400);
}

// ═══════════════════════════════════════════════════════
// HELPER: Save base64 photo to disk
// ═══════════════════════════════════════════════════════
function saveVisitorPhoto(string $base64Data, string $visitorCode): ?string {
    // Strip data URL prefix if present
    if (preg_match('/^data:image\/(\w+);base64,/', $base64Data, $matches)) {
        $ext = strtolower($matches[1]);
        if ($ext === 'jpeg') $ext = 'jpg';
        $base64Data = substr($base64Data, strlen($matches[0]));
    } else {
        $ext = 'jpg';
    }
    
    $decoded = base64_decode($base64Data);
    if ($decoded === false) return null;
    
    // Validate file size (max 2MB)
    if (strlen($decoded) > 2 * 1024 * 1024) return null;
    
    $uploadDir = __DIR__ . '/../uploads/visitors/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $filename = strtolower($visitorCode) . '_' . time() . '.' . $ext;
    $filepath = $uploadDir . $filename;
    
    if (file_put_contents($filepath, $decoded) === false) return null;
    
    return 'uploads/visitors/' . $filename;
}
