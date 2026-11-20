<?php
// ==============================================================================
// Smart Gram - Grievance Model
// ==============================================================================

namespace App\Models;

use App\Core\Database;
use App\Services\WebhookService;
use Exception;
use PDO;

class Grievance {
    /**
     * Retrieve all active villages for form selection
     */
    public static function getVillages(): array {
        $db = Database::getConnection();
        return $db->query("
            SELECT id, name_en, name_te, mandal, district 
            FROM villages 
            WHERE is_active = 1 
            ORDER BY name_en ASC
        ")->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retrieve all wards for a specific village
     */
    public static function getWardsByVillage(int $villageId): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT id, village_id, ward_number, ward_name 
            FROM wards 
            WHERE village_id = :village_id 
            ORDER BY id ASC
        ");
        $stmt->execute([':village_id' => $villageId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retrieve active problem categories with department information
     */
    public static function getCategories(): array {
        $db = Database::getConnection();
        return $db->query("
            SELECT c.*, d.name_en AS dept_name_en, d.name_te AS dept_name_te, d.code AS dept_code
            FROM categories c
            JOIN departments d ON c.department_id = d.id
            WHERE c.is_active = 1
            ORDER BY c.department_id ASC, c.id ASC
        ")->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retrieve a category by its ID
     */
    public static function getCategoryById(int $id): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT c.*, d.name_en AS dept_name_en, d.name_te AS dept_name_te
            FROM categories c
            JOIN departments d ON c.department_id = d.id
            WHERE c.id = :id AND c.is_active = 1
            LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Find a grievance by its unique tracking ID (e.g. GRV-2026-00001)
     */
    public static function findByTrackingId(string $trackingId): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT g.*, 
                   v.name_en AS village_name_en, v.name_te AS village_name_te,
                   gp.id AS gram_panchayat_id, gp.name_en AS panchayat_name_en, gp.name_te AS panchayat_name_te,
                   m.id AS mandal_id, m.name_en AS mandal_name_en, m.name_te AS mandal_name_te,
                   dist.id AS district_id, dist.name_en AS district_name_en, dist.name_te AS district_name_te,
                   st.id AS state_id, st.name_en AS state_name_en, st.name_te AS state_name_te,
                   w.ward_number, w.ward_name,
                   c.name_en AS category_name_en, c.name_te AS category_name_te, c.sla_hours,
                   d.id AS department_id, d.name_en AS dept_name_en, d.name_te AS dept_name_te, d.code AS dept_code,
                   u.full_name AS assigned_officer_name, u.phone AS assigned_officer_phone
            FROM grievances g
            JOIN villages v ON g.village_id = v.id
            LEFT JOIN gram_panchayats gp ON COALESCE(g.gram_panchayat_id, v.gram_panchayat_id) = gp.id
            LEFT JOIN mandals m ON COALESCE(g.mandal_id, gp.mandal_id) = m.id
            LEFT JOIN districts dist ON COALESCE(g.district_id, m.district_id) = dist.id
            LEFT JOIN states st ON COALESCE(g.state_id, dist.state_id) = st.id
            LEFT JOIN wards w ON g.ward_id = w.id
            JOIN categories c ON g.category_id = c.id
            JOIN departments d ON g.department_id = d.id
            LEFT JOIN users u ON g.assigned_to_user_id = u.id
            WHERE g.tracking_id = :tracking_id
            LIMIT 1
        ");
        $stmt->execute([':tracking_id' => $trackingId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Find a grievance by its primary key ID
     */
    public static function findById(int $id): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT g.*, 
                   v.name_en AS village_name_en, v.name_te AS village_name_te,
                   gp.id AS gram_panchayat_id, gp.name_en AS panchayat_name_en, gp.name_te AS panchayat_name_te,
                   m.id AS mandal_id, m.name_en AS mandal_name_en, m.name_te AS mandal_name_te,
                   dist.id AS district_id, dist.name_en AS district_name_en, dist.name_te AS district_name_te,
                   st.id AS state_id, st.name_en AS state_name_en, st.name_te AS state_name_te,
                   w.ward_number, w.ward_name,
                   c.name_en AS category_name_en, c.name_te AS category_name_te, c.sla_hours,
                   d.id AS department_id, d.name_en AS dept_name_en, d.name_te AS dept_name_te, d.code AS dept_code,
                   u.full_name AS assigned_officer_name, u.phone AS assigned_officer_phone, u.email AS assigned_officer_email
            FROM grievances g
            JOIN villages v ON g.village_id = v.id
            LEFT JOIN gram_panchayats gp ON COALESCE(g.gram_panchayat_id, v.gram_panchayat_id) = gp.id
            LEFT JOIN mandals m ON COALESCE(g.mandal_id, gp.mandal_id) = m.id
            LEFT JOIN districts dist ON COALESCE(g.district_id, m.district_id) = dist.id
            LEFT JOIN states st ON COALESCE(g.state_id, dist.state_id) = st.id
            LEFT JOIN wards w ON g.ward_id = w.id
            JOIN categories c ON g.category_id = c.id
            JOIN departments d ON g.department_id = d.id
            LEFT JOIN users u ON g.assigned_to_user_id = u.id
            WHERE g.id = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Retrieve active officers, prioritized by department and geographic jurisdiction
     */
    public static function getEligibleOfficers(?int $departmentId = null, ?int $villageId = null, ?array $geoScope = null): array {
        $db = Database::getConnection();
        $where = ["u.role = 'officer'", "u.is_active = 1"];
        $params = [];

        // Auto-resolve geographic hierarchy for village if not supplied
        if ((empty($geoScope) || empty($geoScope['gram_panchayat_id'])) && !empty($villageId)) {
            $resolved = Location::resolveHierarchy($villageId);
            if ($resolved) {
                $geoScope = array_merge($geoScope ?? [], $resolved);
            }
        }

        // Geographic filtering for officers
        $geoConditions = [];
        // 1. Unrestricted officers (state scope or null scope)
        $geoConditions[] = "(u.location_scope IS NULL OR u.location_scope = 'state')";

        // 2. Village scope matching
        if (!empty($villageId)) {
            $geoConditions[] = "(u.location_scope = 'village' AND u.village_id = :geo_village_id)";
            $geoConditions[] = "(u.location_scope IS NULL AND (u.village_id = :geo_village_id_fallback OR u.village_id IS NULL))";
            $params[':geo_village_id'] = $villageId;
            $params[':geo_village_id_fallback'] = $villageId;
        }

        // 3. Gram Panchayat scope matching
        if (!empty($geoScope['gram_panchayat_id'])) {
            $geoConditions[] = "(u.location_scope = 'gram_panchayat' AND u.gram_panchayat_id = :geo_gp_id)";
            $params[':geo_gp_id'] = (int)$geoScope['gram_panchayat_id'];
        }

        // 4. Mandal scope matching
        if (!empty($geoScope['mandal_id'])) {
            $geoConditions[] = "(u.location_scope = 'mandal' AND u.mandal_id = :geo_mandal_id)";
            $params[':geo_mandal_id'] = (int)$geoScope['mandal_id'];
        }

        // 5. District scope matching
        if (!empty($geoScope['district_id'])) {
            $geoConditions[] = "(u.location_scope = 'district' AND u.district_id = :geo_district_id)";
            $params[':geo_district_id'] = (int)$geoScope['district_id'];
        }

        if (!empty($geoConditions)) {
            $where[] = "(" . implode(" OR ", $geoConditions) . ")";
        }

        $sql = "
            SELECT u.id, u.full_name, u.email, u.phone, u.department_id,
                   u.location_scope, u.mandal_id, u.district_id, u.gram_panchayat_id, u.village_id,
                   d.name_en AS dept_name_en, d.name_te AS dept_name_te
            FROM users u
            LEFT JOIN departments d ON u.department_id = d.id
            WHERE " . implode(" AND ", $where) . "
            ORDER BY (u.department_id = :dept_match) DESC, u.full_name ASC
        ";
        $params[':dept_match'] = $departmentId ?? 0;

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retrieve all active departments for referral
     */
    public static function getAllDepartments(): array {
        $db = Database::getConnection();
        return $db->query("
            SELECT id, name_en, name_te, code, description 
            FROM departments 
            WHERE is_active = 1 
            ORDER BY id ASC
        ")->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Find a grievance by tracking ID AND registered mobile number (Zero Citizen Login Verification)
     */
    public static function findByTrackingIdAndPhone(string $trackingId, string $phone): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT g.*, 
                   v.name_en AS village_name_en, v.name_te AS village_name_te,
                   gp.id AS gram_panchayat_id, gp.name_en AS panchayat_name_en, gp.name_te AS panchayat_name_te,
                   m.id AS mandal_id, m.name_en AS mandal_name_en, m.name_te AS mandal_name_te,
                   dist.id AS district_id, dist.name_en AS district_name_en, dist.name_te AS district_name_te,
                   st.id AS state_id, st.name_en AS state_name_en, st.name_te AS state_name_te,
                   w.ward_number, w.ward_name,
                   c.name_en AS category_name_en, c.name_te AS category_name_te, c.sla_hours,
                   d.name_en AS dept_name_en, d.name_te AS dept_name_te,
                   u.full_name AS assigned_officer_name, u.phone AS assigned_officer_phone
            FROM grievances g
            JOIN villages v ON g.village_id = v.id
            LEFT JOIN gram_panchayats gp ON COALESCE(g.gram_panchayat_id, v.gram_panchayat_id) = gp.id
            LEFT JOIN mandals m ON COALESCE(g.mandal_id, gp.mandal_id) = m.id
            LEFT JOIN districts dist ON COALESCE(g.district_id, m.district_id) = dist.id
            LEFT JOIN states st ON COALESCE(g.state_id, dist.state_id) = st.id
            LEFT JOIN wards w ON g.ward_id = w.id
            JOIN categories c ON g.category_id = c.id
            JOIN departments d ON g.department_id = d.id
            LEFT JOIN users u ON g.assigned_to_user_id = u.id
            WHERE g.tracking_id = :tracking_id AND g.citizen_phone = :phone
            LIMIT 1
        ");
        $stmt->execute([
            ':tracking_id' => trim($trackingId),
            ':phone' => trim($phone)
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Find attachments associated with a grievance
     */
    public static function getAttachments(int $grievanceId): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT * FROM attachments WHERE grievance_id = :grievance_id ORDER BY id ASC
        ");
        $stmt->execute([':grievance_id' => $grievanceId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retrieve complete chronological timeline for a grievance
     */
    public static function getTimeline(int $grievanceId): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT gu.*, u.full_name AS user_name, u.role AS user_role
            FROM grievance_updates gu
            LEFT JOIN users u ON gu.user_id = u.id
            WHERE gu.grievance_id = :grievance_id
            ORDER BY gu.id ASC
        ");
        $stmt->execute([':grievance_id' => $grievanceId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retrieve citizen feedback if already submitted
     */
    public static function getFeedback(int $grievanceId): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT * FROM feedback WHERE grievance_id = :grievance_id LIMIT 1
        ");
        $stmt->execute([':grievance_id' => $grievanceId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Save citizen satisfaction rating or reopen the grievance
     */
    public static function saveFeedback(int $grievanceId, bool $isSatisfied, ?int $rating, ?string $comment): void {
        $db = Database::getConnection();
        $db->beginTransaction();

        try {
            // 1. Fetch current grievance status
            $checkStmt = $db->prepare("SELECT status, tracking_id FROM grievances WHERE id = :id");
            $checkStmt->execute([':id' => $grievanceId]);
            $current = $checkStmt->fetch(PDO::FETCH_ASSOC);

            if (!$current) {
                throw new Exception("Grievance record not found.");
            }

            $currentStatus = $current['status'];

            // 2. Insert or replace feedback record
            $fbStmt = $db->prepare("
                INSERT INTO feedback (grievance_id, is_resolved_satisfied, rating, citizen_comment, created_at)
                VALUES (:grievance_id, :is_resolved, :rating, :citizen_comment, NOW())
                ON DUPLICATE KEY UPDATE 
                    is_resolved_satisfied = VALUES(is_resolved_satisfied),
                    rating = VALUES(rating),
                    citizen_comment = VALUES(citizen_comment)
            ");
            $fbStmt->execute([
                ':grievance_id'     => $grievanceId,
                ':is_resolved'      => $isSatisfied ? 1 : 0,
                ':rating'           => $isSatisfied ? $rating : null,
                ':citizen_comment'  => $comment
            ]);

            // 3. Update grievance lifecycle based on satisfaction
            if ($isSatisfied) {
                // Citizen confirmed resolution -> Close grievance
                $newStatus = 'closed';
                $action = 'citizen_feedback';
                $remarks = "Citizen verified and confirmed resolution. Rating: " . ($rating ?? 'N/A') . "/5 stars. " . ($comment ? "Remarks: {$comment}" : "");
            } else {
                // Citizen reported issue still exists -> Reopen grievance
                $newStatus = 'reopened';
                $action = 'reopen';
                $remarks = "Citizen reported issue NOT resolved. Reopen Reason: " . ($comment ?: 'No reason provided');
            }

            $updateGrievanceStmt = $db->prepare("
                UPDATE grievances 
                SET status = :new_status, updated_at = NOW() 
                WHERE id = :id
            ");
            $updateGrievanceStmt->execute([
                ':new_status' => $newStatus,
                ':id'         => $grievanceId
            ]);

            // 4. Log lifecycle event in grievance_updates
            $timelineStmt = $db->prepare("
                INSERT INTO grievance_updates (grievance_id, user_id, action, from_status, to_status, remarks, created_at)
                VALUES (:grievance_id, NULL, :action, :from_status, :to_status, :remarks, NOW())
            ");
            $timelineStmt->execute([
                ':grievance_id' => $grievanceId,
                ':action'       => $action,
                ':from_status'  => $currentStatus,
                ':to_status'    => $newStatus,
                ':remarks'      => $remarks
            ]);

            // 5. Audit log
            AuditLog::log($action, 'grievances', $grievanceId, null, [
                'tracking_id' => $current['tracking_id'],
                'is_satisfied' => $isSatisfied,
                'rating' => $rating
            ]);

            $db->commit();

            if (!$isSatisfied) {
                try {
                    $freshGrv = self::findById($grievanceId);
                    if ($freshGrv) {
                        WebhookService::notifyGrievanceReopened($freshGrv, $comment ?: 'No reason provided');
                    }
                } catch (Exception $wEx) {
                    error_log("Webhook dispatch error on reopen: " . $wEx->getMessage());
                }
            }
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /**
     * Staff Action: Verify a submitted grievance and move it to under_review
     */
    public static function verifyGrievance(int $grievanceId, int $userId, string $remarks = ''): void {
        $db = Database::getConnection();
        $db->beginTransaction();
        try {
            $checkStmt = $db->prepare("SELECT status, tracking_id FROM grievances WHERE id = :id");
            $checkStmt->execute([':id' => $grievanceId]);
            $current = $checkStmt->fetch(PDO::FETCH_ASSOC);
            if (!$current) throw new Exception("Grievance not found.");

            $stmt = $db->prepare("UPDATE grievances SET status = 'under_review', updated_at = NOW() WHERE id = :id");
            $stmt->execute([':id' => $grievanceId]);

            $updateStmt = $db->prepare("
                INSERT INTO grievance_updates (grievance_id, user_id, action, from_status, to_status, remarks, created_at)
                VALUES (:gid, :uid, 'staff_verified', :from_status, 'under_review', :remarks, NOW())
            ");
            $updateStmt->execute([
                ':gid' => $grievanceId,
                ':uid' => $userId,
                ':from_status' => $current['status'],
                ':remarks' => $remarks ?: 'Grievance verified and placed under review by Panchayat Staff'
            ]);

            AuditLog::log('staff_verified', 'grievances', $grievanceId, $userId, [
                'tracking_id' => $current['tracking_id']
            ]);

            $db->commit();

            try {
                $freshGrv = self::findById($grievanceId);
                if ($freshGrv) {
                    WebhookService::notifyStatusChanged($freshGrv, $current['status'], 'under_review', $remarks);
                }
            } catch (Exception $wEx) {
                error_log("Webhook dispatch error on verify: " . $wEx->getMessage());
            }
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /**
     * Staff/Admin Action: Assign (or reassign) grievance to field officer
     */
    public static function assignOfficer(int $grievanceId, int $officerId, int $userId, ?string $priority = null, string $remarks = ''): void {
        $db = Database::getConnection();
        $db->beginTransaction();
        try {
            $checkStmt = $db->prepare("SELECT status, tracking_id FROM grievances WHERE id = :id");
            $checkStmt->execute([':id' => $grievanceId]);
            $current = $checkStmt->fetch(PDO::FETCH_ASSOC);
            if (!$current) throw new Exception("Grievance not found.");

            $offStmt = $db->prepare("SELECT full_name FROM users WHERE id = :uid AND role = 'officer'");
            $offStmt->execute([':uid' => $officerId]);
            $officer = $offStmt->fetch(PDO::FETCH_ASSOC);
            $officerName = $officer['full_name'] ?? ('Officer #' . $officerId);

            $sql = "UPDATE grievances SET assigned_to_user_id = :officer_id, status = 'assigned', updated_at = NOW()";
            $params = [':officer_id' => $officerId, ':id' => $grievanceId];

            if ($priority && in_array($priority, ['low', 'medium', 'high', 'critical'], true)) {
                $sql .= ", priority = :priority";
                $params[':priority'] = $priority;
            }
            $sql .= " WHERE id = :id";

            $stmt = $db->prepare($sql);
            $stmt->execute($params);

            $updateStmt = $db->prepare("
                INSERT INTO grievance_updates (grievance_id, user_id, action, from_status, to_status, remarks, created_at)
                VALUES (:gid, :uid, 'staff_assigned', :from_status, 'assigned', :remarks, NOW())
            ");
            $fullRemarks = "Assigned to {$officerName}. " . ($remarks ? "Remarks: {$remarks}" : "");
            $updateStmt->execute([
                ':gid' => $grievanceId,
                ':uid' => $userId,
                ':from_status' => $current['status'],
                ':remarks' => $fullRemarks
            ]);

            AuditLog::log('staff_assigned', 'grievances', $grievanceId, $userId, [
                'tracking_id' => $current['tracking_id'],
                'assigned_to' => $officerId,
                'officer_name' => $officerName
            ]);

            $db->commit();

            try {
                $freshGrv = self::findById($grievanceId);
                $offStmt = $db->prepare("SELECT id, full_name, phone, email FROM users WHERE id = :id");
                $offStmt->execute([':id' => $officerId]);
                $officerData = $offStmt->fetch(PDO::FETCH_ASSOC);
                if ($freshGrv && $officerData) {
                    WebhookService::notifyOfficerAssigned($freshGrv, $officerData, $priority);
                }
            } catch (Exception $wEx) {
                error_log("Webhook dispatch error on assign: " . $wEx->getMessage());
            }
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /**
     * Officer Action: Start field work on site
     */
    public static function startWork(int $grievanceId, int $officerId, string $remarks = ''): void {
        $db = Database::getConnection();
        $db->beginTransaction();
        try {
            $checkStmt = $db->prepare("SELECT status, tracking_id FROM grievances WHERE id = :id");
            $checkStmt->execute([':id' => $grievanceId]);
            $current = $checkStmt->fetch(PDO::FETCH_ASSOC);
            if (!$current) throw new Exception("Grievance not found.");

            $stmt = $db->prepare("UPDATE grievances SET status = 'in_progress', updated_at = NOW() WHERE id = :id");
            $stmt->execute([':id' => $grievanceId]);

            $updateStmt = $db->prepare("
                INSERT INTO grievance_updates (grievance_id, user_id, action, from_status, to_status, remarks, created_at)
                VALUES (:gid, :uid, 'officer_started_work', :from_status, 'in_progress', :remarks, NOW())
            ");
            $updateStmt->execute([
                ':gid' => $grievanceId,
                ':uid' => $officerId,
                ':from_status' => $current['status'],
                ':remarks' => $remarks ?: 'Field officer has commenced work on site'
            ]);

            AuditLog::log('officer_started_work', 'grievances', $grievanceId, $officerId, [
                'tracking_id' => $current['tracking_id']
            ]);

            $db->commit();

            try {
                $freshGrv = self::findById($grievanceId);
                $offStmt = $db->prepare("SELECT id, full_name, phone, email FROM users WHERE id = :id");
                $offStmt->execute([':id' => $officerId]);
                $officerData = $offStmt->fetch(PDO::FETCH_ASSOC);
                if ($freshGrv) {
                    WebhookService::notifyWorkStarted($freshGrv, $officerData ?: [], $remarks);
                }
            } catch (Exception $wEx) {
                error_log("Webhook dispatch error on start work: " . $wEx->getMessage());
            }
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /**
     * Officer/Staff Action: Add interim progress remark without changing overall status
     */
    public static function addProgressNote(int $grievanceId, int $userId, string $remarks): void {
        $db = Database::getConnection();
        $checkStmt = $db->prepare("SELECT status, tracking_id FROM grievances WHERE id = :id");
        $checkStmt->execute([':id' => $grievanceId]);
        $current = $checkStmt->fetch(PDO::FETCH_ASSOC);
        if (!$current) throw new Exception("Grievance not found.");

        $updateStmt = $db->prepare("
            INSERT INTO grievance_updates (grievance_id, user_id, action, from_status, to_status, remarks, created_at)
            VALUES (:gid, :uid, 'progress_note', :from_status, :to_status, :remarks, NOW())
        ");
        $updateStmt->execute([
            ':gid'         => $grievanceId,
            ':uid'         => $userId,
            ':from_status' => $current['status'],
            ':to_status'   => $current['status'],
            ':remarks'     => $remarks
        ]);

        $db->prepare("UPDATE grievances SET updated_at = NOW() WHERE id = :id")->execute([':id' => $grievanceId]);

        AuditLog::log('progress_note', 'grievances', $grievanceId, $userId, [
            'tracking_id' => $current['tracking_id']
        ]);
    }

    /**
     * Officer Action: Resolve grievance and store mandatory photographic evidence
     */
    public static function resolveGrievance(int $grievanceId, int $officerId, string $remarks, ?array $uploadData = null): void {
        $db = Database::getConnection();
        $db->beginTransaction();
        try {
            $checkStmt = $db->prepare("SELECT status, tracking_id FROM grievances WHERE id = :id");
            $checkStmt->execute([':id' => $grievanceId]);
            $current = $checkStmt->fetch(PDO::FETCH_ASSOC);
            if (!$current) throw new Exception("Grievance not found.");

            // 1. Insert resolution photographic evidence attachment
            if ($uploadData) {
                $attStmt = $db->prepare("
                    INSERT INTO attachments (grievance_id, file_path, original_name, file_size, mime_type, attachment_type, uploaded_by_user_id, created_at)
                    VALUES (:gid, :path, :orig, :size, :mime, 'resolution_evidence', :uid, NOW())
                ");
                $attStmt->execute([
                    ':gid'  => $grievanceId,
                    ':path' => $uploadData['file_path'],
                    ':orig' => $uploadData['original_name'],
                    ':size' => $uploadData['file_size'],
                    ':mime' => $uploadData['mime_type'],
                    ':uid'  => $officerId
                ]);
            }

            // 2. Mark grievance status as resolved
            $stmt = $db->prepare("UPDATE grievances SET status = 'resolved', updated_at = NOW() WHERE id = :id");
            $stmt->execute([':id' => $grievanceId]);

            // 3. Log timeline event
            $updateStmt = $db->prepare("
                INSERT INTO grievance_updates (grievance_id, user_id, action, from_status, to_status, remarks, created_at)
                VALUES (:gid, :uid, 'officer_resolved', :from_status, 'resolved', :remarks, NOW())
            ");
            $updateStmt->execute([
                ':gid' => $grievanceId,
                ':uid' => $officerId,
                ':from_status' => $current['status'],
                ':remarks' => $remarks ?: 'Issue resolved on site with photographic evidence'
            ]);

            AuditLog::log('officer_resolved', 'grievances', $grievanceId, $officerId, [
                'tracking_id' => $current['tracking_id'],
                'has_evidence' => $uploadData !== null
            ]);

            $db->commit();

            try {
                $freshGrv = self::findById($grievanceId);
                $offStmt = $db->prepare("SELECT id, full_name, phone, email FROM users WHERE id = :id");
                $offStmt->execute([':id' => $officerId]);
                $officerData = $offStmt->fetch(PDO::FETCH_ASSOC);
                $photoUrl = $uploadData ? url($uploadData['file_path']) : null;
                if ($freshGrv) {
                    WebhookService::notifyGrievanceResolved($freshGrv, $officerData ?: [], $remarks, $photoUrl);
                }
            } catch (Exception $wEx) {
                error_log("Webhook dispatch error on resolve: " . $wEx->getMessage());
            }
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /**
     * Staff/Admin Action: Reject grievance with mandatory explanation
     */
    public static function rejectGrievance(int $grievanceId, int $userId, string $remarks): void {
        $db = Database::getConnection();
        $db->beginTransaction();
        try {
            $checkStmt = $db->prepare("SELECT status, tracking_id FROM grievances WHERE id = :id");
            $checkStmt->execute([':id' => $grievanceId]);
            $current = $checkStmt->fetch(PDO::FETCH_ASSOC);
            if (!$current) throw new Exception("Grievance not found.");

            $stmt = $db->prepare("UPDATE grievances SET status = 'rejected', updated_at = NOW() WHERE id = :id");
            $stmt->execute([':id' => $grievanceId]);

            $updateStmt = $db->prepare("
                INSERT INTO grievance_updates (grievance_id, user_id, action, from_status, to_status, remarks, created_at)
                VALUES (:gid, :uid, 'staff_rejected', :from_status, 'rejected', :remarks, NOW())
            ");
            $updateStmt->execute([
                ':gid' => $grievanceId,
                ':uid' => $userId,
                ':from_status' => $current['status'],
                ':remarks' => $remarks
            ]);

            AuditLog::log('staff_rejected', 'grievances', $grievanceId, $userId, [
                'tracking_id' => $current['tracking_id'],
                'reason' => $remarks
            ]);

            $db->commit();

            try {
                $freshGrv = self::findById($grievanceId);
                if ($freshGrv) {
                    WebhookService::notifyStatusChanged($freshGrv, $current['status'], 'rejected', $remarks);
                }
            } catch (Exception $wEx) {
                error_log("Webhook dispatch error on reject: " . $wEx->getMessage());
            }
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /**
     * Staff/Admin Action: Refer grievance to another department
     */
    public static function referDepartment(int $grievanceId, int $newDepartmentId, int $userId, string $remarks): void {
        $db = Database::getConnection();
        $db->beginTransaction();
        try {
            $checkStmt = $db->prepare("SELECT status, tracking_id FROM grievances WHERE id = :id");
            $checkStmt->execute([':id' => $grievanceId]);
            $current = $checkStmt->fetch(PDO::FETCH_ASSOC);
            if (!$current) throw new Exception("Grievance not found.");

            $deptStmt = $db->prepare("SELECT name_en, name_te FROM departments WHERE id = :id");
            $deptStmt->execute([':id' => $newDepartmentId]);
            $dept = $deptStmt->fetch(PDO::FETCH_ASSOC);
            $deptName = $dept['name_en'] ?? ('Department #' . $newDepartmentId);

            $stmt = $db->prepare("
                UPDATE grievances 
                SET department_id = :dept_id, assigned_to_user_id = NULL, status = 'referred', updated_at = NOW() 
                WHERE id = :id
            ");
            $stmt->execute([':dept_id' => $newDepartmentId, ':id' => $grievanceId]);

            $updateStmt = $db->prepare("
                INSERT INTO grievance_updates (grievance_id, user_id, action, from_status, to_status, remarks, created_at)
                VALUES (:gid, :uid, 'department_referred', :from_status, 'referred', :remarks, NOW())
            ");
            $fullRemarks = "Referred to {$deptName}. " . ($remarks ? "Remarks: {$remarks}" : "");
            $updateStmt->execute([
                ':gid' => $grievanceId,
                ':uid' => $userId,
                ':from_status' => $current['status'],
                ':remarks' => $fullRemarks
            ]);

            AuditLog::log('department_referred', 'grievances', $grievanceId, $userId, [
                'tracking_id' => $current['tracking_id'],
                'to_department' => $newDepartmentId
            ]);

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /**
     * Map lifecycle status to numerical progress percentage
     */
    public static function getProgressPercentage(string $status): int {
        return match($status) {
            'submitted'             => 15,
            'under_review'          => 30,
            'verified'              => 45,
            'assigned'              => 60,
            'in_progress'           => 75,
            'resolved'              => 90,
            'citizen_verification'  => 90,
            'closed'                => 100,
            'reopened'              => 45,
            'rejected'              => 100,
            default                 => 10
        };
    }

    /**
     * Helper to return badge color classes for each status
     */
    public static function getStatusBadgeClass(string $status): string {
        return match($status) {
            'submitted'             => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
            'under_review'          => 'bg-info-subtle text-info-emphasis border border-info-subtle',
            'verified'              => 'bg-primary-subtle text-primary border border-primary-subtle',
            'assigned'              => 'bg-primary-subtle text-primary border border-primary-subtle',
            'in_progress'           => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
            'referred'              => 'bg-dark-subtle text-dark border border-dark-subtle',
            'resolved'              => 'bg-success-subtle text-success border border-success-subtle',
            'citizen_verification'  => 'bg-warning-subtle text-dark border border-warning-subtle',
            'closed'                => 'bg-success text-white',
            'reopened'              => 'bg-danger-subtle text-danger border border-danger-subtle',
            'rejected'              => 'bg-danger text-white',
            default                 => 'bg-light text-dark'
        };
    }

    /**
     * Priority badge class helper
     */
    public static function getPriorityBadgeClass(string $priority): string {
        return match($priority) {
            'low'      => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
            'medium'   => 'bg-info-subtle text-info-emphasis border border-info-subtle',
            'high'     => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
            'critical' => 'bg-danger text-white border border-danger',
            default    => 'bg-light text-dark'
        };
    }

    /**
     * Compute SLA indicator badge details
     */
    public static function getSlaBadge(?string $slaDueAt, string $status): array {
        if (!$slaDueAt) {
            return [
                'text' => 'No SLA',
                'class' => 'bg-light text-muted',
                'icon' => 'bi-clock',
                'is_overdue' => false
            ];
        }

        if (in_array($status, ['resolved', 'closed'])) {
            return [
                'text' => 'Resolved',
                'class' => 'bg-success-subtle text-success border border-success-subtle',
                'icon' => 'bi-check2-circle',
                'is_overdue' => false
            ];
        }

        $now = time();
        $due = strtotime($slaDueAt);
        $diffSeconds = $due - $now;

        if ($diffSeconds < 0) {
            // Overdue
            $hoursOverdue = (int)ceil(abs($diffSeconds) / 3600);
            $text = ($hoursOverdue >= 24) 
                ? round($hoursOverdue / 24, 1) . ' days overdue' 
                : $hoursOverdue . ' hrs overdue';

            return [
                'text' => $text,
                'class' => 'bg-danger text-white border border-danger fw-bold animate-pulse',
                'icon' => 'bi-exclamation-triangle-fill',
                'is_overdue' => true,
                'hours' => $hoursOverdue
            ];
        } else {
            // Still on time
            $hoursLeft = (int)floor($diffSeconds / 3600);
            $text = ($hoursLeft >= 24) 
                ? round($hoursLeft / 24, 1) . ' days left' 
                : ($hoursLeft > 0 ? $hoursLeft . ' hrs left' : '< 1 hr left');

            $class = ($hoursLeft <= 6)
                ? 'bg-warning-subtle text-warning-emphasis border border-warning-subtle fw-semibold'
                : 'bg-light text-secondary border';

            return [
                'text' => $text,
                'class' => $class,
                'icon' => 'bi-clock-history',
                'is_overdue' => false,
                'hours' => $hoursLeft
            ];
        }
    }

    /**
     * Helper to resolve WHERE clause snippet from integer villageId or geographic scope array
     */
    private static function buildScopeFilter(int|array|null $scope, string $alias = '', string $prefix = 'sc'): array {
        if ($scope === null) {
            return ['', []];
        }

        if (is_int($scope)) {
            $col = $alias ? "{$alias}.village_id" : "village_id";
            return ["{$col} = :{$prefix}_vid", [":{$prefix}_vid" => $scope]];
        }

        $conditions = [];
        $params = [];
        $p = $alias ? "{$alias}." : "";

        if (!empty($scope['village_id']) && $scope['village_id'] !== 'all') {
            $conditions[] = "{$p}village_id = :{$prefix}_vid";
            $params[":{$prefix}_vid"] = (int)$scope['village_id'];
        } elseif (!empty($scope['gram_panchayat_id']) && $scope['gram_panchayat_id'] !== 'all') {
            $conditions[] = "{$p}gram_panchayat_id = :{$prefix}_gpid";
            $params[":{$prefix}_gpid"] = (int)$scope['gram_panchayat_id'];
        } elseif (!empty($scope['mandal_id']) && $scope['mandal_id'] !== 'all') {
            $conditions[] = "{$p}mandal_id = :{$prefix}_mid";
            $params[":{$prefix}_mid"] = (int)$scope['mandal_id'];
        } elseif (!empty($scope['district_id']) && $scope['district_id'] !== 'all') {
            $conditions[] = "{$p}district_id = :{$prefix}_did";
            $params[":{$prefix}_did"] = (int)$scope['district_id'];
        } elseif (!empty($scope['state_id']) && $scope['state_id'] !== 'all') {
            $conditions[] = "{$p}state_id = :{$prefix}_sid";
            $params[":{$prefix}_sid"] = (int)$scope['state_id'];
        }

        return [implode(' AND ', $conditions), $params];
    }

    /**
     * Retrieve complete performance metrics and analytics KPIs (optionally scoped by location)
     */
    public static function getAnalyticsSummary(int|array|null $scope = null): array {
        $db = Database::getConnection();
        [$filterSql, $params] = self::buildScopeFilter($scope, '');
        $whereClause = $filterSql ? "WHERE {$filterSql}" : "";
        $whereAndClause = $filterSql ? "AND {$filterSql}" : "";

        // Status counts
        $statusStmt = $db->prepare("
            SELECT status, COUNT(*) as cnt 
            FROM grievances 
            $whereClause 
            GROUP BY status
        ");
        $statusStmt->execute($params);
        $statusCounts = $statusStmt->fetchAll(PDO::FETCH_KEY_PAIR);

        $total = array_sum($statusCounts);
        $pending = ($statusCounts['submitted'] ?? 0) + ($statusCounts['under_review'] ?? 0) + ($statusCounts['verified'] ?? 0);
        $assigned = $statusCounts['assigned'] ?? 0;
        $inProgress = $statusCounts['in_progress'] ?? 0;
        $resolved = ($statusCounts['resolved'] ?? 0) + ($statusCounts['citizen_verification'] ?? 0);
        $closed = $statusCounts['closed'] ?? 0;
        $reopened = $statusCounts['reopened'] ?? 0;
        $rejected = $statusCounts['rejected'] ?? 0;

        // Overdue count (where sla_due_at < NOW() and status not in resolved, closed, rejected)
        $overdueStmt = $db->prepare("
            SELECT COUNT(*) 
            FROM grievances 
            WHERE sla_due_at < NOW() 
              AND status NOT IN ('resolved', 'closed', 'rejected')
              $whereAndClause
        ");
        $overdueStmt->execute($params);
        $overdue = (int)$overdueStmt->fetchColumn();

        // Critical priority count
        $criticalStmt = $db->prepare("
            SELECT COUNT(*) 
            FROM grievances 
            WHERE priority = 'critical' 
              AND status NOT IN ('closed', 'rejected')
              $whereAndClause
        ");
        $criticalStmt->execute($params);
        $critical = (int)$criticalStmt->fetchColumn();

        // Citizen satisfaction ratings
        $feedFilter = $filterSql ? "AND " . str_replace(['village_id', 'gram_panchayat_id', 'mandal_id', 'district_id', 'state_id'], ['g.village_id', 'g.gram_panchayat_id', 'g.mandal_id', 'g.district_id', 'g.state_id'], $filterSql) : "";
        $feedbackStmt = $db->prepare("
            SELECT AVG(f.rating) as avg_rating, COUNT(f.rating) as total_ratings 
            FROM feedback f
            JOIN grievances g ON f.grievance_id = g.id
            WHERE f.rating IS NOT NULL
            $feedFilter
        ");
        $feedbackStmt->execute($params);
        $feedbackStats = $feedbackStmt->fetch(PDO::FETCH_ASSOC);

        $avgRating = $feedbackStats && $feedbackStats['avg_rating'] !== null 
            ? round((float)$feedbackStats['avg_rating'], 1) 
            : 0.0;
        $totalRatings = (int)($feedbackStats['total_ratings'] ?? 0);

        // SLA Compliance rate (%)
        $complianceRate = ($total > 0) ? max(0, round((($total - $overdue) / $total) * 100, 1)) : 100.0;

        return [
            'total'           => $total,
            'pending'         => $pending,
            'assigned'        => $assigned,
            'in_progress'     => $inProgress,
            'resolved'        => $resolved,
            'closed'          => $closed,
            'reopened'        => $reopened,
            'rejected'        => $rejected,
            'overdue'         => $overdue,
            'critical'        => $critical,
            'avg_rating'      => $avgRating,
            'total_ratings'   => $totalRatings,
            'compliance_rate' => $complianceRate
        ];
    }

    /**
     * Retrieve category breakdown with counts, percentages, and icons (optionally scoped)
     */
    public static function getCategoryBreakdown(int|array|null $scope = null): array {
        $db = Database::getConnection();
        [$filterSql, $params] = self::buildScopeFilter($scope, 'g');
        $whereClause = $filterSql ? "WHERE {$filterSql}" : "";
        $andClause = $filterSql ? "AND {$filterSql}" : "";

        $countStmt = $db->prepare("SELECT COUNT(*) FROM grievances g $whereClause");
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();

        $sql = "
            SELECT c.id, c.name_en, c.name_te, c.icon, d.code as dept_code, d.name_en as dept_name_en, d.name_te as dept_name_te,
                   COUNT(g.id) as count
            FROM categories c
            JOIN departments d ON c.department_id = d.id
            LEFT JOIN grievances g ON c.id = g.category_id $andClause
            GROUP BY c.id, c.name_en, c.name_te, c.icon, d.code, d.name_en, d.name_te
            HAVING count > 0
            ORDER BY count DESC
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as &$r) {
            $r['percentage'] = $total > 0 ? round(($r['count'] / $total) * 100, 1) : 0;
        }

        return $rows;
    }

    /**
     * Retrieve ward hotspot analysis with percentages (optionally scoped)
     */
    public static function getWardBreakdown(int|array|null $scope = null): array {
        $db = Database::getConnection();
        [$gFilter, $params] = self::buildScopeFilter($scope, 'g', 'g');

        $whereG = $gFilter ? "WHERE {$gFilter}" : "";
        $andG = $gFilter ? "AND {$gFilter}" : "";

        $tStmt = $db->prepare("SELECT COUNT(*) FROM grievances g $whereG");
        $tStmt->execute($params);
        $total = (int)$tStmt->fetchColumn();

        $wConditions = [];
        $wParams = [];
        if (is_int($scope) && $scope > 0) {
            $wConditions[] = "w.village_id = :w_vid";
            $wParams[':w_vid'] = $scope;
        } elseif (is_array($scope)) {
            if (!empty($scope['village_id']) && $scope['village_id'] !== 'all') {
                $wConditions[] = "w.village_id = :w_vid";
                $wParams[':w_vid'] = (int)$scope['village_id'];
            } elseif (!empty($scope['gram_panchayat_id']) && $scope['gram_panchayat_id'] !== 'all') {
                $wConditions[] = "v.gram_panchayat_id = :w_gpid";
                $wParams[':w_gpid'] = (int)$scope['gram_panchayat_id'];
            } elseif (!empty($scope['mandal_id']) && $scope['mandal_id'] !== 'all') {
                $wConditions[] = "gp.mandal_id = :w_mid";
                $wParams[':w_mid'] = (int)$scope['mandal_id'];
            } elseif (!empty($scope['district_id']) && $scope['district_id'] !== 'all') {
                $wConditions[] = "m.district_id = :w_did";
                $wParams[':w_did'] = (int)$scope['district_id'];
            }
        }
        $whereW = !empty($wConditions) ? "WHERE " . implode(' AND ', $wConditions) : "";

        $sql = "
            SELECT w.id, w.ward_number, w.ward_name,
                   COUNT(g.id) as count
            FROM wards w
            JOIN villages v ON w.village_id = v.id
            LEFT JOIN gram_panchayats gp ON v.gram_panchayat_id = gp.id
            LEFT JOIN mandals m ON gp.mandal_id = m.id
            LEFT JOIN grievances g ON w.id = g.ward_id $andG
            $whereW
            GROUP BY w.id, w.ward_number, w.ward_name
            ORDER BY count DESC, w.id ASC
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute(array_merge($params, $wParams));
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $maxCount = 0;
        foreach ($rows as &$r) {
            $r['percentage'] = $total > 0 ? round(($r['count'] / $total) * 100, 1) : 0;
            if ($r['count'] > $maxCount) {
                $maxCount = $r['count'];
            }
        }

        foreach ($rows as &$r) {
            $r['is_hotspot'] = ($maxCount > 0 && $r['count'] === $maxCount);
        }

        return $rows;
    }

    /**
     * Retrieve workload distribution among active field officers (optionally scoped)
     */
    public static function getWorkloadDistribution(int|array|null $scope = null): array {
        $db = Database::getConnection();
        [$uFilter, $params] = self::buildScopeFilter($scope, 'u');
        $andU = $uFilter ? "AND {$uFilter}" : "";

        $sql = "
            SELECT u.id, u.full_name, u.email, u.phone, d.name_en AS dept_name_en, d.name_te AS dept_name_te,
                   COUNT(CASE WHEN g.status IN ('assigned') THEN 1 END) AS assigned_count,
                   COUNT(CASE WHEN g.status IN ('in_progress') THEN 1 END) AS in_progress_count,
                   COUNT(CASE WHEN g.status IN ('resolved', 'closed') THEN 1 END) AS resolved_count,
                   COUNT(CASE WHEN g.sla_due_at < NOW() AND g.status IN ('assigned', 'in_progress') THEN 1 END) AS overdue_count,
                   COUNT(g.id) AS total_assigned
            FROM users u
            LEFT JOIN departments d ON u.department_id = d.id
            LEFT JOIN grievances g ON u.id = g.assigned_to_user_id
            WHERE u.role = 'officer' AND u.is_active = 1 $andU
            GROUP BY u.id, u.full_name, u.email, u.phone, d.name_en, d.name_te
            ORDER BY total_assigned DESC, u.full_name ASC
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retrieve urgent overdue grievances requiring immediate administrative intervention (optionally scoped)
     */
    public static function getOverdueGrievances(int|array|null $scope = null, int $limit = 10): array {
        $db = Database::getConnection();
        [$filterSql, $params] = self::buildScopeFilter($scope, 'g');
        $andFilter = $filterSql ? "AND {$filterSql}" : "";

        $sql = "
            SELECT g.*, 
                   w.ward_number, w.ward_name,
                   c.name_en AS category_name_en, c.name_te AS category_name_te,
                   u.full_name AS assigned_officer_name,
                   TIMESTAMPDIFF(HOUR, g.sla_due_at, NOW()) AS hours_overdue
            FROM grievances g
            LEFT JOIN wards w ON g.ward_id = w.id
            JOIN categories c ON g.category_id = c.id
            LEFT JOIN users u ON g.assigned_to_user_id = u.id
            WHERE g.sla_due_at < NOW() 
              AND g.status NOT IN ('resolved', 'closed', 'rejected')
              $andFilter
            ORDER BY g.sla_due_at ASC
            LIMIT " . (int)$limit . "
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Comprehensive query builder for filterable, searchable, and paginated grievances
     */
    public static function getFilteredGrievances(array $filters = [], int $limit = 20, int $offset = 0): array {
        $db = Database::getConnection();

        $where = ["1=1"];
        $params = [];

        if (!empty($filters['state_id']) && $filters['state_id'] !== 'all') {
            $where[] = "COALESCE(g.state_id, dist.state_id) = :state_id";
            $params[':state_id'] = (int)$filters['state_id'];
        }

        if (!empty($filters['district_id']) && $filters['district_id'] !== 'all') {
            $where[] = "COALESCE(g.district_id, m.district_id) = :district_id";
            $params[':district_id'] = (int)$filters['district_id'];
        }

        if (!empty($filters['mandal_id']) && $filters['mandal_id'] !== 'all') {
            $where[] = "COALESCE(g.mandal_id, gp.mandal_id) = :mandal_id";
            $params[':mandal_id'] = (int)$filters['mandal_id'];
        }

        if (!empty($filters['gram_panchayat_id']) && $filters['gram_panchayat_id'] !== 'all') {
            $where[] = "COALESCE(g.gram_panchayat_id, v.gram_panchayat_id) = :gram_panchayat_id";
            $params[':gram_panchayat_id'] = (int)$filters['gram_panchayat_id'];
        }

        if (!empty($filters['village_id']) && $filters['village_id'] !== 'all') {
            $where[] = "g.village_id = :village_id";
            $params[':village_id'] = (int)$filters['village_id'];
        }

        if (!empty($filters['ward_id']) && $filters['ward_id'] !== 'all') {
            $where[] = "g.ward_id = :ward_id";
            $params[':ward_id'] = (int)$filters['ward_id'];
        }

        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $where[] = "g.status = :status";
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['category_id']) && $filters['category_id'] !== 'all') {
            $where[] = "g.category_id = :category_id";
            $params[':category_id'] = (int)$filters['category_id'];
        }

        if (!empty($filters['department_id']) && $filters['department_id'] !== 'all') {
            $where[] = "g.department_id = :department_id";
            $params[':department_id'] = (int)$filters['department_id'];
        }

        if (!empty($filters['priority']) && $filters['priority'] !== 'all') {
            $where[] = "g.priority = :priority";
            $params[':priority'] = $filters['priority'];
        }

        if (!empty($filters['assigned_to']) && $filters['assigned_to'] !== 'all') {
            $where[] = "g.assigned_to_user_id = :assigned_to";
            $params[':assigned_to'] = (int)$filters['assigned_to'];
        }

        if (!empty($filters['sla_status'])) {
            if ($filters['sla_status'] === 'overdue') {
                $where[] = "g.sla_due_at < NOW() AND g.status NOT IN ('resolved', 'closed', 'rejected')";
            } elseif ($filters['sla_status'] === 'due_today') {
                $where[] = "DATE(g.sla_due_at) = CURDATE() AND g.status NOT IN ('resolved', 'closed', 'rejected')";
            } elseif ($filters['sla_status'] === 'on_time') {
                $where[] = "(g.sla_due_at >= NOW() OR g.status IN ('resolved', 'closed'))";
            }
        }

        if (!empty($filters['search'])) {
            $search = '%' . trim($filters['search']) . '%';
            $where[] = "(g.tracking_id LIKE :s_id OR g.citizen_name LIKE :s_name OR g.citizen_phone LIKE :s_phone OR g.description LIKE :s_desc)";
            $params[':s_id'] = $search;
            $params[':s_name'] = $search;
            $params[':s_phone'] = $search;
            $params[':s_desc'] = $search;
        }

        if (!empty($filters['date_from'])) {
            $where[] = "DATE(g.created_at) >= :date_from";
            $params[':date_from'] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $where[] = "DATE(g.created_at) <= :date_to";
            $params[':date_to'] = $filters['date_to'];
        }

        $whereClause = implode(" AND ", $where);

        $geoJoinSql = "
            FROM grievances g
            JOIN villages v ON g.village_id = v.id
            LEFT JOIN gram_panchayats gp ON COALESCE(g.gram_panchayat_id, v.gram_panchayat_id) = gp.id
            LEFT JOIN mandals m ON COALESCE(g.mandal_id, gp.mandal_id) = m.id
            LEFT JOIN districts dist ON COALESCE(g.district_id, m.district_id) = dist.id
            LEFT JOIN states st ON COALESCE(g.state_id, dist.state_id) = st.id
        ";

        // 1. Total matching count
        $countSql = "SELECT COUNT(*) $geoJoinSql WHERE $whereClause";
        $countStmt = $db->prepare($countSql);
        $countStmt->execute($params);
        $totalCount = (int)$countStmt->fetchColumn();

        // 2. Fetch paginated records
        $sql = "
            SELECT g.*, 
                   COALESCE(g.gram_panchayat_id, v.gram_panchayat_id) AS gram_panchayat_id,
                   COALESCE(g.mandal_id, gp.mandal_id) AS mandal_id,
                   COALESCE(g.district_id, m.district_id) AS district_id,
                   COALESCE(g.state_id, dist.state_id) AS state_id,
                   v.name_en AS village_name_en, v.name_te AS village_name_te,
                   gp.name_en AS panchayat_name_en, gp.name_te AS panchayat_name_te,
                   m.name_en AS mandal_name_en, m.name_te AS mandal_name_te,
                   dist.name_en AS district_name_en, dist.name_te AS district_name_te,
                   st.name_en AS state_name_en, st.name_te AS state_name_te,
                   w.ward_number, w.ward_name,
                   c.name_en AS category_name_en, c.name_te AS category_name_te, c.icon AS category_icon,
                   d.name_en AS dept_name_en, d.name_te AS dept_name_te, d.code AS dept_code,
                   u.full_name AS assigned_officer_name,
                   f.rating AS citizen_rating, f.is_resolved_satisfied
            $geoJoinSql
            LEFT JOIN wards w ON g.ward_id = w.id
            JOIN categories c ON g.category_id = c.id
            JOIN departments d ON g.department_id = d.id
            LEFT JOIN users u ON g.assigned_to_user_id = u.id
            LEFT JOIN feedback f ON g.id = f.grievance_id
            WHERE $whereClause
            ORDER BY 
                CASE WHEN g.status IN ('reopened', 'submitted') THEN 1 
                     WHEN g.sla_due_at < NOW() AND g.status NOT IN ('resolved', 'closed', 'rejected') THEN 2
                     ELSE 3 END ASC,
                g.created_at DESC
            LIMIT " . (int)$limit . " OFFSET " . (int)$offset;

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $totalPages = $totalCount > 0 ? (int)ceil($totalCount / $limit) : 1;
        $currentPage = ($limit > 0) ? (int)floor($offset / $limit) + 1 : 1;

        return [
            'items' => $items,
            'total' => $totalCount,
            'pages' => $totalPages,
            'current_page' => $currentPage
        ];
    }

    /**
     * Retrieve all filtered grievances for CSV / Print export without pagination limits
     */
    public static function getAllGrievancesForExport(array $filters = []): array {
        $result = self::getFilteredGrievances($filters, 10000, 0);
        return $result['items'];
    }
}

