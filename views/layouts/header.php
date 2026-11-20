<?php
$currentUser = auth();
$activeLang = current_lang();
$title = $title ?? __('common.app_title') . ' — ' . __('common.app_name');

// Detect current active path for interactive active states
$currentUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
if ($scriptDir !== '/' && $scriptDir !== '' && str_starts_with($currentUri, $scriptDir)) {
    $currentUri = substr($currentUri, strlen($scriptDir));
}
$currentUri = '/' . trim($currentUri, '/');

$isHome = ($currentUri === '/' || $currentUri === '/home');
$isSubmit = str_starts_with($currentUri, '/submit');
$isTrack = str_starts_with($currentUri, '/track');
$isAbout = str_starts_with($currentUri, '/about');
$isDashboard = (str_starts_with($currentUri, '/dashboard') || str_starts_with($currentUri, '/admin') || str_starts_with($currentUri, '/staff') || str_starts_with($currentUri, '/officer') || str_starts_with($currentUri, '/grievance'));
?>
<!DOCTYPE html>
<html lang="<?= e($activeLang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title) ?></title>
    <!-- Google Fonts: Poppins & Noto Sans Telugu -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Telugu:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS & Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body class="bg-light d-flex flex-column min-vh-100 <?= $activeLang === 'te' ? 'font-telugu' : '' ?>" id="appBody">

<!-- Top Utility Sub-Bar -->
<div class="top-utility-bar py-1 border-bottom border-light border-opacity-10 d-none d-md-block">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center text-white small">
            <!-- Left: Live System Status & Helplines -->
            <div class="d-flex align-items-center gap-3">
                <a href="<?= url('health') ?>" class="text-white-50 text-decoration-none d-flex align-items-center gap-2 status-pill-link" title="Click to view live health status">
                    <span class="status-beacon-container">
                        <span class="status-beacon-ping"></span>
                        <span class="status-beacon-dot"></span>
                    </span>
                    <span class="text-white fw-semibold"><?= e(__('common.nav_portal_live')) ?></span>
                    <span class="text-white-50">| 99.4% Redressal</span>
                </a>

                <!-- Emergency Helplines Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-sm btn-link text-white-50 text-decoration-none p-0 d-flex align-items-center gap-1 dropdown-toggle helpline-btn" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-telephone-fill text-warning"></i>
                        <span><?= e(__('common.nav_helpline')) ?></span>
                    </button>
                    <ul class="dropdown-menu shadow border-0 mt-1 rounded-3 fs-7">
                        <li><h6 class="dropdown-header text-uppercase fw-bold"><?= e(__('common.nav_emergency')) ?></h6></li>
                        <li><a class="dropdown-item d-flex justify-content-between align-items-center" href="tel:112"><span>🚨 National Emergency</span> <strong class="text-danger font-monospace">112</strong></a></li>
                        <li><a class="dropdown-item d-flex justify-content-between align-items-center" href="tel:108"><span>🚑 Medical / Ambulance</span> <strong class="text-primary font-monospace">108</strong></a></li>
                        <li><a class="dropdown-item d-flex justify-content-between align-items-center" href="tel:1912"><span>⚡ Electricity Discom</span> <strong class="text-warning font-monospace">1912</strong></a></li>
                        <li><a class="dropdown-item d-flex justify-content-between align-items-center" href="tel:18004251899"><span>🏛️ Panchayat Raj Helpline</span> <strong class="text-success font-monospace">1800-425-1899</strong></a></li>
                    </ul>
                </div>
            </div>

            <!-- Right: Accessibility Font Size & Staff Shortcut -->
            <div class="d-flex align-items-center gap-3">
                <!-- Font Resizer -->
                <div class="d-flex align-items-center gap-1 font-resizer-box">
                    <span class="text-white-50 me-1"><i class="bi bi-type"></i></span>
                    <button type="button" class="btn btn-xs font-size-btn text-white" id="fontDecBtn" title="Decrease Font Size">A-</button>
                    <button type="button" class="btn btn-xs font-size-btn text-white active" id="fontResetBtn" title="Reset Font Size">A</button>
                    <button type="button" class="btn btn-xs font-size-btn text-white" id="fontIncBtn" title="Increase Font Size">A+</button>
                </div>

                <!-- Internal Staff Portal Quick Link -->
                <?php if (!$currentUser): ?>
                    <a href="<?= url('login') ?>" class="text-white-50 text-decoration-none hover-text-white d-flex align-items-center gap-1">
                        <i class="bi bi-shield-lock"></i> <?= e(__('common.nav_staff_login')) ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Main Interactive Navigation Header -->
