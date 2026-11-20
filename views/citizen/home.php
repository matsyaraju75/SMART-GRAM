<?php 
$activeLang = current_lang();
view('layouts/header', ['title' => __('common.app_title') . ' — ' . __('common.app_name')]); 
?>

<!-- ============================================================== -->
<!-- SECTION 1: Hero Section — Dark Green Gradient -->
<!-- ============================================================== -->
<section class="py-5" style="background: linear-gradient(135deg, #0b3d2c 0%, #145a3e 40%, #1a7a52 100%); min-height: 480px;">
    <div class="container py-4">
        <div class="row justify-content-center text-center">
            <div class="col-lg-10 col-xl-9">

                <!-- Gram Panchayat Badge -->
                <div class="d-inline-flex align-items-center gap-2 bg-white bg-opacity-10 text-white border border-white border-opacity-25 px-3 py-1 rounded-pill mb-3 small fw-semibold">
                    <i class="bi bi-geo-alt-fill text-warning"></i>
                    <span>
                        <?= e($village ? ($activeLang === 'te' ? $village['name_te'] : $village['name_en']) . ' ' . ($activeLang === 'te' ? 'గ్రామ పంచాయతీ' : 'Gram Panchayat') : 'Gram Panchayat') ?>
                    </span>
                </div>

                <!-- Hero Heading -->
                <h1 class="display-5 fw-bold text-white mb-3 font-telugu" style="line-height: 1.25;">
                    <?= e(__('citizen.home_hero_title')) ?>
                </h1>
                <p class="lead text-white text-opacity-75 mb-4 mx-auto" style="max-width: 700px; font-size: 1.1rem;">
                    <?= e(__('citizen.home_hero_subtitle')) ?>
                </p>

                <!-- Search Bar -->
                <div class="row justify-content-center mb-4">
                    <div class="col-lg-8 col-xl-7">
                        <form action="<?= url('track') ?>" method="GET" class="input-group input-group-lg shadow-lg rounded-pill overflow-hidden">
                            <span class="input-group-text bg-white border-0 ps-4"><i class="bi bi-search text-success"></i></span>
                            <input type="text" name="tracking_id" class="form-control border-0 py-3 font-monospace" placeholder="<?= e(__('citizen.home_search_placeholder')) ?>" style="font-size: 0.95rem;">
                            <button type="submit" class="btn btn-success fw-bold px-4"><?= e(__('citizen.home_search_btn')) ?></button>
                        </form>
                    </div>
                </div>

                <!-- Quick Action 4-Card Grid (Screenshot Design) -->
                <div class="row g-3 justify-content-center mb-4 text-start">
                    <div class="col-md-6 col-lg-3">
                        <div class="card h-100 border-0 shadow rounded-4 p-3 bg-white text-dark">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <div class="bg-danger bg-opacity-10 text-danger rounded-circle p-2 d-flex align-items-center justify-content-center" style="width:38px;height:38px;">
                                    <i class="bi bi-pencil-square fs-5"></i>
                                </div>
                                <h6 class="fw-bold mb-0 text-dark"><?= e(__('citizen.home_quick_grievance')) ?></h6>
                            </div>
                            <p class="text-muted small mb-3 flex-grow-1" style="font-size: 0.8rem; line-height: 1.4;">
                                <?= $activeLang === 'te' ? 'తాగునీరు, వీధి దీపాలు, రోడ్లపై ఫిర్యాదును నమోదు చేయండి.' : 'Report civic outages, streetlights, roads with photo proof.' ?>
                            </p>
                            <a href="<?= url('submit') ?>" class="btn btn-danger btn-sm rounded-pill fw-bold w-100 py-2 shadow-sm d-flex align-items-center justify-content-center gap-1">
                                <span><?= $activeLang === 'te' ? 'సమస్య నమోదు' : 'File Grievance Now' ?></span>
                                <i class="bi bi-arrow-right-short fs-5"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="card h-100 border-0 shadow rounded-4 p-3 bg-white text-dark">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <div class="bg-success bg-opacity-10 text-success rounded-circle p-2 d-flex align-items-center justify-content-center" style="width:38px;height:38px;">
                                    <i class="bi bi-shield-check fs-5"></i>
                                </div>
                                <h6 class="fw-bold mb-0 text-dark"><?= e(__('citizen.home_quick_welfare')) ?></h6>
                            </div>
                            <p class="text-muted small mb-3 flex-grow-1" style="font-size: 0.8rem; line-height: 1.4;">
                                <?= $activeLang === 'te' ? 'కుటుంబ సంక్షేమ పథకాలు, పెన్షన్ల అర్హతను క్షణాల్లో తనిఖీ చేయండి.' : 'Check eligibility for DBT pensions, PM-Kisan & state schemes.' ?>
                            </p>
                            <a href="<?= url('welfare') ?>" class="btn btn-success btn-sm rounded-pill fw-bold w-100 py-2 shadow-sm d-flex align-items-center justify-content-center gap-1">
                                <span><?= $activeLang === 'te' ? 'అర్హత తనిఖీ' : 'Check Eligibility & Apply' ?></span>
                                <i class="bi bi-arrow-right-short fs-5"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="card h-100 border-0 shadow rounded-4 p-3 bg-white text-dark">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width:38px;height:38px;">
                                    <i class="bi bi-file-earmark-text fs-5"></i>
                                </div>
                                <h6 class="fw-bold mb-0 text-dark"><?= e(__('citizen.home_quick_certificates')) ?></h6>
                            </div>
                            <p class="text-muted small mb-3 flex-grow-1" style="font-size: 0.8rem; line-height: 1.4;">
                                <?= $activeLang === 'te' ? 'కుల, ఆదాయ, నివాస ధృవీకరణ పత్రాల మార్గదర్శకాలు.' : 'Official guidelines for Caste, Income, and Nativity certificates.' ?>
                            </p>
                            <a href="<?= url('welfare#scheme-cards') ?>" class="btn btn-primary btn-sm rounded-pill fw-bold w-100 py-2 shadow-sm d-flex align-items-center justify-content-center gap-1">
                                <span><?= $activeLang === 'te' ? 'ధృవీకరణ పత్రాలు' : 'Get Delivery Signatures' ?></span>
                                <i class="bi bi-arrow-right-short fs-5"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="card h-100 border-0 shadow rounded-4 p-3 bg-white text-dark">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <div class="bg-dark bg-opacity-10 text-dark rounded-circle p-2 d-flex align-items-center justify-content-center" style="width:38px;height:38px;">
                                    <i class="bi bi-calendar-event fs-5"></i>
                                </div>
                                <h6 class="fw-bold mb-0 text-dark"><?= e(__('citizen.home_quick_agenda')) ?></h6>
                            </div>
                            <p class="text-muted small mb-3 flex-grow-1" style="font-size: 0.8rem; line-height: 1.4;">
                                <?= $activeLang === 'te' ? 'గ్రామ సభ సమావేశాల వివరాలు, ఎజెండా మరియు అభివృద్ధి పనులు.' : 'View upcoming meeting agenda, budget allocations & minutes.' ?>
                            </p>
                            <a href="<?= url('projects') ?>" class="btn btn-dark btn-sm rounded-pill fw-bold w-100 py-2 shadow-sm d-flex align-items-center justify-content-center gap-1">
                                <span><?= $activeLang === 'te' ? 'ఎజెండా & నివేదికలు' : 'View Agenda & Minutes' ?></span>
                                <i class="bi bi-arrow-right-short fs-5"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Secondary Buttons -->
                <div class="d-flex flex-wrap justify-content-center gap-2">
                    <a href="#emergency-section" class="btn btn-sm btn-danger rounded-pill px-3 py-2 d-flex align-items-center gap-1 shadow-sm">
                        <i class="bi bi-telephone-fill"></i>
                        <span><?= e(__('citizen.home_btn_emergency')) ?></span>
                    </a>
                    <a href="#services-section" class="btn btn-sm btn-outline-light rounded-pill px-3 py-2 d-flex align-items-center gap-1">
                        <i class="bi bi-building-check"></i>
                        <span><?= e(__('citizen.home_btn_property')) ?></span>
                    </a>
                    <a href="#" class="btn btn-sm btn-outline-light rounded-pill px-3 py-2 d-flex align-items-center gap-1">
                        <i class="bi bi-map"></i>
                        <span><?= e(__('citizen.home_btn_village_map')) ?></span>
                    </a>
                </div>

            </div>
        </div>
    </div>
