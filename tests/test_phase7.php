<?php
// ==============================================================================
// Smart Gram - Phase 7 Automated AI Advisory Test Suite
// Human-in-the-Loop, Heuristic NLP, Urgency Detection, Duplicate Alerts, Staff Approval
// ==============================================================================

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../app/core/Database.php';
require_once __DIR__ . '/../app/models/Grievance.php';
require_once __DIR__ . '/../app/models/AuditLog.php';
require_once __DIR__ . '/../app/services/AiAdvisoryService.php';

use App\Core\Database;
use App\Models\Grievance;
use App\Services\AiAdvisoryService;

$baseUrl = 'http://127.0.0.1:8000';
$cookieFile = __DIR__ . '/test_cookies_phase7.txt';
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
    if (preg_match('/name="_?csrf_token"\s+value="([^"]+)"/', $html, $m)) {
        return $m[1];
    }
    return '';
}

$db = Database::getConnection();
$testsPassed = 0;
$totalTests = 13;

echo "\n======================================================\n";
echo "   SMART GRAM - PHASE 7 AUTOMATED TEST SUITE (AI)     \n";
echo "======================================================\n\n";

// --------------------------------------------------------------------------
// TEST 1: Heuristic NLP - Telugu Hazard Categorization (Hanging Wire -> Cat 5, Dept 2, Critical)
// --------------------------------------------------------------------------
$teluguWireDesc = "మా వీధిలో విద్యుత్ తీగలు తెగి కిందకు వేలాడుతున్నాయి, పిల్లలు నడుస్తుంటే కరెంట్ షాక్ తగిలే ప్రమాదం ఉంది వెంటనే చూడండి.";
$analysis1 = AiAdvisoryService::analyzeWithHeuristicNlp($teluguWireDesc);

if ($analysis1['suggested_category_id'] === 5 
    && $analysis1['suggested_department_id'] === 2 
    && $analysis1['suggested_priority'] === 'critical'
    && $analysis1['confidence'] >= 0.70) {
    echo "  [PASS] Test 1: Heuristic NLP classified Telugu electrical hazard (Cat 5, Dept 2, Critical, Conf: {$analysis1['confidence']})\n";
    $testsPassed++;
} else {
    echo "  [FAIL] Test 1: Category: {$analysis1['suggested_category_id']}, Priority: {$analysis1['suggested_priority']}\n";
}

// --------------------------------------------------------------------------
// TEST 2: Heuristic NLP - Telugu Contaminated Water (Cat 3, Dept 1, Critical)
// --------------------------------------------------------------------------
$waterDesc = "కుళాయిల్లో కలుషిత నీరు మురుగు వాసన వస్తోంది, తాగిన వారికి రోగం వస్తుంది చాలా ప్రమాదం.";
$analysis2 = AiAdvisoryService::analyzeWithHeuristicNlp($waterDesc);

if ($analysis2['suggested_category_id'] === 3 
    && $analysis2['suggested_department_id'] === 1 
    && $analysis2['suggested_priority'] === 'critical') {
    echo "  [PASS] Test 2: Heuristic NLP detected contaminated water health hazard as critical\n";
    $testsPassed++;
} else {
    echo "  [FAIL] Test 2: Category: {$analysis2['suggested_category_id']}, Priority: {$analysis2['suggested_priority']}\n";
}

// --------------------------------------------------------------------------
// TEST 3: Summary Synthesis in Telugu & English
// --------------------------------------------------------------------------
if (!empty($analysis1['summary_te']) && !empty($analysis1['summary_en']) && mb_strlen($analysis1['summary_en']) <= 120) {
    echo "  [PASS] Test 3: Executive 1-line summary generated in both Telugu and English\n";
    $testsPassed++;
} else {
    echo "  [FAIL] Test 3: Summary synthesis failed\n";
}

// --------------------------------------------------------------------------
// TEST 4: Heuristic NLP - English Complaint Categorization (Pipeline Leakage -> Cat 1)
// --------------------------------------------------------------------------
$englishLeakDesc = "Severe drinking water pipeline leak and burst near the main junction. Fresh water is gushing onto the road.";
$analysis4 = AiAdvisoryService::analyzeWithHeuristicNlp($englishLeakDesc);

if ($analysis4['suggested_category_id'] === 1 && $analysis4['suggested_department_id'] === 1) {
    echo "  [PASS] Test 4: Heuristic NLP classified English complaint as Pipeline Leakage (Cat 1)\n";
    $testsPassed++;
} else {
    echo "  [FAIL] Test 4: Classification failed for English text (Cat {$analysis4['suggested_category_id']})\n";
}

// --------------------------------------------------------------------------
// TEST 5 & 6: Duplicate Detection in Same Ward vs Cross Ward
// --------------------------------------------------------------------------
// Seed Complaint A in Ward 1
$trackingA = 'GRV-2026-TESTDUP1';
$db->prepare("DELETE FROM grievances WHERE tracking_id IN ('$trackingA', 'GRV-2026-TESTDUP2')")->execute();

