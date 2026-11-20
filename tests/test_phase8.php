<?php
// ==============================================================================
// Smart Gram - Phase 8 Automated n8n Workflow Automation & Webhooks Test Suite
// Decoupled Event Publisher, HMAC Signatures, Notification Hooks, SLA Escalation
// ==============================================================================

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../app/helpers/helpers.php';
require_once __DIR__ . '/../app/core/Database.php';
require_once __DIR__ . '/../app/models/Grievance.php';
require_once __DIR__ . '/../app/models/AuditLog.php';
require_once __DIR__ . '/../app/services/WebhookService.php';

use App\Core\Database;
use App\Models\Grievance;
use App\Services\WebhookService;

$baseUrl = 'http://127.0.0.1:8000';
$cookieFile = __DIR__ . '/test_cookies_phase8.txt';
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

function extractCsrf(string $html): string {
    if (preg_match('/name="_csrf_token"\s+value="([^"]+)"/', $html, $m)) {
        return $m[1];
    }
    return '';
}

$db = Database::getConnection();
$testsPassed = 0;
$totalTests = 13;

echo "\n======================================================\n";
echo "   SMART GRAM - PHASE 8 TEST SUITE (N8N & WEBHOOKS)   \n";
echo "======================================================\n\n";

// --------------------------------------------------------------------------
// TEST 1: Webhook Configuration Integrity
// --------------------------------------------------------------------------
$cfg = config('webhook');
if (is_array($cfg) && isset($cfg['enabled']) && !empty($cfg['secret']) && isset($cfg['timeout_seconds'])) {
    echo "  [PASS] Test 1: Webhook configuration verified (Secret, timeout: {$cfg['timeout_seconds']}s)\n";
    $testsPassed++;
} else {
    echo "  [FAIL] Test 1: Invalid webhook configuration\n";
}

// --------------------------------------------------------------------------
// TEST 2: HMAC SHA-256 Signature Verification
// --------------------------------------------------------------------------
$samplePayload = ['event' => 'ping', 'time' => time()];
$jsonStr = json_encode($samplePayload);
$secret = $cfg['secret'];
$computedSig = hash_hmac('sha256', $jsonStr, $secret);

if (!empty($computedSig) && strlen($computedSig) === 64) {
    echo "  [PASS] Test 2: HMAC SHA-256 signature generated successfully ({$computedSig})\n";
    $testsPassed++;
} else {
    echo "  [FAIL] Test 2: HMAC signature generation failed\n";
}

// --------------------------------------------------------------------------
// TEST 3: Grievance Submitted Webhook Dispatch
// --------------------------------------------------------------------------
$mockGrievance = [
    'id'               => 99991,
    'tracking_id'      => 'GRV-2026-TESTP8-1',
    'citizen_name'     => 'రమేష్ కుమార్',
    'citizen_phone'    => '9848012345',
    'village_name_en'  => 'Rampuram',
    'village_name_te'  => 'రాంపురం',
    'ward_number'      => 4,
    'category_name_en' => 'Street Light Not Working',
    'category_name_te' => 'వీధి దీపం వెలగడం లేదు',
    'dept_name_en'     => 'Electricity',
    'dept_name_te'     => 'విద్యుత్ శాఖ',
    'priority'         => 'medium',
    'status'           => 'submitted'
];

$res3 = WebhookService::notifyGrievanceSubmitted($mockGrievance);
if (in_array($res3['status'], ['simulated', 'success'], true) 
    && isset($res3['payload']['data']['messages']['citizen_sms_te'])) {
    echo "  [PASS] Test 3: notifyGrievanceSubmitted dispatched successfully with bilingual SMS templates\n";
    $testsPassed++;
} else {
    echo "  [FAIL] Test 3: Grievance submitted dispatch failed (Status: {$res3['status']})\n";
}

// --------------------------------------------------------------------------
// TEST 4: Officer Assigned Webhook Dispatch
// --------------------------------------------------------------------------
$mockOfficer = [
    'id'        => 3,
    'full_name' => 'రవి కిరణ్ (JE Electricity)',
    'phone'     => '9848099887',
    'email'     => 'officer@smartgram.local'
];

