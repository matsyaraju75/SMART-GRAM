<?php
// ==============================================================================
// Smart Gram - Database Migration & Seeder Runner
// Can be executed via CLI: php database/migrate.php
// Or opened in a browser during setup.
// ==============================================================================

require_once __DIR__ . '/../config/app.php';
$dbConfig = require __DIR__ . '/../config/database.php';

$isCli = (php_sapi_name() === 'cli');

function output(string $msg, string $type = 'info'): void {
    global $isCli;
    if ($isCli) {
        $prefix = match($type) {
            'success' => " [SUCCESS] ",
            'error'   => " [ERROR]   ",
            'warning' => " [WARNING] ",
            default   => " [INFO]    "
        };
        echo $prefix . $msg . "\n";
    } else {
        $color = match($type) {
            'success' => '#155724; background:#d4edda;',
            'error'   => '#721c24; background:#f8d7da;',
            'warning' => '#856404; background:#fff3cd;',
            default   => '#0c5460; background:#d1ecf1;'
        };
        echo "<div style='margin:5px 0; padding:10px; border-radius:4px; font-family:monospace; color:$color'>$msg</div>";
    }
}

try {
    output("Connecting to MySQL server at {$dbConfig['host']}:{$dbConfig['port']}...");
    
    // Connect to MySQL server without selecting DB initially
    $dsnServer = sprintf(
        "%s:host=%s;port=%s;charset=%s",
        $dbConfig['driver'],
        $dbConfig['host'],
        $dbConfig['port'],
        $dbConfig['charset']
    );
    
    $pdo = new PDO($dsnServer, $dbConfig['username'], $dbConfig['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    output("Connected successfully!", 'success');

    // 1. Create Database if not exists
    $dbName = $dbConfig['database'];
    output("Checking / Creating database: `$dbName`...");
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$dbName`");
    output("Using database `$dbName`.", 'success');

    // 2. Execute Schema (DDL)
    $schemaFile = __DIR__ . '/schema.sql';
    if (!file_exists($schemaFile)) {
        throw new Exception("schema.sql not found at: $schemaFile");
    }
    output("Running schema migration from database/schema.sql...");
    $schemaSql = file_get_contents($schemaFile);
    $pdo->exec($schemaSql);
    output("All 10 core tables created or verified successfully!", 'success');

    // 3. Execute Seeders (DML)
    $seederFile = __DIR__ . '/seeders.sql';
    if (file_exists($seederFile)) {
        output("Seeding default data from database/seeders.sql...");
        $seederSql = file_get_contents($seederFile);
        $pdo->exec($seederSql);
        output("Sample master data and users seeded successfully!", 'success');
    }

    // 3.1 Execute Alluri Sitharama Raju LGD Master Seeder
    $asrLgdSeeder = __DIR__ . '/seed_asr_lgd.php';
    if (file_exists($asrLgdSeeder)) {
        output("Seeding comprehensive ASR District LGD hierarchy (Mandals, Panchayats, Villages)...");
        require $asrLgdSeeder;
        output("ASR District LGD hierarchy seeded successfully!", 'success');
    }

    // 4. Verify Users Table
    $stmt = $pdo->query("SELECT id, role, full_name, email, phone FROM users ORDER BY id ASC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    output("=== Active Internal Accounts for Testing ===", 'info');
    foreach ($users as $u) {
        $roleName = strtoupper($u['role']);
        $passHint = match($u['role']) {
            'admin' => 'Admin123!',
            'staff' => 'Staff123!',
            'officer' => 'Officer123!',
            default => 'Password'
        };
        output("Role: {$roleName} | Email: {$u['email']} | Password: {$passHint} | Name: {$u['full_name']}", 'info');
    }

    output("Migration & Seeding finished without errors!", 'success');

} catch (PDOException $e) {
    output("Database Error: " . $e->getMessage(), 'error');
    exit(1);
} catch (Exception $e) {
    output("System Error: " . $e->getMessage(), 'error');
    exit(1);
}
