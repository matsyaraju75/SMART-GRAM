<?php
// ==============================================================================
// Smart Gram - Secure Session Manager
// ==============================================================================

namespace App\Core;

class Session {
    private static bool $started = false;

    /**
     * Start secure session with hardened cookie parameters
     */
    public static function start(): void {
        if (self::$started || session_status() === PHP_SESSION_ACTIVE) {
            self::$started = true;
            return;
        }

        // Hardened session cookie configuration
        $lifetime = 7200; // 2 hours
        ini_set('session.use_strict_mode', '1');
        ini_set('session.cookie_httponly', '1');
        ini_set('session.cookie_samesite', 'Lax');

        session_set_cookie_params([
            'lifetime' => $lifetime,
            'path' => '/',
            'domain' => '',
            'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
            'httponly' => true,
            'samesite' => 'Lax'
        ]);

        session_start();
        self::$started = true;
    }

    public static function set(string $key, mixed $value): void {
        self::start();
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool {
        self::start();
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void {
        self::start();
        unset($_SESSION[$key]);
    }

    /**
     * Set a flash message or data that disappears on next page load
     */
    public static function setFlash(string $type, mixed $message): void {
        self::start();
        $_SESSION['_flash'][$type] = $message;
    }

    /**
     * Get and clear flash message or data
     */
    public static function getFlash(string $type): mixed {
        self::start();
        if (isset($_SESSION['_flash'][$type])) {
            $msg = $_SESSION['_flash'][$type];
            unset($_SESSION['_flash'][$type]);
            return $msg;
        }
        return null;
    }

    /**
     * Regenerate session ID to prevent Session Fixation attacks
     */
    public static function regenerate(): void {
        self::start();
        session_regenerate_id(true);
    }

    /**
     * Destroy user session (Logout)
     */
    public static function destroy(): void {
        self::start();
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        session_destroy();
        self::$started = false;
    }
}
