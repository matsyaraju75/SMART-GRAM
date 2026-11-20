<?php 
$activeLang = current_lang();
view('layouts/header', ['title' => __('citizen.about_page_title')]); 
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= url('') ?>" class="text-success text-decoration-none"><?= e(__('common.nav_home')) ?></a></li>
                    <li class="breadcrumb-item active"><?= e(__('common.nav_about')) ?></li>
                </ol>
            </nav>

            <!-- Main About Card -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-header bg-success text-white p-4 p-md-5 text-center border-0">
                    <div class="bg-white text-success rounded-circle d-inline-flex align-items-center justify-content-center p-3 mb-3 shadow-sm" style="width: 64px; height: 64px;">
                        <i class="bi bi-info-circle fs-2"></i>
                    </div>
                    <h2 class="fw-bold mb-2 font-telugu"><?= e(__('citizen.about_page_title')) ?></h2>
                    <p class="text-white-50 mb-0 mx-auto" style="max-width: 600px;">
                        <?= e(__('common.tagline')) ?> — <?= e(__('common.footer_tagline')) ?>
                    </p>
                </div>

                <div class="card-body p-4 p-md-5">
                    
                    <!-- Core Mission -->
                    <div class="mb-5">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="badge bg-success-subtle text-success p-2 rounded-circle"><i class="bi bi-bullseye fs-5"></i></span>
                            <h4 class="fw-bold mb-0 text-dark font-telugu"><?= e(__('citizen.about_mission_title')) ?></h4>
                        </div>
                        <p class="text-secondary leading-relaxed fs-6">
                            <?= e(__('citizen.about_mission_desc')) ?>
                        </p>
                    </div>

                    <!-- 3 Pillars of Smart Gram -->
                    <div class="row g-4 mb-5">
                        <div class="col-md-4">
                            <div class="p-4 rounded-4 bg-light h-100 border">
                                <div class="text-success mb-3"><i class="bi bi-unlock-fill fs-2"></i></div>
                                <h5 class="fw-bold mb-2"><?= e(__('citizen.no_login_title')) ?></h5>
                                <p class="text-muted small mb-0"><?= e(__('citizen.no_login_desc')) ?></p>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="p-4 rounded-4 bg-light h-100 border">
                                <div class="text-primary mb-3"><i class="bi bi-eye-fill fs-2"></i></div>
                                <h5 class="fw-bold mb-2"><?= e(__('citizen.about_transparency_title')) ?></h5>
                                <p class="text-muted small mb-0"><?= e(__('citizen.about_transparency_desc')) ?></p>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="p-4 rounded-4 bg-light h-100 border">
                                <div class="text-warning-emphasis mb-3"><i class="bi bi-camera-fill fs-2"></i></div>
                                <h5 class="fw-bold mb-2"><?= e(__('citizen.about_evidence_title')) ?></h5>
                                <p class="text-muted small mb-0"><?= e(__('citizen.about_evidence_desc')) ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Call To Action -->
                    <div class="p-4 rounded-4 bg-success-subtle text-center">
                        <h5 class="fw-bold text-success mb-2 font-telugu"><?= e(__('citizen.hero_title')) ?></h5>
                        <p class="text-secondary small mb-3">
                            <?= e(__('citizen.hero_subtitle')) ?>
                        </p>
                        <div class="d-flex justify-content-center gap-3 flex-wrap">
                            <a href="<?= url('') ?>" class="btn btn-success px-4 py-2 rounded-pill fw-semibold shadow-sm">
                                <i class="bi bi-house me-1"></i> <?= e(__('common.btn_home')) ?>
                            </a>
                            <a href="<?= switch_lang_url($activeLang === 'te' ? 'en' : 'te') ?>" class="btn btn-outline-success px-4 py-2 rounded-pill fw-semibold">
                                <i class="bi bi-translate me-1"></i> <?= $activeLang === 'te' ? 'View in English' : 'తెలుగులో చూడండి' ?>
                            </a>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<?php view('layouts/footer'); ?>
