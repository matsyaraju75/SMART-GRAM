<?php view('layouts/header', ['title' => $title ?? 'Welfare Schemes — Smart Gram']); ?>
<?php $activeLang = current_lang(); ?>

<!-- ══════════════════════════════════════════════════════════════════
     SECTION A — HERO BANNER
     ══════════════════════════════════════════════════════════════════ -->
<section class="welfare-hero position-relative overflow-hidden text-white py-5"
         style="background: linear-gradient(135deg, #0d6e3e 0%, #198754 40%, #20c997 100%);">
    <!-- Decorative SVG Circles -->
    <div class="position-absolute top-0 start-0 w-100 h-100 overflow-hidden" style="pointer-events:none;opacity:.08;">
        <svg width="600" height="600" class="position-absolute" style="top:-150px;right:-100px;">
            <circle cx="300" cy="300" r="300" fill="white"/>
        </svg>
        <svg width="300" height="300" class="position-absolute" style="bottom:-80px;left:-60px;">
            <circle cx="150" cy="150" r="150" fill="white"/>
        </svg>
    </div>

    <div class="container position-relative py-4">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <span class="badge bg-white bg-opacity-25 text-white rounded-pill px-3 py-2 mb-3 d-inline-flex align-items-center gap-2">
                    <i class="bi bi-shield-check"></i>
                    <?= e(__('citizen.welfare_hero_badge')) ?>
                </span>
                <h1 class="display-5 fw-bold mb-3 lh-sm">
                    <?= e(__('citizen.welfare_hero_title')) ?>
                </h1>
                <p class="lead mb-4 text-white-50 fs-6">
                    <?= e(__('citizen.welfare_hero_subtitle')) ?>
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="#eligibility-checker" class="btn btn-warning rounded-pill px-4 py-2 fw-bold shadow d-flex align-items-center gap-2">
                        <i class="bi bi-check2-circle"></i> <?= e(__('citizen.welfare_check_eligibility_btn')) ?>
                    </a>
                    <a href="#scheme-cards" class="btn btn-outline-light rounded-pill px-4 py-2 fw-semibold d-flex align-items-center gap-2">
                        <i class="bi bi-grid-3x3-gap"></i> <?= e(__('citizen.welfare_browse_schemes_btn')) ?>
                    </a>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="col-lg-5">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="card border-0 bg-white bg-opacity-15 text-white rounded-4 shadow-sm h-100">
                            <div class="card-body text-center p-3">
                                <i class="bi bi-journal-bookmark-fill fs-2 mb-2 d-block text-warning"></i>
                                <h3 class="fw-bold mb-0">1,47</h3>
                                <small class="text-white-50"><?= e(__('citizen.welfare_stat_total_schemes')) ?></small>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="card border-0 bg-white bg-opacity-15 text-white rounded-4 shadow-sm h-100">
                            <div class="card-body text-center p-3">
                                <i class="bi bi-people-fill fs-2 mb-2 d-block text-info"></i>
                                <h3 class="fw-bold mb-0">19,870+</h3>
                                <small class="text-white-50"><?= e(__('citizen.welfare_stat_beneficiaries')) ?></small>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="card border-0 bg-white bg-opacity-15 text-white rounded-4 shadow-sm">
                            <div class="card-body d-flex align-items-center justify-content-center gap-3 p-3">
                                <i class="bi bi-clock-history fs-2 text-success"></i>
                                <div>
                                    <h4 class="fw-bold mb-0">7 <?= e(__('citizen.welfare_stat_days')) ?></h4>
                                    <small class="text-white-50"><?= e(__('citizen.welfare_stat_avg_processing')) ?></small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ══════════════════════════════════════════════════════════════════
     SECTION B — INSTANT FAMILY ELIGIBILITY CHECKER
     ══════════════════════════════════════════════════════════════════ -->
