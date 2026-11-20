<?php 
$activeLang = current_lang();
view('layouts/header', ['title' => __('common.app_title') . ' — ' . __('common.app_name')]); 
?>

<!-- Hero Section -->
<section class="py-5 bg-gradient" style="background: linear-gradient(180deg, rgba(25, 135, 84, 0.08) 0%, rgba(247, 249, 250, 1) 100%);">
    <div class="container text-center py-2">
        <div class="row justify-content-center">
            <div class="col-lg-9 col-xl-8">
                
                <!-- Gram Panchayat Badge -->
                <div class="d-inline-flex align-items-center gap-2 bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill mb-3 fw-semibold small shadow-sm">
                    <i class="bi bi-geo-alt-fill"></i>
                    <span>
                        <?= e($village ? ($activeLang === 'te' ? $village['name_te'] : $village['name_en']) . ' ' . ($activeLang === 'te' ? 'గ్రామ పంచాయతీ' : 'Gram Panchayat') : 'Gram Panchayat') ?>
                    </span>
                </div>

                <!-- Hero Heading -->
                <h1 class="display-5 fw-bold text-dark mb-2 font-telugu">
                    <?= e(__('citizen.hero_title')) ?>
                </h1>
                
                <h2 class="h5 text-success fw-semibold mb-3">
                    <?= e(__('common.tagline')) ?>
                </h2>

                <p class="lead text-secondary mb-4 mx-auto" style="max-width: 680px; font-size: 1.05rem;">
                    <?= e(__('citizen.hero_subtitle')) ?>
                </p>

                <!-- NO CITIZEN LOGIN Callout Banner -->
                <div class="alert alert-light border border-success-subtle shadow-sm rounded-4 text-start p-3 p-md-4 mb-4 mx-auto" style="max-width: 720px; background-color: #fbfdfc;">
                    <div class="d-flex align-items-start gap-3">
                        <div class="bg-success text-white rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px;">
                            <i class="bi bi-check2-circle fs-4"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                <strong class="text-dark fs-6"><?= e(__('citizen.no_login_title')) ?></strong>
                                <span class="badge bg-success-subtle text-success border border-success-subtle font-monospace" style="font-size: 0.72rem;">100% Open Access</span>
                            </div>
                            <p class="text-secondary small mb-0">
                                <?= e(__('citizen.no_login_desc')) ?>
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<!-- Primary Action Cards (The 3 Core Buttons from Master Spec) -->
<section class="py-4">
    <div class="container">
        <div class="row g-4 justify-content-center">
            
            <!-- 1. Submit Grievance Card -->
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden text-center p-4 action-card border-top border-4 border-success">
                    <div class="card-body d-flex flex-column align-items-center p-2">
                        <div class="icon-circle bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center mb-3 shadow-sm" style="width: 70px; height: 70px;">
                            <i class="bi bi-pencil-square fs-2"></i>
                        </div>
                        <h4 class="fw-bold mb-2 text-dark font-telugu"><?= e(__('citizen.action_submit_title')) ?></h4>
                        <p class="text-muted small mb-4 flex-grow-1">
                            <?= e(__('citizen.action_submit_desc')) ?>
                        </p>
                        <a href="<?= url('submit') ?>" class="btn btn-success btn-lg w-100 rounded-pill py-3 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-plus-circle"></i>
                            <span><?= e(__('citizen.action_submit_btn')) ?></span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- 2. Track Grievance Card -->
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden text-center p-4 action-card border-top border-4 border-primary">
                    <div class="card-body d-flex flex-column align-items-center p-2">
                        <div class="icon-circle bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center mb-3 shadow-sm" style="width: 70px; height: 70px;">
                            <i class="bi bi-search fs-2"></i>
                        </div>
                        <h4 class="fw-bold mb-2 text-dark font-telugu"><?= e(__('citizen.action_track_title')) ?></h4>
                        <p class="text-muted small mb-4 flex-grow-1">
                            <?= e(__('citizen.action_track_desc')) ?>
                        </p>
                        <a href="<?= url('track') ?>" class="btn btn-primary btn-lg w-100 rounded-pill py-3 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-binoculars"></i>
                            <span><?= e(__('citizen.action_track_btn')) ?></span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- 3. About Smart Gram Card -->
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden text-center p-4 action-card border-top border-4 border-warning">
                    <div class="card-body d-flex flex-column align-items-center p-2">
                        <div class="icon-circle bg-warning-subtle text-warning-emphasis rounded-circle d-flex align-items-center justify-content-center mb-3 shadow-sm" style="width: 70px; height: 70px;">
                            <i class="bi bi-info-circle-fill fs-2"></i>
                        </div>
                        <h4 class="fw-bold mb-2 text-dark font-telugu"><?= e(__('citizen.action_about_title')) ?></h4>
                        <p class="text-muted small mb-4 flex-grow-1">
                            <?= e(__('citizen.action_about_desc')) ?>
                        </p>
                        <a href="<?= url('about') ?>" class="btn btn-outline-dark btn-lg w-100 rounded-pill py-3 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-arrow-right-circle"></i>
                            <span><?= e(__('citizen.action_about_btn')) ?></span>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Grievance Problem Categories Grid -->
