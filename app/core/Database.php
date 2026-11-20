<?php
// ==============================================================================
// Smart Gram - Database Connection Manager (PDO Singleton)
// ==============================================================================

namespace App\Core;

use PDO;
use PDOException;

class Database {
    private static ?PDO $instance = null;

    /**
     * Get the active PDO database connection instance.
     * Implements the Singleton pattern so only ONE connection is opened per request.
     */
    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $config = require __DIR__ . '/../../config/database.php';

            $dsn = sprintf(
                "%s:host=%s;port=%s;dbname=%s;charset=%s",
                $config['driver'],
                $config['host'],
                $config['port'],
                $config['database'],
                $config['charset']
            );

            try {
                self::$instance = new PDO(
                    $dsn,
                    $config['username'],
                    $config['password'],
                    $config['options']
                );
            } catch (PDOException $e) {
                // If in development mode, provide a friendly explanation for beginners
                $isDev = ($_ENV['APP_ENV'] ?? 'development') === 'development';
                
                if ($isDev) {
                    $msg = "<div style='font-family:sans-serif; padding:20px; background:#fff3cd; color:#856404; border:1px solid #ffeeba; border-radius:8px; margin:20px;'>";
                    $msg .= "<h3>⚠️ Database Connection Error</h3>";
                    $msg .= "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "</p>";
                    $msg .= "<p><strong>Troubleshooting Tips:</strong></p>";
                    $msg .= "<ul>";
                    $msg .= "<li>Ensure MySQL service is started (e.g. in XAMPP Control Panel or Windows Services).</li>";
                    $msg .= "<li>Ensure the database <code>" . htmlspecialchars($config['database']) . "</code> exists.</li>";
                    $msg .= "<li>Check credentials in your <code>.env</code> file (default host: 127.0.0.1, user: root, password: empty).</li>";
                    $msg .= "<li>Run the migration script: <code>php database/migrate.php</code></li>";
                    $msg .= "</ul>";
                    $msg .= "</div>";
                    die($msg);
                } else {
                    error_log("Database connection error: " . $e->getMessage());
                    die("A system error occurred. Please contact the Panchayat administrator.");
                }
            }
        }

        return self::$instance;
    }
}