<section class="py-5 bg-light" id="eligibility-checker">
    <div class="container">
        <div class="text-center mb-4">
            <span class="badge bg-success-subtle text-success rounded-pill px-3 py-2 mb-2">
                <i class="bi bi-lightning-charge-fill me-1"></i> <?= e(__('citizen.welfare_eligibility_badge')) ?>
            </span>
            <h2 class="fw-bold"><?= e(__('citizen.welfare_eligibility_heading')) ?></h2>
            <p class="text-muted"><?= e(__('citizen.welfare_eligibility_subheading')) ?></p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden eligibility-form">
                    <div class="card-header bg-success text-white py-3 px-4 d-flex align-items-center gap-2">
                        <i class="bi bi-clipboard2-check fs-5"></i>
                        <span class="fw-semibold"><?= e(__('citizen.welfare_eligibility_form_title')) ?></span>
                    </div>
                    <div class="card-body p-4">
                        <form id="eligibilityForm" novalidate>
                            <?= csrf_field() ?>
                            <div class="row g-3">
                                <!-- Ration Card Type -->
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">
                                        <i class="bi bi-credit-card-2-front me-1 text-success"></i>
                                        <?= e(__('citizen.welfare_field_ration_card')) ?>
                                    </label>
                                    <select class="form-select rounded-3" name="ration_card" id="rationCard" required>
                                        <option value="">-- <?= e(__('citizen.welfare_field_select')) ?> --</option>
                                        <option value="AAY">AAY (Antyodaya Anna Yojana)</option>
                                        <option value="BPL">BPL (Below Poverty Line)</option>
                                        <option value="PHH">PHH (Priority Household)</option>
                                        <option value="APL">APL (Above Poverty Line)</option>
                                        <option value="NONE">No Ration Card</option>
                                    </select>
                                </div>

                                <!-- Annual Income -->
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">
                                        <i class="bi bi-currency-rupee me-1 text-success"></i>
                                        <?= e(__('citizen.welfare_field_annual_income')) ?>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">₹</span>
                                        <input type="number" class="form-control rounded-end-3" name="annual_income"
                                               id="annualIncome" placeholder="e.g. 120000" min="0" max="10000000" required>
                                    </div>
                                </div>

                                <!-- Caste Category -->
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">
                                        <i class="bi bi-people me-1 text-success"></i>
                                        <?= e(__('citizen.welfare_field_caste_category')) ?>
                                    </label>
                                    <select class="form-select rounded-3" name="caste_category" id="casteCategory" required>
                                        <option value="">-- <?= e(__('citizen.welfare_field_select')) ?> --</option>
                                        <option value="SC">SC (Scheduled Caste)</option>
                                        <option value="ST">ST (Scheduled Tribe)</option>
                                        <option value="BC-A">BC-A</option>
                                        <option value="BC-B">BC-B</option>
                                        <option value="BC-C">BC-C</option>
                                        <option value="BC-D">BC-D</option>
                                        <option value="BC-E">BC-E</option>
                                        <option value="OC">OC (Open Category)</option>
                                        <option value="EWS">EWS (Economically Weaker)</option>
                                    </select>
                                </div>

                                <!-- Family Members -->
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">
                                        <i class="bi bi-person-lines-fill me-1 text-success"></i>
                                        <?= e(__('citizen.welfare_field_family_members')) ?>
                                    </label>
                                    <input type="number" class="form-control rounded-3" name="family_members"
                                           id="familyMembers" placeholder="e.g. 4" min="1" max="20" required>
                                </div>
                            </div>

                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-success btn-lg rounded-pill px-5 py-2 fw-bold shadow d-inline-flex align-items-center gap-2" id="checkEligibilityBtn">
                                    <i class="bi bi-search"></i>
                                    <?= e(__('citizen.welfare_check_now_btn')) ?>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Eligibility Results Container -->
                <div id="eligibilityResults" class="mt-4" style="display:none;">
                    <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                        <i class="bi bi-card-checklist text-success"></i>
                        <?= e(__('citizen.welfare_results_heading')) ?>
                    </h5>
                    <div class="row g-3" id="resultsGrid"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ══════════════════════════════════════════════════════════════════
     SECTION C — CASTE / REGISTRY SCHEMES GRID
     ══════════════════════════════════════════════════════════════════ -->
