<?php
// ==============================================================================
// Smart Gram - Geographic Location Model
// Manages: State -> District -> Mandal -> Gram Panchayat -> Village -> Ward
// ==============================================================================

namespace App\Models;

use App\Core\Database;
use Exception;
use PDO;

class Location {
    /**
     * Retrieve States (optionally filtered by active status)
     */
    public static function getStates(bool $activeOnly = true): array {
        $db = Database::getConnection();
        $sql = "SELECT id, code, name_en, name_te, is_active, created_at FROM states";
        if ($activeOnly) {
            $sql .= " WHERE is_active = 1";
        }
        $sql .= " ORDER BY name_en ASC";
        return $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retrieve Districts (optionally filtered by state_id)
     */
    public static function getDistricts(?int $stateId = null, bool $activeOnly = true): array {
        $db = Database::getConnection();
        $where = [];
        $params = [];

        if ($stateId !== null) {
            $where[] = "d.state_id = :state_id";
            $params[':state_id'] = $stateId;
        }
        if ($activeOnly) {
            $where[] = "d.is_active = 1";
        }

        $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";
        $sql = "
            SELECT d.id, d.state_id, d.code, d.name_en, d.name_te, d.is_active, d.created_at,
                   s.name_en AS state_name_en, s.name_te AS state_name_te
            FROM districts d
            JOIN states s ON d.state_id = s.id
            $whereClause
            ORDER BY d.name_en ASC
        ";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retrieve Mandals (optionally filtered by district_id)
     */
    public static function getMandals(?int $districtId = null, bool $activeOnly = true): array {
        $db = Database::getConnection();
        $where = [];
        $params = [];

        if ($districtId !== null) {
            $where[] = "m.district_id = :district_id";
            $params[':district_id'] = $districtId;
        }
        if ($activeOnly) {
            $where[] = "m.is_active = 1";
        }

        $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";
        $sql = "
            SELECT m.id, m.district_id, m.code, m.name_en, m.name_te, m.is_active, m.created_at,
                   d.name_en AS district_name_en, d.name_te AS district_name_te, d.state_id,
                   s.name_en AS state_name_en, s.name_te AS state_name_te
            FROM mandals m
            JOIN districts d ON m.district_id = d.id
            JOIN states s ON d.state_id = s.id
            $whereClause
            ORDER BY m.name_en ASC
        ";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retrieve Gram Panchayats (optionally filtered by mandal_id)
     */
    public static function getGramPanchayats(?int $mandalId = null, bool $activeOnly = true): array {
        $db = Database::getConnection();
        $where = [];
        $params = [];

        if ($mandalId !== null) {
            $where[] = "gp.mandal_id = :mandal_id";
            $params[':mandal_id'] = $mandalId;
        }
        if ($activeOnly) {
            $where[] = "gp.is_active = 1";
        }

        $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";
        $sql = "
            SELECT gp.id, gp.mandal_id, gp.code, gp.name_en, gp.name_te, gp.is_active, gp.created_at,
                   m.name_en AS mandal_name_en, m.name_te AS mandal_name_te, m.district_id,
                   d.name_en AS district_name_en, d.name_te AS district_name_te, d.state_id,
                   s.name_en AS state_name_en, s.name_te AS state_name_te
            FROM gram_panchayats gp
            JOIN mandals m ON gp.mandal_id = m.id
            JOIN districts d ON m.district_id = d.id
            JOIN states s ON d.state_id = s.id
            $whereClause
            ORDER BY gp.name_en ASC
        ";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retrieve Villages (optionally filtered by gram_panchayat_id)
     */
    public static function getVillages(?int $panchayatId = null, bool $activeOnly = true): array {
        $db = Database::getConnection();
        $where = [];
        $params = [];

        if ($panchayatId !== null) {
            $where[] = "v.gram_panchayat_id = :panchayat_id";
            $params[':panchayat_id'] = $panchayatId;
        }
        if ($activeOnly) {
            $where[] = "v.is_active = 1";
        }

        $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";
        $sql = "
            SELECT v.id, v.gram_panchayat_id, v.name_en, v.name_te, v.mandal, v.district, v.is_active, v.created_at,
                   gp.name_en AS panchayat_name_en, gp.name_te AS panchayat_name_te, gp.mandal_id,
                   m.name_en AS mandal_name_en, m.name_te AS mandal_name_te, m.district_id,
                   d.name_en AS district_name_en, d.name_te AS district_name_te, d.state_id,
                   s.name_en AS state_name_en, s.name_te AS state_name_te
            FROM villages v
            LEFT JOIN gram_panchayats gp ON v.gram_panchayat_id = gp.id
            LEFT JOIN mandals m ON gp.mandal_id = m.id
            LEFT JOIN districts d ON m.district_id = d.id
            LEFT JOIN states s ON d.state_id = s.id
            $whereClause
            ORDER BY v.name_en ASC
        ";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retrieve Wards (optionally filtered by village_id or gram_panchayat_id)
     */
    public static function getWards(?int $villageId = null, ?int $panchayatId = null, bool $activeOnly = true): array {
        $db = Database::getConnection();
        $where = [];
        $params = [];

        if ($villageId !== null) {
            $where[] = "w.village_id = :village_id";
            $params[':village_id'] = $villageId;
        }
        if ($panchayatId !== null) {
            $where[] = "w.gram_panchayat_id = :panchayat_id";
            $params[':panchayat_id'] = $panchayatId;
        }
        if ($activeOnly) {
            $where[] = "w.is_active = 1";
        }

        $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";
        $sql = "
            SELECT w.id, w.village_id, w.gram_panchayat_id, w.ward_number, w.ward_name, w.is_active, w.created_at,
                   v.name_en AS village_name_en, v.name_te AS village_name_te
            FROM wards w
            JOIN villages v ON w.village_id = v.id
            $whereClause
            ORDER BY w.id ASC
        ";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Resolve full upward hierarchy given a village_id
     * Returns: state, district, mandal, gram_panchayat, village details
     */
    public static function resolveHierarchy(int $villageId): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT v.id AS village_id, v.name_en AS village_name_en, v.name_te AS village_name_te,
                   gp.id AS gram_panchayat_id, gp.name_en AS panchayat_name_en, gp.name_te AS panchayat_name_te,
                   m.id AS mandal_id, m.name_en AS mandal_name_en, m.name_te AS mandal_name_te,
                   d.id AS district_id, d.name_en AS district_name_en, d.name_te AS district_name_te,
                   s.id AS state_id, s.name_en AS state_name_en, s.name_te AS state_name_te
            FROM villages v
            LEFT JOIN gram_panchayats gp ON v.gram_panchayat_id = gp.id
            LEFT JOIN mandals m ON gp.mandal_id = m.id
            LEFT JOIN districts d ON m.district_id = d.id
            LEFT JOIN states s ON d.state_id = s.id
            WHERE v.id = :village_id
            LIMIT 1
        ");
        $stmt->execute([':village_id' => $villageId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Retrieve single entity by level and ID
     */
    public static function getEntity(string $level, int $id): ?array {
        $db = Database::getConnection();
        $table = self::getTableForLevel($level);
        if (!$table) {
            return null;
        }

        $stmt = $db->prepare("SELECT * FROM `{$table}` WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Toggle active / inactive status of an entity
     */
    public static function toggleActive(string $level, int $id): bool {
        $db = Database::getConnection();
        $table = self::getTableForLevel($level);
        if (!$table) {
            throw new Exception("Invalid geographic level.");
        }

        $stmt = $db->prepare("UPDATE `{$table}` SET is_active = IF(is_active = 1, 0, 1) WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Check if an entity can be safely deleted or if existing grievances/children depend on it
     */
    public static function canDelete(string $level, int $id): array {
        $db = Database::getConnection();
        $table = self::getTableForLevel($level);
        if (!$table) {
            return ['can_delete' => false, 'reason' => 'Invalid location level.'];
        }

        // 1. Check direct grievances referencing this entity
        $columnMap = [
            'state'          => 'state_id',
            'district'       => 'district_id',
            'mandal'         => 'mandal_id',
            'gram_panchayat' => 'gram_panchayat_id',
            'village'        => 'village_id',
            'ward'           => 'ward_id'
        ];

        $col = $columnMap[$level] ?? null;
        $grievanceCount = 0;
        if ($col) {
            $gStmt = $db->prepare("SELECT COUNT(*) FROM grievances WHERE `{$col}` = :id");
            $gStmt->execute([':id' => $id]);
            $grievanceCount = (int)$gStmt->fetchColumn();
        }

        if ($grievanceCount > 0) {
            return [
                'can_delete'      => false,
                'grievance_count' => $grievanceCount,
                'child_count'     => 0,
                'reason'          => "Cannot delete: {$grievanceCount} active grievance(s) are linked to this location. Please deactivate it instead to preserve audit records."
            ];
        }

        // 2. Check direct child entities
        $childTableMap = [
            'state'          => ['table' => 'districts',       'fk' => 'state_id',          'label' => 'district(s)'],
            'district'       => ['table' => 'mandals',         'fk' => 'district_id',       'label' => 'mandal(s)'],
            'mandal'         => ['table' => 'gram_panchayats', 'fk' => 'mandal_id',         'label' => 'Gram Panchayat(s)'],
            'gram_panchayat' => ['table' => 'villages',        'fk' => 'gram_panchayat_id', 'label' => 'village(s)'],
            'village'        => ['table' => 'wards',           'fk' => 'village_id',        'label' => 'ward(s)'],
            'ward'           => null
        ];

        $childInfo = $childTableMap[$level] ?? null;
        $childCount = 0;
        if ($childInfo) {
            $cStmt = $db->prepare("SELECT COUNT(*) FROM `{$childInfo['table']}` WHERE `{$childInfo['fk']}` = :id");
            $cStmt->execute([':id' => $id]);
            $childCount = (int)$cStmt->fetchColumn();

            if ($childCount > 0) {
                return [
                    'can_delete'      => false,
                    'grievance_count' => 0,
                    'child_count'     => $childCount,
                    'reason'          => "Cannot delete: {$childCount} {$childInfo['label']} belong to this entity. Delete or reassign children first, or deactivate this location."
                ];
            }
        }

        return [
            'can_delete'      => true,
            'grievance_count' => 0,
            'child_count'     => 0,
            'reason'          => 'Safe to delete.'
        ];
    }

    /**
     * Safely delete an entity if no dependencies exist
     */
    public static function deleteEntity(string $level, int $id): bool {
        $check = self::canDelete($level, $id);
        if (!$check['can_delete']) {
            throw new Exception($check['reason']);
        }

        $db = Database::getConnection();
        $table = self::getTableForLevel($level);
        $stmt = $db->prepare("DELETE FROM `{$table}` WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Create or update State
     */
    public static function saveState(array $data, ?int $id = null): int {
        $db = Database::getConnection();
        $code = strtoupper(trim($data['code'] ?? ''));
        $nameEn = trim($data['name_en'] ?? '');
        $nameTe = trim($data['name_te'] ?? '');
        $isActive = isset($data['is_active']) ? (int)$data['is_active'] : 1;

        if (empty($code) || empty($nameEn) || empty($nameTe)) {
            throw new Exception("State code, English name, and Telugu name are required.");
        }

        if ($id) {
            $stmt = $db->prepare("UPDATE states SET code = :code, name_en = :name_en, name_te = :name_te, is_active = :is_active WHERE id = :id");
            $stmt->execute([':code' => $code, ':name_en' => $nameEn, ':name_te' => $nameTe, ':is_active' => $isActive, ':id' => $id]);
            return $id;
        } else {
            $stmt = $db->prepare("INSERT INTO states (code, name_en, name_te, is_active) VALUES (:code, :name_en, :name_te, :is_active)");
            $stmt->execute([':code' => $code, ':name_en' => $nameEn, ':name_te' => $nameTe, ':is_active' => $isActive]);
            return (int)$db->lastInsertId();
        }
    }

    /**
     * Create or update District
     */
    public static function saveDistrict(array $data, ?int $id = null): int {
        $db = Database::getConnection();
        $stateId = (int)($data['state_id'] ?? 0);
        $code = strtoupper(trim($data['code'] ?? ''));
        $nameEn = trim($data['name_en'] ?? '');
        $nameTe = trim($data['name_te'] ?? '');
        $isActive = isset($data['is_active']) ? (int)$data['is_active'] : 1;

        if ($stateId <= 0 || empty($nameEn) || empty($nameTe)) {
            throw new Exception("State selection, English name, and Telugu name are required.");
        }

        if ($id) {
            $stmt = $db->prepare("UPDATE districts SET state_id = :state_id, code = :code, name_en = :name_en, name_te = :name_te, is_active = :is_active WHERE id = :id");
            $stmt->execute([':state_id' => $stateId, ':code' => $code, ':name_en' => $nameEn, ':name_te' => $nameTe, ':is_active' => $isActive, ':id' => $id]);
            return $id;
        } else {
            $stmt = $db->prepare("INSERT INTO districts (state_id, code, name_en, name_te, is_active) VALUES (:state_id, :code, :name_en, :name_te, :is_active)");
            $stmt->execute([':state_id' => $stateId, ':code' => $code, ':name_en' => $nameEn, ':name_te' => $nameTe, ':is_active' => $isActive]);
            return (int)$db->lastInsertId();
        }
    }

    /**
     * Create or update Mandal
     */
    public static function saveMandal(array $data, ?int $id = null): int {
        $db = Database::getConnection();
        $districtId = (int)($data['district_id'] ?? 0);
        $code = strtoupper(trim($data['code'] ?? ''));
        $nameEn = trim($data['name_en'] ?? '');
        $nameTe = trim($data['name_te'] ?? '');
        $isActive = isset($data['is_active']) ? (int)$data['is_active'] : 1;

        if ($districtId <= 0 || empty($nameEn) || empty($nameTe)) {
            throw new Exception("District selection, English name, and Telugu name are required.");
        }

        if ($id) {
            $stmt = $db->prepare("UPDATE mandals SET district_id = :district_id, code = :code, name_en = :name_en, name_te = :name_te, is_active = :is_active WHERE id = :id");
            $stmt->execute([':district_id' => $districtId, ':code' => $code, ':name_en' => $nameEn, ':name_te' => $nameTe, ':is_active' => $isActive, ':id' => $id]);
            return $id;
        } else {
            $stmt = $db->prepare("INSERT INTO mandals (district_id, code, name_en, name_te, is_active) VALUES (:district_id, :code, :name_en, :name_te, :is_active)");
            $stmt->execute([':district_id' => $districtId, ':code' => $code, ':name_en' => $nameEn, ':name_te' => $nameTe, ':is_active' => $isActive]);
            return (int)$db->lastInsertId();
        }
    }

    /**
     * Create or update Gram Panchayat
     */
    public static function saveGramPanchayat(array $data, ?int $id = null): int {
        $db = Database::getConnection();
        $mandalId = (int)($data['mandal_id'] ?? 0);
        $code = strtoupper(trim($data['code'] ?? ''));
        $nameEn = trim($data['name_en'] ?? '');
        $nameTe = trim($data['name_te'] ?? '');
        $isActive = isset($data['is_active']) ? (int)$data['is_active'] : 1;

        if ($mandalId <= 0 || empty($nameEn) || empty($nameTe)) {
            throw new Exception("Mandal selection, English name, and Telugu name are required.");
        }

        if ($id) {
            $stmt = $db->prepare("UPDATE gram_panchayats SET mandal_id = :mandal_id, code = :code, name_en = :name_en, name_te = :name_te, is_active = :is_active WHERE id = :id");
            $stmt->execute([':mandal_id' => $mandalId, ':code' => $code, ':name_en' => $nameEn, ':name_te' => $nameTe, ':is_active' => $isActive, ':id' => $id]);
            return $id;
        } else {
            $stmt = $db->prepare("INSERT INTO gram_panchayats (mandal_id, code, name_en, name_te, is_active) VALUES (:mandal_id, :code, :name_en, :name_te, :is_active)");
            $stmt->execute([':mandal_id' => $mandalId, ':code' => $code, ':name_en' => $nameEn, ':name_te' => $nameTe, ':is_active' => $isActive]);
            return (int)$db->lastInsertId();
        }
    }

    /**
     * Create or update Village
     */
    public static function saveVillage(array $data, ?int $id = null): int {
        $db = Database::getConnection();
        $gpId = (int)($data['gram_panchayat_id'] ?? 0);
        $nameEn = trim($data['name_en'] ?? '');
        $nameTe = trim($data['name_te'] ?? '');
        $isActive = isset($data['is_active']) ? (int)$data['is_active'] : 1;

        if ($gpId <= 0 || empty($nameEn) || empty($nameTe)) {
            throw new Exception("Gram Panchayat selection, English name, and Telugu name are required.");
        }

        // Fetch parent mandal and district name for legacy column compatibility
        $pStmt = $db->prepare("
            SELECT m.name_en AS mandal_name, d.name_en AS district_name 
            FROM gram_panchayats gp
            JOIN mandals m ON gp.mandal_id = m.id
            JOIN districts d ON m.district_id = d.id
            WHERE gp.id = :gp_id
        ");
        $pStmt->execute([':gp_id' => $gpId]);
        $parent = $pStmt->fetch(PDO::FETCH_ASSOC);
        $mandalName = $parent['mandal_name'] ?? '';
        $districtName = $parent['district_name'] ?? '';

        if ($id) {
            $stmt = $db->prepare("
                UPDATE villages 
                SET gram_panchayat_id = :gp_id, name_en = :name_en, name_te = :name_te, 
                    mandal = :mandal, district = :district, is_active = :is_active 
                WHERE id = :id
            ");
            $stmt->execute([
                ':gp_id'     => $gpId,
                ':name_en'   => $nameEn,
                ':name_te'   => $nameTe,
                ':mandal'    => $mandalName,
                ':district'  => $districtName,
                ':is_active' => $isActive,
                ':id'        => $id
            ]);
            return $id;
        } else {
            $stmt = $db->prepare("
                INSERT INTO villages (gram_panchayat_id, name_en, name_te, mandal, district, is_active)
                VALUES (:gp_id, :name_en, :name_te, :mandal, :district, :is_active)
            ");
            $stmt->execute([
                ':gp_id'     => $gpId,
                ':name_en'   => $nameEn,
                ':name_te'   => $nameTe,
                ':mandal'    => $mandalName,
                ':district'  => $districtName,
                ':is_active' => $isActive
            ]);
            return (int)$db->lastInsertId();
        }
    }

    /**
     * Create or update Ward
     */
    public static function saveWard(array $data, ?int $id = null): int {
        $db = Database::getConnection();
        $villageId = (int)($data['village_id'] ?? 0);
        $wardNumber = trim($data['ward_number'] ?? '');
        $wardName = trim($data['ward_name'] ?? '');
        $isActive = isset($data['is_active']) ? (int)$data['is_active'] : 1;

        if ($villageId <= 0 || empty($wardNumber)) {
            throw new Exception("Village selection and Ward number are required.");
        }

        // Retrieve GP ID from village
        $vStmt = $db->prepare("SELECT gram_panchayat_id FROM villages WHERE id = :vid");
        $vStmt->execute([':vid' => $villageId]);
        $gpId = $vStmt->fetchColumn() ?: null;

        if ($id) {
            $stmt = $db->prepare("
                UPDATE wards 
                SET village_id = :village_id, gram_panchayat_id = :gp_id, 
                    ward_number = :ward_number, ward_name = :ward_name, is_active = :is_active 
                WHERE id = :id
            ");
            $stmt->execute([
                ':village_id'   => $villageId,
                ':gp_id'        => $gpId,
                ':ward_number'  => $wardNumber,
                ':ward_name'    => $wardName ?: null,
                ':is_active'    => $isActive,
                ':id'           => $id
            ]);
            return $id;
        } else {
            $stmt = $db->prepare("
                INSERT INTO wards (village_id, gram_panchayat_id, ward_number, ward_name, is_active)
                VALUES (:village_id, :gp_id, :ward_number, :ward_name, :is_active)
            ");
            $stmt->execute([
                ':village_id'   => $villageId,
                ':gp_id'        => $gpId,
                ':ward_number'  => $wardNumber,
                ':ward_name'    => $wardName ?: null,
                ':is_active'    => $isActive
            ]);
            return (int)$db->lastInsertId();
        }
    }

    /**
     * Retrieve total location counts across all hierarchy levels
     */
    public static function getHierarchyCounts(): array {
        $db = Database::getConnection();
        return [
            'states'          => (int)$db->query("SELECT COUNT(*) FROM states WHERE is_active = 1")->fetchColumn(),
            'districts'       => (int)$db->query("SELECT COUNT(*) FROM districts WHERE is_active = 1")->fetchColumn(),
            'mandals'         => (int)$db->query("SELECT COUNT(*) FROM mandals WHERE is_active = 1")->fetchColumn(),
            'gram_panchayats' => (int)$db->query("SELECT COUNT(*) FROM gram_panchayats WHERE is_active = 1")->fetchColumn(),
            'villages'        => (int)$db->query("SELECT COUNT(*) FROM villages WHERE is_active = 1")->fetchColumn(),
            'wards'           => (int)$db->query("SELECT COUNT(*) FROM wards WHERE is_active = 1")->fetchColumn(),
        ];
    }

    /**
     * Helper to map level name to table name
     */
    private static function getTableForLevel(string $level): ?string {
        return match($level) {
            'state', 'states'                      => 'states',
            'district', 'districts'                => 'districts',
            'mandal', 'mandals'                    => 'mandals',
            'gram_panchayat', 'gram_panchayats'    => 'gram_panchayats',
            'village', 'villages'                  => 'villages',
            'ward', 'wards'                        => 'wards',
            default                                => null
        };
    }
}
