<?php 
$activeLang = current_lang();
$user = $user ?? (auth() ?? [
    'full_name' => 'Sri Rajesh Kumar',
    'role' => 'admin',
    'email' => 'admin@smartgram.local'
]);
view('layouts/header', ['title' => 'Panchayat Admin & Staff Operations Console — Smart Gram']); 
?>

<!-- ═══════════════════════════════════════════════════════════════════════════
     SEC-AP & SACIHVALAYAM ADMINISTRATIVE STATUS BAR
     ═══════════════════════════════════════════════════════════════════════════ -->
<div class="py-2 px-3 border-bottom shadow-sm" style="background: #0f3d26; color: #e8f5e9;">
    <div class="container-fluid px-lg-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 small">
            <!-- Left Info -->
            <div class="d-flex flex-wrap align-items-center gap-3">
                <span class="badge bg-success bg-opacity-75 text-white border border-success px-3 py-1 rounded-pill d-inline-flex align-items-center gap-1 font-monospace">
                    <i class="bi bi-shield-check"></i> SEC-AP Certified Admin Console
                </span>
                <span class="text-white-50">|</span>
                <span class="fw-semibold text-white">Thullur Mandal</span>
                <span class="text-white-50">|</span>
                <span class="text-white">Smart Gram Sachivalayam (Wards 1–12)</span>
                <span class="badge bg-white bg-opacity-10 text-white-50 rounded font-monospace" style="font-size: 0.72rem;">GNT 86108</span>
            </div>

            <!-- Right Live Gateway Status -->
            <div class="d-flex flex-wrap align-items-center gap-3 font-monospace" style="font-size: 0.78rem;">
                <span class="d-inline-flex align-items-center gap-1">
                    <span class="status-beacon-container" style="width:10px;height:10px;">
                        <span class="status-beacon-ping" style="background:rgba(32,201,151,0.5);"></span>
                        <span class="status-beacon-dot" style="background:#20c997;width:6px;height:6px;"></span>
                    </span>
                    <strong class="text-white">e-GramSwaraj:</strong> <span class="text-success-emphasis text-light">Sync Live</span>
                </span>
                <span class="text-white-50">•</span>
                <span class="d-inline-flex align-items-center gap-1">
                    <span class="badge bg-info p-1 rounded-circle" style="width:6px;height:6px;"></span>
                    <strong class="text-white">PFMS Gateway:</strong> <span class="text-info">Online</span>
                </span>
                <span class="text-white-50">•</span>
                <span class="text-white-50">CFMS: AP-2026-OK</span>
            </div>
        </div>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════════════════════
     COCKPIT EXECUTIVE HEADER
     ═══════════════════════════════════════════════════════════════════════════ -->
