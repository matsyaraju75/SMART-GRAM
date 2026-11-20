<?php
// ==============================================================================
// Smart Gram - Geographic Hierarchy Database Migration
// Adds: states, districts, mandals, gram_panchayats
// Updates: villages, wards, grievances, users with relational foreign keys
// ==============================================================================

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../app/core/Database.php';

use App\Core\Database;

try {
    $db = Database::getConnection();
    echo "Starting Geographic Hierarchy Migration...\n";

    // Disable foreign key checks during migration
    $db->exec("SET FOREIGN_KEY_CHECKS = 0;");

    // 1. Create `states` table
    $db->exec("
        CREATE TABLE IF NOT EXISTS `states` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `code` VARCHAR(10) UNIQUE NOT NULL,
            `name_en` VARCHAR(100) NOT NULL,
            `name_te` VARCHAR(100) NOT NULL,
            `is_active` TINYINT(1) DEFAULT 1,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "Table `states` created or verified.\n";

    // 2. Create `districts` table
    $db->exec("
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
    ");
    echo "Table `districts` created or verified.\n";

    // 3. Create `mandals` table
    $db->exec("
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
    ");
    echo "Table `mandals` created or verified.\n";

    // 4. Create `gram_panchayats` table
    $db->exec("
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
    ");
    echo "Table `gram_panchayats` created or verified.\n";

    // 5. Update `villages` table to add `gram_panchayat_id` if missing
    $stmt = $db->query("SHOW COLUMNS FROM `villages` LIKE 'gram_panchayat_id'");
    if (!$stmt->fetch()) {
        $db->exec("
            ALTER TABLE `villages` 
            ADD COLUMN `gram_panchayat_id` INT NULL AFTER `id`,
            ADD INDEX `idx_villages_panchayat` (`gram_panchayat_id`),
            ADD CONSTRAINT `fk_villages_gram_panchayat` FOREIGN KEY (`gram_panchayat_id`) REFERENCES `gram_panchayats` (`id`) ON DELETE RESTRICT;
        ");
        echo "Column `gram_panchayat_id` added to `villages`.\n";
    }

    // 6. Update `wards` table to add `gram_panchayat_id` and `is_active` if missing
    $stmt = $db->query("SHOW COLUMNS FROM `wards` LIKE 'gram_panchayat_id'");
    if (!$stmt->fetch()) {
        $db->exec("
            ALTER TABLE `wards` 
            ADD COLUMN `gram_panchayat_id` INT NULL AFTER `village_id`,
            ADD COLUMN `is_active` TINYINT(1) DEFAULT 1 AFTER `ward_name`,
            ADD INDEX `idx_wards_panchayat` (`gram_panchayat_id`),
            ADD CONSTRAINT `fk_wards_panchayat` FOREIGN KEY (`gram_panchayat_id`) REFERENCES `gram_panchayats` (`id`) ON DELETE SET NULL;
        ");
        echo "Columns `gram_panchayat_id` and `is_active` added to `wards`.\n";
    }

    // 7. Update `grievances` table to add geographic hierarchy foreign keys
    $stmt = $db->query("SHOW COLUMNS FROM `grievances` LIKE 'state_id'");
    if (!$stmt->fetch()) {
        $db->exec("
            ALTER TABLE `grievances`
            ADD COLUMN `state_id` INT NULL AFTER `citizen_phone`,
            ADD COLUMN `district_id` INT NULL AFTER `state_id`,
            ADD COLUMN `mandal_id` INT NULL AFTER `district_id`,
            ADD COLUMN `gram_panchayat_id` INT NULL AFTER `mandal_id`,
            ADD INDEX `idx_grievances_state` (`state_id`),
            ADD INDEX `idx_grievances_district` (`district_id`),
            ADD INDEX `idx_grievances_mandal` (`mandal_id`),
            ADD INDEX `idx_grievances_panchayat` (`gram_panchayat_id`),
            ADD CONSTRAINT `fk_grievances_state` FOREIGN KEY (`state_id`) REFERENCES `states` (`id`) ON DELETE RESTRICT,
            ADD CONSTRAINT `fk_grievances_district` FOREIGN KEY (`district_id`) REFERENCES `districts` (`id`) ON DELETE RESTRICT,
            ADD CONSTRAINT `fk_grievances_mandal` FOREIGN KEY (`mandal_id`) REFERENCES `mandals` (`id`) ON DELETE RESTRICT,
            ADD CONSTRAINT `fk_grievances_gram_panchayat` FOREIGN KEY (`gram_panchayat_id`) REFERENCES `gram_panchayats` (`id`) ON DELETE RESTRICT;
        ");
        echo "Geographic hierarchy columns added to `grievances`.\n";
    }

    // 8. Update `users` table to add location scope fields
    $stmt = $db->query("SHOW COLUMNS FROM `users` LIKE 'state_id'");
    if (!$stmt->fetch()) {
        $db->exec("
            ALTER TABLE `users`
            ADD COLUMN `state_id` INT NULL AFTER `village_id`,
            ADD COLUMN `district_id` INT NULL AFTER `state_id`,
            ADD COLUMN `mandal_id` INT NULL AFTER `district_id`,
            ADD COLUMN `gram_panchayat_id` INT NULL AFTER `mandal_id`,
            ADD COLUMN `location_scope` ENUM('state', 'district', 'mandal', 'gram_panchayat', 'village') DEFAULT 'village' AFTER `gram_panchayat_id`,
            ADD INDEX `idx_users_state` (`state_id`),
            ADD INDEX `idx_users_district` (`district_id`),
            ADD INDEX `idx_users_mandal` (`mandal_id`),
            ADD INDEX `idx_users_panchayat` (`gram_panchayat_id`),
            ADD CONSTRAINT `fk_users_state` FOREIGN KEY (`state_id`) REFERENCES `states` (`id`) ON DELETE SET NULL,
            ADD CONSTRAINT `fk_users_district` FOREIGN KEY (`district_id`) REFERENCES `districts` (`id`) ON DELETE SET NULL,
            ADD CONSTRAINT `fk_users_mandal` FOREIGN KEY (`mandal_id`) REFERENCES `mandals` (`id`) ON DELETE SET NULL,
            ADD CONSTRAINT `fk_users_gram_panchayat` FOREIGN KEY (`gram_panchayat_id`) REFERENCES `gram_panchayats` (`id`) ON DELETE SET NULL;
        ");
        echo "Location scope columns added to `users`.\n";
    }

    // Re-enable foreign key checks
    $db->exec("SET FOREIGN_KEY_CHECKS = 1;");

    // 9. Seed Master Geographic Hierarchy Data
    echo "Seeding Master Geographic Hierarchy...\n";

    // 9.1 State: Andhra Pradesh
    $db->exec("
        INSERT INTO `states` (`id`, `code`, `name_en`, `name_te`, `is_active`) VALUES
        (1, 'AP', 'Andhra Pradesh', 'ఆంధ్రప్రదేశ్', 1)
        ON DUPLICATE KEY UPDATE `code`=VALUES(`code`), `name_en`=VALUES(`name_en`), `name_te`=VALUES(`name_te`);
    ");

    // 9.2 Districts: Alluri Sitharama Raju (Initial Scope) & Prakasam (for backwards compatibility)
    $db->exec("
        INSERT INTO `districts` (`id`, `state_id`, `code`, `name_en`, `name_te`, `is_active`) VALUES
        (1, 1, 'ASR', 'Alluri Sitharama Raju', 'అల్లూరి సీతారామరాజు', 1),
        (2, 1, 'PRAKASAM', 'Prakasam', 'ప్రకాశం', 1)
        ON DUPLICATE KEY UPDATE `state_id`=VALUES(`state_id`), `name_en`=VALUES(`name_en`), `name_te`=VALUES(`name_te`);
    ");

    // 9.3 Mandals under Alluri Sitharama Raju & Prakasam
    $db->exec("
        INSERT INTO `mandals` (`id`, `district_id`, `code`, `name_en`, `name_te`, `is_active`) VALUES
        (1, 1, 'PAD', 'Paderu', 'పాడేరు', 1),
        (2, 1, 'ARK', 'Araku Valley', 'అరకు లోయ', 1),
        (3, 1, 'CHP', 'Chintapalle', 'చింతపల్లి', 1),
        (4, 1, 'RPC', 'Rampachodavaram', 'రంపచోడవరం', 1),
        (5, 2, 'CHM', 'Chimakurthy', 'చిమకుర్తి', 1)
        ON DUPLICATE KEY UPDATE `district_id`=VALUES(`district_id`), `name_en`=VALUES(`name_en`), `name_te`=VALUES(`name_te`);
    ");

    // 9.4 Gram Panchayats
    $db->exec("
        INSERT INTO `gram_panchayats` (`id`, `mandal_id`, `code`, `name_en`, `name_te`, `is_active`) VALUES
        (1, 1, 'GP_PAD', 'Paderu', 'పాడేరు', 1),
        (2, 1, 'GP_MIN', 'Minumuluru', 'మినుములూరు', 1),
        (3, 2, 'GP_ARK', 'Araku', 'అరకు', 1),
        (4, 3, 'GP_LMS', 'Lammasingi', 'లంబసింగి', 1),
        (5, 5, 'GP_RAM', 'Ramapuram', 'రామాపురం', 1)
        ON DUPLICATE KEY UPDATE `mandal_id`=VALUES(`mandal_id`), `name_en`=VALUES(`name_en`), `name_te`=VALUES(`name_te`);
    ");

    // 9.5 Villages (linking to Gram Panchayats)
    $db->exec("
        INSERT INTO `villages` (`id`, `gram_panchayat_id`, `name_en`, `name_te`, `mandal`, `district`, `is_active`) VALUES
        (1, 5, 'Ramapuram', 'రామాపురం', 'Chimakurthy', 'Prakasam', 1),
        (2, 1, 'Paderu Main', 'పాడేరు టౌన్', 'Paderu', 'Alluri Sitharama Raju', 1),
        (3, 1, 'Talari', 'తలారి', 'Paderu', 'Alluri Sitharama Raju', 1),
        (4, 2, 'Minumuluru Village', 'మినుములూరు గ్రామం', 'Paderu', 'Alluri Sitharama Raju', 1),
        (5, 3, 'Araku Town', 'అరకు టౌన్', 'Araku Valley', 'Alluri Sitharama Raju', 1),
        (6, 4, 'Lammasingi Village', 'లంబసింగి గ్రామం', 'Chintapalle', 'Alluri Sitharama Raju', 1)
        ON DUPLICATE KEY UPDATE 
            `gram_panchayat_id`=VALUES(`gram_panchayat_id`), 
            `name_en`=VALUES(`name_en`), 
            `name_te`=VALUES(`name_te`),
            `mandal`=VALUES(`mandal`),
            `district`=VALUES(`district`);
    ");

    // 9.6 Wards under Paderu, Araku, Lammasingi
    $db->exec("
        INSERT INTO `wards` (`id`, `village_id`, `gram_panchayat_id`, `ward_number`, `ward_name`, `is_active`) VALUES
        (6, 2, 1, 'Ward 1', 'కోట వీధి / మెయిన్ బజార్ (Fort Street / Main Bazaar)', 1),
        (7, 2, 1, 'Ward 2', 'హాస్పిటల్ రోడ్డు (Hospital Road)', 1),
        (8, 2, 1, 'Ward 3', 'ఆర్టీసీ బస్టాండ్ కాలనీ (RTC Bus Stand Colony)', 1),
        (9, 2, 1, 'Ward 4', 'గిరిజన భవన్ ప్రాంతం (Girijan Bhavan Area)', 1),
        (10, 2, 1, 'Ward 5', 'కొత్తపేట (Kothapeta)', 1),
        (11, 5, 3, 'Ward 1', 'రైల్వే స్టేషన్ కాలనీ (Railway Station Colony)', 1),
        (12, 5, 3, 'Ward 2', 'పద్మాపురం గార్డెన్స్ వీధి (Padmapuram Gardens Street)', 1),
        (13, 6, 4, 'Ward 1', 'చలి కాలనీ (Fog Valley Lane)', 1),
        (14, 6, 4, 'Ward 2', 'కాఫీ తోటల వీధి (Coffee Plantation Road)', 1)
        ON DUPLICATE KEY UPDATE 
            `village_id`=VALUES(`village_id`), 
            `gram_panchayat_id`=VALUES(`gram_panchayat_id`),
            `ward_number`=VALUES(`ward_number`),
            `ward_name`=VALUES(`ward_name`);
    ");

    // 9.7 Backfill any existing grievances with their corresponding hierarchy IDs
    $db->exec("
        UPDATE `grievances` g
        JOIN `villages` v ON g.village_id = v.id
        JOIN `gram_panchayats` gp ON v.gram_panchayat_id = gp.id
        JOIN `mandals` m ON gp.mandal_id = m.id
        JOIN `districts` d ON m.district_id = d.id
        JOIN `states` s ON d.state_id = s.id
        SET g.state_id = s.id,
            g.district_id = d.id,
            g.mandal_id = m.id,
            g.gram_panchayat_id = gp.id
        WHERE g.gram_panchayat_id IS NULL;
    ");
    echo "Existing grievances backfilled with parent geographic IDs.\n";

    echo "Geographic Hierarchy Migration Completed Successfully!\n";

} catch (Exception $e) {
    echo "Migration Error: " . $e->getMessage() . "\n";
    exit(1);
}
