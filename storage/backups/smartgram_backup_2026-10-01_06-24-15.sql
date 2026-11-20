-- ============================================================
-- Smart Gram Database Backup
-- Generated at: 2026-10-01 06:24:15
-- Tables: 16
-- ============================================================

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
SET time_zone = '+00:00';

-- ------------------------------------------------------------
-- Structure for table `attachments`
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `attachments`;
CREATE TABLE `attachments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `grievance_id` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `file_size` int(11) NOT NULL,
  `mime_type` varchar(100) NOT NULL,
  `attachment_type` enum('citizen_photo','resolution_evidence') DEFAULT 'citizen_photo',
  `uploaded_by_user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_attachments_grievance` (`grievance_id`),
  KEY `fk_attachments_user` (`uploaded_by_user_id`),
  CONSTRAINT `fk_attachments_grievance` FOREIGN KEY (`grievance_id`) REFERENCES `grievances` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_attachments_user` FOREIGN KEY (`uploaded_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `attachments` (3 rows)
INSERT INTO `attachments` (`id`, `grievance_id`, `file_path`, `original_name`, `file_size`, `mime_type`, `attachment_type`, `uploaded_by_user_id`, `created_at`) VALUES ('1', '4', 'storage/uploads/8790400dd2bd84302baa8589df4e7f700c941c6e846b46d4e392c915219e56cd.png', 'problem_photo.png', '70', 'image/png', 'citizen_photo', NULL, '2026-10-01 11:52:13');
INSERT INTO `attachments` (`id`, `grievance_id`, `file_path`, `original_name`, `file_size`, `mime_type`, `attachment_type`, `uploaded_by_user_id`, `created_at`) VALUES ('2', '17', 'storage/uploads/2816cb835a9e6ac93359cf78aaed06d65704df626eac424e70e9b955ea365016.jpg', 'test_evidence_photo.jpg', '134', 'image/jpeg', 'resolution_evidence', '3', '2026-10-01 11:53:17');
INSERT INTO `attachments` (`id`, `grievance_id`, `file_path`, `original_name`, `file_size`, `mime_type`, `attachment_type`, `uploaded_by_user_id`, `created_at`) VALUES ('3', '24', 'storage/uploads/017d2e51aa3fa561de0d8ba2759d59a1b24f85e4e20c1ad9eed5d7cc6320eb7b.jpg', 'test_evidence_e2e.jpg', '134', 'image/jpeg', 'resolution_evidence', '3', '2026-10-01 11:54:08');

-- ------------------------------------------------------------
-- Structure for table `audit_logs`
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `audit_logs`;
CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `entity` varchar(50) NOT NULL,
  `entity_id` int(11) DEFAULT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_audit_user` (`user_id`),
  CONSTRAINT `fk_audit_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `audit_logs` (54 rows)
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `ip_address`, `user_agent`, `metadata`, `created_at`) VALUES ('1', NULL, 'citizen_grievance_submitted', 'grievances', '2', '127.0.0.1', 'CLI', '{\"tracking_id\":\"GRV-2026-00001\",\"category_id\":1,\"village_id\":2}', '2026-10-01 11:49:59');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `ip_address`, `user_agent`, `metadata`, `created_at`) VALUES ('2', NULL, 'login_failed', 'users', '1', '127.0.0.1', 'CLI', '{\"attempted_email\":\"admin@smartgram.local\"}', '2026-10-01 11:51:54');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `ip_address`, `user_agent`, `metadata`, `created_at`) VALUES ('3', '1', 'login_success', 'users', '1', '127.0.0.1', 'CLI', '{\"role\":\"admin\",\"email\":\"admin@smartgram.local\"}', '2026-10-01 11:51:54');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `ip_address`, `user_agent`, `metadata`, `created_at`) VALUES ('4', '2', 'login_success', 'users', '2', '127.0.0.1', 'CLI', '{\"role\":\"staff\",\"email\":\"staff@smartgram.local\"}', '2026-10-01 11:51:54');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `ip_address`, `user_agent`, `metadata`, `created_at`) VALUES ('5', '3', 'login_success', 'users', '3', '127.0.0.1', 'CLI', '{\"role\":\"officer\",\"email\":\"officer@smartgram.local\"}', '2026-10-01 11:51:55');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `ip_address`, `user_agent`, `metadata`, `created_at`) VALUES ('6', '3', 'logout', 'users', '3', '127.0.0.1', 'CLI', NULL, '2026-10-01 11:51:55');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `ip_address`, `user_agent`, `metadata`, `created_at`) VALUES ('7', NULL, 'citizen_grievance_submitted', 'grievances', '3', '127.0.0.1', 'CLI', '{\"tracking_id\":\"GRV-2026-00002\",\"category_id\":1,\"village_id\":1}', '2026-10-01 11:52:13');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `ip_address`, `user_agent`, `metadata`, `created_at`) VALUES ('8', NULL, 'citizen_grievance_submitted', 'grievances', '4', '127.0.0.1', 'CLI', '{\"tracking_id\":\"GRV-2026-00003\",\"category_id\":4,\"village_id\":1}', '2026-10-01 11:52:13');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `ip_address`, `user_agent`, `metadata`, `created_at`) VALUES ('9', NULL, 'citizen_feedback', 'grievances', '5', '127.0.0.1', 'CLI', '{\"tracking_id\":\"GRV-2026-90001\",\"is_satisfied\":true,\"rating\":5}', '2026-10-01 11:52:23');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `ip_address`, `user_agent`, `metadata`, `created_at`) VALUES ('10', NULL, 'reopen', 'grievances', '6', '127.0.0.1', 'CLI', '{\"tracking_id\":\"GRV-2026-90002\",\"is_satisfied\":false,\"rating\":null}', '2026-10-01 11:52:23');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `ip_address`, `user_agent`, `metadata`, `created_at`) VALUES ('11', '1', 'login_success', 'users', '1', '127.0.0.1', 'CLI', '{\"role\":\"admin\",\"email\":\"admin@smartgram.local\"}', '2026-10-01 11:52:30');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `ip_address`, `user_agent`, `metadata`, `created_at`) VALUES ('12', '1', 'login_success', 'users', '1', '127.0.0.1', 'CLI', '{\"role\":\"admin\",\"email\":\"admin@smartgram.local\"}', '2026-10-01 11:53:08');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `ip_address`, `user_agent`, `metadata`, `created_at`) VALUES ('13', '1', 'logout', 'users', '1', '127.0.0.1', 'CLI', NULL, '2026-10-01 11:53:09');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `ip_address`, `user_agent`, `metadata`, `created_at`) VALUES ('14', '2', 'login_success', 'users', '2', '127.0.0.1', 'CLI', '{\"role\":\"staff\",\"email\":\"staff@smartgram.local\"}', '2026-10-01 11:53:09');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `ip_address`, `user_agent`, `metadata`, `created_at`) VALUES ('15', '2', 'login_success', 'users', '2', '127.0.0.1', 'CLI', '{\"role\":\"staff\",\"email\":\"staff@smartgram.local\"}', '2026-10-01 11:53:16');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `ip_address`, `user_agent`, `metadata`, `created_at`) VALUES ('16', '2', 'staff_verified', 'grievances', '17', '127.0.0.1', 'CLI', '{\"tracking_id\":\"GRV-2026-66903\"}', '2026-10-01 11:53:16');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `ip_address`, `user_agent`, `metadata`, `created_at`) VALUES ('17', '2', 'staff_assigned', 'grievances', '17', '127.0.0.1', 'CLI', '{\"tracking_id\":\"GRV-2026-66903\",\"assigned_to\":3,\"officer_name\":\"Water Works Field Officer\"}', '2026-10-01 11:53:17');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `ip_address`, `user_agent`, `metadata`, `created_at`) VALUES ('18', '3', 'login_success', 'users', '3', '127.0.0.1', 'CLI', '{\"role\":\"officer\",\"email\":\"officer@smartgram.local\"}', '2026-10-01 11:53:17');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `ip_address`, `user_agent`, `metadata`, `created_at`) VALUES ('19', '3', 'officer_started_work', 'grievances', '17', '127.0.0.1', 'CLI', '{\"tracking_id\":\"GRV-2026-66903\"}', '2026-10-01 11:53:17');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `ip_address`, `user_agent`, `metadata`, `created_at`) VALUES ('20', '3', 'progress_note', 'grievances', '17', '127.0.0.1', 'CLI', '{\"tracking_id\":\"GRV-2026-66903\"}', '2026-10-01 11:53:17');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `ip_address`, `user_agent`, `metadata`, `created_at`) VALUES ('21', '3', 'officer_resolved', 'grievances', '17', '127.0.0.1', 'CLI', '{\"tracking_id\":\"GRV-2026-66903\",\"has_evidence\":true}', '2026-10-01 11:53:17');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `ip_address`, `user_agent`, `metadata`, `created_at`) VALUES ('22', '2', 'login_success', 'users', '2', '127.0.0.1', 'CLI', '{\"role\":\"staff\",\"email\":\"staff@smartgram.local\"}', '2026-10-01 11:53:17');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `ip_address`, `user_agent`, `metadata`, `created_at`) VALUES ('23', '2', 'staff_rejected', 'grievances', '18', '127.0.0.1', 'CLI', '{\"tracking_id\":\"GRV-2026-99633\",\"reason\":\"Duplicate submission with invalid contact number.\"}', '2026-10-01 11:53:17');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `ip_address`, `user_agent`, `metadata`, `created_at`) VALUES ('25', '2', 'login_success', 'users', '2', '127.0.0.1', 'CLI', '{\"role\":\"staff\",\"email\":\"staff@smartgram.local\"}', '2026-10-01 11:53:25');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `ip_address`, `user_agent`, `metadata`, `created_at`) VALUES ('27', '1', 'login_success', 'users', '1', '127.0.0.1', 'CLI', '{\"role\":\"admin\",\"email\":\"admin@smartgram.local\"}', '2026-10-01 11:53:30');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `ip_address`, `user_agent`, `metadata`, `created_at`) VALUES ('28', NULL, 'login_failed', 'users', '2', '127.0.0.1', 'CLI', '{\"attempted_email\":\"staff@smartgram.local\"}', '2026-10-01 11:53:36');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `ip_address`, `user_agent`, `metadata`, `created_at`) VALUES ('29', NULL, 'login_failed', 'users', '2', '127.0.0.1', 'CLI', '{\"attempted_email\":\"staff@smartgram.local\"}', '2026-10-01 11:53:36');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `ip_address`, `user_agent`, `metadata`, `created_at`) VALUES ('30', NULL, 'login_failed', 'users', '2', '127.0.0.1', 'CLI', '{\"attempted_email\":\"staff@smartgram.local\"}', '2026-10-01 11:53:37');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `ip_address`, `user_agent`, `metadata`, `created_at`) VALUES ('31', NULL, 'login_failed', 'users', '2', '127.0.0.1', 'CLI', '{\"attempted_email\":\"staff@smartgram.local\"}', '2026-10-01 11:53:37');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `ip_address`, `user_agent`, `metadata`, `created_at`) VALUES ('32', NULL, 'login_failed', 'users', '2', '127.0.0.1', 'CLI', '{\"attempted_email\":\"staff@smartgram.local\"}', '2026-10-01 11:53:37');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `ip_address`, `user_agent`, `metadata`, `created_at`) VALUES ('33', NULL, 'login_rate_limited', 'users', NULL, '127.0.0.1', 'CLI', '{\"attempted_email\":\"staff@smartgram.local\",\"ip\":\"127.0.0.1\",\"retry_after\":59}', '2026-10-01 11:53:37');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `ip_address`, `user_agent`, `metadata`, `created_at`) VALUES ('34', NULL, 'citizen_grievance_submitted', 'grievances', '22', '127.0.0.1', 'CLI', '{\"tracking_id\":\"GRV-2026-99634\",\"category_id\":1,\"village_id\":1}', '2026-10-01 11:53:37');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `ip_address`, `user_agent`, `metadata`, `created_at`) VALUES ('35', '2', 'login_success', 'users', '2', '127.0.0.1', 'CLI', '{\"role\":\"staff\",\"email\":\"staff@smartgram.local\"}', '2026-10-01 11:53:37');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `ip_address`, `user_agent`, `metadata`, `created_at`) VALUES ('36', '1', 'login_success', 'users', '1', '127.0.0.1', 'CLI', '{\"role\":\"admin\",\"email\":\"admin@smartgram.local\"}', '2026-10-01 11:53:38');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `ip_address`, `user_agent`, `metadata`, `created_at`) VALUES ('37', '3', 'login_success', 'users', '3', '127.0.0.1', 'CLI', '{\"role\":\"officer\",\"email\":\"officer@smartgram.local\"}', '2026-10-01 11:53:38');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `ip_address`, `user_agent`, `metadata`, `created_at`) VALUES ('38', NULL, 'login_failed', 'users', '2', '127.0.0.1', 'CLI', '{\"attempted_email\":\"staff@smartgram.local\"}', '2026-10-01 11:54:05');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `ip_address`, `user_agent`, `metadata`, `created_at`) VALUES ('39', NULL, 'login_failed', 'users', '2', '127.0.0.1', 'CLI', '{\"attempted_email\":\"staff@smartgram.local\"}', '2026-10-01 11:54:05');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `ip_address`, `user_agent`, `metadata`, `created_at`) VALUES ('40', NULL, 'login_failed', 'users', '2', '127.0.0.1', 'CLI', '{\"attempted_email\":\"staff@smartgram.local\"}', '2026-10-01 11:54:06');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `ip_address`, `user_agent`, `metadata`, `created_at`) VALUES ('41', NULL, 'login_failed', 'users', '2', '127.0.0.1', 'CLI', '{\"attempted_email\":\"staff@smartgram.local\"}', '2026-10-01 11:54:06');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `ip_address`, `user_agent`, `metadata`, `created_at`) VALUES ('42', NULL, 'login_failed', 'users', '2', '127.0.0.1', 'CLI', '{\"attempted_email\":\"staff@smartgram.local\"}', '2026-10-01 11:54:06');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `ip_address`, `user_agent`, `metadata`, `created_at`) VALUES ('43', NULL, 'login_rate_limited', 'users', NULL, '127.0.0.1', 'CLI', '{\"attempted_email\":\"staff@smartgram.local\",\"ip\":\"127.0.0.1\",\"retry_after\":59}', '2026-10-01 11:54:06');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `ip_address`, `user_agent`, `metadata`, `created_at`) VALUES ('44', NULL, 'citizen_grievance_submitted', 'grievances', '23', '127.0.0.1', 'CLI', '{\"tracking_id\":\"GRV-2026-99635\",\"category_id\":1,\"village_id\":1}', '2026-10-01 11:54:06');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `ip_address`, `user_agent`, `metadata`, `created_at`) VALUES ('45', '2', 'login_success', 'users', '2', '127.0.0.1', 'CLI', '{\"role\":\"staff\",\"email\":\"staff@smartgram.local\"}', '2026-10-01 11:54:06');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `ip_address`, `user_agent`, `metadata`, `created_at`) VALUES ('46', '1', 'login_success', 'users', '1', '127.0.0.1', 'CLI', '{\"role\":\"admin\",\"email\":\"admin@smartgram.local\"}', '2026-10-01 11:54:07');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `ip_address`, `user_agent`, `metadata`, `created_at`) VALUES ('47', '3', 'login_success', 'users', '3', '127.0.0.1', 'CLI', '{\"role\":\"officer\",\"email\":\"officer@smartgram.local\"}', '2026-10-01 11:54:07');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `ip_address`, `user_agent`, `metadata`, `created_at`) VALUES ('48', NULL, 'citizen_grievance_submitted', 'grievances', '24', '127.0.0.1', 'CLI', '{\"tracking_id\":\"GRV-2026-99636\",\"category_id\":1,\"village_id\":1}', '2026-10-01 11:54:07');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `ip_address`, `user_agent`, `metadata`, `created_at`) VALUES ('49', NULL, 'citizen_grievance_submitted', 'grievances', '25', '127.0.0.1', 'CLI', '{\"tracking_id\":\"GRV-2026-99637\",\"category_id\":1,\"village_id\":1}', '2026-10-01 11:54:07');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `ip_address`, `user_agent`, `metadata`, `created_at`) VALUES ('50', '2', 'login_success', 'users', '2', '127.0.0.1', 'CLI', '{\"role\":\"staff\",\"email\":\"staff@smartgram.local\"}', '2026-10-01 11:54:07');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `ip_address`, `user_agent`, `metadata`, `created_at`) VALUES ('51', '2', 'staff_verified', 'grievances', '24', '127.0.0.1', 'CLI', '{\"tracking_id\":\"GRV-2026-99636\"}', '2026-10-01 11:54:07');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `ip_address`, `user_agent`, `metadata`, `created_at`) VALUES ('52', '2', 'staff_assigned', 'grievances', '24', '127.0.0.1', 'CLI', '{\"tracking_id\":\"GRV-2026-99636\",\"assigned_to\":3,\"officer_name\":\"Water Works Field Officer\"}', '2026-10-01 11:54:07');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `ip_address`, `user_agent`, `metadata`, `created_at`) VALUES ('53', '3', 'login_success', 'users', '3', '127.0.0.1', 'CLI', '{\"role\":\"officer\",\"email\":\"officer@smartgram.local\"}', '2026-10-01 11:54:08');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `ip_address`, `user_agent`, `metadata`, `created_at`) VALUES ('54', '3', 'officer_started_work', 'grievances', '24', '127.0.0.1', 'CLI', '{\"tracking_id\":\"GRV-2026-99636\"}', '2026-10-01 11:54:08');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `ip_address`, `user_agent`, `metadata`, `created_at`) VALUES ('55', '3', 'officer_resolved', 'grievances', '24', '127.0.0.1', 'CLI', '{\"tracking_id\":\"GRV-2026-99636\",\"has_evidence\":true}', '2026-10-01 11:54:08');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `ip_address`, `user_agent`, `metadata`, `created_at`) VALUES ('56', NULL, 'citizen_feedback', 'grievances', '24', '127.0.0.1', 'CLI', '{\"tracking_id\":\"GRV-2026-99636\",\"is_satisfied\":true,\"rating\":5}', '2026-10-01 11:54:08');

-- ------------------------------------------------------------
-- Structure for table `categories`
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `department_id` int(11) NOT NULL,
  `name_en` varchar(100) NOT NULL,
  `name_te` varchar(100) NOT NULL,
  `icon` varchar(50) DEFAULT 'bi-exclamation-circle',
  `sla_hours` int(11) DEFAULT 48,
  `default_priority` enum('low','medium','high','critical') DEFAULT 'medium',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_categories_department` (`department_id`),
  CONSTRAINT `fk_categories_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `categories` (10 rows)
INSERT INTO `categories` (`id`, `department_id`, `name_en`, `name_te`, `icon`, `sla_hours`, `default_priority`, `is_active`, `created_at`) VALUES ('1', '1', 'Pipeline Leakage / Damage', 'పైపులైన్ లీకేజీ / పగిలిన పైపు', 'bi-droplet-half', '24', 'high', '1', '2026-10-01 11:15:53');
INSERT INTO `categories` (`id`, `department_id`, `name_en`, `name_te`, `icon`, `sla_hours`, `default_priority`, `is_active`, `created_at`) VALUES ('2', '1', 'No Water Supply in Tap', 'కుళాయిల్లో నీరు రాకపోవడం', 'bi-water', '12', 'critical', '1', '2026-10-01 11:15:53');
INSERT INTO `categories` (`id`, `department_id`, `name_en`, `name_te`, `icon`, `sla_hours`, `default_priority`, `is_active`, `created_at`) VALUES ('3', '1', 'Contaminated / Muddy Water', 'కలుషిత నీరు / మురుగు వాసన', 'bi-shield-exclamation', '12', 'critical', '1', '2026-10-01 11:15:53');
INSERT INTO `categories` (`id`, `department_id`, `name_en`, `name_te`, `icon`, `sla_hours`, `default_priority`, `is_active`, `created_at`) VALUES ('4', '2', 'Street Light Not Working', 'వీధి దీపం వెలగడం లేదు', 'bi-lightbulb-off', '48', 'medium', '1', '2026-10-01 11:15:53');
INSERT INTO `categories` (`id`, `department_id`, `name_en`, `name_te`, `icon`, `sla_hours`, `default_priority`, `is_active`, `created_at`) VALUES ('5', '2', 'Loose / Hanging Electric Wire', 'వేలాడుతున్న విద్యుత్ తీగలు', 'bi-lightning-charge', '6', 'critical', '1', '2026-10-01 11:15:53');
INSERT INTO `categories` (`id`, `department_id`, `name_en`, `name_te`, `icon`, `sla_hours`, `default_priority`, `is_active`, `created_at`) VALUES ('6', '3', 'Garbage Heap Not Cleaned', 'చెత్త కుప్పలు పేరుకుపోవడం', 'bi-trash', '24', 'medium', '1', '2026-10-01 11:15:53');
INSERT INTO `categories` (`id`, `department_id`, `name_en`, `name_te`, `icon`, `sla_hours`, `default_priority`, `is_active`, `created_at`) VALUES ('7', '3', 'Public Toilet Cleaning Required', 'ప్రజా మరుగుదొడ్ల పరిశుభ్రత', 'bi-badge-wc', '24', 'high', '1', '2026-10-01 11:15:53');
INSERT INTO `categories` (`id`, `department_id`, `name_en`, `name_te`, `icon`, `sla_hours`, `default_priority`, `is_active`, `created_at`) VALUES ('8', '4', 'Drainage Overflow on Road', 'రోడ్డుపైకి మురుగునీరు పొంగడం', 'bi-moisture', '24', 'high', '1', '2026-10-01 11:15:53');
INSERT INTO `categories` (`id`, `department_id`, `name_en`, `name_te`, `icon`, `sla_hours`, `default_priority`, `is_active`, `created_at`) VALUES ('9', '4', 'Drain Blockage / Desilting', 'కాల్వల్లో పూడిక పేరుకుపోవడం', 'bi-hammer', '48', 'medium', '1', '2026-10-01 11:15:53');
INSERT INTO `categories` (`id`, `department_id`, `name_en`, `name_te`, `icon`, `sla_hours`, `default_priority`, `is_active`, `created_at`) VALUES ('10', '5', 'Potholes / Road Broken', 'రోడ్డు గుంతలు / పాడైన రోడ్డు', 'bi-signpost-split', '72', 'low', '1', '2026-10-01 11:15:53');

-- ------------------------------------------------------------
-- Structure for table `departments`
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `departments`;
CREATE TABLE `departments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name_en` varchar(100) NOT NULL,
  `name_te` varchar(100) NOT NULL,
  `code` varchar(30) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `departments` (5 rows)
INSERT INTO `departments` (`id`, `name_en`, `name_te`, `code`, `description`, `is_active`, `created_at`) VALUES ('1', 'Water Supply', 'తాగునీటి సరఫరా', 'WATER', 'Drinking water pipelines, borewells, overhead tanks and RO plants', '1', '2026-10-01 11:15:53');
INSERT INTO `departments` (`id`, `name_en`, `name_te`, `code`, `description`, `is_active`, `created_at`) VALUES ('2', 'Electricity & Street Lights', 'విద్యుత్ & వీధి దీపాలు', 'ELECTRIC', 'Street lights, transformer issues, dangling wires', '1', '2026-10-01 11:15:53');
INSERT INTO `departments` (`id`, `name_en`, `name_te`, `code`, `description`, `is_active`, `created_at`) VALUES ('3', 'Sanitation & Waste Management', 'పారిశుధ్యం & చెత్త నిర్వహణ', 'SANITATION', 'Garbage collection, public bins, village cleaning', '1', '2026-10-01 11:15:53');
INSERT INTO `departments` (`id`, `name_en`, `name_te`, `code`, `description`, `is_active`, `created_at`) VALUES ('4', 'Drainage & Sewage', 'డ్రైనేజీ & మురుగు కాల్వలు', 'DRAINAGE', 'Drainage blockages, open drain overflows, desilting', '1', '2026-10-01 11:15:53');
INSERT INTO `departments` (`id`, `name_en`, `name_te`, `code`, `description`, `is_active`, `created_at`) VALUES ('5', 'Roads & Infrastructure', 'రోడ్లు & భవనాలు', 'ROADS', 'Potholes, damaged concrete roads, culverts, street damage', '1', '2026-10-01 11:15:53');

-- ------------------------------------------------------------
-- Structure for table `districts`
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `districts`;
CREATE TABLE `districts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `state_id` int(11) NOT NULL,
  `code` varchar(20) DEFAULT NULL,
  `name_en` varchar(100) NOT NULL,
  `name_te` varchar(100) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_districts_state` (`state_id`),
  CONSTRAINT `fk_districts_state` FOREIGN KEY (`state_id`) REFERENCES `states` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `districts` (2 rows)
INSERT INTO `districts` (`id`, `state_id`, `code`, `name_en`, `name_te`, `is_active`, `created_at`) VALUES ('1', '1', 'ASR', 'Alluri Sitharama Raju', 'అల్లూరి సీతారామరాజు', '1', '2026-10-01 11:18:24');
INSERT INTO `districts` (`id`, `state_id`, `code`, `name_en`, `name_te`, `is_active`, `created_at`) VALUES ('2', '1', 'PRAKASAM', 'Prakasam', 'ప్రకాశం', '1', '2026-10-01 11:18:24');

-- ------------------------------------------------------------
-- Structure for table `feedback`
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `feedback`;
CREATE TABLE `feedback` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `grievance_id` int(11) NOT NULL,
  `is_resolved_satisfied` tinyint(1) NOT NULL,
  `rating` tinyint(4) DEFAULT NULL,
  `citizen_comment` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `grievance_id` (`grievance_id`),
  CONSTRAINT `fk_feedback_grievance` FOREIGN KEY (`grievance_id`) REFERENCES `grievances` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `feedback` (3 rows)
INSERT INTO `feedback` (`id`, `grievance_id`, `is_resolved_satisfied`, `rating`, `citizen_comment`, `created_at`) VALUES ('3', '14', '1', '5', 'Sanitation staff arrived within 3 hours and thoroughly cleaned. Very satisfied!', '2026-09-29 14:23:01');
INSERT INTO `feedback` (`id`, `grievance_id`, `is_resolved_satisfied`, `rating`, `citizen_comment`, `created_at`) VALUES ('4', '16', '1', '4', 'Panchayat laborers cleaned the desilting cover. Thank you.', '2026-09-28 14:23:01');
INSERT INTO `feedback` (`id`, `grievance_id`, `is_resolved_satisfied`, `rating`, `citizen_comment`, `created_at`) VALUES ('5', '24', '1', '5', 'చాలా ధన్యవాదాలు! వెంటనే స్పందించి సమస్యను చక్కగా పరిష్కరించారు.', '2026-10-01 11:54:08');

-- ------------------------------------------------------------
-- Structure for table `gram_panchayats`
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `gram_panchayats`;
CREATE TABLE `gram_panchayats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mandal_id` int(11) NOT NULL,
  `code` varchar(20) DEFAULT NULL,
  `name_en` varchar(100) NOT NULL,
  `name_te` varchar(100) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_gram_panchayats_mandal` (`mandal_id`),
  CONSTRAINT `fk_gram_panchayats_mandal` FOREIGN KEY (`mandal_id`) REFERENCES `mandals` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `gram_panchayats` (5 rows)
INSERT INTO `gram_panchayats` (`id`, `mandal_id`, `code`, `name_en`, `name_te`, `is_active`, `created_at`) VALUES ('1', '1', 'GP_PAD', 'Paderu', 'పాడేరు', '1', '2026-10-01 11:18:24');
INSERT INTO `gram_panchayats` (`id`, `mandal_id`, `code`, `name_en`, `name_te`, `is_active`, `created_at`) VALUES ('2', '1', 'GP_MIN', 'Minumuluru', 'మినుములూరు', '1', '2026-10-01 11:18:24');
INSERT INTO `gram_panchayats` (`id`, `mandal_id`, `code`, `name_en`, `name_te`, `is_active`, `created_at`) VALUES ('3', '2', 'GP_ARK', 'Araku', 'అరకు', '1', '2026-10-01 11:18:24');
INSERT INTO `gram_panchayats` (`id`, `mandal_id`, `code`, `name_en`, `name_te`, `is_active`, `created_at`) VALUES ('4', '3', 'GP_LMS', 'Lammasingi', 'లంబసింగి', '1', '2026-10-01 11:18:24');
INSERT INTO `gram_panchayats` (`id`, `mandal_id`, `code`, `name_en`, `name_te`, `is_active`, `created_at`) VALUES ('5', '5', 'GP_RAM', 'Ramapuram', 'రామాపురం', '1', '2026-10-01 11:18:24');

-- ------------------------------------------------------------
-- Structure for table `grievance_updates`
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `grievance_updates`;
CREATE TABLE `grievance_updates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `grievance_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(50) NOT NULL,
  `from_status` varchar(30) DEFAULT NULL,
  `to_status` varchar(30) NOT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_updates_grievance` (`grievance_id`),
  KEY `fk_updates_user` (`user_id`),
  CONSTRAINT `fk_updates_grievance` FOREIGN KEY (`grievance_id`) REFERENCES `grievances` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_updates_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `grievance_updates` (45 rows)
INSERT INTO `grievance_updates` (`id`, `grievance_id`, `user_id`, `action`, `from_status`, `to_status`, `remarks`, `created_at`) VALUES ('2', '2', NULL, 'submit', NULL, 'submitted', 'Grievance submitted by citizen', '2026-10-01 11:49:59');
INSERT INTO `grievance_updates` (`id`, `grievance_id`, `user_id`, `action`, `from_status`, `to_status`, `remarks`, `created_at`) VALUES ('3', '3', NULL, 'submit', NULL, 'submitted', 'Grievance submitted by citizen', '2026-10-01 11:52:13');
INSERT INTO `grievance_updates` (`id`, `grievance_id`, `user_id`, `action`, `from_status`, `to_status`, `remarks`, `created_at`) VALUES ('4', '4', NULL, 'submit', NULL, 'submitted', 'Grievance submitted by citizen', '2026-10-01 11:52:13');
INSERT INTO `grievance_updates` (`id`, `grievance_id`, `user_id`, `action`, `from_status`, `to_status`, `remarks`, `created_at`) VALUES ('9', '7', NULL, 'citizen_submitted', NULL, 'submitted', 'Grievance submitted by citizen', '2026-09-29 20:23:01');
INSERT INTO `grievance_updates` (`id`, `grievance_id`, `user_id`, `action`, `from_status`, `to_status`, `remarks`, `created_at`) VALUES ('10', '8', NULL, 'citizen_submitted', NULL, 'submitted', 'Grievance submitted by citizen', '2026-09-30 00:23:01');
INSERT INTO `grievance_updates` (`id`, `grievance_id`, `user_id`, `action`, `from_status`, `to_status`, `remarks`, `created_at`) VALUES ('11', '8', '2', 'staff_assigned', 'submitted', 'assigned', 'Assigned to field officer for site inspection', '2026-09-30 02:23:01');
INSERT INTO `grievance_updates` (`id`, `grievance_id`, `user_id`, `action`, `from_status`, `to_status`, `remarks`, `created_at`) VALUES ('12', '9', NULL, 'citizen_submitted', NULL, 'submitted', 'Grievance submitted by citizen', '2026-09-30 02:23:01');
INSERT INTO `grievance_updates` (`id`, `grievance_id`, `user_id`, `action`, `from_status`, `to_status`, `remarks`, `created_at`) VALUES ('13', '9', '2', 'staff_assigned', 'submitted', 'assigned', 'Assigned to field officer for site inspection', '2026-09-30 04:23:01');
INSERT INTO `grievance_updates` (`id`, `grievance_id`, `user_id`, `action`, `from_status`, `to_status`, `remarks`, `created_at`) VALUES ('14', '10', NULL, 'citizen_submitted', NULL, 'submitted', 'Grievance submitted by citizen', '2026-09-30 20:23:01');
INSERT INTO `grievance_updates` (`id`, `grievance_id`, `user_id`, `action`, `from_status`, `to_status`, `remarks`, `created_at`) VALUES ('15', '10', '2', 'staff_assigned', 'submitted', 'assigned', 'Assigned to field officer for site inspection', '2026-09-30 22:23:01');
INSERT INTO `grievance_updates` (`id`, `grievance_id`, `user_id`, `action`, `from_status`, `to_status`, `remarks`, `created_at`) VALUES ('16', '11', NULL, 'citizen_submitted', NULL, 'submitted', 'Grievance submitted by citizen', '2026-09-30 14:23:01');
INSERT INTO `grievance_updates` (`id`, `grievance_id`, `user_id`, `action`, `from_status`, `to_status`, `remarks`, `created_at`) VALUES ('17', '11', '2', 'staff_assigned', 'submitted', 'assigned', 'Assigned to field officer for site inspection', '2026-09-30 16:23:01');
INSERT INTO `grievance_updates` (`id`, `grievance_id`, `user_id`, `action`, `from_status`, `to_status`, `remarks`, `created_at`) VALUES ('18', '11', '5', 'officer_resolved', 'in_progress', 'resolved', 'Field repair completed on site. Awaiting citizen confirmation.', '2026-09-30 20:23:01');
INSERT INTO `grievance_updates` (`id`, `grievance_id`, `user_id`, `action`, `from_status`, `to_status`, `remarks`, `created_at`) VALUES ('19', '12', NULL, 'citizen_submitted', NULL, 'submitted', 'Grievance submitted by citizen', '2026-09-29 22:23:01');
INSERT INTO `grievance_updates` (`id`, `grievance_id`, `user_id`, `action`, `from_status`, `to_status`, `remarks`, `created_at`) VALUES ('20', '13', NULL, 'citizen_submitted', NULL, 'submitted', 'Grievance submitted by citizen', '2026-10-01 01:23:01');
INSERT INTO `grievance_updates` (`id`, `grievance_id`, `user_id`, `action`, `from_status`, `to_status`, `remarks`, `created_at`) VALUES ('21', '13', '2', 'staff_assigned', 'submitted', 'assigned', 'Assigned to field officer for site inspection', '2026-10-01 03:23:01');
INSERT INTO `grievance_updates` (`id`, `grievance_id`, `user_id`, `action`, `from_status`, `to_status`, `remarks`, `created_at`) VALUES ('22', '13', '4', 'officer_resolved', 'in_progress', 'resolved', 'Field repair completed on site. Awaiting citizen confirmation.', '2026-10-01 07:23:01');
INSERT INTO `grievance_updates` (`id`, `grievance_id`, `user_id`, `action`, `from_status`, `to_status`, `remarks`, `created_at`) VALUES ('23', '14', NULL, 'citizen_submitted', NULL, 'submitted', 'Grievance submitted by citizen', '2026-09-29 06:23:01');
INSERT INTO `grievance_updates` (`id`, `grievance_id`, `user_id`, `action`, `from_status`, `to_status`, `remarks`, `created_at`) VALUES ('24', '14', '2', 'staff_assigned', 'submitted', 'assigned', 'Assigned to field officer for site inspection', '2026-09-29 08:23:01');
INSERT INTO `grievance_updates` (`id`, `grievance_id`, `user_id`, `action`, `from_status`, `to_status`, `remarks`, `created_at`) VALUES ('25', '14', '5', 'officer_resolved', 'in_progress', 'resolved', 'Field repair completed on site. Awaiting citizen confirmation.', '2026-09-29 12:23:01');
INSERT INTO `grievance_updates` (`id`, `grievance_id`, `user_id`, `action`, `from_status`, `to_status`, `remarks`, `created_at`) VALUES ('26', '14', NULL, 'citizen_feedback_closed', 'resolved', 'closed', 'Citizen closed grievance with 5-star rating: Sanitation staff arrived within 3 hours and thoroughly cleaned. Very satisfied!', '2026-09-29 14:23:01');
INSERT INTO `grievance_updates` (`id`, `grievance_id`, `user_id`, `action`, `from_status`, `to_status`, `remarks`, `created_at`) VALUES ('27', '15', NULL, 'citizen_submitted', NULL, 'submitted', 'Grievance submitted by citizen', '2026-09-29 04:23:01');
INSERT INTO `grievance_updates` (`id`, `grievance_id`, `user_id`, `action`, `from_status`, `to_status`, `remarks`, `created_at`) VALUES ('28', '15', '2', 'staff_assigned', 'submitted', 'assigned', 'Assigned to field officer for site inspection', '2026-09-29 06:23:01');
INSERT INTO `grievance_updates` (`id`, `grievance_id`, `user_id`, `action`, `from_status`, `to_status`, `remarks`, `created_at`) VALUES ('29', '15', '3', 'officer_resolved', 'in_progress', 'resolved', 'Field repair completed on site. Awaiting citizen confirmation.', '2026-09-29 10:23:01');
INSERT INTO `grievance_updates` (`id`, `grievance_id`, `user_id`, `action`, `from_status`, `to_status`, `remarks`, `created_at`) VALUES ('30', '15', NULL, 'citizen_reopened', 'resolved', 'reopened', 'Citizen reopened issue: Technician flushed pipeline once but mud returned next morning. Need filter replacement.', '2026-09-29 14:23:01');
INSERT INTO `grievance_updates` (`id`, `grievance_id`, `user_id`, `action`, `from_status`, `to_status`, `remarks`, `created_at`) VALUES ('31', '16', NULL, 'citizen_submitted', NULL, 'submitted', 'Grievance submitted by citizen', '2026-09-28 06:23:01');
INSERT INTO `grievance_updates` (`id`, `grievance_id`, `user_id`, `action`, `from_status`, `to_status`, `remarks`, `created_at`) VALUES ('32', '16', '2', 'staff_assigned', 'submitted', 'assigned', 'Assigned to field officer for site inspection', '2026-09-28 08:23:01');
INSERT INTO `grievance_updates` (`id`, `grievance_id`, `user_id`, `action`, `from_status`, `to_status`, `remarks`, `created_at`) VALUES ('33', '16', '5', 'officer_resolved', 'in_progress', 'resolved', 'Field repair completed on site. Awaiting citizen confirmation.', '2026-09-28 12:23:01');
INSERT INTO `grievance_updates` (`id`, `grievance_id`, `user_id`, `action`, `from_status`, `to_status`, `remarks`, `created_at`) VALUES ('34', '16', NULL, 'citizen_feedback_closed', 'resolved', 'closed', 'Citizen closed grievance with 4-star rating: Panchayat laborers cleaned the desilting cover. Thank you.', '2026-09-28 14:23:01');
INSERT INTO `grievance_updates` (`id`, `grievance_id`, `user_id`, `action`, `from_status`, `to_status`, `remarks`, `created_at`) VALUES ('35', '17', NULL, 'citizen_submitted', NULL, 'submitted', 'Grievance submitted by citizen', '2026-10-01 11:53:16');
INSERT INTO `grievance_updates` (`id`, `grievance_id`, `user_id`, `action`, `from_status`, `to_status`, `remarks`, `created_at`) VALUES ('36', '17', '2', 'staff_verified', 'submitted', 'under_review', 'Verified on phone call with citizen', '2026-10-01 11:53:16');
INSERT INTO `grievance_updates` (`id`, `grievance_id`, `user_id`, `action`, `from_status`, `to_status`, `remarks`, `created_at`) VALUES ('37', '17', '2', 'staff_assigned', 'under_review', 'assigned', 'Assigned to Water Works Field Officer. Remarks: Please visit site immediately with repair valve', '2026-10-01 11:53:17');
INSERT INTO `grievance_updates` (`id`, `grievance_id`, `user_id`, `action`, `from_status`, `to_status`, `remarks`, `created_at`) VALUES ('38', '17', '3', 'officer_started_work', 'assigned', 'in_progress', 'Arrived on site with welding equipment', '2026-10-01 11:53:17');
INSERT INTO `grievance_updates` (`id`, `grievance_id`, `user_id`, `action`, `from_status`, `to_status`, `remarks`, `created_at`) VALUES ('39', '17', '3', 'progress_note', 'in_progress', 'in_progress', 'Excavation completed, replaced 2-inch PVC valve', '2026-10-01 11:53:17');
INSERT INTO `grievance_updates` (`id`, `grievance_id`, `user_id`, `action`, `from_status`, `to_status`, `remarks`, `created_at`) VALUES ('40', '17', '3', 'officer_resolved', 'in_progress', 'resolved', 'Leak sealed with new heavy-duty clamp and tested under pressure. Water flow restored.', '2026-10-01 11:53:17');
INSERT INTO `grievance_updates` (`id`, `grievance_id`, `user_id`, `action`, `from_status`, `to_status`, `remarks`, `created_at`) VALUES ('41', '18', '2', 'staff_rejected', 'submitted', 'rejected', 'Duplicate submission with invalid contact number.', '2026-10-01 11:53:17');
INSERT INTO `grievance_updates` (`id`, `grievance_id`, `user_id`, `action`, `from_status`, `to_status`, `remarks`, `created_at`) VALUES ('44', '22', NULL, 'submit', NULL, 'submitted', 'Grievance submitted by citizen', '2026-10-01 11:53:37');
INSERT INTO `grievance_updates` (`id`, `grievance_id`, `user_id`, `action`, `from_status`, `to_status`, `remarks`, `created_at`) VALUES ('45', '23', NULL, 'submit', NULL, 'submitted', 'Grievance submitted by citizen', '2026-10-01 11:54:06');
INSERT INTO `grievance_updates` (`id`, `grievance_id`, `user_id`, `action`, `from_status`, `to_status`, `remarks`, `created_at`) VALUES ('46', '24', NULL, 'submit', NULL, 'submitted', 'Grievance submitted by citizen', '2026-10-01 11:54:07');
INSERT INTO `grievance_updates` (`id`, `grievance_id`, `user_id`, `action`, `from_status`, `to_status`, `remarks`, `created_at`) VALUES ('47', '25', NULL, 'submit', NULL, 'submitted', 'Grievance submitted by citizen', '2026-10-01 11:54:07');
INSERT INTO `grievance_updates` (`id`, `grievance_id`, `user_id`, `action`, `from_status`, `to_status`, `remarks`, `created_at`) VALUES ('48', '24', '2', 'staff_verified', 'submitted', 'under_review', 'క్షేత్రస్థాయిలో సమస్య ధృవీకరించబడింది. తక్షణ మరమ్మతు అవసరం.', '2026-10-01 11:54:07');
INSERT INTO `grievance_updates` (`id`, `grievance_id`, `user_id`, `action`, `from_status`, `to_status`, `remarks`, `created_at`) VALUES ('49', '24', '2', 'staff_assigned', 'under_review', 'assigned', 'Assigned to Water Works Field Officer. Remarks: మంచినీటి సరఫరా విభాగం అధికారికి కేటాయించడమైనది.', '2026-10-01 11:54:07');
INSERT INTO `grievance_updates` (`id`, `grievance_id`, `user_id`, `action`, `from_status`, `to_status`, `remarks`, `created_at`) VALUES ('50', '24', '3', 'officer_started_work', 'assigned', 'in_progress', 'మరమ్మతు పనులు ప్రారంభించబడ్డాయి. కొత్త పైప్‌లైన్ అమర్చుతున్నాము.', '2026-10-01 11:54:08');
INSERT INTO `grievance_updates` (`id`, `grievance_id`, `user_id`, `action`, `from_status`, `to_status`, `remarks`, `created_at`) VALUES ('51', '24', '3', 'officer_resolved', 'in_progress', 'resolved', 'పైప్‌లైన్ లీకేజీని అరికట్టి కొత్త వాల్వ్ విజయవంతంగా బిగించడమైనది. మంచినీటి సరఫరా పునరుద్ధరించబడింది.', '2026-10-01 11:54:08');
INSERT INTO `grievance_updates` (`id`, `grievance_id`, `user_id`, `action`, `from_status`, `to_status`, `remarks`, `created_at`) VALUES ('52', '24', NULL, 'citizen_feedback', 'resolved', 'closed', 'Citizen verified and confirmed resolution. Rating: 5/5 stars. Remarks: చాలా ధన్యవాదాలు! వెంటనే స్పందించి సమస్యను చక్కగా పరిష్కరించారు.', '2026-10-01 11:54:08');

-- ------------------------------------------------------------
-- Structure for table `grievances`
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `grievances`;
CREATE TABLE `grievances` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tracking_id` varchar(30) NOT NULL,
  `citizen_name` varchar(100) NOT NULL,
  `citizen_phone` varchar(20) NOT NULL,
  `state_id` int(11) DEFAULT NULL,
  `district_id` int(11) DEFAULT NULL,
  `mandal_id` int(11) DEFAULT NULL,
  `gram_panchayat_id` int(11) DEFAULT NULL,
  `village_id` int(11) NOT NULL,
  `ward_id` int(11) DEFAULT NULL,
  `category_id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `ai_summary` text DEFAULT NULL,
  `landmark` varchar(255) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `priority` enum('low','medium','high','critical') DEFAULT 'medium',
  `ai_suggested_priority` varchar(20) DEFAULT NULL,
  `ai_confidence` decimal(3,2) DEFAULT NULL,
  `ai_duplicate_of_id` int(11) DEFAULT NULL,
  `status` enum('submitted','under_review','verified','assigned','in_progress','referred','resolved','citizen_verification','reopened','rejected','closed') DEFAULT 'submitted',
  `assigned_to_user_id` int(11) DEFAULT NULL,
  `sla_due_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `tracking_id` (`tracking_id`),
  KEY `idx_grievances_phone` (`citizen_phone`),
  KEY `idx_grievances_status` (`status`),
  KEY `idx_grievances_village` (`village_id`),
  KEY `idx_grievances_category` (`category_id`),
  KEY `fk_grievances_ward` (`ward_id`),
  KEY `fk_grievances_department` (`department_id`),
  KEY `fk_grievances_assigned_user` (`assigned_to_user_id`),
  KEY `idx_grievances_state` (`state_id`),
  KEY `idx_grievances_district` (`district_id`),
  KEY `idx_grievances_mandal` (`mandal_id`),
  KEY `idx_grievances_panchayat` (`gram_panchayat_id`),
  CONSTRAINT `fk_grievances_assigned_user` FOREIGN KEY (`assigned_to_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_grievances_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  CONSTRAINT `fk_grievances_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`),
  CONSTRAINT `fk_grievances_district` FOREIGN KEY (`district_id`) REFERENCES `districts` (`id`) ON DELETE NO ACTION,
  CONSTRAINT `fk_grievances_gram_panchayat` FOREIGN KEY (`gram_panchayat_id`) REFERENCES `gram_panchayats` (`id`) ON DELETE NO ACTION,
  CONSTRAINT `fk_grievances_mandal` FOREIGN KEY (`mandal_id`) REFERENCES `mandals` (`id`) ON DELETE NO ACTION,
  CONSTRAINT `fk_grievances_state` FOREIGN KEY (`state_id`) REFERENCES `states` (`id`) ON DELETE NO ACTION,
  CONSTRAINT `fk_grievances_village` FOREIGN KEY (`village_id`) REFERENCES `villages` (`id`),
  CONSTRAINT `fk_grievances_ward` FOREIGN KEY (`ward_id`) REFERENCES `wards` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `grievances` (19 rows)
INSERT INTO `grievances` (`id`, `tracking_id`, `citizen_name`, `citizen_phone`, `state_id`, `district_id`, `mandal_id`, `gram_panchayat_id`, `village_id`, `ward_id`, `category_id`, `department_id`, `description`, `ai_summary`, `landmark`, `latitude`, `longitude`, `priority`, `ai_suggested_priority`, `ai_confidence`, `ai_duplicate_of_id`, `status`, `assigned_to_user_id`, `sla_due_at`, `created_at`, `updated_at`) VALUES ('2', 'GRV-2026-00001', 'Ramesh Geographic Tester', '9848012345', '1', '1', '1', '1', '2', '6', '1', '1', 'Water supply pipe leakage in main road near Paderu RTC complex.', NULL, 'Near Paderu RTC Complex', NULL, NULL, 'high', NULL, NULL, NULL, 'submitted', NULL, '2026-10-02 06:19:59', '2026-10-01 11:49:59', '2026-10-01 11:49:59');
INSERT INTO `grievances` (`id`, `tracking_id`, `citizen_name`, `citizen_phone`, `state_id`, `district_id`, `mandal_id`, `gram_panchayat_id`, `village_id`, `ward_id`, `category_id`, `department_id`, `description`, `ai_summary`, `landmark`, `latitude`, `longitude`, `priority`, `ai_suggested_priority`, `ai_confidence`, `ai_duplicate_of_id`, `status`, `assigned_to_user_id`, `sla_due_at`, `created_at`, `updated_at`) VALUES ('3', 'GRV-2026-00002', 'రామయ్య (Ramaiah)', '9876543210', '1', '2', '5', '5', '1', '1', '1', '1', 'మా వీధిలో మెయిన్ పైపులైన్ పగిలి తాగునీరు వృధాగా రోడ్డుపైకి వస్తోంది. దయచేసి వెంటనే బాగు చేయగలరు.', NULL, 'రామాలయం ఎదురుగా (Opposite Temple)', '15.50570000', '80.04990000', 'high', NULL, NULL, NULL, 'submitted', NULL, '2026-10-02 06:22:13', '2026-10-01 11:52:13', '2026-10-01 11:52:13');
INSERT INTO `grievances` (`id`, `tracking_id`, `citizen_name`, `citizen_phone`, `state_id`, `district_id`, `mandal_id`, `gram_panchayat_id`, `village_id`, `ward_id`, `category_id`, `department_id`, `description`, `ai_summary`, `landmark`, `latitude`, `longitude`, `priority`, `ai_suggested_priority`, `ai_confidence`, `ai_duplicate_of_id`, `status`, `assigned_to_user_id`, `sla_due_at`, `created_at`, `updated_at`) VALUES ('4', 'GRV-2026-00003', 'లక్ష్మి (Lakshmi)', '9876543222', '1', '2', '5', '5', '1', '2', '4', '2', 'మా వీధిలో గత 3 రోజులుగా వీధి దీపం వెలగడం లేదు, రాత్రి వేళ చీకటిగా ఉంది.', NULL, 'శివాలయం వీధి పోల్ నెం 12', NULL, NULL, 'medium', NULL, NULL, NULL, 'submitted', NULL, '2026-10-03 06:22:13', '2026-10-01 11:52:13', '2026-10-01 11:52:13');
INSERT INTO `grievances` (`id`, `tracking_id`, `citizen_name`, `citizen_phone`, `state_id`, `district_id`, `mandal_id`, `gram_panchayat_id`, `village_id`, `ward_id`, `category_id`, `department_id`, `description`, `ai_summary`, `landmark`, `latitude`, `longitude`, `priority`, `ai_suggested_priority`, `ai_confidence`, `ai_duplicate_of_id`, `status`, `assigned_to_user_id`, `sla_due_at`, `created_at`, `updated_at`) VALUES ('7', 'GRV-2026-00010', 'K. Venkata Rao', '9848011223', NULL, NULL, NULL, NULL, '1', '1', '1', '1', 'Main drinking water pipeline broke near community hall. Water gushing onto road for 2 days.', NULL, 'Near Community Hall, Gandhi Nagar', NULL, NULL, 'critical', NULL, NULL, NULL, 'submitted', NULL, '2026-09-30 20:23:01', '2026-09-29 20:23:01', '2026-10-01 11:53:01');
INSERT INTO `grievances` (`id`, `tracking_id`, `citizen_name`, `citizen_phone`, `state_id`, `district_id`, `mandal_id`, `gram_panchayat_id`, `village_id`, `ward_id`, `category_id`, `department_id`, `description`, `ai_summary`, `landmark`, `latitude`, `longitude`, `priority`, `ai_suggested_priority`, `ai_confidence`, `ai_duplicate_of_id`, `status`, `assigned_to_user_id`, `sla_due_at`, `created_at`, `updated_at`) VALUES ('8', 'GRV-2026-00011', 'B. Lakshmi Devi', '9848022334', NULL, NULL, NULL, NULL, '1', '1', '4', '2', 'Two street light bulbs fused near primary school street. Very dark at night.', NULL, 'Opposite Primary School', NULL, NULL, 'medium', NULL, NULL, NULL, 'in_progress', '4', '2026-10-02 00:23:01', '2026-09-30 00:23:01', '2026-10-01 11:53:01');
INSERT INTO `grievances` (`id`, `tracking_id`, `citizen_name`, `citizen_phone`, `state_id`, `district_id`, `mandal_id`, `gram_panchayat_id`, `village_id`, `ward_id`, `category_id`, `department_id`, `description`, `ai_summary`, `landmark`, `latitude`, `longitude`, `priority`, `ai_suggested_priority`, `ai_confidence`, `ai_duplicate_of_id`, `status`, `assigned_to_user_id`, `sla_due_at`, `created_at`, `updated_at`) VALUES ('9', 'GRV-2026-00012', 'M. Subba Rao', '9848033445', NULL, NULL, NULL, NULL, '1', '2', '8', '4', 'Open drainage canal is blocked with silt and plastic. Heavy foul smell and mosquito breeding.', NULL, 'Near Shivalayam Temple Arch', NULL, NULL, 'high', NULL, NULL, NULL, 'assigned', '5', '2026-10-01 02:23:01', '2026-09-30 02:23:01', '2026-10-01 11:53:01');
INSERT INTO `grievances` (`id`, `tracking_id`, `citizen_name`, `citizen_phone`, `state_id`, `district_id`, `mandal_id`, `gram_panchayat_id`, `village_id`, `ward_id`, `category_id`, `department_id`, `description`, `ai_summary`, `landmark`, `latitude`, `longitude`, `priority`, `ai_suggested_priority`, `ai_confidence`, `ai_duplicate_of_id`, `status`, `assigned_to_user_id`, `sla_due_at`, `created_at`, `updated_at`) VALUES ('10', 'GRV-2026-00013', 'P. Anjaneyulu', '9848044556', NULL, NULL, NULL, NULL, '1', '2', '2', '1', 'Public tap has had no water supply since yesterday morning. Families unable to fetch water.', NULL, 'Water Tank Junction', NULL, NULL, 'critical', NULL, NULL, NULL, 'in_progress', '3', '2026-10-01 08:23:01', '2026-09-30 20:23:01', '2026-10-01 11:53:01');
INSERT INTO `grievances` (`id`, `tracking_id`, `citizen_name`, `citizen_phone`, `state_id`, `district_id`, `mandal_id`, `gram_panchayat_id`, `village_id`, `ward_id`, `category_id`, `department_id`, `description`, `ai_summary`, `landmark`, `latitude`, `longitude`, `priority`, `ai_suggested_priority`, `ai_confidence`, `ai_duplicate_of_id`, `status`, `assigned_to_user_id`, `sla_due_at`, `created_at`, `updated_at`) VALUES ('11', 'GRV-2026-00014', 'S. Rama Devi', '9848055667', NULL, NULL, NULL, NULL, '1', '3', '6', '3', 'Garbage heap has accumulated for over 4 days beside Anganwadi centre. Stray cattle scattering waste.', NULL, 'Beside Anganwadi Centre', NULL, NULL, 'high', NULL, NULL, NULL, 'resolved', '5', '2026-10-01 14:23:01', '2026-09-30 14:23:01', '2026-10-01 11:53:01');
INSERT INTO `grievances` (`id`, `tracking_id`, `citizen_name`, `citizen_phone`, `state_id`, `district_id`, `mandal_id`, `gram_panchayat_id`, `village_id`, `ward_id`, `category_id`, `department_id`, `description`, `ai_summary`, `landmark`, `latitude`, `longitude`, `priority`, `ai_suggested_priority`, `ai_confidence`, `ai_duplicate_of_id`, `status`, `assigned_to_user_id`, `sla_due_at`, `created_at`, `updated_at`) VALUES ('12', 'GRV-2026-00015', 'Ch. Srinivasa Rao', '9848066778', NULL, NULL, NULL, NULL, '1', '4', '10', '5', 'Large trench dug across road for pipeline repair was left unpaved. Dangerous for two-wheelers.', NULL, 'Main Road, BC Colony Entrance', NULL, NULL, 'medium', NULL, NULL, NULL, 'under_review', NULL, '2026-10-02 22:23:01', '2026-09-29 22:23:01', '2026-10-01 11:53:01');
INSERT INTO `grievances` (`id`, `tracking_id`, `citizen_name`, `citizen_phone`, `state_id`, `district_id`, `mandal_id`, `gram_panchayat_id`, `village_id`, `ward_id`, `category_id`, `department_id`, `description`, `ai_summary`, `landmark`, `latitude`, `longitude`, `priority`, `ai_suggested_priority`, `ai_confidence`, `ai_duplicate_of_id`, `status`, `assigned_to_user_id`, `sla_due_at`, `created_at`, `updated_at`) VALUES ('13', 'GRV-2026-00016', 'D. Nageswara Rao', '9848077889', NULL, NULL, NULL, NULL, '1', '4', '5', '2', 'Low hanging live electric cable near village transformer. Poses electrocution danger.', NULL, 'Transformer Pole #12', NULL, NULL, 'critical', NULL, NULL, NULL, 'resolved', '4', '2026-10-01 07:23:01', '2026-10-01 01:23:01', '2026-10-01 11:53:01');
INSERT INTO `grievances` (`id`, `tracking_id`, `citizen_name`, `citizen_phone`, `state_id`, `district_id`, `mandal_id`, `gram_panchayat_id`, `village_id`, `ward_id`, `category_id`, `department_id`, `description`, `ai_summary`, `landmark`, `latitude`, `longitude`, `priority`, `ai_suggested_priority`, `ai_confidence`, `ai_duplicate_of_id`, `status`, `assigned_to_user_id`, `sla_due_at`, `created_at`, `updated_at`) VALUES ('14', 'GRV-2026-00017', 'T. Kumari', '9848088990', NULL, NULL, NULL, NULL, '1', '5', '7', '3', 'Rythu Bazaar public conveniences have water tap leakage and require thorough sanitization.', NULL, 'Rythu Bazaar Shed #2', NULL, NULL, 'medium', NULL, NULL, NULL, 'closed', '5', '2026-10-01 18:23:01', '2026-09-29 06:23:01', '2026-10-01 11:53:01');
INSERT INTO `grievances` (`id`, `tracking_id`, `citizen_name`, `citizen_phone`, `state_id`, `district_id`, `mandal_id`, `gram_panchayat_id`, `village_id`, `ward_id`, `category_id`, `department_id`, `description`, `ai_summary`, `landmark`, `latitude`, `longitude`, `priority`, `ai_suggested_priority`, `ai_confidence`, `ai_duplicate_of_id`, `status`, `assigned_to_user_id`, `sla_due_at`, `created_at`, `updated_at`) VALUES ('15', 'GRV-2026-00018', 'Y. Venkatesh', '9848099001', NULL, NULL, NULL, NULL, '1', '1', '3', '1', 'Drinking water from borewell is turbid with mud residue. Villagers getting stomach ache.', NULL, 'Near Old Well, Gandhi Nagar', NULL, NULL, 'critical', NULL, NULL, NULL, 'reopened', '3', '2026-09-30 22:23:01', '2026-09-29 04:23:01', '2026-10-01 11:53:01');
INSERT INTO `grievances` (`id`, `tracking_id`, `citizen_name`, `citizen_phone`, `state_id`, `district_id`, `mandal_id`, `gram_panchayat_id`, `village_id`, `ward_id`, `category_id`, `department_id`, `description`, `ai_summary`, `landmark`, `latitude`, `longitude`, `priority`, `ai_suggested_priority`, `ai_confidence`, `ai_duplicate_of_id`, `status`, `assigned_to_user_id`, `sla_due_at`, `created_at`, `updated_at`) VALUES ('16', 'GRV-2026-00019', 'G. Krishna', '9848100112', NULL, NULL, NULL, NULL, '1', '5', '9', '4', 'Covered culvert drain is jammed near vegetable stalls.', NULL, 'Vegetable Market Crossroad', NULL, NULL, 'medium', NULL, NULL, NULL, 'closed', '5', '2026-10-02 02:23:01', '2026-09-28 06:23:01', '2026-10-01 11:53:01');
INSERT INTO `grievances` (`id`, `tracking_id`, `citizen_name`, `citizen_phone`, `state_id`, `district_id`, `mandal_id`, `gram_panchayat_id`, `village_id`, `ward_id`, `category_id`, `department_id`, `description`, `ai_summary`, `landmark`, `latitude`, `longitude`, `priority`, `ai_suggested_priority`, `ai_confidence`, `ai_duplicate_of_id`, `status`, `assigned_to_user_id`, `sla_due_at`, `created_at`, `updated_at`) VALUES ('17', 'GRV-2026-66903', 'P. Workflow Tester', '9848123456', NULL, NULL, NULL, NULL, '1', '1', '1', '1', 'Main water pipeline leaking heavily at village square.', NULL, 'Village Square Pole #4', NULL, NULL, 'critical', NULL, NULL, NULL, 'resolved', '3', '2026-10-02 11:53:16', '2026-10-01 11:53:16', '2026-10-01 11:53:17');
INSERT INTO `grievances` (`id`, `tracking_id`, `citizen_name`, `citizen_phone`, `state_id`, `district_id`, `mandal_id`, `gram_panchayat_id`, `village_id`, `ward_id`, `category_id`, `department_id`, `description`, `ai_summary`, `landmark`, `latitude`, `longitude`, `priority`, `ai_suggested_priority`, `ai_confidence`, `ai_duplicate_of_id`, `status`, `assigned_to_user_id`, `sla_due_at`, `created_at`, `updated_at`) VALUES ('18', 'GRV-2026-99633', 'Fake User', '9999999999', NULL, NULL, NULL, NULL, '1', '1', '1', '1', 'Spam test text 12345', NULL, NULL, NULL, NULL, 'low', NULL, NULL, NULL, 'rejected', NULL, NULL, '2026-10-01 11:53:17', '2026-10-01 11:53:17');
INSERT INTO `grievances` (`id`, `tracking_id`, `citizen_name`, `citizen_phone`, `state_id`, `district_id`, `mandal_id`, `gram_panchayat_id`, `village_id`, `ward_id`, `category_id`, `department_id`, `description`, `ai_summary`, `landmark`, `latitude`, `longitude`, `priority`, `ai_suggested_priority`, `ai_confidence`, `ai_duplicate_of_id`, `status`, `assigned_to_user_id`, `sla_due_at`, `created_at`, `updated_at`) VALUES ('22', 'GRV-2026-99634', 'Kiran <script>alert(\"xss-name\")</script>', '9988776655', '1', '2', '5', '5', '1', '1', '1', '1', '<img src=x onerror=alert(1)> Drinking water leakage near school.', NULL, NULL, NULL, NULL, 'high', NULL, NULL, NULL, 'submitted', NULL, '2026-10-02 06:23:37', '2026-10-01 11:53:37', '2026-10-01 11:53:37');
INSERT INTO `grievances` (`id`, `tracking_id`, `citizen_name`, `citizen_phone`, `state_id`, `district_id`, `mandal_id`, `gram_panchayat_id`, `village_id`, `ward_id`, `category_id`, `department_id`, `description`, `ai_summary`, `landmark`, `latitude`, `longitude`, `priority`, `ai_suggested_priority`, `ai_confidence`, `ai_duplicate_of_id`, `status`, `assigned_to_user_id`, `sla_due_at`, `created_at`, `updated_at`) VALUES ('23', 'GRV-2026-99635', 'Kiran <script>alert(\"xss-name\")</script>', '9988776655', '1', '2', '5', '5', '1', '1', '1', '1', '<img src=x onerror=alert(1)> Drinking water leakage near school.', NULL, NULL, NULL, NULL, 'high', NULL, NULL, NULL, 'submitted', NULL, '2026-10-02 06:24:06', '2026-10-01 11:54:06', '2026-10-01 11:54:06');
INSERT INTO `grievances` (`id`, `tracking_id`, `citizen_name`, `citizen_phone`, `state_id`, `district_id`, `mandal_id`, `gram_panchayat_id`, `village_id`, `ward_id`, `category_id`, `department_id`, `description`, `ai_summary`, `landmark`, `latitude`, `longitude`, `priority`, `ai_suggested_priority`, `ai_confidence`, `ai_duplicate_of_id`, `status`, `assigned_to_user_id`, `sla_due_at`, `created_at`, `updated_at`) VALUES ('24', 'GRV-2026-99636', 'లక్ష్మి నారాయణ (Lakshmi Narayana)', '9440171629', '1', '2', '5', '5', '1', '2', '1', '1', 'మంచినీటి పైప్‌లైన్ పగిలి రోడ్డుపై నీరు వృధాగా పోతోంది. దయచేసి బాగు చేయండి.', NULL, NULL, NULL, NULL, 'high', NULL, NULL, NULL, 'closed', '3', '2026-10-02 06:24:07', '2026-10-01 11:54:07', '2026-10-01 11:54:08');
INSERT INTO `grievances` (`id`, `tracking_id`, `citizen_name`, `citizen_phone`, `state_id`, `district_id`, `mandal_id`, `gram_panchayat_id`, `village_id`, `ward_id`, `category_id`, `department_id`, `description`, `ai_summary`, `landmark`, `latitude`, `longitude`, `priority`, `ai_suggested_priority`, `ai_confidence`, `ai_duplicate_of_id`, `status`, `assigned_to_user_id`, `sla_due_at`, `created_at`, `updated_at`) VALUES ('25', 'GRV-2026-99637', 'లక్ష్మి నారాయణ (Lakshmi Narayana) (Web)', '9421881912', '1', '2', '5', '5', '1', '2', '1', '1', 'HTTP flow validation - E2E web submit test.', NULL, NULL, NULL, NULL, 'high', NULL, NULL, NULL, 'submitted', NULL, '2026-10-02 06:24:07', '2026-10-01 11:54:07', '2026-10-01 11:54:07');

-- ------------------------------------------------------------
-- Structure for table `mandals`
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `mandals`;
CREATE TABLE `mandals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `district_id` int(11) NOT NULL,
  `code` varchar(20) DEFAULT NULL,
  `name_en` varchar(100) NOT NULL,
  `name_te` varchar(100) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_mandals_district` (`district_id`),
  CONSTRAINT `fk_mandals_district` FOREIGN KEY (`district_id`) REFERENCES `districts` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `mandals` (5 rows)
INSERT INTO `mandals` (`id`, `district_id`, `code`, `name_en`, `name_te`, `is_active`, `created_at`) VALUES ('1', '1', 'PAD', 'Paderu', 'పాడేరు', '1', '2026-10-01 11:18:24');
INSERT INTO `mandals` (`id`, `district_id`, `code`, `name_en`, `name_te`, `is_active`, `created_at`) VALUES ('2', '1', 'ARK', 'Araku Valley', 'అరకు లోయ', '1', '2026-10-01 11:18:24');
INSERT INTO `mandals` (`id`, `district_id`, `code`, `name_en`, `name_te`, `is_active`, `created_at`) VALUES ('3', '1', 'CHP', 'Chintapalle', 'చింతపల్లి', '1', '2026-10-01 11:18:24');
INSERT INTO `mandals` (`id`, `district_id`, `code`, `name_en`, `name_te`, `is_active`, `created_at`) VALUES ('4', '1', 'RPC', 'Rampachodavaram', 'రంపచోడవరం', '1', '2026-10-01 11:18:24');
INSERT INTO `mandals` (`id`, `district_id`, `code`, `name_en`, `name_te`, `is_active`, `created_at`) VALUES ('5', '2', 'CHM', 'Chimakurthy', 'చిమకుర్తి', '1', '2026-10-01 11:18:24');

-- ------------------------------------------------------------
-- Structure for table `rate_limits`
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `rate_limits`;
CREATE TABLE `rate_limits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rate_key` varchar(128) NOT NULL,
  `hits` int(11) NOT NULL DEFAULT 1,
  `reset_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_rate_key` (`rate_key`),
  KEY `idx_rate_reset` (`reset_at`)
) ENGINE=InnoDB AUTO_INCREMENT=120 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `rate_limits` (2 rows)
INSERT INTO `rate_limits` (`id`, `rate_key`, `hits`, `reset_at`, `created_at`) VALUES ('117', 'grievance_submit:127.0.0.1', '1', '2026-10-01 11:59:07', '2026-10-01 11:54:07');
INSERT INTO `rate_limits` (`id`, `rate_key`, `hits`, `reset_at`, `created_at`) VALUES ('118', 'tracking_lookup:127.0.0.1', '2', '2026-10-01 11:55:08', '2026-10-01 11:54:08');

-- ------------------------------------------------------------
-- Structure for table `states`
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `states`;
CREATE TABLE `states` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(10) NOT NULL,
  `name_en` varchar(100) NOT NULL,
  `name_te` varchar(100) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `states` (1 rows)
INSERT INTO `states` (`id`, `code`, `name_en`, `name_te`, `is_active`, `created_at`) VALUES ('1', 'AP', 'Andhra Pradesh', 'ఆంధ్రప్రదేశ్', '1', '2026-10-01 11:18:24');

-- ------------------------------------------------------------
-- Structure for table `users`
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role` enum('admin','staff','officer') NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `department_id` int(11) DEFAULT NULL,
  `village_id` int(11) DEFAULT NULL,
  `state_id` int(11) DEFAULT NULL,
  `district_id` int(11) DEFAULT NULL,
  `mandal_id` int(11) DEFAULT NULL,
  `gram_panchayat_id` int(11) DEFAULT NULL,
  `location_scope` enum('state','district','mandal','gram_panchayat','village') DEFAULT 'village',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `fk_users_department` (`department_id`),
  KEY `fk_users_village` (`village_id`),
  KEY `idx_users_state` (`state_id`),
  KEY `idx_users_district` (`district_id`),
  KEY `idx_users_mandal` (`mandal_id`),
  KEY `idx_users_panchayat` (`gram_panchayat_id`),
  CONSTRAINT `fk_users_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_users_district` FOREIGN KEY (`district_id`) REFERENCES `districts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_users_gram_panchayat` FOREIGN KEY (`gram_panchayat_id`) REFERENCES `gram_panchayats` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_users_mandal` FOREIGN KEY (`mandal_id`) REFERENCES `mandals` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_users_state` FOREIGN KEY (`state_id`) REFERENCES `states` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_users_village` FOREIGN KEY (`village_id`) REFERENCES `villages` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `users` (5 rows)
INSERT INTO `users` (`id`, `role`, `full_name`, `email`, `phone`, `password_hash`, `department_id`, `village_id`, `state_id`, `district_id`, `mandal_id`, `gram_panchayat_id`, `location_scope`, `is_active`, `created_at`, `updated_at`) VALUES ('1', 'admin', 'System Administrator', 'admin@smartgram.local', '9876543210', '$2y$10$XWGgK/w6duX50tUQNyYFEOSMSQaYUyXkXVjimnRMFFG/silWke4ce', NULL, NULL, NULL, NULL, NULL, NULL, 'village', '1', '2026-10-01 11:15:53', '2026-10-01 11:15:53');
INSERT INTO `users` (`id`, `role`, `full_name`, `email`, `phone`, `password_hash`, `department_id`, `village_id`, `state_id`, `district_id`, `mandal_id`, `gram_panchayat_id`, `location_scope`, `is_active`, `created_at`, `updated_at`) VALUES ('2', 'staff', 'Panchayat Secretary (Staff)', 'staff@smartgram.local', '9876543211', '$2y$10$dUN2oMr0YwoH9eX0QVJh7OGLiRuEVT8y6oqnTbQ.BYHzP5G3ypr6q', NULL, '1', NULL, NULL, NULL, NULL, 'village', '1', '2026-10-01 11:15:53', '2026-10-01 11:15:53');
INSERT INTO `users` (`id`, `role`, `full_name`, `email`, `phone`, `password_hash`, `department_id`, `village_id`, `state_id`, `district_id`, `mandal_id`, `gram_panchayat_id`, `location_scope`, `is_active`, `created_at`, `updated_at`) VALUES ('3', 'officer', 'Water Works Field Officer', 'officer@smartgram.local', '9876543212', '$2y$10$PYp.v0j.N8whvBtkdmeE3ObHeUp2FYROMso.Gad4Q60Yt.dj7Bkc.', '1', '1', NULL, NULL, NULL, NULL, 'village', '1', '2026-10-01 11:15:53', '2026-10-01 11:15:53');
INSERT INTO `users` (`id`, `role`, `full_name`, `email`, `phone`, `password_hash`, `department_id`, `village_id`, `state_id`, `district_id`, `mandal_id`, `gram_panchayat_id`, `location_scope`, `is_active`, `created_at`, `updated_at`) VALUES ('4', 'officer', 'Electrical Line Inspector', 'electrical@smartgram.local', '9876543213', '$2y$10$PYp.v0j.N8whvBtkdmeE3ObHeUp2FYROMso.Gad4Q60Yt.dj7Bkc.', '2', '1', NULL, NULL, NULL, NULL, 'village', '1', '2026-10-01 11:15:53', '2026-10-01 11:15:53');
INSERT INTO `users` (`id`, `role`, `full_name`, `email`, `phone`, `password_hash`, `department_id`, `village_id`, `state_id`, `district_id`, `mandal_id`, `gram_panchayat_id`, `location_scope`, `is_active`, `created_at`, `updated_at`) VALUES ('5', 'officer', 'Sanitation & Health Inspector', 'sanitation@smartgram.local', '9876543214', '$2y$10$PYp.v0j.N8whvBtkdmeE3ObHeUp2FYROMso.Gad4Q60Yt.dj7Bkc.', '3', '1', NULL, NULL, NULL, NULL, 'village', '1', '2026-10-01 11:15:53', '2026-10-01 11:15:53');

-- ------------------------------------------------------------
-- Structure for table `villages`
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `villages`;
CREATE TABLE `villages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gram_panchayat_id` int(11) DEFAULT NULL,
  `name_en` varchar(100) NOT NULL,
  `name_te` varchar(100) NOT NULL,
  `mandal` varchar(100) NOT NULL,
  `district` varchar(100) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_villages_panchayat` (`gram_panchayat_id`),
  CONSTRAINT `fk_villages_gram_panchayat` FOREIGN KEY (`gram_panchayat_id`) REFERENCES `gram_panchayats` (`id`) ON DELETE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `villages` (7 rows)
INSERT INTO `villages` (`id`, `gram_panchayat_id`, `name_en`, `name_te`, `mandal`, `district`, `is_active`, `created_at`) VALUES ('1', '5', 'Ramapuram', 'రామాపురం', 'Chimakurthy', 'Prakasam', '1', '2026-10-01 11:15:53');
INSERT INTO `villages` (`id`, `gram_panchayat_id`, `name_en`, `name_te`, `mandal`, `district`, `is_active`, `created_at`) VALUES ('2', '1', 'Paderu Main', 'పాడేరు టౌన్', 'Paderu', 'Alluri Sitharama Raju', '1', '2026-10-01 11:18:24');
INSERT INTO `villages` (`id`, `gram_panchayat_id`, `name_en`, `name_te`, `mandal`, `district`, `is_active`, `created_at`) VALUES ('3', '1', 'Talari', 'తలారి', 'Paderu', 'Alluri Sitharama Raju', '1', '2026-10-01 11:18:24');
INSERT INTO `villages` (`id`, `gram_panchayat_id`, `name_en`, `name_te`, `mandal`, `district`, `is_active`, `created_at`) VALUES ('4', '2', 'Minumuluru Village', 'మినుములూరు గ్రామం', 'Paderu', 'Alluri Sitharama Raju', '1', '2026-10-01 11:18:24');
INSERT INTO `villages` (`id`, `gram_panchayat_id`, `name_en`, `name_te`, `mandal`, `district`, `is_active`, `created_at`) VALUES ('5', '3', 'Araku Town', 'అరకు టౌన్', 'Araku Valley', 'Alluri Sitharama Raju', '1', '2026-10-01 11:18:24');
INSERT INTO `villages` (`id`, `gram_panchayat_id`, `name_en`, `name_te`, `mandal`, `district`, `is_active`, `created_at`) VALUES ('6', '4', 'Lammasingi Village', 'లంబసింగి గ్రామం', 'Chintapalle', 'Alluri Sitharama Raju', '1', '2026-10-01 11:18:24');
INSERT INTO `villages` (`id`, `gram_panchayat_id`, `name_en`, `name_te`, `mandal`, `district`, `is_active`, `created_at`) VALUES ('7', '1', 'Test Scalable Village Updated', 'పరీక్ష గ్రామం నవీకరణ', 'Paderu', 'Alluri Sitharama Raju', '0', '2026-10-01 11:47:31');

-- ------------------------------------------------------------
-- Structure for table `wards`
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `wards`;
CREATE TABLE `wards` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `village_id` int(11) NOT NULL,
  `gram_panchayat_id` int(11) DEFAULT NULL,
  `ward_number` varchar(20) NOT NULL,
  `ward_name` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_wards_village` (`village_id`),
  KEY `idx_wards_panchayat` (`gram_panchayat_id`),
  CONSTRAINT `fk_wards_panchayat` FOREIGN KEY (`gram_panchayat_id`) REFERENCES `gram_panchayats` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_wards_village` FOREIGN KEY (`village_id`) REFERENCES `villages` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `wards` (18 rows)
INSERT INTO `wards` (`id`, `village_id`, `gram_panchayat_id`, `ward_number`, `ward_name`, `is_active`, `created_at`) VALUES ('1', '1', '5', 'Ward 1', 'గాంధీ నగర్ (Gandhi Nagar)', '1', '2026-10-01 11:15:53');
INSERT INTO `wards` (`id`, `village_id`, `gram_panchayat_id`, `ward_number`, `ward_name`, `is_active`, `created_at`) VALUES ('2', '1', '5', 'Ward 2', 'శివాలయం వీధి (Shivalayam Street)', '1', '2026-10-01 11:15:53');
INSERT INTO `wards` (`id`, `village_id`, `gram_panchayat_id`, `ward_number`, `ward_name`, `is_active`, `created_at`) VALUES ('3', '1', '5', 'Ward 3', 'పాఠశాల వీధి (School Street)', '1', '2026-10-01 11:15:53');
INSERT INTO `wards` (`id`, `village_id`, `gram_panchayat_id`, `ward_number`, `ward_name`, `is_active`, `created_at`) VALUES ('4', '1', '5', 'Ward 4', 'బీసీ కాలనీ (BC Colony)', '1', '2026-10-01 11:15:53');
INSERT INTO `wards` (`id`, `village_id`, `gram_panchayat_id`, `ward_number`, `ward_name`, `is_active`, `created_at`) VALUES ('5', '1', '5', 'Ward 5', 'రైతు బజార్ ప్రాంతం (Rythu Bazaar Area)', '1', '2026-10-01 11:15:53');
INSERT INTO `wards` (`id`, `village_id`, `gram_panchayat_id`, `ward_number`, `ward_name`, `is_active`, `created_at`) VALUES ('6', '2', '1', 'Ward 1', 'కోట వీధి / మెయిన్ బజార్ (Fort Street / Main Bazaar)', '1', '2026-10-01 11:18:24');
INSERT INTO `wards` (`id`, `village_id`, `gram_panchayat_id`, `ward_number`, `ward_name`, `is_active`, `created_at`) VALUES ('7', '2', '1', 'Ward 2', 'హాస్పిటల్ రోడ్డు (Hospital Road)', '1', '2026-10-01 11:18:24');
INSERT INTO `wards` (`id`, `village_id`, `gram_panchayat_id`, `ward_number`, `ward_name`, `is_active`, `created_at`) VALUES ('8', '2', '1', 'Ward 3', 'ఆర్టీసీ బస్టాండ్ కాలనీ (RTC Bus Stand Colony)', '1', '2026-10-01 11:18:24');
INSERT INTO `wards` (`id`, `village_id`, `gram_panchayat_id`, `ward_number`, `ward_name`, `is_active`, `created_at`) VALUES ('9', '2', '1', 'Ward 4', 'గిరిజన భవన్ ప్రాంతం (Girijan Bhavan Area)', '1', '2026-10-01 11:18:24');
INSERT INTO `wards` (`id`, `village_id`, `gram_panchayat_id`, `ward_number`, `ward_name`, `is_active`, `created_at`) VALUES ('10', '2', '1', 'Ward 5', 'కొత్తపేట (Kothapeta)', '1', '2026-10-01 11:18:24');
INSERT INTO `wards` (`id`, `village_id`, `gram_panchayat_id`, `ward_number`, `ward_name`, `is_active`, `created_at`) VALUES ('11', '5', '3', 'Ward 1', 'రైల్వే స్టేషన్ కాలనీ (Railway Station Colony)', '1', '2026-10-01 11:18:24');
INSERT INTO `wards` (`id`, `village_id`, `gram_panchayat_id`, `ward_number`, `ward_name`, `is_active`, `created_at`) VALUES ('12', '5', '3', 'Ward 2', 'పద్మాపురం గార్డెన్స్ వీధి (Padmapuram Gardens Street)', '1', '2026-10-01 11:18:24');
INSERT INTO `wards` (`id`, `village_id`, `gram_panchayat_id`, `ward_number`, `ward_name`, `is_active`, `created_at`) VALUES ('13', '6', '4', 'Ward 1', 'చలి కాలనీ (Fog Valley Lane)', '1', '2026-10-01 11:18:24');
INSERT INTO `wards` (`id`, `village_id`, `gram_panchayat_id`, `ward_number`, `ward_name`, `is_active`, `created_at`) VALUES ('14', '6', '4', 'Ward 2', 'కాఫీ తోటల వీధి (Coffee Plantation Road)', '1', '2026-10-01 11:18:24');
INSERT INTO `wards` (`id`, `village_id`, `gram_panchayat_id`, `ward_number`, `ward_name`, `is_active`, `created_at`) VALUES ('15', '3', '1', 'Ward 1', 'మెయిన్ వీధి (Main Street)', '1', '2026-10-01 11:47:16');
INSERT INTO `wards` (`id`, `village_id`, `gram_panchayat_id`, `ward_number`, `ward_name`, `is_active`, `created_at`) VALUES ('16', '3', '1', 'Ward 2', 'కొత్త కాలనీ (New Colony)', '1', '2026-10-01 11:47:16');
INSERT INTO `wards` (`id`, `village_id`, `gram_panchayat_id`, `ward_number`, `ward_name`, `is_active`, `created_at`) VALUES ('17', '4', '2', 'Ward 1', 'కాఫీ తోటల వీధి (Coffee Estate Lane)', '1', '2026-10-01 11:47:16');
INSERT INTO `wards` (`id`, `village_id`, `gram_panchayat_id`, `ward_number`, `ward_name`, `is_active`, `created_at`) VALUES ('18', '4', '2', 'Ward 2', 'పంచాయతీ ఆఫీస్ రోడ్డు (Panchayat Office Road)', '1', '2026-10-01 11:47:16');

-- ------------------------------------------------------------
-- Structure for table `webhook_logs`
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `webhook_logs`;
CREATE TABLE `webhook_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_name` varchar(64) NOT NULL,
  `entity_type` varchar(64) NOT NULL DEFAULT 'grievances',
  `entity_id` int(11) DEFAULT NULL,
  `webhook_url` varchar(500) NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`payload`)),
  `http_status` int(11) DEFAULT NULL,
  `response_body` text DEFAULT NULL,
  `status` enum('success','failed','skipped','simulated') NOT NULL DEFAULT 'skipped',
  `execution_time_ms` decimal(8,2) DEFAULT NULL,
  `error_message` varchar(500) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_webhook_event` (`event_name`),
  KEY `idx_webhook_entity` (`entity_type`,`entity_id`),
  KEY `idx_webhook_status` (`status`),
  KEY `idx_webhook_created` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `webhook_logs` (23 rows)
INSERT INTO `webhook_logs` (`id`, `event_name`, `entity_type`, `entity_id`, `webhook_url`, `payload`, `http_status`, `response_body`, `status`, `execution_time_ms`, `error_message`, `created_at`) VALUES ('1', 'grievance_submitted', 'grievances', '2', 'http://localhost:5678/webhook/smartgram (simulated)', '{\"event\":\"grievance_submitted\",\"timestamp\":\"2026-10-01T06:19:59Z\",\"source\":\"smart-gram\",\"data\":{\"grievance_id\":2,\"tracking_id\":\"GRV-2026-00001\",\"citizen_name\":\"Ramesh Geographic Tester\",\"citizen_phone\":\"9848012345\",\"village_name_en\":\"Paderu Main\",\"village_name_te\":\"పాడేరు టౌన్\",\"ward_number\":\"Ward 1\",\"category_name_en\":\"Pipeline Leakage / Damage\",\"category_name_te\":\"పైపులైన్ లీకేజీ / పగిలిన పైపు\",\"priority\":\"high\",\"status\":\"submitted\",\"tracking_url\":\"http://127.0.0.1:8000/track?tracking_id=GRV-2026-00001&phone=9848012345\",\"messages\":{\"citizen_sms_te\":\"నమస్కారం Ramesh Geographic Tester, మీ సమస్య స్మార్ట్ గ్రామ్ పోర్టల్‌లో నమోదైంది. ఐడీ: GRV-2026-00001. విభాగం: తాగునీటి సరఫరా. స్థితిని తనిఖీ చేయండి: http://127.0.0.1:8000/track?tracking_id=GRV-2026-00001&phone=9848012345\",\"citizen_sms_en\":\"Dear Ramesh Geographic Tester, your grievance GRV-2026-00001 has been registered under Water Supply. Track status anytime at: http://127.0.0.1:8000/track?tracking_id=GRV-2026-00001&phone=9848012345\"}}}', '200', '{\"status\":\"simulated_delivery_ok\"}', 'simulated', '0.00', NULL, '2026-10-01 11:49:59');
INSERT INTO `webhook_logs` (`id`, `event_name`, `entity_type`, `entity_id`, `webhook_url`, `payload`, `http_status`, `response_body`, `status`, `execution_time_ms`, `error_message`, `created_at`) VALUES ('2', 'grievance_submitted', 'grievances', '3', 'http://localhost:5678/webhook/smartgram (simulated)', '{\"event\":\"grievance_submitted\",\"timestamp\":\"2026-10-01T06:22:13Z\",\"source\":\"smart-gram\",\"data\":{\"grievance_id\":3,\"tracking_id\":\"GRV-2026-00002\",\"citizen_name\":\"రామయ్య (Ramaiah)\",\"citizen_phone\":\"9876543210\",\"village_name_en\":\"Ramapuram\",\"village_name_te\":\"రామాపురం\",\"ward_number\":\"Ward 1\",\"category_name_en\":\"Pipeline Leakage / Damage\",\"category_name_te\":\"పైపులైన్ లీకేజీ / పగిలిన పైపు\",\"priority\":\"high\",\"status\":\"submitted\",\"tracking_url\":\"http://127.0.0.1:8000/track?tracking_id=GRV-2026-00002&phone=9876543210\",\"messages\":{\"citizen_sms_te\":\"నమస్కారం రామయ్య (Ramaiah), మీ సమస్య స్మార్ట్ గ్రామ్ పోర్టల్‌లో నమోదైంది. ఐడీ: GRV-2026-00002. విభాగం: తాగునీటి సరఫరా. స్థితిని తనిఖీ చేయండి: http://127.0.0.1:8000/track?tracking_id=GRV-2026-00002&phone=9876543210\",\"citizen_sms_en\":\"Dear రామయ్య (Ramaiah), your grievance GRV-2026-00002 has been registered under Water Supply. Track status anytime at: http://127.0.0.1:8000/track?tracking_id=GRV-2026-00002&phone=9876543210\"}}}', '200', '{\"status\":\"simulated_delivery_ok\"}', 'simulated', '0.00', NULL, '2026-10-01 11:52:13');
INSERT INTO `webhook_logs` (`id`, `event_name`, `entity_type`, `entity_id`, `webhook_url`, `payload`, `http_status`, `response_body`, `status`, `execution_time_ms`, `error_message`, `created_at`) VALUES ('3', 'grievance_submitted', 'grievances', '4', 'http://localhost:5678/webhook/smartgram (simulated)', '{\"event\":\"grievance_submitted\",\"timestamp\":\"2026-10-01T06:22:13Z\",\"source\":\"smart-gram\",\"data\":{\"grievance_id\":4,\"tracking_id\":\"GRV-2026-00003\",\"citizen_name\":\"లక్ష్మి (Lakshmi)\",\"citizen_phone\":\"9876543222\",\"village_name_en\":\"Ramapuram\",\"village_name_te\":\"రామాపురం\",\"ward_number\":\"Ward 2\",\"category_name_en\":\"Street Light Not Working\",\"category_name_te\":\"వీధి దీపం వెలగడం లేదు\",\"priority\":\"medium\",\"status\":\"submitted\",\"tracking_url\":\"http://127.0.0.1:8000/track?tracking_id=GRV-2026-00003&phone=9876543222\",\"messages\":{\"citizen_sms_te\":\"నమస్కారం లక్ష్మి (Lakshmi), మీ సమస్య స్మార్ట్ గ్రామ్ పోర్టల్‌లో నమోదైంది. ఐడీ: GRV-2026-00003. విభాగం: విద్యుత్ & వీధి దీపాలు. స్థితిని తనిఖీ చేయండి: http://127.0.0.1:8000/track?tracking_id=GRV-2026-00003&phone=9876543222\",\"citizen_sms_en\":\"Dear లక్ష్మి (Lakshmi), your grievance GRV-2026-00003 has been registered under Electricity & Street Lights. Track status anytime at: http://127.0.0.1:8000/track?tracking_id=GRV-2026-00003&phone=9876543222\"}}}', '200', '{\"status\":\"simulated_delivery_ok\"}', 'simulated', '0.00', NULL, '2026-10-01 11:52:13');
INSERT INTO `webhook_logs` (`id`, `event_name`, `entity_type`, `entity_id`, `webhook_url`, `payload`, `http_status`, `response_body`, `status`, `execution_time_ms`, `error_message`, `created_at`) VALUES ('4', 'grievance_reopened', 'grievances', '6', 'http://localhost:5678/webhook/smartgram (simulated)', '{\"event\":\"grievance_reopened\",\"timestamp\":\"2026-10-01T06:22:23Z\",\"source\":\"smart-gram\",\"data\":{\"grievance_id\":6,\"tracking_id\":\"GRV-2026-90002\",\"citizen_name\":\"సుబ్బారావు (Subba Rao)\",\"citizen_phone\":\"9876500092\",\"ward_number\":\"Ward 2\",\"reopen_reason\":\"లైట్ ఇంకా వెలగడం లేదు, రాత్రి మళ్లీ చీకటిగా ఉంది. దయచేసి మళ్లీ చూడండి.\",\"status\":\"reopened\",\"priority\":\"high\",\"messages\":{\"staff_alert\":\"REOPEN ALERT: Citizen reopened grievance GRV-2026-90002 in Ward Ward 2. Reason: లైట్ ఇంకా వెలగడం లేదు, రాత్రి మళ్లీ చీకటిగా ఉంది. దయచేసి మళ్లీ చూడండి.\"}}}', '200', '{\"status\":\"simulated_delivery_ok\"}', 'simulated', '0.00', NULL, '2026-10-01 11:52:23');
INSERT INTO `webhook_logs` (`id`, `event_name`, `entity_type`, `entity_id`, `webhook_url`, `payload`, `http_status`, `response_body`, `status`, `execution_time_ms`, `error_message`, `created_at`) VALUES ('5', 'status_changed', 'grievances', '17', 'http://localhost:5678/webhook/smartgram (simulated)', '{\"event\":\"status_changed\",\"timestamp\":\"2026-10-01T06:23:16Z\",\"source\":\"smart-gram\",\"data\":{\"grievance_id\":17,\"tracking_id\":\"GRV-2026-66903\",\"from_status\":\"submitted\",\"to_status\":\"under_review\",\"remarks\":\"Verified on phone call with citizen\"}}', '200', '{\"status\":\"simulated_delivery_ok\"}', 'simulated', '0.00', NULL, '2026-10-01 11:53:16');
INSERT INTO `webhook_logs` (`id`, `event_name`, `entity_type`, `entity_id`, `webhook_url`, `payload`, `http_status`, `response_body`, `status`, `execution_time_ms`, `error_message`, `created_at`) VALUES ('6', 'officer_assigned', 'grievances', '17', 'http://localhost:5678/webhook/smartgram (simulated)', '{\"event\":\"officer_assigned\",\"timestamp\":\"2026-10-01T06:23:17Z\",\"source\":\"smart-gram\",\"data\":{\"grievance_id\":17,\"tracking_id\":\"GRV-2026-66903\",\"citizen_name\":\"P. Workflow Tester\",\"citizen_phone\":\"9848123456\",\"officer_id\":3,\"officer_name\":\"Water Works Field Officer\",\"officer_phone\":\"9876543212\",\"officer_email\":\"officer@smartgram.local\",\"ward_number\":\"Ward 1\",\"priority\":\"critical\",\"status\":\"assigned\",\"tracking_url\":\"http://127.0.0.1:8000/track?tracking_id=GRV-2026-66903&phone=9848123456\",\"messages\":{\"citizen_sms_te\":\"నమస్కారం P. Workflow Tester, మీ సమస్య ({GRV-2026-66903}) ఫీల్డ్ అధికారి Water Works Field Officer గారికి (9876543212) కేటాయించబడింది.\",\"citizen_sms_en\":\"Dear P. Workflow Tester, your grievance (GRV-2026-66903) has been assigned to Field Officer Water Works Field Officer (9876543212).\",\"officer_alert\":\"NEW ASSIGNMENT: Grievance GRV-2026-66903 in Ward Ward 1. Priority: CRITICAL. Citizen: P. Workflow Tester (9848123456).\"}}}', '200', '{\"status\":\"simulated_delivery_ok\"}', 'simulated', '0.00', NULL, '2026-10-01 11:53:17');
INSERT INTO `webhook_logs` (`id`, `event_name`, `entity_type`, `entity_id`, `webhook_url`, `payload`, `http_status`, `response_body`, `status`, `execution_time_ms`, `error_message`, `created_at`) VALUES ('7', 'work_started', 'grievances', '17', 'http://localhost:5678/webhook/smartgram (simulated)', '{\"event\":\"work_started\",\"timestamp\":\"2026-10-01T06:23:17Z\",\"source\":\"smart-gram\",\"data\":{\"grievance_id\":17,\"tracking_id\":\"GRV-2026-66903\",\"citizen_name\":\"P. Workflow Tester\",\"citizen_phone\":\"9848123456\",\"officer_name\":\"Water Works Field Officer\",\"remarks\":\"Arrived on site with welding equipment\",\"status\":\"in_progress\",\"tracking_url\":\"http://127.0.0.1:8000/track?tracking_id=GRV-2026-66903&phone=9848123456\",\"messages\":{\"citizen_sms_te\":\"నమస్కారం P. Workflow Tester, మీ సమస్య (GRV-2026-66903) పై ఫీల్డ్ వర్క్ ప్రారంభమైంది.\",\"citizen_sms_en\":\"Dear P. Workflow Tester, work has commenced on your grievance (GRV-2026-66903).\"}}}', '200', '{\"status\":\"simulated_delivery_ok\"}', 'simulated', '0.00', NULL, '2026-10-01 11:53:17');
INSERT INTO `webhook_logs` (`id`, `event_name`, `entity_type`, `entity_id`, `webhook_url`, `payload`, `http_status`, `response_body`, `status`, `execution_time_ms`, `error_message`, `created_at`) VALUES ('8', 'grievance_resolved', 'grievances', '17', 'http://localhost:5678/webhook/smartgram (simulated)', '{\"event\":\"grievance_resolved\",\"timestamp\":\"2026-10-01T06:23:17Z\",\"source\":\"smart-gram\",\"data\":{\"grievance_id\":17,\"tracking_id\":\"GRV-2026-66903\",\"citizen_name\":\"P. Workflow Tester\",\"citizen_phone\":\"9848123456\",\"officer_name\":\"Water Works Field Officer\",\"resolution_remarks\":\"Leak sealed with new heavy-duty clamp and tested under pressure. Water flow restored.\",\"evidence_photo_url\":\"http://127.0.0.1:8000/storage/uploads/2816cb835a9e6ac93359cf78aaed06d65704df626eac424e70e9b955ea365016.jpg\",\"status\":\"resolved\",\"tracking_url\":\"http://127.0.0.1:8000/track?tracking_id=GRV-2026-66903&phone=9848123456\",\"messages\":{\"citizen_sms_te\":\"నమస్కారం P. Workflow Tester, మీ సమస్య (GRV-2026-66903) పరిష్కరించబడింది! పని పూర్తయిన ఫోటోను పరిశీలించి మీ అభిప్రాయాన్ని తెలపండి: http://127.0.0.1:8000/track?tracking_id=GRV-2026-66903&phone=9848123456\",\"citizen_sms_en\":\"Dear P. Workflow Tester, your grievance (GRV-2026-66903) has been resolved with photo proof! Please review and verify: http://127.0.0.1:8000/track?tracking_id=GRV-2026-66903&phone=9848123456\"}}}', '200', '{\"status\":\"simulated_delivery_ok\"}', 'simulated', '0.00', NULL, '2026-10-01 11:53:17');
INSERT INTO `webhook_logs` (`id`, `event_name`, `entity_type`, `entity_id`, `webhook_url`, `payload`, `http_status`, `response_body`, `status`, `execution_time_ms`, `error_message`, `created_at`) VALUES ('9', 'status_changed', 'grievances', '18', 'http://localhost:5678/webhook/smartgram (simulated)', '{\"event\":\"status_changed\",\"timestamp\":\"2026-10-01T06:23:17Z\",\"source\":\"smart-gram\",\"data\":{\"grievance_id\":18,\"tracking_id\":\"GRV-2026-99633\",\"from_status\":\"submitted\",\"to_status\":\"rejected\",\"remarks\":\"Duplicate submission with invalid contact number.\"}}', '200', '{\"status\":\"simulated_delivery_ok\"}', 'simulated', '0.00', NULL, '2026-10-01 11:53:17');
INSERT INTO `webhook_logs` (`id`, `event_name`, `entity_type`, `entity_id`, `webhook_url`, `payload`, `http_status`, `response_body`, `status`, `execution_time_ms`, `error_message`, `created_at`) VALUES ('15', 'resilience_test', 'test', NULL, 'http://localhost:5678/webhook/smartgram (simulated)', '{\"event\":\"resilience_test\",\"timestamp\":\"2026-10-01T06:23:30Z\",\"source\":\"smart-gram\",\"data\":{\"test\":true}}', '200', '{\"status\":\"simulated_delivery_ok\"}', 'simulated', '0.00', NULL, '2026-10-01 11:53:30');
INSERT INTO `webhook_logs` (`id`, `event_name`, `entity_type`, `entity_id`, `webhook_url`, `payload`, `http_status`, `response_body`, `status`, `execution_time_ms`, `error_message`, `created_at`) VALUES ('17', 'sla_breach_escalation', 'grievances', '7', 'http://localhost:5678/webhook/smartgram (simulated)', '{\"event\":\"sla_breach_escalation\",\"timestamp\":\"2026-10-01T06:23:30Z\",\"source\":\"smart-gram\",\"data\":{\"grievance_id\":7,\"tracking_id\":\"GRV-2026-00010\",\"ward_number\":\"Ward 1\",\"category_name_en\":\"Pipeline Leakage / Damage\",\"category_name_te\":\"పైపులైన్ లీకేజీ / పగిలిన పైపు\",\"department_name\":\"Water Supply\",\"assigned_officer\":\"Unassigned\",\"officer_phone\":\"\",\"priority\":\"critical\",\"status\":\"submitted\",\"hours_overdue\":15,\"sla_due_at\":\"2026-09-30 20:23:01\",\"messages\":{\"admin_alert\":\"SLA BREACH ALERT: Grievance GRV-2026-00010 in Ward Ward 1 is 15h overdue! Immediate intervention required.\"}}}', '200', '{\"status\":\"simulated_delivery_ok\"}', 'simulated', '0.00', NULL, '2026-10-01 11:53:30');
INSERT INTO `webhook_logs` (`id`, `event_name`, `entity_type`, `entity_id`, `webhook_url`, `payload`, `http_status`, `response_body`, `status`, `execution_time_ms`, `error_message`, `created_at`) VALUES ('18', 'sla_breach_escalation', 'grievances', '15', 'http://localhost:5678/webhook/smartgram (simulated)', '{\"event\":\"sla_breach_escalation\",\"timestamp\":\"2026-10-01T06:23:30Z\",\"source\":\"smart-gram\",\"data\":{\"grievance_id\":15,\"tracking_id\":\"GRV-2026-00018\",\"ward_number\":\"Ward 1\",\"category_name_en\":\"Contaminated / Muddy Water\",\"category_name_te\":\"కలుషిత నీరు / మురుగు వాసన\",\"department_name\":\"Water Supply\",\"assigned_officer\":\"Water Works Field Officer\",\"officer_phone\":\"9876543212\",\"priority\":\"critical\",\"status\":\"reopened\",\"hours_overdue\":13,\"sla_due_at\":\"2026-09-30 22:23:01\",\"messages\":{\"admin_alert\":\"SLA BREACH ALERT: Grievance GRV-2026-00018 in Ward Ward 1 is 13h overdue! Immediate intervention required.\"}}}', '200', '{\"status\":\"simulated_delivery_ok\"}', 'simulated', '0.00', NULL, '2026-10-01 11:53:30');
INSERT INTO `webhook_logs` (`id`, `event_name`, `entity_type`, `entity_id`, `webhook_url`, `payload`, `http_status`, `response_body`, `status`, `execution_time_ms`, `error_message`, `created_at`) VALUES ('19', 'sla_breach_escalation', 'grievances', '9', 'http://localhost:5678/webhook/smartgram (simulated)', '{\"event\":\"sla_breach_escalation\",\"timestamp\":\"2026-10-01T06:23:30Z\",\"source\":\"smart-gram\",\"data\":{\"grievance_id\":9,\"tracking_id\":\"GRV-2026-00012\",\"ward_number\":\"Ward 2\",\"category_name_en\":\"Drainage Overflow on Road\",\"category_name_te\":\"రోడ్డుపైకి మురుగునీరు పొంగడం\",\"department_name\":\"Drainage & Sewage\",\"assigned_officer\":\"Sanitation & Health Inspector\",\"officer_phone\":\"9876543214\",\"priority\":\"high\",\"status\":\"assigned\",\"hours_overdue\":9,\"sla_due_at\":\"2026-10-01 02:23:01\",\"messages\":{\"admin_alert\":\"SLA BREACH ALERT: Grievance GRV-2026-00012 in Ward Ward 2 is 9h overdue! Immediate intervention required.\"}}}', '200', '{\"status\":\"simulated_delivery_ok\"}', 'simulated', '0.00', NULL, '2026-10-01 11:53:30');
INSERT INTO `webhook_logs` (`id`, `event_name`, `entity_type`, `entity_id`, `webhook_url`, `payload`, `http_status`, `response_body`, `status`, `execution_time_ms`, `error_message`, `created_at`) VALUES ('20', 'sla_breach_escalation', 'grievances', '10', 'http://localhost:5678/webhook/smartgram (simulated)', '{\"event\":\"sla_breach_escalation\",\"timestamp\":\"2026-10-01T06:23:30Z\",\"source\":\"smart-gram\",\"data\":{\"grievance_id\":10,\"tracking_id\":\"GRV-2026-00013\",\"ward_number\":\"Ward 2\",\"category_name_en\":\"No Water Supply in Tap\",\"category_name_te\":\"కుళాయిల్లో నీరు రాకపోవడం\",\"department_name\":\"Water Supply\",\"assigned_officer\":\"Water Works Field Officer\",\"officer_phone\":\"9876543212\",\"priority\":\"critical\",\"status\":\"in_progress\",\"hours_overdue\":3,\"sla_due_at\":\"2026-10-01 08:23:01\",\"messages\":{\"admin_alert\":\"SLA BREACH ALERT: Grievance GRV-2026-00013 in Ward Ward 2 is 3h overdue! Immediate intervention required.\"}}}', '200', '{\"status\":\"simulated_delivery_ok\"}', 'simulated', '0.00', NULL, '2026-10-01 11:53:30');
INSERT INTO `webhook_logs` (`id`, `event_name`, `entity_type`, `entity_id`, `webhook_url`, `payload`, `http_status`, `response_body`, `status`, `execution_time_ms`, `error_message`, `created_at`) VALUES ('21', 'test_webhook_ping', 'system', NULL, 'http://localhost:5678/webhook/smartgram (simulated)', '{\"event\":\"test_webhook_ping\",\"timestamp\":\"2026-10-01T06:23:30Z\",\"source\":\"smart-gram\",\"data\":{\"message\":\"Manual test ping from Smart Gram admin dashboard\",\"admin_id\":1,\"admin_name\":\"System Administrator\",\"time\":\"2026-10-01 06:23:30\"}}', '200', '{\"status\":\"simulated_delivery_ok\"}', 'simulated', '0.00', NULL, '2026-10-01 11:53:30');
INSERT INTO `webhook_logs` (`id`, `event_name`, `entity_type`, `entity_id`, `webhook_url`, `payload`, `http_status`, `response_body`, `status`, `execution_time_ms`, `error_message`, `created_at`) VALUES ('22', 'grievance_submitted', 'grievances', '22', 'http://localhost:5678/webhook/smartgram (simulated)', '{\"event\":\"grievance_submitted\",\"timestamp\":\"2026-10-01T06:23:37Z\",\"source\":\"smart-gram\",\"data\":{\"grievance_id\":22,\"tracking_id\":\"GRV-2026-99634\",\"citizen_name\":\"Kiran <script>alert(\\\"xss-name\\\")</script>\",\"citizen_phone\":\"9988776655\",\"village_name_en\":\"Ramapuram\",\"village_name_te\":\"రామాపురం\",\"ward_number\":\"Ward 1\",\"category_name_en\":\"Pipeline Leakage / Damage\",\"category_name_te\":\"పైపులైన్ లీకేజీ / పగిలిన పైపు\",\"priority\":\"high\",\"status\":\"submitted\",\"tracking_url\":\"http://127.0.0.1:8000/track?tracking_id=GRV-2026-99634&phone=9988776655\",\"messages\":{\"citizen_sms_te\":\"నమస్కారం Kiran <script>alert(\\\"xss-name\\\")</script>, మీ సమస్య స్మార్ట్ గ్రామ్ పోర్టల్‌లో నమోదైంది. ఐడీ: GRV-2026-99634. విభాగం: తాగునీటి సరఫరా. స్థితిని తనిఖీ చేయండి: http://127.0.0.1:8000/track?tracking_id=GRV-2026-99634&phone=9988776655\",\"citizen_sms_en\":\"Dear Kiran <script>alert(\\\"xss-name\\\")</script>, your grievance GRV-2026-99634 has been registered under Water Supply. Track status anytime at: http://127.0.0.1:8000/track?tracking_id=GRV-2026-99634&phone=9988776655\"}}}', '200', '{\"status\":\"simulated_delivery_ok\"}', 'simulated', '0.00', NULL, '2026-10-01 11:53:37');
INSERT INTO `webhook_logs` (`id`, `event_name`, `entity_type`, `entity_id`, `webhook_url`, `payload`, `http_status`, `response_body`, `status`, `execution_time_ms`, `error_message`, `created_at`) VALUES ('23', 'grievance_submitted', 'grievances', '23', 'http://localhost:5678/webhook/smartgram (simulated)', '{\"event\":\"grievance_submitted\",\"timestamp\":\"2026-10-01T06:24:06Z\",\"source\":\"smart-gram\",\"data\":{\"grievance_id\":23,\"tracking_id\":\"GRV-2026-99635\",\"citizen_name\":\"Kiran <script>alert(\\\"xss-name\\\")</script>\",\"citizen_phone\":\"9988776655\",\"village_name_en\":\"Ramapuram\",\"village_name_te\":\"రామాపురం\",\"ward_number\":\"Ward 1\",\"category_name_en\":\"Pipeline Leakage / Damage\",\"category_name_te\":\"పైపులైన్ లీకేజీ / పగిలిన పైపు\",\"priority\":\"high\",\"status\":\"submitted\",\"tracking_url\":\"http://127.0.0.1:8000/track?tracking_id=GRV-2026-99635&phone=9988776655\",\"messages\":{\"citizen_sms_te\":\"నమస్కారం Kiran <script>alert(\\\"xss-name\\\")</script>, మీ సమస్య స్మార్ట్ గ్రామ్ పోర్టల్‌లో నమోదైంది. ఐడీ: GRV-2026-99635. విభాగం: తాగునీటి సరఫరా. స్థితిని తనిఖీ చేయండి: http://127.0.0.1:8000/track?tracking_id=GRV-2026-99635&phone=9988776655\",\"citizen_sms_en\":\"Dear Kiran <script>alert(\\\"xss-name\\\")</script>, your grievance GRV-2026-99635 has been registered under Water Supply. Track status anytime at: http://127.0.0.1:8000/track?tracking_id=GRV-2026-99635&phone=9988776655\"}}}', '200', '{\"status\":\"simulated_delivery_ok\"}', 'simulated', '0.00', NULL, '2026-10-01 11:54:06');
INSERT INTO `webhook_logs` (`id`, `event_name`, `entity_type`, `entity_id`, `webhook_url`, `payload`, `http_status`, `response_body`, `status`, `execution_time_ms`, `error_message`, `created_at`) VALUES ('24', 'grievance_submitted', 'grievances', '24', 'http://localhost:5678/webhook/smartgram (simulated)', '{\"event\":\"grievance_submitted\",\"timestamp\":\"2026-10-01T06:24:07Z\",\"source\":\"smart-gram\",\"data\":{\"grievance_id\":24,\"tracking_id\":\"GRV-2026-99636\",\"citizen_name\":\"లక్ష్మి నారాయణ (Lakshmi Narayana)\",\"citizen_phone\":\"9440171629\",\"village_name_en\":\"Ramapuram\",\"village_name_te\":\"రామాపురం\",\"ward_number\":\"Ward 2\",\"category_name_en\":\"Pipeline Leakage / Damage\",\"category_name_te\":\"పైపులైన్ లీకేజీ / పగిలిన పైపు\",\"priority\":\"high\",\"status\":\"submitted\",\"tracking_url\":\"http://127.0.0.1:8000/track?tracking_id=GRV-2026-99636&phone=9440171629\",\"messages\":{\"citizen_sms_te\":\"నమస్కారం లక్ష్మి నారాయణ (Lakshmi Narayana), మీ సమస్య స్మార్ట్ గ్రామ్ పోర్టల్‌లో నమోదైంది. ఐడీ: GRV-2026-99636. విభాగం: తాగునీటి సరఫరా. స్థితిని తనిఖీ చేయండి: http://127.0.0.1:8000/track?tracking_id=GRV-2026-99636&phone=9440171629\",\"citizen_sms_en\":\"Dear లక్ష్మి నారాయణ (Lakshmi Narayana), your grievance GRV-2026-99636 has been registered under Water Supply. Track status anytime at: http://127.0.0.1:8000/track?tracking_id=GRV-2026-99636&phone=9440171629\"}}}', '200', '{\"status\":\"simulated_delivery_ok\"}', 'simulated', '0.00', NULL, '2026-10-01 11:54:07');
INSERT INTO `webhook_logs` (`id`, `event_name`, `entity_type`, `entity_id`, `webhook_url`, `payload`, `http_status`, `response_body`, `status`, `execution_time_ms`, `error_message`, `created_at`) VALUES ('25', 'grievance_submitted', 'grievances', '25', 'http://localhost:5678/webhook/smartgram (simulated)', '{\"event\":\"grievance_submitted\",\"timestamp\":\"2026-10-01T06:24:07Z\",\"source\":\"smart-gram\",\"data\":{\"grievance_id\":25,\"tracking_id\":\"GRV-2026-99637\",\"citizen_name\":\"లక్ష్మి నారాయణ (Lakshmi Narayana) (Web)\",\"citizen_phone\":\"9421881912\",\"village_name_en\":\"Ramapuram\",\"village_name_te\":\"రామాపురం\",\"ward_number\":\"Ward 2\",\"category_name_en\":\"Pipeline Leakage / Damage\",\"category_name_te\":\"పైపులైన్ లీకేజీ / పగిలిన పైపు\",\"priority\":\"high\",\"status\":\"submitted\",\"tracking_url\":\"http://127.0.0.1:8000/track?tracking_id=GRV-2026-99637&phone=9421881912\",\"messages\":{\"citizen_sms_te\":\"నమస్కారం లక్ష్మి నారాయణ (Lakshmi Narayana) (Web), మీ సమస్య స్మార్ట్ గ్రామ్ పోర్టల్‌లో నమోదైంది. ఐడీ: GRV-2026-99637. విభాగం: తాగునీటి సరఫరా. స్థితిని తనిఖీ చేయండి: http://127.0.0.1:8000/track?tracking_id=GRV-2026-99637&phone=9421881912\",\"citizen_sms_en\":\"Dear లక్ష్మి నారాయణ (Lakshmi Narayana) (Web), your grievance GRV-2026-99637 has been registered under Water Supply. Track status anytime at: http://127.0.0.1:8000/track?tracking_id=GRV-2026-99637&phone=9421881912\"}}}', '200', '{\"status\":\"simulated_delivery_ok\"}', 'simulated', '0.00', NULL, '2026-10-01 11:54:07');
INSERT INTO `webhook_logs` (`id`, `event_name`, `entity_type`, `entity_id`, `webhook_url`, `payload`, `http_status`, `response_body`, `status`, `execution_time_ms`, `error_message`, `created_at`) VALUES ('26', 'status_changed', 'grievances', '24', 'http://localhost:5678/webhook/smartgram (simulated)', '{\"event\":\"status_changed\",\"timestamp\":\"2026-10-01T06:24:07Z\",\"source\":\"smart-gram\",\"data\":{\"grievance_id\":24,\"tracking_id\":\"GRV-2026-99636\",\"from_status\":\"submitted\",\"to_status\":\"under_review\",\"remarks\":\"క్షేత్రస్థాయిలో సమస్య ధృవీకరించబడింది. తక్షణ మరమ్మతు అవసరం.\"}}', '200', '{\"status\":\"simulated_delivery_ok\"}', 'simulated', '0.00', NULL, '2026-10-01 11:54:07');
INSERT INTO `webhook_logs` (`id`, `event_name`, `entity_type`, `entity_id`, `webhook_url`, `payload`, `http_status`, `response_body`, `status`, `execution_time_ms`, `error_message`, `created_at`) VALUES ('27', 'officer_assigned', 'grievances', '24', 'http://localhost:5678/webhook/smartgram (simulated)', '{\"event\":\"officer_assigned\",\"timestamp\":\"2026-10-01T06:24:07Z\",\"source\":\"smart-gram\",\"data\":{\"grievance_id\":24,\"tracking_id\":\"GRV-2026-99636\",\"citizen_name\":\"లక్ష్మి నారాయణ (Lakshmi Narayana)\",\"citizen_phone\":\"9440171629\",\"officer_id\":3,\"officer_name\":\"Water Works Field Officer\",\"officer_phone\":\"9876543212\",\"officer_email\":\"officer@smartgram.local\",\"ward_number\":\"Ward 2\",\"priority\":\"high\",\"status\":\"assigned\",\"tracking_url\":\"http://127.0.0.1:8000/track?tracking_id=GRV-2026-99636&phone=9440171629\",\"messages\":{\"citizen_sms_te\":\"నమస్కారం లక్ష్మి నారాయణ (Lakshmi Narayana), మీ సమస్య ({GRV-2026-99636}) ఫీల్డ్ అధికారి Water Works Field Officer గారికి (9876543212) కేటాయించబడింది.\",\"citizen_sms_en\":\"Dear లక్ష్మి నారాయణ (Lakshmi Narayana), your grievance (GRV-2026-99636) has been assigned to Field Officer Water Works Field Officer (9876543212).\",\"officer_alert\":\"NEW ASSIGNMENT: Grievance GRV-2026-99636 in Ward Ward 2. Priority: HIGH. Citizen: లక్ష్మి నారాయణ (Lakshmi Narayana) (9440171629).\"}}}', '200', '{\"status\":\"simulated_delivery_ok\"}', 'simulated', '0.00', NULL, '2026-10-01 11:54:07');
INSERT INTO `webhook_logs` (`id`, `event_name`, `entity_type`, `entity_id`, `webhook_url`, `payload`, `http_status`, `response_body`, `status`, `execution_time_ms`, `error_message`, `created_at`) VALUES ('28', 'work_started', 'grievances', '24', 'http://localhost:5678/webhook/smartgram (simulated)', '{\"event\":\"work_started\",\"timestamp\":\"2026-10-01T06:24:08Z\",\"source\":\"smart-gram\",\"data\":{\"grievance_id\":24,\"tracking_id\":\"GRV-2026-99636\",\"citizen_name\":\"లక్ష్మి నారాయణ (Lakshmi Narayana)\",\"citizen_phone\":\"9440171629\",\"officer_name\":\"Water Works Field Officer\",\"remarks\":\"మరమ్మతు పనులు ప్రారంభించబడ్డాయి. కొత్త పైప్‌లైన్ అమర్చుతున్నాము.\",\"status\":\"in_progress\",\"tracking_url\":\"http://127.0.0.1:8000/track?tracking_id=GRV-2026-99636&phone=9440171629\",\"messages\":{\"citizen_sms_te\":\"నమస్కారం లక్ష్మి నారాయణ (Lakshmi Narayana), మీ సమస్య (GRV-2026-99636) పై ఫీల్డ్ వర్క్ ప్రారంభమైంది.\",\"citizen_sms_en\":\"Dear లక్ష్మి నారాయణ (Lakshmi Narayana), work has commenced on your grievance (GRV-2026-99636).\"}}}', '200', '{\"status\":\"simulated_delivery_ok\"}', 'simulated', '0.00', NULL, '2026-10-01 11:54:08');
INSERT INTO `webhook_logs` (`id`, `event_name`, `entity_type`, `entity_id`, `webhook_url`, `payload`, `http_status`, `response_body`, `status`, `execution_time_ms`, `error_message`, `created_at`) VALUES ('29', 'grievance_resolved', 'grievances', '24', 'http://localhost:5678/webhook/smartgram (simulated)', '{\"event\":\"grievance_resolved\",\"timestamp\":\"2026-10-01T06:24:08Z\",\"source\":\"smart-gram\",\"data\":{\"grievance_id\":24,\"tracking_id\":\"GRV-2026-99636\",\"citizen_name\":\"లక్ష్మి నారాయణ (Lakshmi Narayana)\",\"citizen_phone\":\"9440171629\",\"officer_name\":\"Water Works Field Officer\",\"resolution_remarks\":\"పైప్‌లైన్ లీకేజీని అరికట్టి కొత్త వాల్వ్ విజయవంతంగా బిగించడమైనది. మంచినీటి సరఫరా పునరుద్ధరించబడింది.\",\"evidence_photo_url\":\"http://127.0.0.1:8000/storage/uploads/017d2e51aa3fa561de0d8ba2759d59a1b24f85e4e20c1ad9eed5d7cc6320eb7b.jpg\",\"status\":\"resolved\",\"tracking_url\":\"http://127.0.0.1:8000/track?tracking_id=GRV-2026-99636&phone=9440171629\",\"messages\":{\"citizen_sms_te\":\"నమస్కారం లక్ష్మి నారాయణ (Lakshmi Narayana), మీ సమస్య (GRV-2026-99636) పరిష్కరించబడింది! పని పూర్తయిన ఫోటోను పరిశీలించి మీ అభిప్రాయాన్ని తెలపండి: http://127.0.0.1:8000/track?tracking_id=GRV-2026-99636&phone=9440171629\",\"citizen_sms_en\":\"Dear లక్ష్మి నారాయణ (Lakshmi Narayana), your grievance (GRV-2026-99636) has been resolved with photo proof! Please review and verify: http://127.0.0.1:8000/track?tracking_id=GRV-2026-99636&phone=9440171629\"}}}', '200', '{\"status\":\"simulated_delivery_ok\"}', 'simulated', '0.00', NULL, '2026-10-01 11:54:08');

SET FOREIGN_KEY_CHECKS=1;
-- End of Smart Gram Backup