</section>

<!-- ============================================================== -->
<!-- SECTION 2: Live Village Operational Metrics -->
<!-- ============================================================== -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="text-center mb-4">
            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill small mb-2 text-uppercase fw-semibold">
                <i class="bi bi-activity me-1"></i> <?= e(__('citizen.home_metrics_badge')) ?>
            </span>
            <h3 class="fw-bold text-dark font-telugu"><?= e(__('citizen.home_metrics_heading')) ?></h3>
        </div>

        <div class="row g-3 g-md-4">
            <!-- Total Grievances -->
            <div class="col-6 col-lg-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 p-md-4 h-100 text-center">
                    <div class="mx-auto bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 56px; height: 56px;">
                        <i class="bi bi-clipboard-data fs-4"></i>
                    </div>
                    <h2 class="fw-bold text-dark mb-1 font-monospace"><?= e($stats['total_grievances'] ?? '1,248') ?></h2>
                    <p class="text-muted small mb-0"><?= e(__('citizen.home_stat_total')) ?></p>
                </div>
            </div>
            <!-- Resolution Rate -->
            <div class="col-6 col-lg-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 p-md-4 h-100 text-center">
                    <div class="mx-auto bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 56px; height: 56px;">
                        <i class="bi bi-check-circle fs-4"></i>
                    </div>
                    <h2 class="fw-bold text-success mb-1 font-monospace"><?= e($stats['resolution_rate'] ?? '94.2%') ?></h2>
                    <p class="text-muted small mb-0"><?= e(__('citizen.home_stat_resolution')) ?></p>
                </div>
            </div>
            <!-- Pending Issues -->
            <div class="col-6 col-lg-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 p-md-4 h-100 text-center">
                    <div class="mx-auto bg-warning-subtle text-warning rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 56px; height: 56px;">
                        <i class="bi bi-hourglass-split fs-4"></i>
                    </div>
                    <h2 class="fw-bold text-warning mb-1 font-monospace"><?= e($stats['pending_issues'] ?? '18') ?></h2>
                    <p class="text-muted small mb-0"><?= e(__('citizen.home_stat_pending')) ?></p>
                </div>
            </div>
            <!-- Families Served -->
            <div class="col-6 col-lg-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 p-md-4 h-100 text-center">
                    <div class="mx-auto bg-info-subtle text-info rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 56px; height: 56px;">
                        <i class="bi bi-people-fill fs-4"></i>
                    </div>
                    <h2 class="fw-bold text-dark mb-1 font-monospace"><?= e($stats['families_served'] ?? '620+') ?></h2>
                    <p class="text-muted small mb-0"><?= e(__('citizen.home_stat_families')) ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================== -->
