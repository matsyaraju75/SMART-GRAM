<?php
// ==============================================================================
// Smart Gram - Location-Scoped Role-Based Access Control (RBAC) Test Suite
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
        require $file;
    }
});

use App\Core\Database;
use App\Models\Grievance;
use App\Models\Location;
use App\Models\User;

$db = Database::getConnection();

echo "==============================================================================\n";
echo " Smart Gram - Location-Scoped RBAC Test Suite\n";
echo "==============================================================================\n\n";

$passCount = 0;
$failCount = 0;

function assertCondition(string $name, bool $condition, string $details = ''): void {
    global $passCount, $failCount;
    if ($condition) {
        echo " [PASS] $name\n";
        $passCount++;
    } else {
        echo " [FAIL] $name" . ($details ? " ($details)" : '') . "\n";
        $failCount++;
    }
}

// ------------------------------------------------------------------------------
// Test Setup: Retrieve or seed test geographical entities & grievances
// ------------------------------------------------------------------------------
echo "--- 1. Geographic Entities & Test Grievances Setup ---\n";

$hier1 = Location::resolveHierarchy(1);
assertCondition("Village 1 hierarchy successfully resolved", !empty($hier1) && !empty($hier1['gram_panchayat_id']), 
    "GP ID: " . ($hier1['gram_panchayat_id'] ?? 'none'));

$stateId = (int)$hier1['state_id'];
$dist1Id = (int)$hier1['district_id'];
$mandal1Id = (int)$hier1['mandal_id'];
$gp1Id = (int)$hier1['gram_panchayat_id'];