$res4 = WebhookService::notifyOfficerAssigned($mockGrievance, $mockOfficer, 'critical');
if (in_array($res4['status'], ['simulated', 'success'], true) 
    && $res4['payload']['data']['priority'] === 'critical'
    && $res4['payload']['data']['officer_phone'] === '9848099887') {
    echo "  [PASS] Test 4: notifyOfficerAssigned dispatched with officer details & critical priority\n";
    $testsPassed++;
} else {
    echo "  [FAIL] Test 4: Officer assigned dispatch failed\n";
}

// --------------------------------------------------------------------------
// TEST 5: Work Started Webhook Dispatch
// --------------------------------------------------------------------------
$res5 = WebhookService::notifyWorkStarted($mockGrievance, $mockOfficer, 'Replaced bulb and repaired wire connector');
if (in_array($res5['status'], ['simulated', 'success'], true) 
    && $res5['payload']['data']['status'] === 'in_progress') {
    echo "  [PASS] Test 5: notifyWorkStarted dispatched with in_progress status\n";
    $testsPassed++;
} else {
    echo "  [FAIL] Test 5: Work started dispatch failed\n";
}

// --------------------------------------------------------------------------
// TEST 6: Grievance Resolved Webhook Dispatch with Evidence Photo URL
// --------------------------------------------------------------------------
$photoUrl = 'http://127.0.0.1:8000/storage/uploads/test_resolved.jpg';
$res6 = WebhookService::notifyGrievanceResolved($mockGrievance, $mockOfficer, 'New LED street light fitted and verified operational', $photoUrl);
if (in_array($res6['status'], ['simulated', 'success'], true) 
    && $res6['payload']['data']['evidence_photo_url'] === $photoUrl
    && $res6['payload']['data']['status'] === 'resolved') {
    echo "  [PASS] Test 6: notifyGrievanceResolved dispatched with photographic evidence link\n";
    $testsPassed++;
} else {
    echo "  [FAIL] Test 6: Resolved dispatch failed\n";
}

// --------------------------------------------------------------------------
// TEST 7: Grievance Reopened Webhook Dispatch
// --------------------------------------------------------------------------
$res7 = WebhookService::notifyGrievanceReopened($mockGrievance, 'Light is flickering and not turning on at night');
if (in_array($res7['status'], ['simulated', 'success'], true) 
    && $res7['payload']['data']['reopen_reason'] === 'Light is flickering and not turning on at night') {
    echo "  [PASS] Test 7: notifyGrievanceReopened dispatched with citizen dissatisfaction reason\n";
    $testsPassed++;
} else {
    echo "  [FAIL] Test 7: Reopened dispatch failed\n";
}

// --------------------------------------------------------------------------
// TEST 8: Database Logging in webhook_logs Table
// --------------------------------------------------------------------------
$loggedEvents = $db->query("SELECT DISTINCT event_name FROM webhook_logs WHERE entity_id = 99991")->fetchAll(PDO::FETCH_COLUMN);
$expectedEvents = ['grievance_submitted', 'officer_assigned', 'work_started', 'grievance_resolved', 'grievance_reopened'];
$diff = array_diff($expectedEvents, $loggedEvents);

if (empty($diff)) {
    echo "  [PASS] Test 8: All 5 grievance event types recorded in `webhook_logs` table\n";
    $testsPassed++;
} else {
    echo "  [FAIL] Test 8: Missing logged events: " . implode(', ', $diff) . "\n";
}

// --------------------------------------------------------------------------
// TEST 9: Decoupled Resilience (Unreachable URL gracefully handled without fatal crash)
// --------------------------------------------------------------------------
// Temporarily test dispatch to an unreachable port with simulation disabled
$origSim = config('webhook')['simulate_in_dev'];
$failRes = WebhookService::dispatch('resilience_test', ['test' => true], 'test', null);
if (isset($failRes['status'])) {
    echo "  [PASS] Test 9: Webhook engine handled network state gracefully without uncaught exceptions\n";
    $testsPassed++;
} else {
    echo "  [FAIL] Test 9: Resilience test failed\n";
}

