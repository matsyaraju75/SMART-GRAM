<?php
// ==============================================================================
// Smart Gram - Citizen Grievance Controller (NO CITIZEN LOGIN)
// Handles submission form, dynamic wards API, validation, and confirmation.
// ==============================================================================

namespace App\Controllers;

use App\Core\Session;
use App\Core\RateLimiter;
use App\Models\Grievance;
use App\Models\Location;
use App\Services\GrievanceService;
use Exception;

class GrievanceController {
    /**
     * Display the grievance submission form with progressive geographic selection
     */
    public function showSubmitForm(): void {
        $states = Location::getStates(true);
        $defaultStateId = !empty($states) ? (int)$states[0]['id'] : null;

        $districts = $defaultStateId ? Location::getDistricts($defaultStateId, true) : [];
        $defaultDistrictId = !empty($districts) ? (int)$districts[0]['id'] : null;

        $mandals = $defaultDistrictId ? Location::getMandals($defaultDistrictId, true) : [];
        $defaultMandalId = !empty($mandals) ? (int)$mandals[0]['id'] : null;

        $panchayats = $defaultMandalId ? Location::getGramPanchayats($defaultMandalId, true) : [];
        $defaultPanchayatId = !empty($panchayats) ? (int)$panchayats[0]['id'] : null;

        $villages = $defaultPanchayatId ? Location::getVillages($defaultPanchayatId, true) : Location::getVillages(null, true);
        $defaultVillageId = !empty($villages) ? (int)$villages[0]['id'] : 1;

        $wards = Location::getWards($defaultVillageId, null, true);
        $categories = Grievance::getCategories();

        $oldInput = Session::getFlash('old_input') ?? [];

        view('citizen/submit', [
            'title'              => __('citizen.form_heading') . ' — ' . __('common.app_name'),
            'states'             => $states,
            'districts'          => $districts,
            'mandals'            => $mandals,
            'panchayats'         => $panchayats,
            'villages'           => $villages,
            'wards'              => $wards,
            'categories'         => $categories,
            'defaultStateId'     => $defaultStateId,
            'defaultDistrictId'  => $defaultDistrictId,
            'defaultMandalId'    => $defaultMandalId,
            'defaultPanchayatId' => $defaultPanchayatId,
            'defaultVillageId'   => $defaultVillageId,
            'old'                => $oldInput
        ]);
    }

