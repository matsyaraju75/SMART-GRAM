<?php
// ==============================================================================
// Smart Gram - Staff & Officer Workflow Controller (Phase 6)
// ==============================================================================

namespace App\Controllers;

use App\Core\Csrf;
use App\Core\Session;
use App\Models\Grievance;
use App\Models\User;
use App\Services\AiAdvisoryService;
use App\Services\FileUploadService;
use Exception;

class WorkflowController {
    /**
     * Internal Grievance Detail & Action View
     */
    public function show(): void {
        $user = auth();
        if (!$user) {
            redirect('login');
        }

        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            redirect('dashboard');
        }

        $grievance = Grievance::findById($id);
        if (!$grievance) {
            Session::setFlash('error', 'Grievance not found.');
            redirect('dashboard');
        }

        // Authorization check: Verify user has permission to access this grievance based on role & geographic jurisdiction
        if (!User::canAccessGrievance($user, $grievance)) {
            Session::setFlash('error', 'Unauthorized: You do not have permission to view grievances outside your assigned jurisdiction.');
            redirect($user['role'] === 'officer' ? 'officer' : 'dashboard');
        }

        $attachments = Grievance::getAttachments($id);
        $timeline = Grievance::getTimeline($id);
        $feedback = Grievance::getFeedback($id);
        $officers = Grievance::getEligibleOfficers($grievance['department_id'], $grievance['village_id'], $grievance);
        $departments = Grievance::getAllDepartments();
        $categories = Grievance::getCategories();

        // AI Advisory Integration (Human-in-the-Loop)
        $aiAdvice = null;
        if (in_array($user['role'], ['staff', 'admin'], true)) {
            $aiAdvice = AiAdvisoryService::analyzeGrievance(
                $grievance['description'],
                !empty($grievance['ward_id']) ? (int)$grievance['ward_id'] : null,
                (int)$grievance['id']
            );
        }

