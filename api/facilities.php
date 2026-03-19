<?php
/**
 * MICROFINANCIAL MANAGEMENT SYSTEM I
 * API: Facilities Reservation Module
 * 
 * Endpoints (via ?action=...):
 *   GET  list_facilities       – All facilities
 *   GET  list_reservations     – Reservations (filterable)
 *   GET  list_equipment        – Equipment inventory
 *   GET  list_maintenance      – Maintenance tickets
 *   GET  dashboard_stats       – Summary counts
 *   POST create_reservation    – New reservation
 *   POST update_status         – Approve/reject/cancel
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

$action = $_GET['action'] ?? '';

// Helper: get current user's position level
function getUserPositionLevel(): int {
    return intval($_SESSION['user']['position_level'] ?? 1);
}

// Helper: auto-detect room level from facility name
// Level 1 = Interview (Normal), Level 2 = Training/Transaction/Operations (VIP), Level 3 = Executive/Boardroom (Important)
function detectRoomLevel(string $name): int {
    $lower = strtolower($name);
    if (strpos($lower, 'executive') !== false || strpos($lower, 'boardroom') !== false) return 3;
    if (strpos($lower, 'training') !== false || strpos($lower, 'transaction') !== false || strpos($lower, 'fleet') !== false || strpos($lower, 'operations') !== false) return 2;
    return 1; // Interview rooms and others default to Level 1
}

// Helper: get room level for a facility
function getRoomLevel(int $facilityId): int {
    $stmt = getFacilitiesDB()->prepare("SELECT name, room_level FROM facilities WHERE facility_id = ?");
    $stmt->execute([$facilityId]);
    $row = $stmt->fetch();
    if (!$row) return 1;
    // Auto-detect from name if room_level is not set or is default
    return $row['room_level'] ? intval($row['room_level']) : detectRoomLevel($row['name']);
}

// Helper: check time slot conflict
function hasTimeConflict(int $facilityId, string $start, string $end, ?int $excludeResId = null): bool {
    $sql = "SELECT COUNT(*) FROM facility_reservations 
            WHERE facility_id = ? AND status IN ('pending','approved')
              AND start_datetime < ? AND end_datetime > ?";
    $params = [$facilityId, $end, $start];
    if ($excludeResId) {
        $sql .= " AND reservation_id != ?";
        $params[] = $excludeResId;
    }
    $stmt = getFacilitiesDB()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchColumn() > 0;
}

// Helper: send urgent notification to all admins
function sendUrgentNotification(string $title, string $message, string $link = '/modules/facilities/'): void {
    $admins = getDB()->query("SELECT user_id FROM users WHERE role IN ('super_admin','admin') AND is_active = 1")->fetchAll();
    $stmt = getDB()->prepare("INSERT INTO notifications (user_id, module, title, message, link) VALUES (?,?,?,?,?)");
    foreach ($admins as $admin) {
        $stmt->execute([$admin['user_id'], 'facilities', $title, $message, $link]);
    }
}

// Helper: Insert room usage log for a completed/cancelled reservation
function insertRoomUsageLog(int $reservationId, string $logStatus = 'completed', ?int $completedBy = null): void {
    try {
        $db = getFacilitiesDB();
        // Check if log already exists for this reservation
        $exists = $db->prepare("SELECT COUNT(*) FROM facility_room_usage_logs WHERE reservation_id = ?");
        $exists->execute([$reservationId]);
        if ($exists->fetchColumn() > 0) return;

        // Fetch full reservation + facility info
        $stmt = $db->prepare("
            SELECT r.*, f.name AS facility_name, f.room_level
            FROM facility_reservations r
            JOIN facilities f ON r.facility_id = f.facility_id
            WHERE r.reservation_id = ?
        ");
        $stmt->execute([$reservationId]);
        $row = $stmt->fetch();
        if (!$row) return;

        // Get user name
        $userName = null;
        try {
            $userStmt = getDB()->prepare("SELECT name FROM users WHERE user_id = ?");
            $userStmt->execute([$row['reserved_by']]);
            $userName = $userStmt->fetchColumn() ?: null;
        } catch (Exception $e) {}

        $duration = (strtotime($row['end_datetime']) - strtotime($row['start_datetime'])) / 60;

        $ins = $db->prepare("INSERT INTO facility_room_usage_logs 
            (reservation_id, reservation_code, facility_id, facility_name, room_level,
             reservation_type, event_title, purpose, department, reserved_by, reserved_by_name,
             start_datetime, end_datetime, duration_minutes, attendees_count, budget,
             equipment_used, status, completed_by, remarks)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $ins->execute([
            $row['reservation_id'],
            $row['reservation_code'],
            $row['facility_id'],
            $row['facility_name'],
            $row['room_level'],
            $row['reservation_type'],
            $row['event_title'],
            $row['purpose'],
            $row['department'],
            $row['reserved_by'],
            $userName,
            $row['start_datetime'],
            $row['end_datetime'],
            intval($duration),
            $row['attendees_count'],
            $row['budget'] ?? 0,
            $row['equipment_needed'],
            $logStatus,
            $completedBy,
            $row['remarks']
        ]);
    } catch (Exception $e) {
        error_log('Room usage log insert failed: ' . $e->getMessage());
    }
}

switch ($action) {

    /* ───── Facilities ───── */
    case 'list_facilities':
        $rows = getFacilitiesDB()->query("SELECT *, room_level FROM facilities ORDER BY room_level, name")->fetchAll();
        jsonResponse(['data' => $rows]);

    /* ───── Reservations ───── */
    case 'list_reservations':
        $status = $_GET['status'] ?? null;
        $type   = $_GET['type'] ?? null;
        $date   = $_GET['date'] ?? null;
        $sql = "SELECT r.*, f.name AS facility_name, f.type AS facility_type, f.capacity, f.room_level,
                       CONCAT(u.first_name,' ',u.last_name) AS reserved_by_name, u.department AS user_department
                FROM facility_reservations r
                JOIN facilities f ON r.facility_id = f.facility_id
                JOIN " . DB_NAME . ".users u ON r.reserved_by = u.user_id WHERE 1=1";
        $params = [];
        if ($status) { $sql .= " AND r.status = ?"; $params[] = $status; }
        if ($type)   { $sql .= " AND r.reservation_type = ?"; $params[] = $type; }
        if ($date)   { $sql .= " AND DATE(r.start_datetime) = ?"; $params[] = $date; }
        $sql .= " ORDER BY r.start_datetime DESC";
        $stmt = getFacilitiesDB()->prepare($sql);
        $stmt->execute($params);
        jsonResponse(['data' => $stmt->fetchAll()]);

    case 'calendar_events':
        $month = $_GET['month'] ?? date('m');
        $year  = $_GET['year']  ?? date('Y');
        $sql = "SELECT r.reservation_id, r.reservation_code, r.event_title, r.purpose,
                       r.start_datetime, r.end_datetime, r.status, r.reservation_type,
                       r.priority, r.budget, r.department,
                       f.name AS facility_name, f.type AS facility_type
                FROM facility_reservations r
                JOIN facilities f ON r.facility_id = f.facility_id
                WHERE MONTH(r.start_datetime) = ? AND YEAR(r.start_datetime) = ?
                  AND r.status NOT IN ('cancelled','rejected')
                ORDER BY r.start_datetime ASC";
        $stmt = getFacilitiesDB()->prepare($sql);
        $stmt->execute([$month, $year]);
        jsonResponse(['data' => $stmt->fetchAll()]);

    case 'room_status':
        // Auto-sync facility statuses based on current reservations
        // 1) Set occupied if there is an active approved reservation NOW
        getFacilitiesDB()->exec("UPDATE facilities f SET f.status = 'occupied'
            WHERE f.status != 'maintenance' AND f.status != 'retired'
            AND EXISTS (
                SELECT 1 FROM facility_reservations r 
                WHERE r.facility_id = f.facility_id 
                  AND r.status = 'approved' 
                  AND NOW() BETWEEN r.start_datetime AND r.end_datetime
            )");
        // 2) Set available if no active approved reservation is happening NOW
        getFacilitiesDB()->exec("UPDATE facilities f SET f.status = 'available'
            WHERE f.status = 'occupied'
            AND NOT EXISTS (
                SELECT 1 FROM facility_reservations r 
                WHERE r.facility_id = f.facility_id 
                  AND r.status = 'approved' 
                  AND NOW() BETWEEN r.start_datetime AND r.end_datetime
            )");
        // 3) Auto-complete reservations whose end_datetime has passed
        // First, get IDs of reservations about to be auto-completed (for logging)
        $autoCompleteRows = getFacilitiesDB()->query(
            "SELECT reservation_id FROM facility_reservations WHERE status = 'approved' AND end_datetime < NOW()"
        )->fetchAll();
        getFacilitiesDB()->exec("UPDATE facility_reservations SET status = 'completed'
            WHERE status = 'approved' AND end_datetime < NOW()");
        // Auto-insert room usage logs for auto-completed reservations
        foreach ($autoCompleteRows as $acr) {
            insertRoomUsageLog(intval($acr['reservation_id']), 'completed', null);
        }

        $sql = "SELECT f.*, f.room_level,
                  (SELECT COUNT(*) FROM facility_reservations r 
                   WHERE r.facility_id = f.facility_id 
                     AND r.status = 'approved' 
                     AND NOW() BETWEEN r.start_datetime AND r.end_datetime) AS is_currently_occupied,
                  (SELECT r.event_title FROM facility_reservations r 
                   WHERE r.facility_id = f.facility_id 
                     AND r.status = 'approved' 
                     AND NOW() BETWEEN r.start_datetime AND r.end_datetime 
                   LIMIT 1) AS current_event,
                  (SELECT r.end_datetime FROM facility_reservations r
                   WHERE r.facility_id = f.facility_id
                     AND r.status = 'approved'
                     AND NOW() BETWEEN r.start_datetime AND r.end_datetime
                   LIMIT 1) AS occupied_until,
                  (SELECT COUNT(*) FROM facility_reservations r 
                   WHERE r.facility_id = f.facility_id 
                     AND r.status = 'approved' 
                     AND DATE(r.start_datetime) = CURDATE()) AS today_bookings,
                  (SELECT COUNT(*) FROM facility_reservations r 
                   WHERE r.facility_id = f.facility_id 
                     AND r.status = 'approved' 
                     AND r.start_datetime > NOW()) AS has_upcoming
                FROM facilities f ORDER BY f.name";
        $rows = getFacilitiesDB()->query($sql)->fetchAll();
        jsonResponse(['data' => $rows]);

    case 'create_reservation':
        $d = readJsonBody();
        $userId = $_SESSION['user']['user_id'] ?? ($d['reserved_by'] ?? 1);
        $posLevel = getUserPositionLevel();
        $facilityId = intval($d['facility_id']);
        $roomLevel = getRoomLevel($facilityId);
        $resType = $d['reservation_type'] ?? 'regular';
        $isAutoTagged = 0;

        // --- Auto-VIP tagging: Only for CEO, owner, president, board-level positions ---
        $userRole = strtolower($_SESSION['user']['role'] ?? '');
        $userPosition = strtolower($_SESSION['user']['position'] ?? $_SESSION['user']['job_title'] ?? '');
        $vipRoles = ['ceo', 'owner', 'president', 'chairman', 'vice_president', 'board_member', 'chief'];
        $isVipRole = false;
        foreach ($vipRoles as $vr) {
            if (strpos($userRole, $vr) !== false || strpos($userPosition, $vr) !== false) {
                $isVipRole = true;
                break;
            }
        }
        // Also treat super_admin as VIP-eligible
        if ($userRole === 'super_admin') $isVipRole = true;
        
        // Only auto-tag VIP for Level 2+ rooms; Level 1 stays "normal/regular"
        if ($isVipRole && $resType === 'regular' && $roomLevel >= 2) {
            $resType = 'vip';
            $isAutoTagged = 1;
        }

        // --- Emergency validation: only authorized positions (Lvl 3+) ---
        if ($resType === 'emergency' && $posLevel < 3) {
            jsonResponse(['error' => 'Your position level is not authorized to create important reservations. Required: Director level or above.'], 403);
            break;
        }

        // --- Time slot conflict check (Emergency overrides) ---
        if ($resType !== 'emergency' && hasTimeConflict($facilityId, $d['start_datetime'], $d['end_datetime'])) {
            jsonResponse(['error' => 'Time slot conflict! The selected room is already booked for this time period. Please choose a different time or room.'], 409);
            break;
        }

        $stmt = getFacilitiesDB()->prepare("INSERT INTO facility_reservations 
            (reservation_code, facility_id, reserved_by, department, purpose, event_title,
             reservation_type, priority, start_datetime, end_datetime, attendees_count,
             budget, equipment_needed, special_requests, status, is_auto_tagged)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $code = 'RES-' . date('Y') . '-' . str_pad(random_int(1,999999),6,'0',STR_PAD_LEFT);

        // Auto-approve: emergency always approved, VIP auto-approved for authorized users
        $autoStatus = 'pending';
        if ($resType === 'emergency') {
            $autoStatus = 'approved';
        } elseif ($resType === 'vip' && $posLevel >= 3) {
            $autoStatus = 'approved';
        }

        $priority = $d['priority'] ?? 'normal';
        if ($resType === 'emergency') $priority = 'urgent';
        elseif ($resType === 'vip') $priority = 'high';

        $stmt->execute([
            $code, $facilityId, $userId, $d['department'] ?? null,
            $d['purpose'], $d['event_title'] ?? null,
            $resType, $priority,
            $d['start_datetime'], $d['end_datetime'],
            $d['attendees_count'] ?? null, $d['budget'] ?? 0,
            json_encode($d['equipment_needed'] ?? []),
            $d['special_requests'] ?? null, $autoStatus, $isAutoTagged
        ]);
        $resId = getFacilitiesDB()->lastInsertId();

        // Link equipment if provided
        if (!empty($d['equipment_needed']) && is_array($d['equipment_needed'])) {
            $eqStmt = getFacilitiesDB()->prepare("INSERT IGNORE INTO reservation_equipment 
                (reservation_id, equipment_id, quantity) VALUES (?,?,1)");
            foreach ($d['equipment_needed'] as $eqId) {
                if (is_numeric($eqId)) $eqStmt->execute([$resId, $eqId]);
            }
        }

        // --- Emergency: send urgent notification to all admins ---
        if ($resType === 'emergency') {
            $userName = $_SESSION['user']['name'] ?? 'Unknown';
            sendUrgentNotification(
                '🚨 EMERGENCY Meeting Booked',
                "URGENT: {$userName} has booked an emergency meeting ({$d['event_title']}) on {$d['start_datetime']}. Auto-approved with highest priority.",
                '/modules/facilities/'
            );
        }

        logAudit('facilities', 'CREATE_RESERVATION', 'facility_reservations', $resId, null, [
            'reservation_code' => $code, 'facility_id' => $facilityId,
            'type' => $resType, 'auto_tagged' => $isAutoTagged
        ]);
        jsonResponse([
            'success' => true,
            'reservation_code' => $code,
            'reservation_id' => $resId,
            'reservation_type' => $resType,
            'auto_tagged' => $isAutoTagged,
            'status' => $autoStatus
        ], 201);

    case 'validate_reservation':
        $d = readJsonBody();
        $userId = $_SESSION['user']['user_id'] ?? ($d['validated_by'] ?? 1);
        if (!is_numeric($userId)) $userId = 1;
        $stmt = getFacilitiesDB()->prepare("UPDATE facility_reservations 
            SET is_validated = 1, validated_by = ?, validated_at = NOW(), remarks = CONCAT(IFNULL(remarks,''), '\n[Validated]')
            WHERE reservation_id = ?");
        $stmt->execute([intval($userId), $d['reservation_id']]);
        logAudit('facilities', 'VALIDATE_RESERVATION', 'facility_reservations', intval($d['reservation_id']), null, ['validated' => true]);
        jsonResponse(['success' => true]);

    case 'update_status':
        $d = readJsonBody();
        $userId = $_SESSION['user']['user_id'] ?? ($d['approved_by'] ?? 1);
        if (!is_numeric($userId)) $userId = 1;
        $stmt = getFacilitiesDB()->prepare("UPDATE facility_reservations SET status = ?, remarks = ?, 
            approved_by = ?, approved_at = NOW() WHERE reservation_id = ?");
        $stmt->execute([$d['status'], $d['remarks'] ?? null, intval($userId), $d['reservation_id']]);

        // When approved or ongoing, auto-set facility to 'occupied' if reservation is currently active
        if (in_array($d['status'] ?? '', ['approved', 'ongoing'])) {
            $resRow = getFacilitiesDB()->prepare("SELECT facility_id, start_datetime, end_datetime FROM facility_reservations WHERE reservation_id = ?");
            $resRow->execute([$d['reservation_id']]);
            $resData = $resRow->fetch();
            if ($resData) {
                getFacilitiesDB()->prepare("UPDATE facilities SET status = 'occupied' WHERE facility_id = ?")->execute([$resData['facility_id']]);
            }
        }

        logAudit('facilities', 'UPDATE_RESERVATION_STATUS', 'facility_reservations', intval($d['reservation_id']), null, ['status' => $d['status']]);
        jsonResponse(['success' => true]);

    case 'complete_reservation':
        $d = readJsonBody();
        $userId = $_SESSION['user']['user_id'] ?? 1;
        if (!is_numeric($userId)) $userId = 1;
        $resId = intval($d['reservation_id'] ?? 0);

        // Get facility_id before updating
        $resRow = getFacilitiesDB()->prepare("SELECT facility_id FROM facility_reservations WHERE reservation_id = ?");
        $resRow->execute([$resId]);
        $resData = $resRow->fetch();

        // Mark reservation as completed
        $stmt = getFacilitiesDB()->prepare("UPDATE facility_reservations SET status = 'completed', remarks = CONCAT(IFNULL(remarks,''), '\n[Completed]') WHERE reservation_id = ? AND status IN ('approved','ongoing')");
        $stmt->execute([$resId]);

        // Set facility back to available if no other active approved/ongoing reservations
        if ($resData) {
            $activeCheck = getFacilitiesDB()->prepare("SELECT COUNT(*) FROM facility_reservations WHERE facility_id = ? AND status IN ('approved','ongoing') AND NOW() BETWEEN start_datetime AND end_datetime AND reservation_id != ?");
            $activeCheck->execute([$resData['facility_id'], $resId]);
            if ($activeCheck->fetchColumn() == 0) {
                getFacilitiesDB()->prepare("UPDATE facilities SET status = 'available' WHERE facility_id = ?")->execute([$resData['facility_id']]);
            }
        }

        logAudit('facilities', 'COMPLETE_RESERVATION', 'facility_reservations', $resId, null, ['completed' => true]);

        // Insert room usage log
        insertRoomUsageLog($resId, 'completed', intval($userId));

        jsonResponse(['success' => true]);

    /* ───── Equipment ───── */
    case 'list_equipment':
        $rows = getFacilitiesDB()->query("SELECT e.*, f.name AS facility_name 
            FROM facility_equipment e 
            LEFT JOIN facilities f ON e.facility_id = f.facility_id
            ORDER BY e.name")->fetchAll();
        jsonResponse(['data' => $rows]);

    /* ───── Maintenance ───── */
    case 'list_maintenance':
        $rows = getFacilitiesDB()->query("SELECT m.*, f.name AS facility_name,
            CONCAT(u.first_name,' ',u.last_name) AS reported_by_name,
            e.name AS equipment_name
            FROM facility_maintenance m
            JOIN facilities f ON m.facility_id = f.facility_id
            JOIN " . DB_NAME . ".users u ON m.reported_by = u.user_id
            LEFT JOIN facility_equipment e ON m.equipment_id = e.equipment_id
            ORDER BY
              CASE m.status WHEN 'open' THEN 0 WHEN 'in_progress' THEN 1 WHEN 'resolved' THEN 2 WHEN 'closed' THEN 3 END,
              CASE m.priority WHEN 'critical' THEN 0 WHEN 'high' THEN 1 WHEN 'medium' THEN 2 WHEN 'low' THEN 3 END,
              m.created_at DESC")->fetchAll();
        jsonResponse(['data' => $rows]);

    case 'create_maintenance':
        $d = readJsonBody();
        $ticket = 'MNT-' . date('Y') . '-' . str_pad(random_int(1,999999),6,'0',STR_PAD_LEFT);
        $userId = $_SESSION['user']['user_id'] ?? ($d['reported_by'] ?? 1);
        if (!is_numeric($userId)) $userId = 1;

        $stmt = getFacilitiesDB()->prepare("INSERT INTO facility_maintenance
            (ticket_number, facility_id, equipment_id, reported_by, issue_type, priority, description, status, assigned_to)
            VALUES (?,?,?,?,?,?,?,'open',?)");
        $equipId = !empty($d['equipment_id']) && is_numeric($d['equipment_id']) ? intval($d['equipment_id']) : null;
        $stmt->execute([
            $ticket,
            intval($d['facility_id']),
            $equipId,
            intval($userId),
            $d['issue_type'] ?? 'equipment',
            $d['priority'] ?? 'medium',
            $d['description'] ?? '',
            $d['assigned_to'] ?? null
        ]);
        $maintId = getFacilitiesDB()->lastInsertId();

        // If equipment malfunction, mark equipment condition as needs_repair
        if ($equipId) {
            getFacilitiesDB()->prepare("UPDATE facility_equipment SET condition_status = 'needs_repair', is_available = 0 WHERE equipment_id = ?")->execute([$equipId]);
        }

        // If critical/high priority, optionally set facility to maintenance status
        if (in_array($d['priority'] ?? '', ['critical', 'high']) && !empty($d['set_facility_maintenance'])) {
            getFacilitiesDB()->prepare("UPDATE facilities SET status = 'maintenance' WHERE facility_id = ?")->execute([intval($d['facility_id'])]);
        }

        logAudit('facilities', 'CREATE_MAINTENANCE', 'facility_maintenance', $maintId, null, [
            'ticket_number' => $ticket, 'facility_id' => $d['facility_id'], 'equipment_id' => $equipId, 'issue_type' => $d['issue_type'] ?? 'equipment'
        ]);
        jsonResponse(['success' => true, 'ticket_number' => $ticket, 'maintenance_id' => $maintId], 201);

    case 'update_maintenance':
        $d = readJsonBody();
        $maintId = intval($d['maintenance_id'] ?? 0);
        $newStatus = $d['status'] ?? '';
        $resolution = $d['resolution_notes'] ?? null;
        $assignedTo = $d['assigned_to'] ?? null;

        // Get current maintenance record
        $cur = getFacilitiesDB()->prepare("SELECT * FROM facility_maintenance WHERE maintenance_id = ?");
        $cur->execute([$maintId]);
        $curData = $cur->fetch();
        if (!$curData) {
            jsonResponse(['error' => 'Maintenance record not found'], 404);
            break;
        }

        $updates = [];
        $params = [];
        if ($newStatus) {
            $updates[] = "status = ?";
            $params[] = $newStatus;
            if ($newStatus === 'resolved' || $newStatus === 'closed') {
                $updates[] = "resolved_at = NOW()";
            }
        }
        if ($resolution !== null) {
            $updates[] = "resolution_notes = ?";
            $params[] = $resolution;
        }
        if ($assignedTo !== null) {
            $updates[] = "assigned_to = ?";
            $params[] = $assignedTo;
        }

        if ($updates) {
            $params[] = $maintId;
            $sql = "UPDATE facility_maintenance SET " . implode(', ', $updates) . " WHERE maintenance_id = ?";
            getFacilitiesDB()->prepare($sql)->execute($params);
        }

        // When resolved/closed, restore equipment and facility status
        if (in_array($newStatus, ['resolved', 'closed'])) {
            if ($curData['equipment_id']) {
                getFacilitiesDB()->prepare("UPDATE facility_equipment SET condition_status = 'good', is_available = 1 WHERE equipment_id = ?")->execute([$curData['equipment_id']]);
            }
            // Check if facility has any other open maintenance tickets
            $openCheck = getFacilitiesDB()->prepare("SELECT COUNT(*) FROM facility_maintenance WHERE facility_id = ? AND status IN ('open','in_progress') AND maintenance_id != ?");
            $openCheck->execute([$curData['facility_id'], $maintId]);
            if ($openCheck->fetchColumn() == 0) {
                getFacilitiesDB()->prepare("UPDATE facilities SET status = 'available' WHERE facility_id = ? AND status = 'maintenance'")->execute([$curData['facility_id']]);
            }
        }

        logAudit('facilities', 'UPDATE_MAINTENANCE', 'facility_maintenance', $maintId, null, ['status' => $newStatus]);
        jsonResponse(['success' => true]);

    /* ───── Dashboard Stats ───── */
    case 'dashboard_stats':
        $db = getFacilitiesDB();
        $stats = [];
        $stats['total_facilities'] = $db->query("SELECT COUNT(*) FROM facilities")->fetchColumn();
        $stats['available_facilities'] = $db->query("SELECT COUNT(*) FROM facilities WHERE status='available'")->fetchColumn();
        $stats['pending_reservations'] = $db->query("SELECT COUNT(*) FROM facility_reservations WHERE status='pending'")->fetchColumn();
        $stats['today_reservations'] = $db->query("SELECT COUNT(*) FROM facility_reservations WHERE DATE(start_datetime)=CURDATE()")->fetchColumn();
        $stats['total_equipment'] = $db->query("SELECT COUNT(*) FROM facility_equipment")->fetchColumn();
        $stats['open_maintenance'] = $db->query("SELECT COUNT(*) FROM facility_maintenance WHERE status IN ('open','in_progress')")->fetchColumn();
        $stats['emergency_active'] = $db->query("SELECT COUNT(*) FROM facility_reservations WHERE reservation_type='emergency' AND status='approved' AND end_datetime > NOW()")->fetchColumn();
        jsonResponse($stats);

    /* ───── Room Usage Logs ───── */
    case 'list_room_logs':
        $db = getFacilitiesDB();
        $where = ["1=1"];
        $params = [];
        if (!empty($_GET['facility_id'])) {
            $where[] = "l.facility_id = ?";
            $params[] = intval($_GET['facility_id']);
        }
        if (!empty($_GET['room_level'])) {
            $where[] = "l.room_level = ?";
            $params[] = intval($_GET['room_level']);
        }
        if (!empty($_GET['reservation_type'])) {
            $where[] = "l.reservation_type = ?";
            $params[] = $_GET['reservation_type'];
        }
        if (!empty($_GET['department'])) {
            $where[] = "l.department = ?";
            $params[] = $_GET['department'];
        }
        if (!empty($_GET['status'])) {
            $where[] = "l.status = ?";
            $params[] = $_GET['status'];
        }
        if (!empty($_GET['date_from'])) {
            $where[] = "l.start_datetime >= ?";
            $params[] = $_GET['date_from'] . ' 00:00:00';
        }
        if (!empty($_GET['date_to'])) {
            $where[] = "l.start_datetime <= ?";
            $params[] = $_GET['date_to'] . ' 23:59:59';
        }
        $sql = "SELECT l.* FROM facility_room_usage_logs l
                WHERE " . implode(' AND ', $where) . "
                ORDER BY l.logged_at DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        jsonResponse(['data' => $stmt->fetchAll()]);

    case 'room_log_stats':
        $db = getFacilitiesDB();
        $logStats = [];
        $logStats['total_logs'] = intval($db->query("SELECT COUNT(*) FROM facility_room_usage_logs")->fetchColumn());
        $logStats['total_hours'] = round(floatval($db->query("SELECT COALESCE(SUM(duration_minutes),0)/60 FROM facility_room_usage_logs WHERE status='completed'")->fetchColumn()), 1);
        $logStats['total_completed'] = intval($db->query("SELECT COUNT(*) FROM facility_room_usage_logs WHERE status='completed'")->fetchColumn());
        $logStats['total_cancelled'] = intval($db->query("SELECT COUNT(*) FROM facility_room_usage_logs WHERE status='cancelled'")->fetchColumn());
        $logStats['total_no_show'] = intval($db->query("SELECT COUNT(*) FROM facility_room_usage_logs WHERE status='no_show'")->fetchColumn());
        // Most used room
        $mostUsed = $db->query("SELECT facility_name, COUNT(*) as cnt FROM facility_room_usage_logs WHERE status='completed' GROUP BY facility_id ORDER BY cnt DESC LIMIT 1")->fetch();
        $logStats['most_used_room'] = $mostUsed ? $mostUsed['facility_name'] : '—';
        $logStats['most_used_count'] = $mostUsed ? intval($mostUsed['cnt']) : 0;
        // By level
        $logStats['level_1_count'] = intval($db->query("SELECT COUNT(*) FROM facility_room_usage_logs WHERE room_level=1 AND status='completed'")->fetchColumn());
        $logStats['level_2_count'] = intval($db->query("SELECT COUNT(*) FROM facility_room_usage_logs WHERE room_level=2 AND status='completed'")->fetchColumn());
        $logStats['level_3_count'] = intval($db->query("SELECT COUNT(*) FROM facility_room_usage_logs WHERE room_level=3 AND status='completed'")->fetchColumn());
        jsonResponse($logStats);

    case 'get_reservation_log':
        $resId = intval($_GET['reservation_id'] ?? 0);
        if (!$resId) { jsonResponse(['error' => 'Missing reservation_id'], 400); break; }
        $db = getFacilitiesDB();
        $stmt = $db->prepare("SELECT * FROM facility_room_usage_logs WHERE reservation_id = ? LIMIT 1");
        $stmt->execute([$resId]);
        $log = $stmt->fetch();
        jsonResponse(['success' => true, 'log' => $log ?: null]);

    /* ───── Current User Info (position_level, role) ───── */
    case 'user_info':
        $user = $_SESSION['user'] ?? [];
        jsonResponse([
            'user_id' => $user['user_id'] ?? null,
            'name' => $user['name'] ?? '',
            'role' => $user['role'] ?? 'staff',
            'position' => $user['position'] ?? $user['job_title'] ?? '',
            'position_level' => intval($user['position_level'] ?? 1),
            'department' => $user['department'] ?? ''
        ]);

    /* ───── Check Time Slot Conflict ───── */
    case 'check_conflict':
        $facilityId = intval($_GET['facility_id'] ?? 0);
        $start = $_GET['start'] ?? '';
        $end = $_GET['end'] ?? '';
        $excludeId = intval($_GET['exclude_id'] ?? 0) ?: null;
        if (!$facilityId || !$start || !$end) {
            jsonResponse(['error' => 'Missing parameters'], 400);
            break;
        }
        $conflict = hasTimeConflict($facilityId, $start, $end, $excludeId);
        jsonResponse(['has_conflict' => $conflict]);

    /* ───── Get Booked Slots for a Facility on a Date ───── */
    case 'booked_slots':
        $facilityId = intval($_GET['facility_id'] ?? 0);
        $date = $_GET['date'] ?? date('Y-m-d');
        $sql = "SELECT reservation_id, reservation_code, event_title, reservation_type, priority,
                       start_datetime, end_datetime, status, department
                FROM facility_reservations
                WHERE facility_id = ? AND DATE(start_datetime) = ? AND status IN ('pending','approved')
                ORDER BY start_datetime ASC";
        $stmt = getFacilitiesDB()->prepare($sql);
        $stmt->execute([$facilityId, $date]);
        jsonResponse(['data' => $stmt->fetchAll()]);

    /* ───── Reschedule Reservation (with level-based rules) ───── */
    case 'reschedule_reservation':
        $d = readJsonBody();
        $resId = intval($d['reservation_id'] ?? 0);
        $newStart = $d['new_start_datetime'] ?? '';
        $newEnd = $d['new_end_datetime'] ?? '';
        $reason = trim($d['reschedule_reason'] ?? '');
        $userId = $_SESSION['user']['user_id'] ?? 1;

        // Get reservation details
        $resRow = getFacilitiesDB()->prepare("SELECT r.*, f.room_level FROM facility_reservations r JOIN facilities f ON r.facility_id = f.facility_id WHERE r.reservation_id = ?");
        $resRow->execute([$resId]);
        $res = $resRow->fetch();
        if (!$res) { jsonResponse(['error' => 'Reservation not found'], 404); break; }

        $roomLvl = intval($res['room_level']);

        // Rule: Room 3 (VIP/Meeting) - VIP reservations CANNOT be rescheduled
        if ($roomLvl === 3 && $res['reservation_type'] === 'vip' && $res['status'] === 'approved') {
            jsonResponse(['error' => 'Level 3 VIP reservations CANNOT be rescheduled once approved. This is a system policy.'], 403);
            break;
        }

        // Rule: Room 2 (Training) - must provide reason
        if ($roomLvl >= 2 && empty($reason)) {
            jsonResponse(['error' => 'A valid reason is required for rescheduling Level ' . $roomLvl . ' room reservations.'], 400);
            break;
        }

        // Check time slot conflict for new time
        if (hasTimeConflict($res['facility_id'], $newStart, $newEnd, $resId)) {
            jsonResponse(['error' => 'Time slot conflict! The new time overlaps with an existing booking.'], 409);
            break;
        }

        // Perform reschedule
        $stmt = getFacilitiesDB()->prepare("UPDATE facility_reservations 
            SET original_start = COALESCE(original_start, start_datetime),
                original_end = COALESCE(original_end, end_datetime),
                start_datetime = ?, end_datetime = ?,
                reschedule_reason = ?, rescheduled_by = ?, rescheduled_at = NOW(),
                remarks = CONCAT(IFNULL(remarks,''), '\n[Rescheduled: ', ?, ']')
            WHERE reservation_id = ?");
        $stmt->execute([$newStart, $newEnd, $reason, $userId, $reason, $resId]);

        logAudit('facilities', 'RESCHEDULE_RESERVATION', 'facility_reservations', $resId, 
            ['start' => $res['start_datetime'], 'end' => $res['end_datetime']],
            ['start' => $newStart, 'end' => $newEnd, 'reason' => $reason]);
        jsonResponse(['success' => true, 'message' => 'Reservation rescheduled successfully.']);

    /* ───── Cancel Reservation (with level-based rules) ───── */
    case 'cancel_reservation':
        $d = readJsonBody();
        $resId = intval($d['reservation_id'] ?? 0);
        $reason = trim($d['cancel_reason'] ?? '');
        $userId = $_SESSION['user']['user_id'] ?? 1;

        // Get reservation details
        $resRow = getFacilitiesDB()->prepare("SELECT r.*, f.room_level FROM facility_reservations r JOIN facilities f ON r.facility_id = f.facility_id WHERE r.reservation_id = ?");
        $resRow->execute([$resId]);
        $res = $resRow->fetch();
        if (!$res) { jsonResponse(['error' => 'Reservation not found'], 404); break; }

        $roomLvl = intval($res['room_level']);

        // Rule: Room 3 (VIP/Meeting) - VIP reservations CANNOT be cancelled
        if ($roomLvl === 3 && $res['reservation_type'] === 'vip' && $res['status'] === 'approved') {
            jsonResponse(['error' => 'Level 3 VIP reservations CANNOT be cancelled once approved. This is a system policy.'], 403);
            break;
        }

        // Rule: Room 4 (Emergency) - Emergency reservations cannot be cancelled by non-admins
        if ($roomLvl === 4 && $res['reservation_type'] === 'emergency') {
            $userRole = $_SESSION['user']['role'] ?? 'staff';
            if (!in_array($userRole, ['super_admin', 'admin'])) {
                jsonResponse(['error' => 'Emergency reservations can only be cancelled by administrators.'], 403);
                break;
            }
        }

        // Perform cancellation
        $stmt = getFacilitiesDB()->prepare("UPDATE facility_reservations 
            SET status = 'cancelled', cancelled_by = ?, cancelled_at = NOW(), cancel_reason = ?,
                remarks = CONCAT(IFNULL(remarks,''), '\n[Cancelled: ', ?, ']')
            WHERE reservation_id = ?");
        $stmt->execute([$userId, $reason, $reason ?: 'No reason provided', $resId]);

        // Restore facility status
        $activeCheck = getFacilitiesDB()->prepare("SELECT COUNT(*) FROM facility_reservations WHERE facility_id = ? AND status = 'approved' AND NOW() BETWEEN start_datetime AND end_datetime AND reservation_id != ?");
        $activeCheck->execute([$res['facility_id'], $resId]);
        if ($activeCheck->fetchColumn() == 0) {
            getFacilitiesDB()->prepare("UPDATE facilities SET status = 'available' WHERE facility_id = ? AND status = 'occupied'")->execute([$res['facility_id']]);
        }

        logAudit('facilities', 'CANCEL_RESERVATION', 'facility_reservations', $resId, null, ['reason' => $reason]);

        // Insert room usage log for cancellation
        insertRoomUsageLog($resId, 'cancelled', intval($userId));

        jsonResponse(['success' => true, 'message' => 'Reservation cancelled successfully.']);

    default:
        jsonResponse(['error' => 'Invalid action'], 400);
}
