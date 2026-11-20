-- ==============================================================================
-- Smart Gram (గ్రామ సమస్య పరిష్కార వేదిక) - Database Schema
-- Target Engine: MySQL 8.0+ / MariaDB 10.5+
-- Collation: utf8mb4_unicode_ci (Supports English + Telugu characters natively)
-- ==============================================================================

SET FOREIGN_KEY_CHECKS = 0;

-- 1. States (e.g., Andhra Pradesh)
CREATE TABLE IF NOT EXISTS `states` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(10) UNIQUE NOT NULL,
    `name_en` VARCHAR(100) NOT NULL,
    `name_te` VARCHAR(100) NOT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Districts (e.g., Alluri Sitharama Raju)
CREATE TABLE IF NOT EXISTS `districts` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `state_id` INT NOT NULL,
    `code` VARCHAR(20) DEFAULT NULL,
    `name_en` VARCHAR(100) NOT NULL,
    `name_te` VARCHAR(100) NOT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_districts_state` FOREIGN KEY (`state_id`) REFERENCES `states` (`id`) ON DELETE RESTRICT,
    INDEX `idx_districts_state` (`state_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Mandals (e.g., Paderu, Araku Valley, Chintapalle, Rampachodavaram)
CREATE TABLE IF NOT EXISTS `mandals` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `district_id` INT NOT NULL,
    `code` VARCHAR(20) DEFAULT NULL,
    `name_en` VARCHAR(100) NOT NULL,
    `name_te` VARCHAR(100) NOT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_mandals_district` FOREIGN KEY (`district_id`) REFERENCES `districts` (`id`) ON DELETE RESTRICT,
    INDEX `idx_mandals_district` (`district_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Gram Panchayats
CREATE TABLE IF NOT EXISTS `gram_panchayats` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `mandal_id` INT NOT NULL,
    `code` VARCHAR(20) DEFAULT NULL,
    `name_en` VARCHAR(100) NOT NULL,
    `name_te` VARCHAR(100) NOT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_gram_panchayats_mandal` FOREIGN KEY (`mandal_id`) REFERENCES `mandals` (`id`) ON DELETE RESTRICT,
    INDEX `idx_gram_panchayats_mandal` (`mandal_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Villages
CREATE TABLE IF NOT EXISTS `villages` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `gram_panchayat_id` INT NULL,
    `name_en` VARCHAR(100) NOT NULL,
    `name_te` VARCHAR(100) NOT NULL,
    `mandal` VARCHAR(100) DEFAULT NULL,
    `district` VARCHAR(100) DEFAULT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_villages_gram_panchayat` FOREIGN KEY (`gram_panchayat_id`) REFERENCES `gram_panchayats` (`id`) ON DELETE RESTRICT,
    INDEX `idx_villages_gram_panchayat` (`gram_panchayat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Wards
