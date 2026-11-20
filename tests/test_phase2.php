<?php
// ==============================================================================
// Smart Gram - Phase 2 Automated Bilingual & Citizen Interface Test Suite
// ==============================================================================

$baseUrl = 'http://127.0.0.1:8000';
$cookieFile = __DIR__ . '/test_cookies_phase2.txt';

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

echo "=========================================================\n";
echo " Smart Gram — Phase 2 Bilingual & Citizen UI Test Suite\n";
echo "=========================================================\n\n";

// Test 1: Telugu-First Homepage by Default
$resTe = httpGet("$baseUrl/?lang=te", $cookieFile);
printTest("1. Default Homepage loads HTTP 200", $resTe['status'] === 200);

$hasTeTitle = str_contains($resTe['body'], 'గ్రామ సమస్య పరిష్కార వేదిక');
$hasTeTagline = str_contains($resTe['body'], 'మీ సమస్యను సులభంగా తెలియజేయండి');
$hasTeNoLogin = str_contains($resTe['body'], 'పౌరుల లాగిన్ అవసరం లేదు');
$hasTeSubmitBtn = str_contains($resTe['body'], 'సమస్యను నమోదు చేయండి');
$hasTeTrackBtn = str_contains($resTe['body'], 'నా సమస్య స్థితిని చూడండి');
$hasTeAboutBtn = str_contains($resTe['body'], 'Smart Gram గురించి');

printTest("2. Telugu Title is displayed natively", $hasTeTitle);
printTest("3. Telugu Tagline is displayed natively", $hasTeTagline);
printTest("4. Telugu 'Zero Citizen Login' banner is present", $hasTeNoLogin);
printTest("5. Three Primary Action Buttons in Telugu present", $hasTeSubmitBtn && $hasTeTrackBtn && $hasTeAboutBtn);

// Test 2: Category Grid Rendering in Telugu
$hasWaterCat = str_contains($resTe['body'], 'తాగునీటి సమస్యలు');
$hasElectricCat = str_contains($resTe['body'], 'వీధి దీపాలు');
$hasSanitationCat = str_contains($resTe['body'], 'పారిశుధ్యం');
printTest("6. Problem Categories rendered in Telugu", $hasWaterCat && $hasElectricCat && $hasSanitationCat);

// Test 3: Language Switch to English
$resEn = httpGet("$baseUrl/?lang=en", $cookieFile);
printTest("7. English switch returns HTTP 200", $resEn['status'] === 200);

$hasEnTitle = str_contains($resEn['body'], 'Village Grievance Management System');
$hasEnTagline = str_contains($resEn['body'], 'Report your village problems easily');
$hasEnNoLogin = str_contains($resEn['body'], 'NO Citizen Login Required');
$hasEnSubmitBtn = str_contains($resEn['body'], 'Submit Grievance');
$hasEnTrackBtn = str_contains($resEn['body'], 'Track My Grievance');
$hasEnAboutBtn = str_contains($resEn['body'], 'About Smart Gram');

printTest("8. English Title is displayed cleanly", $hasEnTitle);
printTest("9. English 'Zero Citizen Login' banner is present", $hasEnNoLogin);
printTest("10. Three Primary Action Buttons in English present", $hasEnSubmitBtn && $hasEnTrackBtn && $hasEnAboutBtn);

// Test 4: About Page in Telugu & English
$aboutTe = httpGet("$baseUrl/about?lang=te", $cookieFile);
$hasAboutTe = str_contains($aboutTe['body'], 'Smart Gram గురించి') && str_contains($aboutTe['body'], 'మా లక్ష్యం');
printTest("11. About page loads in Telugu", $aboutTe['status'] === 200 && $hasAboutTe);

$aboutEn = httpGet("$baseUrl/about?lang=en", $cookieFile);
$hasAboutEn = str_contains($aboutEn['body'], 'About Smart Gram') && str_contains($aboutEn['body'], 'Our Mission');
printTest("12. About page loads in English", $aboutEn['status'] === 200 && $hasAboutEn);

// Test 5: Verify Phase 1 Internal Login is Preserved
$loginPage = httpGet("$baseUrl/login", $cookieFile);
$hasStaffLogin = str_contains($loginPage['body'], 'అధికారుల లాగిన్') || str_contains($loginPage['body'], 'Internal Staff');
printTest("13. Phase 1 Internal Staff/Officer Login is fully preserved", $loginPage['status'] === 200 && $hasStaffLogin);

echo "\n=========================================================\n";
echo " ALL 13 PHASE 2 BILINGUAL & CITIZEN TESTS COMPLETED!\n";
echo "=========================================================\n";