<!-- SECTION 3: Direct Gram Panchayat Counter Services -->
<!-- ============================================================== -->
<section class="py-5 bg-light border-top border-bottom" id="services-section">
    <div class="container">
        <div class="text-center mb-5">
            <span class="badge bg-light text-secondary border px-3 py-1 rounded-pill small mb-2 text-uppercase fw-semibold">
                <?= e(__('citizen.home_services_badge')) ?>
            </span>
            <h3 class="fw-bold text-dark font-telugu"><?= e(__('citizen.home_services_heading')) ?></h3>
            <p class="text-muted small mx-auto" style="max-width: 640px;">
                <?= e(__('citizen.home_services_subheading')) ?>
            </p>
        </div>

        <div class="row g-3 g-md-4">
            <?php
            $serviceCards = $serviceCategories ?? [
                ['icon' => 'bi-droplet-half', 'color' => 'primary', 'title_key' => 'home_svc_water_title', 'desc_key' => 'home_svc_water_desc', 'sla' => '48h'],
                ['icon' => 'bi-lightning-charge-fill', 'color' => 'warning', 'title_key' => 'home_svc_electric_title', 'desc_key' => 'home_svc_electric_desc', 'sla' => '24h'],
                ['icon' => 'bi-moisture', 'color' => 'info', 'title_key' => 'home_svc_drainage_title', 'desc_key' => 'home_svc_drainage_desc', 'sla' => '72h'],
                ['icon' => 'bi-signpost-split-fill', 'color' => 'secondary', 'title_key' => 'home_svc_road_title', 'desc_key' => 'home_svc_road_desc', 'sla' => '7d'],
                ['icon' => 'bi-trash3-fill', 'color' => 'success', 'title_key' => 'home_svc_sanitation_title', 'desc_key' => 'home_svc_sanitation_desc', 'sla' => '24h'],
                ['icon' => 'bi-heart-pulse-fill', 'color' => 'danger', 'title_key' => 'home_svc_health_title', 'desc_key' => 'home_svc_health_desc', 'sla' => '48h'],
                ['icon' => 'bi-building', 'color' => 'dark', 'title_key' => 'home_svc_building_title', 'desc_key' => 'home_svc_building_desc', 'sla' => '15d'],
                ['icon' => 'bi-hospital', 'color' => 'primary', 'title_key' => 'home_svc_clinic_title', 'desc_key' => 'home_svc_clinic_desc', 'sla' => '48h'],
                ['icon' => 'bi-flower1', 'color' => 'success', 'title_key' => 'home_svc_agriculture_title', 'desc_key' => 'home_svc_agriculture_desc', 'sla' => '7d'],
            ];
            foreach ($serviceCards as $svc): ?>
            <div class="col-sm-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="d-flex align-items-start gap-3 mb-3">
                            <div class="bg-<?= $svc['color'] ?> text-white p-3 rounded-4 shadow-sm flex-shrink-0">
                                <i class="bi <?= $svc['icon'] ?> fs-4"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1 text-dark font-telugu"><?= e(__('citizen.' . $svc['title_key'])) ?></h5>
                                <p class="text-muted small mb-0"><?= e(__('citizen.' . $svc['desc_key'])) ?></p>
                            </div>
                        </div>
                        <div class="mt-auto d-flex flex-wrap gap-2">
                            <a href="<?= url('submit') ?>" class="btn btn-sm btn-outline-success rounded-pill px-3">
                                <i class="bi bi-pencil-square me-1"></i><?= e(__('citizen.home_svc_lodge')) ?>
                            </a>
                            <a href="<?= url('track') ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                <i class="bi bi-search me-1"></i><?= e(__('citizen.home_svc_status')) ?>
                            </a>
                            <span class="badge bg-light text-dark border rounded-pill px-2 py-1 small font-monospace align-self-center">
                                <i class="bi bi-clock me-1"></i>SLA: <?= e($svc['sla']) ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============================================================== -->
