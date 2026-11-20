<?php
// ==============================================================================
// Smart Gram - Geographic Hierarchy & Location Architecture Test Suite
// ==============================================================================

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

require_once BASE_PATH . '/config/app.php';
require_once BASE_PATH . '/app/helpers/helpers.php';

spl_autoload_register(function (string $class) {
    $prefix = 'App\\';
    $baseDir = BASE_PATH . '/app/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

use App\Core\Database;
use App\Models\Grievance;
use App\Models\Location;
use App\Services\GrievanceService;

$passedCount = 0;
$totalCount = 0;

function assertTest(string $description, bool $condition, string $details = ''): void {
    global $passedCount, $totalCount;
    $totalCount++;
    if ($condition) {
        $passedCount++;
        echo "  [PASS] {$description}\n";
    } else {
        echo "  [FAIL] {$description} - {$details}\n";
    }
}

echo "\n======================================================================\n";
echo "   SMART GRAM — GEOGRAPHIC HIERARCHY & ARCHITECTURE VERIFICATION     \n";
echo "======================================================================\n\n";

try {
    $db = Database::getConnection();
} catch (Exception $e) {
    echo "ERROR: Could not connect to database: " . $e->getMessage() . "\n";
    exit(1);
}

// -----------------------------------------------------------------------------
// 1. Database Schema & Tables Verification
// -----------------------------------------------------------------------------
echo "--- 1. Database Relational Tables & Foreign Keys ---\n";

$requiredTables = ['states', 'districts', 'mandals', 'gram_panchayats', 'villages', 'wards'];
foreach ($requiredTables as $table) {
    $stmt = $db->query("SHOW TABLES LIKE '{$table}'");
    assertTest("Table '{$table}' exists in database", $stmt->rowCount() > 0);
}

// Verify foreign key columns on grievances
$gColumns = $db->query("SHOW COLUMNS FROM grievances")->fetchAll(PDO::FETCH_COLUMN);
assertTest("Grievances table has 'state_id' column", in_array('state_id', $gColumns));
assertTest("Grievances table has 'district_id' column", in_array('district_id', $gColumns));
assertTest("Grievances table has 'mandal_id' column", in_array('mandal_id', $gColumns));
assertTest("Grievances table has 'gram_panchayat_id' column", in_array('gram_panchayat_id', $gColumns));
assertTest("Grievances table has 'village_id' column", in_array('village_id', $gColumns));
assertTest("Grievances table has 'ward_id' column", in_array('ward_id', $gColumns));

// Verify foreign key columns on users (officer/staff administrative scope)
$uColumns = $db->query("SHOW COLUMNS FROM users")->fetchAll(PDO::FETCH_COLUMN);
assertTest("Users table has 'location_scope' column", in_array('location_scope', $uColumns));
assertTest("Users table has 'state_id' column", in_array('state_id', $uColumns));
assertTest("Users table has 'district_id' column", in_array('district_id', $uColumns));
assertTest("Users table has 'mandal_id' column", in_array('mandal_id', $uColumns));
assertTest("Users table has 'gram_panchayat_id' column", in_array('gram_panchayat_id', $uColumns));

// -----------------------------------------------------------------------------
// 2. Location Master Data & Dynamic Seeding Verification
// -----------------------------------------------------------------------------
echo "\n--- 2. Location Master Data (Multi-tier Dynamic Hierarchy) ---\n";

$states = Location::getStates(true);
assertTest("Active states found (Dynamic from DB, not hardcoded)", count($states) >= 1);
$ap = null;
foreach ($states as $s) {
    if ($s['code'] === 'AP' || stripos($s['name_en'], 'Andhra Pradesh') !== false) {
        $ap = $s;
        break;
    }
}
assertTest("Andhra Pradesh state exists in database", $ap !== null);

if ($ap) {
    $districts = Location::getDistricts((int)$ap['id'], true);
    assertTest("Districts belonging to Andhra Pradesh retrieved", count($districts) >= 1);

    $asrDistrict = null;
    foreach ($districts as $d) {
        if ($d['code'] === 'ASR' || stripos($d['name_en'], 'Alluri') !== false) {
            $asrDistrict = $d;
            break;
        }
    }
    assertTest("Alluri Sitharama Raju District exists dynamically in database", $asrDistrict !== null);

    if ($asrDistrict) {
        $mandals = Location::getMandals((int)$asrDistrict['id'], true);
        assertTest("Mandals belonging to ASR district retrieved dynamically", count($mandals) >= 4);

        $paderuMandal = null;
        foreach ($mandals as $m) {
            if ($m['code'] === 'ASR-PAD' || stripos($m['name_en'], 'Paderu') !== false) {
                $paderuMandal = $m;
                break;
            }
        }
        assertTest("Paderu Mandal exists in ASR District", $paderuMandal !== null);

        if ($paderuMandal) {
            $panchayats = Location::getGramPanchayats((int)$paderuMandal['id'], true);
            assertTest("Gram Panchayats belonging to Paderu Mandal retrieved", count($panchayats) >= 1);

            $paderuGP = $panchayats[0] ?? null;
            if ($paderuGP) {
                $villages = Location::getVillages((int)$paderuGP['id'], true);
                assertTest("Villages belonging to Gram Panchayat retrieved", count($villages) >= 1);

                // Find a village that has wards
                $foundWards = false;
                foreach ($villages as $v) {
                    $wards = Location::getWards((int)$v['id'], null, true);
                    if (!empty($wards)) {
                        $foundWards = true;
                        break;
                    }
                }
                assertTest("Wards belonging to Village retrieved", $foundWards);
            }
        }
    }
}

// -----------------------------------------------------------------------------
// 3. Upward Hierarchy Resolution Verification
// -----------------------------------------------------------------------------
echo "\n--- 3. Upward Geographic Hierarchy Resolver ---\n";

// Test upward resolution from a village in Paderu (e.g. Paderu Main)
$dbPaderuVillage = $db->query("SELECT id FROM villages WHERE name_en LIKE '%Paderu%' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
if ($dbPaderuVillage) {
    $resolved = Location::resolveHierarchy((int)$dbPaderuVillage['id']);
    assertTest("Resolved hierarchy has correct village_id", (int)$resolved['village_id'] === (int)$dbPaderuVillage['id']);
    assertTest("Resolved hierarchy has non-empty gram_panchayat_id", !empty($resolved['gram_panchayat_id']));
    assertTest("Resolved hierarchy has non-empty mandal_id", !empty($resolved['mandal_id']));
    assertTest("Resolved hierarchy has non-empty district_id", !empty($resolved['district_id']));
    assertTest("Resolved hierarchy has non-empty state_id", !empty($resolved['state_id']));
}

// -----------------------------------------------------------------------------
// 4. Safe Deletion & Dependency Validation
// -----------------------------------------------------------------------------
echo "\n--- 4. Safe Deletion & Dependency Validation ---\n";

// Village 1 has existing grievances, should NOT be deletable
$canDeleteVillage1 = Location::canDelete('village', 1);
assertTest("Village 1 with existing grievances cannot be deleted safely", $canDeleteVillage1['can_delete'] === false);
assertTest("Safe deletion check gives descriptive dependency reason", !empty($canDeleteVillage1['reason']));

// State 1 with districts cannot be deleted safely
$canDeleteState1 = Location::canDelete('state', 1);
assertTest("State 1 with child districts cannot be deleted safely", $canDeleteState1['can_delete'] === false);

// -----------------------------------------------------------------------------
// 5. Admin Location Management CRUD Tests
// -----------------------------------------------------------------------------
echo "\n--- 5. Admin Location Management CRUD ---\n";

// Test adding a temporary village in Paderu GP
$testGpId = (int)$db->query("SELECT id FROM gram_panchayats LIMIT 1")->fetchColumn();
$tempVillageId = Location::saveVillage([
    'gram_panchayat_id' => $testGpId,
    'name_en'           => 'Test Scalable Village',
    'name_te'           => 'పరీక్ష గ్రామం',
    'is_active'         => 1
]);
assertTest("Admin can create a new Village dynamically", $tempVillageId > 0);

// Test updating the village
$updateId = Location::saveVillage([
    'gram_panchayat_id' => $testGpId,
    'name_en'           => 'Test Scalable Village Updated',
    'name_te'           => 'పరీక్ష గ్రామం నవీకరణ',
    'is_active'         => 1
], $tempVillageId);
assertTest("Admin can update an existing Village", $updateId === $tempVillageId);

// Test toggling active/inactive
$toggleRes = Location::toggleActive('village', $tempVillageId);
assertTest("Admin can toggle status (active/inactive)", $toggleRes === true);

// Since no grievances or wards are linked to temp village, safe deletion should succeed
$canDeleteTemp = Location::canDelete('village', $tempVillageId);
assertTest("Newly created temporary village with no dependencies can be deleted", $canDeleteTemp['can_delete'] === true);

$deleteRes = Location::deleteEntity('village', $tempVillageId);
assertTest("Admin can safely delete unlinked location entity", $deleteRes === true);

// -----------------------------------------------------------------------------
// 6. Grievance Submission with Geographic Hierarchy
// -----------------------------------------------------------------------------
echo "\n--- 6. Grievance Creation & Full Geographic Hierarchy Persistence ---\n";

// Create a test grievance using GrievanceService with progressive location fields
$testPaderuVillage = $db->query("SELECT * FROM villages WHERE name_en LIKE '%Paderu%' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
if (!$testPaderuVillage) {
    $testPaderuVillage = $db->query("SELECT * FROM villages LIMIT 1")->fetch(PDO::FETCH_ASSOC);
}
$testWard = $db->query("SELECT * FROM wards WHERE village_id = {$testPaderuVillage['id']} LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$testCategory = $db->query("SELECT * FROM categories LIMIT 1")->fetch(PDO::FETCH_ASSOC);

$createdGrievance = GrievanceService::createGrievance([
    'citizen_name'        => 'Ramesh Geographic Tester',
    'citizen_phone'       => '9848012345',
    'village_id'          => $testPaderuVillage['id'],
    'ward_id'             => $testWard['id'] ?? null,
    'category_id'         => $testCategory['id'],
    'landmark'            => 'Near Paderu RTC Complex',
    'description'         => 'Water supply pipe leakage in main road near Paderu RTC complex.',
    'preferred_language'  => 'te'
]);

assertTest("Grievance submission via GrievanceService succeeded", !empty($createdGrievance['id']));
$testTrackingId = $createdGrievance['tracking_id'] ?? null;
assertTest("Tracking ID generated for grievance", !empty($testTrackingId));

// Retrieve grievance and verify all 6 geographic fields are populated
$gRec = Grievance::findByTrackingId($testTrackingId);
assertTest("Retrieved grievance has state_id populated", !empty($gRec['state_id']));
assertTest("Retrieved grievance has district_id populated", !empty($gRec['district_id']));
assertTest("Retrieved grievance has mandal_id populated", !empty($gRec['mandal_id']));
assertTest("Retrieved grievance has gram_panchayat_id populated", !empty($gRec['gram_panchayat_id']));
assertTest("Retrieved grievance has village_id populated", (int)$gRec['village_id'] === (int)$testPaderuVillage['id']);
assertTest("Retrieved grievance joins state_name_en correctly", !empty($gRec['state_name_en']));
assertTest("Retrieved grievance joins district_name_en correctly", !empty($gRec['district_name_en']));
assertTest("Retrieved grievance joins mandal_name_en correctly", !empty($gRec['mandal_name_en']));
assertTest("Retrieved grievance joins panchayat_name_en correctly", !empty($gRec['panchayat_name_en']));

// -----------------------------------------------------------------------------
// 7. Admin Dashboard Multi-Tier Geographic Filtering
// -----------------------------------------------------------------------------
echo "\n--- 7. Admin Dashboard Multi-Tier Geographic Filtering ---\n";

// Filter by district_id
$districtFilter = Grievance::getFilteredGrievances(['district_id' => $gRec['district_id']]);
assertTest("Dashboard filtering by district_id returns matching records", $districtFilter['total'] >= 1);

// Filter by mandal_id
$mandalFilter = Grievance::getFilteredGrievances(['mandal_id' => $gRec['mandal_id']]);
assertTest("Dashboard filtering by mandal_id returns matching records", $mandalFilter['total'] >= 1);

// Filter by gram_panchayat_id
$gpFilter = Grievance::getFilteredGrievances(['gram_panchayat_id' => $gRec['gram_panchayat_id']]);
assertTest("Dashboard filtering by gram_panchayat_id returns matching records", $gpFilter['total'] >= 1);

// Filter by village_id
$villageFilter = Grievance::getFilteredGrievances(['village_id' => $gRec['village_id']]);
assertTest("Dashboard filtering by village_id returns matching records", $villageFilter['total'] >= 1);

// Filter by nonexistent district
$emptyFilter = Grievance::getFilteredGrievances(['district_id' => 999999]);
assertTest("Dashboard filtering by non-matching district returns 0 records", $emptyFilter['total'] === 0);

// -----------------------------------------------------------------------------
// Summary
// -----------------------------------------------------------------------------
echo "\n======================================================================\n";
echo "                      GEOGRAPHIC SUITE SUMMARY                        \n";
echo "======================================================================\n";
printf("  Total Checks : %d\n", $totalCount);
printf("  Passed       : %d\n", $passedCount);
printf("  Failed       : %d\n", $totalCount - $passedCount);
echo "======================================================================\n";

if ($passedCount === $totalCount) {
    echo "  ALL GEOGRAPHIC HIERARCHY TESTS PASSED WITH 100% SUCCESS!\n\n";
    exit(0);
} else {
    echo "  SOME TESTS FAILED!\n\n";
    exit(1);
}
