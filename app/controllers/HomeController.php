<?php
// ==============================================================================
// Smart Gram - Public Citizen Home Controller (NO CITIZEN LOGIN)
// ==============================================================================

namespace App\Controllers;

use App\Core\Database;
use PDO;

class HomeController {
    /**
     * Display the public homepage for rural citizens
     */
    public function index(): void {
        $db = Database::getConnection();

        // Fetch active categories with department names for public preview
        $categories = $db->query("
            SELECT c.*, d.name_en AS dept_name_en, d.name_te AS dept_name_te
            FROM categories c
            JOIN departments d ON c.department_id = d.id
            WHERE c.is_active = 1
            ORDER BY c.id ASC
            LIMIT 6
        ")->fetchAll(PDO::FETCH_ASSOC);

        // Fetch active village info
        $village = $db->query("SELECT * FROM villages WHERE is_active = 1 LIMIT 1")->fetch(PDO::FETCH_ASSOC);

        view('citizen/home', [
            'title' => __('common.app_title') . ' — ' . __('common.app_name'),
            'categories' => $categories,
            'village' => $village
        ]);
    }

    /**
     * Display the About Smart Gram information page
     */
    public function about(): void {
        view('citizen/about', [
            'title' => __('citizen.about_page_title') . ' — ' . __('common.app_name')
        ]);
    }

    /**
     * Production Health Check Endpoint (JSON)
     * For uptime monitoring, load balancers, and DevOps probes
     */
    public function health(): void {
        header('Content-Type: application/json; charset=utf-8');

        $dbStatus = 'ok';
        $dbLatencyMs = 0;
        try {
            $start = microtime(true);
            $db = Database::getConnection();
            $db->query("SELECT 1")->fetch();
            $dbLatencyMs = round((microtime(true) - $start) * 1000, 2);
        } catch (\Exception $e) {
            $dbStatus = 'error: ' . $e->getMessage();
        }

        $uploadDir = BASE_PATH . '/storage/uploads';
        $storageWritable = is_dir($uploadDir) && is_writable($uploadDir);

        $requiredExtensions = ['pdo', 'pdo_mysql', 'mbstring', 'fileinfo', 'curl', 'json'];
        $missingExtensions = [];
        foreach ($requiredExtensions as $ext) {
            if (!extension_loaded($ext)) {
                $missingExtensions[] = $ext;
            }
        }

        $allHealthy = ($dbStatus === 'ok') && $storageWritable && empty($missingExtensions);

        http_response_code($allHealthy ? 200 : 503);

        echo json_encode([
            'status' => $allHealthy ? 'healthy' : 'degraded',
            'timestamp' => date('c'),
            'app' => [
                'name' => config('name', 'Smart Gram'),
                'env' => config('env', 'production'),
                'php_version' => PHP_VERSION,
            ],
            'checks' => [
                'database' => [
                    'status' => $dbStatus === 'ok' ? 'connected' : 'disconnected',
                    'latency_ms' => $dbLatencyMs
                ],
                'storage' => [
                    'uploads_writable' => $storageWritable,
                    'path' => 'storage/uploads'
                ],
                'extensions' => [
                    'loaded' => empty($missingExtensions),
                    'missing' => $missingExtensions
                ]
            ]
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        exit;
    }
}