<!-- SECTION 4: Constitutional Quote & Sarpanch Message -->
<!-- ============================================================== -->
<section class="py-5" style="background: linear-gradient(135deg, #f8fbf9 0%, #edf7f2 100%);">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
                    <div class="row align-items-center g-4">
                        <div class="col-md-4 text-center text-md-start">
                            <div class="position-relative d-inline-block">
                                <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=400&q=80" 
                                     alt="Sri Rajesh Kumar, Sarpanch" 
                                     class="rounded-4 img-fluid shadow-sm object-fit-cover" 
                                     style="width: 220px; height: 260px; object-fit: cover;">
                                <div class="position-absolute bottom-0 start-0 end-0 bg-dark bg-opacity-75 text-white p-2 rounded-bottom-4 text-center">
                                    <strong class="d-block small">Sri Rajesh Kumar</strong>
                                    <span class="text-warning" style="font-size: 0.72rem;">Sarpanch, Gram Panchayat</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="d-flex align-items-center gap-2 text-success fw-bold small text-uppercase mb-2">
                                <i class="bi bi-patch-check-fill fs-5"></i>
                                <span>Constitutional Mandate — 73rd Amendment</span>
                            </div>
                            <blockquote class="blockquote mb-3">
                                <h4 class="fw-bold text-dark font-telugu lh-base" style="font-size: 1.35rem;">
                                    "<?= e(__('citizen.home_quote_text')) ?>"
                                </h4>
                            </blockquote>
                            <p class="text-secondary small mb-4 lh-lg">
                                <?= $activeLang === 'te' 
                                    ? 'స్మార్ట్ గ్రామ్ పోర్టల్ ద్వారా గ్రామ సమస్యలు ఎటువంటి మధ్యవర్తులు లేకుండా నేరుగా పంచాయతీ దృష్టికి వస్తాయి. ప్రతి ఫిర్యాదుకు డిజిటల్ రసీదు, నిర్ణీత గడువు మరియు ఫీల్డ్ అధికారి ఫోటో సాక్ష్యంతో పరిష్కారం లభిస్తుంది. మా పంచాయతీ పరిధిలో 100% పారదర్శకతే మా లక్ష్యం.' 
                                    : 'Welcome to the official Smart Gram citizen service portal. Every complaint is assigned a verified tracking timeline with mandatory photo evidence upon closure. Direct administrative action is initiated within 24 hours to ensure seamless, doorstep civic delivery.' ?>
                            </p>
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 pt-3 border-top">
                                <div>
                                    <span class="text-muted small d-block"><?= e(__('citizen.home_quote_source')) ?></span>
                                    <strong class="text-success small"><?= e(__('citizen.home_quote_cite')) ?></strong>
                                </div>
                                <a href="<?= url('about') ?>" class="btn btn-outline-success btn-sm rounded-pill px-4 py-2 fw-semibold d-inline-flex align-items-center gap-2">
                                    <i class="bi bi-person-lines-fill"></i>
                                    <span><?= $activeLang === 'te' ? 'సర్పంచ్ సందేశం చదవండి' : "View Sarpanch's Message" ?></span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================== -->
