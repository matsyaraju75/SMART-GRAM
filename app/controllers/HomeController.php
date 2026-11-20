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

        // Live Village Operational Metrics (demo data)
        $stats = [
            'total_grievances' => '1,248',
            'resolution_rate'  => '94.2%',
            'pending_issues'   => '18',
            'families_served'  => '620+',
        ];

        // Service categories for the counter services grid
        $serviceCategories = [
            ['icon' => 'bi-droplet-half',          'color' => 'primary',   'title_key' => 'home_svc_water_title',       'desc_key' => 'home_svc_water_desc',       'sla' => '48h'],
            ['icon' => 'bi-lightning-charge-fill',  'color' => 'warning',   'title_key' => 'home_svc_electric_title',    'desc_key' => 'home_svc_electric_desc',    'sla' => '24h'],
            ['icon' => 'bi-moisture',               'color' => 'info',      'title_key' => 'home_svc_drainage_title',    'desc_key' => 'home_svc_drainage_desc',    'sla' => '72h'],
            ['icon' => 'bi-signpost-split-fill',    'color' => 'secondary', 'title_key' => 'home_svc_road_title',        'desc_key' => 'home_svc_road_desc',        'sla' => '7d'],
            ['icon' => 'bi-trash3-fill',            'color' => 'success',   'title_key' => 'home_svc_sanitation_title',  'desc_key' => 'home_svc_sanitation_desc',  'sla' => '24h'],
            ['icon' => 'bi-heart-pulse-fill',       'color' => 'danger',    'title_key' => 'home_svc_health_title',      'desc_key' => 'home_svc_health_desc',      'sla' => '48h'],
            ['icon' => 'bi-building',               'color' => 'dark',      'title_key' => 'home_svc_building_title',    'desc_key' => 'home_svc_building_desc',    'sla' => '15d'],
            ['icon' => 'bi-hospital',               'color' => 'primary',   'title_key' => 'home_svc_clinic_title',      'desc_key' => 'home_svc_clinic_desc',      'sla' => '48h'],
            ['icon' => 'bi-flower1',                'color' => 'success',   'title_key' => 'home_svc_agriculture_title', 'desc_key' => 'home_svc_agriculture_desc', 'sla' => '7d'],
        ];

        // Recent Gram Sabha resolutions (demo data)
        $recentResolutions = [
            ['date' => '2026-11-15', 'number' => 'GS/RES/2026/042', 'subject_key' => 'home_res_sub1', 'status' => 'approved'],
            ['date' => '2026-11-10', 'number' => 'GS/RES/2026/041', 'subject_key' => 'home_res_sub2', 'status' => 'pending'],
            ['date' => '2026-11-02', 'number' => 'GS/RES/2026/040', 'subject_key' => 'home_res_sub3', 'status' => 'approved'],
            ['date' => '2026-10-25', 'number' => 'GS/RES/2026/039', 'subject_key' => 'home_res_sub4', 'status' => 'sanctioned'],
            ['date' => '2026-10-18', 'number' => 'GS/RES/2026/038', 'subject_key' => 'home_res_sub5', 'status' => 'approved'],
        ];

        view('citizen/home', [
            'title'             => __('common.app_title') . ' — ' . __('common.app_name'),
            'categories'        => $categories,
            'village'           => $village,
            'stats'             => $stats,
            'serviceCategories' => $serviceCategories,
            'recentResolutions' => $recentResolutions,
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