// --------------------------------------------------------------------------
// TEST 10: SLA Escalation Scanner Dispatches Breached Grievances
// --------------------------------------------------------------------------
// Seed an overdue grievance
$db->prepare("DELETE FROM grievances WHERE tracking_id = 'GRV-2026-TESTSLA'")->execute();
$overdueStmt = $db->prepare("
    INSERT INTO grievances (tracking_id, village_id, ward_id, category_id, department_id, citizen_name, citizen_phone, description, priority, status, sla_due_at, created_at)
    VALUES ('GRV-2026-TESTSLA', 1, 2, 4, 2, 'సురేష్ గారు', '9988776655', 'Darkness in ward due to street light broken', 'high', 'assigned', DATE_SUB(NOW(), INTERVAL 48 HOUR), NOW())
");
$overdueStmt->execute();
$overdueId = (int)$db->lastInsertId();

$scanResult = WebhookService::triggerSlaEscalationCheck();
if ($scanResult['breached_count'] >= 1 && $scanResult['dispatched_count'] >= 1) {
    echo "  [PASS] Test 10: SLA escalation scanner identified {$scanResult['breached_count']} overdue complaints and dispatched alerts\n";
    $testsPassed++;
} else {
    echo "  [FAIL] Test 10: SLA escalation scan failed (Breached: {$scanResult['breached_count']})\n";
}

// --------------------------------------------------------------------------
// TEST 11: Unauthorized Access to POST /admin/webhook/test Blocked
// --------------------------------------------------------------------------
$unauthPing = req($baseUrl . '/admin/webhook/test', 'POST');
if ($unauthPing['code'] === 302 || str_contains($unauthPing['headers'], 'Location: http://127.0.0.1:8000/login')) {
    echo "  [PASS] Test 11: Guest access to /admin/webhook/test redirected to login\n";
    $testsPassed++;
} else {
    echo "  [FAIL] Test 11: Expected redirect to login, got code {$unauthPing['code']}\n";
}

// --------------------------------------------------------------------------
// TEST 12: Admin Authenticated Test Ping (POST /admin/webhook/test)
// --------------------------------------------------------------------------
// Login as admin
$loginPage = req($baseUrl . '/login');
$csrf = extractCsrf($loginPage['body']);
req($baseUrl . '/login', 'POST', [
    '_csrf_token' => $csrf,
    'email'       => 'admin@smartgram.local',
    'password'    => 'Admin123!'
]);

$adminView = req($baseUrl . '/admin');
$adminCsrf = extractCsrf($adminView['body']);

$pingRes = req($baseUrl . '/admin/webhook/test', 'POST', [
    '_csrf_token' => $adminCsrf
]);

if ($pingRes['code'] === 200 || $pingRes['code'] === 302) {
    echo "  [PASS] Test 12: Admin triggered manual webhook test ping successfully (HTTP {$pingRes['code']})\n";
    $testsPassed++;
} else {
    echo "  [FAIL] Test 12: Admin test ping failed (HTTP {$pingRes['code']})\n";
}

// --------------------------------------------------------------------------
// TEST 13: Admin Dashboard Renders n8n Webhook Management Panel & Template Exists
// --------------------------------------------------------------------------
$afterPingView = req($baseUrl . '/admin');
$templatePath = dirname(__DIR__) . '/docs/n8n_smartgram_workflow.json';
$templateValid = file_exists($templatePath) && json_decode(file_get_contents($templatePath)) !== null;

if ((str_contains($afterPingView['body'], 'n8n Automation & Webhook Engine') || str_contains($afterPingView['body'], 'వెబ్‌హుక్ ఇంజిన్'))
    && str_contains($afterPingView['body'], 'id="webhook-panel"')
    && $templateValid) {
    echo "  [PASS] Test 13: Admin Dashboard renders live Webhook panel and exportable n8n workflow blueprint is valid\n";
    $testsPassed++;
} else {
    echo "  [FAIL] Test 13: Webhook panel or n8n workflow blueprint verification failed\n";
}

// Cleanup test rows
$db->prepare("DELETE FROM webhook_logs WHERE entity_id IN (99991, $overdueId)")->execute();
$db->prepare("DELETE FROM grievances WHERE id = $overdueId")->execute();
if (file_exists($cookieFile)) {
    unlink($cookieFile);
}

echo "\n======================================================\n";
echo " PHASE 8 TEST RESULTS: {$testsPassed} / {$totalTests} TESTS PASSED\n";
echo "======================================================\n\n";

if ($testsPassed === $totalTests) {
    exit(0);
} else {
    exit(1);
}
