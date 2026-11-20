<?php 
$activeLang = current_lang();
view('layouts/header', ['title' => __('citizen.form_heading') . ' — ' . __('common.app_name')]); 
?>

<div class="container py-4 py-md-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">

            <!-- Breadcrumb Navigation -->
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= url('') ?>" class="text-success text-decoration-none"><?= e(__('common.nav_home')) ?></a></li>
                    <li class="breadcrumb-item active"><?= e(__('citizen.action_submit_title')) ?></li>
                </ol>
            </nav>

            <!-- Reassuring "No Citizen Login" Reminder -->
            <div class="alert alert-light border border-success-subtle shadow-sm rounded-4 p-3 mb-4 d-flex align-items-center gap-3">
                <div class="bg-success text-white rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px;">
                    <i class="bi bi-shield-check fs-5"></i>
                </div>
                <div class="small">
                    <strong class="text-dark d-block"><?= e(__('citizen.no_login_title')) ?></strong>
                    <span class="text-secondary"><?= e(__('citizen.no_login_desc')) ?></span>
                </div>
            </div>

            <!-- Form Card -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-success text-white p-4 text-center border-0">
                    <div class="bg-white text-success rounded-circle d-inline-flex align-items-center justify-content-center p-3 mb-2 shadow-sm" style="width: 56px; height: 56px;">
                        <i class="bi bi-pencil-fill fs-4"></i>
                    </div>
                    <h3 class="fw-bold mb-1 font-telugu"><?= e(__('citizen.form_heading')) ?></h3>
                    <p class="text-white-50 small mb-0"><?= e(__('citizen.form_subheading')) ?></p>
                </div>

                <div class="card-body p-4 p-md-5 bg-white">
                    <form id="grievanceForm" method="POST" action="<?= url('submit') ?>" enctype="multipart/form-data">
                        <?= csrf_field() ?>

                        <!-- Row 1: Citizen Name & Mobile Number -->
                        <div class="row g-3 mb-3">
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

                        <!-- Progressive Geographic Hierarchy Selection Card -->
                        <div class="card border-0 bg-light p-3 p-md-4 rounded-4 mb-4 border shadow-sm">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                                    <i class="bi bi-geo-alt-fill text-success"></i>
                                    <span><?= $activeLang === 'te' ? 'సమస్య ఉన్న ప్రదేశం (భౌగోళిక వివరాలు)' : 'Grievance Location Hierarchy' ?></span>
                                </h6>
                                <span class="badge bg-success-subtle text-success border border-success-subtle small">
                                    <?= $activeLang === 'te' ? 'క్రమబద్ధ ఎంపిక' : 'Progressive Selection' ?>
                                </span>
                            </div>

                            <!-- Stepper Breadcrumb / Visual Chain -->
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

                            <!-- Hierarchy Row 1: State & District -->
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

                            <!-- Hierarchy Row 2: Mandal & Gram Panchayat -->
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

                            <!-- Hierarchy Row 3: Village & Ward -->
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

                        <!-- Row 3: Problem Category Select -->
                        <div class="mb-3">
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

                        <!-- Row 4: Problem Description Textarea -->
                        <div class="mb-3">
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

                        <!-- Row 5: Landmark & Location -->
                        <div class="mb-3">
                            <label for="landmark" class="form-label fw-semibold text-secondary small">
                                <i class="bi bi-pin-map me-1 text-success"></i> <?= e(__('citizen.field_landmark')) ?>
                            </label>
                            <input type="text" class="form-control rounded-3 py-2" id="landmark" name="landmark" 
                                   value="<?= e($old['landmark'] ?? '') ?>" 
                                   placeholder="<?= e(__('citizen.field_landmark_placeholder')) ?>">
                        </div>

                        <!-- Row 6: GPS Location Coordinates (Optional) -->
                        <div class="mb-3 p-3 rounded-3 bg-light border">
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

                        <!-- Row 7: Photo Attachment with Preview -->
                        <div class="mb-4">
                            <label for="photo" class="form-label fw-semibold text-secondary small">
                                <i class="bi bi-camera me-1 text-success"></i> <?= e(__('citizen.field_photo')) ?>
                            </label>
                            <input type="file" class="form-control rounded-3 py-2" id="photo" name="photo" accept="image/jpeg,image/png,image/webp">
                            <div class="form-text text-muted" style="font-size: 0.75rem;">
                                <?= e(__('citizen.field_photo_hint')) ?>
                            </div>

                            <!-- Live Image Preview Container -->
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
