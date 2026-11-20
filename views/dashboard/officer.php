<?php 
use App\Models\Grievance;
view('layouts/header', ['title' => 'Officer Field Workbench - Smart Gram']); 
$lang = current_lang();
?>

<div class="container py-4">
    <!-- Header Greeting -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 pb-3 mb-4 border-bottom">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-warning-subtle text-dark border border-warning-subtle px-3 py-1 rounded-pill fw-bold text-uppercase">
                    <i class="bi bi-person-workspace me-1"></i> Field Officer Portal
                </span>
                <span class="text-muted small">| Assigned Grievances Workbench (క్షేత్రస్థాయి అధికారి డెస్క్)</span>
            </div>
            <h2 class="fw-bold mb-1">Assigned Grievances Workbench</h2>
            <p class="text-muted small mb-0">
                <?= $lang === 'te' ? 'అధికారి:' : 'Officer:' ?> <strong><?= e($user['full_name']) ?></strong> | 
                <?= $lang === 'te' ? 'ఫోన్:' : 'Phone:' ?> <?= e($user['phone']) ?> |
                <?= $lang === 'te' ? 'అధికార పరిధి:' : 'Jurisdiction:' ?> <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-semibold"><?= e($scopeBadge ?? \App\Models\User::getScopeBadge($user, $lang)) ?></span>
            </p>
        </div>

        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-outline-dark btn-sm rounded-pill px-3 shadow-sm d-flex align-items-center gap-1">
                <i class="bi bi-printer-fill"></i> <?= __('dashboard.btn_print') ?>
            </button>
        </div>
    </div>

    <!-- Officer KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-bold text-uppercase"><?= __('dashboard.kpi_total') ?></span>
                    <div class="bg-primary-subtle text-primary p-2 rounded-3"><i class="bi bi-briefcase-fill fs-5"></i></div>
                </div>
                <div class="display-6 fw-bold text-dark"><?= $stats['assigned'] + $stats['in_progress'] + $stats['resolved'] ?></div>
                <div class="text-muted small mt-2"><?= $lang === 'te' ? 'మొత్తం కేటాయించినవి' : 'Total Assigned' ?></div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-bold text-uppercase"><?= __('dashboard.kpi_in_progress') ?></span>
                    <div class="bg-warning-subtle text-warning-emphasis p-2 rounded-3"><i class="bi bi-tools fs-5"></i></div>
                </div>
                <div class="display-6 fw-bold text-warning-emphasis"><?= $stats['in_progress'] ?></div>
                <div class="text-muted small mt-2"><?= $lang === 'te' ? 'పని జరుగుతోంది' : 'Active On-Site' ?></div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-bold text-uppercase"><?= __('dashboard.kpi_resolved') ?></span>
                    <div class="bg-success-subtle text-success p-2 rounded-3"><i class="bi bi-patch-check-fill fs-5"></i></div>
                </div>
                <div class="display-6 fw-bold text-success"><?= $stats['resolved'] ?></div>
                <div class="text-muted small mt-2"><?= $lang === 'te' ? 'పరిష్కరించినవి' : 'Completed' ?></div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-bold text-uppercase"><?= __('dashboard.kpi_overdue') ?></span>
                    <div class="bg-danger text-white p-2 rounded-3"><i class="bi bi-exclamation-octagon-fill fs-5"></i></div>
                </div>
                <div class="display-6 fw-bold text-danger"><?= $stats['overdue'] ?></div>
                <div class="text-danger small mt-2 fw-semibold"><?= $lang === 'te' ? 'గడువు దాటినవి (SLA)' : 'Overdue SLA' ?></div>
            </div>
        </div>
    </div>

    <!-- Assigned Grievances List -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-white">
        <div class="card-header bg-white py-3 px-4 border-0 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h5 class="fw-bold mb-0"><i class="bi bi-clipboard2-check text-primary me-2"></i><?= $lang === 'te' ? 'నాకు అప్పగించబడిన సమస్యల జాబితా' : 'My Field Tasks & Assigned Grievances' ?></h5>
                <span class="text-muted small"><?= $totalGrievances ?> <?= $lang === 'te' ? 'సమస్యలు కేటాయించబడ్డాయి' : 'assigned records' ?></span>
            </div>

            <form method="GET" action="<?= url('officer') ?>" class="d-flex gap-2">
                <input type="text" name="search" class="form-control form-control-sm rounded-pill px-3" placeholder="<?= __('dashboard.search_placeholder') ?>" value="<?= e($filters['search']) ?>" style="min-width: 200px;">
                <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3"><?= __('dashboard.btn_filter') ?></button>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4"><?= __('dashboard.col_id') ?></th>
                        <th><?= __('dashboard.col_citizen') ?></th>
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
                        <tr><td colspan="8" class="text-center py-4 text-muted"><?= __('dashboard.no_records_found') ?></td></tr>
                    <?php else: ?>
                        <?php foreach ($grievances as $g): ?>
                            <?php $slaBadge = Grievance::getSlaBadge($g['sla_due_at'], $g['status']); ?>
                            <tr>
                                <td class="ps-4">
                                    <span class="fw-bold text-primary font-monospace"><?= e($g['tracking_id']) ?></span>
                                    <div class="text-muted" style="font-size: 0.72rem;"><?= date('d M Y', strtotime($g['created_at'])) ?></div>
                                </td>
                                <td>
                                    <div class="fw-semibold small"><?= e($g['citizen_name']) ?></div>
                                    <div class="text-muted small"><i class="bi bi-telephone me-1"></i><?= e($g['citizen_phone']) ?></div>
                                </td>
                                <td><span class="badge bg-light text-dark border"><?= e($g['ward_number'] ?? '—') ?></span></td>
                                <td>
                                    <span class="small fw-semibold"><?= $lang === 'te' ? e($g['category_name_te']) : e($g['category_name_en']) ?></span>
                                    <?php if (!empty($g['landmark'])): ?>
                                        <div class="text-muted small" style="font-size: 0.72rem;"><i class="bi bi-geo-alt me-1"></i><?= e($g['landmark']) ?></div>
                                    <?php endif; ?>
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
