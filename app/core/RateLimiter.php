<?php
// ==============================================================================
// Smart Gram - Security Rate Limiter (Phase 9)
// IP & Key based rate-limiting against brute force, scraping, and spam flooding
// ==============================================================================

namespace App\Core;

use Exception;
use PDO;

class RateLimiter {
    /**
     * Determine if a given key has exceeded its maximum attempts
     */
    public static function tooManyAttempts(string $key, int $maxAttempts): bool {
        return self::attempts($key) >= $maxAttempts;
    }

    /**
     * Increment the counter for a key and set reset timestamp
     *
     * @return int The new hit count
     */
    public static function hit(string $key, int $decaySeconds = 60): int {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("
                INSERT INTO rate_limits (rate_key, hits, reset_at)
                VALUES (:key, 1, DATE_ADD(NOW(), INTERVAL :decay SECOND))
                ON DUPLICATE KEY UPDATE 
                    hits = IF(reset_at < NOW(), 1, hits + 1),
                    reset_at = IF(reset_at < NOW(), DATE_ADD(NOW(), INTERVAL :decay2 SECOND), reset_at)
            ");
            $stmt->execute([
                ':key'    => $key,
                ':decay'  => $decaySeconds,
                ':decay2' => $decaySeconds
            ]);

            return self::attempts($key);
        } catch (Exception $e) {
            error_log("RateLimiter hit error: " . $e->getMessage());
            return 1;
        }
    }

    /**
     * Get the current number of attempts for a key
     */
    public static function attempts(string $key): int {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("
                SELECT hits FROM rate_limits 
                WHERE rate_key = :key AND reset_at >= NOW() 
                LIMIT 1
            ");
            $stmt->execute([':key' => $key]);
            $hits = $stmt->fetchColumn();
            return $hits !== false ? (int)$hits : 0;
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Get the number of remaining attempts
     */
    public static function remaining(string $key, int $maxAttempts): int {
        $attempts = self::attempts($key);
        return max(0, $maxAttempts - $attempts);
    }

    /**
     * Seconds until the key reset time expires
     */
    public static function availableIn(string $key): int {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("
                SELECT TIMESTAMPDIFF(SECOND, NOW(), reset_at) AS diff 
                FROM rate_limits 
                WHERE rate_key = :key AND reset_at >= NOW() 
                LIMIT 1
            ");
            $stmt->execute([':key' => $key]);
            $diff = $stmt->fetchColumn();
            return $diff !== false ? max(1, (int)$diff) : 0;
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Reset/clear the rate limiter for a key
     */
    public static function clear(string $key): void {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("DELETE FROM rate_limits WHERE rate_key = :key");
            $stmt->execute([':key' => $key]);
        } catch (Exception $e) {
            // Silence
        }
    }

    /**
     * Get sanitized client IP address
     */
    public static function clientIp(): string {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        return filter_var($ip, FILTER_VALIDATE_IP) ?: '127.0.0.1';
    }
}
