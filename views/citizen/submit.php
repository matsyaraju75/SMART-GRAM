<?php 
$activeLang = current_lang();
view('layouts/header', ['title' => __('citizen.form_heading') . ' — ' . __('common.app_name')]); 
?>

<!-- ═══════════════════════════════════════════════════════════════════════════
     SECTION 1: Page Header with Step Icons — Submission Flow Indicator
     ═══════════════════════════════════════════════════════════════════════════ -->
<section class="py-4 py-md-5" style="background: linear-gradient(135deg, #f0fdf4 0%, #e8f5e9 100%);">
    <div class="container">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= url('') ?>" class="text-success text-decoration-none"><?= e(__('common.nav_home')) ?></a></li>
                <li class="breadcrumb-item active"><?= e(__('citizen.action_submit_title')) ?></li>
            </ol>
        </nav>

        <div class="text-center mb-4">
            <h2 class="fw-bold text-dark mb-2"><?= e(__('citizen.submit_page_title')) ?></h2>
            <p class="text-muted mb-0"><?= e(__('citizen.submit_page_subtitle')) ?></p>
        </div>

        <!-- Step Indicator -->
        <div class="d-flex justify-content-center align-items-center flex-wrap gap-0 px-2 px-md-5">
            <?php
            $steps = [
                ['icon' => 'bi-geo-alt-fill',    'label' => __('citizen.submit_step_location')],
                ['icon' => 'bi-tags-fill',        'label' => __('citizen.submit_step_category')],
                ['icon' => 'bi-pencil-fill',      'label' => __('citizen.submit_step_describe')],
                ['icon' => 'bi-camera-fill',      'label' => __('citizen.submit_step_photo')],
                ['icon' => 'bi-send-check-fill',  'label' => __('citizen.submit_step_submit')],
            ];
            foreach ($steps as $i => $step):
            ?>
                <div class="d-flex align-items-center">
                    <div class="text-center" style="min-width: 70px;">
                        <div class="rounded-circle bg-success text-white d-inline-flex align-items-center justify-content-center shadow-sm mb-1" style="width: 44px; height: 44px;">
                            <i class="bi <?= $step['icon'] ?> fs-5"></i>
                        </div>
                        <div class="small fw-semibold text-dark" style="font-size: 0.72rem;"><?= e($step['label']) ?></div>
                    </div>
                    <?php if ($i < count($steps) - 1): ?>
                        <div class="flex-shrink-0 d-none d-sm-block" style="width: 40px; height: 2px; background: linear-gradient(90deg, #198754, #a3cfbb); margin: 0 4px; margin-bottom: 20px;"></div>
                        <div class="flex-shrink-0 d-sm-none" style="width: 16px; height: 2px; background: #a3cfbb; margin: 0 2px; margin-bottom: 20px;"></div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════════════════════
     SECTION 2: Enhanced Grievance Submission Form
     ═══════════════════════════════════════════════════════════════════════════ -->
