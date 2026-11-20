<?php
// ==============================================================================
// Smart Gram - Audit Log Model
// ==============================================================================

namespace App\Models;

use App\Core\Database;
use PDO;

class AuditLog {
    /**
     * Record an audit event in the database
     */
    public static function log(
        string $action,
        string $entity,
        ?int $entityId = null,
        ?int $userId = null,
        ?array $metadata = null
    ): void {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("
                INSERT INTO audit_logs (user_id, action, entity, entity_id, ip_address, user_agent, metadata)
                VALUES (:user_id, :action, :entity, :entity_id, :ip_address, :user_agent, :metadata)
            ");

            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $userAgent = substr($_SERVER['HTTP_USER_AGENT'] ?? 'CLI', 0, 255);
            $metaJson = $metadata ? json_encode($metadata, JSON_UNESCAPED_UNICODE) : null;

            $stmt->execute([
                ':user_id'    => $userId,
                ':action'     => $action,
                ':entity'     => $entity,
                ':entity_id'  => $entityId,
                ':ip_address' => $ipAddress,
                ':user_agent' => $userAgent,
                ':metadata'   => $metaJson
            ]);
        } catch (\Exception $e) {
            // Audit logging should not crash the app if it fails, but should be logged to error_log
            error_log("Failed to write audit log: " . $e->getMessage());
        }
    }

    /**
     * Get recent audit logs for the Admin dashboard
     */
    public static function getRecent(int $limit = 20): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT a.*, u.full_name, u.email, u.role
            FROM audit_logs a
            LEFT JOIN users u ON a.user_id = u.id
            ORDER BY a.created_at DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