<!-- SECTION 5: Gram Sabha Resolutions & Sanction Orders -->
<!-- ============================================================== -->
<section class="py-5 bg-white" id="resolutions-section">
    <div class="container">
        <div class="text-center mb-4">
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 rounded-pill small mb-2 text-uppercase fw-semibold">
                <i class="bi bi-journal-text me-1"></i> <?= e(__('citizen.home_resolutions_badge')) ?>
            </span>
            <h3 class="fw-bold text-dark font-telugu"><?= e(__('citizen.home_resolutions_heading')) ?></h3>
            <p class="text-muted small"><?= e(__('citizen.home_resolutions_subheading')) ?></p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3 small fw-semibold text-uppercase text-muted"><?= e(__('citizen.home_res_date')) ?></th>
                                    <th class="py-3 small fw-semibold text-uppercase text-muted"><?= e(__('citizen.home_res_number')) ?></th>
                                    <th class="py-3 small fw-semibold text-uppercase text-muted"><?= e(__('citizen.home_res_subject')) ?></th>
                                    <th class="py-3 small fw-semibold text-uppercase text-muted"><?= $activeLang === 'te' ? 'మంజూరైన నిధులు' : 'Sanction Amount' ?></th>
                                    <th class="py-3 pe-4 small fw-semibold text-uppercase text-muted text-end"><?= e(__('citizen.home_res_status')) ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $resolutions = $recentResolutions ?? [
                                    ['date' => '2026-11-15', 'number' => 'GS/RES/2026/042', 'subject_key' => 'home_res_sub1', 'amount' => '₹4,50,000', 'status' => 'approved'],
                                    ['date' => '2026-11-10', 'number' => 'GS/RES/2026/041', 'subject_key' => 'home_res_sub2', 'amount' => '₹2,10,000', 'status' => 'pending'],
                                    ['date' => '2026-11-02', 'number' => 'GS/RES/2026/040', 'subject_key' => 'home_res_sub3', 'amount' => '₹3,25,000', 'status' => 'sanctioned'],
                                    ['date' => '2026-10-25', 'number' => 'GS/RES/2026/039', 'subject_key' => 'home_res_sub4', 'amount' => '₹1,80,000', 'status' => 'approved'],
                                    ['date' => '2026-10-18', 'number' => 'GS/RES/2026/038', 'subject_key' => 'home_res_sub5', 'amount' => '₹5,40,000', 'status' => 'approved'],
                                ];
                                $statusBadges = [
                                    'approved'   => 'bg-success',
                                    'pending'    => 'bg-warning text-dark',
                                    'sanctioned' => 'bg-primary',
                                    'rejected'   => 'bg-danger',
                                ];
                                foreach ($resolutions as $res): ?>
                                <tr>
                                    <td class="ps-4 py-3 font-monospace small"><?= e($res['date']) ?></td>
                                    <td class="py-3 font-monospace fw-semibold text-primary small"><?= e($res['number']) ?></td>
                                    <td class="py-3 font-telugu fw-medium text-dark"><?= e(__('citizen.' . $res['subject_key'])) ?></td>
                                    <td class="py-3 font-monospace fw-bold text-success small"><?= e($res['amount'] ?? '₹3,50,000') ?></td>
                                    <td class="py-3 pe-4 text-end">
                                        <span class="badge <?= $statusBadges[$res['status']] ?? 'bg-secondary' ?> rounded-pill px-3 py-1 text-uppercase small">
                                            <?= e(__('citizen.home_status_' . $res['status'])) ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================== -->
