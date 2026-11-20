<?php 
use App\Models\Grievance;
view('layouts/header', ['title' => __('dashboard.title') . ' - Smart Gram']); 
$lang = current_lang();
?>

<style>
/* Dashboard Styles & Animations */
.kpi-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    border: none;
    border-radius: 1rem;
}
.kpi-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important;
}
.hotspot-glow {
    background: linear-gradient(135deg, rgba(220, 53, 69, 0.05) 0%, rgba(255, 193, 7, 0.05) 100%);
    border-left: 4px solid #dc3545 !important;
}
.animate-pulse {
    animation: pulse-red 2s infinite;
}
@keyframes pulse-red {
    0% { opacity: 1; }
    50% { opacity: 0.6; }
    100% { opacity: 1; }
}

/* Official Print Layout */
@media print {
    body { background: #fff !important; font-size: 12pt; }
    .navbar, .footer, .btn, .no-print, .pagination, .alert { display: none !important; }
    .card { border: 1px solid #ddd !important; box-shadow: none !important; break-inside: avoid; }
    .print-only-header { display: block !important; margin-bottom: 20px; }
    .container { max-width: 100% !important; padding: 0 !important; }
}
.print-only-header { display: none; }
</style>

<div class="container py-4">

    <!-- Print Only Header -->
    <div class="print-only-header text-center border-bottom pb-3">
        <h2 class="fw-bold mb-1">గ్రామ పంచాయతీ కార్యాలయ నివేదిక (Smart Gram Official Report)</h2>
        <p class="text-muted mb-0">Generated on <?= date('d M Y, h:i A') ?> | Village: Ramapuram (రామాపురం)</p>
    </div>

    <!-- Header Section -->
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 pb-3 mb-4 border-bottom">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill fw-bold text-uppercase">
                    <i class="bi bi-shield-lock-fill me-1"></i> Admin Portal
                </span>
                <span class="text-muted small">| System Control Center (పంచాయతీ ప్రధాన నియంత్రణ కేంద్రం)</span>
            </div>
            <h2 class="fw-bold mb-1"><?= __('dashboard.title') ?></h2>
            <p class="text-muted small mb-0"><?= __('dashboard.subtitle') ?></p>
        </div>

        <!-- Action Toolbar -->
        <div class="d-flex flex-wrap gap-2 no-print">
            <a href="<?= url('console') ?>" class="btn btn-warning rounded-pill px-3 shadow-sm d-flex align-items-center gap-2 fw-bold text-dark" style="background:#ffc107; border-color:#ffc107;">
                <i class="bi bi-cpu-fill"></i>
                <span><?= $lang === 'te' ? 'కార్యాచరణ కన్సోల్' : 'Executive Operations Console' ?></span>
            </a>
            <a href="<?= url('admin/locations') ?>" class="btn btn-primary rounded-pill px-3 shadow-sm d-flex align-items-center gap-2">
                <i class="bi bi-geo-alt-fill"></i>
                <span><?= $lang === 'te' ? 'ప్రాంతాల నిర్వహణ' : 'Manage Locations' ?></span>
            </a>
            <a href="<?= url('dashboard/export?' . http_build_query($filters)) ?>" class="btn btn-outline-success rounded-pill px-3 shadow-sm d-flex align-items-center gap-2">
                <i class="bi bi-file-earmark-spreadsheet-fill"></i>
                <span><?= __('dashboard.btn_export_csv') ?></span>
            </a>
            <button onclick="window.print()" class="btn btn-outline-dark rounded-pill px-3 shadow-sm d-flex align-items-center gap-2">
                <i class="bi bi-printer-fill"></i>
                <span><?= __('dashboard.btn_print') ?></span>
            </button>
            <div class="vr mx-1 d-none d-sm-block"></div>
            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill d-flex align-items-center gap-1">
                <i class="bi bi-person-check-fill"></i> <?= e($user['full_name']) ?>
            </span>
        </div>
    </div>

    <!-- 1. KPI Summary Cards Grid -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-speedometer2 text-primary me-2"></i><?= __('dashboard.section_kpi') ?></h5>
        <span class="badge bg-light text-muted border">Real-time Analytics</span>
    </div>
    <div class="row g-3 mb-4">
        <!-- Total Grievances -->
        <div class="col-6 col-md-4 col-xl-3">
            <div class="card kpi-card shadow-sm h-100 p-3 bg-white">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="text-muted small fw-bold text-uppercase"><?= __('dashboard.kpi_total') ?></span>
                    <div class="bg-primary-subtle text-primary p-2 rounded-3"><i class="bi bi-folder2-open fs-5"></i></div>
                </div>
                <div class="display-6 fw-bold text-dark"><?= $analytics['total'] ?></div>
                <div class="text-muted small mt-2">
                    <i class="bi bi-check2-circle text-success me-1"></i><?= $lang === 'te' ? 'మొత్తం రికార్డులు' : 'All submissions' ?>
                </div>
            </div>
        </div>

        <!-- Pending / Under Review -->
        <div class="col-6 col-md-4 col-xl-3">
            <div class="card kpi-card shadow-sm h-100 p-3 bg-white">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="text-muted small fw-bold text-uppercase"><?= __('dashboard.kpi_pending') ?></span>
                    <div class="bg-info-subtle text-info-emphasis p-2 rounded-3"><i class="bi bi-hourglass-split fs-5"></i></div>
                </div>
                <div class="display-6 fw-bold text-info-emphasis"><?= $analytics['pending'] ?></div>
                <div class="text-muted small mt-2">
                    <?= $lang === 'te' ? 'పరిశీలన & నిర్ధారణ కోసం' : 'Awaiting verification' ?>
                </div>
            </div>
        </div>

        <!-- In Progress -->
        <div class="col-6 col-md-4 col-xl-3">
            <div class="card kpi-card shadow-sm h-100 p-3 bg-white">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="text-muted small fw-bold text-uppercase"><?= __('dashboard.kpi_in_progress') ?></span>
                    <div class="bg-warning-subtle text-warning-emphasis p-2 rounded-3"><i class="bi bi-tools fs-5"></i></div>
                </div>
                <div class="display-6 fw-bold text-warning-emphasis"><?= $analytics['in_progress'] + $analytics['assigned'] ?></div>
                <div class="text-muted small mt-2">
                    <?= $analytics['assigned'] ?> <?= $lang === 'te' ? 'కేటాయించినవి' : 'assigned' ?>, <?= $analytics['in_progress'] ?> <?= $lang === 'te' ? 'పనిలో ఉన్నవి' : 'working' ?>
                </div>
            </div>
        </div>

        <!-- Resolved & Closed -->
        <div class="col-6 col-md-4 col-xl-3">
            <div class="card kpi-card shadow-sm h-100 p-3 bg-white">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="text-muted small fw-bold text-uppercase"><?= __('dashboard.kpi_resolved') ?></span>
                    <div class="bg-success-subtle text-success p-2 rounded-3"><i class="bi bi-patch-check-fill fs-5"></i></div>
                </div>
                <div class="display-6 fw-bold text-success"><?= $analytics['resolved'] + $analytics['closed'] ?></div>
                <div class="text-muted small mt-2">
                    <span class="badge bg-success-subtle text-success"><?= $analytics['closed'] ?> <?= __('dashboard.kpi_closed') ?></span>
                </div>
            </div>
        </div>

        <!-- SLA Overdue Alert Card -->
        <div class="col-6 col-md-4 col-xl-3">
            <div class="card kpi-card shadow-sm h-100 p-3 border-danger <?= $analytics['overdue'] > 0 ? 'bg-danger-subtle' : 'bg-white' ?>">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="text-danger small fw-bold text-uppercase"><?= __('dashboard.kpi_overdue') ?></span>
                    <div class="bg-danger text-white p-2 rounded-3 <?= $analytics['overdue'] > 0 ? 'animate-pulse' : '' ?>">
                        <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                    </div>
                </div>
                <div class="display-6 fw-bold text-danger"><?= $analytics['overdue'] ?></div>
                <div class="text-danger small mt-2 fw-semibold">
                    <?= $analytics['overdue'] > 0 ? ($lang === 'te' ? 'తక్షణ చర్య అవసరం!' : 'Immediate Action Required!') : ($lang === 'te' ? 'గడువు దాటినవి లేవు' : 'Zero Breaches') ?>
                </div>
            </div>
        </div>

        <!-- Reopened Grievances -->
        <div class="col-6 col-md-4 col-xl-3">
            <div class="card kpi-card shadow-sm h-100 p-3 bg-white">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="text-muted small fw-bold text-uppercase"><?= __('dashboard.kpi_reopened') ?></span>
                    <div class="bg-danger-subtle text-danger p-2 rounded-3"><i class="bi bi-arrow-counterclockwise fs-5"></i></div>
                </div>
                <div class="display-6 fw-bold text-danger"><?= $analytics['reopened'] ?></div>
                <div class="text-muted small mt-2">
                    <?= $lang === 'te' ? 'పౌరులు తిరిగి తెరిచారు' : 'Citizen Unsatisfied' ?>
                </div>
            </div>
        </div>

        <!-- Citizen Satisfaction Rating -->
        <div class="col-6 col-md-4 col-xl-3">
            <div class="card kpi-card shadow-sm h-100 p-3 bg-white">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="text-muted small fw-bold text-uppercase"><?= __('dashboard.kpi_satisfaction') ?></span>
                    <div class="bg-warning-subtle text-warning-emphasis p-2 rounded-3"><i class="bi bi-star-fill fs-5"></i></div>
                </div>
                <div class="d-flex align-items-baseline gap-2">
                    <div class="display-6 fw-bold text-dark"><?= $analytics['avg_rating'] ?: '—' ?></div>
                    <span class="text-muted small">/ 5.0</span>
                </div>
                <div class="text-muted small mt-2">
                    <?php if ($analytics['total_ratings'] > 0): ?>
                        <span class="text-warning"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i></span>
                        <span class="ms-1"><?= $analytics['total_ratings'] ?> <?= __('dashboard.ratings_count') ?></span>
                    <?php else: ?>
                        <span><?= $lang === 'te' ? 'ఇంకా రేటింగ్‌లు లేవు' : 'No ratings yet' ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- SLA Compliance Rate -->
        <div class="col-6 col-md-4 col-xl-3">
            <div class="card kpi-card shadow-sm h-100 p-3 bg-white">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="text-muted small fw-bold text-uppercase"><?= __('dashboard.kpi_compliance') ?></span>
                    <div class="bg-success-subtle text-success p-2 rounded-3"><i class="bi bi-graph-up-arrow fs-5"></i></div>
                </div>
                <div class="display-6 fw-bold <?= $analytics['compliance_rate'] >= 80 ? 'text-success' : 'text-danger' ?>">
                    <?= $analytics['compliance_rate'] ?>%
                </div>
                <div class="progress mt-2" style="height: 6px;">
                    <div class="progress-bar <?= $analytics['compliance_rate'] >= 80 ? 'bg-success' : 'bg-danger' ?>" style="width: <?= $analytics['compliance_rate'] ?>%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Urgent Overdue Escalations Alert Table -->
    <?php if (!empty($overdueAlerts)): ?>
    <div class="card border-danger shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-header bg-danger text-white py-3 px-4 d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-shield-exclamation fs-4"></i>
                <h5 class="fw-bold mb-0"><?= __('dashboard.section_sla_alerts') ?></h5>
            </div>
            <span class="badge bg-white text-danger fw-bold px-3 py-1 rounded-pill">
                <?= count($overdueAlerts) ?> <?= $lang === 'te' ? 'సమస్యలు గడువు దాటాయి' : 'Overdue Breaches' ?>
            </span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-danger text-danger">
                    <tr>
                        <th class="ps-4"><?= __('dashboard.col_id') ?></th>
                        <th><?= __('dashboard.col_citizen') ?></th>
                        <th><?= __('dashboard.col_ward') ?></th>
                        <th><?= __('dashboard.col_category') ?></th>
                        <th><?= __('dashboard.col_priority') ?></th>
                        <th><?= __('dashboard.col_assigned') ?></th>
                        <th><?= __('dashboard.col_sla') ?></th>
                        <th class="text-end pe-4"><?= __('dashboard.col_action') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($overdueAlerts as $od): ?>
                    <tr>
                        <td class="ps-4">
                            <span class="fw-bold text-danger font-monospace"><?= e($od['tracking_id']) ?></span>
                        </td>
                        <td>
                            <div class="fw-semibold small"><?= e($od['citizen_name']) ?></div>
                            <div class="text-muted small"><?= e($od['citizen_phone']) ?></div>
                        </td>
                        <td class="small"><?= e($od['ward_number'] ?? '—') ?></td>
                        <td>
                            <span class="badge bg-light text-dark border">
                                <?= $lang === 'te' ? e($od['category_name_te']) : e($od['category_name_en']) ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge <?= Grievance::getPriorityBadgeClass($od['priority']) ?> rounded-pill px-2 py-1">
                                <?= strtoupper($od['priority']) ?>
                            </span>
                        </td>
                        <td class="small">
                            <?= $od['assigned_officer_name'] ? e($od['assigned_officer_name']) : '<span class="text-danger fw-bold">' . ($lang === 'te' ? 'అధికారి కేటాయించలేదు' : 'Unassigned') . '</span>' ?>
                        </td>
                        <td>
                            <span class="badge bg-danger text-white rounded-pill px-2 py-1 fw-bold">
                                <i class="bi bi-clock-history me-1"></i><?= $od['hours_overdue'] ?> <?= __('dashboard.sla_hours') ?> <?= __('dashboard.sla_overdue_by') ?>
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <a href="<?= url('grievance/view?id=' . $od['id']) ?>" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                <?= __('dashboard.action_view') ?> <i class="bi bi-arrow-right"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <!-- 3. Two-Column Analytics: Category Breakdown & Ward Hotspots -->
    <div class="row g-4 mb-4">
        <!-- Category Distribution -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-4 bg-white">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-pie-chart-fill text-primary me-2"></i><?= __('dashboard.section_categories') ?>
                    </h5>
                    <span class="badge bg-light text-secondary border"><?= count($categoryBreakdown) ?> <?= $lang === 'te' ? 'విభాగాలు' : 'Categories' ?></span>
                </div>
                
                <div class="d-flex flex-column gap-3 mt-3">
                    <?php foreach ($categoryBreakdown as $cat): ?>
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small fw-semibold d-flex align-items-center gap-2">
                                <i class="bi <?= e($cat['icon']) ?> text-muted"></i>
                                <?= $lang === 'te' ? e($cat['name_te']) : e($cat['name_en']) ?>
                            </span>
                            <span class="badge bg-light text-dark border"><?= $cat['count'] ?> (<?= $cat['percentage'] ?>%)</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: <?= $cat['percentage'] ?>%"></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Ward Hotspots Analysis -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-4 bg-white">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-geo-alt-fill text-danger me-2"></i><?= __('dashboard.section_wards') ?>
                    </h5>
                    <span class="badge bg-light text-secondary border"><?= count($wardBreakdown) ?> <?= $lang === 'te' ? 'వార్డులు' : 'Wards' ?></span>
                </div>

                <div class="table-responsive mt-2">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th><?= __('dashboard.col_ward') ?></th>
                                <th><?= __('dashboard.kpi_total') ?></th>
                                <th><?= $lang === 'te' ? 'శాతం' : 'Distribution' ?></th>
                                <th><?= $lang === 'te' ? 'స్థితి' : 'Status' ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($wardBreakdown as $w): ?>
                            <tr class="<?= $w['is_hotspot'] ? 'hotspot-glow' : '' ?>">
                                <td>
                                    <div class="fw-bold text-dark"><?= e($w['ward_number']) ?></div>
                                    <div class="text-muted small"><?= e($w['ward_name']) ?></div>
                                </td>
                                <td>
                                    <span class="fw-bold fs-6"><?= $w['count'] ?></span>
                                </td>
                                <td style="width: 35%;">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height: 6px;">
                                            <div class="progress-bar <?= $w['is_hotspot'] ? 'bg-danger' : 'bg-secondary' ?>" style="width: <?= $w['percentage'] ?>%"></div>
                                        </div>
                                        <span class="small text-muted"><?= $w['percentage'] ?>%</span>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($w['is_hotspot']): ?>
                                        <span class="badge bg-danger text-white rounded-pill px-2 py-1">
                                            <i class="bi bi-fire me-1"></i>Hotspot
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-light text-muted border">Normal</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. Field Officer Workload Distribution -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-white">
        <div class="card-header bg-white py-3 px-4 border-0 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">
                <i class="bi bi-people-fill text-success me-2"></i><?= __('dashboard.section_workload') ?>
            </h5>
            <span class="text-muted small"><?= count($workload) ?> <?= $lang === 'te' ? 'అధికారులు అందుబాటులో ఉన్నారు' : 'Field Officers Active' ?></span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Officer Name & Contact</th>
                        <th>Department</th>
                        <th>Assigned</th>
                        <th>In Progress</th>
                        <th>Resolved</th>
                        <th>Overdue Breaches</th>
                        <th class="pe-4">Workload Load Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($workload)): ?>
                        <tr><td colspan="7" class="text-center py-3 text-muted">No officers configured yet.</td></tr>
                    <?php else: ?>
                        <?php foreach ($workload as $off): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold"><?= e($off['full_name']) ?></div>
                                <div class="text-muted small"><i class="bi bi-telephone me-1"></i><?= e($off['phone']) ?></div>
                            </td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                    <?= $lang === 'te' ? e($off['dept_name_te']) : e($off['dept_name_en']) ?>
                                </span>
                            </td>
                            <td><span class="badge bg-light text-dark border"><?= $off['assigned_count'] ?></span></td>
                            <td><span class="badge bg-warning-subtle text-warning-emphasis"><?= $off['in_progress_count'] ?></span></td>
                            <td><span class="badge bg-success-subtle text-success"><?= $off['resolved_count'] ?></span></td>
                            <td>
                                <?php if ($off['overdue_count'] > 0): ?>
                                    <span class="badge bg-danger text-white"><?= $off['overdue_count'] ?> Overdue</span>
                                <?php else: ?>
                                    <span class="badge bg-light text-success border">0</span>
                                <?php endif; ?>
                            </td>
                            <td class="pe-4" style="min-width: 150px;">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height: 6px;">
                                        <div class="progress-bar <?= $off['overdue_count'] > 0 ? 'bg-danger' : 'bg-success' ?>" style="width: <?= min(100, $off['total_assigned'] * 20) ?>%"></div>
                                    </div>
                                    <span class="small fw-semibold"><?= $off['total_assigned'] ?> active</span>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- 5. Search & Multi-Tier Geographic Filter Bar -->
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white no-print">
        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
            <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i class="bi bi-funnel-fill text-primary"></i>
                <?= $lang === 'te' ? 'ఫిల్టర్లు & శోధన (Geographic & Operational Filters)' : 'Geographic & Operational Filters' ?>
            </h6>
            <span class="badge bg-light text-muted border">6-Tier Administrative Hierarchy</span>
        </div>

        <form method="GET" action="<?= url('admin') ?>" class="row g-3" id="adminFilterForm">
            <!-- Row 1: Keywords & Core Status -->
            <div class="col-12 col-md-4">
                <label class="form-label small fw-bold text-muted"><?= __('dashboard.search_placeholder') ?></label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control bg-light border-start-0" placeholder="<?= __('dashboard.search_placeholder') ?>" value="<?= e($filters['search']) ?>">
                </div>
            </div>

            <div class="col-6 col-md-2">
                <label class="form-label small fw-bold text-muted"><?= __('dashboard.col_status') ?></label>
                <select name="status" class="form-select bg-light">
                    <option value="all"><?= __('dashboard.filter_all_status') ?></option>
                    <option value="submitted" <?= $filters['status'] === 'submitted' ? 'selected' : '' ?>>Submitted</option>
                    <option value="under_review" <?= $filters['status'] === 'under_review' ? 'selected' : '' ?>>Under Review</option>
                    <option value="assigned" <?= $filters['status'] === 'assigned' ? 'selected' : '' ?>>Assigned</option>
                    <option value="in_progress" <?= $filters['status'] === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                    <option value="resolved" <?= $filters['status'] === 'resolved' ? 'selected' : '' ?>>Resolved</option>
                    <option value="closed" <?= $filters['status'] === 'closed' ? 'selected' : '' ?>>Closed</option>
                    <option value="reopened" <?= $filters['status'] === 'reopened' ? 'selected' : '' ?>>Reopened</option>
                </select>
            </div>

            <div class="col-6 col-md-2">
                <label class="form-label small fw-bold text-muted"><?= $lang === 'te' ? 'శాఖ' : 'Department' ?></label>
                <select name="department_id" class="form-select bg-light">
                    <option value="all"><?= $lang === 'te' ? 'అన్ని శాఖలు' : 'All Departments' ?></option>
                    <?php foreach ($departments as $dept): ?>
                        <option value="<?= $dept['id'] ?>" <?= (string)($filters['department_id'] ?? 'all') === (string)$dept['id'] ? 'selected' : '' ?>>
                            <?= $lang === 'te' ? e($dept['name_te']) : e($dept['name_en']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-6 col-md-2">
                <label class="form-label small fw-bold text-muted"><?= __('dashboard.col_category') ?></label>
                <select name="category_id" class="form-select bg-light">
                    <option value="all"><?= __('dashboard.filter_all_categories') ?></option>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= (string)$filters['category_id'] === (string)$c['id'] ? 'selected' : '' ?>>
                            <?= $lang === 'te' ? e($c['name_te']) : e($c['name_en']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-6 col-md-1">
                <label class="form-label small fw-bold text-muted"><?= __('dashboard.col_priority') ?></label>
                <select name="priority" class="form-select bg-light">
                    <option value="all">All</option>
                    <option value="low" <?= $filters['priority'] === 'low' ? 'selected' : '' ?>>Low</option>
                    <option value="medium" <?= $filters['priority'] === 'medium' ? 'selected' : '' ?>>Med</option>
                    <option value="high" <?= $filters['priority'] === 'high' ? 'selected' : '' ?>>High</option>
                    <option value="critical" <?= $filters['priority'] === 'critical' ? 'selected' : '' ?>>Crit</option>
                </select>
            </div>

            <div class="col-6 col-md-1">
                <label class="form-label small fw-bold text-muted"><?= __('dashboard.col_sla') ?></label>
                <select name="sla_status" class="form-select bg-light">
                    <option value="all">All</option>
                    <option value="overdue" <?= $filters['sla_status'] === 'overdue' ? 'selected' : '' ?>>Overdue</option>
                    <option value="due_today" <?= $filters['sla_status'] === 'due_today' ? 'selected' : '' ?>>Today</option>
                    <option value="on_time" <?= $filters['sla_status'] === 'on_time' ? 'selected' : '' ?>>On Time</option>
                </select>
            </div>

            <!-- Row 2: Progressive Geographic Dropdowns (6 Tiers) -->
            <div class="col-6 col-md-2">
                <label class="form-label small fw-bold text-primary">
                    <i class="bi bi-geo-alt me-1"></i><?= $lang === 'te' ? 'రాష్ట్రం' : 'State' ?>
                </label>
                <select name="state_id" id="adminFilterState" class="form-select bg-light">
                    <option value="all"><?= $lang === 'te' ? 'అన్ని రాష్ట్రాలు' : 'All States' ?></option>
                    <?php foreach ($states as $st): ?>
                        <option value="<?= $st['id'] ?>" <?= (string)($filters['state_id'] ?? 'all') === (string)$st['id'] ? 'selected' : '' ?>>
                            <?= $lang === 'te' && !empty($st['name_te']) ? e($st['name_te']) : e($st['name_en']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-6 col-md-2">
                <label class="form-label small fw-bold text-primary">
                    <i class="bi bi-map me-1"></i><?= $lang === 'te' ? 'జిల్లా' : 'District' ?>
                </label>
                <select name="district_id" id="adminFilterDistrict" class="form-select bg-light">
                    <option value="all"><?= $lang === 'te' ? 'అన్ని జిల్లాలు' : 'All Districts' ?></option>
                    <?php foreach ($districts as $dst): ?>
                        <option value="<?= $dst['id'] ?>" <?= (string)($filters['district_id'] ?? 'all') === (string)$dst['id'] ? 'selected' : '' ?>>
                            <?= $lang === 'te' && !empty($dst['name_te']) ? e($dst['name_te']) : e($dst['name_en']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-6 col-md-2">
                <label class="form-label small fw-bold text-primary">
                    <i class="bi bi-compass me-1"></i><?= $lang === 'te' ? 'మండలం' : 'Mandal' ?>
                </label>
                <select name="mandal_id" id="adminFilterMandal" class="form-select bg-light">
                    <option value="all"><?= $lang === 'te' ? 'అన్ని మండలాలు' : 'All Mandals' ?></option>
                    <?php foreach ($mandals as $mnd): ?>
                        <option value="<?= $mnd['id'] ?>" <?= (string)($filters['mandal_id'] ?? 'all') === (string)$mnd['id'] ? 'selected' : '' ?>>
                            <?= $lang === 'te' && !empty($mnd['name_te']) ? e($mnd['name_te']) : e($mnd['name_en']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-6 col-md-2">
                <label class="form-label small fw-bold text-primary">
                    <i class="bi bi-building me-1"></i><?= $lang === 'te' ? 'గ్రామ పంచాయతీ' : 'Gram Panchayat' ?>
                </label>
                <select name="gram_panchayat_id" id="adminFilterPanchayat" class="form-select bg-light">
                    <option value="all"><?= $lang === 'te' ? 'అన్ని పంచాయతీలు' : 'All Panchayats' ?></option>
                    <?php foreach ($panchayats as $gp): ?>
                        <option value="<?= $gp['id'] ?>" <?= (string)($filters['gram_panchayat_id'] ?? 'all') === (string)$gp['id'] ? 'selected' : '' ?>>
                            <?= $lang === 'te' && !empty($gp['name_te']) ? e($gp['name_te']) : e($gp['name_en']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-6 col-md-2">
                <label class="form-label small fw-bold text-primary">
                    <i class="bi bi-houses me-1"></i><?= $lang === 'te' ? 'గ్రామం' : 'Village' ?>
                </label>
                <select name="village_id" id="adminFilterVillage" class="form-select bg-light">
                    <option value="all"><?= $lang === 'te' ? 'అన్ని గ్రామాలు' : 'All Villages' ?></option>
                    <?php foreach ($villages as $vlg): ?>
                        <option value="<?= $vlg['id'] ?>" <?= (string)($filters['village_id'] ?? 'all') === (string)$vlg['id'] ? 'selected' : '' ?>>
                            <?= $lang === 'te' && !empty($vlg['name_te']) ? e($vlg['name_te']) : e($vlg['name_en']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-6 col-md-2">
                <label class="form-label small fw-bold text-primary">
                    <i class="bi bi-diagram-3 me-1"></i><?= __('dashboard.col_ward') ?>
                </label>
                <select name="ward_id" id="adminFilterWard" class="form-select bg-light">
                    <option value="all"><?= __('dashboard.filter_all_wards') ?></option>
                    <?php foreach ($wards as $w): ?>
                        <option value="<?= $w['id'] ?>" <?= (string)$filters['ward_id'] === (string)$w['id'] ? 'selected' : '' ?>>
                            <?= e($w['ward_number']) ?> - <?= e($w['ward_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Row 3: Date Filters & Action Buttons -->
            <div class="col-6 col-md-3">
                <label class="form-label small fw-bold text-muted"><?= $lang === 'te' ? 'తేదీ నుండి' : 'From Date' ?></label>
                <input type="date" name="date_from" class="form-control bg-light" value="<?= e($filters['date_from'] ?? '') ?>">
            </div>

            <div class="col-6 col-md-3">
                <label class="form-label small fw-bold text-muted"><?= $lang === 'te' ? 'తేదీ వరకు' : 'To Date' ?></label>
                <input type="date" name="date_to" class="form-control bg-light" value="<?= e($filters['date_to'] ?? '') ?>">
            </div>

            <div class="col-12 col-md-6 d-flex align-items-end justify-content-end gap-2 pt-2">
                <a href="<?= url('admin') ?>" class="btn btn-light rounded-pill px-4"><?= __('dashboard.btn_reset') ?></a>
                <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">
                    <i class="bi bi-filter me-1"></i><?= __('dashboard.btn_filter') ?>
                </button>
            </div>
        </form>
    </div>

    <!-- 6. Master Grievances Table -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-white">
        <div class="card-header bg-white py-3 px-4 border-0 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold mb-0"><i class="bi bi-card-list text-primary me-2"></i><?= __('dashboard.section_master_table') ?></h5>
                <span class="text-muted small"><?= $totalGrievances ?> <?= $lang === 'te' ? 'ఫిర్యాదులు కనుగొనబడ్డాయి' : 'matching records' ?></span>
            </div>
            <span class="badge bg-light text-secondary border">Page <?= $currentPage ?> of <?= $totalPages ?></span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4"><?= __('dashboard.col_id') ?></th>
                        <th><?= __('dashboard.col_citizen') ?></th>
                        <th><?= $lang === 'te' ? 'ప్రాంతం & వార్డు' : 'Location & Ward' ?></th>
                        <th><?= __('dashboard.col_category') ?></th>
                        <th><?= __('dashboard.col_priority') ?></th>
                        <th><?= __('dashboard.col_status') ?></th>
                        <th><?= __('dashboard.col_assigned') ?></th>
                        <th><?= __('dashboard.col_sla') ?></th>
                        <th class="text-end pe-4"><?= __('dashboard.col_action') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($grievances)): ?>
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2 text-muted"></i>
                                <?= __('dashboard.no_records_found') ?>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($grievances as $g): ?>
                            <?php $slaBadge = Grievance::getSlaBadge($g['sla_due_at'], $g['status']); ?>
                            <tr>
                                <td class="ps-4">
                                    <span class="fw-bold text-primary font-monospace"><?= e($g['tracking_id']) ?></span>
                                    <div class="text-muted" style="font-size: 0.75rem;"><?= date('d M Y', strtotime($g['created_at'])) ?></div>
                                </td>
                                <td>
                                    <div class="fw-semibold small"><?= e($g['citizen_name']) ?></div>
                                    <div class="text-muted small"><i class="bi bi-telephone me-1"></i><?= e($g['citizen_phone']) ?></div>
                                </td>
                                <td>
                                    <div class="fw-semibold small text-dark">
                                        <?= $lang === 'te' ? e($g['village_name_te'] ?? $g['village_name_en']) : e($g['village_name_en'] ?? '—') ?>
                                    </div>
                                    <div class="text-muted" style="font-size: 0.72rem;">
                                        <?= e($g['mandal_name_en'] ?? '') ?><?= !empty($g['district_name_en']) ? ', ' . e($g['district_name_en']) : '' ?>
                                    </div>
                                    <?php if (!empty($g['ward_number'])): ?>
                                        <span class="badge bg-light text-dark border mt-1" style="font-size: 0.7rem;">
                                            <?= $lang === 'te' ? 'వార్డు' : 'Ward' ?> <?= e($g['ward_number']) ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="small fw-semibold">
                                        <?= $lang === 'te' ? e($g['category_name_te']) : e($g['category_name_en']) ?>
                                    </span>
                                    <div class="text-muted" style="font-size: 0.72rem;"><?= e($g['dept_name_en']) ?></div>
                                </td>
                                <td>
                                    <span class="badge <?= Grievance::getPriorityBadgeClass($g['priority']) ?> rounded-pill px-2 py-1">
                                        <?= strtoupper($g['priority']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge <?= Grievance::getStatusBadgeClass($g['status']) ?> px-2 py-1 rounded-pill small">
                                        <?= strtoupper(str_replace('_', ' ', $g['status'])) ?>
                                    </span>
                                </td>
                                <td class="small">
                                    <?= $g['assigned_officer_name'] ? e($g['assigned_officer_name']) : '<span class="text-muted">Unassigned</span>' ?>
                                </td>
                                <td>
                                    <span class="badge <?= $slaBadge['class'] ?> rounded-pill px-2 py-1">
                                        <i class="bi <?= $slaBadge['icon'] ?> me-1"></i><?= $slaBadge['text'] ?>
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="<?= url('grievance/view?id=' . $g['id']) ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm">
                                        <?= __('dashboard.action_view') ?> <i class="bi bi-arrow-right"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination Controls -->
        <?php if ($totalPages > 1): ?>
        <div class="card-footer bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center no-print">
            <span class="text-muted small">Showing page <?= $currentPage ?> of <?= $totalPages ?></span>
            <ul class="pagination pagination-sm mb-0">
                <?php if ($currentPage > 1): ?>
                    <li class="page-item">
                        <a class="page-link rounded-pill px-3 me-1" href="?<?= http_build_query(array_merge($filters, ['page' => $currentPage - 1])) ?>">Previous</a>
                    </li>
                <?php endif; ?>
                
                <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                    <li class="page-item <?= $p === $currentPage ? 'active' : '' ?>">
                        <a class="page-link rounded-pill px-3 me-1" href="?<?= http_build_query(array_merge($filters, ['page' => $p])) ?>"><?= $p ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($currentPage < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link rounded-pill px-3" href="?<?= http_build_query(array_merge($filters, ['page' => $currentPage + 1])) ?>">Next</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
        <?php endif; ?>
    </div>

    <!-- 7. Security Audit Trail Section -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-white no-print">
        <div class="card-header bg-white py-3 px-4 border-0 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0 text-muted"><i class="bi bi-shield-check me-2"></i>System Security & Audit Trail (Last 6 Events)</h6>
            <span class="badge bg-light text-muted border">Live Trail</span>
        </div>
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0 font-monospace" style="font-size: 0.8rem;">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Timestamp</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Entity</th>
                        <th>IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentLogs as $log): ?>
                        <tr>
                            <td class="ps-4 text-muted"><?= e($log['created_at']) ?></td>
                            <td><?= e($log['full_name'] ?: 'System') ?> (<?= e($log['role'] ?: 'anon') ?>)</td>
                            <td><span class="badge bg-secondary-subtle text-secondary"><?= e($log['action']) ?></span></td>
                            <td><?= e($log['entity']) ?> #<?= e($log['entity_id'] ?? '') ?></td>
                            <td class="text-muted"><?= e($log['ip_address']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <!-- 8. n8n Automation & Webhook Live Engine Panel (Phase 8) -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-white no-print" id="webhook-panel">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div class="d-flex align-items-center gap-2">
                <span class="p-2 rounded-3 bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                    <i class="bi bi-broadcast-pin fs-5"></i>
                </span>
                <div>
                    <h5 class="fw-bold mb-0 text-dark"><?= __('webhook.panel_title') ?></h5>
                    <div class="d-flex align-items-center gap-2 mt-1">
                        <?php if ($webhookConfig['enabled'] && !empty($webhookConfig['url'])): ?>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill" style="font-size: 0.72rem;">
                                <i class="bi bi-check-circle-fill me-1"></i><?= __('webhook.badge_status_active') ?>
                            </span>
                        <?php elseif ($webhookConfig['enabled']): ?>
                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1 rounded-pill" style="font-size: 0.72rem;">
                                <i class="bi bi-info-circle-fill me-1"></i><?= __('webhook.badge_status_offline') ?>
                            </span>
                        <?php else: ?>
                            <span class="badge bg-secondary-subtle text-secondary border px-2 py-1 rounded-pill" style="font-size: 0.72rem;">
                                <?= __('webhook.badge_status_disabled') ?>
                            </span>
                        <?php endif; ?>
                        <span class="text-muted small">Target: <code class="text-primary"><?= e($webhookConfig['url'] ?: 'http://localhost:5678/webhook/smartgram (simulated)') ?></code></span>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-wrap align-items-center gap-2">
                <form method="POST" action="<?= url('admin/webhook/test') ?>" class="d-inline">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm d-flex align-items-center gap-1">
                        <i class="bi bi-send-fill"></i> <?= __('webhook.btn_test_ping') ?>
                    </button>
                </form>

                <form method="POST" action="<?= url('admin/webhook/sla-scan') ?>" class="d-inline">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3 shadow-sm d-flex align-items-center gap-1">
                        <i class="bi bi-alarm-fill"></i> <?= __('webhook.btn_scan_sla') ?>
                    </button>
                </form>
            </div>
        </div>

        <div class="p-4">
            <!-- Metric Counters -->
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="p-3 bg-light rounded-3 border text-center">
                        <span class="text-muted small d-block mb-1"><?= __('webhook.total_dispatches') ?></span>
                        <div class="fs-4 fw-bold text-dark font-monospace"><?= $webhookStats['total'] ?></div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="p-3 bg-success-subtle rounded-3 border border-success-subtle text-center">
                        <span class="text-success small d-block mb-1"><?= __('webhook.delivered') ?></span>
                        <div class="fs-4 fw-bold text-success font-monospace"><?= $webhookStats['delivered'] ?></div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="p-3 bg-warning-subtle rounded-3 border border-warning-subtle text-center">
                        <span class="text-warning-emphasis small d-block mb-1"><?= __('webhook.simulated') ?></span>
                        <div class="fs-4 fw-bold text-warning-emphasis font-monospace"><?= $webhookStats['simulated'] ?></div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="p-3 bg-danger-subtle rounded-3 border border-danger-subtle text-center">
                        <span class="text-danger small d-block mb-1"><?= __('webhook.failed') ?></span>
                        <div class="fs-4 fw-bold text-danger font-monospace"><?= $webhookStats['failed'] ?></div>
                    </div>
                </div>
            </div>

            <!-- Recent Webhook Logs Table -->
            <div class="table-responsive border rounded-3 overflow-hidden">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3"><?= __('webhook.col_event') ?></th>
                            <th><?= __('webhook.col_entity') ?></th>
                            <th><?= __('webhook.col_http_status') ?></th>
                            <th><?= __('webhook.col_status') ?></th>
                            <th><?= __('webhook.col_time') ?></th>
                            <th class="pe-3"><?= __('webhook.col_created') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($webhookLogs)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="bi bi-clock-history fs-3 d-block mb-1"></i>
                                    <?= __('webhook.no_logs_found') ?>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($webhookLogs as $wl): ?>
                                <tr>
                                    <td class="ps-3">
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace px-2 py-1">
                                            <?= e($wl['event_name']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-muted"><?= e($wl['entity_type']) ?></span>
                                        <?php if (!empty($wl['entity_id'])): ?>
                                            <a href="<?= url('grievance/view?id=' . $wl['entity_id']) ?>" class="fw-bold font-monospace text-decoration-none">
                                                #<?= $wl['entity_id'] ?>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($wl['http_status']): ?>
                                            <span class="badge <?= $wl['http_status'] >= 200 && $wl['http_status'] < 300 ? 'bg-success' : 'bg-danger' ?> font-monospace">
                                                <?= $wl['http_status'] ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary font-monospace">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge <?= $wl['status'] === 'success' ? 'bg-success' : ($wl['status'] === 'simulated' ? 'bg-warning text-dark' : 'bg-danger') ?> rounded-pill px-2 py-1">
                                            <?= strtoupper($wl['status']) ?>
                                        </span>
                                    </td>
                                    <td class="font-monospace text-muted"><?= $wl['execution_time_ms'] ?> ms</td>
                                    <td class="pe-3 text-muted"><?= date('d M Y, h:i:s A', strtotime($wl['created_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-3 p-3 bg-light rounded-3 d-flex align-items-center justify-content-between">
                <span class="small text-muted d-flex align-items-center gap-1">
                    <i class="bi bi-file-earmark-code text-primary"></i> Pre-built n8n Workflow Blueprint available: <code>docs/n8n_smartgram_workflow.json</code>
                </span>
                <span class="badge bg-white text-muted border">HMAC SHA-256 Secured</span>
            </div>
        </div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const isTe = <?= $lang === 'te' ? 'true' : 'false' ?>;
    const stateEl = document.getElementById('adminFilterState');
    const districtEl = document.getElementById('adminFilterDistrict');
    const mandalEl = document.getElementById('adminFilterMandal');
    const panchayatEl = document.getElementById('adminFilterPanchayat');
    const villageEl = document.getElementById('adminFilterVillage');
    const wardEl = document.getElementById('adminFilterWard');

    function resetDropdown(el, defaultText) {
        if (!el) return;
        el.innerHTML = `<option value="all">${defaultText}</option>`;
    }

    if (stateEl) {
        stateEl.addEventListener('change', function () {
            const stateId = this.value;
            resetDropdown(districtEl, isTe ? 'అన్ని జిల్లాలు' : 'All Districts');
            resetDropdown(mandalEl, isTe ? 'అన్ని మండలాలు' : 'All Mandals');
            resetDropdown(panchayatEl, isTe ? 'అన్ని పంచాయతీలు' : 'All Panchayats');
            resetDropdown(villageEl, isTe ? 'అన్ని గ్రామాలు' : 'All Villages');
            resetDropdown(wardEl, isTe ? 'అన్ని వార్డులు' : 'All Wards');

            if (stateId && stateId !== 'all') {
                fetch(`<?= url('api/districts') ?>?state_id=${stateId}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.success && data.districts) {
                            data.districts.forEach(d => {
                                const name = isTe && d.name_te ? d.name_te : d.name_en;
                                districtEl.innerHTML += `<option value="${d.id}">${name}</option>`;
                            });
                        }
                    });
            }
        });
    }

    if (districtEl) {
        districtEl.addEventListener('change', function () {
            const districtId = this.value;
            resetDropdown(mandalEl, isTe ? 'అన్ని మండలాలు' : 'All Mandals');
            resetDropdown(panchayatEl, isTe ? 'అన్ని పంచాయతీలు' : 'All Panchayats');
            resetDropdown(villageEl, isTe ? 'అన్ని గ్రామాలు' : 'All Villages');
            resetDropdown(wardEl, isTe ? 'అన్ని వార్డులు' : 'All Wards');

            if (districtId && districtId !== 'all') {
                fetch(`<?= url('api/mandals') ?>?district_id=${districtId}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.success && data.mandals) {
                            data.mandals.forEach(m => {
                                const name = isTe && m.name_te ? m.name_te : m.name_en;
                                mandalEl.innerHTML += `<option value="${m.id}">${name}</option>`;
                            });
                        }
                    });
            }
        });
    }

    if (mandalEl) {
        mandalEl.addEventListener('change', function () {
            const mandalId = this.value;
            resetDropdown(panchayatEl, isTe ? 'అన్ని పంచాయతీలు' : 'All Panchayats');
            resetDropdown(villageEl, isTe ? 'అన్ని గ్రామాలు' : 'All Villages');
            resetDropdown(wardEl, isTe ? 'అన్ని వార్డులు' : 'All Wards');

            if (mandalId && mandalId !== 'all') {
                fetch(`<?= url('api/panchayats') ?>?mandal_id=${mandalId}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.success && data.panchayats) {
                            data.panchayats.forEach(p => {
                                const name = isTe && p.name_te ? p.name_te : p.name_en;
                                panchayatEl.innerHTML += `<option value="${p.id}">${name}</option>`;
                            });
                        }
                    });
            }
        });
    }

    if (panchayatEl) {
        panchayatEl.addEventListener('change', function () {
            const panchayatId = this.value;
            resetDropdown(villageEl, isTe ? 'అన్ని గ్రామాలు' : 'All Villages');
            resetDropdown(wardEl, isTe ? 'అన్ని వార్డులు' : 'All Wards');

            if (panchayatId && panchayatId !== 'all') {
                fetch(`<?= url('api/villages') ?>?panchayat_id=${panchayatId}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.success && data.villages) {
                            data.villages.forEach(v => {
                                const name = isTe && v.name_te ? v.name_te : v.name_en;
                                villageEl.innerHTML += `<option value="${v.id}">${name}</option>`;
                            });
                        }
                    });
            }
        });
    }

    if (villageEl) {
        villageEl.addEventListener('change', function () {
            const villageId = this.value;
            resetDropdown(wardEl, isTe ? 'అన్ని వార్డులు' : 'All Wards');

            if (villageId && villageId !== 'all') {
                fetch(`<?= url('api/wards') ?>?village_id=${villageId}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.success && data.wards) {
                            data.wards.forEach(w => {
                                wardEl.innerHTML += `<option value="${w.id}">${w.ward_number} - ${w.ward_name}</option>`;
                            });
                        }
                    });
            }
        });
    }
});
</script>

<?php view('layouts/footer'); ?>