CREATE TABLE IF NOT EXISTS `wards` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `village_id` INT NOT NULL,
    `gram_panchayat_id` INT DEFAULT NULL,
    `ward_number` VARCHAR(20) NOT NULL,
    `ward_name` VARCHAR(100) DEFAULT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_wards_village` FOREIGN KEY (`village_id`) REFERENCES `villages` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_wards_panchayat` FOREIGN KEY (`gram_panchayat_id`) REFERENCES `gram_panchayats` (`id`) ON DELETE SET NULL,
    INDEX `idx_wards_village` (`village_id`),
    INDEX `idx_wards_panchayat` (`gram_panchayat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Departments
CREATE TABLE IF NOT EXISTS `departments` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name_en` VARCHAR(100) NOT NULL,
    `name_te` VARCHAR(100) NOT NULL,
    `code` VARCHAR(30) UNIQUE NOT NULL,
    `description` TEXT DEFAULT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Categories
CREATE TABLE IF NOT EXISTS `categories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `department_id` INT NOT NULL,
    `name_en` VARCHAR(100) NOT NULL,
    `name_te` VARCHAR(100) NOT NULL,
    `icon` VARCHAR(50) DEFAULT 'bi-exclamation-circle',
    `sla_hours` INT DEFAULT 48,
    `default_priority` ENUM('low', 'medium', 'high', 'critical') DEFAULT 'medium',
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_categories_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. Users (Staff, Officers, Admins ONLY - NO CITIZEN ACCOUNTS)
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `role` ENUM('admin', 'staff', 'officer') NOT NULL,
    `full_name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(150) UNIQUE NOT NULL,
    `phone` VARCHAR(20) NOT NULL,
    `password_hash` VARCHAR(255) NOT NULL,
    `department_id` INT DEFAULT NULL,
    `village_id` INT DEFAULT NULL,
    `state_id` INT DEFAULT NULL,
    `district_id` INT DEFAULT NULL,
    `mandal_id` INT DEFAULT NULL,
    `gram_panchayat_id` INT DEFAULT NULL,
    `location_scope` ENUM('state', 'district', 'mandal', 'gram_panchayat', 'village') DEFAULT 'village',
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_users_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_users_village` FOREIGN KEY (`village_id`) REFERENCES `villages` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_users_state` FOREIGN KEY (`state_id`) REFERENCES `states` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_users_district` FOREIGN KEY (`district_id`) REFERENCES `districts` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_users_mandal` FOREIGN KEY (`mandal_id`) REFERENCES `mandals` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_users_gram_panchayat` FOREIGN KEY (`gram_panchayat_id`) REFERENCES `gram_panchayats` (`id`) ON DELETE SET NULL,
    INDEX `idx_users_location_scope` (`location_scope`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. Grievances
CREATE TABLE IF NOT EXISTS `grievances` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `tracking_id` VARCHAR(30) UNIQUE NOT NULL,
    `citizen_name` VARCHAR(100) NOT NULL,
    `citizen_phone` VARCHAR(20) NOT NULL,
    `state_id` INT NULL,
    `district_id` INT NULL,
    `mandal_id` INT NULL,
    `gram_panchayat_id` INT NULL,
    `village_id` INT NOT NULL,
    `ward_id` INT DEFAULT NULL,
    `category_id` INT NOT NULL,
    `department_id` INT NOT NULL,
    `description` TEXT NOT NULL,
    `ai_summary` TEXT DEFAULT NULL,
    `landmark` VARCHAR(255) DEFAULT NULL,
    `latitude` DECIMAL(10, 8) DEFAULT NULL,
    `longitude` DECIMAL(11, 8) DEFAULT NULL,
    `priority` ENUM('low', 'medium', 'high', 'critical') DEFAULT 'medium',
    `ai_suggested_priority` VARCHAR(20) DEFAULT NULL,
    `ai_confidence` DECIMAL(3, 2) DEFAULT NULL,
    `ai_duplicate_of_id` INT DEFAULT NULL,
    `status` ENUM(
        'submitted',
        'under_review',
        'verified',
        'assigned',
        'in_progress',
        'referred',
        'resolved',
        'citizen_verification',
        'reopened',
        'rejected',
        'closed'
    ) DEFAULT 'submitted',
    `assigned_to_user_id` INT DEFAULT NULL,
    `sla_due_at` DATETIME DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_grievances_phone` (`citizen_phone`),
    INDEX `idx_grievances_status` (`status`),
    INDEX `idx_grievances_state` (`state_id`),
    INDEX `idx_grievances_district` (`district_id`),
    INDEX `idx_grievances_mandal` (`mandal_id`),
    INDEX `idx_grievances_panchayat` (`gram_panchayat_id`),
    INDEX `idx_grievances_village` (`village_id`),
    INDEX `idx_grievances_ward` (`ward_id`),
    INDEX `idx_grievances_category` (`category_id`),
    CONSTRAINT `fk_grievances_state` FOREIGN KEY (`state_id`) REFERENCES `states` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_grievances_district` FOREIGN KEY (`district_id`) REFERENCES `districts` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_grievances_mandal` FOREIGN KEY (`mandal_id`) REFERENCES `mandals` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_grievances_gram_panchayat` FOREIGN KEY (`gram_panchayat_id`) REFERENCES `gram_panchayats` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_grievances_village` FOREIGN KEY (`village_id`) REFERENCES `villages` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_grievances_ward` FOREIGN KEY (`ward_id`) REFERENCES `wards` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_grievances_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_grievances_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_grievances_assigned_user` FOREIGN KEY (`assigned_to_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. Attachments
CREATE TABLE IF NOT EXISTS `attachments` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `grievance_id` INT NOT NULL,
    `file_path` VARCHAR(255) NOT NULL,
    `original_name` VARCHAR(255) NOT NULL,
    `file_size` INT NOT NULL,
    `mime_type` VARCHAR(100) NOT NULL,
    `attachment_type` ENUM('citizen_photo', 'resolution_evidence') DEFAULT 'citizen_photo',
    `uploaded_by_user_id` INT DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_attachments_grievance` FOREIGN KEY (`grievance_id`) REFERENCES `grievances` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_attachments_user` FOREIGN KEY (`uploaded_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. Grievance Updates (Audit timeline)
CREATE TABLE IF NOT EXISTS `grievance_updates` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `grievance_id` INT NOT NULL,
    `user_id` INT DEFAULT NULL,
    `action` VARCHAR(50) NOT NULL,
    `from_status` VARCHAR(30) DEFAULT NULL,
    `to_status` VARCHAR(30) NOT NULL,
    `remarks` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_updates_grievance` FOREIGN KEY (`grievance_id`) REFERENCES `grievances` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_updates_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 13. Feedback (Post-resolution citizen rating)
CREATE TABLE IF NOT EXISTS `feedback` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `grievance_id` INT UNIQUE NOT NULL,
    `is_resolved_satisfied` TINYINT(1) NOT NULL,
    `rating` TINYINT DEFAULT NULL,
    `citizen_comment` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_feedback_grievance` FOREIGN KEY (`grievance_id`) REFERENCES `grievances` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 14. Audit Logs
CREATE TABLE IF NOT EXISTS `audit_logs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT DEFAULT NULL,
    `action` VARCHAR(100) NOT NULL,
    `entity` VARCHAR(50) NOT NULL,
    `entity_id` INT DEFAULT NULL,
    `ip_address` VARCHAR(45) NOT NULL,
    `user_agent` VARCHAR(255) DEFAULT NULL,
    `metadata` JSON DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_audit_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 15. Webhook Logs (Phase 8 n8n Automation & Notifications)
CREATE TABLE IF NOT EXISTS `webhook_logs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `event_name` VARCHAR(64) NOT NULL,
    `entity_type` VARCHAR(64) NOT NULL DEFAULT 'grievances',
    `entity_id` INT NULL,
    `webhook_url` VARCHAR(500) NOT NULL,
    `payload` JSON NOT NULL,
    `http_status` INT NULL,
    `response_body` TEXT NULL,
    `status` ENUM('success', 'failed', 'skipped', 'simulated') NOT NULL DEFAULT 'skipped',
    `execution_time_ms` DECIMAL(8,2) NULL,
    `error_message` VARCHAR(500) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_webhook_event` (`event_name`),
    INDEX `idx_webhook_entity` (`entity_type`, `entity_id`),
    INDEX `idx_webhook_status` (`status`),
    INDEX `idx_webhook_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 16. Rate Limits (Phase 9 Security Hardening)
CREATE TABLE IF NOT EXISTS `rate_limits` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `rate_key` VARCHAR(128) NOT NULL,
    `hits` INT NOT NULL DEFAULT 1,
    `reset_at` TIMESTAMP NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uq_rate_key` (`rate_key`),
    INDEX `idx_rate_reset` (`reset_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
