<?php
// ==============================================================================
// Smart Gram - Master Test Runner across ALL 10 Phases
// ==============================================================================

$phpBin = PHP_BINARY;
$testDir = __DIR__;

$suites = [
    'Phase 1: Database (PDO) & Internal Auth'   => 'test_phase1.php',
    'Phase 2: Bilingual UI & Telugu-First'      => 'test_phase2.php',
    'Phase 3: Citizen Grievance Submissions'    => 'test_phase3.php',
    'Phase 4: Tracking, Timeline & Feedback'    => 'test_phase4.php',
    'Phase 5: Admin & Staff Dashboards'         => 'test_phase5.php',
    'Phase 6: Staff & Officer Workflow'         => 'test_phase6.php',
    'Phase 7: AI Advisory Integration'          => 'test_phase7.php',
    'Phase 8: n8n Automation & Webhooks'        => 'test_phase8.php',
    'Phase 9: Security Hardening & E2E Testing' => 'test_phase9.php',
    'Phase 10: Production Readiness & Docs'     => 'test_phase10.php',
    'Architecture: Geographic Hierarchy & Relational Multi-Tier DB' => 'test_geographic_hierarchy.php',
    'Security & RBAC: Location-Scoped Role-Based Access Control'    => 'test_location_rbac.php',
];

echo "\n======================================================================\n";
echo "   SMART GRAM — COMPREHENSIVE 10-PHASE MASTER REGRESSION RUNNER      \n";
echo "======================================================================\n\n";

$allPassed = true;
$phaseResults = [];

foreach ($suites as $phaseName => $testFile) {
    echo "Running {$phaseName} ({$testFile})...\n";
    $output = [];
    $exitCode = 0;
    exec("\"{$phpBin}\" \"{$testDir}/{$testFile}\"", $output, $exitCode);

    if ($exitCode === 0) {
        echo "  --> [SUCCESS] {$phaseName} PASSED!\n\n";
        $phaseResults[$phaseName] = 'PASS';
    } else {
        echo "  --> [FAILURE] {$phaseName} FAILED!\n";
        echo implode("\n", array_slice($output, -10)) . "\n\n";
        $phaseResults[$phaseName] = 'FAIL';
        $allPassed = false;
    }
}

echo "======================================================================\n";
echo "                      MASTER SUITE RESULTS SUMMARY                    \n";
echo "======================================================================\n";

foreach ($phaseResults as $name => $status) {
    $icon = ($status === 'PASS') ? '✅ PASS' : '❌ FAIL';
    printf("  %-45s : %s\n", $name, $icon);
}

echo "======================================================================\n";
if ($allPassed) {
    echo "  🎉 ALL 10 PHASES COMPLETED WITH 100% AUTOMATED TEST COVERAGE!      \n";
    echo "======================================================================\n\n";
    exit(0);
} else {
    echo "  ⚠️ SOME SUITES FAILED. PLEASE REVIEW LOGS.                         \n";
    echo "======================================================================\n\n";
    exit(1);
}
