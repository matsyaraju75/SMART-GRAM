<?php
// ==============================================================================
// Smart Gram - Phase 10 Production Deployment & Documentation Test Suite
// Verifies: Production Health Check, CLI Task Runners, Automated DB Backups,
// Production Configuration Template, Documentation Suite, and Zero-Login Integrity.
// ==============================================================================

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../app/helpers/helpers.php';
require_once __DIR__ . '/../app/core/Database.php';

use App\Core\Database;

$baseUrl = 'http://127.0.0.1:8000';
$cookieFile = __DIR__ . '/test_cookies_phase10.txt';
if (file_exists($cookieFile)) {
    unlink($cookieFile);
}

function req(string $url, string $method = 'GET', array $data = []): array {
    global $cookieFile;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);

    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $headerStr = substr($response, 0, $headerSize);
    $body = substr($response, $headerSize);
    curl_close($ch);

    return [
        'code' => $httpCode,
        'headers' => $headerStr,
        'body' => $body
    ];
}

$testsPassed = 0;
$totalTests = 13;

echo "\n====================================================================\n";
echo "   SMART GRAM - PHASE 10 PRODUCTION READINESS TEST SUITE            \n";
echo "====================================================================\n\n";

// --------------------------------------------------------------------------
// TEST 1: Production Health Check Endpoint HTTP 200 & JSON Response
// --------------------------------------------------------------------------
$healthResp = req("{$baseUrl}/health");
$healthData = json_decode($healthResp['body'], true);

if ($healthResp['code'] === 200 && is_array($healthData) && ($healthData['status'] ?? '') === 'healthy') {
    echo "  [PASS] Test 1: Production Health Check endpoint returns HTTP 200 OK & status 'healthy'\n";
    $testsPassed++;
} else {
    echo "  [FAIL] Test 1: Health check failed or invalid JSON (HTTP Code: {$healthResp['code']})\n";
}

// --------------------------------------------------------------------------
// TEST 2: Health Check Database Latency & Status Probe
// --------------------------------------------------------------------------
$dbCheck = $healthData['checks']['database'] ?? [];
if (($dbCheck['status'] ?? '') === 'connected' && isset($dbCheck['latency_ms']) && is_numeric($dbCheck['latency_ms'])) {
    echo "  [PASS] Test 2: Database probe reports connected (Latency: {$dbCheck['latency_ms']} ms)\n";
    $testsPassed++;
} else {
    echo "  [FAIL] Test 2: Database health probe report invalid\n";
}

// --------------------------------------------------------------------------
// TEST 3: Health Check Storage Writability Probe
// --------------------------------------------------------------------------
$storageCheck = $healthData['checks']['storage'] ?? [];
if (($storageCheck['uploads_writable'] ?? false) === true) {
    echo "  [PASS] Test 3: Storage probe confirms 'storage/uploads' is fully writable\n";
    $testsPassed++;
} else {
    echo "  [FAIL] Test 3: Storage health probe report invalid\n";
}

// --------------------------------------------------------------------------
// TEST 4: Health Check PHP Extensions Audit
// --------------------------------------------------------------------------
$extCheck = $healthData['checks']['extensions'] ?? [];
if (($extCheck['loaded'] ?? false) === true && empty($extCheck['missing'])) {
    echo "  [PASS] Test 4: Extensions probe confirms all critical extensions loaded (pdo, mbstring, fileinfo, curl)\n";
    $testsPassed++;
} else {
    echo "  [FAIL] Test 4: Missing required PHP extensions\n";
}

// --------------------------------------------------------------------------
// TEST 5: Automated CLI SLA Breach Scanner Execution
// --------------------------------------------------------------------------
$phpBin = PHP_BINARY;
$cronScript = dirname(__DIR__) . '/bin/cron_sla_scan.php';
$cronOutput = [];
$cronExit = 0;
exec("\"{$phpBin}\" \"{$cronScript}\"", $cronOutput, $cronExit);
$cronOutputStr = implode("\n", $cronOutput);

$logFile = dirname(__DIR__) . '/storage/logs/cron_sla.log';
$logExists = file_exists($logFile) && filesize($logFile) > 0;

if ($cronExit === 0 && str_contains($cronOutputStr, 'Status: OK') && $logExists) {
    echo "  [PASS] Test 5: Automated CLI SLA Scanner executed successfully with exit code 0 and log recorded\n";
    $testsPassed++;
} else {
    echo "  [FAIL] Test 5: CLI SLA Scanner failed (Exit Code: {$cronExit})\n";
}

// --------------------------------------------------------------------------
// TEST 6: Automated Database Backup & Index Optimization Tool
// --------------------------------------------------------------------------
$backupScript = dirname(__DIR__) . '/database/backup.php';
$backupOutput = [];
$backupExit = 0;
exec("\"{$phpBin}\" \"{$backupScript}\"", $backupOutput, $backupExit);
$backupOutputStr = implode("\n", $backupOutput);

