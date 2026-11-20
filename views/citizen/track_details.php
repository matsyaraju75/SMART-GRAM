<?php 
$activeLang = current_lang();
$statusClass = \App\Models\Grievance::getStatusBadgeClass($grievance['status']);
$statusText = __('status.' . $grievance['status']);
view('layouts/header', ['title' => __('citizen.details_heading') . ' — ' . $grievance['tracking_id']]); 
?>

<div class="container py-4 py-md-5">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-9">

            <!-- Breadcrumb Navigation -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="<?= url('') ?>" class="text-success text-decoration-none"><?= e(__('common.nav_home')) ?></a></li>
                        <li class="breadcrumb-item"><a href="<?= url('track') ?>" class="text-primary text-decoration-none"><?= e(__('citizen.action_track_title')) ?></a></li>
                        <li class="breadcrumb-item active font-monospace"><?= e($grievance['tracking_id']) ?></li>
                    </ol>
                </nav>
                <a href="<?= url('track') ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                    <i class="bi bi-arrow-left me-1"></i> వేరే సమస్యను చూడండి
                </a>
            </div>

            <!-- Grievance Header Card -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-white">
                <div class="card-body p-4 p-md-5">
                    
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 border-bottom pb-4 mb-4">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="text-muted small text-uppercase fw-semibold"><?= e(__('citizen.label_tracking_id')) ?></span>
                                <button type="button" id="copyIdBtn" class="btn btn-link btn-sm p-0 text-success text-decoration-none" title="Copy ID">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                                <span id="copyNotice" class="badge bg-success-subtle text-success small" style="display:none;">Copied!</span>
                            </div>
                            <h2 class="display-6 fw-bold text-dark font-monospace mb-0" id="trackingIdText">
                                <?= e($grievance['tracking_id']) ?>
                            </h2>
                            <div class="text-muted small mt-1">
                                <?= e(__('citizen.label_submitted_on')) ?> <strong><?= date('d M Y, h:i A', strtotime($grievance['created_at'])) ?></strong>
                            </div>
                        </div>

                        <!-- Status Badge -->
                        <div class="text-md-end">
                            <span class="text-muted small d-block mb-1"><?= e(__('citizen.label_current_status')) ?></span>
                            <span class="badge <?= $statusClass ?> fs-6 px-3 py-2 rounded-pill fw-semibold font-telugu">
                                <?= e($statusText) ?>
                            </span>
                        </div>
                    </div>

                    <!-- Visual Progress Bar / Stepper -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between text-muted small fw-semibold mb-2">
                            <span>పురోగతి (Resolution Progress)</span>
                            <span><?= $progressPercent ?>%</span>
                        </div>
                        <div class="progress" style="height: 10px; border-radius: 10px; background-color: #e9ecef;">
                            <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" role="progressbar" 
                                 style="width: <?= $progressPercent ?>%;" 
                                 aria-valuenow="<?= $progressPercent ?>" aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>

                        <!-- Milestone Steps -->
                        <div class="row text-center mt-3 g-2" style="font-size: 0.76rem;">
                            <div class="col <?= $progressPercent >= 15 ? 'text-success fw-bold' : 'text-muted' ?>">
                                <i class="bi <?= $progressPercent >= 15 ? 'bi-check-circle-fill' : 'bi-circle' ?>"></i>
                                <div class="d-none d-sm-block">నమోదైంది (Submitted)</div>
                            </div>
                            <div class="col <?= $progressPercent >= 45 ? 'text-success fw-bold' : 'text-muted' ?>">
                                <i class="bi <?= $progressPercent >= 45 ? 'bi-check-circle-fill' : 'bi-circle' ?>"></i>
                                <div class="d-none d-sm-block">ధృవీకరణ (Verified)</div>
                            </div>
                            <div class="col <?= $progressPercent >= 60 ? 'text-success fw-bold' : 'text-muted' ?>">
                                <i class="bi <?= $progressPercent >= 60 ? 'bi-check-circle-fill' : 'bi-circle' ?>"></i>
                                <div class="d-none d-sm-block">అప్పగింత (Assigned)</div>
                            </div>
                            <div class="col <?= $progressPercent >= 75 ? 'text-success fw-bold' : 'text-muted' ?>">
                                <i class="bi <?= $progressPercent >= 75 ? 'bi-check-circle-fill' : 'bi-circle' ?>"></i>
                                <div class="d-none d-sm-block">పనిలో ఉంది (In Progress)</div>
                            </div>
                            <div class="col <?= $progressPercent >= 90 ? 'text-success fw-bold' : 'text-muted' ?>">
                                <i class="bi <?= $progressPercent >= 90 ? 'bi-check-circle-fill' : 'bi-circle' ?>"></i>
                                <div class="d-none d-sm-block">పరిష్కారం (Resolved)</div>
                            </div>
                            <div class="col <?= $progressPercent >= 100 ? 'text-success fw-bold' : 'text-muted' ?>">
                                <i class="bi <?= $progressPercent >= 100 ? 'bi-check-circle-fill' : 'bi-circle' ?>"></i>
                                <div class="d-none d-sm-block">ముగిసింది (Closed)</div>
                            </div>
                        </div>
                    </div>

                    <!-- Details Grid -->
                    <div class="p-3 p-md-4 rounded-4 bg-light border mb-4">
                        <div class="row g-3 small">
                            <div class="col-sm-6 col-md-3">
                                <span class="text-muted d-block"><?= e(__('citizen.summary_village')) ?></span>
                                <span class="fw-semibold text-dark"><?= e($activeLang === 'te' ? $grievance['village_name_te'] : $grievance['village_name_en']) ?></span>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <span class="text-muted d-block"><?= e(__('citizen.summary_ward')) ?></span>
                                <span class="fw-semibold text-dark"><?= e($grievance['ward_number'] ? $grievance['ward_number'] . ' (' . $grievance['ward_name'] . ')' : '—') ?></span>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <span class="text-muted d-block"><?= e(__('citizen.summary_category')) ?></span>
                                <span class="fw-semibold text-primary"><?= e($activeLang === 'te' ? $grievance['category_name_te'] : $grievance['category_name_en']) ?></span>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <span class="text-muted d-block">విభాగం (Department):</span>
                                <span class="fw-semibold text-dark"><?= e($activeLang === 'te' ? $grievance['dept_name_te'] : $grievance['dept_name_en']) ?></span>
                            </div>

                            <div class="col-sm-6 col-md-6 border-top pt-2">
                                <span class="text-muted d-block"><?= e(__('citizen.label_assigned_officer')) ?></span>
                                <?php if (!empty($grievance['assigned_officer_name'])): ?>
                                    <span class="fw-semibold text-success"><i class="bi bi-person-badge me-1"></i><?= e($grievance['assigned_officer_name']) ?></span>
                                <?php else: ?>
                                    <span class="text-muted font-italic"><?= e(__('citizen.label_not_assigned')) ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="col-sm-6 col-md-6 border-top pt-2">
                                <span class="text-muted d-block"><?= e(__('citizen.summary_sla')) ?></span>
                                <span class="badge bg-warning-subtle text-dark border border-warning-subtle">
                                    <i class="bi bi-clock-history me-1"></i>
                                    <?= date('d M Y, h:i A', strtotime($grievance['sla_due_at'])) ?> (<?= e($grievance['sla_hours']) ?>h)
                                </span>
                            </div>

                            <?php if (!empty($grievance['landmark'])): ?>
                                <div class="col-12 border-top pt-2">
                                    <span class="text-muted d-block">ల్యాండ్‌మార్క్ (Landmark):</span>
                                    <span class="text-dark"><?= e($grievance['landmark']) ?></span>
                                </div>
                            <?php endif; ?>

                            <div class="col-12 border-top pt-2">
                                <span class="text-muted d-block">సమస్య వివరాలు (Description):</span>
                                <p class="text-dark mb-0 bg-white p-3 rounded-3 border mt-1"><?= nl2br(e($grievance['description'])) ?></p>
                            </div>
                        </div>

                        <!-- Photographic Evidence / Attachments Gallery -->
                        <?php if (!empty($attachments)): ?>
                            <div class="mt-4 pt-3 border-top">
                                <h6 class="fw-bold text-dark small mb-3">
                                    <i class="bi bi-images me-1 text-primary"></i> ఫోటో ఆధారాలు (Photo Attachments)
                                </h6>
                                <div class="d-flex flex-wrap gap-3">
                                    <?php foreach ($attachments as $att): ?>
                                        <div class="position-relative border rounded-3 p-1 shadow-sm bg-white text-center" style="width: 140px;">
                                            <a href="<?= url($att['file_path']) ?>" target="_blank" title="Click to view full image">
                                                <img src="<?= url($att['file_path']) ?>" alt="Evidence" class="rounded-2" style="width: 100%; height: 95px; object-fit: cover;">
                                            </a>
                                            <span class="badge <?= $att['attachment_type'] === 'resolution_evidence' ? 'bg-success' : 'bg-secondary' ?> text-white w-100 rounded-1 mt-1" style="font-size: 0.68rem;">
                                                <?= $att['attachment_type'] === 'resolution_evidence' ? 'పరిష్కార సాక్ష్యం' : 'ఫిర్యాదు ఫోటో' ?>
                                            </span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                    </div>

                </div>
            </div>

            <!-- Section 15: Grievance Lifecycle Timeline Card -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-white">
                <div class="card-header bg-white p-4 border-0 pb-0">
                    <div class="d-flex align-items-center gap-2">
                        <div class="bg-primary text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                            <i class="bi bi-clock-history fs-5"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0 text-dark font-telugu"><?= e(__('citizen.timeline_title')) ?></h5>
                            <span class="text-muted small"><?= e(__('citizen.timeline_desc')) ?></span>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4 p-md-5 pt-3">
                    <div class="timeline position-relative ps-4 ms-2 border-start border-2 border-success-subtle">
                        <?php foreach ($timeline as $idx => $event): ?>
                            <div class="timeline-item position-relative mb-4">
                                <!-- Dot Marker -->
                                <div class="position-absolute translate-middle-x rounded-circle bg-white border border-2 border-success d-flex align-items-center justify-content-center" 
                                     style="left: -25px; top: 4px; width: 18px; height: 18px;">
                                    <div class="bg-success rounded-circle" style="width: 8px; height: 8px;"></div>
                                </div>

                                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-1 mb-1">
                                    <strong class="text-dark font-telugu">
                                        <?= e(__('status.' . $event['to_status'])) ?>
                                    </strong>
                                    <span class="text-muted small font-monospace">
                                        <?= date('d M Y, h:i A', strtotime($event['created_at'])) ?>
                                    </span>
                                </div>

                                <?php if (!empty($event['user_name'])): ?>
                                    <div class="small text-muted mb-1">
                                        <i class="bi bi-person me-1"></i> <?= e($event['user_name']) ?> 
                                        <span class="badge bg-light text-secondary border text-capitalize"><?= e($event['user_role'] ?? 'Staff') ?></span>
                                    </div>
                                <?php else: ?>
                                    <div class="small text-muted mb-1">
                                        <i class="bi bi-person-circle me-1"></i> పౌరుడు (Citizen)
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($event['remarks'])): ?>
                                    <div class="p-2 px-3 rounded-3 bg-light border text-secondary small mt-1">
                                        <?= nl2br(e($event['remarks'])) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Section 16: Citizen Verification, Feedback & Reopen Card -->
            <?php if (in_array($grievance['status'], ['resolved', 'citizen_verification', 'closed'], true) && !$feedback): ?>
                
                <!-- Feedback Action Form Card -->
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 border-top border-4 border-warning bg-white">
                    <div class="card-header bg-white p-4 border-0 pb-0">
                        <div class="d-flex align-items-center gap-2">
                            <div class="bg-warning text-dark rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                <i class="bi bi-patch-question-fill fs-5"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0 text-dark font-telugu"><?= e(__('citizen.feedback_card_title')) ?></h5>
                                <span class="text-muted small">దయచేసి మీ సమస్య పరిష్కారమైందో లేదో తెలియజేయండి</span>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-4 p-md-5 pt-3">
                        <form method="POST" action="<?= url('track/feedback') ?>">
                            <?= csrf_field() ?>
                            <input type="hidden" name="grievance_id" value="<?= e($grievance['id']) ?>">
                            <input type="hidden" name="tracking_id" value="<?= e($grievance['tracking_id']) ?>">
                            <input type="hidden" name="citizen_phone" value="<?= e($grievance['citizen_phone']) ?>">

                            <!-- Main Question: Was it resolved? -->
                            <div class="mb-4">
                                <label class="form-label fw-bold text-dark fs-6 font-telugu mb-3">
                                    <?= e(__('citizen.feedback_question')) ?> <span class="text-danger">*</span>
                                </label>

                                <div class="d-flex flex-column flex-sm-row gap-3">
                                    <div class="form-check p-3 border rounded-3 flex-grow-1 bg-light">
                                        <input class="form-check-input ms-0 me-2" type="radio" name="is_satisfied" id="satisfiedYes" value="1" checked>
                                        <label class="form-check-label fw-semibold text-success" for="satisfiedYes">
                                            <i class="bi bi-check-circle-fill me-1"></i> <?= e(__('citizen.feedback_opt_yes')) ?>
                                        </label>
                                    </div>

                                    <div class="form-check p-3 border rounded-3 flex-grow-1 bg-light">
                                        <input class="form-check-input ms-0 me-2" type="radio" name="is_satisfied" id="satisfiedNo" value="0">
                                        <label class="form-check-label fw-semibold text-danger" for="satisfiedNo">
                                            <i class="bi bi-arrow-repeat me-1"></i> <?= e(__('citizen.feedback_opt_no')) ?>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Container A: When Yes (Satisfaction Rating + Remarks) -->
                            <div id="satisfiedContainer">
                                <div class="mb-3">
                                    <label for="rating" class="form-label fw-semibold text-secondary small">
                                        <?= e(__('citizen.rating_label')) ?>
                                    </label>
                                    <div class="d-flex gap-2">
                                        <?php for ($star = 5; $star >= 1; $star--): ?>
                                            <div class="form-check form-check-inline border p-2 px-3 rounded-pill bg-light">
                                                <input class="form-check-input" type="radio" name="rating" id="star<?= $star ?>" value="<?= $star ?>" <?= $star === 5 ? 'checked' : '' ?>>
                                                <label class="form-check-label fw-bold text-dark" for="star<?= $star ?>">
                                                    <?= $star ?> <i class="bi bi-star-fill text-warning"></i>
                                                </label>
                                            </div>
                                        <?php endfor; ?>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="commentYes" class="form-label fw-semibold text-secondary small">
                                        <?= e(__('citizen.comment_label')) ?>
                                    </label>
                                    <textarea class="form-control rounded-3" id="commentYes" name="comment" rows="3" placeholder="మీ అభిప్రాయాన్ని రాయండి (ఐచ్ఛికం)..."></textarea>
                                </div>

                                <button type="submit" class="btn btn-success btn-lg w-100 rounded-pill py-3 fw-bold shadow-sm">
                                    <i class="bi bi-send-check-fill me-1"></i> <?= e(__('citizen.btn_submit_feedback')) ?>
                                </button>
                            </div>

                            <!-- Container B: When No (Reopen Grievance Reason Required) -->
                            <div id="reopenContainer" style="display: none;">
                                <div class="alert alert-danger border-0 rounded-3 small mb-3">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                    సమస్య ఇంకా పరిష్కారం కాకపోతే, ఈ ఫిర్యాదు తిరిగి తెరవబడుతుంది (Reopened) మరియు సంబంధిత ఉన్నతాధికారుల సమీక్షకు పంపబడుతుంది.
                                </div>

                                <div class="mb-4">
                                    <label for="commentNo" class="form-label fw-semibold text-danger small">
                                        <?= e(__('citizen.reopen_reason_label')) ?> <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control rounded-3 border-danger" id="commentNo" name="reopen_reason" rows="3" 
                                              placeholder="ఉదా: పైపు ఇంకా లీక్ అవుతోంది, నీరు సరిగా రావడం లేదు..."></textarea>
                                </div>

                                <button type="submit" class="btn btn-danger btn-lg w-100 rounded-pill py-3 fw-bold shadow-sm">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i> <?= e(__('citizen.btn_reopen_submit')) ?>
                                </button>
                            </div>

                        </form>
                    </div>
                </div>

            <?php elseif ($feedback): ?>

                <!-- Already Given Feedback Summary Card -->
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white border-start border-4 border-success">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill text-success fs-4"></i>
                            <h6 class="fw-bold mb-0 text-dark font-telugu">పౌరుల ధృవీకరణ పూర్తయింది (Verified by Citizen)</h6>
                        </div>
                        <?php if ($feedback['is_resolved_satisfied']): ?>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill">
                                పరిష్కారమైంది (Satisfied)
                            </span>
                        <?php else: ?>
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill">
                                తిరిగి తెరవబడింది (Reopened)
                            </span>
                        <?php endif; ?>
                    </div>

                    <?php if ($feedback['rating']): ?>
                        <div class="mb-1 text-warning">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="bi <?= $i <= $feedback['rating'] ? 'bi-star-fill' : 'bi-star' ?>"></i>
                            <?php endfor; ?>
                            <span class="text-muted small ms-1">(<?= e($feedback['rating']) ?>/5 నక్షత్రాలు)</span>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($feedback['citizen_comment'])): ?>
                        <p class="text-muted small mb-0 mt-2 bg-light p-3 rounded-3 border">
                            "<?= e($feedback['citizen_comment']) ?>"
                        </p>
                    <?php endif; ?>
                </div>

            <?php elseif ($grievance['status'] === 'reopened'): ?>

                <!-- Reopened Notice Banner -->
                <div class="alert alert-danger border-0 shadow-sm rounded-4 p-4 mb-4 d-flex align-items-center gap-3">
                    <div class="bg-danger text-white rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px;">
                        <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                    </div>
                    <div>
                        <strong class="d-block text-dark mb-1">ఫిర్యాదు తిరిగి తెరవబడింది (Grievance Reopened)</strong>
                        <span class="text-secondary small">
                            <?= e(__('citizen.status_reopened_notice')) ?>
                        </span>
                    </div>
                </div>

            <?php endif; ?>

        </div>
    </div>