// Retrieve or create a second DISTINCT district for isolation testing
$dist2 = $db->query("SELECT * FROM districts WHERE id != $dist1Id AND state_id = $stateId LIMIT 1")->fetch(PDO::FETCH_ASSOC);
if (!$dist2) {
    $db->exec("INSERT INTO districts (state_id, code, name_en, name_te, is_active) 
               VALUES ($stateId, 'TEST_DIST2_" . time() . "', 'Vizianagaram Test', 'విజయనగరం టెస్ట్', 1)");
    $dist2Id = (int)$db->lastInsertId();
    $dist2 = $db->query("SELECT * FROM districts WHERE id = $dist2Id")->fetch(PDO::FETCH_ASSOC);
}
assertCondition("Secondary District for isolation testing exists", !empty($dist2) && $dist2['id'] != $dist1Id, 
    "District 2 ID: {$dist2['id']}, District 1 ID: $dist1Id");

// Retrieve or create a second DISTINCT mandal in District 2
$mandal2 = $db->query("SELECT * FROM mandals WHERE district_id = {$dist2['id']} AND id != $mandal1Id LIMIT 1")->fetch(PDO::FETCH_ASSOC);
if (!$mandal2) {
    $db->exec("INSERT INTO mandals (district_id, code, name_en, name_te, is_active) 
               VALUES ({$dist2['id']}, 'MND_EXT_" . time() . "', 'Bhogapuram Test', 'భోగాపురం టెస్ట్', 1)");
    $m2Id = (int)$db->lastInsertId();
    $mandal2 = $db->query("SELECT * FROM mandals WHERE id = $m2Id")->fetch(PDO::FETCH_ASSOC);
}
assertCondition("Secondary Mandal exists in District 2", !empty($mandal2) && $mandal2['id'] != $mandal1Id);

// Retrieve or create a second DISTINCT Gram Panchayat in Mandal 2
$gp2 = $db->query("SELECT * FROM gram_panchayats WHERE mandal_id = {$mandal2['id']} AND id != $gp1Id LIMIT 1")->fetch(PDO::FETCH_ASSOC);
if (!$gp2) {
    $db->exec("INSERT INTO gram_panchayats (mandal_id, code, name_en, name_te, is_active) 
               VALUES ({$mandal2['id']}, 'GP_EXT_" . time() . "', 'Bhogapuram GP Test', 'భోగాపురం GP టెస్ట్', 1)");
    $gp2Id = (int)$db->lastInsertId();
    $gp2 = $db->query("SELECT * FROM gram_panchayats WHERE id = $gp2Id")->fetch(PDO::FETCH_ASSOC);
}
assertCondition("Secondary GP exists in Mandal 2", !empty($gp2) && $gp2['id'] != $gp1Id);

// Retrieve or create a second DISTINCT Village in GP 2
$village2 = $db->query("SELECT * FROM villages WHERE gram_panchayat_id = {$gp2['id']} AND id != 1 LIMIT 1")->fetch(PDO::FETCH_ASSOC);
if (!$village2) {
    $db->exec("INSERT INTO villages (gram_panchayat_id, code, name_en, name_te, mandal, district, is_active) 
               VALUES ({$gp2['id']}, 'VIL_EXT_" . time() . "', 'Savara Village Test', 'సవర గ్రామం టెస్ట్', '{$mandal2['name_en']}', '{$dist2['name_en']}', 1)");
    $v2Id = (int)$db->lastInsertId();
    $village2 = $db->query("SELECT * FROM villages WHERE id = $v2Id")->fetch(PDO::FETCH_ASSOC);
}
assertCondition("Secondary Village exists in GP 2", !empty($village2) && $village2['id'] != 1);

// Create test grievance in Village 1 (District 1, Mandal 1, GP 1)
$trackVil1 = 'TEST-RBAC-V1-' . time();
$db->exec("INSERT INTO grievances (
    tracking_id, citizen_name, citizen_phone, 
    state_id, district_id, mandal_id, gram_panchayat_id, village_id, ward_id,
    category_id, department_id, description, status, priority, created_at
) VALUES (
    '$trackVil1', 'Citizen In V1', '9876543210',
    $stateId, $dist1Id, $mandal1Id, $gp1Id, 1, 1,
    1, 1, 'Drinking water pipeline leakage in V1', 'submitted', 'high', NOW()
)");
$g1Id = (int)$db->lastInsertId();
$grv1 = Grievance::findById($g1Id);
assertCondition("Test Grievance in Village 1 created", !empty($grv1), "ID: $g1Id");

// Create test grievance in Village 2 (District 2, Mandal 2, GP 2)
$trackVil2 = 'TEST-RBAC-V2-' . time();
$db->exec("INSERT INTO grievances (
    tracking_id, citizen_name, citizen_phone, 
    state_id, district_id, mandal_id, gram_panchayat_id, village_id,
    category_id, department_id, description, status, priority, created_at
) VALUES (
    '$trackVil2', 'Citizen In V2', '9876543211',
    $stateId, {$dist2['id']}, {$mandal2['id']}, {$gp2['id']}, {$village2['id']},
    1, 1, 'Water problem in external district village 2', 'submitted', 'medium', NOW()
)");
$g2Id = (int)$db->lastInsertId();
$grv2 = Grievance::findById($g2Id);
assertCondition("Test Grievance in Village 2 created", !empty($grv2), "ID: $g2Id");

// ------------------------------------------------------------------------------
// Test 2: User Location Scope Filters
// ------------------------------------------------------------------------------
echo "\n--- 2. User::getGeographicScopeFilters() Tests ---\n";

$adminUser = ['id' => 1, 'role' => 'admin', 'location_scope' => 'state', 'state_id' => $stateId];
$adminFilters = User::getGeographicScopeFilters($adminUser);
assertCondition("Admin receives unrestricted filters (empty array)", empty($adminFilters), json_encode($adminFilters));

$staffUser = ['id' => 2, 'role' => 'staff', 'location_scope' => 'gram_panchayat', 'gram_panchayat_id' => $gp1Id];
$staffFilters = User::getGeographicScopeFilters($staffUser);
assertCondition("Staff receives gram_panchayat_id filter", 
    isset($staffFilters['gram_panchayat_id']) && $staffFilters['gram_panchayat_id'] === $gp1Id, 
    json_encode($staffFilters));

$officerMandal = ['id' => 3, 'role' => 'officer', 'location_scope' => 'mandal', 'mandal_id' => $mandal1Id];
$mandalFilters = User::getGeographicScopeFilters($officerMandal);
assertCondition("Mandal officer receives mandal_id filter", 
    isset($mandalFilters['mandal_id']) && $mandalFilters['mandal_id'] === $mandal1Id, 
    json_encode($mandalFilters));

$officerDist = ['id' => 4, 'role' => 'officer', 'location_scope' => 'district', 'district_id' => $dist1Id];
$distFilters = User::getGeographicScopeFilters($officerDist);
assertCondition("District officer receives district_id filter", 
    isset($distFilters['district_id']) && $distFilters['district_id'] === $dist1Id, 
    json_encode($distFilters));

// ------------------------------------------------------------------------------
// Test 3: User::canAccessGrievance() Authorization Matrix
// ------------------------------------------------------------------------------
echo "\n--- 3. User::canAccessGrievance() Authorization Matrix ---\n";
echo "Staff User: " . json_encode($staffUser) . "\n";
echo "Grievance 1: ID={$grv1['id']}, GP={$grv1['gram_panchayat_id']}, MND={$grv1['mandal_id']}, DST={$grv1['district_id']}\n";
echo "Grievance 2: ID={$grv2['id']}, GP={$grv2['gram_panchayat_id']}, MND={$grv2['mandal_id']}, DST={$grv2['district_id']}\n";

// A. Admin Global Access
assertCondition("Admin can access Grievance in Village 1", User::canAccessGrievance($adminUser, $grv1));
assertCondition("Admin can access Grievance in Village 2", User::canAccessGrievance($adminUser, $grv2));

// B. Staff GP Scoped Access
assertCondition("Staff (GP 1) CAN access Grievance 1 (in GP 1)", User::canAccessGrievance($staffUser, $grv1));
assertCondition("Staff (GP 1) CANNOT access Grievance 2 (in GP 2)", !User::canAccessGrievance($staffUser, $grv2));

// C. Officer Mandal Scoped Access
$waterOfficerMandal1 = [
    'id' => 50,
    'role' => 'officer',
    'department_id' => 1, // Water Supply
    'location_scope' => 'mandal',
    'mandal_id' => (int)$hier1['mandal_id']
];
assertCondition("Water Officer (Mandal 1) CAN access Water Grievance in Mandal 1", User::canAccessGrievance($waterOfficerMandal1, $grv1));
assertCondition("Water Officer (Mandal 1) CANNOT access Water Grievance in Mandal 2", !User::canAccessGrievance($waterOfficerMandal1, $grv2));

// D. Officer Department Boundary
$elecOfficerMandal1 = [
    'id' => 51,
    'role' => 'officer',
    'department_id' => 2, // Electricity
    'location_scope' => 'mandal',
    'mandal_id' => (int)$hier1['mandal_id']
];
assertCondition("Electricity Officer CANNOT access Water Grievance in same mandal (unassigned)", 
    !User::canAccessGrievance($elecOfficerMandal1, $grv1));

// E. Direct Assignment Overrides Department
$grv1DirectAssigned = array_merge($grv1, ['assigned_to_user_id' => 51]);
assertCondition("Directly assigned officer CAN access grievance even if department differs", 
    User::canAccessGrievance($elecOfficerMandal1, $grv1DirectAssigned));

// F. District Officer Access across Mandals
$waterOfficerDist1 = [
    'id' => 52,
    'role' => 'officer',
    'department_id' => 1,
    'location_scope' => 'district',
    'district_id' => (int)$hier1['district_id']
];
assertCondition("District 1 Officer CAN access Water Grievance in District 1", User::canAccessGrievance($waterOfficerDist1, $grv1));
assertCondition("District 1 Officer CANNOT access Water Grievance in District 2", !User::canAccessGrievance($waterOfficerDist1, $grv2));

// ------------------------------------------------------------------------------
// Test 4: Grievance Analytics & Query Scoping
// ------------------------------------------------------------------------------
echo "\n--- 4. Location-Scoped Analytics & Filtering ---\n";

$analyticsGP1 = Grievance::getAnalyticsSummary($staffFilters);
assertCondition("GP 1 Analytics summary returned successfully", 
    isset($analyticsGP1['total']) && $analyticsGP1['total'] >= 1, 
    "Total in GP1: " . $analyticsGP1['total']);

$filteredGP1 = Grievance::getFilteredGrievances($staffFilters, 10, 0);
$allInGP1 = true;
$mismatchInfo = '';
foreach ($filteredGP1['items'] as $item) {
    if ((int)$item['gram_panchayat_id'] !== (int)$hier1['gram_panchayat_id']) {
        $allInGP1 = false;
        $mismatchInfo = "Item GP=" . ($item['gram_panchayat_id'] ?? 'null') . ", Expected GP={$hier1['gram_panchayat_id']}, VillageID={$item['village_id']}";
        break;
    }
}
assertCondition("Staff query strictly contains only grievances in assigned GP", $allInGP1, 
    "Count: " . count($filteredGP1['items']) . " | " . $mismatchInfo);

// Test with external GP filter
$filteredGP2 = Grievance::getFilteredGrievances(['gram_panchayat_id' => $gp2['id']], 10, 0);
assertCondition("External GP 2 contains Grievance 2 and excludes Grievance 1", 
    $filteredGP2['total'] >= 1 && (int)$filteredGP2['items'][0]['gram_panchayat_id'] === (int)$gp2['id']);

// ------------------------------------------------------------------------------
// Test 5: Eligible Officers Assignment Matching
// ------------------------------------------------------------------------------
echo "\n--- 5. Eligible Officers Assignment Matching ---\n";

// Officers matching village 1
$eligibleForV1 = Grievance::getEligibleOfficers(1, 1, $grv1);
assertCondition("Eligible officers list returned for Village 1", !empty($eligibleForV1));

// Verify that officers in eligible list have scopes matching village 1
$allEligibleValid = true;
foreach ($eligibleForV1 as $off) {
    $scope = $off['location_scope'];
    if ($scope === 'village' && (int)$off['village_id'] !== 1) { $allEligibleValid = false; break; }
    if ($scope === 'gram_panchayat' && (int)$off['gram_panchayat_id'] !== (int)$hier1['gram_panchayat_id']) { $allEligibleValid = false; break; }
    if ($scope === 'mandal' && (int)$off['mandal_id'] !== (int)$hier1['mandal_id']) { $allEligibleValid = false; break; }
    if ($scope === 'district' && (int)$off['district_id'] !== (int)$hier1['district_id']) { $allEligibleValid = false; break; }
}
assertCondition("All returned eligible officers belong to matching jurisdiction", $allEligibleValid);

// ------------------------------------------------------------------------------
// Test 6: Scope Badges Output
// ------------------------------------------------------------------------------
echo "\n--- 6. User Scope Badges ---\n";

$badgeAdmin = User::getScopeBadge($adminUser, 'en');
assertCondition("Admin scope badge rendered", str_contains($badgeAdmin, 'Admin'), $badgeAdmin);

$badgeStaff = User::getScopeBadge(array_merge($staffUser, ['panchayat_name' => 'Ramapuram GP']), 'en');
assertCondition("Staff GP scope badge rendered", str_contains($badgeStaff, 'GP Scope') || str_contains($badgeStaff, 'Ramapuram'), $badgeStaff);

$badgeMandal = User::getScopeBadge(array_merge($officerMandal, ['mandal_name' => 'Paderu Mandal']), 'en');
assertCondition("Mandal officer scope badge rendered", str_contains($badgeMandal, 'Mandal Scope') || str_contains($badgeMandal, 'Paderu'), $badgeMandal);

$badgeDistrict = User::getScopeBadge(array_merge($officerDist, ['district_name' => 'ASR District']), 'te');
assertCondition("Telugu District scope badge rendered", str_contains($badgeDistrict, 'జిల్లా'), $badgeDistrict);

// ------------------------------------------------------------------------------
// Summary
// ------------------------------------------------------------------------------
echo "\n==============================================================================\n";
echo " Test Results: $passCount PASSED, $failCount FAILED\n";
echo "==============================================================================\n";

if ($failCount > 0) {
    exit(1);
}