$backupDir = dirname(__DIR__) . '/storage/backups';
$sqlFiles = glob($backupDir . '/*.sql');

$hasSqlFile = !empty($sqlFiles);
$latestSql = $hasSqlFile ? end($sqlFiles) : '';
$validSql = $hasSqlFile && (filesize($latestSql) > 1000) && str_contains(file_get_contents($latestSql), 'CREATE TABLE');

if ($backupExit === 0 && $validSql && str_contains($backupOutputStr, 'Optimizing database tables')) {
    echo "  [PASS] Test 6: Database Backup & Optimization utility created valid SQL dump (" . round(filesize($latestSql)/1024, 1) . " KB) and optimized tables\n";
    $testsPassed++;
} else {
    echo "  [FAIL] Test 6: Database backup utility failed (Exit Code: {$backupExit})\n";
}

// --------------------------------------------------------------------------
// TEST 7: Production Configuration Template (.env.production)
// --------------------------------------------------------------------------
$prodEnvFile = dirname(__DIR__) . '/.env.production';
if (file_exists($prodEnvFile)) {
    $prodContent = file_get_contents($prodEnvFile);
    $hasProdEnv = str_contains($prodContent, 'APP_ENV="production"') || str_contains($prodContent, 'APP_ENV=production');
    $hasDebugOff = str_contains($prodContent, 'APP_DEBUG=false');
    $hasTeluguDefault = str_contains($prodContent, 'APP_DEFAULT_LANG="te"') || str_contains($prodContent, 'APP_DEFAULT_LANG=te');

    if ($hasProdEnv && $hasDebugOff && $hasTeluguDefault) {
        echo "  [PASS] Test 7: Production environment template (.env.production) verified (APP_ENV=production, APP_DEBUG=false, Telugu default)\n";
        $testsPassed++;
    } else {
        echo "  [FAIL] Test 7: .env.production missing required production settings\n";
    }
} else {
    echo "  [FAIL] Test 7: .env.production file not found\n";
}

// --------------------------------------------------------------------------
// TEST 8: Comprehensive Production Deployment Guide (docs/DEPLOYMENT.md)
// --------------------------------------------------------------------------
$deployDoc = dirname(__DIR__) . '/docs/DEPLOYMENT.md';
if (file_exists($deployDoc)) {
    $docContent = file_get_contents($deployDoc);
    $hasApache = str_contains($docContent, 'VirtualHost') && str_contains($docContent, 'DocumentRoot');
    $hasNginx = str_contains($docContent, 'fastcgi_pass');
    $hasDocker = str_contains($docContent, 'Dockerfile') && str_contains($docContent, 'docker-compose');
    $hasCron = str_contains($docContent, 'cron_sla_scan.php');
    $hasSsl = str_contains($docContent, 'certbot');

    if ($hasApache && $hasNginx && $hasDocker && $hasCron && $hasSsl) {
        echo "  [PASS] Test 8: Production Deployment Guide verified (Apache, Nginx, Docker, Cron, and SSL covered)\n";
        $testsPassed++;
    } else {
        echo "  [FAIL] Test 8: docs/DEPLOYMENT.md missing critical sections\n";
    }
} else {
    echo "  [FAIL] Test 8: docs/DEPLOYMENT.md not found\n";
}

// --------------------------------------------------------------------------
// TEST 9: Comprehensive Bilingual User Manual (docs/USER_MANUAL.md)
// --------------------------------------------------------------------------
$manualDoc = dirname(__DIR__) . '/docs/USER_MANUAL.md';
if (file_exists($manualDoc)) {
    $manualContent = file_get_contents($manualDoc);
    $hasCitizenGuide = str_contains($manualContent, 'పౌరుల') && str_contains($manualContent, 'Citizen Persona');
    $hasStaffGuide = str_contains($manualContent, 'సిబ్బంది') && str_contains($manualContent, 'Staff Persona');
    $hasOfficerGuide = str_contains($manualContent, 'అధికారుల') && str_contains($manualContent, 'Field Officer');
    $hasAdminGuide = str_contains($manualContent, 'నిర్వాహకుల') && str_contains($manualContent, 'Admin Guide');

    if ($hasCitizenGuide && $hasStaffGuide && $hasOfficerGuide && $hasAdminGuide) {
        echo "  [PASS] Test 9: Bilingual User Manual verified (Citizen, Staff, Officer, and Admin personas fully documented)\n";
        $testsPassed++;
    } else {
        echo "  [FAIL] Test 9: docs/USER_MANUAL.md missing persona sections\n";
    }
} else {
    echo "  [FAIL] Test 9: docs/USER_MANUAL.md not found\n";
}

