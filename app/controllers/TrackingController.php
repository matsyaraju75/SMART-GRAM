<?php
// ==============================================================================
// Smart Gram - Citizen Grievance Tracking & Verification Controller
// (NO CITIZEN LOGIN REQUIRED - Verifies via Grievance ID + Registered Phone)
// ==============================================================================

namespace App\Controllers;

use App\Core\Session;
use App\Core\RateLimiter;
use App\Models\Grievance;
use Exception;

class TrackingController {
    /**
     * Show tracking lookup form or display grievance timeline details
     */
    public function showTrackForm(): void {
        $trackingId = trim($_GET['tracking_id'] ?? $_GET['id'] ?? $_POST['tracking_id'] ?? '');
        $phone = trim($_GET['phone'] ?? $_GET['citizen_phone'] ?? $_POST['citizen_phone'] ?? '');

        // If both tracking ID and phone are supplied, perform lookup
        if (!empty($trackingId) && !empty($phone)) {
            $ip = RateLimiter::clientIp();
            $rateKey = "tracking_lookup:{$ip}";

            // Anti-Enumeration Rate Limiting (30 lookups per 60 seconds)
            if (RateLimiter::tooManyAttempts($rateKey, 30)) {
                $seconds = RateLimiter::availableIn($rateKey);
                Session::setFlash('error', current_lang() === 'te'
                    ? "మీరు చాలా శోధనలను చేసారు. దయచేసి {$seconds} సెకన్ల తర్వాత ప్రయత్నించండి."
                    : "Too many tracking requests. Please wait {$seconds} seconds before trying again.");
                view('citizen/track', [
                    'title' => __('citizen.track_heading') . ' — ' . __('common.app_name'),
                    'old_id' => $trackingId,
                    'old_phone' => $phone
                ]);
                return;
            }

            RateLimiter::hit($rateKey, 60);

            $grievance = Grievance::findByTrackingIdAndPhone($trackingId, $phone);

            if ($grievance) {
                $timeline = Grievance::getTimeline((int)$grievance['id']);
                $attachments = Grievance::getAttachments((int)$grievance['id']);
                $feedback = Grievance::getFeedback((int)$grievance['id']);
                $progressPercent = Grievance::getProgressPercentage($grievance['status']);

                view('citizen/track_details', [
                    'title' => __('citizen.details_heading') . ' — ' . $grievance['tracking_id'],
                    'grievance' => $grievance,
                    'timeline' => $timeline,
                    'attachments' => $attachments,
                    'feedback' => $feedback,
                    'progressPercent' => $progressPercent
                ]);
                return;
            } else {
                Session::setFlash('error', __('citizen.err_not_found'));
            }
        }

        // Render clean lookup search form
        view('citizen/track', [
            'title' => __('citizen.track_heading') . ' — ' . __('common.app_name'),
            'old_id' => $trackingId,
            'old_phone' => $phone
        ]);
    }

    /**
     * Handle citizen feedback / reopen submission (NO CITIZEN LOGIN)
     */
    public function submitFeedback(): void {
        $grievanceId = (int)($_POST['grievance_id'] ?? 0);
        $trackingId = trim($_POST['tracking_id'] ?? '');
        $phone = trim($_POST['citizen_phone'] ?? '');
        $isSatisfied = (int)($_POST['is_satisfied'] ?? 1) === 1;
        $rating = !empty($_POST['rating']) ? (int)$_POST['rating'] : null;
        $comment = trim($_POST['comment'] ?? '');

        // Verification: Ensure tracking ID and phone match the grievance ID
        $grievance = Grievance::findByTrackingIdAndPhone($trackingId, $phone);

        if (!$grievance || (int)$grievance['id'] !== $grievanceId) {
            Session::setFlash('error', __('citizen.err_not_found'));
            redirect('track');
        }

        // Check if eligible for feedback / reopening
        if (!in_array($grievance['status'], ['resolved', 'citizen_verification'], true)) {
            Session::setFlash('error', 'Feedback or reopening is only allowed once the grievance is resolved.');
            redirect('track?id=' . urlencode($trackingId) . '&phone=' . urlencode($phone));
        }

        // If reopening (unsatisfied), a reason is required
        if (!$isSatisfied && mb_strlen($comment) < 5) {
            Session::setFlash('error', current_lang() === 'te' 
                ? 'దయచేసి సమస్య ఎందుకు పరిష్కారం కాలేదో స్పష్టంగా వివరించండి (కనీసం 5 అక్షరాలు).' 
                : 'Please explain why the issue was not resolved so officers can re-inspect.');
            redirect('track?id=' . urlencode($trackingId) . '&phone=' . urlencode($phone));
        }

        try {
            Grievance::saveFeedback($grievanceId, $isSatisfied, $rating, $comment);
            if ($isSatisfied) {
                Session::setFlash('success', current_lang() === 'te'
                    ? 'మీ సంతృప్తి రేటింగ్ విజయవంతంగా నమోదు చేయబడింది. సమస్య పరిష్కరించబడింది (Closed).'
                    : 'Thank you! Your resolution verification has been recorded and the grievance is now closed.');
            } else {
                Session::setFlash('success', current_lang() === 'te'
                    ? 'మీ ఫిర్యాదు తిరిగి తెరవబడింది (Reopened). పంచాయతీ అధికారులు దీనిని పునఃపరిశీలిస్తారు.'
                    : 'Grievance reopened successfully. It has been queued for re-inspection by Panchayat authorities.');
            }

        } catch (Exception $e) {
            Session::setFlash('error', 'Failed to record response: ' . $e->getMessage());
        }

        redirect('track?id=' . urlencode($trackingId) . '&phone=' . urlencode($phone));
    }
}
