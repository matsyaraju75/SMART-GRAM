<?php
// ==============================================================================
// Smart Gram - Admin Location Management View
// Manages: State -> District -> Mandal -> Gram Panchayat -> Village -> Ward
// ==============================================================================

$lang = current_lang();
view('layouts/header', ['title' => $title]);
?>

<div class="container py-4">
    <!-- Breadcrumb & Top Bar -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="<?= url('admin') ?>" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?= $lang === 'te' ? 'భౌగోళిక ప్రాంతాల నిర్వహణ' : 'Geographic Hierarchy' ?></li>
                </ol>
            </nav>
            <h2 class="fw-bold mb-0 text-dark">
                <i class="bi bi-diagram-3-fill text-success me-2"></i>
                <?= $lang === 'te' ? 'భౌగోళిక పరిపాలనా నిర్మాణం' : 'Administrative Geographic Hierarchy' ?>
            </h2>
            <p class="text-muted small mb-0">
                <?= $lang === 'te' 
                    ? 'రాష్ట్రం, జిల్లా, మండలం, గ్రామ పంచాయతీ, గ్రామం, వార్డుల నిర్వహణ మరియు నియంత్రణ' 
                    : 'Manage States, Districts, Mandals, Gram Panchayats, Villages, and Wards' ?>
            </p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <a href="<?= url('admin') ?>" class="btn btn-outline-secondary rounded-pill px-3 shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> <?= $lang === 'te' ? 'డ్యాష్‌బోర్డ్' : 'Dashboard' ?>
            </a>
            <div class="dropdown">
                <button class="btn btn-success rounded-pill px-3 dropdown-toggle shadow-sm" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-plus-circle me-1"></i> <?= $lang === 'te' ? 'కొత్త ప్రాంతం చేర్చు' : 'Add Location' ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3">
                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addDistrictModal"><i class="bi bi-map me-2 text-primary"></i><?= $lang === 'te' ? 'కొత్త జిల్లా' : 'Add District' ?></a></li>
                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addMandalModal"><i class="bi bi-diagram-3 me-2 text-success"></i><?= $lang === 'te' ? 'కొత్త మండలం' : 'Add Mandal' ?></a></li>
                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addPanchayatModal"><i class="bi bi-houses me-2 text-info"></i><?= $lang === 'te' ? 'కొత్త గ్రామ పంచాయతీ' : 'Add Gram Panchayat' ?></a></li>
                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addVillageModal"><i class="bi bi-geo-alt me-2 text-warning"></i><?= $lang === 'te' ? 'కొత్త గ్రామం' : 'Add Village' ?></a></li>
                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addWardModal"><i class="bi bi-signpost me-2 text-secondary"></i><?= $lang === 'te' ? 'కొత్త వార్డు' : 'Add Ward' ?></a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addStateModal"><i class="bi bi-building me-2 text-dark"></i><?= $lang === 'te' ? 'కొత్త రాష్ట్రం' : 'Add State' ?></a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Alert Notifications -->
    <?php if ($success = \App\Core\Session::getFlash('success')): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><?= e($success) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($error = \App\Core\Session::getFlash('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= e($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Hierarchy Summary KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-4 col-md-2">
            <div class="card border-0 shadow-sm rounded-4 text-center p-3 bg-white h-100">
                <span class="text-muted small fw-bold text-uppercase">States</span>
                <h4 class="fw-bold mb-0 text-dark"><?= $counts['states'] ?></h4>
            </div>
        </div>
        <div class="col-4 col-md-2">
            <div class="card border-0 shadow-sm rounded-4 text-center p-3 bg-white h-100 border-start border-4 border-primary">
                <span class="text-muted small fw-bold text-uppercase">Districts</span>
                <h4 class="fw-bold mb-0 text-primary"><?= $counts['districts'] ?></h4>
            </div>
        </div>
        <div class="col-4 col-md-2">
            <div class="card border-0 shadow-sm rounded-4 text-center p-3 bg-white h-100 border-start border-4 border-success">
                <span class="text-muted small fw-bold text-uppercase">Mandals</span>
                <h4 class="fw-bold mb-0 text-success"><?= $counts['mandals'] ?></h4>
            </div>
        </div>
        <div class="col-4 col-md-2">
            <div class="card border-0 shadow-sm rounded-4 text-center p-3 bg-white h-100 border-start border-4 border-info">
                <span class="text-muted small fw-bold text-uppercase">Panchayats</span>
                <h4 class="fw-bold mb-0 text-info"><?= $counts['gram_panchayats'] ?></h4>
            </div>
        </div>
        <div class="col-4 col-md-2">
            <div class="card border-0 shadow-sm rounded-4 text-center p-3 bg-white h-100 border-start border-4 border-warning">
                <span class="text-muted small fw-bold text-uppercase">Villages</span>
                <h4 class="fw-bold mb-0 text-warning-emphasis"><?= $counts['villages'] ?></h4>
            </div>
        </div>
        <div class="col-4 col-md-2">
            <div class="card border-0 shadow-sm rounded-4 text-center p-3 bg-white h-100 border-start border-4 border-secondary">
                <span class="text-muted small fw-bold text-uppercase">Wards</span>
                <h4 class="fw-bold mb-0 text-secondary"><?= $counts['wards'] ?></h4>
            </div>
        </div>
    </div>

    <!-- Tabbed Navigation -->
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden mb-4">
        <div class="card-header bg-white border-bottom p-0">
            <ul class="nav nav-tabs border-0 px-3 pt-2" id="locationTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link <?= $activeTab === 'districts' ? 'active fw-bold' : '' ?>" href="<?= url('admin/locations?tab=districts') ?>">
                        <i class="bi bi-map me-1 text-primary"></i> <?= $lang === 'te' ? 'జిల్లాలు (Districts)' : 'Districts' ?> (<?= count($districts) ?>)
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $activeTab === 'mandals' ? 'active fw-bold' : '' ?>" href="<?= url('admin/locations?tab=mandals') ?>">
                        <i class="bi bi-diagram-3 me-1 text-success"></i> <?= $lang === 'te' ? 'మండలాలు (Mandals)' : 'Mandals' ?> (<?= count($mandals) ?>)
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $activeTab === 'panchayats' ? 'active fw-bold' : '' ?>" href="<?= url('admin/locations?tab=panchayats') ?>">
                        <i class="bi bi-houses me-1 text-info"></i> <?= $lang === 'te' ? 'పంచాయతీలు (GPs)' : 'Gram Panchayats' ?> (<?= count($panchayats) ?>)
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $activeTab === 'villages' ? 'active fw-bold' : '' ?>" href="<?= url('admin/locations?tab=villages') ?>">
                        <i class="bi bi-geo-alt me-1 text-warning"></i> <?= $lang === 'te' ? 'గ్రామాలు (Villages)' : 'Villages' ?> (<?= count($villages) ?>)
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $activeTab === 'wards' ? 'active fw-bold' : '' ?>" href="<?= url('admin/locations?tab=wards') ?>">
                        <i class="bi bi-signpost me-1 text-secondary"></i> <?= $lang === 'te' ? 'వార్డులు (Wards)' : 'Wards' ?> (<?= count($wards) ?>)
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $activeTab === 'states' ? 'active fw-bold' : '' ?>" href="<?= url('admin/locations?tab=states') ?>">
                        <i class="bi bi-building me-1 text-dark"></i> <?= $lang === 'te' ? 'రాష్ట్రాలు (States)' : 'States' ?> (<?= count($states) ?>)
                    </a>
                </li>
            </ul>
        </div>

        <div class="card-body p-4">
            <?php if ($activeTab === 'districts'): ?>
                <!-- DISTRICTS TABLE -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Districts (జిల్లాలు)</h5>
                    <button class="btn btn-sm btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#addDistrictModal">
                        <i class="bi bi-plus-lg me-1"></i> Add District
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>District (English / తెలుగు)</th>
                                <th>Code</th>
                                <th>State</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($districts as $d): ?>
                            <tr>
                                <td class="fw-bold text-muted">#<?= $d['id'] ?></td>
                                <td>
                                    <div class="fw-bold text-dark"><?= e($d['name_en']) ?></div>
                                    <div class="text-muted small"><?= e($d['name_te']) ?></div>
                                </td>
                                <td><span class="badge bg-light text-dark border"><?= e($d['code'] ?: '—') ?></span></td>
                                <td><?= e($d['state_name_en']) ?> (<?= e($d['state_name_te']) ?>)</td>
                                <td>
                                    <?php if ($d['is_active']): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2 py-1">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <form method="POST" action="<?= url('admin/locations/toggle') ?>" class="d-inline">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="level" value="district">
                                        <input type="hidden" name="id" value="<?= $d['id'] ?>">
                                        <button type="submit" class="btn btn-sm <?= $d['is_active'] ? 'btn-outline-warning' : 'btn-outline-success' ?> rounded-pill px-2 py-1" title="Toggle Active Status">
                                            <i class="bi <?= $d['is_active'] ? 'bi-pause-circle' : 'bi-play-circle' ?>"></i>
                                        </button>
                                    </form>
                                    <button class="btn btn-sm btn-outline-primary rounded-pill px-2 py-1" 
                                            data-bs-toggle="modal" data-bs-target="#editDistrictModal<?= $d['id'] ?>" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form method="POST" action="<?= url('admin/locations/delete') ?>" class="d-inline" onsubmit="return confirm('Delete this district? Note: Cannot be deleted if child entities or grievances exist.');">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="level" value="district">
                                        <input type="hidden" name="id" value="<?= $d['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-1" title="Safe Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Edit District Modal -->
                            <div class="modal fade" id="editDistrictModal<?= $d['id'] ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <form method="POST" action="<?= url('admin/locations/update') ?>" class="modal-content rounded-4 border-0 shadow">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="level" value="district">
                                        <input type="hidden" name="id" value="<?= $d['id'] ?>">
                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold">Edit District: <?= e($d['name_en']) ?></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">State</label>
                                                <select name="state_id" class="form-select" required>
                                                    <?php foreach ($states as $st): ?>
                                                        <option value="<?= $st['id'] ?>" <?= $d['state_id'] == $st['id'] ? 'selected' : '' ?>><?= e($st['name_en']) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">English Name</label>
                                                <input type="text" name="name_en" class="form-control" value="<?= e($d['name_en']) ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">Telugu Name (తెలుగు)</label>
                                                <input type="text" name="name_te" class="form-control" value="<?= e($d['name_te']) ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">District Code</label>
                                                <input type="text" name="code" class="form-control" value="<?= e($d['code']) ?>">
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="dAct<?= $d['id'] ?>" <?= $d['is_active'] ? 'checked' : '' ?>>
                                                <label class="form-check-label small" for="dAct<?= $d['id'] ?>">Active</label>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary rounded-pill px-4">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            <?php elseif ($activeTab === 'mandals'): ?>
                <!-- MANDALS TABLE -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Mandals (మండలాలు)</h5>
                    <button class="btn btn-sm btn-success rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#addMandalModal">
                        <i class="bi bi-plus-lg me-1"></i> Add Mandal
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Mandal (English / తెలుగు)</th>
                                <th>Code</th>
                                <th>District</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($mandals as $m): ?>
                            <tr>
                                <td class="fw-bold text-muted">#<?= $m['id'] ?></td>
                                <td>
                                    <div class="fw-bold text-dark"><?= e($m['name_en']) ?></div>
                                    <div class="text-muted small"><?= e($m['name_te']) ?></div>
                                </td>
                                <td><span class="badge bg-light text-dark border"><?= e($m['code'] ?: '—') ?></span></td>
                                <td><?= e($m['district_name_en']) ?></td>
                                <td>
                                    <?php if ($m['is_active']): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2 py-1">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <form method="POST" action="<?= url('admin/locations/toggle') ?>" class="d-inline">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="level" value="mandal">
                                        <input type="hidden" name="id" value="<?= $m['id'] ?>">
                                        <button type="submit" class="btn btn-sm <?= $m['is_active'] ? 'btn-outline-warning' : 'btn-outline-success' ?> rounded-pill px-2 py-1">
                                            <i class="bi <?= $m['is_active'] ? 'bi-pause-circle' : 'bi-play-circle' ?>"></i>
                                        </button>
                                    </form>
                                    <button class="btn btn-sm btn-outline-primary rounded-pill px-2 py-1" data-bs-toggle="modal" data-bs-target="#editMandalModal<?= $m['id'] ?>">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form method="POST" action="<?= url('admin/locations/delete') ?>" class="d-inline" onsubmit="return confirm('Delete this mandal?');">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="level" value="mandal">
                                        <input type="hidden" name="id" value="<?= $m['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-1">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Edit Mandal Modal -->
                            <div class="modal fade" id="editMandalModal<?= $m['id'] ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <form method="POST" action="<?= url('admin/locations/update') ?>" class="modal-content rounded-4 border-0 shadow">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="level" value="mandal">
                                        <input type="hidden" name="id" value="<?= $m['id'] ?>">
                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold">Edit Mandal: <?= e($m['name_en']) ?></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">District</label>
                                                <select name="district_id" class="form-select" required>
                                                    <?php foreach ($districts as $d): ?>
                                                        <option value="<?= $d['id'] ?>" <?= $m['district_id'] == $d['id'] ? 'selected' : '' ?>><?= e($d['name_en']) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">English Name</label>
                                                <input type="text" name="name_en" class="form-control" value="<?= e($m['name_en']) ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">Telugu Name (తెలుగు)</label>
                                                <input type="text" name="name_te" class="form-control" value="<?= e($m['name_te']) ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">Mandal Code</label>
                                                <input type="text" name="code" class="form-control" value="<?= e($m['code']) ?>">
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="mAct<?= $m['id'] ?>" <?= $m['is_active'] ? 'checked' : '' ?>>
                                                <label class="form-check-label small" for="mAct<?= $m['id'] ?>">Active</label>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-success rounded-pill px-4">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            <?php elseif ($activeTab === 'panchayats'): ?>
                <!-- GRAM PANCHAYATS TABLE -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Gram Panchayats (గ్రామ పంచాయతీలు)</h5>
                    <button class="btn btn-sm btn-info text-white rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#addPanchayatModal">
                        <i class="bi bi-plus-lg me-1"></i> Add Gram Panchayat
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Gram Panchayat (English / తెలుగు)</th>
                                <th>Code</th>
                                <th>Mandal</th>
                                <th>District</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($panchayats as $gp): ?>
                            <tr>
                                <td class="fw-bold text-muted">#<?= $gp['id'] ?></td>
                                <td>
                                    <div class="fw-bold text-dark"><?= e($gp['name_en']) ?></div>
                                    <div class="text-muted small"><?= e($gp['name_te']) ?></div>
                                </td>
                                <td><span class="badge bg-light text-dark border"><?= e($gp['code'] ?: '—') ?></span></td>
                                <td><?= e($gp['mandal_name_en']) ?></td>
                                <td><?= e($gp['district_name_en']) ?></td>
                                <td>
                                    <?php if ($gp['is_active']): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2 py-1">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <form method="POST" action="<?= url('admin/locations/toggle') ?>" class="d-inline">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="level" value="gram_panchayat">
                                        <input type="hidden" name="id" value="<?= $gp['id'] ?>">
                                        <button type="submit" class="btn btn-sm <?= $gp['is_active'] ? 'btn-outline-warning' : 'btn-outline-success' ?> rounded-pill px-2 py-1">
                                            <i class="bi <?= $gp['is_active'] ? 'bi-pause-circle' : 'bi-play-circle' ?>"></i>
                                        </button>
                                    </form>
                                    <button class="btn btn-sm btn-outline-primary rounded-pill px-2 py-1" data-bs-toggle="modal" data-bs-target="#editGpModal<?= $gp['id'] ?>">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form method="POST" action="<?= url('admin/locations/delete') ?>" class="d-inline" onsubmit="return confirm('Delete this Gram Panchayat?');">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="level" value="gram_panchayat">
                                        <input type="hidden" name="id" value="<?= $gp['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-1">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Edit GP Modal -->
                            <div class="modal fade" id="editGpModal<?= $gp['id'] ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <form method="POST" action="<?= url('admin/locations/update') ?>" class="modal-content rounded-4 border-0 shadow">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="level" value="gram_panchayat">
                                        <input type="hidden" name="id" value="<?= $gp['id'] ?>">
                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold">Edit Panchayat: <?= e($gp['name_en']) ?></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">Mandal</label>
                                                <select name="mandal_id" class="form-select" required>
                                                    <?php foreach ($mandals as $m): ?>
                                                        <option value="<?= $m['id'] ?>" <?= $gp['mandal_id'] == $m['id'] ? 'selected' : '' ?>><?= e($m['name_en']) ?> (<?= e($m['district_name_en']) ?>)</option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">English Name</label>
                                                <input type="text" name="name_en" class="form-control" value="<?= e($gp['name_en']) ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">Telugu Name (తెలుగు)</label>
                                                <input type="text" name="name_te" class="form-control" value="<?= e($gp['name_te']) ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">Code</label>
                                                <input type="text" name="code" class="form-control" value="<?= e($gp['code']) ?>">
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="gpAct<?= $gp['id'] ?>" <?= $gp['is_active'] ? 'checked' : '' ?>>
                                                <label class="form-check-label small" for="gpAct<?= $gp['id'] ?>">Active</label>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-info text-white rounded-pill px-4">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            <?php elseif ($activeTab === 'villages'): ?>
                <!-- VILLAGES TABLE -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Villages (గ్రామాలు)</h5>
                    <button class="btn btn-sm btn-warning text-dark rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#addVillageModal">
                        <i class="bi bi-plus-lg me-1"></i> Add Village
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Village (English / తెలుగు)</th>
                                <th>Gram Panchayat</th>
                                <th>Mandal</th>
                                <th>District</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($villages as $v): ?>
                            <tr>
                                <td class="fw-bold text-muted">#<?= $v['id'] ?></td>
                                <td>
                                    <div class="fw-bold text-dark"><?= e($v['name_en']) ?></div>
                                    <div class="text-muted small"><?= e($v['name_te']) ?></div>
                                </td>
                                <td><?= e($v['panchayat_name_en'] ?: '—') ?></td>
                                <td><?= e($v['mandal_name_en'] ?: $v['mandal']) ?></td>
                                <td><?= e($v['district_name_en'] ?: $v['district']) ?></td>
                                <td>
                                    <?php if ($v['is_active']): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2 py-1">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <form method="POST" action="<?= url('admin/locations/toggle') ?>" class="d-inline">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="level" value="village">
                                        <input type="hidden" name="id" value="<?= $v['id'] ?>">
                                        <button type="submit" class="btn btn-sm <?= $v['is_active'] ? 'btn-outline-warning' : 'btn-outline-success' ?> rounded-pill px-2 py-1">
                                            <i class="bi <?= $v['is_active'] ? 'bi-pause-circle' : 'bi-play-circle' ?>"></i>
                                        </button>
                                    </form>
                                    <button class="btn btn-sm btn-outline-primary rounded-pill px-2 py-1" data-bs-toggle="modal" data-bs-target="#editVillageModal<?= $v['id'] ?>">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form method="POST" action="<?= url('admin/locations/delete') ?>" class="d-inline" onsubmit="return confirm('Delete this village?');">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="level" value="village">
                                        <input type="hidden" name="id" value="<?= $v['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-1">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Edit Village Modal -->
                            <div class="modal fade" id="editVillageModal<?= $v['id'] ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <form method="POST" action="<?= url('admin/locations/update') ?>" class="modal-content rounded-4 border-0 shadow">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="level" value="village">
                                        <input type="hidden" name="id" value="<?= $v['id'] ?>">
                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold">Edit Village: <?= e($v['name_en']) ?></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">Gram Panchayat</label>
                                                <select name="gram_panchayat_id" class="form-select" required>
                                                    <?php foreach ($panchayats as $gp): ?>
                                                        <option value="<?= $gp['id'] ?>" <?= $v['gram_panchayat_id'] == $gp['id'] ? 'selected' : '' ?>><?= e($gp['name_en']) ?> (<?= e($gp['mandal_name_en']) ?>)</option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">English Name</label>
                                                <input type="text" name="name_en" class="form-control" value="<?= e($v['name_en']) ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">Telugu Name (తెలుగు)</label>
                                                <input type="text" name="name_te" class="form-control" value="<?= e($v['name_te']) ?>" required>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="vAct<?= $v['id'] ?>" <?= $v['is_active'] ? 'checked' : '' ?>>
                                                <label class="form-check-label small" for="vAct<?= $v['id'] ?>">Active</label>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            <?php elseif ($activeTab === 'wards'): ?>
                <!-- WARDS TABLE -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Wards (వార్డులు / వీధులు)</h5>
                    <button class="btn btn-sm btn-secondary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#addWardModal">
                        <i class="bi bi-plus-lg me-1"></i> Add Ward
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Ward Number</th>
                                <th>Ward Name / Area</th>
                                <th>Village</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($wards as $w): ?>
                            <tr>
                                <td class="fw-bold text-muted">#<?= $w['id'] ?></td>
                                <td><span class="badge bg-light text-dark border"><?= e($w['ward_number']) ?></span></td>
                                <td><?= e($w['ward_name'] ?: '—') ?></td>
                                <td><?= e($w['village_name_en']) ?> (<?= e($w['village_name_te']) ?>)</td>
                                <td>
                                    <?php if ($w['is_active']): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2 py-1">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <form method="POST" action="<?= url('admin/locations/toggle') ?>" class="d-inline">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="level" value="ward">
                                        <input type="hidden" name="id" value="<?= $w['id'] ?>">
                                        <button type="submit" class="btn btn-sm <?= $w['is_active'] ? 'btn-outline-warning' : 'btn-outline-success' ?> rounded-pill px-2 py-1">
                                            <i class="bi <?= $w['is_active'] ? 'bi-pause-circle' : 'bi-play-circle' ?>"></i>
                                        </button>
                                    </form>
                                    <button class="btn btn-sm btn-outline-primary rounded-pill px-2 py-1" data-bs-toggle="modal" data-bs-target="#editWardModal<?= $w['id'] ?>">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form method="POST" action="<?= url('admin/locations/delete') ?>" class="d-inline" onsubmit="return confirm('Delete this ward?');">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="level" value="ward">
                                        <input type="hidden" name="id" value="<?= $w['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-1">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Edit Ward Modal -->
                            <div class="modal fade" id="editWardModal<?= $w['id'] ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <form method="POST" action="<?= url('admin/locations/update') ?>" class="modal-content rounded-4 border-0 shadow">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="level" value="ward">
                                        <input type="hidden" name="id" value="<?= $w['id'] ?>">
                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold">Edit Ward: <?= e($w['ward_number']) ?></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">Village</label>
                                                <select name="village_id" class="form-select" required>
                                                    <?php foreach ($villages as $v): ?>
                                                        <option value="<?= $v['id'] ?>" <?= $w['village_id'] == $v['id'] ? 'selected' : '' ?>><?= e($v['name_en']) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">Ward Number (e.g. Ward 1)</label>
                                                <input type="text" name="ward_number" class="form-control" value="<?= e($w['ward_number']) ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">Ward Name / Landmark Area</label>
                                                <input type="text" name="ward_name" class="form-control" value="<?= e($w['ward_name']) ?>">
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="wAct<?= $w['id'] ?>" <?= $w['is_active'] ? 'checked' : '' ?>>
                                                <label class="form-check-label small" for="wAct<?= $w['id'] ?>">Active</label>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-secondary rounded-pill px-4">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            <?php elseif ($activeTab === 'states'): ?>
                <!-- STATES TABLE -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">States (రాష్ట్రాలు)</h5>
                    <button class="btn btn-sm btn-dark rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#addStateModal">
                        <i class="bi bi-plus-lg me-1"></i> Add State
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>State (English / తెలుగు)</th>
                                <th>Code</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($states as $st): ?>
                            <tr>
                                <td class="fw-bold text-muted">#<?= $st['id'] ?></td>
                                <td>
                                    <div class="fw-bold text-dark"><?= e($st['name_en']) ?></div>
                                    <div class="text-muted small"><?= e($st['name_te']) ?></div>
                                </td>
                                <td><span class="badge bg-light text-dark border"><?= e($st['code']) ?></span></td>
                                <td>
                                    <?php if ($st['is_active']): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2 py-1">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <form method="POST" action="<?= url('admin/locations/toggle') ?>" class="d-inline">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="level" value="state">
                                        <input type="hidden" name="id" value="<?= $st['id'] ?>">
                                        <button type="submit" class="btn btn-sm <?= $st['is_active'] ? 'btn-outline-warning' : 'btn-outline-success' ?> rounded-pill px-2 py-1">
                                            <i class="bi <?= $st['is_active'] ? 'bi-pause-circle' : 'bi-play-circle' ?>"></i>
                                        </button>
                                    </form>
                                    <button class="btn btn-sm btn-outline-primary rounded-pill px-2 py-1" data-bs-toggle="modal" data-bs-target="#editStateModal<?= $st['id'] ?>">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form method="POST" action="<?= url('admin/locations/delete') ?>" class="d-inline" onsubmit="return confirm('Delete this state?');">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="level" value="state">
                                        <input type="hidden" name="id" value="<?= $st['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-1">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Edit State Modal -->
                            <div class="modal fade" id="editStateModal<?= $st['id'] ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <form method="POST" action="<?= url('admin/locations/update') ?>" class="modal-content rounded-4 border-0 shadow">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="level" value="state">
                                        <input type="hidden" name="id" value="<?= $st['id'] ?>">
                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold">Edit State: <?= e($st['name_en']) ?></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">State Code (e.g. AP)</label>
                                                <input type="text" name="code" class="form-control" value="<?= e($st['code']) ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">English Name</label>
                                                <input type="text" name="name_en" class="form-control" value="<?= e($st['name_en']) ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">Telugu Name (తెలుగు)</label>
                                                <input type="text" name="name_te" class="form-control" value="<?= e($st['name_te']) ?>" required>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="stAct<?= $st['id'] ?>" <?= $st['is_active'] ? 'checked' : '' ?>>
                                                <label class="form-check-label small" for="stAct<?= $st['id'] ?>">Active</label>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-dark rounded-pill px-4">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- ================= MODALS FOR ADDING NEW LOCATIONS ================= -->

<!-- 1. Add District Modal -->
<div class="modal fade" id="addDistrictModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="<?= url('admin/locations/store') ?>" class="modal-content rounded-4 border-0 shadow">
            <?= csrf_field() ?>
            <input type="hidden" name="level" value="district">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bi bi-map text-primary me-2"></i>Add New District</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-bold">State <span class="text-danger">*</span></label>
                    <select name="state_id" class="form-select" required>
                        <?php foreach ($states as $st): ?>
                            <option value="<?= $st['id'] ?>"><?= e($st['name_en']) ?> (<?= e($st['name_te']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">District Name (English) <span class="text-danger">*</span></label>
                    <input type="text" name="name_en" class="form-control" placeholder="e.g. Alluri Sitharama Raju" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">District Name (Telugu / తెలుగు) <span class="text-danger">*</span></label>
                    <input type="text" name="name_te" class="form-control" placeholder="ఉదా: అల్లూరి సీతారామరాజు" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">District Code</label>
                    <input type="text" name="code" class="form-control" placeholder="e.g. ASR">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4">Create District</button>
            </div>
        </form>
    </div>
</div>

<!-- 2. Add Mandal Modal -->
<div class="modal fade" id="addMandalModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="<?= url('admin/locations/store') ?>" class="modal-content rounded-4 border-0 shadow">
            <?= csrf_field() ?>
            <input type="hidden" name="level" value="mandal">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bi bi-diagram-3 text-success me-2"></i>Add New Mandal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-bold">District <span class="text-danger">*</span></label>
                    <select name="district_id" class="form-select" required>
                        <?php foreach ($districts as $d): ?>
                            <option value="<?= $d['id'] ?>"><?= e($d['name_en']) ?> (<?= e($d['name_te']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Mandal Name (English) <span class="text-danger">*</span></label>
                    <input type="text" name="name_en" class="form-control" placeholder="e.g. Paderu" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Mandal Name (Telugu / తెలుగు) <span class="text-danger">*</span></label>
                    <input type="text" name="name_te" class="form-control" placeholder="ఉదా: పాడేరు" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Mandal Code</label>
                    <input type="text" name="code" class="form-control" placeholder="e.g. PAD">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success rounded-pill px-4">Create Mandal</button>
            </div>
        </form>
    </div>
</div>

<!-- 3. Add Gram Panchayat Modal -->
<div class="modal fade" id="addPanchayatModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="<?= url('admin/locations/store') ?>" class="modal-content rounded-4 border-0 shadow">
            <?= csrf_field() ?>
            <input type="hidden" name="level" value="gram_panchayat">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bi bi-houses text-info me-2"></i>Add New Gram Panchayat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Mandal <span class="text-danger">*</span></label>
                    <select name="mandal_id" class="form-select" required>
                        <?php foreach ($mandals as $m): ?>
                            <option value="<?= $m['id'] ?>"><?= e($m['name_en']) ?> (<?= e($m['district_name_en']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Gram Panchayat Name (English) <span class="text-danger">*</span></label>
                    <input type="text" name="name_en" class="form-control" placeholder="e.g. Minumuluru" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Gram Panchayat Name (Telugu / తెలుగు) <span class="text-danger">*</span></label>
                    <input type="text" name="name_te" class="form-control" placeholder="ఉదా: మినుములూరు" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Code</label>
                    <input type="text" name="code" class="form-control" placeholder="e.g. GP_MIN">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-info text-white rounded-pill px-4">Create Gram Panchayat</button>
            </div>
        </form>
    </div>
</div>

<!-- 4. Add Village Modal -->
<div class="modal fade" id="addVillageModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="<?= url('admin/locations/store') ?>" class="modal-content rounded-4 border-0 shadow">
            <?= csrf_field() ?>
            <input type="hidden" name="level" value="village">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bi bi-geo-alt text-warning me-2"></i>Add New Village</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Gram Panchayat <span class="text-danger">*</span></label>
                    <select name="gram_panchayat_id" class="form-select" required>
                        <?php foreach ($panchayats as $gp): ?>
                            <option value="<?= $gp['id'] ?>"><?= e($gp['name_en']) ?> (<?= e($gp['mandal_name_en']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Village Name (English) <span class="text-danger">*</span></label>
                    <input type="text" name="name_en" class="form-control" placeholder="e.g. Talari" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Village Name (Telugu / తెలుగు) <span class="text-danger">*</span></label>
                    <input type="text" name="name_te" class="form-control" placeholder="ఉదా: తలారి" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold">Create Village</button>
            </div>
        </form>
    </div>
</div>

<!-- 5. Add Ward Modal -->
<div class="modal fade" id="addWardModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="<?= url('admin/locations/store') ?>" class="modal-content rounded-4 border-0 shadow">
            <?= csrf_field() ?>
            <input type="hidden" name="level" value="ward">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bi bi-signpost text-secondary me-2"></i>Add New Ward</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Village <span class="text-danger">*</span></label>
                    <select name="village_id" class="form-select" required>
                        <?php foreach ($villages as $v): ?>
                            <option value="<?= $v['id'] ?>"><?= e($v['name_en']) ?> (<?= e($v['name_te']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Ward Number <span class="text-danger">*</span></label>
                    <input type="text" name="ward_number" class="form-control" placeholder="e.g. Ward 1" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Ward Name / Landmark Locality</label>
                    <input type="text" name="ward_name" class="form-control" placeholder="e.g. Gandhi Nagar / గాంధీ నగర్">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-secondary rounded-pill px-4">Create Ward</button>
            </div>
        </form>
    </div>
</div>

<!-- 6. Add State Modal -->
<div class="modal fade" id="addStateModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="<?= url('admin/locations/store') ?>" class="modal-content rounded-4 border-0 shadow">
            <?= csrf_field() ?>
            <input type="hidden" name="level" value="state">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bi bi-building text-dark me-2"></i>Add New State</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-bold">State Code (e.g. AP, TS, KA) <span class="text-danger">*</span></label>
                    <input type="text" name="code" class="form-control" placeholder="e.g. AP" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">State Name (English) <span class="text-danger">*</span></label>
                    <input type="text" name="name_en" class="form-control" placeholder="e.g. Andhra Pradesh" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">State Name (Telugu / తెలుగు) <span class="text-danger">*</span></label>
                    <input type="text" name="name_te" class="form-control" placeholder="ఉదా: ఆంధ్రప్రదేశ్" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-dark rounded-pill px-4">Create State</button>
            </div>
        </form>
    </div>
</div>

<?php view('layouts/footer'); ?>
