<?php
// ==============================================================================
// Smart Gram - Phase 9 Database Migration: Security Rate Limiting
// ==============================================================================

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../app/core/Database.php';

use App\Core\Database;

try {
    $db = Database::getConnection();
    echo "Running Phase 9 Database Migration (Security & Rate Limiting)...\n";

    $sql = "
    CREATE TABLE IF NOT EXISTS rate_limits (
        id INT AUTO_INCREMENT PRIMARY KEY,
        rate_key VARCHAR(128) NOT NULL,
        hits INT NOT NULL DEFAULT 1,
        reset_at TIMESTAMP NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY uq_rate_key (rate_key),
        INDEX idx_reset (reset_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";

    $db->exec($sql);
    echo "Migration completed successfully: `rate_limits` table created or confirmed.\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
