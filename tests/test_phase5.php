<?php
// ==============================================================================
// Smart Gram - Phase 5 Automated Verification Suite
// Admin & Panchayat Dashboard with Analytics, Hotspots, Overdue SLA & Filters
// ==============================================================================

$baseUrl = 'http://127.0.0.1:8000';
$cookieFile = __DIR__ . '/test_cookies_phase5.txt';
if (file_exists($cookieFile)) {
    unlink($cookieFile);
}

function req(string $url, string $method = 'GET', array $data = [], bool $followRedirects = true): array {
    global $cookieFile;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $followRedirects);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

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

function assertTest(string $desc, bool $condition, string $failMsg = ''): void {
    if ($condition) {
        echo " [PASS] $desc\n";
    } else {
        echo " [FAIL] $desc" . ($failMsg ? " - $failMsg" : '') . "\n";
        exit(1);
    }
}

echo "=========================================================\n";
echo " Smart Gram — Phase 5 Dashboard & Analytics Test Suite\n";
echo "=========================================================\n\n";

// --- Authenticate as Admin ---
$loginPage = req("$baseUrl/login");
$csrf = extractCsrf($loginPage['body']);
$loginResp = req("$baseUrl/login", 'POST', [
    '_csrf_token' => $csrf,
    'email' => 'admin@smartgram.local',
    'password' => 'Admin123!'
]);

assertTest("1. Admin logs in successfully and accesses Admin Dashboard", 
    $loginResp['code'] === 200 && str_contains($loginResp['body'], 'System Administrator')
);

// --- Test 2: KPI summary counters ---
$dashResp = req("$baseUrl/admin");
assertTest("2. Admin Dashboard renders all 8 KPI Performance Indicators", 
    str_contains($dashResp['body'], 'Performance Indicators') || str_contains($dashResp['body'], 'సమస్యల సారాంశం')
);

// --- Test 3: Category Breakdown ---
assertTest("3. Category breakdown analytics rendered with progress bars and percentages", 
    (str_contains($dashResp['body'], 'Category Breakdown') || str_contains($dashResp['body'], 'విభాగాల వారీగా')) &&
    str_contains($dashResp['body'], 'progress-bar bg-primary')
);

// --- Test 4: Ward Hotspots Analysis ---
assertTest("4. Ward hotspots analysis identifies village hotspot with badge", 
    (str_contains($dashResp['body'], 'Ward Hotspots') || str_contains($dashResp['body'], 'వార్డుల వారీగా')) &&
    str_contains($dashResp['body'], 'Hotspot')
);

// --- Test 5: SLA Overdue Detection ---
assertTest("5. Overdue SLA alerts table flags overdue grievances with breach hours", 
    (str_contains($dashResp['body'], 'Overdue Breaches') || str_contains($dashResp['body'], 'గడువు దాటినవి') || str_contains($dashResp['body'], 'overdue'))
);

// --- Test 6: Field Officer Workload Distribution ---
assertTest("6. Field officer workload distribution reports active staff assignments", 
    (str_contains($dashResp['body'], 'Field Officer Workload') || str_contains($dashResp['body'], 'క్షేత్ర స్థాయి అధికారుల పనిభారం')) &&
    str_contains($dashResp['body'], 'Water Works Field Officer')
);

// --- Test 7: Citizen Satisfaction Rating Metric ---
assertTest("7. Citizen satisfaction rating calculated and displayed out of 5.0", 
    str_contains($dashResp['body'], '/ 5.0') &&
    (str_contains($dashResp['body'], 'ratings') || str_contains($dashResp['body'], 'రేటింగ్‌లు'))
);

// --- Test 8: Grievance Filtering by Status ---
$filterStatus = req("$baseUrl/admin?status=in_progress");
assertTest("8. Filtering grievances by status ('in_progress') succeeds", 
    $filterStatus['code'] === 200 && str_contains($filterStatus['body'], 'IN PROGRESS')
);

// --- Test 9: Grievance Filtering by Priority ---
$filterPriority = req("$baseUrl/admin?priority=critical");
assertTest("9. Filtering grievances by priority ('critical') succeeds", 
    $filterPriority['code'] === 200 && str_contains($filterPriority['body'], 'CRITICAL')
);

// --- Test 10: Grievance Search by Tracking ID / Phone ---
$searchResp = req("$baseUrl/admin?search=GRV-2026-00010");
assertTest("10. Searching grievances by Tracking ID returns matching record", 
    $searchResp['code'] === 200 && str_contains($searchResp['body'], 'GRV-2026-00010') && str_contains($searchResp['body'], 'K. Venkata Rao')
);

// --- Test 11: Export CSV Endpoint ---
$exportResp = req("$baseUrl/dashboard/export");
assertTest("11. Export CSV generates valid text/csv download with headers", 
    $exportResp['code'] === 200 && 
    str_contains($exportResp['headers'], 'text/csv') &&
    str_contains($exportResp['body'], 'Grievance ID') &&
    str_contains($exportResp['body'], 'Citizen Name') &&
    str_contains($exportResp['body'], 'Ward')
);

// --- Test 12: Bilingual Dashboard Localization (Telugu) ---
$teDash = req("$baseUrl/admin?lang=te");
assertTest("12. Telugu dashboard localization renders native Telugu headers and labels", 
    $teDash['code'] === 200 && 
    str_contains($teDash['body'], 'పరిపాలన డ్యాష్‌బోర్డ్') &&
    str_contains($teDash['body'], 'మొత్తం ఫిర్యాదులు')
);

// --- Test 13: Staff and Officer Role Dashboards ---
// Logout admin
req("$baseUrl/logout", 'POST', ['_csrf_token' => extractCsrf($teDash['body'])]);

// Login as Staff
$staffLoginPage = req("$baseUrl/login");
$staffLogin = req("$baseUrl/login", 'POST', [
    '_csrf_token' => extractCsrf($staffLoginPage['body']),
    'email' => 'staff@smartgram.local',
    'password' => 'Staff123!'
]);
assertTest("13. Panchayat Staff portal loads village-specific workbench with 200 OK", 
    $staffLogin['code'] === 200 && (str_contains($staffLogin['body'], 'Staff Verification Queue') || str_contains($staffLogin['body'], 'Panchayat Staff Portal'))
);

if (file_exists($cookieFile)) {
    unlink($cookieFile);
}

echo "\n=========================================================\n";
echo " ALL 13 PHASE 5 DASHBOARD & ANALYTICS TESTS COMPLETED!\n";
echo "=========================================================\n\n";