$seedStmt = $db->prepare("
    INSERT INTO grievances (tracking_id, village_id, ward_id, citizen_name, citizen_phone, category_id, department_id, description, status, priority, created_at, updated_at)
    VALUES (:tracking, 1, 1, 'వెంకటేశ్వర్లు', '9876543210', 2, 1, 'మా వార్డులో కుళాయిల్లో నీరు రాకపోవడం వల్ల మూడు రోజులుగా తాగునీరు లేదు', 'submitted', 'high', NOW(), NOW())
");
$seedStmt->execute([':tracking' => $trackingA]);
$complaintAId = (int)$db->lastInsertId();

// Detect duplicates for a second complaint in the same Ward 1
$newComplaintSameWard = "కుళాయిల్లో నీరు రావడం లేదు ప్రజలకు తాగునీరు లేదు";
$duplicates = AiAdvisoryService::detectDuplicates($newComplaintSameWard, 1, null, 2);

if (!empty($duplicates) && $duplicates[0]['id'] === $complaintAId && $duplicates[0]['similarity_pct'] >= 40) {
    echo "  [PASS] Test 5: Duplicate detected in same ward (Match: {$duplicates[0]['similarity_pct']}%, ID: {$duplicates[0]['tracking_id']})\n";
    $testsPassed++;
} else {
    echo "  [FAIL] Test 5: Duplicate detection in same ward failed. Count: " . count($duplicates) . "\n";
}

// Ensure cross-ward query (Ward 2) does not report Complaint A as a duplicate
$diffWardDuplicates = AiAdvisoryService::detectDuplicates($newComplaintSameWard, 2, null, 2);
$foundCrossWard = false;
foreach ($diffWardDuplicates as $d) {
    if ($d['id'] === $complaintAId) {
        $foundCrossWard = true;
    }
}
if (!$foundCrossWard) {
    echo "  [PASS] Test 6: Cross-ward isolation verified (No false duplicates reported across ward boundaries)\n";
    $testsPassed++;
} else {
    echo "  [FAIL] Test 6: Complaint reported across ward boundaries\n";
}

// --------------------------------------------------------------------------
// TEST 7: Direct Service Action applySuggestions() Updates Database Record
// --------------------------------------------------------------------------
$staffUser = $db->query("SELECT id FROM users WHERE role = 'staff' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$staffId = (int)$staffUser['id'];

AiAdvisoryService::applySuggestions($complaintAId, $staffId, [
    'category_id'     => 3,
    'priority'        => 'critical',
    'summary'         => 'కలుషిత నీటి సమస్య — తక్షణ తాగునీటి సరఫరా తనిఖీ అవసరం',
    'confidence'      => 0.95,
    'duplicate_of_id' => null
]);

$reloadedA = $db->query("SELECT * FROM grievances WHERE id = $complaintAId")->fetch(PDO::FETCH_ASSOC);
if ((int)$reloadedA['category_id'] === 3 
    && $reloadedA['priority'] === 'critical'
    && $reloadedA['ai_suggested_priority'] === 'critical'
    && !empty($reloadedA['ai_summary'])
    && (float)$reloadedA['ai_confidence'] == 0.95) {
    echo "  [PASS] Test 7: applySuggestions() successfully updated database columns (Category 3, Critical, Summary, Confidence)\n";
    $testsPassed++;
} else {
    echo "  [FAIL] Test 7: Database update check failed. Category: {$reloadedA['category_id']}, Priority: {$reloadedA['priority']}\n";
}

// --------------------------------------------------------------------------
// TEST 8: applySuggestions() Logged Timeline 'ai_accepted' and Audit Log
// --------------------------------------------------------------------------
$timelineUpdate = $db->query("SELECT * FROM grievance_updates WHERE grievance_id = $complaintAId AND action = 'ai_accepted' ORDER BY id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$auditRecord = $db->query("SELECT * FROM audit_logs WHERE entity = 'grievances' AND entity_id = $complaintAId AND action = 'ai_accepted' ORDER BY id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);

if ($timelineUpdate && $auditRecord && (int)$timelineUpdate['user_id'] === $staffId) {
    echo "  [PASS] Test 8: 'ai_accepted' timeline entry and audit log entry created by staff user\n";
    $testsPassed++;
} else {
    echo "  [FAIL] Test 8: Timeline or audit log missing for ai_accepted\n";
}

// --------------------------------------------------------------------------
// TEST 9: Unauthenticated User Blocked from POST /grievance/ai-apply
// --------------------------------------------------------------------------
$unauthRes = req($baseUrl . '/grievance/ai-apply', 'POST', ['id' => $complaintAId]);
if ($unauthRes['code'] === 302 || str_contains($unauthRes['headers'], 'Location: http://127.0.0.1:8000/login')) {
    echo "  [PASS] Test 9: Unauthenticated user successfully redirected from /grievance/ai-apply\n";
    $testsPassed++;
} else {
    echo "  [FAIL] Test 9: Expected redirect to login, got HTTP {$unauthRes['code']}\n";
}

// --------------------------------------------------------------------------
// TEST 10: Staff Login & Grievance Detail Page Renders AI Advisory Assistant Card
// --------------------------------------------------------------------------
// Login as staff
$loginPage = req($baseUrl . '/login');
$csrf = extractCsrf($loginPage['body']);
$loginRes = req($baseUrl . '/login', 'POST', [
    '_csrf_token' => $csrf,
    'email'       => 'staff@smartgram.local',
    'password'    => 'Staff123!'
]);

$viewRes = req($baseUrl . '/grievance/view?id=' . $complaintAId);
if ($viewRes['code'] === 200 
    && (str_contains($viewRes['body'], 'AI Advisory Assistant') || str_contains($viewRes['body'], 'AI సలహా సహాయకుడు'))
    && (str_contains($viewRes['body'], 'Human Authority') || str_contains($viewRes['body'], 'మానవ నిర్ణయాధికారం'))) {
    echo "  [PASS] Test 10: Grievance detail view renders interactive AI Advisory Assistant Card for staff\n";
    $testsPassed++;
} else {
    echo "  [FAIL] Test 10: AI Advisory card missing in HTML response (HTTP {$viewRes['code']})\n";
}

// --------------------------------------------------------------------------
// TEST 11: Web HTTP - Staff Submits POST /grievance/ai-apply via Form
// --------------------------------------------------------------------------
// Seed a fresh unanalyzed complaint
$trackingFresh = 'GRV-2026-TESTFRESH';
$db->prepare("DELETE FROM grievances WHERE tracking_id = '$trackingFresh'")->execute();
$seedStmt->execute([':tracking' => $trackingFresh]);
$freshId = (int)$db->lastInsertId();

$freshViewRes = req($baseUrl . '/grievance/view?id=' . $freshId);
$viewCsrf = extractCsrf($freshViewRes['body']);

$aiApplyRes = req($baseUrl . '/grievance/ai-apply', 'POST', [
    '_csrf_token' => $viewCsrf,
    'id'          => $freshId,
    'category_id' => 1,
    'priority'    => 'critical',
    'summary'     => 'పైపులైన్ పగిలి నీరు వృధాగా పోతోంది — వెంటనే రిపేర్ చేయాలి',
    'confidence'  => 0.92
]);

$reloadedFresh = $db->query("SELECT * FROM grievances WHERE id = $freshId")->fetch(PDO::FETCH_ASSOC);
if ($aiApplyRes['code'] === 200 || $aiApplyRes['code'] === 302) {
    if ((int)$reloadedFresh['category_id'] === 1 && $reloadedFresh['priority'] === 'critical') {
        echo "  [PASS] Test 11: Web HTTP POST /grievance/ai-apply processed successfully by staff\n";
        $testsPassed++;
    } else {
        echo "  [FAIL] Test 11: Record did not update after HTTP POST\n";
    }
} else {
    echo "  [FAIL] Test 11: HTTP request failed with code {$aiApplyRes['code']}\n";
}

// --------------------------------------------------------------------------
// TEST 12: Executive 1-Line Summary Displayed under Citizen Description
// --------------------------------------------------------------------------
$afterApplyView = req($baseUrl . '/grievance/view?id=' . $freshId);
if (str_contains($afterApplyView['body'], 'పైపులైన్ పగిలి నీరు వృధాగా పోతోంది') && str_contains($afterApplyView['body'], '92%')) {
    echo "  [PASS] Test 12: Applied 1-line AI summary and confidence badge displayed under problem description\n";
    $testsPassed++;
} else {
    echo "  [FAIL] Test 12: AI summary block missing on detail page after application\n";
}

// --------------------------------------------------------------------------
// TEST 13: Timeline Shows "AI ACCEPTED" Action Badge
// --------------------------------------------------------------------------
if (str_contains($afterApplyView['body'], 'AI ACCEPTED')) {
    echo "  [PASS] Test 13: Chronological timeline renders 'AI ACCEPTED' event badge\n";
    $testsPassed++;
} else {
    echo "  [FAIL] Test 13: Timeline missing AI ACCEPTED badge\n";
}

// Cleanup test rows
$db->prepare("DELETE FROM grievance_updates WHERE grievance_id IN ($complaintAId, $freshId)")->execute();
$db->prepare("DELETE FROM audit_logs WHERE entity = 'grievances' AND entity_id IN ($complaintAId, $freshId)")->execute();
$db->prepare("DELETE FROM grievances WHERE id IN ($complaintAId, $freshId)")->execute();
if (file_exists($cookieFile)) {
    unlink($cookieFile);
}

echo "\n======================================================\n";
echo " PHASE 7 TEST RESULTS: {$testsPassed} / {$totalTests} TESTS PASSED\n";
echo "======================================================\n\n";

if ($testsPassed === $totalTests) {
    exit(0);
} else {
    exit(1);
}
