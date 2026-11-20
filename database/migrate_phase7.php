<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../app/core/Database.php';

try {
    $pdo = App\Core\Database::getConnection();

    // Check existing columns
    $stmt = $pdo->query("SHOW COLUMNS FROM grievances LIKE 'ai_summary'");
    if (!$stmt->fetch()) {
        $pdo->exec("
            ALTER TABLE grievances 
            ADD COLUMN ai_summary TEXT DEFAULT NULL AFTER description,
            ADD COLUMN ai_suggested_priority VARCHAR(20) DEFAULT NULL AFTER priority,
            ADD COLUMN ai_confidence DECIMAL(3, 2) DEFAULT NULL,
            ADD COLUMN ai_duplicate_of_id INT DEFAULT NULL;
        ");
        echo "Phase 7 AI columns added to grievances table.\n";
    } else {
        echo "Phase 7 AI columns already exist.\n";
    }
} catch (Exception $e) {
    echo "Migration Note: " . $e->getMessage() . "\n";
}
