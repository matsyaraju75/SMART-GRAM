<?php
use App\Models\Grievance;
view('layouts/header', ['title' => __('workflow.title_detail') . ' - ' . $grievance['tracking_id']]);
$lang = current_lang();
$slaBadge = Grievance::getSlaBadge($grievance['sla_due_at'], $grievance['status']);
$progressPercent = Grievance::getProgressPercentage($grievance['status']);
?>

<div class="container py-4">

    <!-- Flash Message Alerts -->
    <?php if ($msg = flash('success')): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm mb-4 border-0 d-flex align-items-center gap-2" role="alert">
            <i class="bi bi-check-circle-fill fs-5 text-success"></i>
            <div><?= e($msg) ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($msg = flash('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm mb-4 border-0 d-flex align-items-center gap-2" role="alert">
            <i class="bi bi-exclamation-triangle-fill fs-5 text-danger"></i>
            <div><?= e($msg) ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Top Action & Navigation Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 pb-3 mb-4 border-bottom">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="<?= url($user['role'] === 'admin' ? 'admin' : ($user['role'] === 'staff' ? 'staff' : 'officer')) ?>" class="text-decoration-none small text-muted d-flex align-items-center gap-1">
                    <i class="bi bi-arrow-left"></i> <?= __('common.btn_back') ?>
                </a>
                <span class="text-muted small">|</span>
                <span class="badge bg-secondary-subtle text-secondary px-3 py-1 rounded-pill fw-bold text-uppercase">
                    <?= e($grievance['dept_name_en']) ?>
                </span>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <h3 class="fw-bold mb-0 font-monospace text-primary"><?= e($grievance['tracking_id']) ?></h3>
                <span class="badge <?= Grievance::getStatusBadgeClass($grievance['status']) ?> px-3 py-2 rounded-pill fs-7">
                    <?= strtoupper(str_replace('_', ' ', $grievance['status'])) ?>
                </span>
                <span class="badge <?= Grievance::getPriorityBadgeClass($grievance['priority']) ?> px-3 py-2 rounded-pill fs-7">
                    <?= strtoupper($grievance['priority']) ?>
                </span>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <span class="badge <?= $slaBadge['class'] ?> px-3 py-2 rounded-pill fs-7 d-flex align-items-center gap-1">
                <i class="bi <?= $slaBadge['icon'] ?>"></i> <?= $slaBadge['text'] ?>
            </span>
            <a href="<?= url('track?tracking_id=' . urlencode($grievance['tracking_id']) . '&phone=' . urlencode($grievance['citizen_phone'])) ?>" target="_blank" class="btn btn-outline-secondary btn-sm rounded-pill px-3 shadow-sm d-flex align-items-center gap-1">
                <i class="bi bi-eye"></i> <?= $lang === 'te' ? 'పౌరుల వీక్షణ' : 'Public View' ?>
            </a>
        </div>
    </div>

    <!-- Main Workflow Layout (2 Columns) -->
    <div class="row g-4">

        <!-- Left Column: Citizen, Incident & Timeline -->
        <div class="col-lg-7">

            <!-- Reopened Notice Banner if applicable -->
            <?php if ($grievance['status'] === 'reopened'): ?>
                <div class="alert alert-danger border-danger rounded-4 p-4 mb-4 shadow-sm">
                    <div class="d-flex align-items-start gap-3">
                        <i class="bi bi-exclamation-octagon-fill fs-2 text-danger flex-shrink-0"></i>
                        <div>
                            <h5 class="fw-bold text-danger mb-1"><?= __('workflow.reopened_alert_title') ?></h5>
                            <p class="small text-danger-emphasis mb-0"><?= __('workflow.reopened_alert_desc') ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Citizen & Location Card -->
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-person-circle text-primary me-2"></i><?= __('workflow.citizen_card_title') ?>
                    </h5>
                    <span class="badge bg-light text-muted border">Zero Citizen Login Submission</span>
                </div>

                <div class="row g-3">
                    <div class="col-sm-6">
                        <span class="text-muted small d-block">Citizen Name:</span>
                        <div class="fw-bold fs-6 text-dark"><?= e($grievance['citizen_name']) ?></div>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-muted small d-block">Contact Phone:</span>
                        <div class="fw-bold font-monospace text-dark d-flex align-items-center gap-2">
                            <span><?= e($grievance['citizen_phone']) ?></span>
                            <a href="tel:<?= e($grievance['citizen_phone']) ?>" class="btn btn-sm btn-outline-success rounded-pill py-0 px-2" title="Call">
                                <i class="bi bi-telephone-fill"></i>
                            </a>
                            <a href="https://wa.me/91<?= e($grievance['citizen_phone']) ?>" target="_blank" class="btn btn-sm btn-outline-success rounded-pill py-0 px-2" title="WhatsApp">
                                <i class="bi bi-whatsapp"></i>
                            </a>
                        </div>
                    </div>

                    <div class="col-12 border-top pt-3">
                        <span class="text-muted small fw-bold text-uppercase d-block mb-2">
                            <i class="bi bi-geo-alt-fill text-danger me-1"></i><?= $lang === 'te' ? 'భౌగోళిక / పరిపాలనా విభాగం (Geographic Location)' : 'Administrative & Geographic Location' ?>
                        </span>
                        <div class="p-3 bg-light rounded-3 border">
                            <div class="row g-3">
                                <div class="col-sm-4">
                                    <span class="text-muted small d-block"><?= $lang === 'te' ? 'రాష్ట్రం & జిల్లా:' : 'State & District:' ?></span>
                                    <span class="fw-semibold text-dark">
                                        <?= e($grievance['state_name_en'] ?? 'Andhra Pradesh') ?> &rarr; <?= e($grievance['district_name_en'] ?? 'Alluri Sitharama Raju') ?>
                                    </span>
                                </div>
                                <div class="col-sm-4">
                                    <span class="text-muted small d-block"><?= $lang === 'te' ? 'మండలం & పంచాయతీ:' : 'Mandal & Panchayat:' ?></span>
                                    <span class="fw-semibold text-dark">
                                        <?= e($grievance['mandal_name_en'] ?? 'N/A') ?> &rarr; <?= e($grievance['panchayat_name_en'] ?? 'N/A') ?>
                                    </span>
                                </div>
                                <div class="col-sm-4">
                                    <span class="text-muted small d-block"><?= $lang === 'te' ? 'గ్రామం & వార్డు:' : 'Village & Ward:' ?></span>
                                    <span class="fw-semibold text-dark">
                                        <?= e($grievance['village_name_te']) ?> (<?= e($grievance['village_name_en']) ?>)
                                    </span>
                                    <?php if (!empty($grievance['ward_number'])): ?>
                                        <div class="mt-1">
                                            <span class="badge bg-white text-dark border">
                                                <?= $lang === 'te' ? 'వార్డు' : 'Ward' ?> <?= e($grievance['ward_number']) ?>
                                            </span>
                                            <span class="small text-muted"><?= e($grievance['ward_name'] ?? '') ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php if (!empty($grievance['landmark'])): ?>
                                <div class="col-12 border-top pt-2 mt-2">
                                    <span class="text-muted small me-2"><?= $lang === 'te' ? 'గుర్తు / ల్యాండ్‌మార్క్:' : 'Landmark:' ?></span>
                                    <span class="small fw-semibold text-dark"><?= e($grievance['landmark']) ?></span>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 border-top pt-3">
                        <span class="text-muted small d-block mb-1">Problem Category:</span>
                        <div class="p-2 bg-light rounded-3 d-flex align-items-center justify-content-between">
                            <span class="fw-bold text-primary">
                                <?= $lang === 'te' ? e($grievance['category_name_te']) : e($grievance['category_name_en']) ?>
                            </span>
                            <span class="badge bg-secondary-subtle text-secondary">SLA: <?= $grievance['sla_hours'] ?>h</span>
                        </div>
                    </div>

                    <div class="col-12 border-top pt-3">
                        <span class="text-muted small d-block mb-1">Citizen Problem Description:</span>
                        <div class="p-3 bg-light rounded-3 border">
                            <?= nl2br(e($grievance['description'])) ?>
                        </div>
                    </div>

                    <?php if (!empty($grievance['ai_summary'])): ?>
                        <div class="col-12 border-top pt-3">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="text-primary small fw-bold d-flex align-items-center gap-1">
                                    <i class="bi bi-stars"></i> <?= __('ai.summary') ?>
                                </span>
                                <?php if (!empty($grievance['ai_confidence'])): ?>
                                    <span class="badge bg-primary-subtle text-primary border" style="font-size: 0.7rem;">
                                        <?= round($grievance['ai_confidence'] * 100) ?>% <?= __('ai.confidence') ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div class="p-3 bg-primary-subtle border border-primary-subtle rounded-3 small text-dark fw-medium">
                                <?= e($grievance['ai_summary']) ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Photographic Evidence Gallery -->
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-camera-fill text-primary me-2"></i><?= __('workflow.photos_evidence') ?>
                    </h5>
                    <span class="badge bg-light text-muted border"><?= count($attachments) ?> Files Attached</span>
                </div>

                <?php if (empty($attachments)): ?>
                    <div class="text-center py-4 text-muted bg-light rounded-3">
                        <i class="bi bi-image fs-1 d-block mb-1 text-muted"></i>
                        No photographic evidence attached.
                    </div>
                <?php else: ?>
                    <div class="row g-3">
                        <?php foreach ($attachments as $att): ?>
                            <div class="col-6 col-sm-4">
                                <div class="card h-100 border p-1 shadow-sm rounded-3 text-center">
                                    <a href="<?= url($att['file_path']) ?>" target="_blank" title="Click to view full photo">
                                        <img src="<?= url($att['file_path']) ?>" alt="Evidence" class="rounded-2" style="width: 100%; height: 120px; object-fit: cover;">
                                    </a>
                                    <div class="p-1">
                                        <span class="badge <?= $att['attachment_type'] === 'resolution_evidence' ? 'bg-success' : 'bg-primary' ?> text-white w-100 rounded-1" style="font-size: 0.7rem;">
                                            <?= $att['attachment_type'] === 'resolution_evidence' ? __('workflow.resolution_evidence') : __('workflow.citizen_photo') ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Complete Chronological Audit Timeline -->
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
                <h5 class="fw-bold mb-4 text-dark">
                    <i class="bi bi-clock-history text-primary me-2"></i><?= __('workflow.timeline_heading') ?>
                </h5>

                <div class="position-relative ps-4" style="border-left: 2px solid #e9ecef; margin-left: 10px;">
                    <?php foreach ($timeline as $t): ?>
                        <div class="position-relative mb-4">
                            <!-- Dot -->
                            <div class="position-absolute rounded-circle bg-primary" style="width: 14px; height: 14px; left: -31px; top: 4px; border: 3px solid #fff;"></div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="badge <?= $t['action'] === 'ai_accepted' ? 'bg-primary-subtle text-primary border border-primary' : 'bg-light text-dark border' ?> fw-bold text-uppercase" style="font-size: 0.72rem;">
                                    <?php if ($t['action'] === 'ai_accepted'): ?><i class="bi bi-stars me-1"></i><?php endif; ?>
                                    <?= strtoupper(str_replace('_', ' ', $t['action'])) ?>
                                </span>
                                <span class="text-muted small"><?= date('d M Y, h:i A', strtotime($t['created_at'])) ?></span>
                            </div>

                            <div class="small fw-semibold text-dark">
                                <?php if ($t['user_name']): ?>
                                    <i class="bi bi-person me-1"></i><?= e($t['user_name']) ?> (<?= e($t['user_role']) ?>)
                                <?php else: ?>
                                    <i class="bi bi-person me-1"></i>Citizen / System
                                <?php endif; ?>
                            </div>

                            <?php if (!empty($t['remarks'])): ?>
                                <div class="p-2 bg-light rounded-3 small text-muted mt-2 border">
                                    <?= nl2br(e($t['remarks'])) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>

        <!-- Right Column: Official Role Action Panels -->
        <div class="col-lg-5">

            <!-- Panel 0: AI Advisory Assistant (Human-in-the-Loop) -->
            <?php if (!empty($aiAdvice) && in_array($user['role'], ['staff', 'admin'], true)): ?>
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-white" style="border-top: 4px solid #6366f1 !important;">
                    <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <span class="p-2 rounded-3 bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                <i class="bi bi-robot fs-5"></i>
                            </span>
                            <div>
                                <h6 class="fw-bold mb-0 text-dark"><?= __('ai.card_title') ?></h6>
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle" style="font-size: 0.68rem;">
                                    <i class="bi bi-shield-check me-1"></i><?= __('ai.human_in_loop_badge') ?>
                                </span>
                            </div>
                        </div>
                        <?php if ($aiAdvice['is_ai_powered']): ?>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill" style="font-size: 0.7rem;">Gemini AI</span>
                        <?php else: ?>
                            <span class="badge bg-secondary-subtle text-secondary border px-2 py-1 rounded-pill" style="font-size: 0.7rem;">Heuristic NLP</span>
                        <?php endif; ?>
                    </div>

                    <div class="p-4 d-flex flex-column gap-3">
                        <div class="alert alert-info-subtle border-info-subtle rounded-3 p-2 px-3 small text-muted mb-0 d-flex align-items-start gap-2">
                            <i class="bi bi-info-circle-fill text-info mt-1"></i>
                            <span style="font-size: 0.78rem;"><?= __('ai.human_in_loop_notice') ?></span>
                        </div>

                        <!-- Duplicate Alert Banner -->
                        <?php if (!empty($aiAdvice['potential_duplicates'])): ?>
                            <div class="p-3 bg-warning-subtle border border-warning rounded-3">
                                <div class="d-flex align-items-center gap-2 text-warning-emphasis fw-bold mb-1">
                                    <i class="bi bi-exclamation-triangle-fill text-warning"></i>
                                    <span><?= __('ai.duplicate_alert_title') ?></span>
                                </div>
                                <p class="small text-muted mb-2"><?= __('ai.duplicate_alert_desc') ?></p>
                                <div class="d-flex flex-column gap-2">
                                    <?php foreach ($aiAdvice['potential_duplicates'] as $dup): ?>
                                        <div class="bg-white p-2 rounded-2 border d-flex justify-content-between align-items-center">
                                            <div>
                                                <a href="<?= url('grievance/view?id=' . $dup['id']) ?>" target="_blank" class="fw-bold font-monospace text-decoration-none small">
                                                    <?= e($dup['tracking_id']) ?> <i class="bi bi-box-arrow-up-right" style="font-size: 0.7rem;"></i>
                                                </a>
                                                <div class="text-muted" style="font-size: 0.75rem;">
                                                    <?= e($dup['citizen_name']) ?> &bull; <?= $lang === 'te' ? e($dup['category_name_te']) : e($dup['category_name_en']) ?>
                                                </div>
                                            </div>
                                            <span class="badge bg-warning text-dark fw-bold">
                                                <?= $dup['similarity_pct'] ?>% <?= __('ai.similarity_score') ?>
                                            </span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Recommendations Table / Specs -->
                        <div class="bg-light p-3 rounded-3 border">
                            <div class="row g-2">
                                <div class="col-sm-6">
                                    <span class="text-muted d-block" style="font-size: 0.75rem;"><?= __('ai.suggested_category') ?>:</span>
                                    <span class="fw-bold text-dark small">
                                        <?= $lang === 'te' ? e($aiAdvice['category_name_te']) : e($aiAdvice['category_name_en']) ?>
                                    </span>
                                </div>
                                <div class="col-sm-6">
                                    <span class="text-muted d-block" style="font-size: 0.75rem;"><?= __('ai.suggested_priority') ?>:</span>
                                    <span class="badge <?= Grievance::getPriorityBadgeClass($aiAdvice['suggested_priority']) ?> px-2 py-1 rounded-pill">
                                        <?= strtoupper($aiAdvice['suggested_priority']) ?>
                                    </span>
                                </div>
                                <div class="col-12 border-top pt-2">
                                    <span class="text-muted d-block" style="font-size: 0.75rem;"><?= __('ai.urgency_reason') ?>:</span>
                                    <span class="small text-muted fst-italic">
                                        <?= e($lang === 'te' ? $aiAdvice['urgency_reason_te'] : $aiAdvice['urgency_reason_en']) ?>
                                    </span>
                                </div>
                                <div class="col-12 border-top pt-2">
                                    <span class="text-muted d-block" style="font-size: 0.75rem;"><?= __('ai.summary') ?>:</span>
                                    <span class="small fw-semibold text-dark">
                                        <?= e($lang === 'te' ? $aiAdvice['summary_te'] : $aiAdvice['summary_en']) ?>
                                    </span>
                                </div>
                                <div class="col-12 border-top pt-2">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="text-muted" style="font-size: 0.75rem;"><?= __('ai.confidence') ?>:</span>
                                        <span class="fw-bold text-primary small"><?= round($aiAdvice['confidence'] * 100) ?>%</span>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: <?= round($aiAdvice['confidence'] * 100) ?>%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Apply Recommendations Action -->
                        <?php if (!in_array($grievance['status'], ['closed', 'rejected'], true)): ?>
                            <form method="POST" action="<?= url('grievance/ai-apply') ?>">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id" value="<?= $grievance['id'] ?>">
                                <input type="hidden" name="category_id" value="<?= $aiAdvice['suggested_category_id'] ?>">
                                <input type="hidden" name="priority" value="<?= $aiAdvice['suggested_priority'] ?>">
                                <input type="hidden" name="summary" value="<?= e($lang === 'te' ? $aiAdvice['summary_te'] : $aiAdvice['summary_en']) ?>">
                                <input type="hidden" name="confidence" value="<?= $aiAdvice['confidence'] ?>">
                                <?php if (!empty($aiAdvice['potential_duplicates'][0]['id'])): ?>
                                    <input type="hidden" name="duplicate_of_id" value="<?= $aiAdvice['potential_duplicates'][0]['id'] ?>">
                                <?php endif; ?>
                                
                                <button type="submit" class="btn btn-outline-primary rounded-pill px-4 shadow-sm w-100 fw-bold d-flex align-items-center justify-content-center gap-2">
                                    <i class="bi bi-stars"></i> <?= __('ai.btn_apply') ?>
                                </button>
                            </form>
                        <?php endif; ?>

                    </div>
                </div>
            <?php endif; ?>

            <!-- Panel 1: Staff / Admin Operations -->
            <?php if (in_array($user['role'], ['staff', 'admin'], true)): ?>
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-white">
                    <div class="card-header bg-success text-white py-3 px-4 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">
                            <i class="bi bi-person-badge-fill me-2"></i><?= __('workflow.badge_staff_action') ?>
                        </h5>
                        <span class="badge bg-white text-success fw-bold">Panchayat Secretary</span>
                    </div>

                    <div class="p-4 d-flex flex-column gap-4">

                        <!-- Action A: Verify Grievance -->
                        <?php if (in_array($grievance['status'], ['submitted'])): ?>
                            <div class="p-3 bg-light rounded-3 border">
                                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-check2-circle text-primary me-1"></i> 1. Verify Intake</h6>
                                <p class="small text-muted mb-3">Confirm the validity of this complaint and place it under formal review.</p>
                                <form method="POST" action="<?= url('grievance/verify') ?>">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= $grievance['id'] ?>">
                                    <input type="text" name="remarks" class="form-control form-control-sm mb-2" placeholder="Verification notes (optional)...">
                                    <button type="submit" class="btn btn-sm btn-primary rounded-pill px-4 shadow-sm w-100">
                                        <i class="bi bi-check-lg me-1"></i> <?= __('workflow.btn_verify') ?>
                                    </button>
                                </form>
                            </div>
                        <?php endif; ?>

                        <!-- Action B: Assign Field Officer -->
                        <?php if (in_array($grievance['status'], ['submitted', 'under_review', 'assigned', 'reopened', 'referred'])): ?>
                            <div class="p-3 bg-light rounded-3 border">
                                <h6 class="fw-bold text-dark mb-2">
                                    <i class="bi bi-person-plus-fill text-success me-1"></i> 
                                    <?= $grievance['assigned_to_user_id'] ? __('workflow.btn_reassign') : __('workflow.btn_assign') ?>
                                </h6>
                                <p class="small text-muted mb-3">Assign an active field officer and adjust priority if critical.</p>

                                <form method="POST" action="<?= url('grievance/assign') ?>">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= $grievance['id'] ?>">

                                    <div class="mb-2">
                                        <label class="form-label small fw-bold text-muted"><?= __('workflow.select_officer') ?> *</label>
                                        <select name="officer_id" class="form-select form-select-sm" required>
                                            <option value="">-- <?= __('workflow.select_officer') ?> --</option>
                                            <?php foreach ($officers as $off): ?>
                                                <option value="<?= $off['id'] ?>" <?= (string)$grievance['assigned_to_user_id'] === (string)$off['id'] ? 'selected' : '' ?>>
                                                    <?= e($off['full_name']) ?> (<?= e($off['dept_name_en'] ?? 'General') ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="mb-2">
                                        <label class="form-label small fw-bold text-muted"><?= __('workflow.select_priority') ?></label>
                                        <select name="priority" class="form-select form-select-sm">
                                            <option value="low" <?= $grievance['priority'] === 'low' ? 'selected' : '' ?>>Low</option>
                                            <option value="medium" <?= $grievance['priority'] === 'medium' ? 'selected' : '' ?>>Medium</option>
                                            <option value="high" <?= $grievance['priority'] === 'high' ? 'selected' : '' ?>>High</option>
                                            <option value="critical" <?= $grievance['priority'] === 'critical' ? 'selected' : '' ?>>Critical</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-muted">Instructions / Remarks</label>
                                        <textarea name="remarks" rows="2" class="form-control form-control-sm" placeholder="<?= __('workflow.remarks_placeholder') ?>"></textarea>
                                    </div>

                                    <button type="submit" class="btn btn-sm btn-success rounded-pill px-4 shadow-sm w-100">
                                        <i class="bi bi-send-fill me-1"></i> <?= __('workflow.btn_assign') ?>
                                    </button>
                                </form>
                            </div>
                        <?php endif; ?>

                        <!-- Action C: Refer to Another Department -->
                        <div class="accordion" id="referAccordion">
                            <div class="accordion-item border rounded-3 overflow-hidden">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed py-2 px-3 small fw-bold text-dark bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapseRefer">
                                        <i class="bi bi-arrow-left-right text-warning me-2"></i> <?= __('workflow.btn_refer') ?>
                                    </button>
                                </h2>
                                <div id="collapseRefer" class="accordion-collapse collapse" data-bs-parent="#referAccordion">
                                    <div class="accordion-body p-3">
                                        <form method="POST" action="<?= url('grievance/refer') ?>">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="id" value="<?= $grievance['id'] ?>">

                                            <div class="mb-2">
                                                <label class="form-label small fw-bold text-muted"><?= __('workflow.select_department') ?> *</label>
                                                <select name="department_id" class="form-select form-select-sm" required>
                                                    <?php foreach ($departments as $d): ?>
                                                        <option value="<?= $d['id'] ?>" <?= (string)$grievance['department_id'] === (string)$d['id'] ? 'selected' : '' ?>>
                                                            <?= e($d['name_en']) ?> (<?= e($d['name_te']) ?>)
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                            <div class="mb-2">
                                                <label class="form-label small fw-bold text-muted">Referral Reason</label>
                                                <input type="text" name="remarks" class="form-control form-control-sm" placeholder="Reason for transfer..." required>
                                            </div>

                                            <button type="submit" class="btn btn-sm btn-warning text-dark rounded-pill px-3 w-100 fw-bold">
                                                Transfer Department
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action D: Reject Grievance -->
                        <?php if (!in_array($grievance['status'], ['closed', 'resolved', 'rejected'])): ?>
                            <div class="accordion" id="rejectAccordion">
                                <div class="accordion-item border rounded-3 overflow-hidden">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed py-2 px-3 small fw-bold text-danger bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapseReject">
                                            <i class="bi bi-x-circle text-danger me-2"></i> <?= __('workflow.btn_reject') ?>
                                        </button>
                                    </h2>
                                    <div id="collapseReject" class="accordion-collapse collapse" data-bs-parent="#rejectAccordion">
                                        <div class="accordion-body p-3">
                                            <form method="POST" action="<?= url('grievance/reject') ?>">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="id" value="<?= $grievance['id'] ?>">

                                                <div class="mb-2">
                                                    <label class="form-label small fw-bold text-muted">Mandatory Rejection Reason *</label>
                                                    <textarea name="remarks" rows="3" class="form-control form-control-sm" placeholder="<?= __('workflow.rejection_reason_placeholder') ?>" required></textarea>
                                                </div>

                                                <button type="submit" class="btn btn-sm btn-danger rounded-pill px-3 w-100 fw-bold">
                                                    Confirm Rejection
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>
            <?php endif; ?>

            <!-- Panel 2: Field Officer Operations -->
            <?php if (in_array($user['role'], ['officer', 'admin'], true)): ?>
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-white">
                    <div class="card-header bg-warning text-dark py-3 px-4 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">
                            <i class="bi bi-tools me-2"></i><?= __('workflow.badge_officer_action') ?>
                        </h5>
                        <span class="badge bg-dark text-white fw-bold">Field Officer</span>
                    </div>

                    <div class="p-4 d-flex flex-column gap-4">

                        <!-- Action 1: Start Work -->
                        <?php if (in_array($grievance['status'], ['assigned'])): ?>
                            <div class="p-3 bg-light rounded-3 border">
                                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-play-circle-fill text-primary me-1"></i> Start Field Work</h6>
                                <p class="small text-muted mb-3">Mark this grievance as active and underway on site.</p>
                                <form method="POST" action="<?= url('grievance/start-work') ?>">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= $grievance['id'] ?>">
                                    <input type="text" name="remarks" class="form-control form-control-sm mb-2" placeholder="On-site commencement note (optional)...">
                                    <button type="submit" class="btn btn-sm btn-primary rounded-pill px-4 shadow-sm w-100 fw-bold">
                                        <i class="bi bi-play-fill me-1"></i> <?= __('workflow.btn_start_work') ?>
                                    </button>
                                </form>
                            </div>
                        <?php endif; ?>

                        <!-- Action 2: Interim Progress Note -->
                        <?php if (in_array($grievance['status'], ['in_progress', 'assigned', 'reopened'])): ?>
                            <div class="p-3 bg-light rounded-3 border">
                                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-pencil-square text-secondary me-1"></i> <?= __('workflow.btn_add_note') ?></h6>
                                <p class="small text-muted mb-2">Record intermediate progress, findings, or delays.</p>
                                <form method="POST" action="<?= url('grievance/note') ?>">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= $grievance['id'] ?>">
                                    <textarea name="remarks" rows="2" class="form-control form-control-sm mb-2" placeholder="<?= __('workflow.note_placeholder') ?>" required></textarea>
                                    <button type="submit" class="btn btn-sm btn-outline-secondary rounded-pill px-3 w-100">
                                        <?= __('workflow.btn_add_note') ?>
                                    </button>
                                </form>
                            </div>
                        <?php endif; ?>

                        <!-- Action 3: Mark Resolved with Mandatory Photographic Evidence -->
                        <?php if (in_array($grievance['status'], ['in_progress', 'assigned', 'reopened'])): ?>
                            <div class="p-3 bg-success-subtle border border-success-subtle rounded-3">
                                <h6 class="fw-bold text-success mb-2">
                                    <i class="bi bi-patch-check-fill text-success me-1"></i> 
                                    <?= __('workflow.btn_resolve') ?>
                                </h6>
                                <p class="small text-success-emphasis mb-3">
                                    Resolving requires uploading a clear photo of the completed work on site.
                                </p>

                                <form method="POST" action="<?= url('grievance/resolve') ?>" enctype="multipart/form-data">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= $grievance['id'] ?>">

                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-dark">
                                            <?= __('workflow.label_resolution_photo') ?> *
                                        </label>
                                        <input type="file" name="resolution_photo" class="form-control form-control-sm bg-white" accept="image/jpeg,image/png,image/webp" required>
                                        <div class="form-text small" style="font-size: 0.75rem;"><?= __('workflow.help_resolution_photo') ?></div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-dark">Field Completion Remarks *</label>
                                        <textarea name="remarks" rows="3" class="form-control form-control-sm bg-white" placeholder="<?= __('workflow.resolution_remarks_placeholder') ?>" required></textarea>
                                    </div>

                                    <button type="submit" class="btn btn-success rounded-pill px-4 shadow-sm w-100 fw-bold">
                                        <i class="bi bi-check2-circle me-1"></i> Submit Resolution Evidence
                                    </button>
                                </form>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>
            <?php endif; ?>

            <!-- Resolution / Closed Summary Card if Completed -->
            <?php if (in_array($grievance['status'], ['resolved', 'closed'])): ?>
                <div class="card border-success border-2 shadow-sm rounded-4 p-4 mb-4 bg-white">
                    <div class="d-flex align-items-center gap-2 mb-2 text-success">
                        <i class="bi bi-check-circle-fill fs-3"></i>
                        <h5 class="fw-bold mb-0">Resolution Recorded</h5>
                    </div>
                    <p class="small text-muted mb-3">
                        This grievance was resolved by the field officer and is in the citizen verification stage.
                    </p>

                    <?php if (!empty($feedback)): ?>
                        <div class="p-3 bg-light rounded-3 border">
                            <span class="text-muted small d-block">Citizen Verification Rating:</span>
                            <div class="d-flex align-items-center gap-1 my-1 text-warning fs-5">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="bi <?= $i <= ($feedback['rating'] ?? 0) ? 'bi-star-fill' : 'bi-star' ?>"></i>
                                <?php endfor; ?>
                                <span class="text-dark small ms-2 fw-bold"><?= $feedback['rating'] ?> / 5 Stars</span>
                            </div>
                            <?php if (!empty($feedback['citizen_comment'])): ?>
                                <p class="small text-muted mb-0 fst-italic">"<?= e($feedback['citizen_comment']) ?>"</p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        </div>
    </div>

</div>

<?php view('layouts/footer'); ?>
