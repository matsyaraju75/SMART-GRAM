<?php
// ==============================================================================
// Smart Gram - Database Backup & Optimization Utility (Phase 10)
// Exports all MySQL tables, schemas, and records to storage/backups/
// Can be executed via CLI: php database/backup.php
// ==============================================================================

declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/config/app.php';
require_once BASE_PATH . '/app/core/Database.php';

use App\Core\Database;

$isCli = (php_sapi_name() === 'cli');
$timestamp = date('Y-m-d_H-i-s');
$backupDir = BASE_PATH . '/storage/backups';

if (!is_dir($backupDir)) {
    mkdir($backupDir, 0755, true);
}

$backupFile = $backupDir . "/smartgram_backup_{$timestamp}.sql";

if ($isCli) {
    echo "============================================================\n";
    echo "   SMART GRAM - AUTOMATED DATABASE BACKUP & OPTIMIZATION   \n";
    echo "============================================================\n\n";
}

try {
    $db = Database::getConnection();
    
    // Fetch all database tables
    $tables = [];
    $stmt = $db->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'");
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        $tables[] = $row[0];
    }

    $out = "-- ============================================================\n";
    $out .= "-- Smart Gram Database Backup\n";
    $out .= "-- Generated at: " . date('Y-m-d H:i:s') . "\n";
    $out .= "-- Tables: " . count($tables) . "\n";
    $out .= "-- ============================================================\n\n";
    $out .= "SET FOREIGN_KEY_CHECKS=0;\n";
    $out .= "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n";
    $out .= "SET time_zone = '+00:00';\n\n";

    $totalRows = 0;

    foreach ($tables as $table) {
        if ($isCli) echo "  Backing up table: `{$table}`...";
        
        $out .= "-- ------------------------------------------------------------\n";
        $out .= "-- Structure for table `{$table}`\n";
        $out .= "-- ------------------------------------------------------------\n";
        $out .= "DROP TABLE IF EXISTS `{$table}`;\n";

        $createStmt = $db->query("SHOW CREATE TABLE `{$table}`")->fetch(PDO::FETCH_ASSOC);
        $out .= $createStmt['Create Table'] . ";\n\n";

        // Dump data
        $rows = $db->query("SELECT * FROM `{$table}`")->fetchAll(PDO::FETCH_ASSOC);
        $count = count($rows);
        $totalRows += $count;

        if ($count > 0) {
            $out .= "-- Dumping data for table `{$table}` ({$count} rows)\n";
            foreach ($rows as $r) {
                $cols = array_keys($r);
                $escapedCols = array_map(fn($c) => "`" . str_replace("`", "``", $c) . "`", $cols);
                
                $escapedValues = array_map(function($v) use ($db) {
                    if ($v === null) return 'NULL';
                    return $db->quote((string)$v);
                }, array_values($r));

                $out .= "INSERT INTO `{$table}` (" . implode(', ', $escapedCols) . ") VALUES (" . implode(', ', $escapedValues) . ");\n";
            }
            $out .= "\n";
        }

        if ($isCli) echo " [OK] ({$count} rows)\n";
    }

    $out .= "SET FOREIGN_KEY_CHECKS=1;\n";
    $out .= "-- End of Smart Gram Backup\n";

    file_put_contents($backupFile, $out);
    $fileSizeBytes = filesize($backupFile);
    $fileSizeKb = round($fileSizeBytes / 1024, 2);

    // Optimize tables
    if ($isCli) echo "\n  Optimizing database tables...\n";
    foreach ($tables as $table) {
        $db->query("OPTIMIZE TABLE `{$table}`");
    }

    if ($isCli) {
        echo "  [DONE] All tables optimized successfully.\n\n";
        echo "============================================================\n";
        echo "  Backup Created: {$backupFile}\n";
        echo "  File Size: {$fileSizeKb} KB\n";
        echo "  Total Tables: " . count($tables) . "\n";
        echo "  Total Records: {$totalRows}\n";
        echo "============================================================\n";
    } else {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'file' => basename($backupFile),
            'size_kb' => $fileSizeKb,
            'tables' => count($tables),
            'records' => $totalRows
        ]);
    }

} catch (\Throwable $e) {
    if ($isCli) {
        fwrite(STDERR, "\nERROR during database backup: " . $e->getMessage() . "\n");
        exit(1);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit;
    }
}
