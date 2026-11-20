<?php
// ==============================================================================
// Smart Gram - Phase 4 Automated Grievance Tracking, Timeline & Feedback Test Suite
// ==============================================================================

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../app/core/Database.php';
require_once __DIR__ . '/../app/models/Grievance.php';

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

$baseUrl = 'http://127.0.0.1:8000';
$cookieFile = __DIR__ . '/test_cookies_phase4.txt';

if (file_exists($cookieFile)) {
    unlink($cookieFile);
}
touch($cookieFile);

function printTest(string $name, bool $passed, string $details = ''): void {
    if ($passed) {
        echo " [PASS] $name\n";
    } else {
        echo " [FAIL] $name - $details\n";
    }
}

function httpGet(string $url, ?string $cookieFile = null): array {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile ?: '');
    if ($cookieFile) {
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
    }
    $body = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return ['status' => $status, 'body' => (string)$body];
}

function httpPost(string $url, array $data, ?string $cookieFile = null): array {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile ?: '');
    if ($cookieFile) {
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
    }
    $body = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return ['status' => $status, 'body' => (string)$body];
}

echo "=========================================================\n";
echo " Smart Gram — Phase 4 Tracking, Timeline & Feedback Suite\n";
echo "=========================================================\n\n";

$db = \App\Core\Database::getConnection();

// Test 1: Tracking Lookup Page Loads
$trackPage = httpGet("$baseUrl/track?lang=te", $cookieFile);
printTest("1. Tracking lookup search page loads HTTP 200", $trackPage['status'] === 200);

$hasHeading = str_contains($trackPage['body'], 'సమస్య స్థితిని పరిశీలించండి') || str_contains($trackPage['body'], 'Track Grievance');
$hasInputs = str_contains($trackPage['body'], 'name="id"') && str_contains($trackPage['body'], 'name="phone"');
$hasNoLogin = str_contains($trackPage['body'], 'పౌరుల లాగిన్ అవసరం లేదు') || str_contains($trackPage['body'], 'No login required');
printTest("2. Search form displays Telugu labels and 'No Login Required' callout", $hasHeading && $hasInputs && $hasNoLogin);

// Test 2: Invalid Search Lookup
$invalidLookup = httpGet("$baseUrl/track?id=GRV-2026-99999&phone=9999999999", $cookieFile);
$hasNotFoundError = str_contains($invalidLookup['body'], 'కనుగొనబడలేదు') || str_contains($invalidLookup['body'], 'not found');
printTest("3. Non-existent grievance or wrong phone triggers friendly error", $hasNotFoundError);

// Test 3: Setup Test Grievance 1 in Database
$testId1 = 'GRV-2026-90001';
$testPhone1 = '9876500091';
$db->exec("DELETE FROM grievances WHERE tracking_id IN ('$testId1', 'GRV-2026-90002')");

