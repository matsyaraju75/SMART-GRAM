<?php
// ==============================================================================
// Smart Gram - Phase 1 Automated End-to-End Test Suite
// ==============================================================================

$baseUrl = 'http://127.0.0.1:8000';
$cookieFile = __DIR__ . '/test_cookies.txt';

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
echo " Smart Gram — Phase 1 Automated Verification Suite\n";
echo "=========================================================\n\n";

// Test 1: Public Home Page (Zero Login Required)
$res = httpGet("$baseUrl/");
$hasTitle = str_contains($res['body'], 'Smart Gram');
$hasTelugu = str_contains($res['body'], 'గ్రామ సమస్య పరిష్కార వేదిక');
$hasZeroLogin = str_contains($res['body'], 'పౌరుల లాగిన్ అవసరం లేదు') || str_contains($res['body'], 'NO Citizen Login Required');
printTest("1. Public Homepage loads (HTTP 200)", $res['status'] === 200);
printTest("2. Homepage contains Telugu & Zero Citizen Login notice", $hasTitle && $hasTelugu && $hasZeroLogin);

// Test 2: Login Page & CSRF Token Generation
$loginRes = httpGet("$baseUrl/login", $cookieFile);
preg_match('/name="_csrf_token"\s+value="([^"]+)"/', $loginRes['body'], $matches);
$csrfToken = $matches[1] ?? null;
printTest("3. Login page returns HTTP 200", $loginRes['status'] === 200);
printTest("4. Cryptographic CSRF token generated", !empty($csrfToken));

// Test 3: Failed Login (Bad credentials)
$badLogin = httpPost("$baseUrl/login", [
    '_csrf_token' => $csrfToken,
    'email' => 'admin@smartgram.local',
    'password' => 'WrongPassword!'
], $cookieFile);
$hasErrorMsg = str_contains($badLogin['body'], 'Invalid email or password');
printTest("5. Invalid credentials properly rejected", $hasErrorMsg);

// Extract fresh CSRF token after redirect
preg_match('/name="_csrf_token"\s+value="([^"]+)"/', $badLogin['body'], $matches);
$csrfToken = $matches[1] ?? $csrfToken;

// Test 4: Successful Admin Login
$adminLogin = httpPost("$baseUrl/login", [
    '_csrf_token' => $csrfToken,
    'email' => 'admin@smartgram.local',
    'password' => 'Admin123!'
], $cookieFile);
$isAdminDashboard = str_contains($adminLogin['body'], 'System Control Center') && str_contains($adminLogin['body'], 'Admin Portal');
printTest("6. Admin Login succeeds and loads Admin Dashboard", $isAdminDashboard);

// Test 5: Verify MySQL Audit Logs via CLI
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../app/core/Database.php';
require_once __DIR__ . '/../app/models/AuditLog.php';
$recentLogs = \App\Models\AuditLog::getRecent(5);
$hasLoginAudit = false;
foreach ($recentLogs as $log) {
    if ($log['action'] === 'login_success' && $log['email'] === 'admin@smartgram.local') {
        $hasLoginAudit = true;
        break;
    }
}
printTest("7. Successful login recorded in MySQL audit_logs", $hasLoginAudit);

// Test 6: Staff Login
file_put_contents($cookieFile, '');
$staffPage = httpGet("$baseUrl/login", $cookieFile);
preg_match('/name="_csrf_token"\s+value="([^"]+)"/', $staffPage['body'], $matches);
$csrfToken = $matches[1] ?? null;
$staffLogin = httpPost("$baseUrl/login", [
    '_csrf_token' => $csrfToken,
    'email' => 'staff@smartgram.local',
    'password' => 'Staff123!'
], $cookieFile);
$isStaffDashboard = str_contains($staffLogin['body'], 'Staff Verification Queue');
printTest("8. Staff Login succeeds and loads Staff Queue", $isStaffDashboard);

// Test 7: Officer Login
file_put_contents($cookieFile, '');
$officerPage = httpGet("$baseUrl/login", $cookieFile);
preg_match('/name="_csrf_token"\s+value="([^"]+)"/', $officerPage['body'], $matches);
$csrfToken = $matches[1] ?? null;
$officerLogin = httpPost("$baseUrl/login", [
    '_csrf_token' => $csrfToken,
    'email' => 'officer@smartgram.local',
    'password' => 'Officer123!'
], $cookieFile);
$isOfficerDashboard = str_contains($officerLogin['body'], 'Assigned Grievances Workbench');
printTest("9. Officer Login succeeds and loads Officer Workbench", $isOfficerDashboard);

// Test 8: Logout
preg_match('/name="_csrf_token"\s+value="([^"]+)"/', $officerLogin['body'], $matches);
$csrfToken = $matches[1] ?? $csrfToken;
$logoutRes = httpPost("$baseUrl/logout", [
    '_csrf_token' => $csrfToken
], $cookieFile);
$hasLoggedOut = str_contains($logoutRes['body'], 'successfully logged out');
printTest("10. User Logout destroys session and redirects to login", $hasLoggedOut);

// Test 9: Protected Route Access Rejection
file_put_contents($cookieFile, '');
$guestAccess = httpGet("$baseUrl/dashboard", $cookieFile);
$isRedirected = str_contains($guestAccess['body'], 'Please login to access the internal portal');
printTest("11. Guest access to protected /dashboard blocked", $isRedirected);

echo "\n=========================================================\n";
echo " ALL TESTS COMPLETED!\n";
echo "=========================================================\n";
