-- ==============================================================================
-- Smart Gram (గ్రామ సమస్య పరిష్కార వేదిక) - Seed Data
-- ==============================================================================

-- 1. States
INSERT INTO `states` (`id`, `code`, `name_en`, `name_te`, `is_active`) VALUES
(1, 'AP', 'Andhra Pradesh', 'ఆంధ్రప్రదేశ్', 1)
ON DUPLICATE KEY UPDATE `code`=VALUES(`code`), `name_en`=VALUES(`name_en`), `name_te`=VALUES(`name_te`);

-- 2. Districts (Initial Target: Alluri Sitharama Raju; Prakasam for legacy support)
INSERT INTO `districts` (`id`, `state_id`, `code`, `name_en`, `name_te`, `is_active`) VALUES
(1, 1, 'ASR', 'Alluri Sitharama Raju', 'అల్లూరి సీతారామరాజు', 1),
(2, 1, 'PRAKASAM', 'Prakasam', 'ప్రకాశం', 1)
ON DUPLICATE KEY UPDATE `state_id`=VALUES(`state_id`), `name_en`=VALUES(`name_en`), `name_te`=VALUES(`name_te`);

-- 3. Mandals
INSERT INTO `mandals` (`id`, `district_id`, `code`, `name_en`, `name_te`, `is_active`) VALUES
(1, 1, 'PAD', 'Paderu', 'పాడేరు', 1),
(2, 1, 'ARK', 'Araku Valley', 'అరకు లోయ', 1),
(3, 1, 'CHP', 'Chintapalle', 'చింతపల్లి', 1),
(4, 1, 'RPC', 'Rampachodavaram', 'రంపచోడవరం', 1),
(5, 2, 'CHM', 'Chimakurthy', 'చిమకుర్తి', 1)
ON DUPLICATE KEY UPDATE `district_id`=VALUES(`district_id`), `name_en`=VALUES(`name_en`), `name_te`=VALUES(`name_te`);

-- 4. Gram Panchayats
INSERT INTO `gram_panchayats` (`id`, `mandal_id`, `code`, `name_en`, `name_te`, `is_active`) VALUES
(1, 1, 'GP_PAD', 'Paderu', 'పాడేరు', 1),
(2, 1, 'GP_MIN', 'Minumuluru', 'మినుములూరు', 1),
(3, 2, 'GP_ARK', 'Araku', 'అరకు', 1),
(4, 3, 'GP_LMS', 'Lammasingi', 'లంబసింగి', 1),
(5, 5, 'GP_RAM', 'Ramapuram', 'రామాపురం', 1)
ON DUPLICATE KEY UPDATE `mandal_id`=VALUES(`mandal_id`), `name_en`=VALUES(`name_en`), `name_te`=VALUES(`name_te`);

-- 5. Villages
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

-- 6. Wards
INSERT INTO `wards` (`id`, `village_id`, `gram_panchayat_id`, `ward_number`, `ward_name`, `is_active`) VALUES
(1, 1, 5, 'Ward 1', 'గాంధీ నగర్ (Gandhi Nagar)', 1),
(2, 1, 5, 'Ward 2', 'శివాలయం వీధి (Shivalayam Street)', 1),
(3, 1, 5, 'Ward 3', 'పాఠశాల వీధి (School Street)', 1),
(4, 1, 5, 'Ward 4', 'బీసీ కాలనీ (BC Colony)', 1),
(5, 1, 5, 'Ward 5', 'రైతు బజార్ ప్రాంతం (Rythu Bazaar Area)', 1),
(6, 2, 1, 'Ward 1', 'కోట వీధి / మెయిన్ బజార్ (Fort Street / Main Bazaar)', 1),
(7, 2, 1, 'Ward 2', 'హాస్పిటల్ రోడ్డు (Hospital Road)', 1),
(8, 2, 1, 'Ward 3', 'ఆర్టీసీ బస్టాండ్ కాలనీ (RTC Bus Stand Colony)', 1),
(9, 2, 1, 'Ward 4', 'గిరిజన భవన్ ప్రాంతం (Girijan Bhavan Area)', 1),
(10, 2, 1, 'Ward 5', 'కొత్తపేట (Kothapeta)', 1),
(11, 5, 3, 'Ward 1', 'రైల్వే స్టేషన్ కాలనీ (Railway Station Colony)', 1),
(12, 5, 3, 'Ward 2', 'పద్మాపురం గార్డెన్స్ వీధి (Padmapuram Gardens Street)', 1),
(13, 6, 4, 'Ward 1', 'చలి కాలనీ (Fog Valley Lane)', 1),
(14, 6, 4, 'Ward 2', 'కాఫీ తోటల వీధి (Coffee Plantation Road)', 1),
(15, 3, 1, 'Ward 1', 'మెయిన్ వీధి (Main Street)', 1),
(16, 3, 1, 'Ward 2', 'కొత్త కాలనీ (New Colony)', 1),
(17, 4, 2, 'Ward 1', 'కాఫీ తోటల వీధి (Coffee Estate Lane)', 1),
(18, 4, 2, 'Ward 2', 'పంచాయతీ ఆఫీస్ రోడ్డు (Panchayat Office Road)', 1)
ON DUPLICATE KEY UPDATE 
    `village_id`=VALUES(`village_id`), 
    `gram_panchayat_id`=VALUES(`gram_panchayat_id`),
    `ward_number`=VALUES(`ward_number`),
    `ward_name`=VALUES(`ward_name`);

