<?php
// ==============================================================================
// Smart Gram - Webhook & Automation Service (Phase 8 - n8n Integration)
// Decoupled Event Publisher with HMAC Signatures & Non-Blocking Resilience
// ==============================================================================

namespace App\Services;

use App\Core\Database;
use Exception;
use PDO;

class WebhookService {

    /**
     * Dispatch an event webhook with HMAC SHA-256 signature and non-blocking timeout
     */
    public static function dispatch(
        string $eventName, 
        array $payload, 
        string $entityType = 'grievances', 
        ?int $entityId = null
    ): array {
        $config = config('webhook') ?? [];
        $enabled = (bool)($config['enabled'] ?? false);
        $url = trim($config['url'] ?? '');
        $secret = $config['secret'] ?? 'smartgram_default_secret_key';
        $timeout = (int)($config['timeout_seconds'] ?? 2);
        $simulateInDev = (bool)($config['simulate_in_dev'] ?? true);

        if (!$enabled) {
            return [
                'status'  => 'skipped',
                'reason'  => 'Webhook automation is disabled in config.',
                'http_code' => null
            ];
        }

        $timestamp = gmdate('Y-m-d\TH:i:s\Z');
        $envelope = [
            'event'     => $eventName,
            'timestamp' => $timestamp,
            'source'    => 'smart-gram',
            'data'      => $payload
        ];

        $jsonPayload = json_encode($envelope, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $signature = hash_hmac('sha256', $jsonPayload, $secret);

        $startTime = microtime(true);

        // Simulation Mode (for dev/test or when URL is empty)
        if (empty($url) && $simulateInDev) {
            $executionTimeMs = round((microtime(true) - $startTime) * 1000, 2);
            self::logDispatch(
                $eventName,
                $entityType,
                $entityId,
                'http://localhost:5678/webhook/smartgram (simulated)',
                $jsonPayload,
                200,
                '{"status":"simulated_delivery_ok"}',
                'simulated',
                $executionTimeMs,
                null
            );

            return [
                'status'    => 'simulated',
                'http_code' => 200,
                'time_ms'   => $executionTimeMs,
                'payload'   => $envelope
            ];
        }

        if (empty($url)) {
            return [
                'status'    => 'skipped',
                'reason'    => 'No webhook URL configured.',
                'http_code' => null
            ];
        }

        // Live HTTP POST dispatch via cURL
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'User-Agent: SmartGram-AutomationEngine/1.0',
            "X-SmartGram-Event: {$eventName}",
            "X-SmartGram-Timestamp: {$timestamp}",
            "X-SmartGram-Signature: sha256={$signature}"
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, min(2, $timeout));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For local self-signed dev

        $responseBody = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        $executionTimeMs = round((microtime(true) - $startTime) * 1000, 2);
        $deliveryStatus = ($httpCode >= 200 && $httpCode < 300) ? 'success' : 'failed';
        $errorMsg = $curlError ?: ($httpCode >= 400 ? "HTTP Error $httpCode" : null);

        // Always log dispatch in database
        self::logDispatch(
            $eventName,
            $entityType,
            $entityId,
            $url,
            $jsonPayload,
            $httpCode ?: null,
            substr((string)$responseBody, 0, 1000),
            $deliveryStatus,
            $executionTimeMs,
            $errorMsg
        );

        return [
            'status'    => $deliveryStatus,
            'http_code' => $httpCode,
            'time_ms'   => $executionTimeMs,
            'error'     => $errorMsg,
            'payload'   => $envelope
        ];
    }

    /**
     * Persist dispatch record into database
     */
    private static function logDispatch(
        string $event,
        string $entityType,
        ?int $entityId,
        string $url,
        string $payload,
        ?int $httpCode,
        ?string $response,
        string $status,
        float $timeMs,
        ?string $error
    ): void {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("
                INSERT INTO webhook_logs 
                (event_name, entity_type, entity_id, webhook_url, payload, http_status, response_body, status, execution_time_ms, error_message, created_at)
                VALUES 
                (:event, :entity_type, :entity_id, :url, :payload, :http_status, :response, :status, :time_ms, :error, NOW())
            ");
            $stmt->execute([
                ':event'       => $event,
                ':entity_type' => $entityType,
                ':entity_id'   => $entityId,
                ':url'         => $url,
                ':payload'     => $payload,
                ':http_status' => $httpCode,
                ':response'    => $response,
                ':status'      => $status,
                ':time_ms'     => $timeMs,
                ':error'       => $error
            ]);
        } catch (Exception $e) {
            error_log("Failed to write webhook log: " . $e->getMessage());
        }
    }