$db->exec("
    INSERT INTO grievances (
        tracking_id, citizen_name, citizen_phone, village_id, ward_id,
        category_id, department_id, description, landmark, priority, status, sla_due_at, created_at
    ) VALUES (
        '$testId1', 'గోపాలకృష్ణ (Gopal)', '$testPhone1', 1, 1,
        1, 1, 'పైపులైన్ లీకేజీ సమస్య పరీక్షార్థం', 'శివాలయం పక్కన', 'high', 'submitted',
        DATE_ADD(NOW(), INTERVAL 24 HOUR), NOW()
    )
");
$g1Id = (int)$db->lastInsertId();

$db->exec("
    INSERT INTO grievance_updates (grievance_id, user_id, action, from_status, to_status, remarks, created_at)
    VALUES ($g1Id, NULL, 'submit', NULL, 'submitted', 'Grievance submitted by citizen', NOW())
");

// Test 4: Valid Tracking Lookup & Details View
$validLookup = httpGet("$baseUrl/track?id=$testId1&phone=$testPhone1&lang=te", $cookieFile);
printTest("4. Valid tracking lookup returns details view HTTP 200", $validLookup['status'] === 200);

$hasTrackingId = str_contains($validLookup['body'], $testId1);
$hasStatusSubmitted = str_contains($validLookup['body'], 'నమోదైంది');
$hasProgressBar = str_contains($validLookup['body'], 'progress-bar');
$hasTimeline = str_contains($validLookup['body'], 'సమస్య పరిష్కార కాలక్రమం') || str_contains($validLookup['body'], 'Timeline');

printTest("5. Details view renders Grievance ID, Status badge & Progress bar", $hasTrackingId && $hasStatusSubmitted && $hasProgressBar);
printTest("6. Chronological visual timeline rendered with timestamped events", $hasTimeline);

// Test 5: Simulate Resolution by Officer & Display Verification Card
$db->exec("
    UPDATE grievances SET status = 'resolved', updated_at = NOW() WHERE id = $g1Id
");
$db->exec("
    INSERT INTO grievance_updates (grievance_id, user_id, action, from_status, to_status, remarks, created_at)
    VALUES ($g1Id, 3, 'resolve', 'in_progress', 'resolved', 'Water pipe replaced and leak sealed. Tested and confirmed.', NOW())
");

$resolvedView = httpGet("$baseUrl/track?id=$testId1&phone=$testPhone1&lang=te", $cookieFile);
$hasResolvedStatus = str_contains($resolvedView['body'], 'సమస్య పరిష్కరించబడింది') || str_contains($resolvedView['body'], 'Resolved');
$hasFeedbackCard = str_contains($resolvedView['body'], 'పౌరుల ధృవీకరణ') || str_contains($resolvedView['body'], 'Citizen Verification');
printTest("7. Status updates to Resolved (90%) and displays Citizen Verification Card", $hasResolvedStatus && $hasFeedbackCard);

// Test 6: Citizen Submits Satisfaction Feedback (Yes -> Closed)
preg_match('/name="_csrf_token"\s+value="([^"]+)"/', $resolvedView['body'], $matches);
$csrfToken = $matches[1] ?? null;

$fbResponse = httpPost("$baseUrl/track/feedback", [
    '_csrf_token'    => $csrfToken,
    'grievance_id'   => $g1Id,
    'tracking_id'    => $testId1,
    'citizen_phone'  => $testPhone1,
    'is_satisfied'   => '1',
    'rating'         => '5',
    'comment'        => 'బాగా పనిచేశారు, సమస్య త్వరగా పరిష్కారమైంది (Excellent quick resolution).'
], $cookieFile);

// Verify DB State for Grievance 1
$g1Updated = $db->query("SELECT status FROM grievances WHERE id = $g1Id")->fetchColumn();
$fbRecord = $db->query("SELECT * FROM feedback WHERE grievance_id = $g1Id")->fetch(\PDO::FETCH_ASSOC);

printTest("8. Citizen satisfaction feedback updates grievance status to 'closed'", $g1Updated === 'closed');
printTest("9. Feedback record saved in database with 5-star rating", $fbRecord && $fbRecord['rating'] == 5 && $fbRecord['is_resolved_satisfied'] == 1);

// Test 7: Verify Display of Submitted Rating & Closed Badge
$closedView = httpGet("$baseUrl/track?id=$testId1&phone=$testPhone1&lang=te", $cookieFile);
$hasVerifiedBadge = str_contains($closedView['body'], 'పౌరుల ధృవీకరణ పూర్తయింది') || str_contains($closedView['body'], 'Verified by Citizen');
$has5Stars = str_contains($closedView['body'], '5/5 నక్షత్రాలు') || str_contains($closedView['body'], '5/5');
printTest("10. Closed grievance displays citizen verification badge and rating stars", $hasVerifiedBadge && $has5Stars);

// Test 8: Reopen Workflow (No, Problem Still Exists -> Reopened)
$testId2 = 'GRV-2026-90002';
$testPhone2 = '9876500092';
$db->exec("
    INSERT INTO grievances (
        tracking_id, citizen_name, citizen_phone, village_id, ward_id,
        category_id, department_id, description, landmark, priority, status, sla_due_at, created_at
    ) VALUES (
        '$testId2', 'సుబ్బారావు (Subba Rao)', '$testPhone2', 1, 2,
        4, 2, 'స్ట్రీట్ లైట్ సమస్య', 'గాంధీ బొమ్మ వద్ద', 'medium', 'resolved',
        DATE_ADD(NOW(), INTERVAL 48 HOUR), NOW()
    )
");
$g2Id = (int)$db->lastInsertId();

$g2View = httpGet("$baseUrl/track?id=$testId2&phone=$testPhone2", $cookieFile);
preg_match('/name="_csrf_token"\s+value="([^"]+)"/', $g2View['body'], $m2);
$token2 = $m2[1] ?? $csrfToken;

$reopenPost = httpPost("$baseUrl/track/feedback", [
    '_csrf_token'    => $token2,
    'grievance_id'   => $g2Id,
    'tracking_id'    => $testId2,
    'citizen_phone'  => $testPhone2,
    'is_satisfied'   => '0',
    'comment'        => 'లైట్ ఇంకా వెలగడం లేదు, రాత్రి మళ్లీ చీకటిగా ఉంది. దయచేసి మళ్లీ చూడండి.'
], $cookieFile);

$g2Status = $db->query("SELECT status FROM grievances WHERE id = $g2Id")->fetchColumn();
$timelineReopen = $db->query("SELECT * FROM grievance_updates WHERE grievance_id = $g2Id AND action = 'reopen'")->fetch(\PDO::FETCH_ASSOC);

printTest("11. Reopen submission transitions grievance status to 'reopened'", $g2Status === 'reopened');
printTest("12. Timeline records reopen action with citizen's exact reason", $timelineReopen && str_contains($timelineReopen['remarks'], 'లైట్ ఇంకా వెలగడం లేదు'));

// Verify Details Page Shows Reopened Alert Notice
$reopenedView = httpGet("$baseUrl/track?id=$testId2&phone=$testPhone2&lang=te", $cookieFile);
$hasReopenNotice = str_contains($reopenedView['body'], 'తిరిగి తెరవబడింది') || str_contains($reopenedView['body'], 'Reopened');
printTest("13. Reopened grievance renders warning notice informing citizen of re-inspection", $hasReopenNotice);

// Cleanup test records
$db->exec("DELETE FROM grievances WHERE tracking_id IN ('$testId1', '$testId2')");

echo "\n=========================================================\n";
echo " ALL 13 PHASE 4 TRACKING & FEEDBACK TESTS COMPLETED!\n";
echo "=========================================================\n";
