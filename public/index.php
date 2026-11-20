<?php
// ==============================================================================
// Smart Gram (గ్రామ సమస్య పరిష్కార వేదిక) - Front Controller
// All HTTP requests are routed through this single entry point.
// ==============================================================================

declare(strict_types=1);

// Built-in PHP CLI Server router: serve static files directly
if (php_sapi_name() === 'cli-server') {
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
    if ($path !== '/' && file_exists(__DIR__ . $path) && is_file(__DIR__ . $path)) {
        return false;
    }
}

// Security Headers (Defense-in-depth across all endpoints)
if (!headers_sent()) {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
    header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net; font-src 'self' https://fonts.gstatic.com https://cdn.jsdelivr.net data:; img-src 'self' data: https:; connect-src 'self';");
}

// 1. Error Reporting Configuration
error_reporting(E_ALL);
ini_set('display_errors', '1');

// 2. Base Paths
define('BASE_PATH', dirname(__DIR__));

// 3. Autoloader for 'App\' namespace
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

// 4. Load Configuration & Helpers
require_once BASE_PATH . '/config/app.php';
require_once BASE_PATH . '/app/helpers/helpers.php';

// 5. Initialize Secure Session
use App\Core\Session;
use App\Core\Router;
use App\Controllers\HomeController;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\GrievanceController;
use App\Controllers\TrackingController;
use App\Controllers\WorkflowController;
use App\Controllers\ChatbotController;
use App\Controllers\LocationController;
use App\Controllers\WelfareController;
use App\Controllers\ProjectsController;

Session::start();

// 6. Handle Language Selection (Telugu Default / English Toggle)
if (isset($_GET['lang']) && in_array($_GET['lang'], ['te', 'en'], true)) {
    Session::set('lang', $_GET['lang']);
    setcookie('smartgram_lang', $_GET['lang'], time() + (86400 * 365), '/');
} elseif (!Session::has('lang') && isset($_COOKIE['smartgram_lang']) && in_array($_COOKIE['smartgram_lang'], ['te', 'en'], true)) {
    Session::set('lang', $_COOKIE['smartgram_lang']);
}

// 7. Secure Attachment File Serving
$reqPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
// Normalize base path for subdirectories if any
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
if ($scriptDir !== '/' && $scriptDir !== '' && str_starts_with($reqPath, $scriptDir)) {
    $reqPath = substr($reqPath, strlen($scriptDir));
}
if (str_starts_with($reqPath, '/storage/uploads/')) {
    $safeFilename = basename($reqPath);
    $fullUploadPath = BASE_PATH . '/storage/uploads/' . $safeFilename;
    if (file_exists($fullUploadPath) && is_file($fullUploadPath)) {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($fullUploadPath);
        header('Content-Type: ' . $mime);
        header('Content-Length: ' . filesize($fullUploadPath));
        header('X-Content-Type-Options: nosniff');
        header('Cache-Control: public, max-age=86400');
        readfile($fullUploadPath);
        exit;
    }
}

// 8. Router Setup
$router = new Router();

// Public Citizen Routes (NO CITIZEN LOGIN REQUIRED)
$router->get('/', [HomeController::class, 'index']);
$router->get('/home', [HomeController::class, 'index']);
$router->get('/about', [HomeController::class, 'about']);
$router->get('/health', [HomeController::class, 'health']);
$router->get('/welfare', [WelfareController::class, 'index']);
$router->post('/api/welfare/check', [WelfareController::class, 'checkEligibility']);
$router->get('/projects', [ProjectsController::class, 'index']);
$router->get('/console', [DashboardController::class, 'console']);

// Phase 3: Citizen Grievance Submission Flow
$router->get('/submit', [GrievanceController::class, 'showSubmitForm']);
$router->post('/submit', [GrievanceController::class, 'submit']);
$router->get('/submit/success', [GrievanceController::class, 'showSuccess']);
// Progressive Geographic Hierarchy APIs
$router->get('/api/districts', [GrievanceController::class, 'getDistrictsApi']);
$router->get('/api/mandals', [GrievanceController::class, 'getMandalsApi']);
$router->get('/api/panchayats', [GrievanceController::class, 'getPanchayatsApi']);
$router->get('/api/villages', [GrievanceController::class, 'getVillagesApi']);
$router->get('/api/wards', [GrievanceController::class, 'getWardsApi']);

// Phase 4: Grievance Tracking, Timeline & Citizen Feedback / Reopening
$router->get('/track', [TrackingController::class, 'showTrackForm']);
$router->post('/track', [TrackingController::class, 'showTrackForm']);
$router->post('/track/feedback', [TrackingController::class, 'submitFeedback']);

// Phase 10: Smart Gram AI Chatbot Assistant API
$router->post('/api/chatbot', [ChatbotController::class, 'message']);

// Authentication Routes (Internal Staff/Officer/Admin ONLY)
$router->get('/login', [AuthController::class, 'showLogin'], ['guest']);
$router->post('/login', [AuthController::class, 'login'], ['guest']);
$router->post('/logout', [AuthController::class, 'logout'], ['auth']);

// Protected Internal Dashboards
$router->get('/dashboard', [DashboardController::class, 'index'], ['auth']);
$router->get('/dashboard/export', [DashboardController::class, 'export'], ['auth']);
$router->get('/admin', [DashboardController::class, 'admin'], ['auth', 'role:admin']);
$router->get('/staff', [DashboardController::class, 'staff'], ['auth', 'role:staff']);
$router->get('/officer', [DashboardController::class, 'officer'], ['auth', 'role:officer']);

// Geographic Hierarchy: Admin Location Management
$router->get('/admin/locations', [LocationController::class, 'index'], ['auth', 'role:admin']);
$router->post('/admin/locations/store', [LocationController::class, 'store'], ['auth', 'role:admin']);
$router->post('/admin/locations/update', [LocationController::class, 'update'], ['auth', 'role:admin']);
$router->post('/admin/locations/toggle', [LocationController::class, 'toggle'], ['auth', 'role:admin']);
$router->post('/admin/locations/delete', [LocationController::class, 'delete'], ['auth', 'role:admin']);

// Phase 6: Staff & Officer Workflow Actions
$router->get('/grievance/view', [WorkflowController::class, 'show'], ['auth']);
$router->post('/grievance/verify', [WorkflowController::class, 'verify'], ['auth']);
$router->post('/grievance/assign', [WorkflowController::class, 'assign'], ['auth']);
$router->post('/grievance/start-work', [WorkflowController::class, 'startWork'], ['auth']);
$router->post('/grievance/note', [WorkflowController::class, 'addNote'], ['auth']);
$router->post('/grievance/resolve', [WorkflowController::class, 'resolve'], ['auth']);
$router->post('/grievance/reject', [WorkflowController::class, 'reject'], ['auth']);
$router->post('/grievance/refer', [WorkflowController::class, 'refer'], ['auth']);

// Phase 7: AI Advisory Integration (Human-in-the-Loop)
$router->post('/grievance/ai-apply', [WorkflowController::class, 'applyAi'], ['auth']);

// Phase 8: n8n Workflow Automation & Notifications
$router->post('/admin/webhook/test', [DashboardController::class, 'testWebhook'], ['auth', 'role:admin']);
$router->post('/admin/webhook/sla-scan', [DashboardController::class, 'scanSlaBreaches'], ['auth', 'role:admin']);

// 9. Dispatch Request
$router->dispatch();
