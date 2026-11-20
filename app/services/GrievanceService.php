<?php
// ==============================================================================
// Smart Gram - Grievance Business Service
// Handles atomic grievance registration, unique ID generation, and timeline updates.
// ==============================================================================

namespace App\Services;

require_once __DIR__ . '/../models/Location.php';
require_once __DIR__ . '/../models/AuditLog.php';
require_once __DIR__ . '/../models/Grievance.php';

use App\Core\Database;
use App\Models\Grievance;
use App\Models\Location;
use App\Models\AuditLog;
use Exception;
use PDO;

class GrievanceService {
    /**
     * Generate the next unique Grievance Tracking ID
     * Format: GRV-YYYY-XXXXX (e.g. GRV-2026-00001)
     */
    public static function generateTrackingId(): string {
        $db = Database::getConnection();
        $currentYear = (int)date('Y');
        $prefix = "GRV-{$currentYear}-";

        // Query the highest existing sequence for the current year
        $stmt = $db->prepare("
            SELECT tracking_id 
            FROM grievances 
            WHERE tracking_id LIKE :prefix 
            ORDER BY id DESC 
            LIMIT 1
        ");
        $stmt->execute([':prefix' => $prefix . '%']);
        $latest = $stmt->fetchColumn();

        if ($latest) {
            $parts = explode('-', $latest);
            $lastNum = isset($parts[2]) ? (int)$parts[2] : 0;
            $nextNum = $lastNum + 1;
        } else {
            $nextNum = 1;
        }

        // Retry loop: handle rare collision from concurrent inserts
        $attempts = 0;
        do {
            $candidate = sprintf("GRV-%d-%05d", $currentYear, $nextNum + $attempts);
            $check = $db->prepare("SELECT COUNT(*) FROM grievances WHERE tracking_id = :tid");
            $check->execute([':tid' => $candidate]);
            if ((int)$check->fetchColumn() === 0) {
                return $candidate;
            }
            $attempts++;
        } while ($attempts < 10);

        // Last resort: use timestamp-based suffix to guarantee uniqueness
        return sprintf("GRV-%d-%05d", $currentYear, $nextNum + 1000 + rand(1, 9999));
    }

    /**
     * Create and record a new grievance submitted by a citizen (NO CITIZEN LOGIN)
     *
     * @param array $data Form data array
     * @param array|null $file Optional uploaded file ($_FILES['photo'])
     * @return array Created grievance record
     * @throws Exception If validation or database transaction fails
     */
    public static function createGrievance(array $data, ?array $file = null): array {
        $db = Database::getConnection();
        $db->beginTransaction();

        try {
            // 1. Verify category and retrieve routing department & SLA
            $categoryId = (int)($data['category_id'] ?? 0);
            $category = Grievance::getCategoryById($categoryId);

            if (!$category) {
                throw new Exception("The selected problem category is invalid.");
            }

            $departmentId = (int)$category['department_id'];
            $priority = $category['default_priority'] ?? 'medium';
            $slaHours = (int)($category['sla_hours'] ?? 48);
            $slaDueAt = date('Y-m-d H:i:s', time() + ($slaHours * 3600));

            // 2. Generate unique tracking ID
            $trackingId = self::generateTrackingId();

            // 3. Prepare parameters & resolve geographic hierarchy
            $citizenName = trim($data['citizen_name'] ?? '');
            $citizenPhone = trim($data['citizen_phone'] ?? '');
            $villageId = (int)($data['village_id'] ?? 1);
            $wardId = !empty($data['ward_id']) ? (int)$data['ward_id'] : null;
            $description = trim($data['description'] ?? '');
            $landmark = !empty($data['landmark']) ? trim($data['landmark']) : null;
            $latitude = !empty($data['latitude']) ? (float)$data['latitude'] : null;
            $longitude = !empty($data['longitude']) ? (float)$data['longitude'] : null;

            // Progressive geographic fields (auto-resolve upward if not explicitly provided)
            $stateId = !empty($data['state_id']) ? (int)$data['state_id'] : null;
            $districtId = !empty($data['district_id']) ? (int)$data['district_id'] : null;
            $mandalId = !empty($data['mandal_id']) ? (int)$data['mandal_id'] : null;
            $gramPanchayatId = !empty($data['gram_panchayat_id']) ? (int)$data['gram_panchayat_id'] : null;

            if (!$stateId || !$districtId || !$mandalId || !$gramPanchayatId) {
                $geo = Location::resolveHierarchy($villageId);
                if ($geo) {
                    $stateId = $stateId ?: ($geo['state_id'] ? (int)$geo['state_id'] : null);
                    $districtId = $districtId ?: ($geo['district_id'] ? (int)$geo['district_id'] : null);
                    $mandalId = $mandalId ?: ($geo['mandal_id'] ? (int)$geo['mandal_id'] : null);
                    $gramPanchayatId = $gramPanchayatId ?: ($geo['gram_panchayat_id'] ? (int)$geo['gram_panchayat_id'] : null);
                }
            }

            // 4. Insert into grievances table
            $stmt = $db->prepare("
                INSERT INTO grievances (
                    tracking_id, citizen_name, citizen_phone, 
                    state_id, district_id, mandal_id, gram_panchayat_id, village_id, ward_id,
                    category_id, department_id, description, landmark, latitude,
                    longitude, priority, status, sla_due_at, created_at
                ) VALUES (
                    :tracking_id, :citizen_name, :citizen_phone, 
                    :state_id, :district_id, :mandal_id, :gram_panchayat_id, :village_id, :ward_id,
                    :category_id, :department_id, :description, :landmark, :latitude,
                    :longitude, :priority, 'submitted', :sla_due_at, NOW()
                )
            ");

            $stmt->execute([
                ':tracking_id'        => $trackingId,
                ':citizen_name'       => $citizenName,
                ':citizen_phone'      => $citizenPhone,
                ':state_id'           => $stateId,
                ':district_id'        => $districtId,
                ':mandal_id'          => $mandalId,
                ':gram_panchayat_id'  => $gramPanchayatId,
                ':village_id'         => $villageId,
                ':ward_id'            => $wardId,
                ':category_id'        => $categoryId,
                ':department_id'      => $departmentId,
                ':description'        => $description,
                ':landmark'           => $landmark,
                ':latitude'           => $latitude,
                ':longitude'          => $longitude,
                ':priority'           => $priority,
                ':sla_due_at'         => $slaDueAt
            ]);

            $grievanceId = (int)$db->lastInsertId();

            // 5. Create initial timeline event in grievance_updates
            $updateStmt = $db->prepare("
                INSERT INTO grievance_updates (
                    grievance_id, user_id, action, from_status, to_status, remarks, created_at
                ) VALUES (
                    :grievance_id, NULL, 'submit', NULL, 'submitted', 'Grievance submitted by citizen', NOW()
                )
            ");
            $updateStmt->execute([':grievance_id' => $grievanceId]);

            // 6. Handle optional photo attachment
            if ($file && isset($file['error']) && $file['error'] !== UPLOAD_ERR_NO_FILE) {
                $uploadResult = FileUploadService::uploadImage($file);

                $attachStmt = $db->prepare("
                    INSERT INTO attachments (
                        grievance_id, file_path, original_name, file_size, mime_type,
                        attachment_type, uploaded_by_user_id, created_at
                    ) VALUES (
                        :grievance_id, :file_path, :original_name, :file_size, :mime_type,
                        'citizen_photo', NULL, NOW()
                    )
                ");
                $attachStmt->execute([
                    ':grievance_id'   => $grievanceId,
                    ':file_path'      => $uploadResult['file_path'],
                    ':original_name'  => $uploadResult['original_name'],
                    ':file_size'      => $uploadResult['file_size'],
                    ':mime_type'      => $uploadResult['mime_type']
                ]);
            }

            // 7. Record in audit logs
            AuditLog::log('citizen_grievance_submitted', 'grievances', $grievanceId, null, [
                'tracking_id' => $trackingId,
                'category_id' => $categoryId,
                'village_id'  => $villageId
            ]);

            // 8. Commit transaction
            $db->commit();

            // 9. Dispatch Decoupled Webhook Event (Phase 8 Automation)
            try {
                $fullGrievance = Grievance::findById($grievanceId);
                if ($fullGrievance) {
                    WebhookService::notifyGrievanceSubmitted($fullGrievance);
                }
            } catch (Exception $webhookErr) {
                error_log("Webhook dispatch error on submission: " . $webhookErr->getMessage());
            }

            return [
                'id'           => $grievanceId,
                'tracking_id'  => $trackingId,
                'citizen_name' => $citizenName,
                'citizen_phone'=> $citizenPhone,
                'category'     => $category,
                'sla_due_at'   => $slaDueAt
            ];

        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }
}