-- 7. Departments
INSERT INTO `departments` (`id`, `name_en`, `name_te`, `code`, `description`, `is_active`) VALUES
(1, 'Water Supply', 'తాగునీటి సరఫరా', 'WATER', 'Drinking water pipelines, borewells, overhead tanks and RO plants', 1),
(2, 'Electricity & Street Lights', 'విద్యుత్ & వీధి దీపాలు', 'ELECTRIC', 'Street lights, transformer issues, dangling wires', 1),
(3, 'Sanitation & Waste Management', 'పారిశుధ్యం & చెత్త నిర్వహణ', 'SANITATION', 'Garbage collection, public bins, village cleaning', 1),
(4, 'Drainage & Sewage', 'డ్రైనేజీ & మురుగు కాల్వలు', 'DRAINAGE', 'Drainage blockages, open drain overflows, desilting', 1),
(5, 'Roads & Infrastructure', 'రోడ్లు & భవనాలు', 'ROADS', 'Potholes, damaged concrete roads, culverts, street damage', 1)
ON DUPLICATE KEY UPDATE `id`=`id`;

-- 8. Categories
INSERT INTO `categories` (`id`, `department_id`, `name_en`, `name_te`, `icon`, `sla_hours`, `default_priority`, `is_active`) VALUES
(1, 1, 'Pipeline Leakage / Damage', 'పైపులైన్ లీకేజీ / పగిలిన పైపు', 'bi-droplet-half', 24, 'high', 1),
(2, 1, 'No Water Supply in Tap', 'కుళాయిల్లో నీరు రాకపోవడం', 'bi-water', 12, 'critical', 1),
(3, 1, 'Contaminated / Muddy Water', 'కలుషిత నీరు / మురుగు వాసన', 'bi-shield-exclamation', 12, 'critical', 1),
(4, 2, 'Street Light Not Working', 'వీధి దీపం వెలగడం లేదు', 'bi-lightbulb-off', 48, 'medium', 1),
(5, 2, 'Loose / Hanging Electric Wire', 'వేలాడుతున్న విద్యుత్ తీగలు', 'bi-lightning-charge', 6, 'critical', 1),
(6, 3, 'Garbage Heap Not Cleaned', 'చెత్త కుప్పలు పేరుకుపోవడం', 'bi-trash', 24, 'medium', 1),
(7, 3, 'Public Toilet Cleaning Required', 'ప్రజా మరుగుదొడ్ల పరిశుభ్రత', 'bi-badge-wc', 24, 'high', 1),
(8, 4, 'Drainage Overflow on Road', 'రోడ్డుపైకి మురుగునీరు పొంగడం', 'bi-moisture', 24, 'high', 1),
(9, 4, 'Drain Blockage / Desilting', 'కాల్వల్లో పూడిక పేరుకుపోవడం', 'bi-hammer', 48, 'medium', 1),
(10, 5, 'Potholes / Road Broken', 'రోడ్డు గుంతలు / పాడైన రోడ్డు', 'bi-signpost-split', 72, 'low', 1)
ON DUPLICATE KEY UPDATE `id`=`id`;

-- 9. Seed Users (Admin, Staff, Officers with Geographic Scope)
-- Password hashes generated via PHP password_hash('Password123!', PASSWORD_BCRYPT) or respective defaults
INSERT INTO `users` (`id`, `role`, `full_name`, `email`, `phone`, `password_hash`, `department_id`, `village_id`, `state_id`, `district_id`, `mandal_id`, `gram_panchayat_id`, `location_scope`, `is_active`) VALUES
(1, 'admin', 'System Administrator', 'admin@smartgram.local', '9876543210', '$2y$10$XWGgK/w6duX50tUQNyYFEOSMSQaYUyXkXVjimnRMFFG/silWke4ce', NULL, NULL, 1, NULL, NULL, NULL, 'state', 1),
(2, 'staff', 'Panchayat Secretary (Staff)', 'staff@smartgram.local', '9876543211', '$2y$10$dUN2oMr0YwoH9eX0QVJh7OGLiRuEVT8y6oqnTbQ.BYHzP5G3ypr6q', NULL, 1, 1, 1, 1, 1, 'gram_panchayat', 1),
(3, 'officer', 'Water Works Field Officer', 'officer@smartgram.local', '9876543212', '$2y$10$PYp.v0j.N8whvBtkdmeE3ObHeUp2FYROMso.Gad4Q60Yt.dj7Bkc.', 1, 1, 1, 1, 1, NULL, 'mandal', 1),
(4, 'officer', 'Electrical Line Inspector', 'electrical@smartgram.local', '9876543213', '$2y$10$PYp.v0j.N8whvBtkdmeE3ObHeUp2FYROMso.Gad4Q60Yt.dj7Bkc.', 2, 1, 1, 1, NULL, NULL, 'district', 1),
(5, 'officer', 'Sanitation & Health Inspector', 'sanitation@smartgram.local', '9876543214', '$2y$10$PYp.v0j.N8whvBtkdmeE3ObHeUp2FYROMso.Gad4Q60Yt.dj7Bkc.', 3, 1, 1, 1, 1, NULL, 'mandal', 1)
ON DUPLICATE KEY UPDATE `email`=`email`;