</div>

<script>
// Clipboard Copy Helper
const copyBtn = document.getElementById('copyIdBtn');
const copyNotice = document.getElementById('copyNotice');
if (copyBtn && copyNotice) {
    copyBtn.addEventListener('click', function() {
        const idText = document.getElementById('trackingIdText').textContent.trim();
        navigator.clipboard.writeText(idText).then(() => {
            copyNotice.style.display = 'inline-block';
            setTimeout(() => { copyNotice.style.display = 'none'; }, 3000);
        });
    });
}

// Toggle between Satisfaction Feedback and Reopen Reason
const yesRadio = document.getElementById('satisfiedYes');
const noRadio = document.getElementById('satisfiedNo');
const satisfiedBox = document.getElementById('satisfiedContainer');
const reopenBox = document.getElementById('reopenContainer');
const commentYes = document.getElementById('commentYes');
const commentNo = document.getElementById('commentNo');

if (yesRadio && noRadio) {
    yesRadio.addEventListener('change', function() {
        if (this.checked) {
            satisfiedBox.style.display = 'block';
            reopenBox.style.display = 'none';
            if (commentNo) commentNo.name = 'unused';
            if (commentYes) commentYes.name = 'comment';
        }
    });

    noRadio.addEventListener('change', function() {
        if (this.checked) {
            satisfiedBox.style.display = 'none';
            reopenBox.style.display = 'block';
            if (commentYes) commentYes.name = 'unused';
            if (commentNo) commentNo.name = 'comment';
        }
    });
}
</script>

<?php view('layouts/footer'); ?>
