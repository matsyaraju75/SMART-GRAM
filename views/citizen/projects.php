<?php view('layouts/header', ['title' => $title ?? 'Village Projects & Gram Sabha — Smart Gram']); ?>
<?php $activeLang = current_lang(); ?>

<!-- ═══════════════════════════════════════════════════════════════════════════
     SECTION A: Village Development Capital & Expenditure
     ═══════════════════════════════════════════════════════════════════════════ -->
<section class="py-5" style="background: linear-gradient(135deg, #1b4332 0%, #2d6a4f 50%, #40916c 100%);">
    <div class="container">
        <div class="text-center mb-4">
            <span class="badge bg-warning text-dark rounded-pill px-3 py-2 mb-3 fs-7 fw-semibold">
                <i class="bi bi-bank2 me-1"></i>
                Village Development Capital & Expenditure
            </span>
            <h2 class="text-white fw-bold mb-1">Gram Panchayat Financial Overview</h2>
            <p class="text-white-50 mb-0">FY 2026-27 — All amounts under 15th Finance Commission & SFC Grants</p>
        </div>

        <div class="row g-4 justify-content-center">
            <!-- Total Budget Allocated -->
            <div class="col-md-4">
                <div class="card border-0 rounded-4 shadow-lg h-100 overflow-hidden" style="background: linear-gradient(135deg, #198754 0%, #20c997 100%);">
                    <div class="card-body p-4 text-white text-center">
                        <div class="d-flex justify-content-center mb-3">
                            <div class="rounded-circle bg-white bg-opacity-25 d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                                <i class="bi bi-wallet2 fs-2 text-white"></i>
                            </div>
                        </div>
                        <p class="text-white-50 small text-uppercase fw-semibold mb-1 letter-spacing-1">Total Budget Allocated</p>
                        <h2 class="display-5 fw-bold mb-0"><?= e($budget['total_allocated_label']) ?></h2>
                        <p class="text-white-50 small mt-1 mb-0">15th FC + SFC + Own Revenue</p>
                    </div>
                </div>
            </div>

            <!-- Amount Utilized -->
            <div class="col-md-4">
                <div class="card border-0 rounded-4 shadow-lg h-100 overflow-hidden" style="background: linear-gradient(135deg, #e65100 0%, #ff9800 100%);">
                    <div class="card-body p-4 text-white text-center">
                        <div class="d-flex justify-content-center mb-3">
                            <div class="rounded-circle bg-white bg-opacity-25 d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                                <i class="bi bi-cash-stack fs-2 text-white"></i>
                            </div>
                        </div>
                        <p class="text-white-50 small text-uppercase fw-semibold mb-1 letter-spacing-1">Amount Utilized</p>
                        <h2 class="display-5 fw-bold mb-0"><?= e($budget['amount_utilized_label']) ?></h2>
                        <div class="progress mt-3 bg-white bg-opacity-25 rounded-pill" style="height: 10px;">
                            <div class="progress-bar bg-white rounded-pill" role="progressbar" style="width: <?= (int)$budget['utilization_pct'] ?>%;" aria-valuenow="<?= (int)$budget['utilization_pct'] ?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <p class="text-white-50 small mt-1 mb-0"><?= (int)$budget['utilization_pct'] ?>% of allocated budget utilized</p>
                    </div>
                </div>
            </div>

            <!-- Balance Available -->
            <div class="col-md-4">
                <div class="card border-0 rounded-4 shadow-lg h-100 overflow-hidden" style="background: linear-gradient(135deg, #0d47a1 0%, #42a5f5 100%);">
                    <div class="card-body p-4 text-white text-center">
                        <div class="d-flex justify-content-center mb-3">
                            <div class="rounded-circle bg-white bg-opacity-25 d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                                <i class="bi bi-piggy-bank fs-2 text-white"></i>
                            </div>
                        </div>
                        <p class="text-white-50 small text-uppercase fw-semibold mb-1 letter-spacing-1">Balance Available</p>
                        <h2 class="display-5 fw-bold mb-0"><?= e($budget['balance_available_label']) ?></h2>
                        <p class="text-white-50 small mt-1 mb-0">Remaining for ongoing & upcoming works</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════════════════════
     SECTION B: Interactive Ward-Wise Project Tracker
     ═══════════════════════════════════════════════════════════════════════════ -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="text-center mb-5">
            <span class="badge bg-success-subtle text-success rounded-pill px-3 py-2 mb-3 fs-7 fw-semibold">
                <i class="bi bi-kanban me-1"></i>
                Ward-Wise Project Tracker
            </span>
            <h2 class="fw-bold text-dark">Village Development Projects</h2>
            <p class="text-muted mb-0">Track all ongoing, completed, and upcoming projects across all wards</p>
        </div>

        <!-- Filter Pills -->
        <div class="d-flex flex-wrap gap-2 justify-content-center mb-4">
            <button class="btn btn-success rounded-pill px-3 py-2 fw-semibold btn-sm active" data-filter="all">
                <i class="bi bi-grid-3x3-gap me-1"></i> All Projects
            </button>
            <button class="btn btn-outline-success rounded-pill px-3 py-2 fw-semibold btn-sm" data-filter="Completed">
                <i class="bi bi-check-circle me-1"></i> Completed
            </button>
            <button class="btn btn-outline-warning rounded-pill px-3 py-2 fw-semibold btn-sm" data-filter="In Progress">
                <i class="bi bi-arrow-repeat me-1"></i> In Progress
            </button>
            <button class="btn btn-outline-info rounded-pill px-3 py-2 fw-semibold btn-sm" data-filter="Upcoming">
                <i class="bi bi-clock me-1"></i> Upcoming
            </button>
        </div>

        <div class="row g-4" id="projectsGrid">
            <?php foreach ($projects as $project): ?>
            <?php
                $statusClass = match($project['status']) {
                    'Completed'   => 'bg-success',
                    'In Progress' => 'bg-warning text-dark',
                    'Upcoming'    => 'bg-info',
                    default       => 'bg-secondary',
                };
                $progressColor = match(true) {
                    $project['progress'] >= 80 => 'bg-success',
                    $project['progress'] >= 40 => 'bg-warning',
                    default                    => 'bg-info',
                };
            ?>
            <div class="col-sm-6 col-lg-4 col-xl-3 project-item" data-status="<?= e($project['status']) ?>">
                <div class="card border-0 rounded-4 shadow-sm h-100 overflow-hidden project-card">
                    <div class="position-relative">
                        <img src="<?= e($project['image']) ?>" class="card-img-top" alt="<?= e($project['name']) ?>" style="height: 180px; object-fit: cover;">
                        <span class="badge <?= $statusClass ?> position-absolute top-0 end-0 m-2 rounded-pill px-3 py-2 shadow-sm">
                            <?= e($project['status']) ?>
                        </span>
                        <span class="badge bg-dark bg-opacity-75 position-absolute bottom-0 start-0 m-2 rounded-pill px-2 py-1">
                            <i class="bi bi-geo-alt-fill text-warning me-1"></i>Ward <?= (int)$project['ward'] ?>
                        </span>
                    </div>
                    <div class="card-body p-3">
                        <h6 class="fw-bold text-dark mb-2"><?= e($project['name']) ?></h6>

                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-person-badge text-muted"></i>
                            <small class="text-muted"><?= e($project['contractor']) ?></small>
                        </div>

                        <div class="d-flex align-items-center gap-2 mb-3">
                            <i class="bi bi-currency-rupee text-success"></i>
                            <span class="fw-bold text-success"><?= e($project['budget']) ?></span>
                        </div>

                        <!-- Progress Bar -->
                        <div class="mb-2">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <small class="text-muted fw-semibold">Progress</small>
                                <small class="fw-bold"><?= (int)$project['progress'] ?>%</small>
                            </div>
                            <div class="progress rounded-pill" style="height: 8px;">
                                <div class="progress-bar <?= $progressColor ?> rounded-pill" role="progressbar" style="width: <?= (int)$project['progress'] ?>%;" aria-valuenow="<?= (int)$project['progress'] ?>" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>

                        <!-- Dates -->
                        <div class="d-flex justify-content-between border-top pt-2 mt-2">
                            <div>
                                <small class="text-muted d-block">Start</small>
                                <small class="fw-semibold"><?= date('d M Y', strtotime($project['start_date'])) ?></small>
                            </div>
                            <div class="text-end">
                                <small class="text-muted d-block">Expected End</small>
                                <small class="fw-semibold"><?= date('d M Y', strtotime($project['end_date'])) ?></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════════════════════
     SECTION C: General Gram Sabha Meeting Notifications
     ═══════════════════════════════════════════════════════════════════════════ -->