<section class="py-5 bg-white border-top border-bottom my-4">
    <div class="container">
        <div class="text-center mb-5">
            <span class="badge bg-light text-secondary border px-3 py-1 rounded-pill small mb-2 text-uppercase fw-semibold">
                Panchayat Responsibilities
            </span>
            <h3 class="fw-bold text-dark font-telugu"><?= e(__('citizen.categories_heading')) ?></h3>
            <p class="text-muted small mx-auto" style="max-width: 600px;">
                <?= e(__('citizen.categories_subheading')) ?>
            </p>
        </div>

        <div class="row g-3 g-md-4">
            <!-- 1. Drinking Water -->
            <div class="col-sm-6 col-lg-4">
                <div class="p-3 p-md-4 rounded-4 border bg-light h-100 d-flex gap-3 align-items-start category-box">
                    <div class="bg-primary text-white p-3 rounded-4 shadow-sm flex-shrink-0">
                        <i class="bi bi-droplet-half fs-4"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1 text-dark"><?= e(__('citizen.cat_water_title')) ?></h5>
                        <p class="text-muted small mb-0"><?= e(__('citizen.cat_water_desc')) ?></p>
                    </div>
                </div>
            </div>

            <!-- 2. Street Lights -->
            <div class="col-sm-6 col-lg-4">
                <div class="p-3 p-md-4 rounded-4 border bg-light h-100 d-flex gap-3 align-items-start category-box">
                    <div class="bg-warning text-dark p-3 rounded-4 shadow-sm flex-shrink-0">
                        <i class="bi bi-lightbulb-fill fs-4"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1 text-dark"><?= e(__('citizen.cat_electric_title')) ?></h5>
                        <p class="text-muted small mb-0"><?= e(__('citizen.cat_electric_desc')) ?></p>
                    </div>
                </div>
            </div>

            <!-- 3. Sanitation & Waste -->
            <div class="col-sm-6 col-lg-4">
                <div class="p-3 p-md-4 rounded-4 border bg-light h-100 d-flex gap-3 align-items-start category-box">
                    <div class="bg-success text-white p-3 rounded-4 shadow-sm flex-shrink-0">
                        <i class="bi bi-trash3-fill fs-4"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1 text-dark"><?= e(__('citizen.cat_sanitation_title')) ?></h5>
                        <p class="text-muted small mb-0"><?= e(__('citizen.cat_sanitation_desc')) ?></p>
                    </div>
                </div>
            </div>

            <!-- 4. Drainage & Sewage -->
            <div class="col-sm-6 col-lg-4">
                <div class="p-3 p-md-4 rounded-4 border bg-light h-100 d-flex gap-3 align-items-start category-box">
                    <div class="bg-info text-dark p-3 rounded-4 shadow-sm flex-shrink-0">
                        <i class="bi bi-moisture fs-4"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1 text-dark"><?= e(__('citizen.cat_drainage_title')) ?></h5>
                        <p class="text-muted small mb-0"><?= e(__('citizen.cat_drainage_desc')) ?></p>
                    </div>
                </div>
            </div>

            <!-- 5. Roads & Concrete Potholes -->
            <div class="col-sm-6 col-lg-4">
                <div class="p-3 p-md-4 rounded-4 border bg-light h-100 d-flex gap-3 align-items-start category-box">
                    <div class="bg-secondary text-white p-3 rounded-4 shadow-sm flex-shrink-0">
                        <i class="bi bi-signpost-split-fill fs-4"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1 text-dark"><?= e(__('citizen.cat_roads_title')) ?></h5>
                        <p class="text-muted small mb-0"><?= e(__('citizen.cat_roads_desc')) ?></p>
                    </div>
                </div>
            </div>

            <!-- 6. Other Village Issues -->
            <div class="col-sm-6 col-lg-4">
                <div class="p-3 p-md-4 rounded-4 border bg-light h-100 d-flex gap-3 align-items-start category-box">
                    <div class="bg-dark text-white p-3 rounded-4 shadow-sm flex-shrink-0">
                        <i class="bi bi-building-check fs-4"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1 text-dark"><?= e(__('citizen.cat_other_title')) ?></h5>
                        <p class="text-muted small mb-0"><?= e(__('citizen.cat_other_desc')) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 4-Step Simple Rural Workflow -->
<section class="py-5">
    <div class="container text-center">
        <h3 class="fw-bold text-dark mb-2 font-telugu"><?= e(__('citizen.how_heading')) ?></h3>
        <p class="text-muted small mb-5 mx-auto" style="max-width: 600px;">
            <?= e(__('citizen.how_subheading')) ?>
        </p>

        <div class="row g-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                    <div class="display-6 fw-bold text-success mb-2">01</div>
                    <h5 class="fw-bold mb-2 font-telugu"><?= e(__('citizen.step_1_title')) ?></h5>
                    <p class="text-muted small mb-0"><?= e(__('citizen.step_1_desc')) ?></p>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                    <div class="display-6 fw-bold text-primary mb-2">02</div>
                    <h5 class="fw-bold mb-2 font-telugu"><?= e(__('citizen.step_2_title')) ?></h5>
                    <p class="text-muted small mb-0"><?= e(__('citizen.step_2_desc')) ?></p>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                    <div class="display-6 fw-bold text-warning mb-2">03</div>
                    <h5 class="fw-bold mb-2 font-telugu"><?= e(__('citizen.step_3_title')) ?></h5>
                    <p class="text-muted small mb-0"><?= e(__('citizen.step_3_desc')) ?></p>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                    <div class="display-6 fw-bold text-success mb-2">04</div>
                    <h5 class="fw-bold mb-2 font-telugu"><?= e(__('citizen.step_4_title')) ?></h5>
                    <p class="text-muted small mb-0"><?= e(__('citizen.step_4_desc')) ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php view('layouts/footer'); ?>
