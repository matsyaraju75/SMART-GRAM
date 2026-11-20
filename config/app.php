<?php
// ==============================================================================
// Smart Gram (గ్రామ సమస్య పరిష్కార వేదిక) - Application Configuration
// ==============================================================================

// Simple .env parser for beginner-friendly, zero-dependency environment loading
if (!function_exists('loadEnv')) {
    function loadEnv(string $path): void {
        if (!file_exists($path)) {
            return;
        }
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }
            if (str_contains($line, '=')) {
                [$name, $value] = explode('=', $line, 2);
                $name = trim($name);
                $value = trim($value);
                // Strip surrounding quotes
                $value = trim($value, "\"'");
                if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
                    putenv("$name=$value");
                    $_ENV[$name] = $value;
                    $_SERVER[$name] = $value;
                }
            }
        }
    }
}

// Load .env if present
loadEnv(__DIR__ . '/../.env');

return [
    'name' => $_ENV['APP_NAME'] ?? 'Smart Gram',
    'name_te' => 'గ్రామ సమస్య పరిష్కార వేదిక',
    'tagline_te' => 'మీ సమస్యను సులభంగా తెలియజేయండి',
    'env' => $_ENV['APP_ENV'] ?? 'development',
    'debug' => filter_var($_ENV['APP_DEBUG'] ?? true, FILTER_VALIDATE_BOOLEAN),
    'url' => rtrim($_ENV['APP_URL'] ?? 'http://localhost/smart-gram/public', '/'),
    
    // Localization
    'default_lang' => $_ENV['APP_DEFAULT_LANG'] ?? 'te',
    'supported_languages' => [
        'te' => 'తెలుగు',
        'en' => 'English'
    ],
    
    // Internal Roles (NO CITIZEN ROLE IN AUTH)
    'roles' => ['admin', 'staff', 'officer'],
    
    // Grievance Statuses
    'statuses' => [
        'submitted' => ['en' => 'Submitted', 'te' => 'నమోదైంది'],
        'under_review' => ['en' => 'Under Review', 'te' => 'పరిశీలనలో ఉంది'],
        'verified' => ['en' => 'Verified', 'te' => 'ధృవీకరించబడింది'],
        'assigned' => ['en' => 'Assigned', 'te' => 'అప్పగించబడింది'],
        'in_progress' => ['en' => 'In Progress', 'te' => 'పని జరుగుతోంది'],
        'referred' => ['en' => 'Referred', 'te' => 'మరొక విభాగానికి బదిలీ'],
        'resolved' => ['en' => 'Resolved', 'te' => 'పరిష్కరించబడింది'],
        'citizen_verification' => ['en' => 'Citizen Verification', 'te' => 'పౌరుల ధృవీకరణ'],
        'reopened' => ['en' => 'Reopened', 'te' => 'తిరిగి తెరవబడింది'],
        'rejected' => ['en' => 'Rejected', 'te' => 'తిరస్కరించబడింది'],
        'closed' => ['en' => 'Closed', 'te' => 'ముగిసింది']
    ],
    
    // Priorities
    'priorities' => [
        'low' => ['en' => 'Low', 'te' => 'సాధారణం'],
        'medium' => ['en' => 'Medium', 'te' => 'మధ్యస్థం'],
        'high' => ['en' => 'High', 'te' => 'ముఖ్యమైనది'],
        'critical' => ['en' => 'Critical', 'te' => 'అత్యవసరం']
    ],
    
    // Session & Upload Rules
    'session_lifetime' => (int)($_ENV['SESSION_LIFETIME'] ?? 7200),
    'upload_max_mb' => (int)($_ENV['UPLOAD_MAX_FILE_SIZE_MB'] ?? 5),
    'allowed_mime_types' => ['image/jpeg', 'image/png', 'image/webp'],

    // AI Advisory Engine (Human-in-the-Loop)
    'ai' => [
        'enabled' => filter_var($_ENV['AI_ENABLED'] ?? true, FILTER_VALIDATE_BOOLEAN),
        'gemini_api_key' => $_ENV['GEMINI_API_KEY'] ?? '',
        'model' => $_ENV['GEMINI_MODEL'] ?? 'gemini-1.5-flash',
        'timeout_seconds' => 4,
    ],

    // n8n Webhook & Notification Automation (Phase 8)
    'webhook' => [
        'enabled' => filter_var($_ENV['N8N_WEBHOOK_ENABLED'] ?? false, FILTER_VALIDATE_BOOLEAN),
        'url' => $_ENV['N8N_WEBHOOK_URL'] ?? '',
        'secret' => $_ENV['N8N_WEBHOOK_SECRET'] ?? 'smartgram_n8n_secret_token_2026',
        'timeout_seconds' => (int)($_ENV['N8N_TIMEOUT_SECONDS'] ?? 2),
        'simulate_in_dev' => filter_var($_ENV['N8N_SIMULATE_IN_DEV'] ?? true, FILTER_VALIDATE_BOOLEAN),
    ]
];