<nav class="navbar navbar-expand-lg navbar-dark smart-navbar sticky-top py-2" id="mainSmartNavbar">
    <!-- Scroll Progress Bar -->
    <div class="nav-progress-bar" id="navProgressBar"></div>
    <div class="container">
        <!-- Brand / Logo Emblem with Micro-Interaction -->
        <a class="navbar-brand d-flex align-items-center gap-2 brand-interactive" href="<?= url('') ?>">
            <div class="brand-logo-emblem shadow-sm d-flex align-items-center justify-content-center">
                <i class="bi bi-houses-fill"></i>
            </div>
            <div class="brand-text-container">
                <span class="fw-bold brand-title d-block"><?= e(__('common.app_name')) ?></span>
                <span class="brand-subtitle d-block"><?= e(__('common.app_title')) ?></span>
            </div>
        </a>

        <!-- Mobile Controls: Quick Report + Drawer Toggle -->
        <div class="d-flex align-items-center gap-2 d-lg-none">
            <a href="<?= url('submit') ?>" class="btn btn-warning btn-sm rounded-pill fw-bold px-3 py-1 shadow-sm d-flex align-items-center gap-1">
                <i class="bi bi-plus-circle-fill"></i>
                <span><?= e(__('common.nav_quick_report')) ?></span>
            </a>
            <button class="navbar-toggler modern-hamburger border-0 shadow-none p-1" type="button"
                    data-bs-toggle="offcanvas" data-bs-target="#mobileNavDrawer"
                    aria-controls="mobileNavDrawer" aria-label="Toggle navigation"
                    id="mobileHamburgerBtn">
                <span class="hamburger-bar"></span>
                <span class="hamburger-bar"></span>
                <span class="hamburger-bar"></span>
            </button>
        </div>

        <!-- Desktop Navigation Items -->
        <div class="collapse navbar-collapse" id="desktopNavContent">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0 nav-pills-interactive">
                <li class="nav-item">
                    <a class="nav-link nav-link-custom <?= $isHome ? 'active' : '' ?>" href="<?= url('') ?>">
                        <i class="bi bi-house-door-fill me-1"></i>
                        <span><?= e(__('common.nav_home')) ?></span>
                        <?php if ($isHome): ?><span class="active-dot"></span><?php endif; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-link-custom <?= $isSubmit ? 'active' : '' ?>" href="<?= url('submit') ?>">
                        <i class="bi bi-pencil-square me-1"></i>
                        <span><?= e(__('common.nav_submit')) ?></span>
                        <?php if ($isSubmit): ?><span class="active-dot"></span><?php endif; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-link-custom <?= $isTrack ? 'active' : '' ?>" href="<?= url('track') ?>">
                        <i class="bi bi-search me-1"></i>
                        <span><?= e(__('common.nav_track')) ?></span>
                        <?php if ($isTrack): ?><span class="active-dot"></span><?php endif; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-link-custom <?= $isAbout ? 'active' : '' ?>" href="<?= url('about') ?>">
                        <i class="bi bi-info-circle-fill me-1"></i>
                        <span><?= e(__('common.nav_about')) ?></span>
                        <?php if ($isAbout): ?><span class="active-dot"></span><?php endif; ?>
                    </a>
                </li>
                <?php if ($currentUser): ?>
                <li class="nav-item">
                    <a class="nav-link nav-link-custom text-warning <?= $isDashboard ? 'active' : '' ?>" href="<?= url('dashboard') ?>">
                        <i class="bi bi-speedometer2 me-1"></i>
                        <span><?= $activeLang === 'te' ? 'డ్యాష్‌బోర్డ్' : 'Dashboard' ?></span>
                        <span class="badge bg-warning text-dark text-uppercase ms-1 px-2 py-0 fs-8"><?= e($currentUser['role']) ?></span>
                    </a>
                </li>
                <?php endif; ?>
            </ul>

            <!-- Right Controls: Quick Search + CTA + Language + Auth -->
            <div class="d-flex align-items-center gap-2">
                <!-- Interactive Quick Track Search Button (Opens Modal) -->
                <button type="button" class="btn btn-glass-icon btn-sm"
                        data-bs-toggle="modal" data-bs-target="#quickTrackModal"
                        title="Quick Search (Ctrl+K)" aria-label="Quick Grievance Search">
                    <i class="bi bi-search"></i>
                </button>

                <!-- Interactive Sliding Language Switcher Capsule -->
                <div class="lang-capsule p-1 d-flex align-items-center shadow-sm">
                    <a href="<?= switch_lang_url('te') ?>" class="lang-option <?= $activeLang === 'te' ? 'active' : '' ?>" title="తెలుగులోకి మార్చండి">
                        తెలుగు
                    </a>
                    <a href="<?= switch_lang_url('en') ?>" class="lang-option <?= $activeLang === 'en' ? 'active' : '' ?>" title="Switch to English">
                        English
                    </a>
                </div>

                <!-- Primary CTA: Submit Grievance Button -->
                <a href="<?= url('submit') ?>" class="btn btn-warning rounded-pill fw-bold px-3 py-2 shadow-sm d-flex align-items-center gap-2 cta-report-btn">
                    <i class="bi bi-plus-circle-fill"></i>
                    <span><?= e(__('common.nav_quick_report')) ?></span>
                </a>

                <!-- Internal Staff Authentication Menu -->
                <?php if ($currentUser): ?>
                    <div class="dropdown">
                        <button class="btn btn-outline-light dropdown-toggle d-flex align-items-center gap-2 rounded-pill px-3 py-1 user-dropdown-btn shadow-sm" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle fs-5"></i>
                            <span class="fw-semibold text-capitalize text-truncate" style="max-width: 110px;"><?= e($currentUser['full_name']) ?></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2 rounded-4 p-2">
                            <li class="px-3 py-2 border-bottom mb-1">
                                <span class="d-block small text-muted">Signed in as</span>
                                <strong class="d-block text-dark text-truncate"><?= e($currentUser['email']) ?></strong>
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill text-uppercase mt-1"><?= e($currentUser['role']) ?></span>
                            </li>
                            <li><a class="dropdown-item rounded-3 py-2 d-flex align-items-center gap-2" href="<?= url('dashboard') ?>"><i class="bi bi-speedometer2 text-success"></i> <?= $activeLang === 'te' ? 'డ్యాష్‌బోర్డ్' : 'Dashboard' ?></a></li>
                            <?php if ($currentUser['role'] === 'admin'): ?>
                                <li><a class="dropdown-item rounded-3 py-2 d-flex align-items-center gap-2" href="<?= url('admin/locations') ?>"><i class="bi bi-geo-alt-fill text-danger"></i> <?= $activeLang === 'te' ? 'ప్రాంతాల నిర్వహణ' : 'Manage Locations' ?></a></li>
                                <li><a class="dropdown-item rounded-3 py-2 d-flex align-items-center gap-2" href="<?= url('dashboard/export') ?>"><i class="bi bi-file-earmark-spreadsheet text-primary"></i> Export CSV</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <form method="POST" action="<?= url('logout') ?>" class="m-0">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="dropdown-item rounded-3 py-2 text-danger d-flex align-items-center gap-2">
                                        <i class="bi bi-box-arrow-right"></i> <?= e(__('common.nav_logout')) ?>
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="<?= url('login') ?>" class="btn btn-outline-light btn-sm rounded-pill px-3 py-2 staff-login-btn" title="Internal Staff Login">
                        <i class="bi bi-shield-lock me-1"></i> <?= e(__('common.nav_staff_login')) ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<!-- Mobile Offcanvas Drawer -->
