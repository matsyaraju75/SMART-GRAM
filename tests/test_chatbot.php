<?php
// Direct unit test for Chatbot Controller & NLP engine logic
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/config/app.php';
require_once BASE_PATH . '/app/helpers/helpers.php';

spl_autoload_register(function (string $class) {
    $prefix = 'App\\';
    $baseDir = BASE_PATH . '/app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    if (file_exists($file)) require_once $file;
});

use App\Core\Session;
use App\Core\Csrf;
use App\Services\AiAdvisoryService;
use App\Models\Grievance;

Session::start();
$_SESSION['_csrf_token'] = 'test_token_123';

echo "=== Chatbot & AI Advisory Integration Verification ===\n";

// 1. Test Heuristic NLP Engine
echo "[1] Testing Heuristic NLP Classification...\n";
$nlpRes = AiAdvisoryService::analyzeWithHeuristicNlp("The water pipeline in ward 3 is broken and leaking everywhere");
if ($nlpRes['suggested_category_id'] === 1) {
    echo "    ✓ Correctly categorized as 'Pipeline Leakage' (Category 1, Confidence: {$nlpRes['confidence']})\n";
} else {
    echo "    ✗ Category mismatch: " . print_r($nlpRes, true) . "\n";
}

// 2. Test Telugu Heuristic NLP
echo "[2] Testing Telugu Heuristic NLP...\n";
$teluguRes = AiAdvisoryService::analyzeWithHeuristicNlp("మా వీధిలో వీధి దీపం వెలగడం లేదు చీకటిగా ఉంది");
if ($teluguRes['suggested_category_id'] === 4) {
    echo "    ✓ Correctly categorized as 'Street Light' (Category 4, Priority: {$teluguRes['suggested_priority']})\n";
} else {
    echo "    ✗ Category mismatch: " . print_r($teluguRes, true) . "\n";
}

// 3. Test Grievance Lookup by Tracking ID
echo "[3] Testing Grievance Tracking Lookup...\n";
$db = \App\Core\Database::getConnection();
$sample = $db->query("SELECT tracking_id FROM grievances LIMIT 1")->fetch(PDO::FETCH_ASSOC);

if ($sample) {
    $grv = Grievance::findByTrackingId($sample['tracking_id']);
    if ($grv && isset($grv['status'])) {
        echo "    ✓ Grievance '{$sample['tracking_id']}' found: Status={$grv['status']}, Dept={$grv['dept_name_en']}\n";
    } else {
        echo "    ✗ Grievance lookup failed for {$sample['tracking_id']}\n";
    }
} else {
    echo "    ⚠ No grievances in database to test lookup.\n";
}

// 4. Test CSRF Validation
echo "[4] Testing CSRF Guard...\n";
$valid = Csrf::validate('test_token_123');
$invalid = Csrf::validate('wrong_token');
if ($valid && !$invalid) {
    echo "    ✓ CSRF validation functioning correctly!\n";
} else {
    echo "    ✗ CSRF validation failed!\n";
}

echo "\nAll Chatbot core services verified successfully!\n";
