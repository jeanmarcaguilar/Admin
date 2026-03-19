<?php
// Suppress PHP warnings/notices – API must return clean JSON only
ini_set('display_errors', 0);
error_reporting(0);
/**
 * MICROFINANCIAL MANAGEMENT SYSTEM I
 * API: Legal Management Module
 * 
 * Endpoints (via ?action=...):
 *   GET  list_cases          – Legal cases
 *   GET  list_contracts      – Contracts & agreements
 *   GET  list_compliance     – Compliance tracking
 *   GET  list_loans          – Loan documentation
 *   GET  list_collaterals    – Collateral registry
 *   GET  list_demands        – Demand letters
 *   GET  list_kyc            – KYC records
 *   GET  list_resolutions    – Board resolutions
 *   GET  list_poa            – Power of Attorney
 *   GET  list_permits        – Permits & licenses
 *   GET  list_hearings       – Case hearings
 *   GET  list_evidence       – Case evidence
 *   GET  list_notices        – Escalation notices
 *   GET  list_decision_matrix – Decision matrix rules
 *   GET  case_analytics      – Case analytics data
 *   GET  dashboard_stats     – Summary counts
 *   POST file_complaint      – File new complaint/case
 *   POST update_workflow     – Update case workflow step
 *   POST add_hearing         – Add case hearing
 *   POST add_evidence        – Upload case evidence
 *   POST generate_notice     – Generate escalation notice
 *   POST render_verdict      – Render case verdict
 *   POST check_escalations   – Check & auto-escalate overdue items
 *   POST create_case         – New legal case (legacy)
 *   POST create_contract     – New contract
 *   POST update_case_status  – Update case status
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

function readJsonBodyLegal() {
    return json_decode(file_get_contents('php://input'), true) ?: [];
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

// ═══════════════════════════════════════════
// LEGAL SECURITY PIN (4-digit, per-submodule)
// ═══════════════════════════════════════════

if (in_array($action, ['send_legal_pin', 'verify_legal_pin', 'check_legal_access'])) {
    switch ($action) {
        case 'send_legal_pin':
            $tab = $_GET['tab'] ?? $_POST['tab'] ?? 'Legal';
            $pin = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
            $_SESSION['legal_pin'] = $pin;
            $_SESSION['legal_pin_created'] = time();

            $sent = sendLegalPinEmail($pin, $tab);

            if ($sent === true) {
                echo json_encode(['success' => true, 'message' => 'Security PIN sent to your email.', 'expires_in' => 120]);
            } else {
                echo json_encode(['success' => true, 'mail_failed' => true, 'fallback_pin' => $pin, 'message' => 'Email could not be sent. Use the PIN shown on screen.', 'expires_in' => 120]);
            }
            exit;

        case 'verify_legal_pin':
            $d = readJsonBodyLegal();
            $userPin = trim($d['pin'] ?? '');
            $tab = $d['tab'] ?? '';

            if (empty($userPin)) { http_response_code(400); echo json_encode(['success' => false, 'message' => 'PIN is required.']); exit; }

            $elapsed = time() - ($_SESSION['legal_pin_created'] ?? 0);
            if ($elapsed > 120) {
                unset($_SESSION['legal_pin'], $_SESSION['legal_pin_created']);
                http_response_code(410);
                echo json_encode(['success' => false, 'expired' => true, 'message' => 'PIN has expired. Please request a new one.']);
                exit;
            }

            if ($userPin === ($_SESSION['legal_pin'] ?? '')) {
                if (!isset($_SESSION['legal_tabs_verified'])) $_SESSION['legal_tabs_verified'] = [];
                $_SESSION['legal_tabs_verified'][$tab] = time();
                unset($_SESSION['legal_pin'], $_SESSION['legal_pin_created']);
                try { logAudit('legal', 'VERIFY_LEGAL_PIN', 'legal', null, null, ['tab' => $tab, 'verified' => true]); } catch (\Exception $e) {}
                echo json_encode(['success' => true, 'message' => 'PIN verified. Access granted.']);
            } else {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Invalid PIN. Please try again.']);
            }
            exit;

        case 'check_legal_access':
            $tab = $_GET['tab'] ?? '';
            $verified = !empty($_SESSION['legal_tabs_verified'][$tab]) && (time() - ($_SESSION['legal_tabs_verified'][$tab] ?? 0)) <= 300;
            echo json_encode(['verified' => $verified]);
            exit;
    }
}

function sendLegalPinEmail(string $pin, string $tab): mixed {
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
        $mail->SMTPOptions = ['ssl' => ['verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true]];

        $mail->setFrom(MAIL_FROM_EMAIL, MAIL_FROM_NAME);
        $recipientEmail = $_SESSION['user']['email'] ?? OTP_RECIPIENT;
        $mail->addAddress($recipientEmail);

        $logoPath = __DIR__ . '/../assets/images/logo.png';
        if (file_exists($logoPath)) $mail->addEmbeddedImage($logoPath, 'company_logo', 'logo.png');

        $mail->isHTML(true);
        $mail->Subject = "Legal Security PIN - {$tab} - Microfinancial Admin";

        $year = date('Y');
        $userName = $_SESSION['user']['first_name'] ?? 'User';
        $digits = str_split($pin);
        $digitBoxes = '';
        foreach ($digits as $d) {
            $digitBoxes .= "<td style=\"padding:0 6px;\"><div style=\"width:52px;height:60px;line-height:60px;text-align:center;font-size:32px;font-weight:700;color:#D97706;background:#FEF3C7;border:2px solid #FDE68A;border-radius:12px;font-family:'Segoe UI',Arial,sans-serif;\">{$d}</div></td>";
        }

        $mail->Body = "
        <div style=\"background-color:#f3f4f6;padding:40px 20px;font-family:'Segoe UI',Roboto,Arial,sans-serif;\">
          <div style=\"max-width:520px;margin:0 auto;background:#ffffff;border-radius:20px;overflow:hidden;box-shadow:0 8px 40px rgba(0,0,0,0.10);\">
            <div style=\"background:linear-gradient(135deg,#D97706 0%,#B45309 50%,#92400E 100%);padding:36px 24px 28px;text-align:center;\">
              <img src=\"cid:company_logo\" alt=\"Microfinancial\" style=\"width:72px;height:72px;margin:0 auto 12px;display:block;border-radius:50%;background:#fff;padding:4px;box-shadow:0 4px 12px rgba(0,0,0,0.15);\" />
              <h1 style=\"margin:0;color:#ffffff;font-size:24px;font-weight:700;\">Legal Security PIN</h1>
              <p style=\"margin:6px 0 0;color:rgba(255,255,255,0.80);font-size:13px;\">Legal Management &mdash; {$tab}</p>
            </div>
            <div style=\"padding:36px 32px 28px;text-align:center;\">
              <div style=\"width:56px;height:56px;margin:0 auto 16px;background:#FEF3C7;border-radius:50%;text-align:center;line-height:56px;\"><span style=\"font-size:28px;\">&#9878;</span></div>
              <h2 style=\"margin:0 0 8px;color:#1F2937;font-size:22px;font-weight:700;\">Legal Module Verification</h2>
              <p style=\"color:#6B7280;font-size:15px;margin:0 0 28px;line-height:1.6;\">Hi <strong>{$userName}</strong>, use the 4-digit security PIN below to access <strong style=\"color:#D97706;\">{$tab}</strong>.</p>
              <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" style=\"margin:0 auto 28px;\"><tr>{$digitBoxes}</tr></table>
              <div style=\"background:linear-gradient(135deg,#FEF3C7,#FDE68A);border:1px solid #FDE68A;border-radius:12px;padding:14px 20px;margin:0 0 24px;text-align:left;\">
                <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"100%\"><tr><td style=\"width:28px;vertical-align:top;padding-top:2px;\"><span style=\"font-size:18px;\">&#9200;</span></td><td style=\"color:#92400E;font-size:13px;line-height:1.5;\">This PIN expires in <strong>120 seconds</strong>.<br/>Do not share this PIN with anyone.</td></tr></table>
              </div>
            </div>
            <div style=\"background:#F9FAFB;padding:20px 24px;text-align:center;border-top:1px solid #E5E7EB;\">
              <p style=\"margin:0 0 4px;color:#6B7280;font-size:12px;font-weight:600;\">Microfinancial Management System</p>
              <p style=\"margin:0;color:#9CA3AF;font-size:11px;\">&copy; {$year} All Rights Reserved</p>
            </div>
          </div>
        </div>";

        $mail->AltBody = "Your Legal Security PIN for {$tab} is: {$pin}\nThis PIN expires in 120 seconds.";
        $mail->send();
        return true;
    } catch (Exception $e) { return $mail->ErrorInfo; }
}
try {
    $mdb = getDB();
    // Check if workflow_step column exists on legal_cases
    $cols = $mdb->query("SHOW COLUMNS FROM legal_cases LIKE 'workflow_step'")->fetchAll();
    if (empty($cols)) {
        $mdb->exec("ALTER TABLE `legal_cases`
            ADD COLUMN `severity` ENUM('minor','moderate','major') DEFAULT 'moderate' AFTER `priority`,
            ADD COLUMN `complainant_name` VARCHAR(300) DEFAULT NULL AFTER `opposing_party`,
            ADD COLUMN `complainant_department` VARCHAR(100) DEFAULT NULL AFTER `complainant_name`,
            ADD COLUMN `accused_name` VARCHAR(300) DEFAULT NULL AFTER `complainant_department`,
            ADD COLUMN `accused_department` VARCHAR(100) DEFAULT NULL AFTER `accused_name`,
            ADD COLUMN `accused_employee_id` VARCHAR(20) DEFAULT NULL AFTER `accused_department`,
            ADD COLUMN `workflow_step` ENUM('complaint_filed','under_review','for_hearing','ongoing_investigation','verdict','closed','dismissed') DEFAULT 'complaint_filed' AFTER `status`,
            ADD COLUMN `verdict` ENUM('not_guilty','guilty_warning','guilty_suspension','guilty_termination','filed_in_court','deduct_salary','dismissed') DEFAULT NULL AFTER `workflow_step`,
            ADD COLUMN `penalty_details` TEXT DEFAULT NULL AFTER `verdict`,
            ADD COLUMN `legal_officer` VARCHAR(200) DEFAULT NULL AFTER `penalty_details`,
            ADD COLUMN `next_hearing` DATE DEFAULT NULL AFTER `legal_officer`,
            ADD COLUMN `admin_decision` ENUM('dismiss','internal_discipline','escalate_legal','return_to_dept') DEFAULT NULL AFTER `next_hearing`,
            ADD COLUMN `escalation_deadline` DATETIME DEFAULT NULL AFTER `admin_decision`,
            ADD COLUMN `auto_escalated` TINYINT(1) DEFAULT 0 AFTER `escalation_deadline`,
            ADD COLUMN `linked_loan_id` INT DEFAULT NULL AFTER `auto_escalated`,
            ADD COLUMN `penalty_amount` DECIMAL(15,2) DEFAULT NULL AFTER `linked_loan_id`
        ");
    }
    // Create hearings table
    $mdb->exec("CREATE TABLE IF NOT EXISTS `legal_case_hearings` (
        `hearing_id` INT AUTO_INCREMENT PRIMARY KEY,
        `case_id` INT NOT NULL,
        `hearing_date` DATETIME NOT NULL,
        `hearing_type` ENUM('initial_review','admin_hearing','investigation','formal_hearing','verdict_hearing','follow_up') NOT NULL DEFAULT 'initial_review',
        `location` VARCHAR(300) DEFAULT NULL,
        `officer_name` VARCHAR(200) DEFAULT NULL,
        `attendees` TEXT DEFAULT NULL,
        `witnesses` TEXT DEFAULT NULL,
        `minutes` TEXT DEFAULT NULL,
        `outcome` VARCHAR(500) DEFAULT NULL,
        `next_action` VARCHAR(500) DEFAULT NULL,
        `created_by` INT NOT NULL,
        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB");
    // Create evidence table
    $mdb->exec("CREATE TABLE IF NOT EXISTS `legal_case_evidence` (
        `evidence_id` INT AUTO_INCREMENT PRIMARY KEY,
        `case_id` INT NOT NULL,
        `evidence_type` ENUM('document','photo','video','audio','email','report','other') NOT NULL DEFAULT 'document',
        `file_name` VARCHAR(300) NOT NULL,
        `file_path` VARCHAR(500) DEFAULT NULL,
        `file_size` INT DEFAULT NULL,
        `mime_type` VARCHAR(100) DEFAULT NULL,
        `description` TEXT DEFAULT NULL,
        `uploaded_by` INT NOT NULL,
        `uploaded_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB");
    // Add mime_type column to existing tables that were created before this migration
    try { $mdb->exec("ALTER TABLE `legal_case_evidence` ADD COLUMN `mime_type` VARCHAR(100) DEFAULT NULL AFTER `file_size`"); } catch (\Throwable $e) {}
    // Ensure upload dir exists
    $evUploadBase = __DIR__ . '/../uploads/documents/legal/evidence/';
    if (!is_dir($evUploadBase)) mkdir($evUploadBase, 0755, true);
    // Create notices table
    $mdb->exec("CREATE TABLE IF NOT EXISTS `legal_escalation_notices` (
        `notice_id` INT AUTO_INCREMENT PRIMARY KEY,
        `case_id` INT DEFAULT NULL,
        `loan_doc_id` INT DEFAULT NULL,
        `notice_type` ENUM('reminder','warning','final_demand','legal_endorsement','written_warning','suspension_notice','termination_notice') NOT NULL,
        `recipient_name` VARCHAR(300) NOT NULL,
        `recipient_dept` VARCHAR(100) DEFAULT NULL,
        `subject` VARCHAR(500) NOT NULL,
        `body` TEXT DEFAULT NULL,
        `severity` ENUM('minor','moderate','major') DEFAULT 'minor',
        `days_overdue` INT DEFAULT NULL,
        `amount_involved` DECIMAL(15,2) DEFAULT NULL,
        `auto_generated` TINYINT(1) DEFAULT 0,
        `sent_date` DATETIME DEFAULT NULL,
        `status` ENUM('draft','sent','acknowledged','expired') DEFAULT 'draft',
        `created_by` INT NOT NULL,
        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB");
    // Create decision matrix table
    $mdb->exec("CREATE TABLE IF NOT EXISTS `legal_decision_matrix` (
        `matrix_id` INT AUTO_INCREMENT PRIMARY KEY,
        `case_type` VARCHAR(100) NOT NULL,
        `severity` ENUM('minor','moderate','major') NOT NULL,
        `recommended_action` VARCHAR(300) NOT NULL,
        `days_threshold` INT DEFAULT NULL,
        `amount_threshold` DECIMAL(15,2) DEFAULT NULL,
        `description` TEXT DEFAULT NULL,
        `is_active` TINYINT(1) DEFAULT 1,
        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB");
    // Seed decision matrix if empty
    $cnt = $mdb->query("SELECT COUNT(*) FROM legal_decision_matrix")->fetchColumn();
    if ($cnt == 0) {
        $mdb->exec("INSERT INTO `legal_decision_matrix` (`case_type`,`severity`,`recommended_action`,`days_threshold`,`amount_threshold`,`description`) VALUES
            ('Loan Default','minor','Reminder Notice',15,0,'1–15 days overdue'),
            ('Loan Default','minor','Warning Notice',30,0,'16–30 days overdue'),
            ('Loan Default','moderate','Final Demand Letter',60,5000,'31–60 days overdue, balance > ₱5,000'),
            ('Loan Default','major','Legal Endorsement / File Case',60,5000,'60+ days overdue, balance > ₱5,000'),
            ('Fraud','major','Termination + Court Filing',NULL,NULL,'Immediate legal action'),
            ('Theft','major','Termination + Court Filing',NULL,NULL,'Immediate legal action'),
            ('Harassment','moderate','Suspension + Investigation',NULL,NULL,'Internal investigation'),
            ('Data Breach','major','Legal Case Filed',NULL,NULL,'Data breach incidents'),
            ('Forgery','major','Termination + Court Filing',NULL,NULL,'Forgery cases'),
            ('Contract Violation','moderate','Suspension',NULL,NULL,'Contract violation'),
            ('Policy Violation','minor','Written Warning',NULL,NULL,'First offense'),
            ('Policy Violation','moderate','Suspension',NULL,NULL,'Repeated offense'),
            ('Policy Violation','major','Legal Case Filed',NULL,NULL,'Severe violation')
        ");
    }
    // Add legal_status to loan_documentation if missing
    $loanCols = $mdb->query("SHOW COLUMNS FROM loan_documentation LIKE 'legal_status'")->fetchAll();
    if (empty($loanCols)) {
        $mdb->exec("ALTER TABLE `loan_documentation`
            ADD COLUMN `legal_status` ENUM('none','under_legal','filed_in_court') DEFAULT 'none' AFTER `status`,
            ADD COLUMN `penalty_amount` DECIMAL(15,2) DEFAULT NULL AFTER `legal_status`,
            ADD COLUMN `days_overdue` INT DEFAULT NULL AFTER `penalty_amount`
        ");
    }
    // Expand case_type ENUM to include new types
    $typeCol = $mdb->query("SHOW COLUMNS FROM legal_cases LIKE 'case_type'")->fetch(PDO::FETCH_ASSOC);
    if ($typeCol && strpos($typeCol['Type'], 'loan_default') === false) {
        $mdb->exec("ALTER TABLE `legal_cases` MODIFY COLUMN `case_type` ENUM(
            'litigation','arbitration','mediation','regulatory','compliance','internal_investigation','other',
            'loan_default','fraud','theft','harassment','data_breach','forgery','contract_violation','policy_violation'
        ) DEFAULT 'other'");
    }
    // Add case_id column to loan_documentation for linking
    $lcCol = $mdb->query("SHOW COLUMNS FROM loan_documentation LIKE 'case_id'")->fetchAll();
    if (empty($lcCol)) {
        $mdb->exec("ALTER TABLE `loan_documentation` ADD COLUMN `case_id` INT DEFAULT NULL AFTER `days_overdue`");
    }
} catch (\Throwable $e) {
    // Migration may already be done; ignore errors
}

$action = $_GET['action'] ?? '';

switch ($action) {

    case 'list_cases':
        $status = $_GET['status'] ?? null;
        $sql = "SELECT lc.*, CONCAT(u.first_name,' ',u.last_name) AS assigned_name,
                       CONCAT(cr.first_name,' ',cr.last_name) AS created_by_name,
                       (SELECT COUNT(*) FROM legal_case_hearings h WHERE h.case_id = lc.case_id) AS hearing_count,
                       (SELECT COUNT(*) FROM legal_case_evidence e WHERE e.case_id = lc.case_id) AS evidence_count,
                       (SELECT COUNT(*) FROM legal_escalation_notices n WHERE n.case_id = lc.case_id) AS notice_count
                FROM legal_cases lc
                LEFT JOIN users u ON lc.assigned_to = u.user_id
                JOIN users cr ON lc.created_by = cr.user_id";
        $params = [];
        if ($status) { $sql .= " WHERE lc.status = ?"; $params[] = $status; }
        $sql .= " ORDER BY lc.created_at DESC";
        $stmt = getDB()->prepare($sql);
        $stmt->execute($params);
        jsonResponse(['data' => $stmt->fetchAll()]);

    case 'list_contracts':
        $rows = getDB()->query("SELECT c.*, CONCAT(u.first_name,' ',u.last_name) AS assigned_name
            FROM legal_contracts c
            LEFT JOIN users u ON c.assigned_to = u.user_id
            ORDER BY c.created_at DESC")->fetchAll();
        jsonResponse(['data' => $rows]);

    case 'list_compliance':
        $rows = getDB()->query("SELECT c.*, CONCAT(u.first_name,' ',u.last_name) AS assigned_name
            FROM legal_compliance c
            LEFT JOIN users u ON c.assigned_to = u.user_id
            ORDER BY c.deadline ASC")->fetchAll();
        jsonResponse(['data' => $rows]);

    case 'list_loans':
        $status = $_GET['status'] ?? null;
        $sql = "SELECT l.*, CONCAT(u.first_name,' ',u.last_name) AS created_by_name
                FROM loan_documentation l
                JOIN users u ON l.created_by = u.user_id WHERE 1=1";
        $params = [];
        if ($status) { $sql .= " AND l.status = ?"; $params[] = $status; }
        $sql .= " ORDER BY l.created_at DESC";
        $stmt = getDB()->prepare($sql);
        $stmt->execute($params);
        jsonResponse(['data' => $stmt->fetchAll()]);

    case 'list_collaterals':
        $rows = getDB()->query("SELECT c.*, CONCAT(u.first_name,' ',u.last_name) AS created_by_name
            FROM collateral_registry c
            JOIN users u ON c.created_by = u.user_id
            ORDER BY c.created_at DESC")->fetchAll();
        jsonResponse(['data' => $rows]);

    case 'list_demands':
        $rows = getDB()->query("SELECT d.*, CONCAT(u.first_name,' ',u.last_name) AS created_by_name
            FROM demand_letters d
            JOIN users u ON d.created_by = u.user_id
            ORDER BY d.created_at DESC")->fetchAll();
        jsonResponse(['data' => $rows]);

    case 'list_kyc':
        $rows = getDB()->query("SELECT k.*, CONCAT(u.first_name,' ',u.last_name) AS created_by_name
            FROM kyc_records k
            JOIN users u ON k.created_by = u.user_id
            ORDER BY k.created_at DESC")->fetchAll();
        jsonResponse(['data' => $rows]);

    case 'list_resolutions':
        $rows = getDB()->query("SELECT r.*, CONCAT(u.first_name,' ',u.last_name) AS created_by_name
            FROM board_resolutions r
            JOIN users u ON r.created_by = u.user_id
            ORDER BY r.meeting_date DESC")->fetchAll();
        jsonResponse(['data' => $rows]);

    case 'list_poa':
        $rows = getDB()->query("SELECT p.*, CONCAT(u.first_name,' ',u.last_name) AS created_by_name
            FROM power_of_attorney p
            JOIN users u ON p.created_by = u.user_id
            ORDER BY p.created_at DESC")->fetchAll();
        jsonResponse(['data' => $rows]);

    case 'list_permits':
        $rows = getDB()->query("SELECT p.*, CONCAT(u.first_name,' ',u.last_name) AS created_by_name
            FROM permits_licenses p
            JOIN users u ON p.created_by = u.user_id
            ORDER BY p.expiry_date ASC")->fetchAll();
        jsonResponse(['data' => $rows]);

    case 'create_case':
        $d = readJsonBody();
        $num = 'LC-' . date('Y') . '-' . str_pad(random_int(1,999999),6,'0',STR_PAD_LEFT);
        $stmt = getDB()->prepare("INSERT INTO legal_cases 
            (case_number, title, case_type, description, priority, status, filing_date, due_date,
             opposing_party, court_venue, assigned_lawyer, assigned_to, department, financial_impact, created_by)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute([
            $num, $d['title'], $d['case_type'], $d['description'],
            $d['priority'] ?? 'medium', 'open', $d['filing_date'] ?? date('Y-m-d'),
            $d['due_date'] ?? null, $d['opposing_party'] ?? null, $d['court_venue'] ?? null,
            $d['assigned_lawyer'] ?? null, $d['assigned_to'] ?? null,
            $d['department'] ?? null, $d['financial_impact'] ?? 0, $d['created_by'] ?? 1
        ]);
        logAudit('legal', 'CREATE_CASE', 'legal_cases', null, null, ['case_number' => $num, 'title' => $d['title']]);
        jsonResponse(['success' => true, 'case_number' => $num], 201);

    case 'create_contract':
        $d = readJsonBody();
        $num = 'CON-' . date('Y') . '-' . str_pad(random_int(1,999999),6,'0',STR_PAD_LEFT);
        $stmt = getDB()->prepare("INSERT INTO legal_contracts
            (contract_number, title, contract_type, party_name, description,
             start_date, end_date, value, status, assigned_to, created_by)
            VALUES (?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute([
            $num, $d['title'], $d['contract_type'], $d['party_name'],
            $d['description'] ?? null, $d['start_date'], $d['end_date'] ?? null,
            $d['value'] ?? 0, 'draft', $d['assigned_to'] ?? null, $d['created_by'] ?? 1
        ]);
        logAudit('legal', 'CREATE_CONTRACT', 'legal_contracts', null, null, ['contract_number' => $num, 'title' => $d['title']]);
        jsonResponse(['success' => true, 'contract_number' => $num], 201);

    case 'update_case_status':
        $d = readJsonBody();
        $stmt = getDB()->prepare("UPDATE legal_cases SET status=?, resolution_summary=? WHERE case_id=?");
        $stmt->execute([$d['status'], $d['resolution_summary'] ?? null, $d['case_id']]);
        logAudit('legal', 'UPDATE_CASE_STATUS', 'legal_cases', intval($d['case_id']), null, ['status' => $d['status']]);
        jsonResponse(['success' => true]);

    case 'dashboard_stats':
        $db = getDB();
        $stats = [];
        $stats['total_loans'] = $db->query("SELECT COUNT(*) FROM loan_documentation")->fetchColumn();
        $stats['active_loans'] = $db->query("SELECT COUNT(*) FROM loan_documentation WHERE status IN ('active','signed')")->fetchColumn();
        $stats['total_collaterals'] = $db->query("SELECT COUNT(*) FROM collateral_registry")->fetchColumn();
        $stats['active_collaterals'] = $db->query("SELECT COUNT(*) FROM collateral_registry WHERE lien_status='active'")->fetchColumn();
        $stats['total_cases'] = $db->query("SELECT COUNT(*) FROM legal_cases")->fetchColumn();
        $stats['active_cases'] = $db->query("SELECT COUNT(*) FROM legal_cases WHERE status IN ('open','in_progress')")->fetchColumn();
        $stats['total_demands'] = $db->query("SELECT COUNT(*) FROM demand_letters")->fetchColumn();
        $stats['total_kyc'] = $db->query("SELECT COUNT(*) FROM kyc_records")->fetchColumn();
        $stats['verified_kyc'] = $db->query("SELECT COUNT(*) FROM kyc_records WHERE verification_status='verified'")->fetchColumn();
        $stats['total_contracts'] = $db->query("SELECT COUNT(*) FROM legal_contracts")->fetchColumn();
        $stats['active_contracts'] = $db->query("SELECT COUNT(*) FROM legal_contracts WHERE status='active'")->fetchColumn();
        $stats['compliance_items'] = $db->query("SELECT COUNT(*) FROM legal_compliance")->fetchColumn();
        $stats['non_compliant'] = $db->query("SELECT COUNT(*) FROM legal_compliance WHERE status='non_compliant'")->fetchColumn();
        $stats['total_resolutions'] = $db->query("SELECT COUNT(*) FROM board_resolutions")->fetchColumn();
        $stats['total_poa'] = $db->query("SELECT COUNT(*) FROM power_of_attorney")->fetchColumn();
        $stats['active_poa'] = $db->query("SELECT COUNT(*) FROM power_of_attorney WHERE status='active'")->fetchColumn();
        $stats['total_permits'] = $db->query("SELECT COUNT(*) FROM permits_licenses")->fetchColumn();
        $stats['active_permits'] = $db->query("SELECT COUNT(*) FROM permits_licenses WHERE status='active'")->fetchColumn();
        $stats['expiring_permits'] = $db->query("SELECT COUNT(*) FROM permits_licenses WHERE status='active' AND expiry_date <= DATE_ADD(CURDATE(), INTERVAL 90 DAY)")->fetchColumn();
        $stats['total_financial_exposure'] = $db->query("SELECT COALESCE(SUM(financial_impact),0) FROM legal_cases WHERE status IN ('open','in_progress')")->fetchColumn();
        // Case workflow stats
        $stats['cases_complaint_filed'] = $db->query("SELECT COUNT(*) FROM legal_cases WHERE workflow_step='complaint_filed'")->fetchColumn();
        $stats['cases_under_review'] = $db->query("SELECT COUNT(*) FROM legal_cases WHERE workflow_step='under_review'")->fetchColumn();
        $stats['cases_for_hearing'] = $db->query("SELECT COUNT(*) FROM legal_cases WHERE workflow_step='for_hearing'")->fetchColumn();
        $stats['cases_investigating'] = $db->query("SELECT COUNT(*) FROM legal_cases WHERE workflow_step='ongoing_investigation'")->fetchColumn();
        $stats['cases_verdict'] = $db->query("SELECT COUNT(*) FROM legal_cases WHERE workflow_step='verdict'")->fetchColumn();
        $stats['cases_closed'] = $db->query("SELECT COUNT(*) FROM legal_cases WHERE workflow_step='closed'")->fetchColumn();
        $stats['cases_dismissed'] = $db->query("SELECT COUNT(*) FROM legal_cases WHERE workflow_step='dismissed'")->fetchColumn();
        $stats['overdue_escalations'] = $db->query("SELECT COUNT(*) FROM legal_cases WHERE status IN ('open','in_progress') AND escalation_deadline IS NOT NULL AND escalation_deadline < NOW()")->fetchColumn();
        $stats['total_hearings'] = $db->query("SELECT COUNT(*) FROM legal_case_hearings")->fetchColumn();
        $stats['pending_verdicts'] = $db->query("SELECT COUNT(*) FROM legal_cases WHERE workflow_step IN ('for_hearing','ongoing_investigation') AND verdict IS NULL")->fetchColumn();
        jsonResponse($stats);

    // ═══════════════════════════════════════════════════════
    // COMPLAINT / CASE WORKFLOW ENDPOINTS
    // ═══════════════════════════════════════════════════════

    case 'file_complaint':
        $d = readJsonBody();
        if (empty($d['title']) || empty($d['case_type'])) jsonResponse(['error' => 'Title and case type required'], 400);
        $num = 'LC-' . date('Y') . '-' . str_pad(random_int(1,999999),6,'0',STR_PAD_LEFT);
        $severity = $d['severity'] ?? 'moderate';
        $escalationDays = 7;
        $escalationDeadline = date('Y-m-d H:i:s', strtotime("+{$escalationDays} days"));
        
        $stmt = getDB()->prepare("INSERT INTO legal_cases 
            (case_number, title, case_type, description, priority, severity, status, workflow_step,
             filing_date, due_date, opposing_party, complainant_name, complainant_department,
             accused_name, accused_department, accused_employee_id, court_venue,
             assigned_lawyer, assigned_to, department, legal_officer,
             financial_impact, escalation_deadline, linked_loan_id, created_by)
            VALUES (?,?,?,?,?,?,'open','complaint_filed',?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute([
            $num, $d['title'], $d['case_type'], $d['description'] ?? '',
            $d['priority'] ?? 'medium', $severity,
            $d['filing_date'] ?? date('Y-m-d'), $d['due_date'] ?? null,
            $d['opposing_party'] ?? null, $d['complainant_name'] ?? null,
            $d['complainant_department'] ?? null, $d['accused_name'] ?? null,
            $d['accused_department'] ?? null, $d['accused_employee_id'] ?? null,
            $d['court_venue'] ?? null, $d['assigned_lawyer'] ?? null,
            $d['assigned_to'] ?? null, $d['department'] ?? null,
            $d['legal_officer'] ?? null, $d['financial_impact'] ?? 0,
            $escalationDeadline, $d['linked_loan_id'] ?? null,
            $d['created_by'] ?? ($_SESSION['user_id'] ?? 1)
        ]);
        $caseId = getDB()->lastInsertId();
        
        // If linked to loan, update loan legal status
        if (!empty($d['linked_loan_id'])) {
            getDB()->prepare("UPDATE loan_documentation SET legal_status='under_legal', case_id=? WHERE loan_doc_id=?")
                ->execute([$caseId, $d['linked_loan_id']]);
        }
        
        logAudit('legal', 'FILE_COMPLAINT', 'legal_cases', $caseId, null, ['case_number' => $num, 'title' => $d['title'], 'severity' => $severity]);
        jsonResponse(['success' => true, 'case_number' => $num, 'case_id' => $caseId], 201);

    case 'update_workflow':
        $d = readJsonBody();
        if (empty($d['case_id']) || empty($d['workflow_step'])) jsonResponse(['error' => 'case_id and workflow_step required'], 400);
        
        $updates = ['workflow_step = ?'];
        $params = [$d['workflow_step']];
        
        // Map workflow to status
        $statusMap = [
            'complaint_filed' => 'open', 'under_review' => 'in_progress',
            'for_hearing' => 'in_progress', 'ongoing_investigation' => 'in_progress',
            'verdict' => 'pending_review', 'closed' => 'closed', 'dismissed' => 'closed'
        ];
        if (isset($statusMap[$d['workflow_step']])) {
            $updates[] = 'status = ?';
            $params[] = $statusMap[$d['workflow_step']];
        }
        
        if (isset($d['admin_decision'])) { $updates[] = 'admin_decision = ?'; $params[] = $d['admin_decision']; }
        if (isset($d['legal_officer'])) { $updates[] = 'legal_officer = ?'; $params[] = $d['legal_officer']; }
        if (isset($d['next_hearing'])) { $updates[] = 'next_hearing = ?'; $params[] = $d['next_hearing']; }
        if (isset($d['resolution_summary'])) { $updates[] = 'resolution_summary = ?'; $params[] = $d['resolution_summary']; }
        
        // Reset escalation timer
        $updates[] = 'escalation_deadline = ?';
        $params[] = date('Y-m-d H:i:s', strtotime('+7 days'));
        
        $params[] = $d['case_id'];
        getDB()->prepare("UPDATE legal_cases SET " . implode(', ', $updates) . " WHERE case_id = ?")->execute($params);
        
        logAudit('legal', 'UPDATE_WORKFLOW', 'legal_cases', intval($d['case_id']), null, ['workflow_step' => $d['workflow_step']]);
        jsonResponse(['success' => true]);

    case 'render_verdict':
        $d = readJsonBody();
        if (empty($d['case_id']) || empty($d['verdict'])) jsonResponse(['error' => 'case_id and verdict required'], 400);
        
        $stmt = getDB()->prepare("UPDATE legal_cases SET 
            workflow_step='verdict', status='pending_review', verdict=?, penalty_details=?,
            penalty_amount=?, resolution_summary=?, resolution_date=NOW()
            WHERE case_id=?");
        $stmt->execute([
            $d['verdict'], $d['penalty_details'] ?? null,
            $d['penalty_amount'] ?? null, $d['resolution_summary'] ?? null,
            $d['case_id']
        ]);
        
        // If verdict is filed_in_court and linked to loan, update loan
        if ($d['verdict'] === 'filed_in_court') {
            $case = getDB()->prepare("SELECT linked_loan_id FROM legal_cases WHERE case_id=?")->fetch(PDO::FETCH_ASSOC)
                ? getDB()->prepare("SELECT linked_loan_id FROM legal_cases WHERE case_id=?") : null;
            $caseStmt = getDB()->prepare("SELECT linked_loan_id FROM legal_cases WHERE case_id=?");
            $caseStmt->execute([$d['case_id']]);
            $caseRow = $caseStmt->fetch(PDO::FETCH_ASSOC);
            if ($caseRow && $caseRow['linked_loan_id']) {
                getDB()->prepare("UPDATE loan_documentation SET legal_status='filed_in_court' WHERE loan_doc_id=?")
                    ->execute([$caseRow['linked_loan_id']]);
            }
        }
        
        logAudit('legal', 'RENDER_VERDICT', 'legal_cases', intval($d['case_id']), null, ['verdict' => $d['verdict']]);
        jsonResponse(['success' => true]);

    case 'close_case':
        $d = readJsonBody();
        if (empty($d['case_id'])) jsonResponse(['error' => 'case_id required'], 400);
        getDB()->prepare("UPDATE legal_cases SET workflow_step='closed', status='closed', resolution_date=NOW(), resolution_summary=? WHERE case_id=?")
            ->execute([$d['resolution_summary'] ?? null, $d['case_id']]);
        logAudit('legal', 'CLOSE_CASE', 'legal_cases', intval($d['case_id']), null, ['action' => 'closed']);
        jsonResponse(['success' => true]);

    case 'add_hearing':
        $d = readJsonBody();
        if (empty($d['case_id']) || empty($d['hearing_date'])) jsonResponse(['error' => 'case_id and hearing_date required'], 400);
        $stmt = getDB()->prepare("INSERT INTO legal_case_hearings
            (case_id, hearing_date, hearing_type, location, officer_name, attendees, witnesses, minutes, outcome, next_action, created_by)
            VALUES (?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute([
            $d['case_id'], $d['hearing_date'], $d['hearing_type'] ?? 'initial_review',
            $d['location'] ?? null, $d['officer_name'] ?? null,
            $d['attendees'] ?? null, $d['witnesses'] ?? null,
            $d['minutes'] ?? null, $d['outcome'] ?? null, $d['next_action'] ?? null,
            $d['created_by'] ?? ($_SESSION['user_id'] ?? 1)
        ]);
        // Update case next_hearing
        if (!empty($d['next_hearing_date'])) {
            getDB()->prepare("UPDATE legal_cases SET next_hearing=? WHERE case_id=?")->execute([$d['next_hearing_date'], $d['case_id']]);
        }
        logAudit('legal', 'ADD_HEARING', 'legal_case_hearings', null, null, ['case_id' => $d['case_id']]);
        jsonResponse(['success' => true, 'hearing_id' => getDB()->lastInsertId()], 201);

    case 'list_hearings':
        $caseId = $_GET['case_id'] ?? null;
        if (!$caseId) jsonResponse(['error' => 'case_id required'], 400);
        $stmt = getDB()->prepare("SELECT h.*, CONCAT(u.first_name,' ',u.last_name) AS created_by_name
            FROM legal_case_hearings h JOIN users u ON h.created_by = u.user_id
            WHERE h.case_id = ? ORDER BY h.hearing_date DESC");
        $stmt->execute([$caseId]);
        jsonResponse(['data' => $stmt->fetchAll()]);

    case 'add_evidence':
        try {
            // Handles multipart/form-data (file upload)
            $caseId  = intval($_POST['case_id'] ?? 0);
            $evType  = $_POST['evidence_type'] ?? 'document';
            $desc    = trim($_POST['description'] ?? '');
            $userId  = $_SESSION['user_id'] ?? 1;

            if (!$caseId) jsonResponse(['error' => 'case_id required'], 400);

            $fileErr = $_FILES['evidence_file']['error'] ?? -1;
            if ($fileErr !== UPLOAD_ERR_OK) {
                $errMap = [1=>'File too large (INI)',2=>'File too large (form)',3=>'Partial upload',4=>'No file sent',6=>'No tmp dir',7=>'Write failed'];
                jsonResponse(['error' => 'Upload error: ' . ($errMap[$fileErr] ?? "code $fileErr")], 400);
            }

            $file     = $_FILES['evidence_file'];
            $origName = basename($file['name']);
            $size     = (int)$file['size'];
            $tmpPath  = $file['tmp_name'];

            // Allowed MIME types
            $allowed = [
                'image/jpeg','image/png','image/gif','image/webp',
                'video/mp4','video/webm','video/quicktime','video/x-msvideo',
                'audio/mpeg','audio/wav','audio/ogg','audio/mp4',
                'application/pdf','application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.ms-powerpoint',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'text/plain','text/csv','application/zip',
                'application/x-rar-compressed','application/x-rar',
                'message/rfc822','application/octet-stream'
            ];
            $finfo    = new finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($tmpPath);
            if (!in_array($mimeType, $allowed)) {
                jsonResponse(['error' => 'File type not allowed: ' . $mimeType], 400);
            }
            if ($size > 50 * 1024 * 1024) jsonResponse(['error' => 'File exceeds 50 MB limit'], 400);

            // Save to uploads/documents/legal/evidence/{case_id}/
            $uploadDir = __DIR__ . '/../uploads/documents/legal/evidence/' . $caseId . '/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

            $ext      = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
            $safeName = preg_replace('/[^A-Za-z0-9._-]/', '_', pathinfo($origName, PATHINFO_FILENAME));
            $stored   = $safeName . '_' . time() . '.' . $ext;
            $destPath = $uploadDir . $stored;
            if (!move_uploaded_file($tmpPath, $destPath)) jsonResponse(['error' => 'Failed to move uploaded file'], 500);

            $relPath = 'uploads/documents/legal/evidence/' . $caseId . '/' . $stored;

            // Use a single DB connection for prepare + execute + lastInsertId
            $db = getDB();
            $stmt = $db->prepare("INSERT INTO legal_case_evidence
                (case_id, evidence_type, file_name, file_path, file_size, mime_type, description, uploaded_by)
                VALUES (?,?,?,?,?,?,?,?)");
            $stmt->execute([$caseId, $evType, $origName, $relPath, $size, $mimeType, $desc ?: null, $userId]);
            $evId = (int)$db->lastInsertId();
            logAudit('legal', 'ADD_EVIDENCE', 'legal_case_evidence', $evId, null, ['case_id' => $caseId, 'file_name' => $origName]);
            jsonResponse(['success' => true, 'evidence_id' => $evId, 'file_path' => $relPath], 201);
        } catch (Exception $ex) {
            jsonResponse(['error' => 'Server error: ' . $ex->getMessage()], 500);
        }

    case 'get_evidence_file':
        $evId = intval($_GET['evidence_id'] ?? 0);
        if (!$evId) jsonResponse(['error' => 'evidence_id required'], 400);
        $row = getDB()->prepare("SELECT * FROM legal_case_evidence WHERE evidence_id=?");
        $row->execute([$evId]);
        $ev = $row->fetch(PDO::FETCH_ASSOC);
        if (!$ev) jsonResponse(['error' => 'Not found'], 404);
        $absPath = __DIR__ . '/../' . $ev['file_path'];
        if (!file_exists($absPath)) jsonResponse(['error' => 'File not found on disk'], 404);
        $mime = $ev['mime_type'] ?: mime_content_type($absPath);
        $forceDownload = !empty($_GET['download']);
        $inline = !$forceDownload && (in_array(explode('/', $mime)[0], ['image', 'video', 'audio']) || $mime === 'application/pdf');
        header('Content-Type: ' . $mime);
        header('Content-Length: ' . filesize($absPath));
        header('Content-Disposition: ' . ($inline ? 'inline' : 'attachment') . '; filename="' . basename($ev['file_name']) . '"');
        header('X-Content-Type-Options: nosniff');
        readfile($absPath);
        exit;

    case 'delete_evidence':
        $d = readJsonBody();
        if (empty($d['evidence_id'])) jsonResponse(['error' => 'evidence_id required'], 400);
        $row = getDB()->prepare("SELECT * FROM legal_case_evidence WHERE evidence_id=?");
        $row->execute([$d['evidence_id']]);
        $ev = $row->fetch(PDO::FETCH_ASSOC);
        if ($ev && $ev['file_path']) {
            $absPath = __DIR__ . '/../' . $ev['file_path'];
            if (file_exists($absPath)) @unlink($absPath);
        }
        getDB()->prepare("DELETE FROM legal_case_evidence WHERE evidence_id=?")->execute([$d['evidence_id']]);
        logAudit('legal', 'DELETE_EVIDENCE', 'legal_case_evidence', intval($d['evidence_id']), $ev, null);
        jsonResponse(['success' => true]);

    case 'list_evidence':
        $caseId = $_GET['case_id'] ?? null;
        if (!$caseId) jsonResponse(['error' => 'case_id required'], 400);
        $stmt = getDB()->prepare("SELECT e.*, CONCAT(u.first_name,' ',u.last_name) AS uploaded_by_name
            FROM legal_case_evidence e JOIN users u ON e.uploaded_by = u.user_id
            WHERE e.case_id = ? ORDER BY e.uploaded_at DESC");
        $stmt->execute([$caseId]);
        jsonResponse(['data' => $stmt->fetchAll()]);

    case 'generate_notice':
        $d = readJsonBody();
        if (empty($d['notice_type']) || empty($d['recipient_name']) || empty($d['subject'])) {
            jsonResponse(['error' => 'notice_type, recipient_name, subject required'], 400);
        }
        $stmt = getDB()->prepare("INSERT INTO legal_escalation_notices
            (case_id, loan_doc_id, notice_type, recipient_name, recipient_dept, subject, body,
             severity, days_overdue, amount_involved, auto_generated, status, created_by)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute([
            $d['case_id'] ?? null, $d['loan_doc_id'] ?? null, $d['notice_type'],
            $d['recipient_name'], $d['recipient_dept'] ?? null, $d['subject'],
            $d['body'] ?? null, $d['severity'] ?? 'minor', $d['days_overdue'] ?? null,
            $d['amount_involved'] ?? null, $d['auto_generated'] ?? 0, 'draft',
            $d['created_by'] ?? ($_SESSION['user_id'] ?? 1)
        ]);
        logAudit('legal', 'GENERATE_NOTICE', 'legal_escalation_notices', null, null, ['type' => $d['notice_type'], 'recipient' => $d['recipient_name']]);
        jsonResponse(['success' => true, 'notice_id' => getDB()->lastInsertId()], 201);

    case 'send_notice':
        $d = readJsonBody();
        if (empty($d['notice_id'])) jsonResponse(['error' => 'notice_id required'], 400);
        getDB()->prepare("UPDATE legal_escalation_notices SET status='sent', sent_date=NOW() WHERE notice_id=?")
            ->execute([$d['notice_id']]);
        jsonResponse(['success' => true]);

    case 'list_notices':
        $caseId = $_GET['case_id'] ?? null;
        $sql = "SELECT n.*, CONCAT(u.first_name,' ',u.last_name) AS created_by_name
            FROM legal_escalation_notices n JOIN users u ON n.created_by = u.user_id";
        $params = [];
        if ($caseId) { $sql .= " WHERE n.case_id = ?"; $params[] = $caseId; }
        $sql .= " ORDER BY n.created_at DESC";
        $stmt = getDB()->prepare($sql);
        $stmt->execute($params);
        jsonResponse(['data' => $stmt->fetchAll()]);

    case 'list_decision_matrix':
        $rows = getDB()->query("SELECT * FROM legal_decision_matrix WHERE is_active=1 ORDER BY case_type, severity")->fetchAll();
        jsonResponse(['data' => $rows]);

    case 'check_escalations':
        // Auto-check cases past escalation deadline with no action taken
        $db = getDB();
        $overdue = $db->query("SELECT case_id, case_number, title, workflow_step, escalation_deadline 
            FROM legal_cases 
            WHERE status IN ('open','in_progress') 
            AND escalation_deadline IS NOT NULL 
            AND escalation_deadline < NOW() 
            AND auto_escalated = 0")->fetchAll();
        
        $escalated = [];
        foreach ($overdue as $c) {
            // Auto-escalate: move to next workflow step
            $nextStep = 'under_review';
            if ($c['workflow_step'] === 'complaint_filed') $nextStep = 'under_review';
            elseif ($c['workflow_step'] === 'under_review') $nextStep = 'for_hearing';
            elseif ($c['workflow_step'] === 'for_hearing') $nextStep = 'ongoing_investigation';
            
            $db->prepare("UPDATE legal_cases SET workflow_step=?, auto_escalated=1, 
                escalation_deadline=DATE_ADD(NOW(), INTERVAL 7 DAY) WHERE case_id=?")
                ->execute([$nextStep, $c['case_id']]);
            
            // Generate auto-notice
            $db->prepare("INSERT INTO legal_escalation_notices 
                (case_id, notice_type, recipient_name, subject, body, severity, auto_generated, status, created_by)
                VALUES (?, 'warning', 'Admin', ?, ?, 'moderate', 1, 'sent', 1)")
                ->execute([
                    $c['case_id'],
                    'Auto-Escalation: ' . $c['case_number'],
                    'Case ' . $c['case_number'] . ' (' . $c['title'] . ') has been automatically escalated from ' . $c['workflow_step'] . ' to ' . $nextStep . ' due to no action within 7 days.'
                ]);
            
            $escalated[] = $c['case_number'];
        }
        jsonResponse(['success' => true, 'escalated_count' => count($escalated), 'escalated_cases' => $escalated]);

    case 'case_analytics':
        $db = getDB();
        $analytics = [];
        // By status
        $analytics['by_status'] = $db->query("SELECT workflow_step, COUNT(*) as count FROM legal_cases GROUP BY workflow_step")->fetchAll();
        // By severity
        $analytics['by_severity'] = $db->query("SELECT severity, COUNT(*) as count FROM legal_cases WHERE severity IS NOT NULL GROUP BY severity")->fetchAll();
        // By type
        $analytics['by_type'] = $db->query("SELECT case_type, COUNT(*) as count FROM legal_cases GROUP BY case_type")->fetchAll();
        // By department
        $analytics['by_department'] = $db->query("SELECT COALESCE(accused_department, department, 'Unknown') as dept, COUNT(*) as count FROM legal_cases GROUP BY dept ORDER BY count DESC LIMIT 10")->fetchAll();
        // Monthly trend (last 12 months)
        $analytics['monthly_trend'] = $db->query("SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count FROM legal_cases WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH) GROUP BY month ORDER BY month")->fetchAll();
        // Verdict distribution
        $analytics['by_verdict'] = $db->query("SELECT verdict, COUNT(*) as count FROM legal_cases WHERE verdict IS NOT NULL GROUP BY verdict")->fetchAll();
        // Average resolution time (days)
        $analytics['avg_resolution_days'] = $db->query("SELECT ROUND(AVG(DATEDIFF(resolution_date, filing_date)),1) as avg_days FROM legal_cases WHERE resolution_date IS NOT NULL AND filing_date IS NOT NULL")->fetchColumn() ?: 0;
        // Active cases needing attention (past escalation deadline)
        $analytics['overdue_escalations'] = $db->query("SELECT COUNT(*) FROM legal_cases WHERE status IN ('open','in_progress') AND escalation_deadline IS NOT NULL AND escalation_deadline < NOW()")->fetchColumn();
        jsonResponse($analytics);

    default:
        jsonResponse(['error' => 'Invalid action'], 400);
}
