<?php
// ==============================================================================
// Smart Gram - Admin Location Management Controller
// Manages administrative hierarchy: States, Districts, Mandals, GPs, Villages, Wards
// ==============================================================================

namespace App\Controllers;

use App\Core\Session;
use App\Models\AuditLog;
use App\Models\Location;
use Exception;

class LocationController {
    /**
     * Display the Location Management Dashboard
     */
    public function index(): void {
        $user = auth();
        $tab = $_GET['tab'] ?? 'districts';
        $validTabs = ['states', 'districts', 'mandals', 'panchayats', 'villages', 'wards'];
        if (!in_array($tab, $validTabs, true)) {
            $tab = 'districts';
        }

        // Retrieve data for all levels
        $states = Location::getStates(false);
        $districts = Location::getDistricts(null, false);
        $mandals = Location::getMandals(null, false);
        $panchayats = Location::getGramPanchayats(null, false);
        $villages = Location::getVillages(null, false);
        $wards = Location::getWards(null, null, false);
        $counts = Location::getHierarchyCounts();

        view('admin/locations/index', [
            'title'       => 'Administrative Geographic Hierarchy - Smart Gram Admin',
            'user'        => $user,
            'activeTab'   => $tab,
            'states'      => $states,
            'districts'   => $districts,
            'mandals'     => $mandals,
            'panchayats'  => $panchayats,
            'villages'    => $villages,
            'wards'       => $wards,
            'counts'      => $counts
        ]);
    }

    /**
     * Store a newly created location entity
     */
    public function store(): void {
        $level = trim($_POST['level'] ?? '');
        $user = auth();

        try {
            $newId = match($level) {
                'state'          => Location::saveState($_POST),
                'district'       => Location::saveDistrict($_POST),
                'mandal'         => Location::saveMandal($_POST),
                'gram_panchayat' => Location::saveGramPanchayat($_POST),
                'village'        => Location::saveVillage($_POST),
                'ward'           => Location::saveWard($_POST),
                default          => throw new Exception("Invalid geographic level: {$level}")
            };

            AuditLog::log('admin_created_location', $level, $newId, (int)$user['id'], [
                'name_en' => $_POST['name_en'] ?? ($_POST['ward_number'] ?? ''),
                'level'   => $level
            ]);

            Session::setFlash('success', current_lang() === 'te' 
                ? 'ప్రాంత వివరాలు విజయవంతంగా చేర్చబడ్డాయి.' 
                : 'Geographic entity added successfully.');

        } catch (Exception $e) {
            Session::setFlash('error', $e->getMessage());
        }

        $redirectTab = match($level) {
            'gram_panchayat' => 'panchayats',
            'state'          => 'states',
            'district'       => 'districts',
            'mandal'         => 'mandals',
            'village'        => 'villages',
            'ward'           => 'wards',
            default          => 'districts'
        };

        redirect('admin/locations?tab=' . $redirectTab);
    }

    /**
     * Update an existing location entity
     */
    public function update(): void {
        $level = trim($_POST['level'] ?? '');
        $id = (int)($_POST['id'] ?? 0);
        $user = auth();

        if ($id <= 0) {
            Session::setFlash('error', 'Invalid entity ID.');
            redirect('admin/locations');
        }

        try {
            match($level) {
                'state'          => Location::saveState($_POST, $id),
                'district'       => Location::saveDistrict($_POST, $id),
                'mandal'         => Location::saveMandal($_POST, $id),
                'gram_panchayat' => Location::saveGramPanchayat($_POST, $id),
                'village'        => Location::saveVillage($_POST, $id),
                'ward'           => Location::saveWard($_POST, $id),
                default          => throw new Exception("Invalid geographic level: {$level}")
            };

            AuditLog::log('admin_updated_location', $level, $id, (int)$user['id'], [
                'name_en' => $_POST['name_en'] ?? ($_POST['ward_number'] ?? ''),
                'level'   => $level
            ]);

            Session::setFlash('success', current_lang() === 'te' 
                ? 'ప్రాంత వివరాలు నవీకరించబడ్డాయి.' 
                : 'Geographic entity updated successfully.');

        } catch (Exception $e) {
            Session::setFlash('error', $e->getMessage());
        }

        $redirectTab = match($level) {
            'gram_panchayat' => 'panchayats',
            'state'          => 'states',
            'district'       => 'districts',
            'mandal'         => 'mandals',
            'village'        => 'villages',
            'ward'           => 'wards',
            default          => 'districts'
        };

        redirect('admin/locations?tab=' . $redirectTab);
    }

    /**
     * Toggle Active / Inactive status of an entity
     */
    public function toggle(): void {
        $level = trim($_POST['level'] ?? '');
        $id = (int)($_POST['id'] ?? 0);
        $user = auth();

        if ($id <= 0) {
            Session::setFlash('error', 'Invalid entity ID.');
            redirect('admin/locations');
        }

        try {
            Location::toggleActive($level, $id);

            AuditLog::log('admin_toggled_location_status', $level, $id, (int)$user['id'], [
                'level' => $level,
                'id'    => $id
            ]);

            Session::setFlash('success', current_lang() === 'te' 
                ? 'స్థితి విజయవంతంగా మార్చబడింది.' 
                : 'Status toggled successfully.');

        } catch (Exception $e) {
            Session::setFlash('error', $e->getMessage());
        }

        $redirectTab = match($level) {
            'gram_panchayat' => 'panchayats',
            'state'          => 'states',
            'district'       => 'districts',
            'mandal'         => 'mandals',
            'village'        => 'villages',
            'ward'           => 'wards',
            default          => 'districts'
        };

        redirect('admin/locations?tab=' . $redirectTab);
    }

    /**
     * Safely delete a location entity (with dependency check)
     */
    public function delete(): void {
        $level = trim($_POST['level'] ?? '');
        $id = (int)($_POST['id'] ?? 0);
        $user = auth();

        if ($id <= 0) {
            Session::setFlash('error', 'Invalid entity ID.');
            redirect('admin/locations');
        }

        try {
            Location::deleteEntity($level, $id);

            AuditLog::log('admin_deleted_location', $level, $id, (int)$user['id'], [
                'level' => $level,
                'id'    => $id
            ]);

            Session::setFlash('success', current_lang() === 'te' 
                ? 'ప్రాంతం విజయవంతంగా తొలగించబడింది.' 
                : 'Location entity deleted successfully.');

        } catch (Exception $e) {
            Session::setFlash('error', $e->getMessage());
        }

        $redirectTab = match($level) {
            'gram_panchayat' => 'panchayats',
            'state'          => 'states',
            'district'       => 'districts',
            'mandal'         => 'mandals',
            'village'        => 'villages',
            'ward'           => 'wards',
            default          => 'districts'
        };

        redirect('admin/locations?tab=' . $redirectTab);
    }
}
