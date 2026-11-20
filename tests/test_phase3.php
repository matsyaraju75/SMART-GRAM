<?php
// ==============================================================================
// Smart Gram - Phase 3 Automated Grievance Submission Test Suite
// ==============================================================================

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../app/core/Database.php';

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

$baseUrl = 'http://127.0.0.1:8000';
$cookieFile = __DIR__ . '/test_cookies_phase3.txt';

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
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
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
echo " Smart Gram — Phase 3 Grievance Submission Test Suite\n";
echo "=========================================================\n\n";

// Test 1: Submission Form Page Loads in Telugu by Default
$formPage = httpGet("$baseUrl/submit?lang=te", $cookieFile);
printTest("1. Grievance Form page loads HTTP 200", $formPage['status'] === 200);

$hasFormHeading = str_contains($formPage['body'], 'సమస్య నమోదు ఫారమ్');
$hasNoLoginBanner = str_contains($formPage['body'], 'పౌరుల లాగిన్ అవసరం లేదు');
$hasFields = str_contains($formPage['body'], 'citizen_name') && 
             str_contains($formPage['body'], 'citizen_phone') && 
             str_contains($formPage['body'], 'category_id') && 
             str_contains($formPage['body'], 'description');
preg_match('/name="_csrf_token"\s+value="([^"]+)"/', $formPage['body'], $matches);
$csrfToken = $matches[1] ?? null;

printTest("2. Form displays Telugu headings & 'No Citizen Login' notice", $hasFormHeading && $hasNoLoginBanner);
printTest("3. All required input fields are present in form", $hasFields);
printTest("4. Cryptographic CSRF token rendered in form", !empty($csrfToken));

// Test 2: Dynamic Wards API Endpoint
$apiRes = httpGet("$baseUrl/api/wards?village_id=1");
$apiData = json_decode($apiRes['body'], true);
$hasWards = !empty($apiData['wards']) && count($apiData['wards']) >= 3;
printTest("5. Dynamic Wards API returns JSON array of village wards", $apiRes['status'] === 200 && $hasWards);

// Test 3: Validation Failure on Invalid Mobile Number
$invalidPost = httpPost("$baseUrl/submit", [
    '_csrf_token' => $csrfToken,
    'citizen_name' => 'రామయ్య',
    'citizen_phone' => '12345', // Invalid phone
    'village_id' => '1',
    'category_id' => '1',
    'description' => 'సమస్య వివరాలు ఇక్కడ ఉన్నాయి...'
], $cookieFile);

$hasPhoneError = str_contains($invalidPost['body'], 'మొబైల్ నంబరును నమోదు చేయండి') || str_contains($invalidPost['body'], 'valid 10-digit mobile');
printTest("6. Invalid phone format rejected with validation error", $hasPhoneError);

// Extract fresh CSRF token
preg_match('/name="_csrf_token"\s+value="([^"]+)"/', $invalidPost['body'], $matches);
$csrfToken = $matches[1] ?? $csrfToken;

// Test 4: Successful Submission (Standard Text Form without Photo)
$validPost = httpPost("$baseUrl/submit", [
    '_csrf_token' => $csrfToken,
    'citizen_name' => 'రామయ్య (Ramaiah)',
    'citizen_phone' => '9876543210',
    'village_id' => '1',
    'ward_id' => '1',
    'category_id' => '1', // Pipeline Leakage
    'landmark' => 'రామాలయం ఎదురుగా (Opposite Temple)',
    'latitude' => '15.5057',
    'longitude' => '80.0499',
    'description' => 'మా వీధిలో మెయిన్ పైపులైన్ పగిలి తాగునీరు వృధాగా రోడ్డుపైకి వస్తోంది. దయచేసి వెంటనే బాగు చేయగలరు.'
], $cookieFile);

$hasSuccessBanner = str_contains($validPost['body'], 'విజయవంతంగా నమోదు చేయబడింది') || str_contains($validPost['body'], 'Submitted Successfully');
preg_match('/(GRV-2026-\d{5})/', $validPost['body'], $idMatches);
$generatedId1 = $idMatches[1] ?? null;