// --------------------------------------------------------------------------
// TEST 10: Complete API Reference Specifications (docs/API.md)
// --------------------------------------------------------------------------
$apiDoc = dirname(__DIR__) . '/docs/API.md';
if (file_exists($apiDoc)) {
    $apiContent = file_get_contents($apiDoc);
    $hasHealthApi = str_contains($apiContent, 'GET /health');
    $hasWardsApi = str_contains($apiContent, 'GET /api/wards');
    $hasHmacSpec = str_contains($apiContent, 'X-SmartGram-Signature');
    $hasEvents = str_contains($apiContent, 'grievance.submitted') && str_contains($apiContent, 'sla_breach_escalation');

    if ($hasHealthApi && $hasWardsApi && $hasHmacSpec && $hasEvents) {
        echo "  [PASS] Test 10: API Reference verified (Health probe, Wards API, HMAC signatures, and all event payloads documented)\n";
        $testsPassed++;
    } else {
        echo "  [FAIL] Test 10: docs/API.md missing critical API specs\n";
    }
} else {
    echo "  [FAIL] Test 10: docs/API.md not found\n";
}

// --------------------------------------------------------------------------
// TEST 11: Exportable n8n Workflow Blueprint Validation
// --------------------------------------------------------------------------
$n8nFile = dirname(__DIR__) . '/docs/n8n_smartgram_workflow.json';
if (file_exists($n8nFile)) {
    $n8nJson = json_decode(file_get_contents($n8nFile), true);
    $isValidN8n = is_array($n8nJson) && isset($n8nJson['nodes']) && isset($n8nJson['connections']);

    if ($isValidN8n && count($n8nJson['nodes']) >= 4) {
        echo "  [PASS] Test 11: Exportable n8n workflow blueprint is valid JSON and ready for instant visual import\n";
        $testsPassed++;
    } else {
        echo "  [FAIL] Test 11: n8n workflow blueprint is invalid or corrupt\n";
    }
} else {
    echo "  [FAIL] Test 11: docs/n8n_smartgram_workflow.json not found\n";
}

// --------------------------------------------------------------------------
// TEST 12: Zero Citizen Login Architecture Preserved
// --------------------------------------------------------------------------
$homeResp = req("{$baseUrl}/");
$submitResp = req("{$baseUrl}/submit");
$trackResp = req("{$baseUrl}/track");
$aboutResp = req("{$baseUrl}/about");

$allPublic200 = ($homeResp['code'] === 200) &&
                 ($submitResp['code'] === 200) &&
                 ($trackResp['code'] === 200) &&
                 ($aboutResp['code'] === 200);

if ($allPublic200) {
    echo "  [PASS] Test 12: Zero Citizen Login constraint confirmed 100% intact across all public entry points\n";
    $testsPassed++;
} else {
    echo "  [FAIL] Test 12: Public citizen routes encountered unexpected HTTP codes\n";
}

// --------------------------------------------------------------------------
// TEST 13: Internal Administrative Security & RBAC Isolation
// --------------------------------------------------------------------------
$guestAdmin = req("{$baseUrl}/admin");
$guestStaff = req("{$baseUrl}/staff");
$guestOfficer = req("{$baseUrl}/officer");

$allRedirectToLogin = (str_contains($guestAdmin['headers'], '/login') || str_contains($guestAdmin['body'], 'login')) &&
                      (str_contains($guestStaff['headers'], '/login') || str_contains($guestStaff['body'], 'login')) &&
                      (str_contains($guestOfficer['headers'], '/login') || str_contains($guestOfficer['body'], 'login'));

if ($allRedirectToLogin) {
    echo "  [PASS] Test 13: Internal Administrative Portals (Admin, Staff, Officer) securely guarded by RBAC & Session middleware\n";
    $testsPassed++;
} else {
    echo "  [FAIL] Test 13: Internal administrative portals allowed unauthorized guest access\n";
}

// Cleanup cookie file
if (file_exists($cookieFile)) unlink($cookieFile);

// --------------------------------------------------------------------------
// SUMMARY RESULTS
// --------------------------------------------------------------------------
echo "\n====================================================================\n";
echo "   PHASE 10 TEST RESULTS: {$testsPassed} / {$totalTests} TESTS PASSED\n";
echo "====================================================================\n\n";

if ($testsPassed === $totalTests) {
    echo ">>> ALL PHASE 10 PRODUCTION READINESS & DEPLOYMENT TESTS PASSED! <<<\n\n";
    exit(0);
} else {
    echo ">>> SOME TESTS FAILED. PLEASE REVIEW LOGS. <<<\n\n";
    exit(1);
}