    /**
     * Event Hook: New Grievance Submitted by Citizen
     */
    public static function notifyGrievanceSubmitted(array $grievance): array {
        $trackingUrl = url("track?tracking_id=" . urlencode($grievance['tracking_id']) . "&phone=" . urlencode($grievance['citizen_phone']));

        $citizenName = $grievance['citizen_name'] ?? 'Citizen';
        $trackingId = $grievance['tracking_id'];
        $deptName = $grievance['dept_name_en'] ?? 'Panchayat';
        $deptNameTe = $grievance['dept_name_te'] ?? 'గ్రామ పంచాయతీ';
        $categoryNameEn = $grievance['category_name_en'] ?? 'General';
        $categoryNameTe = $grievance['category_name_te'] ?? 'సాధారణ';

        $payload = [
            'grievance_id'     => (int)$grievance['id'],
            'tracking_id'      => $trackingId,
            'citizen_name'     => $citizenName,
            'citizen_phone'    => $grievance['citizen_phone'],
            'village_name_en'  => $grievance['village_name_en'] ?? '',
            'village_name_te'  => $grievance['village_name_te'] ?? '',
            'ward_number'      => $grievance['ward_number'] ?? null,
            'category_name_en' => $categoryNameEn,
            'category_name_te' => $categoryNameTe,
            'priority'         => $grievance['priority'] ?? 'medium',
            'status'           => 'submitted',
            'tracking_url'     => $trackingUrl,
            'messages' => [
                'citizen_sms_te' => "నమస్కారం {$citizenName}, మీ సమస్య స్మార్ట్ గ్రామ్ పోర్టల్‌లో నమోదైంది. ఐడీ: {$trackingId}. విభాగం: {$deptNameTe}. స్థితిని తనిఖీ చేయండి: {$trackingUrl}",
                'citizen_sms_en' => "Dear {$citizenName}, your grievance {$trackingId} has been registered under {$deptName}. Track status anytime at: {$trackingUrl}"
            ]
        ];

        return self::dispatch('grievance_submitted', $payload, 'grievances', (int)$grievance['id']);
    }

    /**
     * Event Hook: Officer Assigned by Staff
     */
    public static function notifyOfficerAssigned(array $grievance, array $officer, ?string $priority = null): array {
        $trackingUrl = url("track?tracking_id=" . urlencode($grievance['tracking_id']) . "&phone=" . urlencode($grievance['citizen_phone']));
        $officerName = $officer['full_name'] ?? 'Officer';
        $officerPhone = $officer['phone'] ?? '';
        $citizenName = $grievance['citizen_name'] ?? 'Citizen';
        $trackingId = $grievance['tracking_id'];
        $wardNumber = $grievance['ward_number'] ?? 'N/A';
        $currentPriority = $priority ?: ($grievance['priority'] ?? 'medium');

        $payload = [
            'grievance_id'  => (int)$grievance['id'],
            'tracking_id'   => $trackingId,
            'citizen_name'  => $citizenName,
            'citizen_phone' => $grievance['citizen_phone'],
            'officer_id'    => (int)$officer['id'],
            'officer_name'  => $officerName,
            'officer_phone' => $officerPhone,
            'officer_email' => $officer['email'] ?? '',
            'ward_number'   => $wardNumber,
            'priority'      => $currentPriority,
            'status'        => 'assigned',
            'tracking_url'  => $trackingUrl,
            'messages' => [
                'citizen_sms_te' => "నమస్కారం {$citizenName}, మీ సమస్య ({{$trackingId}}) ఫీల్డ్ అధికారి {$officerName} గారికి ({$officerPhone}) కేటాయించబడింది.",
                'citizen_sms_en' => "Dear {$citizenName}, your grievance ({$trackingId}) has been assigned to Field Officer {$officerName} ({$officerPhone}).",
                'officer_alert'  => "NEW ASSIGNMENT: Grievance {$trackingId} in Ward {$wardNumber}. Priority: " . strtoupper($currentPriority) . ". Citizen: {$citizenName} ({$grievance['citizen_phone']})."
            ]
        ];

        return self::dispatch('officer_assigned', $payload, 'grievances', (int)$grievance['id']);
    }

