<?php
// ==============================================================================
// Smart Gram - User Model (Internal Staff, Officers, Admins ONLY)
// ==============================================================================

namespace App\Models;

use App\Core\Database;
use PDO;

class User {
    /**
     * Find an active internal user by their unique email
     */
    public static function findByEmail(string $email): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT u.*, 
                   d.name_en AS department_name, 
                   v.name_en AS village_name,
                   gp.name_en AS panchayat_name,
                   m.name_en AS mandal_name,
                   dist.name_en AS district_name,
                   st.name_en AS state_name
            FROM users u
            LEFT JOIN departments d ON u.department_id = d.id
            LEFT JOIN villages v ON u.village_id = v.id
            LEFT JOIN gram_panchayats gp ON u.gram_panchayat_id = gp.id
            LEFT JOIN mandals m ON u.mandal_id = m.id
            LEFT JOIN districts dist ON u.district_id = dist.id
            LEFT JOIN states st ON u.state_id = st.id
            WHERE u.email = :email AND u.is_active = 1
            LIMIT 1
        ");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        return $user ?: null;
    }

    /**
     * Find an active user by their primary key ID
     */
    public static function findById(int $id): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT u.*, 
                   d.name_en AS department_name, 
                   v.name_en AS village_name,
                   gp.name_en AS panchayat_name,
                   m.name_en AS mandal_name,
                   dist.name_en AS district_name,
                   st.name_en AS state_name
            FROM users u
            LEFT JOIN departments d ON u.department_id = d.id
            LEFT JOIN villages v ON u.village_id = v.id
            LEFT JOIN gram_panchayats gp ON u.gram_panchayat_id = gp.id
            LEFT JOIN mandals m ON u.mandal_id = m.id
            LEFT JOIN districts dist ON u.district_id = dist.id
            LEFT JOIN states st ON u.state_id = st.id
            WHERE u.id = :id AND u.is_active = 1
            LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        $user = $stmt->fetch();

        return $user ?: null;
    }

    /**
     * Verify a plaintext password against stored bcrypt hash
     */
    public static function verifyPassword(string $plainPassword, string $storedHash): bool {
        return password_verify($plainPassword, $storedHash);
    }

    /**
     * Determine if a user has geographic & role authorization to access/manage a grievance
     */
    public static function canAccessGrievance(array $user, array $grievance): bool {
        // 1. Admin has global access
        if (($user['role'] ?? '') === 'admin') {
            return true;
        }

        // 2. Direct assignment (officer assigned directly to the grievance)
        if (!empty($grievance['assigned_to_user_id']) && (int)$grievance['assigned_to_user_id'] === (int)$user['id']) {
            return true;
        }

        // 3. For Officer: must belong to the grievance's department
        if (($user['role'] ?? '') === 'officer') {
            if (empty($user['department_id']) || (int)$user['department_id'] !== (int)($grievance['department_id'] ?? 0)) {
                return false;
            }
        }

        // 4. Verify Geographic Scope
        $scope = $user['location_scope'] ?? null;

        // Ensure grievance has full geographic hierarchy populated if village_id is present
        if (!empty($grievance['village_id']) && (empty($grievance['gram_panchayat_id']) || empty($grievance['mandal_id']) || empty($grievance['district_id']))) {
            $resolved = Location::resolveHierarchy((int)$grievance['village_id']);
            if ($resolved) {
                $grievance = array_merge($resolved, $grievance);
            }
        }

        // Fallback checks for legacy accounts without explicit location_scope
        if (!$scope) {
            if (!empty($user['village_id'])) {
                return (int)$user['village_id'] === (int)($grievance['village_id'] ?? 0);
            }
            if (!empty($user['gram_panchayat_id'])) {
                return (int)$user['gram_panchayat_id'] === (int)($grievance['gram_panchayat_id'] ?? 0);
            }
            if (!empty($user['mandal_id'])) {
                return (int)$user['mandal_id'] === (int)($grievance['mandal_id'] ?? 0);
            }
            if (!empty($user['district_id'])) {
                return (int)$user['district_id'] === (int)($grievance['district_id'] ?? 0);
            }
            return true;
        }

        return match($scope) {
            'state' => !empty($user['state_id']) 
                ? (int)$user['state_id'] === (int)($grievance['state_id'] ?? 0) 
                : true,
            'district' => !empty($user['district_id']) 
                ? (int)$user['district_id'] === (int)($grievance['district_id'] ?? 0) 
                : true,
            'mandal' => !empty($user['mandal_id']) 
                ? (int)$user['mandal_id'] === (int)($grievance['mandal_id'] ?? 0) 
                : true,
            'gram_panchayat' => !empty($user['gram_panchayat_id']) 
                ? (int)$user['gram_panchayat_id'] === (int)($grievance['gram_panchayat_id'] ?? 0) 
                : true,
            'village' => !empty($user['village_id']) 
                ? (int)$user['village_id'] === (int)($grievance['village_id'] ?? 0) 
                : true,
            default => true
        };
    }

    /**
     * Generate query filter parameters representing this user's geographic boundary
     */
    public static function getGeographicScopeFilters(array $user): array {
        if (($user['role'] ?? '') === 'admin') {
            return [];
        }

        $scope = $user['location_scope'] ?? null;
        $filters = [];

        if ($scope === 'village' && !empty($user['village_id'])) {
            $filters['village_id'] = (int)$user['village_id'];
        } elseif ($scope === 'gram_panchayat' && !empty($user['gram_panchayat_id'])) {
            $filters['gram_panchayat_id'] = (int)$user['gram_panchayat_id'];
        } elseif ($scope === 'mandal' && !empty($user['mandal_id'])) {
            $filters['mandal_id'] = (int)$user['mandal_id'];
        } elseif ($scope === 'district' && !empty($user['district_id'])) {
            $filters['district_id'] = (int)$user['district_id'];
        } elseif ($scope === 'state' && !empty($user['state_id'])) {
            $filters['state_id'] = (int)$user['state_id'];
        } else {
            // Fallback for unconfigured accounts
            if (!empty($user['village_id'])) {
                $filters['village_id'] = (int)$user['village_id'];
            } elseif (!empty($user['gram_panchayat_id'])) {
                $filters['gram_panchayat_id'] = (int)$user['gram_panchayat_id'];
            } elseif (!empty($user['mandal_id'])) {
                $filters['mandal_id'] = (int)$user['mandal_id'];
            } elseif (!empty($user['district_id'])) {
                $filters['district_id'] = (int)$user['district_id'];
            }
        }

        return $filters;
    }

    /**
     * Return human-friendly badge string for a user's location scope
     */
    public static function getScopeBadge(array $user, string $lang = 'en'): string {
        $role = $user['role'] ?? '';
        if ($role === 'admin') {
            return $lang === 'te' ? 'రాష్ట్ర స్థాయి (System-Wide Admin)' : 'System-Wide Admin';
        }

        $scope = $user['location_scope'] ?? '';
        $mandalName = $user['mandal_name'] ?? '';
        $panchayatName = $user['panchayat_name'] ?? '';
        $villageName = $user['village_name'] ?? '';
        $districtName = $user['district_name'] ?? '';

        if ($scope === 'gram_panchayat' || (!empty($user['gram_panchayat_id']) && !$scope)) {
            $label = $lang === 'te' ? 'గ్రామ పంచాయతీ పరిధి:' : 'GP Scope:';
            $name = $panchayatName ?: ($villageName ?: 'Paderu');
            return "$label $name";
        }

        if ($scope === 'mandal' || (!empty($user['mandal_id']) && !$scope)) {
            $label = $lang === 'te' ? 'మండల పరిధి:' : 'Mandal Scope:';
            $name = $mandalName ?: 'Paderu';
            return "$label $name";
        }

        if ($scope === 'district' || (!empty($user['district_id']) && !$scope)) {
            $label = $lang === 'te' ? 'జిల్లా పరిధి:' : 'District Scope:';
            $name = $districtName ?: 'Alluri Sitharama Raju';
            return "$label $name";
        }

        if ($scope === 'village' || !empty($user['village_id'])) {
            $label = $lang === 'te' ? 'గ్రామ పరిధి:' : 'Village Scope:';
            $name = $villageName ?: 'Ramapuram';
            return "$label $name";
        }

        return $lang === 'te' ? 'స్థానిక పరిధి' : 'Local Scope';
    }

    /**
     * Retrieve all active users along with their complete geographic scope
     */
    public static function getAllUsers(): array {
        $db = Database::getConnection();
        return $db->query("
            SELECT u.*, 
                   d.name_en AS department_name_en, d.name_te AS department_name_te,
                   v.name_en AS village_name,
                   gp.name_en AS panchayat_name,
                   m.name_en AS mandal_name,
                   dist.name_en AS district_name,
                   st.name_en AS state_name
            FROM users u
            LEFT JOIN departments d ON u.department_id = d.id
            LEFT JOIN villages v ON u.village_id = v.id
            LEFT JOIN gram_panchayats gp ON u.gram_panchayat_id = gp.id
            LEFT JOIN mandals m ON u.mandal_id = m.id
            LEFT JOIN districts dist ON u.district_id = dist.id
            LEFT JOIN states st ON u.state_id = st.id
            WHERE u.is_active = 1
            ORDER BY u.role ASC, u.full_name ASC
        ")->fetchAll(PDO::FETCH_ASSOC);
    }
}
