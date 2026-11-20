<?php
// ==============================================================================
// Smart Gram - Phase 6 Automated Workflow Test Suite
// Staff Verification, Officer Assignment, Field Work, and Photographic Evidence
// ==============================================================================

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../app/core/Database.php';

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

$baseUrl = 'http://127.0.0.1:8000';
$cookieFile = __DIR__ . '/test_cookies_phase6.txt';
if (file_exists($cookieFile)) {
    unlink($cookieFile);
}

function req(string $url, string $method = 'GET', array $data = [], array $files = []): array {
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
        if (!empty($files)) {
            // Multipart form data
            $postFields = $data;
            foreach ($files as $field => $filepath) {
                $postFields[$field] = new CURLFile($filepath, 'image/jpeg', basename($filepath));
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        } else {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        }
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

function assertTest(string $desc, bool $condition, string $failMsg = ''): void {
    if ($condition) {
        echo " [PASS] $desc\n";
    } else {
        echo " [FAIL] $desc" . ($failMsg ? " - $failMsg" : '') . "\n";
        exit(1);
    }
}

echo "=========================================================\n";
echo " Smart Gram — Phase 6 Staff & Officer Workflow Suite\n";
echo "=========================================================\n\n";

$db = \App\Core\Database::getConnection();

// Create a dedicated fresh grievance for testing the entire workflow
$testTrackingId = 'GRV-2026-66' . rand(100, 999);
$db->exec("
    INSERT INTO grievances 
    (tracking_id, village_id, ward_id, category_id, department_id, citizen_name, citizen_phone, description, landmark, priority, status, sla_due_at, created_at)
    VALUES 
    ('$testTrackingId', 1, 1, 1, 1, 'P. Workflow Tester', '9848123456', 'Main water pipeline leaking heavily at village square.', 'Village Square Pole #4', 'high', 'submitted', DATE_ADD(NOW(), INTERVAL 24 HOUR), NOW())
");
$testGrievanceId = (int)$db->lastInsertId();

$db->exec("
    INSERT INTO grievance_updates (grievance_id, user_id, action, from_status, to_status, remarks, created_at)
    VALUES ($testGrievanceId, NULL, 'citizen_submitted', NULL, 'submitted', 'Grievance submitted by citizen', NOW())
");

// --- 1. Staff Login ---
$loginPage = req("$baseUrl/login");
$csrf = extractCsrf($loginPage['body']);
$staffLogin = req("$baseUrl/login", 'POST', [
    '_csrf_token' => $csrf,
    'email' => 'staff@smartgram.local',
    'password' => 'Staff123!'
]);

assertTest("1. Staff logs in and accesses internal workflow with 200 OK", 
    $staffLogin['code'] === 200 && str_contains($staffLogin['body'], 'Staff Verification Queue')
);

// --- 2. Staff views Grievance Detail ---
$detailView = req("$baseUrl/grievance/view?id=$testGrievanceId");
assertTest("2. Grievance Detail page renders citizen card, description, and timeline", 
    $detailView['code'] === 200 && 
    str_contains($detailView['body'], $testTrackingId) && 
    str_contains($detailView['body'], 'P. Workflow Tester') &&
    str_contains($detailView['body'], 'Main water pipeline leaking')
);

// --- 3. Staff Verifies Complaint ---
$csrf = extractCsrf($detailView['body']);
$verifyResp = req("$baseUrl/grievance/verify", 'POST', [
    '_csrf_token' => $csrf,
    'id' => $testGrievanceId,
    'remarks' => 'Verified on phone call with citizen'
]);

$statusAfterVerify = $db->query("SELECT status FROM grievances WHERE id = $testGrievanceId")->fetchColumn();
assertTest("3. Staff verifies grievance, transitioning status to 'under_review'", 
    $statusAfterVerify === 'under_review' && (str_contains($verifyResp['body'], 'ధృవీకరించబడింది') || str_contains($verifyResp['body'], 'verified') || str_contains($verifyResp['body'], 'UNDER REVIEW'))
);

// --- 4. Staff Assigns Field Officer ---
$csrf = extractCsrf($verifyResp['body']);
$assignResp = req("$baseUrl/grievance/assign", 'POST', [
    '_csrf_token' => $csrf,
    'id' => $testGrievanceId,
    'officer_id' => 3, // Water Works Field Officer
    'priority' => 'critical',
    'remarks' => 'Please visit site immediately with repair valve'
]);

$assignedRow = $db->query("SELECT status, assigned_to_user_id, priority FROM grievances WHERE id = $testGrievanceId")->fetch(PDO::FETCH_ASSOC);
assertTest("4. Staff assigns field officer, setting status to 'assigned' & priority to 'critical'", 
    $assignedRow['status'] === 'assigned' && 
    (int)$assignedRow['assigned_to_user_id'] === 3 &&
    $assignedRow['priority'] === 'critical'
);

// --- 5. Verify Timeline & Audit Log of Assignment ---
$lastUpdate = $db->query("SELECT action, to_status, remarks FROM grievance_updates WHERE grievance_id = $testGrievanceId ORDER BY id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
assertTest("5. Assignment recorded in grievance_updates with assigned officer details", 
    $lastUpdate['action'] === 'staff_assigned' && 
    $lastUpdate['to_status'] === 'assigned' &&
    str_contains($lastUpdate['remarks'], 'Water Works Field Officer')
);

// --- 6. Field Officer Login ---
file_put_contents($cookieFile, '');
$loginPage = req("$baseUrl/login");
$csrf = extractCsrf($loginPage['body']);
$officerLogin = req("$baseUrl/login", 'POST', [
    '_csrf_token' => $csrf,
    'email' => 'officer@smartgram.local',
    'password' => 'Officer123!'
]);

assertTest("6. Field Officer logs in and loads Assigned Workbench", 
    $officerLogin['code'] === 200 && str_contains($officerLogin['body'], 'Assigned Grievances Workbench')
);

// --- 7. Field Officer Starts Work ---
$officerDetail = req("$baseUrl/grievance/view?id=$testGrievanceId");
$csrf = extractCsrf($officerDetail['body']);

$startWorkResp = req("$baseUrl/grievance/start-work", 'POST', [
    '_csrf_token' => $csrf,
    'id' => $testGrievanceId,
    'remarks' => 'Arrived on site with welding equipment'
]);

$statusAfterStart = $db->query("SELECT status FROM grievances WHERE id = $testGrievanceId")->fetchColumn();
assertTest("7. Field Officer commences on-site work, transitioning status to 'in_progress'", 
    $statusAfterStart === 'in_progress'
);

// --- 8. Field Officer Adds Interim Progress Note ---
$csrf = extractCsrf($startWorkResp['body']);
$noteResp = req("$baseUrl/grievance/note", 'POST', [
    '_csrf_token' => $csrf,
    'id' => $testGrievanceId,
    'remarks' => 'Excavation completed, replaced 2-inch PVC valve'
]);

$noteCount = (int)$db->query("SELECT COUNT(*) FROM grievance_updates WHERE grievance_id = $testGrievanceId AND action = 'progress_note'")->fetchColumn();
assertTest("8. Interim progress note recorded in timeline without altering in_progress status", 
    $noteCount >= 1 && str_contains($noteResp['body'], 'Excavation completed')
);

// --- 9. Resolution Attempt Without Mandatory Photo Evidence is Blocked ---
$csrf = extractCsrf($noteResp['body']);
$badResolve = req("$baseUrl/grievance/resolve", 'POST', [
    '_csrf_token' => $csrf,
    'id' => $testGrievanceId,
    'remarks' => 'Completed work without photo'
]);

$statusAfterBad = $db->query("SELECT status FROM grievances WHERE id = $testGrievanceId")->fetchColumn();
assertTest("9. Resolution attempt without mandatory photo evidence is strictly rejected", 
    $statusAfterBad === 'in_progress' && str_contains($badResolve['body'], 'photo')
);

// --- 10. Field Officer Resolves Grievance with Photographic Evidence ---
$testPhoto = __DIR__ . '/test_evidence_photo.jpg';
// Create valid minimal JPEG image (1x1 pixel)
file_put_contents($testPhoto, base64_decode('/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAABAAEBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA='));

$csrf = extractCsrf($badResolve['body']);
$resolveResp = req(
    "$baseUrl/grievance/resolve", 
    'POST', 
    [
        '_csrf_token' => $csrf,
        'id' => $testGrievanceId,
        'remarks' => 'Leak sealed with new heavy-duty clamp and tested under pressure. Water flow restored.'
    ],
    [
        'resolution_photo' => $testPhoto
    ]
);

$statusAfterResolve = $db->query("SELECT status FROM grievances WHERE id = $testGrievanceId")->fetchColumn();
assertTest("10. Officer resolves grievance with photo upload, transitioning status to 'resolved'", 
    $statusAfterResolve === 'resolved'
);

// --- 11. Verify Resolution Evidence in Attachments Table ---
$evidenceAtt = $db->query("
    SELECT * FROM attachments 
    WHERE grievance_id = $testGrievanceId AND attachment_type = 'resolution_evidence' 
    ORDER BY id DESC LIMIT 1
")->fetch(PDO::FETCH_ASSOC);

assertTest("11. Resolution photographic evidence securely registered in database with officer ID", 
    $evidenceAtt !== false && 
    (int)$evidenceAtt['uploaded_by_user_id'] === 3 &&
    file_exists(BASE_PATH . '/' . $evidenceAtt['file_path'])
);

// --- 12. Verify Public Tracking Page Displays Resolution Evidence Photo ---
$publicTrack = req("$baseUrl/track?tracking_id=$testTrackingId&phone=9848123456");
assertTest("12. Public Tracking Page reflects 'Resolved' (90%) and displays resolution evidence photo", 
    $publicTrack['code'] === 200 && 
    str_contains($publicTrack['body'], 'పరిష్కార సాక్ష్యం') &&
    str_contains($publicTrack['body'], basename($evidenceAtt['file_path']))
);

// --- 13. Staff Rejection Workflow ---
// Create a separate spam/invalid complaint
$spamTrackingId = 'GRV-2026-99' . rand(100, 999);
$db->exec("
    INSERT INTO grievances 
    (tracking_id, village_id, ward_id, category_id, department_id, citizen_name, citizen_phone, description, priority, status, created_at)
    VALUES 
    ('$spamTrackingId', 1, 1, 1, 1, 'Fake User', '9999999999', 'Spam test text 12345', 'low', 'submitted', NOW())
");
$spamGrievanceId = (int)$db->lastInsertId();

file_put_contents($cookieFile, '');
// Login as Staff again
$loginPage = req("$baseUrl/login");
$csrf = extractCsrf($loginPage['body']);
req("$baseUrl/login", 'POST', [
    '_csrf_token' => $csrf,
    'email' => 'staff@smartgram.local',
    'password' => 'Staff123!'
]);

$spamDetail = req("$baseUrl/grievance/view?id=$spamGrievanceId");
$csrf = extractCsrf($spamDetail['body']);
$rejectResp = req("$baseUrl/grievance/reject", 'POST', [
    '_csrf_token' => $csrf,
    'id' => $spamGrievanceId,
    'remarks' => 'Duplicate submission with invalid contact number.'
]);

$spamStatus = $db->query("SELECT status FROM grievances WHERE id = $spamGrievanceId")->fetchColumn();
assertTest("13. Staff rejection workflow transitions grievance status to 'rejected' with mandatory reason", 
    $spamStatus === 'rejected'
);

// Cleanup
if (file_exists($testPhoto)) {
    unlink($testPhoto);
}
if (file_exists($cookieFile)) {
    unlink($cookieFile);
}

echo "\n=========================================================\n";
echo " ALL 13 PHASE 6 WORKFLOW & RESOLUTION TESTS COMPLETED!\n";
echo "=========================================================\n\n";
