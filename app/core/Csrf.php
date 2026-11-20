<?php
// ==============================================================================
// Smart Gram - CSRF (Cross-Site Request Forgery) Guard
// ==============================================================================

namespace App\Core;

class Csrf {
    /**
     * Generate or retrieve active CSRF token for the session
     */
    public static function token(): string {
        Session::start();
        if (!Session::has('_csrf_token')) {
            Session::set('_csrf_token', bin2hex(random_bytes(32)));
        }
        return Session::get('_csrf_token');
    }

    /**
     * Generate HTML hidden input field with CSRF token
     */
    public static function field(): string {
        $token = self::token();
        return '<input type="hidden" name="_csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }

    /**
     * Validate incoming token from POST request
     */
    public static function validate(?string $submittedToken = null): bool {
        Session::start();
        $submittedToken = $submittedToken ?? ($_POST['_csrf_token'] ?? $_POST['csrf_token'] ?? null);
        $storedToken = Session::get('_csrf_token');

        if (!$storedToken || !$submittedToken) {
            return false;
        }

        // Timing attack safe string comparison
        return hash_equals($storedToken, $submittedToken);
    }
}
