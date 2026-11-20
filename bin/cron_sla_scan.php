<?php
// ==============================================================================
// Smart Gram - Automated CLI SLA Breach & Escalation Scanner (Phase 10)
// Designed for execution via cron job / Windows Task Scheduler:
// e.g. Linux Crontab: */15 * * * * php /path/to/smart-gram/bin/cron_sla_scan.php >> /path/to/smart-gram/storage/logs/cron_sla.log 2>&1
// ==============================================================================

declare(strict_types=1);

// Enforce CLI environment
if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    echo "Error: This task runner can only be executed via the command line (CLI).\n";
    exit(1);
}

define('BASE_PATH', dirname(__DIR__));

// Autoloader for App\ namespace
spl_autoload_register(function (string $class) {
    $prefix = 'App\\';
    $baseDir = BASE_PATH . '/app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $file = $baseDir . str_replace('\\', '/', substr($class, $len)) . '.php';
    if (file_exists($file)) require_once $file;
});

require_once BASE_PATH . '/config/app.php';
require_once BASE_PATH . '/app/helpers/helpers.php';

use App\Services\WebhookService;

$startTime = microtime(true);
$timestamp = date('Y-m-d H:i:s');

echo "------------------------------------------------------------\n";
echo "[{$timestamp}] Starting Smart Gram Automated SLA Breach Scanner\n";
echo "------------------------------------------------------------\n";

try {
    $results = WebhookService::triggerSlaEscalationCheck();
    $duration = round((microtime(true) - $startTime) * 1000, 2);

    $logMessage = sprintf(
        "[%s] Completed in %0.2f ms | Breached: %d | Webhook Alerts Dispatched: %d\n",
        $timestamp,
        $duration,
        $results['breached_count'],
        $results['dispatched_count']
    );

    echo $logMessage;

    // Persist to cron log
    $logDir = BASE_PATH . '/storage/logs';
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    file_put_contents($logDir . '/cron_sla.log', $logMessage, FILE_APPEND);

    echo "[{$timestamp}] Status: OK\n";
    exit(0);

} catch (\Throwable $e) {
    $errMsg = sprintf("[%s] CRON ERROR: %s in %s:%d\n", $timestamp, $e->getMessage(), $e->getFile(), $e->getLine());
    fwrite(STDERR, $errMsg);
    
    $logDir = BASE_PATH . '/storage/logs';
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    file_put_contents($logDir . '/cron_sla.log', $errMsg, FILE_APPEND);
    exit(1);
}
