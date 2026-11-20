<?php
// ==============================================================================
// Smart Gram - Internal Authentication Controller
// (Staff, Officer, Admin ONLY - NO CITIZEN LOGIN)
// ==============================================================================

namespace App\Controllers;

use App\Core\Session;
use App\Core\RateLimiter;
use App\Models\User;
use App\Models\AuditLog;

class AuthController {
    /**
     * Show the internal login form
     */
    public function showLogin(): void {
        if (auth()) {
            redirect('dashboard');
        }

        view('auth/login', [
            'title' => 'Internal Portal Login - Smart Gram',
            'old_email' => Session::getFlash('old_email')
        ]);
    }

    /**
     * Process internal user login request
     */
    public function login(): void {
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        $ip = RateLimiter::clientIp();
        $rateKey = "login:{$ip}";

        // Anti-Brute-Force Rate Limiting (5 attempts per 60 seconds)
        if (RateLimiter::tooManyAttempts($rateKey, 5)) {
            $seconds = RateLimiter::availableIn($rateKey);
            AuditLog::log('login_rate_limited', 'users', null, null, [
                'attempted_email' => $email,
                'ip' => $ip,
                'retry_after' => $seconds
            ]);
            Session::setFlash('error', "Too many failed login attempts. Please wait {$seconds} seconds before trying again.");
            Session::setFlash('old_email', $email);
            redirect('login');
        }

        // 1. Basic validation
        if (empty($email) || empty($password)) {
            Session::setFlash('error', 'Please provide both email and password.');
            Session::setFlash('old_email', $email);
            redirect('login');
        }

        // 2. Fetch user from database
        $user = User::findByEmail($email);

        // 3. Verify user existence and password hash
        if (!$user || !User::verifyPassword($password, $user['password_hash'])) {
            // Increment rate limiter on failure
            RateLimiter::hit($rateKey, 60);

            // Log failed login attempt for security monitoring
            AuditLog::log('login_failed', 'users', $user['id'] ?? null, null, [
                'attempted_email' => $email
            ]);

            Session::setFlash('error', 'Invalid email or password. Please try again.');
            Session::setFlash('old_email', $email);
            redirect('login');
        }

        // Authentication success: clear rate limit counter
        RateLimiter::clear($rateKey);

        // 4. Prevent Session Fixation: Regenerate session ID upon successful authentication
        Session::regenerate();

        // 5. Store safe user payload in session (exclude password hash)
        unset($user['password_hash']);
        Session::set('user', $user);

        // 6. Record successful login in audit trail
        AuditLog::log('login_success', 'users', $user['id'], $user['id'], [
            'role' => $user['role'],
            'email' => $user['email']
        ]);

        Session::setFlash('success', "Welcome back, {$user['full_name']}!");

        // 7. Route user to their role-specific dashboard
        redirect('dashboard');
    }

    /**
     * Terminate user session and log out
     */
    public function logout(): void {
        $user = auth();

        if ($user) {
            AuditLog::log('logout', 'users', $user['id'], $user['id']);
        }

        // Clear user session & regenerate session ID to prevent fixation
        Session::remove('user');
        Session::regenerate();
        Session::setFlash('success', 'You have been successfully logged out.');

        redirect('login');
    }
}