<section class="py-5" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
    <div class="container">
        <div class="text-center mb-5">
            <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2 mb-3 fs-7 fw-semibold">
                <i class="bi bi-megaphone me-1"></i>
                Gram Sabha Notifications
            </span>
            <h2 class="fw-bold text-dark">General Gram Sabha Meetings</h2>
            <p class="text-muted mb-0">Stay informed about upcoming and past Gram Sabha proceedings</p>
        </div>

        <div class="row g-4">
            <!-- Upcoming Meeting Card -->
            <div class="col-lg-6">
                <div class="card border-0 rounded-4 shadow-sm h-100 overflow-hidden">
                    <div class="card-header py-3 px-4 border-0 text-white" style="background: linear-gradient(135deg, #198754 0%, #20c997 100%);">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-calendar-event fs-4"></i>
                            <div>
                                <h5 class="fw-bold mb-0">Next Gram Sabha</h5>
                                <small class="text-white-50">Upcoming Meeting Details</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start gap-3 mb-4">
                            <div class="text-center bg-success bg-opacity-10 rounded-4 p-3" style="min-width: 80px;">
                                <span class="display-6 fw-bold text-success d-block"><?= date('d', strtotime($meetings['next']['date'])) ?></span>
                                <small class="text-success fw-semibold"><?= date('M Y', strtotime($meetings['next']['date'])) ?></small>
                            </div>
                            <div>
                                <div class="mb-2">
                                    <span class="badge bg-success rounded-pill px-2 py-1 me-1">
                                        <i class="bi bi-clock me-1"></i><?= e($meetings['next']['time']) ?>
                                    </span>
                                </div>
                                <p class="text-muted mb-0">
                                    <i class="bi bi-geo-alt-fill text-danger me-1"></i>
                                    <?= e($meetings['next']['venue']) ?>
                                </p>
                            </div>
                        </div>

                        <h6 class="fw-bold text-dark mb-3"><i class="bi bi-list-check text-success me-2"></i>Agenda Items</h6>
                        <ol class="list-group list-group-numbered list-group-flush">
                            <?php foreach ($meetings['next']['agenda'] as $item): ?>
                            <li class="list-group-item border-0 px-0 py-2 bg-transparent">
                                <span class="text-dark"><?= e($item) ?></span>
                            </li>
                            <?php endforeach; ?>
                        </ol>

                        <div class="mt-4 d-grid">
                            <button class="btn btn-success rounded-pill fw-bold py-2 shadow-sm">
                                <i class="bi bi-person-plus-fill me-2"></i>Register to Attend
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Previous Meetings List -->
            <div class="col-lg-6">
                <div class="card border-0 rounded-4 shadow-sm h-100 overflow-hidden">
                    <div class="card-header py-3 px-4 border-0 text-white" style="background: linear-gradient(135deg, #495057 0%, #6c757d 100%);">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-clock-history fs-4"></i>
                            <div>
                                <h5 class="fw-bold mb-0">Previous Meetings</h5>
                                <small class="text-white-50">Minutes & Attendance Records</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <?php foreach ($meetings['previous'] as $i => $prev): ?>
                        <div class="d-flex align-items-start gap-3 <?= $i < count($meetings['previous']) - 1 ? 'mb-4 pb-3 border-bottom' : '' ?>">
                            <div class="text-center bg-light rounded-3 p-2" style="min-width: 56px;">
                                <span class="fw-bold text-dark d-block"><?= date('d', strtotime($prev['date'])) ?></span>
                                <small class="text-muted"><?= date('M', strtotime($prev['date'])) ?></small>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="fw-bold text-dark mb-1"><?= e($prev['title']) ?></h6>
                                <div class="d-flex flex-wrap gap-2 mb-2">
                                    <span class="badge bg-primary-subtle text-primary rounded-pill px-2 py-1">
                                        <i class="bi bi-people-fill me-1"></i><?= (int)$prev['attendance'] ?> Attendees
                                    </span>
                                    <span class="badge bg-success-subtle text-success rounded-pill px-2 py-1">
                                        <i class="bi bi-check2-all me-1"></i><?= (int)$prev['resolutions'] ?> Resolutions
                                    </span>
                                </div>
                                <a href="<?= e($prev['file']) ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                                    <i class="bi bi-file-earmark-pdf me-1"></i>Download Minutes
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════════════════════
     SECTION D: Social Audit & RTI Transparency Disclosures
     ═══════════════════════════════════════════════════════════════════════════ -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="text-center mb-5">
            <span class="badge bg-danger-subtle text-danger rounded-pill px-3 py-2 mb-3 fs-7 fw-semibold">
                <i class="bi bi-shield-check me-1"></i>
                Transparency & Accountability
            </span>
            <h2 class="fw-bold text-dark">Social Audit & RTI Disclosures</h2>
            <p class="text-muted mb-0">Public transparency documents, audit reports, and RTI information</p>
        </div>

        <div class="row g-4">
            <!-- Audit Reports Table -->
            <div class="col-lg-7">
                <div class="card border-0 rounded-4 shadow-sm h-100 overflow-hidden">
                    <div class="card-header py-3 px-4 border-0 d-flex align-items-center gap-2" style="background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%); color: white;">
                        <i class="bi bi-clipboard2-data fs-4"></i>
                        <div>
                            <h5 class="fw-bold mb-0">Latest Audit Reports</h5>
                            <small class="text-white-50">Social audits & financial reviews</small>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4 py-3 text-muted small text-uppercase fw-semibold">Date</th>
                                        <th class="py-3 text-muted small text-uppercase fw-semibold">Audit Type</th>
                                        <th class="py-3 text-muted small text-uppercase fw-semibold">Findings</th>
                                        <th class="py-3 text-muted small text-uppercase fw-semibold">Status</th>
                                        <th class="pe-4 py-3 text-muted small text-uppercase fw-semibold text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($audits as $audit): ?>
                                    <?php
                                        $auditStatusClass = $audit['status'] === 'Completed' ? 'bg-success' : 'bg-warning text-dark';
                                    ?>
                                    <tr>
                                        <td class="ps-4 py-3">
                                            <small class="fw-semibold"><?= date('d M Y', strtotime($audit['date'])) ?></small>
                                        </td>
                                        <td class="py-3">
                                            <span class="fw-semibold text-dark"><?= e($audit['type']) ?></span>
                                        </td>
                                        <td class="py-3">
                                            <small class="text-muted"><?= e($audit['findings']) ?></small>
                                        </td>
                                        <td class="py-3">
                                            <span class="badge <?= $auditStatusClass ?> rounded-pill px-2 py-1"><?= e($audit['status']) ?></span>
                                        </td>
                                        <td class="pe-4 py-3 text-end">
                                            <a href="<?= e($audit['file']) ?>" class="btn btn-outline-danger btn-sm rounded-pill px-3">
                                                <i class="bi bi-download me-1"></i>View
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RTI & Financial Disclosures -->
            <div class="col-lg-5">
                <!-- RTI Request Card -->
                <div class="card border-0 rounded-4 shadow-sm mb-4 overflow-hidden">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="rounded-circle bg-info bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="bi bi-info-circle-fill text-info fs-4"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0">Right to Information (RTI)</h6>
                                <small class="text-muted">File RTI request under Section 6(1)</small>
                            </div>
                        </div>
                        <p class="text-muted small mb-3">
                            Every citizen has the right to access information from public authorities under the Right to Information Act, 2005.
                        </p>
                        <button class="btn btn-info rounded-pill fw-bold px-4 py-2 w-100 text-white">
                            <i class="bi bi-file-earmark-text me-2"></i>File RTI Request
                        </button>
                    </div>
                </div>

                <!-- Financial Disclosure Documents -->
                <div class="card border-0 rounded-4 shadow-sm overflow-hidden">
                    <div class="card-header py-3 px-4 border-0 bg-light">
                        <h6 class="fw-bold mb-0 text-dark">
                            <i class="bi bi-folder2-open text-primary me-2"></i>Public Disclosure Documents
                        </h6>
                    </div>
                    <div class="list-group list-group-flush">
                        <?php foreach ($disclosures as $doc): ?>
                        <a href="<?= e($doc['file']) ?>" class="list-group-item list-group-item-action d-flex align-items-center gap-3 px-4 py-3 border-0">
                            <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px;">
                                <i class="bi bi-file-earmark-pdf-fill text-danger"></i>
                            </div>
                            <div class="flex-grow-1">
                                <span class="fw-semibold text-dark d-block"><?= e($doc['title']) ?></span>
                                <small class="text-muted"><?= date('d M Y', strtotime($doc['date'])) ?></small>
                            </div>
                            <i class="bi bi-download text-muted"></i>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Public Notices Banner -->
        <div class="mt-5 p-4 rounded-4 shadow-sm border-start border-5 border-warning" style="background: linear-gradient(135deg, #fff9e6 0%, #fff3cd 100%);">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-warning bg-opacity-25 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 56px; height: 56px;">
                    <i class="bi bi-bell-fill text-warning fs-3"></i>
                </div>
                <div>
                    <h6 class="fw-bold text-dark mb-1"><i class="bi bi-megaphone-fill text-warning me-2"></i>Public Notice</h6>
                    <p class="text-muted mb-0">
                        All residents of Ward 3 are hereby notified that road widening work will commence from <strong>25th November 2026</strong>. 
                        Kindly remove temporary structures from the road margins within 15 days. For queries, contact the Gram Panchayat Office.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Project Filter JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterBtns = document.querySelectorAll('[data-filter]');
    const projectItems = document.querySelectorAll('.project-item');

    filterBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            const filter = this.getAttribute('data-filter');

            // Update active button
            filterBtns.forEach(function(b) {
                b.classList.remove('active', 'btn-success', 'btn-warning', 'btn-info');
                b.classList.add('btn-outline-success');
                if (b.getAttribute('data-filter') === 'Completed') b.classList.replace('btn-outline-success', 'btn-outline-success');
                if (b.getAttribute('data-filter') === 'In Progress') b.classList.replace('btn-outline-success', 'btn-outline-warning');
                if (b.getAttribute('data-filter') === 'Upcoming') b.classList.replace('btn-outline-success', 'btn-outline-info');
            });
            this.classList.add('active');
            if (filter === 'all') this.classList.replace('btn-outline-success', 'btn-success');
            if (filter === 'Completed') this.classList.replace('btn-outline-success', 'btn-success');
            if (filter === 'In Progress') this.classList.replace('btn-outline-warning', 'btn-warning');
            if (filter === 'Upcoming') this.classList.replace('btn-outline-info', 'btn-info');

            // Filter projects
            projectItems.forEach(function(item) {
                if (filter === 'all' || item.getAttribute('data-status') === filter) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });
});
</script>

<?php view('layouts/footer'); ?>