    /**
     * Event Hook: Work Started by Officer
     */
    public static function notifyWorkStarted(array $grievance, array $officer, string $remarks): array {
        $trackingUrl = url("track?tracking_id=" . urlencode($grievance['tracking_id']) . "&phone=" . urlencode($grievance['citizen_phone']));
        $payload = [
            'grievance_id'  => (int)$grievance['id'],
            'tracking_id'   => $grievance['tracking_id'],
            'citizen_name'  => $grievance['citizen_name'],
            'citizen_phone' => $grievance['citizen_phone'],
            'officer_name'  => $officer['full_name'] ?? '',
            'remarks'       => $remarks,
            'status'        => 'in_progress',
            'tracking_url'  => $trackingUrl,
            'messages' => [
                'citizen_sms_te' => "నమస్కారం {$grievance['citizen_name']}, మీ సమస్య ({$grievance['tracking_id']}) పై ఫీల్డ్ వర్క్ ప్రారంభమైంది.",
                'citizen_sms_en' => "Dear {$grievance['citizen_name']}, work has commenced on your grievance ({$grievance['tracking_id']})."
            ]
        ];

        return self::dispatch('work_started', $payload, 'grievances', (int)$grievance['id']);
    }

    /**
     * Event Hook: Grievance Resolved with Photographic Evidence
     */
    public static function notifyGrievanceResolved(
        array $grievance, 
        array $officer, 
        string $resolutionRemarks, 
        ?string $evidencePhotoUrl = null
    ): array {
        $trackingUrl = url("track?tracking_id=" . urlencode($grievance['tracking_id']) . "&phone=" . urlencode($grievance['citizen_phone']));
        $citizenName = $grievance['citizen_name'];
        $trackingId = $grievance['tracking_id'];

        $payload = [
            'grievance_id'       => (int)$grievance['id'],
            'tracking_id'        => $trackingId,
            'citizen_name'       => $citizenName,
            'citizen_phone'      => $grievance['citizen_phone'],
            'officer_name'       => $officer['full_name'] ?? '',
            'resolution_remarks' => $resolutionRemarks,
            'evidence_photo_url' => $evidencePhotoUrl,
            'status'             => 'resolved',
            'tracking_url'       => $trackingUrl,
            'messages' => [
                'citizen_sms_te' => "నమస్కారం {$citizenName}, మీ సమస్య ({$trackingId}) పరిష్కరించబడింది! పని పూర్తయిన ఫోటోను పరిశీలించి మీ అభిప్రాయాన్ని తెలపండి: {$trackingUrl}",
                'citizen_sms_en' => "Dear {$citizenName}, your grievance ({$trackingId}) has been resolved with photo proof! Please review and verify: {$trackingUrl}"
            ]
        ];

        return self::dispatch('grievance_resolved', $payload, 'grievances', (int)$grievance['id']);
    }

    /**
     * Event Hook: Citizen Dissatisfied and Reopens Grievance
     */
    public static function notifyGrievanceReopened(array $grievance, string $reopenReason): array {
        $payload = [
            'grievance_id'  => (int)$grievance['id'],
            'tracking_id'   => $grievance['tracking_id'],
            'citizen_name'  => $grievance['citizen_name'],
            'citizen_phone' => $grievance['citizen_phone'],
            'ward_number'   => $grievance['ward_number'] ?? null,
            'reopen_reason' => $reopenReason,
            'status'        => 'reopened',
            'priority'      => 'high', // Automatically elevated
            'messages' => [
                'staff_alert' => "REOPEN ALERT: Citizen reopened grievance {$grievance['tracking_id']} in Ward {$grievance['ward_number']}. Reason: {$reopenReason}"
            ]
        ];

        return self::dispatch('grievance_reopened', $payload, 'grievances', (int)$grievance['id']);
    }

