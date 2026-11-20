<?php 
$activeLang = current_lang();
view('layouts/header', ['title' => __('citizen.success_heading') . ' — ' . __('common.app_name')]); 
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">

            <!-- Success Card -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden text-center bg-white">
                
                <!-- Card Header -->
                <div class="bg-success text-white py-5 px-4">
                    <div class="bg-white text-success rounded-circle d-inline-flex align-items-center justify-content-center p-3 mb-3 shadow-sm animate-bounce" style="width: 76px; height: 76px;">
                        <i class="bi bi-check2-circle fs-1"></i>
                    </div>
                    <h2 class="fw-bold mb-2 font-telugu"><?= e(__('citizen.success_heading')) ?></h2>
                    <p class="text-white-50 mb-0 mx-auto" style="max-width: 500px;">
                        <?= e(__('citizen.success_subheading')) ?>
                    </p>
                </div>

                <!-- Grievance ID Callout Box -->
                <div class="card-body p-4 p-md-5">
                    
                    <div class="p-4 rounded-4 bg-light border border-success-subtle mb-4">
                        <div class="text-muted small text-uppercase fw-semibold mb-1">
                            <?= e(__('citizen.label_tracking_id')) ?>
                        </div>
                        <div class="display-6 fw-bold text-success font-monospace my-2 tracking-number" id="trackingIdText">
                            <?= e($grievance['tracking_id']) ?>
                        </div>
                        <button type="button" id="copyIdBtn" class="btn btn-outline-success btn-sm rounded-pill px-3 py-1 mt-2">
                            <i class="bi bi-clipboard me-1"></i> <?= e(__('citizen.copy_id_btn')) ?>
                        </button>
                        <div id="copyNotice" class="text-success small fw-medium mt-1" style="display: none;">
                            <i class="bi bi-check-lg"></i> <?= e(__('citizen.id_copied')) ?>
                        </div>
                    </div>

                    <!-- Citizen Advice Banner -->
                    <div class="alert alert-warning border-0 rounded-3 text-start small mb-4 d-flex align-items-start gap-2 p-3">
                        <i class="bi bi-exclamation-circle-fill text-warning-emphasis fs-5 mt-1"></i>
                        <span class="text-dark">
                            <?= e(__('citizen.advice_keep_id')) ?>
                        </span>
                    </div>

                    <!-- Grievance Summary Information -->
                    <div class="text-start border rounded-4 p-4 mb-4 bg-white">
                        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                            <i class="bi bi-card-checklist me-1 text-success"></i> నమోదు వివరాలు (Submission Summary)
                        </h6>
                        <dl class="row mb-0 small">
                            <dt class="col-sm-4 text-muted"><?= e(__('citizen.summary_village')) ?></dt>
                            <dd class="col-sm-8 fw-semibold"><?= e($activeLang === 'te' ? $grievance['village_name_te'] : $grievance['village_name_en']) ?></dd>

                            <?php if ($grievance['ward_number']): ?>
                                <dt class="col-sm-4 text-muted"><?= e(__('citizen.summary_ward')) ?></dt>
                                <dd class="col-sm-8"><?= e($grievance['ward_number']) ?> (<?= e($grievance['ward_name']) ?>)</dd>
                            <?php endif; ?>

                            <dt class="col-sm-4 text-muted"><?= e(__('citizen.summary_category')) ?></dt>
                            <dd class="col-sm-8 fw-semibold text-primary">
                                <?= e($activeLang === 'te' ? $grievance['category_name_te'] : $grievance['category_name_en']) ?>
                            </dd>

                            <dt class="col-sm-4 text-muted">బాధ్యత గల విభాగం (Department):</dt>
                            <dd class="col-sm-8"><?= e($activeLang === 'te' ? $grievance['dept_name_te'] : $grievance['dept_name_en']) ?></dd>

                            <dt class="col-sm-4 text-muted"><?= e(__('citizen.summary_sla')) ?></dt>
                            <dd class="col-sm-8">
                                <span class="badge bg-warning-subtle text-dark border border-warning-subtle">
                                    <?= e($grievance['sla_hours']) ?> గంటల్లో (Within <?= e($grievance['sla_hours']) ?> Hours)
                                </span>
                            </dd>

                            <?php if (!empty($grievance['landmark'])): ?>
                                <dt class="col-sm-4 text-muted">ల్యాండ్‌మార్క్ (Landmark):</dt>
                                <dd class="col-sm-8"><?= e($grievance['landmark']) ?></dd>
                            <?php endif; ?>

                            <dt class="col-sm-4 text-muted">వివరాలు (Description):</dt>
                            <dd class="col-sm-8 text-secondary mb-0"><?= nl2br(e($grievance['description'])) ?></dd>
                        </dl>

                        <!-- Attached Photo Thumbnail -->
                        <?php if (!empty($attachments)): ?>
                            <div class="mt-3 pt-3 border-top">
                                <span class="text-muted small d-block mb-2">జతచేసిన ఫోటో (Attached Photo):</span>
                                <?php foreach ($attachments as $att): ?>
                                    <div class="d-inline-block border rounded-3 p-1 shadow-sm">
                                        <img src="<?= url($att['file_path']) ?>" alt="Photo" class="rounded-2" style="max-height: 140px; max-width: 100%; object-fit: cover;">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Navigation Action Buttons -->
                    <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
                        <a href="<?= url('') ?>" class="btn btn-outline-secondary rounded-pill px-4 py-2">
                            <i class="bi bi-house me-1"></i> <?= e(__('citizen.btn_return_home')) ?>
                        </a>
                        <a href="<?= url('track?id=' . urlencode($grievance['tracking_id']) . '&phone=' . urlencode($grievance['citizen_phone'])) ?>" class="btn btn-success rounded-pill px-4 py-2 shadow-sm fw-semibold">
                            <i class="bi bi-binoculars me-1"></i> <?= e(__('citizen.btn_track_now')) ?>
                        </a>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<script>
document.getElementById('copyIdBtn').addEventListener('click', function() {
    const trackingId = document.getElementById('trackingIdText').textContent.trim();
    navigator.clipboard.writeText(trackingId).then(() => {
        const notice = document.getElementById('copyNotice');
        notice.style.display = 'block';
        setTimeout(() => { notice.style.display = 'none'; }, 4000);
    }).catch(err => {
        console.error('Failed to copy ID', err);
    });
});
</script>

<?php view('layouts/footer'); ?>
