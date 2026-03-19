<?php
/**
 * MICROFINANCIAL MANAGEMENT SYSTEM I
 * API: Audit Logs
 *
 * Endpoints (via ?action=...):
 *   GET  recent       – Recent audit log entries (with user info)
 *   GET  stats        – Audit summary stats by module
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

session_start();
if (empty($_SESSION['authenticated'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

require_once __DIR__ . '/../config/db.php';

$action = $_GET['action'] ?? '';

switch ($action) {

    case 'recent':
        $limit = min(max(intval($_GET['limit'] ?? 25), 1), 100);
        $module = $_GET['module'] ?? '';

        $sql = "SELECT a.log_id, a.module, a.action, a.table_name, a.record_id,
                       a.new_values, a.ip_address, a.created_at,
                       u.first_name, u.last_name, u.employee_id, u.role
                FROM audit_logs a
                LEFT JOIN users u ON a.user_id = u.user_id";

        $params = [];
        if ($module && in_array($module, ['facilities','documents','legal','visitors','departments','system'], true)) {
            $sql .= " WHERE a.module = :mod";
            $params[':mod'] = $module;
        }
        $sql .= " ORDER BY a.created_at DESC LIMIT :lim";

        $stmt = getDB()->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();

        echo json_encode(['data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
        exit;

    case 'stats':
        $db = getDB();
        $stats = [];
        $stats['total'] = (int) $db->query("SELECT COUNT(*) FROM audit_logs")->fetchColumn();
        $stats['today'] = (int) $db->query("SELECT COUNT(*) FROM audit_logs WHERE DATE(created_at) = CURDATE()")->fetchColumn();

        $byModule = $db->query("SELECT module, COUNT(*) as cnt FROM audit_logs GROUP BY module ORDER BY cnt DESC")->fetchAll(PDO::FETCH_ASSOC);
        $stats['by_module'] = $byModule;

        $recentActions = $db->query("SELECT action, COUNT(*) as cnt FROM audit_logs WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) GROUP BY action ORDER BY cnt DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
        $stats['recent_actions'] = $recentActions;

        echo json_encode(['data' => $stats]);
        exit;

    default:
        http_response_code(400);
        echo json_encode(['error' => 'Unknown action: ' . $action]);
        exit;
}