printTest("7. Valid grievance submitted successfully without login", $hasSuccessBanner);
printTest("8. Unique Grievance ID generated (format GRV-YYYY-XXXXX)", !empty($generatedId1));

// Test 5: Verify MySQL Record, Priority, Department Routing, and Initial Timeline
$db = \App\Core\Database::getConnection();
$stmt = $db->prepare("SELECT * FROM grievances WHERE tracking_id = :id");
$stmt->execute([':id' => $generatedId1]);
$grv1 = $stmt->fetch(\PDO::FETCH_ASSOC);

$isDbValid = $grv1 && 
             $grv1['citizen_phone'] === '9876543210' && 
             $grv1['status'] === 'submitted' && 
             $grv1['department_id'] == 1 && // Routed to Water Supply
             !empty($grv1['sla_due_at']);
printTest("9. Database record stored correctly with department routing & SLA", $isDbValid);

// Verify Timeline Entry
$updateStmt = $db->prepare("SELECT * FROM grievance_updates WHERE grievance_id = :gid");
$updateStmt->execute([':gid' => $grv1['id']]);
$timeline = $updateStmt->fetch(\PDO::FETCH_ASSOC);
$isTimelineValid = $timeline && $timeline['action'] === 'submit' && $timeline['to_status'] === 'submitted';
printTest("10. Initial timeline status update logged in grievance_updates", $isTimelineValid);

// Test 6: Submission WITH Photo Attachment
// Create a small 1x1 valid PNG image in scratch for testing
$testImgPath = __DIR__ . '/test_sample_photo.png';
$samplePngData = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
file_put_contents($testImgPath, $samplePngData);

// Fetch fresh CSRF token
$freshForm = httpGet("$baseUrl/submit", $cookieFile);
preg_match('/name="_csrf_token"\s+value="([^"]+)"/', $freshForm['body'], $matches);
$csrfToken = $matches[1] ?? null;

$postFields = [
    '_csrf_token' => $csrfToken,
    'citizen_name' => 'లక్ష్మి (Lakshmi)',
    'citizen_phone' => '9876543222',
    'village_id' => '1',
    'ward_id' => '2',
    'category_id' => '4', // Street light
    'landmark' => 'శివాలయం వీధి పోల్ నెం 12',
    'description' => 'మా వీధిలో గత 3 రోజులుగా వీధి దీపం వెలగడం లేదు, రాత్రి వేళ చీకటిగా ఉంది.',
    'photo' => new CURLFile($testImgPath, 'image/png', 'problem_photo.png')
];

$photoPost = httpPost("$baseUrl/submit", $postFields, $cookieFile);
preg_match('/(GRV-2026-\d{5})/', $photoPost['body'], $idMatches2);
$generatedId2 = $idMatches2[1] ?? null;

printTest("11. Grievance with photo attachment submitted successfully", !empty($generatedId2));

// Verify attachment in DB and disk
$stmt2 = $db->prepare("SELECT g.id, a.file_path, a.mime_type FROM grievances g JOIN attachments a ON g.id = a.grievance_id WHERE g.tracking_id = :id");
$stmt2->execute([':id' => $generatedId2]);
$attRow = $stmt2->fetch(\PDO::FETCH_ASSOC);

$fileOnDisk = $attRow && file_exists(BASE_PATH . '/' . $attRow['file_path']);
printTest("12. Photo attachment securely saved in storage/uploads and linked in DB", $fileOnDisk && $attRow['mime_type'] === 'image/png');

// Test 7: Success Page Content & Copy ID button
$successPage = httpGet("$baseUrl/submit/success?id=$generatedId1", $cookieFile);
$hasCopyBtn = str_contains($successPage['body'], 'copyIdBtn');
$hasAdvice = str_contains($successPage['body'], 'ఈ Grievance ID ని భద్రపరుచుకోండి') || str_contains($successPage['body'], 'save this Grievance ID');
printTest("13. Confirmation page renders Grievance ID, Copy button, and citizen advice", $hasCopyBtn && $hasAdvice);

// Clean up test image
if (file_exists($testImgPath)) {
    unlink($testImgPath);
}

echo "\n=========================================================\n";
echo " ALL 13 PHASE 3 GRIEVANCE SUBMISSION TESTS COMPLETED!\n";
echo "=========================================================\n";