<section class="py-5" id="scheme-cards">
    <div class="container">
        <div class="text-center mb-5">
            <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2 mb-2">
                <i class="bi bi-grid-3x3-gap-fill me-1"></i> <?= e(__('citizen.welfare_schemes_badge')) ?>
            </span>
            <h2 class="fw-bold"><?= e(__('citizen.welfare_schemes_heading')) ?></h2>
            <p class="text-muted col-lg-8 mx-auto"><?= e(__('citizen.welfare_schemes_subheading')) ?></p>
        </div>

        <div class="row g-4">
            <?php foreach ($schemes as $scheme): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 scheme-card position-relative overflow-hidden">
                    <!-- Top Color Strip -->
                    <div class="position-absolute top-0 start-0 w-100" style="height:4px;background:var(--bs-<?= e($scheme['color']) ?>);"></div>
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                                 style="width:50px;height:50px;background:var(--bs-<?= e($scheme['color']) ?>);color:#fff;font-size:1.3rem;">
                                <i class="bi <?= e($scheme['icon']) ?>"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0"><?= e(__('citizen.' . $scheme['title_key'])) ?></h5>
                                <small class="text-muted"><?= e(__('citizen.' . $scheme['desc_key'])) ?></small>
                            </div>
                        </div>

                        <!-- Eligibility Criteria -->
                        <h6 class="fw-semibold text-dark small text-uppercase mb-2">
                            <i class="bi bi-check-circle me-1 text-success"></i> <?= e(__('citizen.welfare_eligibility_label')) ?>
                        </h6>
                        <ul class="list-unstyled small mb-3">
                            <?php foreach ($scheme['eligibility'] as $criterion): ?>
                            <li class="mb-1 d-flex align-items-start gap-2">
                                <i class="bi bi-dot text-success fs-5 lh-1"></i>
                                <span><?= e($criterion) ?></span>
                            </li>
                            <?php endforeach; ?>
                        </ul>

                        <!-- Required Documents -->
                        <h6 class="fw-semibold text-dark small text-uppercase mb-2">
                            <i class="bi bi-folder2-open me-1 text-primary"></i> <?= e(__('citizen.welfare_documents_label')) ?>
                        </h6>
                        <div class="d-flex flex-wrap gap-1 mb-3">
                            <?php foreach ($scheme['documents'] as $doc): ?>
                            <span class="badge bg-light text-dark border rounded-pill px-2 py-1 small"><?= e($doc) ?></span>
                            <?php endforeach; ?>
                        </div>

                        <!-- Processing Time -->
                        <div class="d-flex align-items-center gap-2 text-muted small mb-3">
                            <i class="bi bi-clock text-warning"></i>
                            <span><?= e(__('citizen.welfare_processing_time')) ?>: <strong><?= e($scheme['processing_days']) ?> <?= e(__('citizen.welfare_days')) ?></strong></span>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0 p-4 pt-0">
                        <a href="#" class="btn btn-<?= e($scheme['color']) ?> rounded-pill w-100 fw-semibold py-2 d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-pencil-square"></i>
                            <?= e(__('citizen.welfare_apply_now_btn')) ?>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ══════════════════════════════════════════════════════════════════
     SECTION D — VILLAGE SCHEME ASSISTANCE & VERIFICATION DESK
     ══════════════════════════════════════════════════════════════════ -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <span class="badge bg-info-subtle text-info rounded-pill px-3 py-2 mb-2">
                <i class="bi bi-building me-1"></i> <?= e(__('citizen.welfare_desk_badge')) ?>
            </span>
            <h2 class="fw-bold"><?= e(__('citizen.welfare_desk_heading')) ?></h2>
            <p class="text-muted"><?= e(__('citizen.welfare_desk_subheading')) ?></p>
        </div>

        <div class="row g-4">
            <!-- Office Details & Contact -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                            <i class="bi bi-geo-alt-fill text-danger"></i>
                            <?= e(__('citizen.welfare_office_title')) ?>
                        </h5>

                        <!-- Address -->
                        <div class="mb-3 d-flex align-items-start gap-3">
                            <div class="rounded-circle bg-success bg-opacity-10 p-2 d-flex align-items-center justify-content-center" style="min-width:40px;height:40px;">
                                <i class="bi bi-building text-success"></i>
                            </div>
                            <div>
                                <strong class="d-block"><?= e(__('citizen.welfare_office_address_label')) ?></strong>
                                <span class="text-muted small">Gram Panchayat Secretariat Office,<br>
                                Near Village Community Hall, Main Road,<br>
                                Ramayanpet Village, Medak District,<br>
                                Telangana — 502 032</span>
                            </div>
                        </div>

                        <!-- Office Timings -->
                        <div class="mb-3 d-flex align-items-start gap-3">
                            <div class="rounded-circle bg-warning bg-opacity-10 p-2 d-flex align-items-center justify-content-center" style="min-width:40px;height:40px;">
                                <i class="bi bi-clock text-warning"></i>
                            </div>
                            <div>
                                <strong class="d-block"><?= e(__('citizen.welfare_office_timings_label')) ?></strong>
                                <span class="text-muted small">Mon — Sat: 10:00 AM — 5:00 PM<br>
                                Sunday & Public Holidays: Closed<br>
                                <span class="text-success fw-semibold">Walk-in: No appointment needed</span></span>
                            </div>
                        </div>

                        <!-- Phone & Email -->
                        <div class="mb-3 d-flex align-items-start gap-3">
                            <div class="rounded-circle bg-primary bg-opacity-10 p-2 d-flex align-items-center justify-content-center" style="min-width:40px;height:40px;">
                                <i class="bi bi-telephone text-primary"></i>
                            </div>
                            <div>
                                <strong class="d-block"><?= e(__('citizen.welfare_contact_label')) ?></strong>
                                <span class="text-muted small">
                                    <i class="bi bi-telephone-fill text-success me-1"></i> <a href="tel:08456-234567" class="text-decoration-none">08456-234567</a><br>
                                    <i class="bi bi-whatsapp text-success me-1"></i> <a href="https://wa.me/919876543210" class="text-decoration-none">+91 98765 43210</a><br>
                                    <i class="bi bi-envelope-fill text-danger me-1"></i> <a href="mailto:gp.ramayanpet@telangana.gov.in" class="text-decoration-none">gp.ramayanpet@telangana.gov.in</a>
                                </span>
                            </div>
                        </div>

                        <!-- Walk-in vs Online -->
                        <div class="alert alert-success bg-success-subtle border-0 rounded-3 d-flex align-items-start gap-2 mb-0">
                            <i class="bi bi-info-circle-fill text-success mt-1"></i>
                            <div class="small">
                                <strong><?= e(__('citizen.welfare_walkin_title')) ?></strong><br>
                                <?= e(__('citizen.welfare_walkin_desc')) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Required Documents Checklist & Map -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                            <i class="bi bi-list-check text-primary"></i>
                            <?= e(__('citizen.welfare_docs_checklist_title')) ?>
                        </h5>
                        <div class="row g-2">
                            <?php
                            $checklistDocs = [
                                ['icon' => 'bi-person-vcard', 'name' => 'Aadhaar Card (Original + Xerox)'],
                                ['icon' => 'bi-credit-card-2-front', 'name' => 'Ration Card (White/Pink/Orange)'],
                                ['icon' => 'bi-file-earmark-text', 'name' => 'Caste Certificate (Father/Husband)'],
                                ['icon' => 'bi-currency-rupee', 'name' => 'Income Proof / Salary Slip'],
                                ['icon' => 'bi-bank', 'name' => 'Bank Passbook (First Page Xerox)'],
                                ['icon' => 'bi-camera', 'name' => 'Passport Size Photos (2 copies)'],
                                ['icon' => 'bi-house-door', 'name' => 'Address Proof (Electricity Bill / Voter ID)'],
                                ['icon' => 'bi-phone', 'name' => 'Mobile Number linked with Aadhaar'],
                            ];
                            foreach ($checklistDocs as $doc): ?>
                            <div class="col-sm-6">
                                <div class="form-check d-flex align-items-center gap-2 p-2 rounded-3 bg-light">
                                    <i class="bi <?= e($doc['icon']) ?> text-success"></i>
                                    <label class="form-check-label small"><?= e($doc['name']) ?></label>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Google Maps Placeholder -->
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="ratio ratio-16x9">
                        <img src="https://placehold.co/800x450/e9ecef/6c757d?text=Google+Maps+%E2%80%94+Panchayat+Office+Location"
                             alt="Map Placeholder" class="object-fit-cover">
                    </div>
                    <div class="card-body p-3 text-center">
                        <a href="https://maps.google.com" target="_blank" class="btn btn-outline-success btn-sm rounded-pill px-3 d-inline-flex align-items-center gap-2">
                            <i class="bi bi-map"></i> <?= e(__('citizen.welfare_open_map_btn')) ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ══════════════════════════════════════════════════════════════════
     ELIGIBILITY CHECKER JAVASCRIPT
     ══════════════════════════════════════════════════════════════════ -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('eligibilityForm');
    const resultsContainer = document.getElementById('eligibilityResults');
    const resultsGrid = document.getElementById('resultsGrid');
    const checkBtn = document.getElementById('checkEligibilityBtn');

    if (!form) return;

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(form);
        checkBtn.disabled = true;
        checkBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Checking...';

        fetch('<?= url("api/welfare/check") ?>', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            resultsGrid.innerHTML = '';
            if (data.success && data.results) {
                data.results.forEach(function(item) {
                    const statusIcon = item.eligible
                        ? '<i class="bi bi-check-circle-fill text-success fs-4"></i>'
                        : '<i class="bi bi-x-circle-fill text-danger fs-4"></i>';
                    const statusBadge = item.eligible
                        ? '<span class="badge bg-success rounded-pill">Eligible</span>'
                        : '<span class="badge bg-danger rounded-pill">Not Eligible</span>';
                    const borderClass = item.eligible ? 'border-success' : 'border-danger';

                    const card = document.createElement('div');
                    card.className = 'col-md-6';
                    card.innerHTML = `
                        <div class="card border-start border-4 ${borderClass} rounded-3 shadow-sm h-100">
                            <div class="card-body p-3 d-flex align-items-start gap-3">
                                <div class="mt-1">${statusIcon}</div>
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <strong class="small">${item.scheme}</strong>
                                        ${statusBadge}
                                    </div>
                                    <p class="text-muted small mb-0">${item.reason}</p>
                                </div>
                            </div>
                        </div>
                    `;
                    resultsGrid.appendChild(card);
                });
                resultsContainer.style.display = 'block';
                resultsContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        })
        .catch(err => {
            console.error('Eligibility check error:', err);
            resultsGrid.innerHTML = '<div class="col-12"><div class="alert alert-danger">An error occurred. Please try again.</div></div>';
            resultsContainer.style.display = 'block';
        })
        .finally(() => {
            checkBtn.disabled = false;
            checkBtn.innerHTML = '<i class="bi bi-search"></i> <?= e(__("citizen.welfare_check_now_btn")) ?>';
        });
    });
});
</script>

<?php view('layouts/footer'); ?>