<div class="py-4 bg-white border-bottom shadow-sm">
    <div class="container-fluid px-lg-4">
        <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <i class="bi bi-bank2 text-success fs-5"></i>
                    <span class="text-uppercase fw-bold small text-muted letter-spacing-1">Panchayat Secretariat Administrative Cockpit</span>
                </div>
                <h2 class="fw-bold text-dark mb-1 font-telugu" style="font-size: 1.85rem;">
                    Sachivalayam Executive Operations
                </h2>
                <p class="text-muted small mb-0 font-telugu">
                    గ్రామ సచివాలయ నిర్వహణ, త్వరిత పౌర సమస్యల పరిష్కారం & డిజిటల్ సంతకాల కేంద్రం
                </p>
            </div>

            <!-- Action Toolbar (3 Main Buttons from Screenshot) -->
            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-outline-dark rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2" onclick="window.print()">
                    <i class="bi bi-file-earmark-pdf-fill text-danger"></i>
                    <span>Audit Dossier (PDF)</span>
                </button>
                <button type="button" class="btn btn-warning rounded-pill px-3 py-2 fw-bold text-dark shadow-sm d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#broadcastModal" style="background:#e67e22; border-color:#e67e22; color:#fff !important;">
                    <i class="bi bi-broadcast"></i>
                    <span>Broadcast PA / SMS</span>
                </button>
                <a href="<?= url('submit') ?>" class="btn btn-success rounded-pill px-4 py-2 fw-bold shadow-sm d-flex align-items-center gap-2" style="background:#198754;">
                    <i class="bi bi-plus-circle-fill"></i>
                    <span>Log Grievance</span>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-lg-4 py-4" style="background-color: #f4f6f9;">

    <!-- ═══════════════════════════════════════════════════════════════════════
         ROW 1: 4 KPI METRIC CARDS (Exact Replica from Screenshot)
         ═══════════════════════════════════════════════════════════════════════ -->
    <div class="row g-3 mb-4">
        <!-- 1. Active Redressal SLA -->
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white border-start border-4 border-danger">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-uppercase fw-bold text-muted small" style="font-size:0.75rem;">ACTIVE REDRESSAL SLA</span>
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill font-monospace" style="font-size:0.7rem;">
                        4 Critical
                    </span>
                </div>
                <div class="d-flex align-items-baseline gap-2 mb-2">
                    <span class="display-5 fw-bold text-dark font-monospace">18</span>
                    <span class="text-muted small">Open Tickets</span>
                </div>
                <div class="mb-2">
                    <div class="d-flex justify-content-between small text-muted mb-1" style="font-size:0.72rem;">
                        <span>Resolution Rate (48h SLA)</span>
                        <strong class="text-success font-monospace">89.4%</strong>
                    </div>
                    <div class="progress rounded-pill" style="height:6px;">
                        <div class="progress-bar bg-success rounded-pill" style="width:89.4%;"></div>
                    </div>
                </div>
                <div class="pt-2 border-top d-flex justify-content-between text-muted small" style="font-size:0.72rem;">
                    <span><strong class="text-dark">11</strong> In-Progress</span>
                    <span><strong class="text-dark">3</strong> Queued</span>
                </div>
            </div>
        </div>

        <!-- 2. DSC DSK Queue -->
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white border-start border-4 border-primary">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-uppercase fw-bold text-muted small" style="font-size:0.75rem;">DSC DSK QUEUE</span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill font-monospace" style="font-size:0.7rem;">
                        VRO Cleared
                    </span>
                </div>
                <div class="d-flex align-items-baseline gap-2 mb-2">
                    <span class="display-5 fw-bold text-primary font-monospace">34</span>
                    <span class="text-muted small">Pending Signature</span>
                </div>
                <p class="text-muted small mb-2" style="font-size:0.72rem;">
                    e-Mudhra cryptographic hardware token connected on Slot 1.
                </p>
                <div class="pt-2 border-top d-flex justify-content-between text-muted small" style="font-size:0.72rem;">
                    <span><strong class="text-dark">19</strong> Pensions</span>
                    <span><strong class="text-dark">11</strong> Income/Caste</span>
                    <span><strong class="text-dark">4</strong> Tap Conn</span>
                </div>
            </div>
        </div>

        <!-- 3. Field Force Deployment -->
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white border-start border-4 border-success">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-uppercase fw-bold text-muted small" style="font-size:0.75rem;">FIELD FORCE DEPLOYMENT</span>
                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill font-monospace" style="font-size:0.7rem;">
                        87.5% Present
                    </span>
                </div>
                <div class="d-flex align-items-baseline gap-2 mb-2">
                    <span class="display-5 fw-bold text-success font-monospace">14 <span class="fs-4 text-muted fw-normal">/ 16</span></span>
                    <span class="text-muted small">Staff Active</span>
                </div>
                <div class="mb-2">
                    <div class="progress rounded-pill" style="height:6px;">
                        <div class="progress-bar bg-success rounded-pill" style="width:87.5%;"></div>
                    </div>
                </div>
                <div class="pt-2 border-top d-flex justify-content-between text-muted small" style="font-size:0.72rem;">
                    <span><strong class="text-dark">8</strong> Volunteers</span>
                    <span><strong class="text-dark">3</strong> Linemen</span>
                    <span><strong class="text-dark">3</strong> Sanitation</span>
                </div>
            </div>
        </div>

        <!-- 4. Q3 Revenue Audit -->
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white border-start border-4 border-info">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-uppercase fw-bold text-muted small" style="font-size:0.75rem;">Q3 REVENUE AUDIT</span>
                    <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle rounded-pill font-monospace" style="font-size:0.7rem;">
                        Target: ₹10.0L
                    </span>
                </div>
                <div class="d-flex align-items-baseline gap-2 mb-2">
                    <span class="display-5 fw-bold text-dark font-monospace">₹8.42</span>
                    <span class="text-muted small">Lakhs</span>
                </div>
                <div class="mb-2">
                    <div class="d-flex justify-content-between small text-muted mb-1" style="font-size:0.72rem;">
                        <span>Collection Target Progress</span>
                        <strong class="text-info font-monospace">84.2%</strong>
                    </div>
                    <div class="progress rounded-pill" style="height:6px;">
                        <div class="progress-bar bg-info rounded-pill" style="width:84.2%;"></div>
                    </div>
                </div>
                <div class="pt-2 border-top d-flex justify-content-between text-muted small" style="font-size:0.72rem;">
                    <span>Auto-settled via CFMS</span>
                    <strong class="text-success font-monospace">+12.4% YoY</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════════════
         MAIN OPERATIONS CONSOLE: 2-COLUMN SPLIT (Left 65% / Right 35%)
         ═══════════════════════════════════════════════════════════════════════ -->
    <div class="row g-4">
        
        <!-- ── LEFT COLUMN (65% width) ── -->
        <div class="col-lg-8">

            <!-- 1. Grievance Redressal Dispatch Board -->
            <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden">
                <div class="card-header bg-white border-bottom p-3 p-md-4 d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="status-beacon-container" style="width:10px;height:10px;">
                                <span class="status-beacon-ping" style="background:rgba(220,53,69,0.5);"></span>
                                <span class="status-beacon-dot" style="background:#dc3545;width:6px;height:6px;"></span>
                            </span>
                            <h5 class="fw-bold mb-0 text-dark">Grievance Redressal Dispatch Board</h5>
                        </div>
                        <p class="text-muted small mb-0 font-telugu" style="font-size:0.8rem;">
                            సమస్యల పరిశీలన, ఫీల్డ్ వర్కర్స్ కేటాయింపు మరియు ప్రత్యక్ష GPS స్థితి
                        </p>
                    </div>

                    <!-- Filter Tabs -->
                    <div class="btn-group btn-group-sm rounded-pill p-1 bg-light border shadow-none" role="group">
                        <button type="button" class="btn btn-sm btn-dark rounded-pill px-3 active" id="filterAll">All (18)</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary border-0 rounded-pill px-3" id="filterUrgent">Urgent / SOS (4)</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary border-0 rounded-pill px-3" id="filterAssigned">Assigned (11)</button>
                    </div>
                </div>

                <div class="card-body p-3 p-md-4 d-flex flex-column gap-3">
                    
                    <!-- Dispatch Ticket 1: Borewell Motor Burnt (High Emergency) -->
                    <div class="card border rounded-4 p-3 shadow-none bg-light bg-opacity-50 border-start border-4 border-danger">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-3">
                            <div class="flex-grow-1">
                                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill font-monospace" style="font-size:0.7rem;">GRV-2026-0811</span>
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill" style="font-size:0.7rem;">
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i>High Emergency • Ward 4
                                    </span>
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill font-monospace" style="font-size:0.7rem;">
                                        <i class="bi bi-clock me-1"></i>SLA: 4 hrs left
                                    </span>
                                </div>
                                <h6 class="fw-bold text-dark mb-1 font-telugu" style="font-size:1.02rem;">
                                    Borewell Motor Burnt - SC Colony Main Cistern
                                </h6>
                                <p class="text-muted small mb-2" style="font-size:0.8rem;">
                                    <strong class="text-dark">Reporter:</strong> K. Ramaiah (Resident) &bull; <strong class="text-dark">Assigned:</strong> V. Ramesh (Lead Lineman)
                                </p>
                                <div class="p-2 rounded-3 bg-white border small d-flex align-items-center gap-2 text-secondary" style="font-size:0.75rem;">
                                    <i class="bi bi-geo-alt-fill text-danger"></i>
                                    <span><strong>GPS Telemetry:</strong> En Route (Near Ward 4 Water Overhead Tank #3)</span>
                                </div>
                            </div>
                            <div class="d-flex flex-column gap-2 flex-shrink-0" style="min-width: 140px;">
                                <a href="tel:9876543210" class="btn btn-success btn-sm rounded-pill fw-bold py-2 shadow-sm d-flex align-items-center justify-content-center gap-1">
                                    <i class="bi bi-telephone-fill"></i>
                                    <span>Call Assignee</span>
                                </a>
                                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill py-1 d-flex align-items-center justify-content-center gap-1">
                                    <i class="bi bi-arrow-left-right"></i>
                                    <span>Reassign</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Dispatch Ticket 2: Canine Vaccination Drive (Sanitation) -->
                    <div class="card border rounded-4 p-3 shadow-none bg-light bg-opacity-50 border-start border-4 border-warning">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-3">
                            <div class="flex-grow-1">
                                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill font-monospace" style="font-size:0.7rem;">GRV-2026-0852</span>
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill" style="font-size:0.7rem;">
                                        Sanitation & Vet • Ward 7
                                    </span>
                                    <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill font-monospace" style="font-size:0.7rem;">
                                        <i class="bi bi-clock me-1"></i>SLA: 18 hrs remaining
                                    </span>
                                </div>
                                <h6 class="fw-bold text-dark mb-1 font-telugu" style="font-size:1.02rem;">
                                    Street Dog Canine Vaccination Drive & Safe Disposal
                                </h6>
                                <p class="text-muted small mb-2" style="font-size:0.8rem;">
                                    <strong class="text-dark">Reporter:</strong> P. Sunitha (Ward Volunteer) &bull; <strong class="text-dark">Assigned:</strong> Sanitation Squad Unit 2
                                </p>
                                <div class="p-2 rounded-3 bg-white border small d-flex align-items-center gap-2 text-secondary" style="font-size:0.75rem;">
                                    <i class="bi bi-truck text-primary"></i>
                                    <span><strong>Status:</strong> Van Dispatched from Mandal Veterinary Facility</span>
                                </div>
                            </div>
                            <div class="d-flex flex-column gap-2 flex-shrink-0" style="min-width: 140px;">
                                <button type="button" class="btn btn-outline-primary btn-sm rounded-pill fw-semibold py-1 d-flex align-items-center justify-content-center gap-1">
                                    <i class="bi bi-geo"></i>
                                    <span>Track Vehicle</span>
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill py-1 d-flex align-items-center justify-content-center gap-1">
                                    <i class="bi bi-journal-text"></i>
                                    <span>Update Notes</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Dispatch Ticket 3: Drainage Overflow (Field Work Complete) -->
                    <div class="card border rounded-4 p-3 shadow-none bg-light bg-opacity-50 border-start border-4 border-success">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-3">
                            <div class="flex-grow-1">
                                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill font-monospace" style="font-size:0.7rem;">GRV-2026-0859</span>
                                    <span class="badge bg-success text-white rounded-pill" style="font-size:0.7rem;">
                                        <i class="bi bi-check-circle me-1"></i>Field Work Complete
                                    </span>
                                    <span class="badge bg-light text-secondary border rounded-pill" style="font-size:0.7rem;">
                                        Ward 2 ZP High School Lane
                                    </span>
                                </div>
                                <h6 class="fw-bold text-dark mb-1 font-telugu" style="font-size:1.02rem;">
                                    Drainage Overflow Cleared & Bleaching Disinfected
                                </h6>
                                <p class="text-muted small mb-2" style="font-size:0.8rem;">
                                    <strong class="text-dark">Reporter:</strong> Headmaster, ZP High School &bull; <strong class="text-dark">Supervised by:</strong> S. Appala Naidu
                                </p>
                                <div class="p-2 rounded-3 bg-white border small d-flex align-items-center justify-content-between gap-2 text-secondary" style="font-size:0.75rem;">
                                    <span><i class="bi bi-camera-fill text-success me-1"></i>3 Completion Geo-Photos Uploaded</span>
                                    <a href="#viewPhotosModal" data-bs-toggle="modal" class="text-success text-decoration-none fw-semibold">View Field Photos &rarr;</a>
                                </div>
                            </div>
                            <div class="d-flex flex-column gap-2 flex-shrink-0" style="min-width: 140px;">
                                <button type="button" class="btn btn-success btn-sm rounded-pill fw-bold py-2 shadow-sm d-flex align-items-center justify-content-center gap-1" style="background:#198754;">
                                    <i class="bi bi-check2-circle"></i>
                                    <span>Approve & Close</span>
                                </button>
                                <button type="button" class="btn btn-outline-danger btn-sm rounded-pill py-1 d-flex align-items-center justify-content-center gap-1">
                                    <i class="bi bi-x-circle"></i>
                                    <span>Reject Quality</span>
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- 2. Statutory Certificate & DSC Approval Desk -->
            <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
                <div class="card-header bg-white border-bottom p-3 p-md-4 d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-pen-fill text-primary"></i>
                            <h5 class="fw-bold mb-0 text-dark">Statutory Certificate & DSC Approval Desk</h5>
                        </div>
                        <p class="text-muted small mb-0 font-telugu" style="font-size:0.8rem;">
                            డిజిటల్ కీ (e-Mudhra DSC) ద్వారా ధృవీకరణ పత్రాలు జారీచేయండి
                        </p>
                    </div>
                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill font-monospace px-3 py-1" style="font-size:0.72rem;">
                        <span class="status-beacon-dot d-inline-block bg-success me-1" style="width:6px;height:6px;border-radius:50%;"></span>
                        DSC Token: Connected (Slot 1)
                    </span>
                </div>

                <div class="card-body p-3 p-md-4 d-flex flex-column gap-3">
                    
                    <!-- Certificate 1: Income Certificate -->
                    <div class="card border rounded-4 p-3 bg-light bg-opacity-50">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill font-monospace" style="font-size:0.7rem;">ACRT-0021</span>
                                    <h6 class="fw-bold mb-0 text-dark font-telugu">Annual Income Certificate (₹68,000 / yr)</h6>
                                </div>
                                <p class="text-muted small mb-1" style="font-size:0.78rem;">
                                    <strong>Applicant:</strong> Smt. B. Anasuya (W/o Venkateswarlu, Ward 3) &bull; <strong>Purpose:</strong> Jagananna Vidya Deevena (Fee Reimbursement)
                                </p>
                                <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size:0.7rem;">
                                    <i class="bi bi-check2 me-1"></i>VRO Physical Verification: Cleared & Recommended
                                </span>
                            </div>
                            <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                <button type="button" class="btn btn-success btn-sm rounded-pill fw-bold px-3 py-2 shadow-sm d-flex align-items-center gap-1" style="background:#0f5132;">
                                    <i class="bi bi-key-fill text-warning"></i>
                                    <span>Sign with DSC</span>
                                </button>
                                <button type="button" class="btn btn-link text-danger text-decoration-none small">Reject</button>
                            </div>
                        </div>
                    </div>

                    <!-- Certificate 2: Integrated Caste Certificate -->
                    <div class="card border rounded-4 p-3 bg-light bg-opacity-50">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill font-monospace" style="font-size:0.7rem;">ACRT-0028</span>
                                    <h6 class="fw-bold mb-0 text-dark font-telugu">Integrated Caste & Nativity Certificate (BC-B)</h6>
                                </div>
                                <p class="text-muted small mb-1" style="font-size:0.78rem;">
                                    <strong>Applicant:</strong> K. Naveen (S/o Subba Rao, Ward 10)
                                </p>
                                <span class="text-muted small" style="font-size:0.72rem;">
                                    DigiLocker 10th Marks & Family Ration Card Linked &bull; Ward Sachivalayam Verification Done
                                </span>
                            </div>
                            <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                <button type="button" class="btn btn-primary btn-sm rounded-pill fw-bold px-3 py-2 shadow-sm d-flex align-items-center gap-1">
                                    <i class="bi bi-qr-code"></i>
                                    <span>Approve & Issue QR</span>
                                </button>
                                <button type="button" class="btn btn-link text-danger text-decoration-none small">Reject</button>
                            </div>
                        </div>
                    </div>

                    <!-- Certificate 3: Jal Jeevan Mission Tap -->
                    <div class="card border rounded-4 p-3 bg-light bg-opacity-50">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle rounded-pill font-monospace" style="font-size:0.7rem;">ATAP-0032</span>
                                    <h6 class="fw-bold mb-0 text-dark font-telugu">Jal Jeevan Mission Domestic Tap Connection</h6>
                                </div>
                                <p class="text-muted small mb-1" style="font-size:0.78rem;">
                                    <strong>Applicant:</strong> M. Venkat (Plot 42, Ward 5 North)
                                </p>
                                <span class="text-muted small" style="font-size:0.72rem;">
                                    Fee Calculated: ₹1,200 (Metered Installation) &bull; Engineering Assistant Pipeline Clearance OK
                                </span>
                            </div>
                            <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                <button type="button" class="btn btn-success btn-sm rounded-pill fw-bold px-3 py-2 shadow-sm d-flex align-items-center gap-1" style="background:#198754;">
                                    <i class="bi bi-receipt"></i>
                                    <span>Emit Demand Note</span>
                                </button>
                                <button type="button" class="btn btn-link text-secondary text-decoration-none small">Review</button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>

        <!-- ── RIGHT COLUMN (35% width) ── -->
        <div class="col-lg-4">

            <!-- 1. Village Asset Telemetry -->
            <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden">
                <div class="card-header bg-white border-bottom p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-broadcast-pin text-primary"></i>
                            <h6 class="fw-bold mb-0 text-dark">Village Asset Telemetry</h6>
                        </div>
                        <span class="text-muted small font-telugu" style="font-size:0.72rem;">రీడింగ్స్ మరియు ఆటోమేటెడ్ ఫాల్ట్ డిటెక్షన్ వ్యవస్థ</span>
                    </div>
                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill font-monospace" style="font-size:0.68rem;">
                        <span class="status-beacon-dot d-inline-block bg-success me-1" style="width:5px;height:5px;border-radius:50%;"></span>
                        Live Sensors
                    </span>
                </div>

                <div class="card-body p-3 d-flex flex-column gap-3">
                    
                    <!-- Telemetry 1: Drinking Water Tanks -->
                    <div class="p-3 rounded-3 border bg-light bg-opacity-50">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <strong class="small text-dark d-flex align-items-center gap-1">
                                <i class="bi bi-droplet-half text-primary"></i> Drinking Water Tanks
                            </strong>
                            <span class="badge bg-danger-subtle text-danger font-monospace" style="font-size:0.68rem;">1 Warning | 11 Normal</span>
                        </div>
                        <div class="text-muted small mb-2" style="font-size:0.72rem;">12 Overhead Reservoirs</div>
                        <div class="p-2 rounded bg-danger bg-opacity-10 border border-danger border-opacity-25 small text-danger" style="font-size:0.72rem; line-height:1.4;">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                            <strong>Tank #7 (Ward 4):</strong> Low Chlorination residual detected (0.12 ppm). Auto-alert sent to operator.
                        </div>
                    </div>

                    <!-- Telemetry 2: CCMS Smart Streetlights -->
                    <div class="p-3 rounded-3 border bg-light bg-opacity-50">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <strong class="small text-dark d-flex align-items-center gap-1">
                                <i class="bi bi-lightbulb-fill text-warning"></i> CCMS Smart Streetlights
                            </strong>
                            <span class="badge bg-success-subtle text-success font-monospace" style="font-size:0.68rem;">97.7% Active</span>
                        </div>
                        <div class="text-muted small mb-2" style="font-size:0.72rem;">482 CCMS Connected Units &bull; <strong>471 Lit</strong></div>
                        <div class="p-2 rounded bg-warning bg-opacity-10 border border-warning border-opacity-25 small text-dark" style="font-size:0.72rem; line-height:1.4;">
                            <i class="bi bi-lightning-charge-fill text-warning me-1"></i>
                            11 Tripped breakers: Ward 3 (5), Ward 9 (6). Lineman ticket auto-generated.
                        </div>
                    </div>

                    <!-- Telemetry 3: Solar RO Water Plants -->
                    <div class="p-3 rounded-3 border bg-light bg-opacity-50">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <strong class="small text-dark d-flex align-items-center gap-1">
                                <i class="bi bi-sun-fill text-warning"></i> Solar RO Water Plants
                            </strong>
                            <span class="badge bg-success text-white font-monospace" style="font-size:0.68rem;">3/3 Running</span>
                        </div>
                        <div class="text-muted small mb-2" style="font-size:0.72rem;">TDS Average: <strong>110 ppm</strong> &bull; BIS Potable Grade</div>
                        <span class="text-muted small" style="font-size:0.72rem;">Next scheduled filter membrane flush: in 14 days (Auto-log).</span>
                    </div>

                    <!-- Telemetry 4: Solid Waste EV Fleet -->
                    <div class="p-3 rounded-3 border bg-light bg-opacity-50">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <strong class="small text-dark d-flex align-items-center gap-1">
                                <i class="bi bi-truck-front-fill text-success"></i> Solid Waste EV Fleet
                            </strong>
                            <span class="badge bg-success text-white font-monospace" style="font-size:0.68rem;">4 Carts Active</span>
                        </div>
                        <div class="text-muted small mb-1" style="font-size:0.72rem;">Morning Route Coverage: <strong>Wards 1–6 (100%)</strong></div>
                        <span class="text-muted small" style="font-size:0.72rem;">Fleet Avg Battery: 86% &bull; Dump Yard Route Pending</span>
                    </div>

                </div>
            </div>

            <!-- 2. Gram Sabha Agenda Desk -->
            <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
                <div class="card-header bg-white border-bottom p-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-calendar-check-fill text-success"></i>
                        <h6 class="fw-bold mb-0 text-dark">Gram Sabha Agenda Desk</h6>
                    </div>
                    <span class="text-muted small font-telugu" style="font-size:0.72rem;">రాబోవు గ్రామసభ సమావేశ నోటీసు & ప్రజా ప్రచార కేంద్రం</span>
                </div>

                <div class="card-body p-3">
                    <form id="gramSabhaAgendaForm" onsubmit="event.preventDefault(); alert('Gram Sabha Notice broadcasted to 2,412 registered citizens successfully!');">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-dark">Meeting Resolution Title</label>
                            <input type="text" class="form-control form-control-sm rounded-3" value="Special Gram Sabha on Q4 Village Budget & Allocations" required>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-dark">Scheduled Date</label>
                                <input type="text" class="form-control form-control-sm rounded-3 font-monospace" value="24-03-2026" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-dark">Venue</label>
                                <input type="text" class="form-control form-control-sm rounded-3" value="Panchayat Hall" required>
                            </div>
                        </div>

                        <label class="form-label small fw-semibold text-dark mb-1">Broadcast Target Audience</label>
                        <div class="p-2 rounded-3 bg-light border mb-3 small" style="font-size:0.76rem;">
                            <div class="form-check mb-1">
                                <input class="form-check-input" type="checkbox" checked id="aud1">
                                <label class="form-check-label" for="aud1">2,412 Registered Household Heads (SMS / IVRS)</label>
                            </div>
                            <div class="form-check mb-1">
                                <input class="form-check-input" type="checkbox" checked id="aud2">
                                <label class="form-check-label" for="aud2">Village Public PA System Automated Chime</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" checked id="aud3">
                                <label class="form-check-label" for="aud3">Ward Volunteer Mobile Group Dispatch</label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success btn-sm w-100 rounded-pill py-2 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2" style="background:#198754;">
                            <i class="bi bi-send-fill"></i>
                            <span>Publish Notice & Trigger Broadcast</span>
                        </button>
                    </form>
                </div>
            </div>

        </div>

    </div>

</div>

<!-- Broadcast Modal -->
<div class="modal fade" id="broadcastModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-dark text-white p-3">
                <h6 class="modal-title fw-bold"><i class="bi bi-broadcast me-2 text-warning"></i>Emergency Village PA & SMS Broadcast</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted small">Send immediate broadcast message across village loudspeakers and SMS network.</p>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Broadcast Channel</label>
                    <select class="form-select form-select-sm">
                        <option>Village PA Speakers + SMS + WhatsApp</option>
                        <option>Ward Volunteers Only</option>
                        <option>Drinking Water Emergency Alert</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Message (Telugu & English)</label>
                    <textarea class="form-control form-control-sm" rows="3" placeholder="ప్రజలందరికీ గమనిక: రేపు ఉదయం 7 గంటలకు తాగునీటి సరఫరా ఉంటుంది..."></textarea>
                </div>
            </div>
            <div class="modal-footer p-3 bg-light">
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-sm btn-danger rounded-pill px-4" onclick="alert('Broadcast sent successfully!');" data-bs-dismiss="modal">Send Broadcast</button>
            </div>
        </div>
    </div>
</div>

<?php view('layouts/footer'); ?>
