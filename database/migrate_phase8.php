<?php
// ==============================================================================
// Smart Gram - Phase 8 Database Migration: Webhook Logs & Automation
// ==============================================================================

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../app/core/Database.php';

use App\Core\Database;

try {
    $db = Database::getConnection();
    echo "Running Phase 8 Database Migration...\n";

    $sql = "
    CREATE TABLE IF NOT EXISTS webhook_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        event_name VARCHAR(64) NOT NULL,
        entity_type VARCHAR(64) NOT NULL DEFAULT 'grievances',
        entity_id INT NULL,
        webhook_url VARCHAR(500) NOT NULL,
        payload JSON NOT NULL,
        http_status INT NULL,
        response_body TEXT NULL,
        status ENUM('success', 'failed', 'skipped', 'simulated') NOT NULL DEFAULT 'skipped',
        execution_time_ms DECIMAL(8,2) NULL,
        error_message VARCHAR(500) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_event (event_name),
        INDEX idx_entity (entity_type, entity_id),
        INDEX idx_status (status),
        INDEX idx_created (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";

    $db->exec($sql);
    echo "Migration completed successfully: `webhook_logs` table created or confirmed.\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