<!-- SECTION 6: Village Emergency Helplines -->
<!-- ============================================================== -->
<section class="py-5" id="emergency-section" style="background: linear-gradient(135deg, #fff5f5 0%, #ffe3e3 100%);">
    <div class="container">
        <div class="text-center mb-4">
            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill small mb-2 text-uppercase fw-semibold">
                <i class="bi bi-exclamation-triangle-fill me-1"></i> <?= e(__('citizen.home_emergency_badge')) ?>
            </span>
            <h3 class="fw-bold text-danger font-telugu"><?= e(__('citizen.home_emergency_heading')) ?></h3>
            <p class="text-muted small"><?= e(__('citizen.home_emergency_subheading')) ?></p>
        </div>

        <div class="row g-3 g-md-4 justify-content-center">
            <?php
            $helplines = [
                ['number' => '112',  'icon' => 'bi-shield-exclamation', 'color' => 'danger',  'name_key' => 'home_hl_emergency'],
                ['number' => '108',  'icon' => 'bi-heart-pulse-fill',   'color' => 'primary', 'name_key' => 'home_hl_ambulance'],
                ['number' => '1912', 'icon' => 'bi-lightning-charge-fill', 'color' => 'warning', 'name_key' => 'home_hl_electricity'],
                ['number' => '100',  'icon' => 'bi-shield-lock-fill',   'color' => 'dark',    'name_key' => 'home_hl_police'],
                ['number' => '181',  'icon' => 'bi-person-hearts',      'color' => 'info',    'name_key' => 'home_hl_women'],
                ['number' => '1800-425-1899', 'icon' => 'bi-building-fill', 'color' => 'success', 'name_key' => 'home_hl_panchayat'],
            ];
            foreach ($helplines as $hl): ?>
            <div class="col-6 col-md-4 col-lg-2">
                <a href="tel:<?= str_replace('-', '', $hl['number']) ?>" class="card border-0 shadow-sm rounded-4 p-3 h-100 text-center text-decoration-none hover-shadow">
                    <div class="mx-auto bg-<?= $hl['color'] ?> text-white rounded-circle d-flex align-items-center justify-content-center mb-2 shadow-sm" style="width: 52px; height: 52px;">
                        <i class="bi <?= $hl['icon'] ?> fs-4"></i>
                    </div>
                    <h4 class="fw-bold font-monospace text-<?= $hl['color'] ?> mb-1" style="font-size: 1.3rem;"><?= e($hl['number']) ?></h4>
                    <p class="text-muted small mb-0 font-telugu"><?= e(__('citizen.' . $hl['name_key'])) ?></p>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php view('layouts/footer'); ?>