        view('grievance/detail', [
            'title'       => 'Grievance ' . $grievance['tracking_id'] . ' - Smart Gram',
            'user'        => $user,
            'grievance'   => $grievance,
            'attachments' => $attachments,
            'timeline'    => $timeline,
            'feedback'    => $feedback,
            'officers'    => $officers,
            'departments' => $departments,
            'categories'  => $categories,
            'aiAdvice'    => $aiAdvice
        ]);
    }

    /**
     * Staff Action: Verify Grievance
     */
    public function verify(): void {
        Csrf::validate();
        $user = auth();
        if (!$user || !in_array($user['role'], ['staff', 'admin'], true)) {
            Session::setFlash('error', 'Unauthorized role.');
            redirect('login');
        }

        $id = (int)($_POST['id'] ?? 0);
        $remarks = trim($_POST['remarks'] ?? '');

        $grievance = Grievance::findById($id);
        if (!$grievance || !User::canAccessGrievance($user, $grievance)) {
            Session::setFlash('error', 'Unauthorized: Grievance is outside your administrative jurisdiction.');
            redirect('dashboard');
        }

        try {
            Grievance::verifyGrievance($id, (int)$user['id'], $remarks);
            Session::setFlash('success', __('workflow.msg_verified_success'));
        } catch (Exception $e) {
            Session::setFlash('error', 'Error: ' . $e->getMessage());
        }

        redirect("grievance/view?id=$id");
    }

    /**
     * Staff/Admin Action: Assign Field Officer & Priority
     */
    public function assign(): void {
        Csrf::validate();
        $user = auth();
        if (!$user || !in_array($user['role'], ['staff', 'admin'], true)) {
            Session::setFlash('error', 'Unauthorized role.');
            redirect('login');
        }

        $id = (int)($_POST['id'] ?? 0);
        $officerId = (int)($_POST['officer_id'] ?? 0);
        $priority = $_POST['priority'] ?? null;
        $remarks = trim($_POST['remarks'] ?? '');

        if ($officerId <= 0) {
            Session::setFlash('error', 'Please select an officer to assign.');
            redirect("grievance/view?id=$id");
        }

        $grievance = Grievance::findById($id);
        if (!$grievance || !User::canAccessGrievance($user, $grievance)) {
            Session::setFlash('error', 'Unauthorized: Grievance is outside your administrative jurisdiction.');
            redirect('dashboard');
        }

        // Verify that the assigned officer is eligible for this grievance (department & location scope)
        $targetOfficer = User::findById($officerId);
        if (!$targetOfficer || !User::canAccessGrievance($targetOfficer, $grievance)) {
            Session::setFlash('error', 'Selected officer is not authorized or eligible for this grievance jurisdiction.');
            redirect("grievance/view?id=$id");
        }

        try {
            Grievance::assignOfficer($id, $officerId, (int)$user['id'], $priority, $remarks);
            Session::setFlash('success', __('workflow.msg_assigned_success'));
        } catch (Exception $e) {
            Session::setFlash('error', 'Error: ' . $e->getMessage());
        }

        redirect("grievance/view?id=$id");
    }

    /**
     * Field Officer Action: Start Field Work
     */
    public function startWork(): void {
        Csrf::validate();
        $user = auth();
        if (!$user || !in_array($user['role'], ['officer', 'admin'], true)) {
            Session::setFlash('error', 'Unauthorized role.');
            redirect('login');
        }

        $id = (int)($_POST['id'] ?? 0);
        $remarks = trim($_POST['remarks'] ?? '');

        $grievance = Grievance::findById($id);
        if (!$grievance || !User::canAccessGrievance($user, $grievance)) {
            Session::setFlash('error', 'Unauthorized: Grievance is outside your administrative jurisdiction.');
            redirect('dashboard');
        }

        try {
            Grievance::startWork($id, (int)$user['id'], $remarks);
            Session::setFlash('success', __('workflow.msg_work_started'));
        } catch (Exception $e) {
            Session::setFlash('error', 'Error: ' . $e->getMessage());
        }

        redirect("grievance/view?id=$id");
    }

    /**
     * Progress Note Entry
     */
    public function addNote(): void {
        Csrf::validate();
        $user = auth();
        if (!$user) {
            redirect('login');
        }

        $id = (int)($_POST['id'] ?? 0);
        $remarks = trim($_POST['remarks'] ?? '');

        if (empty($remarks)) {
            Session::setFlash('error', 'Note content cannot be empty.');
            redirect("grievance/view?id=$id");
        }

        $grievance = Grievance::findById($id);
        if (!$grievance || !User::canAccessGrievance($user, $grievance)) {
            Session::setFlash('error', 'Unauthorized: Grievance is outside your administrative jurisdiction.');
            redirect('dashboard');
        }

        try {
            Grievance::addProgressNote($id, (int)$user['id'], $remarks);
            Session::setFlash('success', __('workflow.msg_note_added'));
        } catch (Exception $e) {
            Session::setFlash('error', 'Error: ' . $e->getMessage());
        }

        redirect("grievance/view?id=$id");
    }

    /**
     * Field Officer Action: Resolve Grievance with Mandatory Photographic Evidence
     */
    public function resolve(): void {
        Csrf::validate();
        $user = auth();
        if (!$user || !in_array($user['role'], ['officer', 'admin'], true)) {
            Session::setFlash('error', 'Unauthorized role.');
            redirect('login');
        }

        $id = (int)($_POST['id'] ?? 0);
        $remarks = trim($_POST['remarks'] ?? '');

        $grievance = Grievance::findById($id);
        if (!$grievance || !User::canAccessGrievance($user, $grievance)) {
            Session::setFlash('error', 'Unauthorized: Grievance is outside your administrative jurisdiction.');
            redirect('dashboard');
        }

        // Mandatory photographic evidence check
        if (!isset($_FILES['resolution_photo']) || $_FILES['resolution_photo']['error'] === UPLOAD_ERR_NO_FILE) {
            Session::setFlash('error', 'Mandatory resolution evidence photo is missing. Please attach a photo of the completed work.');
            redirect("grievance/view?id=$id");
        }

        try {
            $uploadData = FileUploadService::uploadImage($_FILES['resolution_photo']);
            Grievance::resolveGrievance($id, (int)$user['id'], $remarks, $uploadData);
            Session::setFlash('success', __('workflow.msg_resolved_success'));
        } catch (Exception $e) {
            Session::setFlash('error', 'Upload Error: ' . $e->getMessage());
        }

        redirect("grievance/view?id=$id");
    }

    /**
     * Staff/Admin Action: Reject Grievance with Mandatory Explanation
     */
    public function reject(): void {
        Csrf::validate();
        $user = auth();
        if (!$user || !in_array($user['role'], ['staff', 'admin'], true)) {
            Session::setFlash('error', 'Unauthorized role.');
            redirect('login');
        }

        $id = (int)($_POST['id'] ?? 0);
        $remarks = trim($_POST['remarks'] ?? '');

        if (empty($remarks)) {
            Session::setFlash('error', 'Rejection explanation is mandatory. Please provide a clear reason.');
            redirect("grievance/view?id=$id");
        }

        $grievance = Grievance::findById($id);
        if (!$grievance || !User::canAccessGrievance($user, $grievance)) {
            Session::setFlash('error', 'Unauthorized: Grievance is outside your administrative jurisdiction.');
            redirect('dashboard');
        }

        try {
            Grievance::rejectGrievance($id, (int)$user['id'], $remarks);
            Session::setFlash('success', __('workflow.msg_rejected_success'));
        } catch (Exception $e) {
            Session::setFlash('error', 'Error: ' . $e->getMessage());
        }

        redirect("grievance/view?id=$id");
    }

    /**
     * Staff/Admin Action: Refer Grievance to Another Department
     */
    public function refer(): void {
        Csrf::validate();
        $user = auth();
        if (!$user || !in_array($user['role'], ['staff', 'admin'], true)) {
            Session::setFlash('error', 'Unauthorized role.');
            redirect('login');
        }

        $id = (int)($_POST['id'] ?? 0);
        $newDeptId = (int)($_POST['department_id'] ?? 0);
        $remarks = trim($_POST['remarks'] ?? '');

        if ($newDeptId <= 0) {
            Session::setFlash('error', 'Please select a valid destination department.');
            redirect("grievance/view?id=$id");
        }

        $grievance = Grievance::findById($id);
        if (!$grievance || !User::canAccessGrievance($user, $grievance)) {
            Session::setFlash('error', 'Unauthorized: Grievance is outside your administrative jurisdiction.');
            redirect('dashboard');
        }

        try {
            Grievance::referDepartment($id, $newDeptId, (int)$user['id'], $remarks);
            Session::setFlash('success', __('workflow.msg_referred_success'));
        } catch (Exception $e) {
            Session::setFlash('error', 'Error: ' . $e->getMessage());
        }

        redirect("grievance/view?id=$id");
    }

    /**
     * Staff/Admin Action: Human-in-the-Loop AI Advisory Acceptance (Phase 7)
     */
    public function applyAi(): void {
        Csrf::validate();
        $user = auth();
        if (!$user || !in_array($user['role'], ['staff', 'admin'], true)) {
            Session::setFlash('error', 'Unauthorized role.');
            redirect('login');
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            redirect('dashboard');
        }

        $grievance = Grievance::findById($id);
        if (!$grievance || !User::canAccessGrievance($user, $grievance)) {
            Session::setFlash('error', 'Unauthorized: Grievance is outside your administrative jurisdiction.');
            redirect('dashboard');
        }

        try {
            AiAdvisoryService::applySuggestions($id, (int)$user['id'], [
                'category_id'     => !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null,
                'priority'        => $_POST['priority'] ?? null,
                'summary'         => $_POST['summary'] ?? null,
                'confidence'      => $_POST['confidence'] ?? null,
                'duplicate_of_id' => !empty($_POST['duplicate_of_id']) ? (int)$_POST['duplicate_of_id'] : null,
            ]);
            Session::setFlash('success', __('ai.msg_applied_success'));
        } catch (Exception $e) {
            Session::setFlash('error', 'Error: ' . $e->getMessage());
        }

        redirect("grievance/view?id=$id");
    }
}
