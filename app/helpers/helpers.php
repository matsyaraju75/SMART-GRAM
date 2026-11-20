<?php
// ==============================================================================
// Smart Gram - Global Helper Functions
// ==============================================================================

use App\Core\Session;
use App\Core\Csrf;

if (!function_exists('config')) {
    function config(string $key, mixed $default = null): mixed {
        static $appConfig = null;
        if ($appConfig === null) {
            $appConfig = require __DIR__ . '/../../config/app.php';
        }
        return $appConfig[$key] ?? $default;
    }
}

if (!function_exists('url')) {
    function url(string $path = ''): string {
        $baseUrl = config('url');
        return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string {
        return url('assets/' . ltrim($path, '/'));
    }
}

if (!function_exists('redirect')) {
    function redirect(string $path): void {
        header('Location: ' . url($path));
        exit;
    }
}

if (!function_exists('e')) {
    function e(?string $value): string {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string {
        return Csrf::token();
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string {
        return Csrf::field();
    }
}

if (!function_exists('auth')) {
    function auth(): ?array {
        return Session::get('user');
    }
}

if (!function_exists('has_role')) {
    function has_role(string|array $roles): bool {
        $user = auth();
        if (!$user) return false;
        if (is_string($roles)) $roles = [$roles];
        return in_array($user['role'], $roles, true);
    }
}

if (!function_exists('flash')) {
    function flash(string $type): ?string {
        return Session::getFlash($type);
    }
}

if (!function_exists('view')) {
    function view(string $viewPath, array $data = []): void {
        extract($data);
        $fullPath = __DIR__ . '/../../views/' . ltrim($viewPath, '/') . '.php';
        if (!file_exists($fullPath)) {
            die("View template not found: " . htmlspecialchars($viewPath, ENT_QUOTES, 'UTF-8'));
        }
        require $fullPath;
    }
}

if (!function_exists('current_lang')) {
    function current_lang(): string {
        return Session::get('lang', config('default_lang', 'te'));
    }
}

if (!function_exists('switch_lang_url')) {
    function switch_lang_url(string $targetLang): string {
        $queryParams = $_GET;
        $queryParams['lang'] = $targetLang;
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        return $path . '?' . http_build_query($queryParams);
    }
}

// Bilingual translation helper with automatic English fallback
if (!function_exists('__')) {
    function __(string $key, array $replace = []): string {
        static $cache = [];
        $lang = current_lang();
        
        $parts = explode('.', $key, 2);
        $file = $parts[0] ?? 'common';
        $item = $parts[1] ?? $key;

        // Check primary language dictionary
        if (!isset($cache[$lang][$file])) {
            $path = __DIR__ . "/../../lang/{$lang}/{$file}.php";
            $cache[$lang][$file] = file_exists($path) ? require $path : [];
        }

        if (isset($cache[$lang][$file][$item])) {
            $text = $cache[$lang][$file][$item];
            foreach ($replace as $placeholder => $val) {
                $text = str_replace(':' . $placeholder, (string)$val, $text);
            }
            return $text;
        }

        // Fallback to English if not found in primary language
        if ($lang !== 'en') {
            if (!isset($cache['en'][$file])) {
                $enPath = __DIR__ . "/../../lang/en/{$file}.php";
                $cache['en'][$file] = file_exists($enPath) ? require $enPath : [];
            }
            if (isset($cache['en'][$file][$item])) {
                $text = $cache['en'][$file][$item];
                foreach ($replace as $placeholder => $val) {
                    $text = str_replace(':' . $placeholder, (string)$val, $text);
                }
                return $text;
            }
        }

        return $item;
    }
}