<div class="offcanvas offcanvas-end mobile-nav-drawer" tabindex="-1" id="mobileNavDrawer" aria-labelledby="mobileNavDrawerLabel">
    <div class="offcanvas-header border-bottom border-light border-opacity-10 py-3">
        <div class="d-flex align-items-center gap-2">
            <div class="brand-logo-emblem d-flex align-items-center justify-content-center shadow-sm">
                <i class="bi bi-houses-fill"></i>
            </div>
            <div>
                <h5 class="offcanvas-title text-white fw-bold mb-0" id="mobileNavDrawerLabel"><?= e(__('common.app_name')) ?></h5>
                <span class="text-white-50 small"><?= e(__('common.app_title')) ?></span>
            </div>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body d-flex flex-column justify-content-between p-4">
        <div>
            <!-- Mobile Action Cards -->
            <div class="d-grid gap-2 mb-4">
                <a href="<?= url('submit') ?>" class="btn btn-warning rounded-4 py-3 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-plus-circle-fill fs-5"></i>
                    <span><?= e(__('common.nav_submit')) ?></span>
                </a>
                <a href="<?= url('track') ?>" class="btn btn-outline-light rounded-4 py-2 fw-semibold d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-search"></i>
                    <span><?= e(__('common.nav_track')) ?></span>
                </a>
            </div>

            <!-- Mobile Navigation Links -->
            <ul class="nav flex-column gap-2 mobile-nav-links">
                <li class="nav-item">
                    <a class="nav-link <?= $isHome ? 'active' : '' ?>" href="<?= url('') ?>">
                        <i class="bi bi-house-door-fill me-2"></i> <?= e(__('common.nav_home')) ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $isSubmit ? 'active' : '' ?>" href="<?= url('submit') ?>">
                        <i class="bi bi-pencil-square me-2"></i> <?= e(__('common.nav_submit')) ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $isTrack ? 'active' : '' ?>" href="<?= url('track') ?>">
                        <i class="bi bi-search me-2"></i> <?= e(__('common.nav_track')) ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $isAbout ? 'active' : '' ?>" href="<?= url('about') ?>">
                        <i class="bi bi-info-circle-fill me-2"></i> <?= e(__('common.nav_about')) ?>
                    </a>
                </li>
                <?php if ($currentUser): ?>
                <li class="nav-item mt-2 pt-2 border-top border-light border-opacity-10">
                    <a class="nav-link text-warning fw-bold <?= $isDashboard ? 'active' : '' ?>" href="<?= url('dashboard') ?>">
                        <i class="bi bi-speedometer2 me-2"></i> <?= $activeLang === 'te' ? 'డ్యాష్‌బోర్డ్' : 'Dashboard' ?>
                    </a>
                </li>
                <?php if ($currentUser['role'] === 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link text-white-50" href="<?= url('admin/locations') ?>">
                        <i class="bi bi-geo-alt-fill me-2 text-danger"></i> <?= $activeLang === 'te' ? 'ప్రాంతాల నిర్వహణ' : 'Manage Locations' ?>
                    </a>
                </li>
                <?php endif; ?>
                <?php endif; ?>
            </ul>

            <!-- Mobile Language Switcher -->
            <div class="mt-4 pt-3 border-top border-light border-opacity-10">
                <span class="text-white-50 small d-block mb-2"><i class="bi bi-translate me-1"></i> భాష ఎంచుకోండి (Language):</span>
                <div class="btn-group w-100 shadow-sm" role="group">
                    <a href="<?= switch_lang_url('te') ?>" class="btn rounded-start-pill py-2 fw-bold <?= $activeLang === 'te' ? 'btn-light text-success' : 'btn-outline-light' ?>">
                        తెలుగు
                    </a>
                    <a href="<?= switch_lang_url('en') ?>" class="btn rounded-end-pill py-2 fw-bold <?= $activeLang === 'en' ? 'btn-light text-success' : 'btn-outline-light' ?>">
                        English
                    </a>
                </div>
            </div>

            <!-- Mobile Emergency Contacts -->
            <div class="mt-4 pt-3 border-top border-light border-opacity-10">
                <span class="text-white-50 small d-block mb-2"><i class="bi bi-telephone-fill me-1 text-warning"></i> <?= e(__('common.nav_emergency')) ?>:</span>
                <div class="d-flex flex-wrap gap-2">
                    <a href="tel:112" class="badge bg-danger-subtle text-danger border border-danger-subtle text-decoration-none px-3 py-2 rounded-pill font-monospace">112 Emergency</a>
                    <a href="tel:108" class="badge bg-primary-subtle text-primary border border-primary-subtle text-decoration-none px-3 py-2 rounded-pill font-monospace">108 Ambulance</a>
                    <a href="tel:1912" class="badge bg-warning-subtle text-dark border border-warning-subtle text-decoration-none px-3 py-2 rounded-pill font-monospace">1912 Electricity</a>
                </div>
            </div>
        </div>

        <!-- Mobile Drawer Footer (Auth) -->
        <div class="pt-4 border-top border-light border-opacity-10 mt-4">
            <?php if ($currentUser): ?>
                <div class="d-flex align-items-center justify-content-between mb-3 text-white">
                    <div>
                        <strong class="d-block text-capitalize"><?= e($currentUser['full_name']) ?></strong>
                        <span class="text-white-50 small"><?= e($currentUser['role']) ?></span>
                    </div>
                </div>
                <form method="POST" action="<?= url('logout') ?>" class="m-0">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-outline-danger w-100 rounded-pill py-2">
                        <i class="bi bi-box-arrow-right me-1"></i> <?= e(__('common.nav_logout')) ?>
                    </button>
                </form>
            <?php else: ?>
                <a href="<?= url('login') ?>" class="btn btn-outline-light w-100 rounded-pill py-2 d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-shield-lock"></i>
                    <span><?= e(__('common.nav_staff_login')) ?></span>
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Interactive Quick Track Modal -->
<div class="modal fade" id="quickTrackModal" tabindex="-1" aria-labelledby="quickTrackModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
            <div class="modal-header bg-success text-white py-3 px-4">
                <h5 class="modal-title fw-bold d-flex align-items-center gap-2" id="quickTrackModalLabel">
                    <i class="bi bi-search"></i>
                    <span><?= e(__('common.nav_quick_track')) ?></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= url('track') ?>" method="GET" class="m-0">
                <div class="modal-body p-4">
                    <p class="text-muted small mb-3">
                        <?= $activeLang === 'te' 
                            ? 'మీ Grievance ID మరియు రిజిస్టర్ చేసిన మొబైల్ నంబరుతో వెంటనే స్థితిని పరిశీలించండి.' 
                            : 'Quickly look up grievance timeline using your Tracking ID and registered mobile number.' ?>
                    </p>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark">Grievance ID</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-hash text-muted"></i></span>
                            <input type="text" name="tracking_id" class="form-control border-start-0 font-monospace text-uppercase" placeholder="GRV-2026-XXXXX" required autocomplete="off" style="letter-spacing: 1px;">
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold text-dark">Mobile Number (మొబైల్ నంబర్)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-phone text-muted"></i></span>
                            <input type="tel" name="phone" class="form-control border-start-0 font-monospace" placeholder="10-digit mobile number" pattern="[6-9][0-9]{9}" maxlength="10" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light px-4 py-3">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-semibold shadow-sm d-flex align-items-center gap-2">
                        <i class="bi bi-search"></i>
                        <span><?= e(__('common.nav_track')) ?></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- System Flash Alerts -->
<div class="container mt-3">
    <?php if ($msg = flash('success')): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm d-flex align-items-center gap-2 rounded-4 p-3 border-0 bg-success text-white" role="alert">
            <i class="bi bi-check-circle-fill fs-5"></i>
            <div class="fw-medium"><?= e($msg) ?></div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($msg = flash('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm d-flex align-items-center gap-2 rounded-4 p-3 border-0 bg-danger text-white" role="alert">
            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
            <div class="fw-medium"><?= e($msg) ?></div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
</div>

<main class="flex-grow-1">