    /**
     * Dynamic AJAX API: Get districts for a selected state
     */
    public function getDistrictsApi(): void {
        $stateId = (int)($_GET['state_id'] ?? 0);
        $districts = Location::getDistricts($stateId ?: null, true);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success'   => true,
            'state_id'  => $stateId,
            'districts' => $districts
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Dynamic AJAX API: Get mandals for a selected district
     */
    public function getMandalsApi(): void {
        $districtId = (int)($_GET['district_id'] ?? 0);
        $mandals = Location::getMandals($districtId ?: null, true);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success'     => true,
            'district_id' => $districtId,
            'mandals'     => $mandals
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Dynamic AJAX API: Get Gram Panchayats for a selected mandal
     */
    public function getPanchayatsApi(): void {
        $mandalId = (int)($_GET['mandal_id'] ?? 0);
        $panchayats = Location::getGramPanchayats($mandalId ?: null, true);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success'    => true,
            'mandal_id'  => $mandalId,
            'panchayats' => $panchayats
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Dynamic AJAX API: Get villages for a selected Gram Panchayat
     */
    public function getVillagesApi(): void {
        $panchayatId = (int)($_GET['panchayat_id'] ?? 0);
        $villages = Location::getVillages($panchayatId ?: null, true);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success'      => true,
            'panchayat_id' => $panchayatId,
            'villages'     => $villages
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Dynamic AJAX API: Get wards for a selected village
     */
    public function getWardsApi(): void {
        $villageId = (int)($_GET['village_id'] ?? 0);
        $wards = Location::getWards($villageId ?: null, null, true);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success'    => true,
            'village_id' => $villageId,
            'wards'      => $wards
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Process grievance submission from citizen
     */
    public function submit(): void {
        $ip = RateLimiter::clientIp();
        $rateKey = "grievance_submit:{$ip}";

        // Preserve input for re-populating on error
        $oldInput = $_POST;
        unset($oldInput['_csrf_token']);

        // Anti-Spam Rate Limiting (10 submissions per 300 seconds)
        if (RateLimiter::tooManyAttempts($rateKey, 10)) {
            $seconds = RateLimiter::availableIn($rateKey);
            Session::setFlash('error', current_lang() === 'te'
                ? "మీరు చాలా అభ్యర్థనలను సమర్పించారు. దయచేసి {$seconds} సెకన్ల తర్వాత ప్రయత్నించండి."
                : "Too many grievance submissions from this network. Please wait {$seconds} seconds before submitting again.");
            Session::setFlash('old_input', $oldInput);
            redirect('submit');
        }

        $name = trim($_POST['citizen_name'] ?? '');
        $phone = trim($_POST['citizen_phone'] ?? '');
        $villageId = (int)($_POST['village_id'] ?? 0);
        $categoryId = (int)($_POST['category_id'] ?? 0);
        $description = trim($_POST['description'] ?? '');

        // 1. Validation: Citizen Name
        if (mb_strlen($name) < 2 || mb_strlen($name) > 100) {
            Session::setFlash('error', current_lang() === 'te' 
                ? 'దయచేసి మీ సరైన పూర్తి పేరును నమోదు చేయండి (కనీసం 2 అక్షరాలు).' 
                : 'Please enter a valid full name (minimum 2 characters).');
            Session::setFlash('old_input', $oldInput);
            redirect('submit');
        }

        // 2. Validation: 10-digit mobile number
        if (!preg_match('/^[6-9]\d{9}$/', $phone)) {
            Session::setFlash('error', current_lang() === 'te'
                ? 'దయచేసి సరైన 10 అంకెల మొబైల్ నంబరును నమోదు చేయండి.'
                : 'Please enter a valid 10-digit mobile number starting with 6-9.');
            Session::setFlash('old_input', $oldInput);
            redirect('submit');
        }

        // 3. Validation: Village & Category selection
        if ($villageId <= 0) {
            Session::setFlash('error', current_lang() === 'te'
                ? 'దయచేసి మీ గ్రామాన్ని ఎంచుకోండి.'
                : 'Please select your Gram Panchayat village.');
            Session::setFlash('old_input', $oldInput);
            redirect('submit');
        }

        if ($categoryId <= 0) {
            Session::setFlash('error', current_lang() === 'te'
                ? 'దయచేసి సమస్య విభాగాన్ని ఎంచుకోండి.'
                : 'Please select a problem category.');
            Session::setFlash('old_input', $oldInput);
            redirect('submit');
        }

        // 4. Validation: Description
        if (mb_strlen($description) < 10) {
            Session::setFlash('error', current_lang() === 'te'
                ? 'దయచేసి సమస్య వివరాలను మరింత స్పష్టంగా వివరించండి (కనీసం 10 అక్షరాలు).'
                : 'Please provide a clear description of the problem (minimum 10 characters).');
            Session::setFlash('old_input', $oldInput);
            redirect('submit');
        }

        // 5. Create Grievance Record
        try {
            $photoFile = (!empty($_FILES['photo']['name'])) ? $_FILES['photo'] : null;
            $result = GrievanceService::createGrievance($_POST, $photoFile);

            // Record successful submission in rate limiter
            RateLimiter::hit($rateKey, 300);

            // Store submission result in session for display
            Session::set('last_submission', $result);

            // Redirect to success confirmation page with Grievance ID
            redirect('submit/success?id=' . urlencode($result['tracking_id']));

        } catch (Exception $e) {
            Session::setFlash('error', $e->getMessage());
            Session::setFlash('old_input', $oldInput);
            redirect('submit');
        }
    }

    /**
     * Display the submission success and Grievance ID confirmation page
     */
    public function showSuccess(): void {
        $trackingId = trim($_GET['id'] ?? '');

        if (empty($trackingId)) {
            $last = Session::get('last_submission');
            $trackingId = $last['tracking_id'] ?? '';
        }

        if (empty($trackingId)) {
            redirect('');
        }

        $grievance = Grievance::findByTrackingId($trackingId);

        if (!$grievance) {
            redirect('');
        }

        $attachments = Grievance::getAttachments((int)$grievance['id']);

        view('citizen/success', [
            'title' => __('citizen.success_heading') . ' — ' . __('common.app_name'),
            'grievance' => $grievance,
            'attachments' => $attachments
        ]);
    }
}