    /**
     * Event Hook: Generic Status Transition
     */
    public static function notifyStatusChanged(array $grievance, string $fromStatus, string $toStatus, string $remarks): array {
        $payload = [
            'grievance_id' => (int)$grievance['id'],
            'tracking_id'  => $grievance['tracking_id'],
            'from_status'  => $fromStatus,
            'to_status'    => $toStatus,
            'remarks'      => $remarks
        ];

        return self::dispatch('status_changed', $payload, 'grievances', (int)$grievance['id']);
    }

    /**
     * Automated SLA Escalation Scanner
     * Identifies overdue unresolved grievances and fires escalation webhooks
     */
    public static function triggerSlaEscalationCheck(): array {
        $db = Database::getConnection();

        // Find unresolved complaints exceeding SLA due date
        $stmt = $db->query("
            SELECT g.id, g.tracking_id, g.citizen_name, g.citizen_phone, g.ward_id, g.priority, g.status, g.sla_due_at,
                   w.ward_number,
                   c.name_en AS category_name_en, c.name_te AS category_name_te,
                   d.name_en AS dept_name_en, d.name_te AS dept_name_te,
                   u.full_name AS officer_name, u.phone AS officer_phone,
                   TIMESTAMPDIFF(HOUR, g.sla_due_at, NOW()) AS hours_overdue
            FROM grievances g
            JOIN categories c ON g.category_id = c.id
            JOIN departments d ON g.department_id = d.id
            LEFT JOIN wards w ON g.ward_id = w.id
            LEFT JOIN users u ON g.assigned_to_user_id = u.id
            WHERE g.sla_due_at < NOW()
              AND g.status NOT IN ('resolved', 'closed', 'rejected')
            ORDER BY g.sla_due_at ASC
        ");

        $overdueGrievances = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $dispatchedCount = 0;

        foreach ($overdueGrievances as $grv) {
            $payload = [
                'grievance_id'     => (int)$grv['id'],
                'tracking_id'      => $grv['tracking_id'],
                'ward_number'      => $grv['ward_number'] ?? 'N/A',
                'category_name_en' => $grv['category_name_en'],
                'category_name_te' => $grv['category_name_te'],
                'department_name'  => $grv['dept_name_en'],
                'assigned_officer' => $grv['officer_name'] ?? 'Unassigned',
                'officer_phone'    => $grv['officer_phone'] ?? '',
                'priority'         => $grv['priority'],
                'status'           => $grv['status'],
                'hours_overdue'    => (int)$grv['hours_overdue'],
                'sla_due_at'       => $grv['sla_due_at'],
                'messages' => [
                    'admin_alert' => "SLA BREACH ALERT: Grievance {$grv['tracking_id']} in Ward {$grv['ward_number']} is {$grv['hours_overdue']}h overdue! Immediate intervention required."
                ]
            ];

            self::dispatch('sla_breach_escalation', $payload, 'grievances', (int)$grv['id']);
            $dispatchedCount++;
        }

        return [
            'total_scanned'   => count($overdueGrievances),
            'breached_count'  => count($overdueGrievances),
            'dispatched_count'=> $dispatchedCount
        ];
    }

    /**
     * Aggregate Webhook Statistics for Admin Dashboard
     */
    public static function getStats(int $hours = 24): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT 
                COUNT(*) AS total,
                SUM(CASE WHEN status = 'success' THEN 1 ELSE 0 END) AS delivered,
                SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) AS failed,
                SUM(CASE WHEN status = 'simulated' THEN 1 ELSE 0 END) AS simulated,
                AVG(execution_time_ms) AS avg_latency_ms
            FROM webhook_logs
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL :hours HOUR)
        ");
        $stmt->bindValue(':hours', $hours, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return [
            'total'          => (int)($row['total'] ?? 0),
            'delivered'      => (int)($row['delivered'] ?? 0),
            'failed'         => (int)($row['failed'] ?? 0),
            'simulated'      => (int)($row['simulated'] ?? 0),
            'avg_latency_ms' => round((float)($row['avg_latency_ms'] ?? 0), 1)
        ];
    }

    /**
     * Retrieve Recent Webhook Logs for Admin Inspection
     */
    public static function getRecentLogs(int $limit = 10): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT * FROM webhook_logs 
            ORDER BY id DESC 
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
