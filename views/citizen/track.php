<?php 
$activeLang = current_lang();
view('layouts/header', ['title' => __('citizen.track_heading') . ' — ' . __('common.app_name')]); 
?>

<div class="container py-4 py-md-5">
    <div class="row justify-content-center">
        <div class="col-lg-7 col-xl-6">

            <!-- Breadcrumb Navigation -->
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= url('') ?>" class="text-success text-decoration-none"><?= e(__('common.nav_home')) ?></a></li>
                    <li class="breadcrumb-item active"><?= e(__('citizen.action_track_title')) ?></li>
                </ol>
            </nav>

            <!-- Reassuring "No Login Required" Banner -->
            <div class="alert alert-light border border-primary-subtle shadow-sm rounded-4 p-3 mb-4 d-flex align-items-center gap-3">
                <div class="bg-primary text-white rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px;">
                    <i class="bi bi-shield-lock-fill fs-5"></i>
                </div>
                <div class="small">
                    <strong class="text-dark d-block"><?= e(__('citizen.no_login_title')) ?></strong>
                    <span class="text-secondary"><?= e(__('citizen.track_card_hint')) ?></span>
                </div>
            </div>

            <!-- Tracking Lookup Card -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden border-top border-4 border-primary">
                <div class="card-header bg-white p-4 text-center border-0 pb-2">
                    <div class="bg-primary-subtle text-primary rounded-circle d-inline-flex align-items-center justify-content-center p-3 mb-2 shadow-sm" style="width: 56px; height: 56px;">
                        <i class="bi bi-search fs-3"></i>
                    </div>
                    <h3 class="fw-bold mb-1 font-telugu text-dark"><?= e(__('citizen.track_heading')) ?></h3>
                    <p class="text-muted small mb-0"><?= e(__('citizen.track_subheading')) ?></p>
                </div>

                <div class="card-body p-4 p-md-5 pt-3">
                    <form method="GET" action="<?= url('track') ?>" autocomplete="off">
                        
                        <!-- Input 1: Grievance ID -->
                        <div class="mb-3">
                            <label for="tracking_id" class="form-label fw-semibold text-secondary small">
                                <i class="bi bi-hash me-1 text-primary"></i> <?= e(__('citizen.track_id_label')) ?> <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control form-control-lg rounded-3 font-monospace text-uppercase" 
                                   id="tracking_id" name="id" 
                                   value="<?= e($old_id ?? '') ?>" 
                                   placeholder="<?= e(__('citizen.track_id_placeholder')) ?>" 
                                   required autofocus>
                            <div class="form-text text-muted" style="font-size: 0.75rem;">
                                సమస్యను నమోదు చేసినప్పుడు వచ్చిన రసీదు ఐడీ (Example: GRV-2026-00001)
                            </div>
                        </div>

                        <!-- Input 2: Mobile Number Verification -->
                        <div class="mb-4">
                            <label for="citizen_phone" class="form-label fw-semibold text-secondary small">
                                <i class="bi bi-telephone me-1 text-primary"></i> <?= e(__('citizen.track_phone_label')) ?> <span class="text-danger">*</span>
                            </label>
                            <input type="tel" class="form-control form-control-lg rounded-3" 
                                   id="citizen_phone" name="phone" 
                                   value="<?= e($old_phone ?? '') ?>" 
                                   placeholder="<?= e(__('citizen.track_phone_placeholder')) ?>" 
                                   pattern="[6-9][0-9]{9}" maxlength="10" required>
                            <div class="form-text text-muted" style="font-size: 0.75rem;">
                                ఫిర్యాదు చేసేటప్పుడు నమోదు చేసిన 10 అంకెల ఫోన్ నంబర్
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary btn-lg w-100 py-3 rounded-pill fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-binoculars"></i>
                            <span><?= e(__('citizen.btn_track_submit')) ?></span>
                        </button>

                    </form>

                    <!-- Helpful Link -->
                    <div class="text-center mt-4 pt-3 border-top">
                        <span class="text-muted small">ఇంకా సమస్యను నమోదు చేయలేదా? (Not registered yet?)</span>
                        <div class="mt-1">
                            <a href="<?= url('submit') ?>" class="text-success text-decoration-none fw-semibold small">
                                <i class="bi bi-plus-circle me-1"></i> కొత్త సమస్యను నమోదు చేయండి (Submit New Grievance) &rarr;
                            </a>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<?php view('layouts/footer'); ?>
