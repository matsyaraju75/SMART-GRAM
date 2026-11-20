<?php 
use App\Models\Grievance;
view('layouts/header', ['title' => 'Staff Dashboard - Smart Gram']); 
$lang = current_lang();
?>

<div class="container py-4">
    <!-- Header Greeting -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 pb-3 mb-4 border-bottom">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill fw-bold text-uppercase">
                    <i class="bi bi-person-badge-fill me-1"></i> Panchayat Staff Portal
                </span>
                <span class="text-muted small">| Staff Verification Queue (పంచాయతీ కార్యదర్శి డెస్క్)</span>
            </div>
            <h2 class="fw-bold mb-1">Staff Verification Queue</h2>
            <p class="text-muted small mb-0">
                <?= $lang === 'te' ? 'లాగిన్ అయిన వారు:' : 'Logged in as:' ?> <strong><?= e($user['full_name']) ?></strong> | 
                <?= $lang === 'te' ? 'అధికార పరిధి:' : 'Jurisdiction:' ?> <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-semibold"><?= e($scopeBadge ?? \App\Models\User::getScopeBadge($user, $lang)) ?></span>
            </p>
        </div>

        <div class="d-flex gap-2">
            <a href="<?= url('dashboard/export?' . http_build_query($filters)) ?>" class="btn btn-outline-success btn-sm rounded-pill px-3 shadow-sm d-flex align-items-center gap-1">
                <i class="bi bi-file-earmark-spreadsheet-fill"></i> <?= __('dashboard.btn_export_csv') ?>
            </a>
            <button onclick="window.print()" class="btn btn-outline-dark btn-sm rounded-pill px-3 shadow-sm d-flex align-items-center gap-1">
                <i class="bi bi-printer-fill"></i> <?= __('dashboard.btn_print') ?>
            </button>
        </div>
    </div>

    <!-- Staff Metrics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-bold text-uppercase"><?= __('dashboard.kpi_total') ?></span>
                    <div class="bg-primary-subtle text-primary p-2 rounded-3"><i class="bi bi-inbox-fill fs-5"></i></div>
                </div>
                <div class="display-6 fw-bold text-dark"><?= $analytics['total'] ?></div>
                <div class="text-muted small mt-2"><?= $lang === 'te' ? 'మొత్తం ఫిర్యాదులు' : 'Village Grievances' ?></div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-bold text-uppercase"><?= __('dashboard.kpi_pending') ?></span>
                    <div class="bg-warning-subtle text-warning-emphasis p-2 rounded-3"><i class="bi bi-hourglass-split fs-5"></i></div>
                </div>
                <div class="display-6 fw-bold text-warning-emphasis"><?= $analytics['pending'] ?></div>
                <div class="text-muted small mt-2"><?= $lang === 'te' ? 'పరిశీలించాల్సినవి' : 'Pending Verification' ?></div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-bold text-uppercase"><?= __('dashboard.kpi_in_progress') ?></span>
                    <div class="bg-info-subtle text-info-emphasis p-2 rounded-3"><i class="bi bi-gear-wide-connected fs-5"></i></div>
                </div>
                <div class="display-6 fw-bold text-info-emphasis"><?= $analytics['in_progress'] + $analytics['assigned'] ?></div>
                <div class="text-muted small mt-2"><?= $lang === 'te' ? 'పరిష్కార ప్రక్రియలో ఉన్నవి' : 'Field Operations Active' ?></div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-bold text-uppercase"><?= __('dashboard.kpi_overdue') ?></span>
                    <div class="bg-danger text-white p-2 rounded-3"><i class="bi bi-clock-history fs-5"></i></div>
                </div>
                <div class="display-6 fw-bold text-danger"><?= $analytics['overdue'] ?></div>
                <div class="text-danger small mt-2 fw-semibold"><?= $lang === 'te' ? 'గడువు దాటినవి (SLA)' : 'Overdue SLA Breaches' ?></div>
            </div>
        </div>
    </div>

    <!-- Ward Breakdown & Overdue Alert -->
    <div class="row g-4 mb-4">
        <!-- Ward Hotspots -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                <h5 class="fw-bold mb-3"><i class="bi bi-geo-alt-fill text-danger me-2"></i><?= __('dashboard.section_wards') ?></h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th><?= __('dashboard.col_ward') ?></th>
                                <th><?= __('dashboard.kpi_total') ?></th>
                                <th><?= $lang === 'te' ? 'శాతం' : 'Distribution' ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($wardBreakdown as $w): ?>
                            <tr>
                                <td>
                                    <span class="fw-bold"><?= e($w['ward_number']) ?></span>
                                    <span class="text-muted small d-block"><?= e($w['ward_name']) ?></span>
                                </td>
                                <td class="fw-bold"><?= $w['count'] ?></td>
                                <td style="width: 40%;">
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar <?= $w['is_hotspot'] ? 'bg-danger' : 'bg-success' ?>" style="width: <?= $w['percentage'] ?>%"></div>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Overdue Alerts -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                <h5 class="fw-bold mb-3 text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i><?= __('dashboard.section_sla_alerts') ?></h5>
                <?php if (empty($overdueAlerts)): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-check2-circle text-success fs-2 d-block mb-1"></i>
                        <?= __('dashboard.no_overdue_grievances') ?>
                    </div>
                <?php else: ?>
                    <div class="d-flex flex-column gap-2">
                        <?php foreach ($overdueAlerts as $od): ?>
                            <div class="p-3 rounded-3 border border-danger-subtle bg-danger-subtle d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold text-danger"><?= e($od['tracking_id']) ?> - <?= e($od['citizen_name']) ?></div>
                                    <div class="text-muted small"><?= $lang === 'te' ? e($od['category_name_te']) : e($od['category_name_en']) ?> (<?= e($od['ward_number']) ?>)</div>
                                </div>
                                <span class="badge bg-danger text-white rounded-pill px-3 py-2 fw-bold">
                                    <?= $od['hours_overdue'] ?> <?= __('dashboard.sla_hours') ?> <?= __('dashboard.sla_overdue_by') ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Grievances Queue & Search Filter -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-white">
        <div class="card-header bg-white py-3 px-4 border-0 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h5 class="fw-bold mb-0"><i class="bi bi-list-task text-primary me-2"></i><?= $lang === 'te' ? 'గ్రామ సమస్యల పరిశీలన జాబితా' : 'Village Grievance Operational Queue' ?></h5>
                <span class="text-muted small"><?= $totalGrievances ?> <?= $lang === 'te' ? 'ఫిర్యాదులు అందుబాటులో ఉన్నాయి' : 'records in queue' ?></span>
            </div>
            
            <form method="GET" action="<?= url('staff') ?>" class="d-flex gap-2">
                <input type="text" name="search" class="form-control form-control-sm rounded-pill px-3" placeholder="<?= __('dashboard.search_placeholder') ?>" value="<?= e($filters['search']) ?>" style="min-width: 220px;">
                <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3"><?= __('dashboard.btn_filter') ?></button>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4"><?= __('dashboard.col_id') ?></th>
                        <th><?= __('dashboard.col_citizen') ?></th>
                        <th><?= $lang === 'te' ? 'గ్రామం' : 'Village' ?></th>
                        <th><?= __('dashboard.col_ward') ?></th>
                        <th><?= __('dashboard.col_category') ?></th>
                        <th><?= __('dashboard.col_priority') ?></th>
                        <th><?= __('dashboard.col_status') ?></th>
                        <th><?= __('dashboard.col_sla') ?></th>
                        <th class="text-end pe-4"><?= __('dashboard.col_action') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($grievances)): ?>
                        <tr><td colspan="9" class="text-center py-4 text-muted"><?= __('dashboard.no_records_found') ?></td></tr>
                    <?php else: ?>
                        <?php foreach ($grievances as $g): ?>
                            <?php $slaBadge = Grievance::getSlaBadge($g['sla_due_at'], $g['status']); ?>
                            <tr>
                                <td class="ps-4">
                                    <span class="fw-bold text-primary font-monospace"><?= e($g['tracking_id']) ?></span>
                                </td>
                                <td>
                                    <div class="fw-semibold small"><?= e($g['citizen_name']) ?></div>
                                    <div class="text-muted small"><?= e($g['citizen_phone']) ?></div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border"><?= e($lang === 'te' ? ($g['village_name_te'] ?? $g['village_name_en']) : ($g['village_name_en'] ?? '—')) ?></span>
                                </td>
                                <td><span class="badge bg-light text-dark border"><?= e($g['ward_number'] ?? '—') ?></span></td>
                                <td>
                                    <span class="small fw-semibold"><?= $lang === 'te' ? e($g['category_name_te']) : e($g['category_name_en']) ?></span>
                                </td>
                                <td>
                                    <span class="badge <?= Grievance::getPriorityBadgeClass($g['priority']) ?> rounded-pill px-2 py-1">
                                        <?= strtoupper($g['priority']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge <?= Grievance::getStatusBadgeClass($g['status']) ?> rounded-pill px-2 py-1 small">
                                        <?= strtoupper(str_replace('_', ' ', $g['status'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge <?= $slaBadge['class'] ?> rounded-pill px-2 py-1">
                                        <i class="bi <?= $slaBadge['icon'] ?> me-1"></i><?= $slaBadge['text'] ?>
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="<?= url('grievance/view?id=' . $g['id']) ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                        <?= __('dashboard.action_view') ?> <i class="bi bi-arrow-right"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php view('layouts/footer'); ?>