<div class="container py-4 py-md-5">
    <div class="row justify-content-center">
        <div class="col-lg-9 col-xl-8">

            <!-- No-Login Reminder -->
            <div class="alert alert-light border border-success-subtle shadow-sm rounded-4 p-3 mb-4 d-flex align-items-center gap-3">
                <div class="bg-success text-white rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px;">
                    <i class="bi bi-shield-check fs-5"></i>
                </div>
                <div class="small">
                    <strong class="text-dark d-block"><?= e(__('citizen.no_login_title')) ?></strong>
                    <span class="text-secondary"><?= e(__('citizen.no_login_desc')) ?></span>
                </div>
            </div>

            <!-- Main Form Card -->
            <div class="card border-0 shadow rounded-4 overflow-hidden">
                <!-- Green Header -->
                <div class="card-header border-0 p-4 text-center text-white" style="background: linear-gradient(135deg, #198754 0%, #0d6a3e 100%);">
                    <div class="bg-white text-success rounded-circle d-inline-flex align-items-center justify-content-center p-3 mb-2 shadow-sm" style="width: 56px; height: 56px;">
                        <i class="bi bi-pencil-fill fs-4"></i>
                    </div>
                    <h3 class="fw-bold mb-1 font-telugu"><?= e(__('citizen.form_heading')) ?></h3>
                    <p class="text-white-50 small mb-0"><?= e(__('citizen.form_subheading')) ?></p>
                </div>

                <div class="card-body p-4 p-md-5 bg-white">
                    <form id="grievanceForm" method="POST" action="<?= url('submit') ?>" enctype="multipart/form-data">
                        <?= csrf_field() ?>

                        <!-- ── Personal Information Card ── -->
                        <div class="card border rounded-4 bg-light mb-4 shadow-sm">
                            <div class="card-body p-3 p-md-4">
                                <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                                    <i class="bi bi-person-vcard-fill text-success"></i>
                                    <?= e(__('citizen.submit_personal_info')) ?>
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="citizen_name" class="form-label fw-semibold text-secondary small">
                                            <i class="bi bi-person me-1 text-success"></i> <?= e(__('citizen.field_name')) ?> <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control rounded-3 py-2" id="citizen_name" name="citizen_name" 
                                               value="<?= e($old['citizen_name'] ?? '') ?>" 
                                               placeholder="<?= e(__('citizen.field_name_placeholder')) ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="citizen_phone" class="form-label fw-semibold text-secondary small">
                                            <i class="bi bi-telephone me-1 text-success"></i> <?= e(__('citizen.field_phone')) ?> <span class="text-danger">*</span>
                                        </label>
                                        <input type="tel" class="form-control rounded-3 py-2" id="citizen_phone" name="citizen_phone" 
                                               value="<?= e($old['citizen_phone'] ?? '') ?>" 
                                               placeholder="<?= e(__('citizen.field_phone_placeholder')) ?>" 
                                               pattern="[6-9][0-9]{9}" maxlength="10" required>
                                        <div class="form-text text-muted" style="font-size: 0.75rem;">
                                            <?= e(__('citizen.field_phone_hint')) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ── Geographic Location Hierarchy Card ── -->
                        <div class="card border rounded-4 bg-light mb-4 shadow-sm">
                            <div class="card-body p-3 p-md-4">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                                        <i class="bi bi-geo-alt-fill text-success"></i>
                                        <span><?= e(__('citizen.submit_location_heading')) ?></span>
                                    </h6>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle small">
                                        <?= $activeLang === 'te' ? 'క్రమబద్ధ ఎంపిక' : 'Progressive Selection' ?>
                                    </span>
                                </div>

                                <!-- Stepper Breadcrumb -->
                                <div class="d-none d-md-flex align-items-center gap-1 text-muted small mb-3 pb-2 border-bottom flex-wrap">
                                    <span class="badge bg-white text-secondary border">1. <?= e(__('citizen.field_state')) ?></span>
                                    <i class="bi bi-chevron-right text-muted" style="font-size:0.75rem;"></i>
                                    <span class="badge bg-white text-secondary border">2. <?= e(__('citizen.field_district')) ?></span>
                                    <i class="bi bi-chevron-right text-muted" style="font-size:0.75rem;"></i>
                                    <span class="badge bg-white text-secondary border">3. <?= e(__('citizen.field_mandal')) ?></span>
                                    <i class="bi bi-chevron-right text-muted" style="font-size:0.75rem;"></i>
                                    <span class="badge bg-white text-secondary border">4. <?= e(__('citizen.field_panchayat')) ?></span>
                                    <i class="bi bi-chevron-right text-muted" style="font-size:0.75rem;"></i>
                                    <span class="badge bg-white text-secondary border">5. <?= e(__('citizen.field_village')) ?></span>
                                    <i class="bi bi-chevron-right text-muted" style="font-size:0.75rem;"></i>
                                    <span class="badge bg-white text-secondary border">6. <?= e(__('citizen.field_ward')) ?></span>
                                </div>

                                <!-- Row 1: State & District -->
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label for="state_id" class="form-label fw-semibold text-secondary small">
                                            <i class="bi bi-building me-1 text-success"></i> <?= e(__('citizen.field_state')) ?> <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select rounded-3 py-2 bg-white" id="state_id" name="state_id" required>
                                            <option value=""><?= e(__('citizen.field_state_placeholder')) ?></option>
                                            <?php foreach ($states as $st): ?>
                                                <option value="<?= $st['id'] ?>" <?= (($old['state_id'] ?? $defaultStateId) == $st['id']) ? 'selected' : '' ?>>
                                                    <?= e($activeLang === 'te' ? $st['name_te'] : $st['name_en']) ?> (<?= e($st['code']) ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="district_id" class="form-label fw-semibold text-secondary small">
                                            <i class="bi bi-map me-1 text-success"></i> <?= e(__('citizen.field_district')) ?> <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select rounded-3 py-2 bg-white" id="district_id" name="district_id" required>
                                            <option value=""><?= e(__('citizen.field_district_placeholder')) ?></option>
                                            <?php foreach ($districts as $d): ?>
                                                <option value="<?= $d['id'] ?>" <?= (($old['district_id'] ?? $defaultDistrictId) == $d['id']) ? 'selected' : '' ?>>
                                                    <?= e($activeLang === 'te' ? $d['name_te'] : $d['name_en']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <!-- Row 2: Mandal & Gram Panchayat -->
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label for="mandal_id" class="form-label fw-semibold text-secondary small">
                                            <i class="bi bi-diagram-3 me-1 text-success"></i> <?= e(__('citizen.field_mandal')) ?> <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select rounded-3 py-2 bg-white" id="mandal_id" name="mandal_id" required>
                                            <option value=""><?= e(__('citizen.field_mandal_placeholder')) ?></option>
                                            <?php foreach ($mandals as $m): ?>
                                                <option value="<?= $m['id'] ?>" <?= (($old['mandal_id'] ?? $defaultMandalId) == $m['id']) ? 'selected' : '' ?>>
                                                    <?= e($activeLang === 'te' ? $m['name_te'] : $m['name_en']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="gram_panchayat_id" class="form-label fw-semibold text-secondary small">
                                            <i class="bi bi-houses me-1 text-success"></i> <?= e(__('citizen.field_panchayat')) ?> <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select rounded-3 py-2 bg-white" id="gram_panchayat_id" name="gram_panchayat_id" required>
                                            <option value=""><?= e(__('citizen.field_panchayat_placeholder')) ?></option>
                                            <?php foreach ($panchayats as $gp): ?>
                                                <option value="<?= $gp['id'] ?>" <?= (($old['gram_panchayat_id'] ?? $defaultPanchayatId) == $gp['id']) ? 'selected' : '' ?>>
                                                    <?= e($activeLang === 'te' ? $gp['name_te'] : $gp['name_en']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <!-- Row 3: Village & Ward -->
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="village_id" class="form-label fw-semibold text-secondary small">
                                            <i class="bi bi-geo-alt me-1 text-success"></i> <?= e(__('citizen.field_village')) ?> <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select rounded-3 py-2 bg-white" id="village_id" name="village_id" required>
                                            <option value=""><?= e(__('citizen.field_village_placeholder')) ?></option>
                                            <?php foreach ($villages as $v): ?>
                                                <option value="<?= $v['id'] ?>" <?= (($old['village_id'] ?? $defaultVillageId) == $v['id']) ? 'selected' : '' ?>>
                                                    <?= e($activeLang === 'te' ? $v['name_te'] : $v['name_en']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="ward_id" class="form-label fw-semibold text-secondary small">
                                            <i class="bi bi-signpost me-1 text-success"></i> <?= e(__('citizen.field_ward')) ?>
                                        </label>
                                        <select class="form-select rounded-3 py-2 bg-white" id="ward_id" name="ward_id">
                                            <option value=""><?= e(__('citizen.field_ward_placeholder')) ?></option>
                                            <?php foreach ($wards as $w): ?>
                                                <option value="<?= $w['id'] ?>" <?= (($old['ward_id'] ?? '') == $w['id']) ? 'selected' : '' ?>>
                                                    <?= e($w['ward_number']) ?><?= !empty($w['ward_name']) ? ' - ' . e($w['ward_name']) : '' ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ── Problem Category ── -->
                        <div class="mb-4">
                            <label for="category_id" class="form-label fw-semibold text-secondary small">
                                <i class="bi bi-tags me-1 text-success"></i> <?= e(__('citizen.field_category')) ?> <span class="text-danger">*</span>
                            </label>
                            <select class="form-select rounded-3 py-2" id="category_id" name="category_id" required>
                                <option value=""><?= e(__('citizen.field_category_placeholder')) ?></option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= (($old['category_id'] ?? '') == $cat['id']) ? 'selected' : '' ?> data-sla="<?= $cat['sla_hours'] ?>">
                                        <?= e($activeLang === 'te' ? $cat['name_te'] : $cat['name_en']) ?> 
                                        (<?= e($activeLang === 'te' ? $cat['dept_name_te'] : $cat['dept_name_en']) ?> — SLA: <?= $cat['sla_hours'] ?>h)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- ── Problem Description ── -->
                        <div class="mb-4">
                            <label for="description" class="form-label fw-semibold text-secondary small">
                                <i class="bi bi-card-text me-1 text-success"></i> <?= e(__('citizen.field_description')) ?> <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control rounded-3" id="description" name="description" rows="4" 
                                      placeholder="<?= e(__('citizen.field_description_placeholder')) ?>" required minlength="10"><?= e($old['description'] ?? '') ?></textarea>
                            <div class="d-flex justify-content-between text-muted mt-1" style="font-size: 0.75rem;">
                                <span>కనీసం 10 అక్షరాలు ఉండాలి (Min. 10 characters)</span>
                                <span id="charCount">0 characters</span>
                            </div>
                        </div>

                        <!-- ── Landmark ── -->
                        <div class="mb-4">
                            <label for="landmark" class="form-label fw-semibold text-secondary small">
                                <i class="bi bi-pin-map me-1 text-success"></i> <?= e(__('citizen.field_landmark')) ?>
                            </label>
                            <input type="text" class="form-control rounded-3 py-2" id="landmark" name="landmark" 
                                   value="<?= e($old['landmark'] ?? '') ?>" 
                                   placeholder="<?= e(__('citizen.field_landmark_placeholder')) ?>">
                        </div>

                        <!-- ── GPS Location ── -->
                        <div class="mb-4 p-3 rounded-4 bg-light border shadow-sm">
                            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                                <div>
                                    <span class="fw-semibold small text-dark d-block">
                                        <i class="bi bi-crosshair me-1 text-success"></i> GPS ప్రదేశం (Optional GPS Location)
                                    </span>
                                    <span class="text-muted" style="font-size: 0.78rem;">
                                        మీ మొబైల్ లేదా కంప్యూటర్ నుండి ప్రస్తుత కోఆర్డినేట్లను ఆటోమేటిక్‌గా గుర్తించండి
                                    </span>
                                </div>
                                <button type="button" id="detectGpsBtn" class="btn btn-outline-success btn-sm rounded-pill px-3 text-nowrap">
                                    <i class="bi bi-geo-alt"></i> <?= e(__('citizen.field_gps_btn')) ?>
                                </button>
                            </div>
                            <div id="gpsStatus" class="mt-2 text-success small fw-medium" style="display:none;"></div>
                            <input type="hidden" id="latitude" name="latitude" value="<?= e($old['latitude'] ?? '') ?>">
                            <input type="hidden" id="longitude" name="longitude" value="<?= e($old['longitude'] ?? '') ?>">
                        </div>

                        <!-- ── Photo Upload with Drag-Drop Styling ── -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-secondary small">
                                <i class="bi bi-camera me-1 text-success"></i> <?= e(__('citizen.field_photo')) ?>
                            </label>
                            <div id="photoDropZone" class="border border-2 border-dashed rounded-4 text-center p-4 position-relative" 
                                 style="border-color: #a3cfbb !important; background: #f9fefb; cursor: pointer; transition: all 0.2s ease;">
                                <div id="photoDropPlaceholder">
                                    <i class="bi bi-cloud-arrow-up-fill text-success fs-1 d-block mb-2"></i>
                                    <p class="fw-semibold text-dark mb-1"><?= e(__('citizen.submit_photo_drag_title')) ?></p>
                                    <p class="text-muted small mb-2"><?= e(__('citizen.submit_photo_drag_subtitle')) ?></p>
                                    <span class="btn btn-sm btn-outline-success rounded-pill px-3">
                                        <i class="bi bi-folder2-open me-1"></i> <?= e(__('citizen.submit_photo_browse')) ?>
                                    </span>
                                </div>
                                <input type="file" class="position-absolute top-0 start-0 w-100 h-100 opacity-0" id="photo" name="photo" accept="image/jpeg,image/png,image/webp" style="cursor: pointer;">
                            </div>
                            <div class="form-text text-muted" style="font-size: 0.75rem;">
                                <?= e(__('citizen.field_photo_hint')) ?>
                            </div>

                            <!-- Live Image Preview -->
                            <div id="photoPreviewContainer" class="mt-3 text-center" style="display: none;">
                                <div class="position-relative d-inline-block border rounded-3 p-1 shadow-sm bg-white">
                                    <img id="photoPreview" src="" alt="Photo Preview" class="img-fluid rounded-2" style="max-height: 220px; object-fit: contain;">
                                    <button type="button" id="removePhotoBtn" class="btn btn-danger btn-sm rounded-circle position-absolute top-0 end-0 translate-middle shadow-sm p-1" style="width: 28px; height: 28px;">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-4">
                            <button type="submit" id="submitBtn" class="btn btn-success btn-lg w-100 py-3 rounded-pill fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2">
                                <i class="bi bi-send-fill"></i>
                                <span><?= e(__('citizen.btn_submit')) ?></span>
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════════════════════
     SECTION 3: Grievance Stats / Summary Panel
     ═══════════════════════════════════════════════════════════════════════════ -->
<section class="py-5" style="background: #f7f9fb;">
    <div class="container">
        <div class="text-center mb-4">
            <h4 class="fw-bold text-dark"><i class="bi bi-bar-chart-line-fill text-success me-2"></i><?= e(__('citizen.submit_stats_heading')) ?></h4>
            <p class="text-muted small"><?= e(__('citizen.submit_stats_subheading')) ?></p>
        </div>
        <div class="row g-3 g-md-4 justify-content-center">
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm rounded-4 text-center p-3 h-100">
                    <div class="bg-success-subtle text-success rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-2" style="width: 48px; height: 48px;">
                        <i class="bi bi-clipboard-data-fill fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-dark mb-0">1,248</h3>
                    <p class="text-muted small mb-0"><?= e(__('citizen.submit_stat_total')) ?></p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm rounded-4 text-center p-3 h-100">
                    <div class="bg-primary-subtle text-primary rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-2" style="width: 48px; height: 48px;">
                        <i class="bi bi-check-circle-fill fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-dark mb-0">94.2%</h3>
                    <p class="text-muted small mb-0"><?= e(__('citizen.submit_stat_resolution')) ?></p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm rounded-4 text-center p-3 h-100">
                    <div class="bg-warning-subtle text-warning rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-2" style="width: 48px; height: 48px;">
                        <i class="bi bi-clock-history fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-dark mb-0">2.4</h3>
                    <p class="text-muted small mb-0"><?= e(__('citizen.submit_stat_avg_days')) ?></p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm rounded-4 text-center p-3 h-100">
                    <div class="bg-danger-subtle text-danger rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-2" style="width: 48px; height: 48px;">
                        <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-dark mb-0">18</h3>
                    <p class="text-muted small mb-0"><?= e(__('citizen.submit_stat_pending')) ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════════════════════
     SECTION 4: Recently Resolved Village Redressals — Gallery
     ═══════════════════════════════════════════════════════════════════════════ -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-4">
            <h4 class="fw-bold text-dark"><i class="bi bi-patch-check-fill text-success me-2"></i><?= e(__('citizen.submit_resolved_heading')) ?></h4>
            <p class="text-muted small"><?= e(__('citizen.submit_resolved_subheading')) ?></p>
        </div>

        <div class="row g-4">
            <?php
            $resolvedItems = [
                [
                    'category'  => 'Drinking Water',
                    'location'  => 'Reddypalem, Ward 3',
                    'date'      => '14 Nov 2026',
                    'officer'   => 'Sri K. Venkatesh',
                    'status'    => 'Resolved',
                    'before'    => 'https://placehold.co/400x240/dc3545/ffffff?text=Before:+Broken+Pipeline',
                    'after'     => 'https://placehold.co/400x240/198754/ffffff?text=After:+Pipeline+Fixed',
                ],
                [
                    'category'  => 'Street Lights',
                    'location'  => 'Muthukur, Ward 1',
                    'date'      => '10 Nov 2026',
                    'officer'   => 'Sri P. Ramesh',
                    'status'    => 'Closed',
                    'before'    => 'https://placehold.co/400x240/dc3545/ffffff?text=Before:+Dark+Street',
                    'after'     => 'https://placehold.co/400x240/198754/ffffff?text=After:+Lights+Installed',
                ],
                [
                    'category'  => 'Road Repair',
                    'location'  => 'Naidupeta, Ward 5',
                    'date'      => '8 Nov 2026',
                    'officer'   => 'Smt. L. Padma',
                    'status'    => 'Resolved',
                    'before'    => 'https://placehold.co/400x240/dc3545/ffffff?text=Before:+Pothole+Road',
                    'after'     => 'https://placehold.co/400x240/198754/ffffff?text=After:+Road+Repaired',
                ],
                [
                    'category'  => 'Drainage & Sewage',
                    'location'  => 'Sullurupeta, Ward 2',
                    'date'      => '5 Nov 2026',
                    'officer'   => 'Sri T. Suresh',
                    'status'    => 'Resolved',
                    'before'    => 'https://placehold.co/400x240/dc3545/ffffff?text=Before:+Blocked+Drain',
                    'after'     => 'https://placehold.co/400x240/198754/ffffff?text=After:+Drain+Cleared',
                ],
                [
                    'category'  => 'Sanitation',
                    'location'  => 'Gudur, Ward 4',
                    'date'      => '2 Nov 2026',
                    'officer'   => 'Sri M. Ravi Kumar',
                    'status'    => 'Closed',
                    'before'    => 'https://placehold.co/400x240/dc3545/ffffff?text=Before:+Garbage+Dump',
                    'after'     => 'https://placehold.co/400x240/198754/ffffff?text=After:+Area+Cleaned',
                ],
                [
                    'category'  => 'Public Facility',
                    'location'  => 'Kaluvoya, Ward 6',
                    'date'      => '30 Oct 2026',
                    'officer'   => 'Smt. B. Lakshmi',
                    'status'    => 'Resolved',
                    'before'    => 'https://placehold.co/400x240/dc3545/ffffff?text=Before:+Damaged+Hall',
                    'after'     => 'https://placehold.co/400x240/198754/ffffff?text=After:+Hall+Repaired',
                ],
            ];
            foreach ($resolvedItems as $item):
            ?>
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 resolved-card">
                    <!-- Before/After Photos -->
                    <div class="row g-0">
                        <div class="col-6">
                            <div class="position-relative">
                                <img src="<?= $item['before'] ?>" class="w-100" alt="Before" style="height: 120px; object-fit: cover;">
                                <span class="badge bg-danger position-absolute top-0 start-0 m-1 rounded-pill" style="font-size: 0.65rem;">
                                    <i class="bi bi-x-circle me-1"></i><?= e(__('citizen.submit_resolved_before')) ?>
                                </span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="position-relative">
                                <img src="<?= $item['after'] ?>" class="w-100" alt="After" style="height: 120px; object-fit: cover;">
                                <span class="badge bg-success position-absolute top-0 start-0 m-1 rounded-pill" style="font-size: 0.65rem;">
                                    <i class="bi bi-check-circle me-1"></i><?= e(__('citizen.submit_resolved_after')) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="fw-bold text-dark mb-0 small"><?= e($item['category']) ?></h6>
                            <span class="badge <?= $item['status'] === 'Resolved' ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' ?> border rounded-pill" style="font-size: 0.7rem;">
                                <?= e($item['status']) ?>
                            </span>
                        </div>
                        <p class="text-muted mb-1 small"><i class="bi bi-geo-alt me-1"></i><?= e($item['location']) ?></p>
                        <div class="d-flex justify-content-between text-muted small">
                            <span><i class="bi bi-calendar3 me-1"></i><?= e($item['date']) ?></span>
                            <span><i class="bi bi-person-check me-1"></i><?= e($item['officer']) ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════════════════════
     ALL EXISTING JavaScript — Preserved Exactly
     ═══════════════════════════════════════════════════════════════════════════ -->
<script>
// Character Counter for description
const descInput = document.getElementById('description');
const charCount = document.getElementById('charCount');
if (descInput && charCount) {
    descInput.addEventListener('input', () => {
        charCount.textContent = descInput.value.length + ' characters';
    });
}

// Progressive Geographic Hierarchy Dropdown Chain
const currentLang = '<?= $activeLang ?>';
const stateSelect = document.getElementById('state_id');
const districtSelect = document.getElementById('district_id');
const mandalSelect = document.getElementById('mandal_id');
const gpSelect = document.getElementById('gram_panchayat_id');
const villageSelect = document.getElementById('village_id');
const wardSelect = document.getElementById('ward_id');

function getEntityName(item) {
    if (currentLang === 'te' && item.name_te) {
        return item.name_te + (item.name_en ? ' (' + item.name_en + ')' : '');
    }
    return item.name_en || item.name_te || '';
}

// 1. State change -> Load Districts
if (stateSelect && districtSelect) {
    stateSelect.addEventListener('change', async function() {
        const stateId = this.value;
        districtSelect.innerHTML = '<option value="">-- ' + (currentLang === 'te' ? 'లోడ్ అవుతోంది...' : 'Loading districts...') + ' --</option>';
        if (mandalSelect) mandalSelect.innerHTML = '<option value=""><?= e(__('citizen.field_mandal_placeholder')) ?></option>';
        if (gpSelect) gpSelect.innerHTML = '<option value=""><?= e(__('citizen.field_panchayat_placeholder')) ?></option>';
        if (villageSelect) villageSelect.innerHTML = '<option value=""><?= e(__('citizen.field_village_placeholder')) ?></option>';
        if (wardSelect) wardSelect.innerHTML = '<option value=""><?= e(__('citizen.field_ward_placeholder')) ?></option>';

        if (!stateId) {
            districtSelect.innerHTML = '<option value=""><?= e(__('citizen.field_district_placeholder')) ?></option>';
            return;
        }

        try {
            const resp = await fetch('<?= url("api/districts") ?>?state_id=' + stateId);
            const data = await resp.json();
            districtSelect.innerHTML = '<option value=""><?= e(__('citizen.field_district_placeholder')) ?></option>';
            if (data.districts && data.districts.length > 0) {
                data.districts.forEach(d => {
                    const opt = document.createElement('option');
                    opt.value = d.id;
                    opt.textContent = getEntityName(d);
                    districtSelect.appendChild(opt);
                });
                if (data.districts.length === 1) {
                    districtSelect.value = data.districts[0].id;
                    districtSelect.dispatchEvent(new Event('change'));
                }
            }
        } catch (err) {
            console.error('District loading failed', err);
            districtSelect.innerHTML = '<option value=""><?= e(__('citizen.field_district_placeholder')) ?></option>';
        }
    });
}

// 2. District change -> Load Mandals
if (districtSelect && mandalSelect) {
    districtSelect.addEventListener('change', async function() {
        const districtId = this.value;
        mandalSelect.innerHTML = '<option value="">-- ' + (currentLang === 'te' ? 'లోడ్ అవుతోంది...' : 'Loading mandals...') + ' --</option>';
        if (gpSelect) gpSelect.innerHTML = '<option value=""><?= e(__('citizen.field_panchayat_placeholder')) ?></option>';
        if (villageSelect) villageSelect.innerHTML = '<option value=""><?= e(__('citizen.field_village_placeholder')) ?></option>';
        if (wardSelect) wardSelect.innerHTML = '<option value=""><?= e(__('citizen.field_ward_placeholder')) ?></option>';

        if (!districtId) {
            mandalSelect.innerHTML = '<option value=""><?= e(__('citizen.field_mandal_placeholder')) ?></option>';
            return;
        }

        try {
            const resp = await fetch('<?= url("api/mandals") ?>?district_id=' + districtId);
            const data = await resp.json();
            mandalSelect.innerHTML = '<option value=""><?= e(__('citizen.field_mandal_placeholder')) ?></option>';
            if (data.mandals && data.mandals.length > 0) {
                data.mandals.forEach(m => {
                    const opt = document.createElement('option');
                    opt.value = m.id;
                    opt.textContent = getEntityName(m);
                    mandalSelect.appendChild(opt);
                });
                if (data.mandals.length === 1) {
                    mandalSelect.value = data.mandals[0].id;
                    mandalSelect.dispatchEvent(new Event('change'));
                }
            }
        } catch (err) {
            console.error('Mandal loading failed', err);
            mandalSelect.innerHTML = '<option value=""><?= e(__('citizen.field_mandal_placeholder')) ?></option>';
        }
    });
}

// 3. Mandal change -> Load Gram Panchayats
if (mandalSelect && gpSelect) {
    mandalSelect.addEventListener('change', async function() {
        const mandalId = this.value;
        gpSelect.innerHTML = '<option value="">-- ' + (currentLang === 'te' ? 'లోడ్ అవుతోంది...' : 'Loading Panchayats...') + ' --</option>';
        if (villageSelect) villageSelect.innerHTML = '<option value=""><?= e(__('citizen.field_village_placeholder')) ?></option>';
        if (wardSelect) wardSelect.innerHTML = '<option value=""><?= e(__('citizen.field_ward_placeholder')) ?></option>';

        if (!mandalId) {
            gpSelect.innerHTML = '<option value=""><?= e(__('citizen.field_panchayat_placeholder')) ?></option>';
            return;
        }

        try {
            const resp = await fetch('<?= url("api/panchayats") ?>?mandal_id=' + mandalId);
            const data = await resp.json();
            gpSelect.innerHTML = '<option value=""><?= e(__('citizen.field_panchayat_placeholder')) ?></option>';
            if (data.panchayats && data.panchayats.length > 0) {
                data.panchayats.forEach(gp => {
                    const opt = document.createElement('option');
                    opt.value = gp.id;
                    opt.textContent = getEntityName(gp);
                    gpSelect.appendChild(opt);
                });
                if (data.panchayats.length === 1) {
                    gpSelect.value = data.panchayats[0].id;
                    gpSelect.dispatchEvent(new Event('change'));
                }
            }
        } catch (err) {
            console.error('GP loading failed', err);
            gpSelect.innerHTML = '<option value=""><?= e(__('citizen.field_panchayat_placeholder')) ?></option>';
        }
    });
}

// 4. Gram Panchayat change -> Load Villages
if (gpSelect && villageSelect) {
    gpSelect.addEventListener('change', async function() {
        const gpId = this.value;
        villageSelect.innerHTML = '<option value="">-- ' + (currentLang === 'te' ? 'లోడ్ అవుతోంది...' : 'Loading villages...') + ' --</option>';
        if (wardSelect) wardSelect.innerHTML = '<option value=""><?= e(__('citizen.field_ward_placeholder')) ?></option>';

        if (!gpId) {
            villageSelect.innerHTML = '<option value=""><?= e(__('citizen.field_village_placeholder')) ?></option>';
            return;
        }

        try {
            const resp = await fetch('<?= url("api/villages") ?>?panchayat_id=' + gpId);
            const data = await resp.json();
            villageSelect.innerHTML = '<option value=""><?= e(__('citizen.field_village_placeholder')) ?></option>';
            if (data.villages && data.villages.length > 0) {
                data.villages.forEach(v => {
                    const opt = document.createElement('option');
                    opt.value = v.id;
                    opt.textContent = getEntityName(v);
                    villageSelect.appendChild(opt);
                });
                if (data.villages.length === 1) {
                    villageSelect.value = data.villages[0].id;
                    villageSelect.dispatchEvent(new Event('change'));
                }
            }
        } catch (err) {
            console.error('Village loading failed', err);
            villageSelect.innerHTML = '<option value=""><?= e(__('citizen.field_village_placeholder')) ?></option>';
        }
    });
}

// 5. Village change -> Load Wards
if (villageSelect && wardSelect) {
    villageSelect.addEventListener('change', async function() {
        const villageId = this.value;
        wardSelect.innerHTML = '<option value="">-- ' + (currentLang === 'te' ? 'లోడ్ అవుతోంది...' : 'Loading wards...') + ' --</option>';

        if (!villageId) {
            wardSelect.innerHTML = '<option value=""><?= e(__('citizen.field_ward_placeholder')) ?></option>';
            return;
        }

        try {
            const resp = await fetch('<?= url("api/wards") ?>?village_id=' + villageId);
            const data = await resp.json();

            wardSelect.innerHTML = '<option value=""><?= e(__('citizen.field_ward_placeholder')) ?></option>';
            if (data.wards && data.wards.length > 0) {
                data.wards.forEach(w => {
                    const opt = document.createElement('option');
                    opt.value = w.id;
                    opt.textContent = w.ward_number + (w.ward_name ? ' - ' + w.ward_name : '');
                    wardSelect.appendChild(opt);
                });
            }
        } catch (err) {
            console.error('Ward loading failed', err);
            wardSelect.innerHTML = '<option value=""><?= e(__('citizen.field_ward_placeholder')) ?></option>';
        }
    });
}

// Photo Preview and Removal
const photoInput = document.getElementById('photo');
const previewContainer = document.getElementById('photoPreviewContainer');
const previewImg = document.getElementById('photoPreview');
const removePhotoBtn = document.getElementById('removePhotoBtn');

if (photoInput) {
    photoInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            if (file.size > 5 * 1024 * 1024) {
                alert('ఫోటో పరిమాణం 5MB కంటే తక్కువగా ఉండాలి (File must be under 5MB).');
                this.value = '';
                previewContainer.style.display = 'none';
                return;
            }
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewContainer.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            previewContainer.style.display = 'none';
        }
    });
}

if (removePhotoBtn) {
    removePhotoBtn.addEventListener('click', function() {
        photoInput.value = '';
        previewContainer.style.display = 'none';
        previewImg.src = '';
    });
}

// Drag-and-drop styling for photo zone
const dropZone = document.getElementById('photoDropZone');
if (dropZone) {
    ['dragenter', 'dragover'].forEach(evt => {
        dropZone.addEventListener(evt, function(e) {
            e.preventDefault();
            dropZone.style.borderColor = '#198754';
            dropZone.style.background = '#e8f5e9';
        });
    });
    ['dragleave', 'drop'].forEach(evt => {
        dropZone.addEventListener(evt, function(e) {
            e.preventDefault();
            dropZone.style.borderColor = '#a3cfbb';
            dropZone.style.background = '#f9fefb';
        });
    });
}

// GPS Detection
const gpsBtn = document.getElementById('detectGpsBtn');
const gpsStatus = document.getElementById('gpsStatus');
const latInput = document.getElementById('latitude');
const lngInput = document.getElementById('longitude');

if (gpsBtn) {
    gpsBtn.addEventListener('click', function() {
        if (!navigator.geolocation) {
            alert('మీ బ్రౌజర్ GPS ని సపోర్ట్ చేయదు (Geolocation not supported).');
            return;
        }
        gpsBtn.disabled = true;
        gpsBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> గుర్తిస్తోంది...';

        navigator.geolocation.getCurrentPosition(
            function(pos) {
                latInput.value = pos.coords.latitude.toFixed(6);
                lngInput.value = pos.coords.longitude.toFixed(6);
                gpsStatus.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> <?= e(__('citizen.field_gps_success')) ?> (' + latInput.value + ', ' + lngInput.value + ')';
                gpsStatus.style.display = 'block';
                gpsBtn.disabled = false;
                gpsBtn.innerHTML = '<i class="bi bi-check2"></i> గుర్తించబడింది';
            },
            function(err) {
                gpsBtn.disabled = false;
                gpsBtn.innerHTML = '<i class="bi bi-geo-alt"></i> <?= e(__('citizen.field_gps_btn')) ?>';
                alert('GPS స్థానం పొందడం విఫలమైంది: ' + err.message);
            },
            { timeout: 10000, enableHighAccuracy: true }
        );
    });
}

// Form Submission spinner
const form = document.getElementById('grievanceForm');
const submitBtn = document.getElementById('submitBtn');
if (form && submitBtn) {
    form.addEventListener('submit', function() {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> <?= e(__('citizen.btn_submitting')) ?>';
    });
}
</script>

<?php view('layouts/footer'); ?>
