<?php view('layouts/header', ['title' => 'Internal Portal Login - Smart Gram']); ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5">
            
            <!-- Citizen Info Callout Banner -->
            <div class="alert alert-info border-0 shadow-sm rounded-4 mb-4 d-flex align-items-start gap-3 p-3">
                <i class="bi bi-info-circle-fill fs-4 text-info mt-1"></i>
                <div class="small">
                    <strong class="d-block text-dark mb-1">పౌరుల లాగిన్ అవసరం లేదు (Citizen Login NOT Required)</strong>
                    <span class="text-secondary">
                        Citizens do not need an account to report issues. To report a complaint or track an existing one, please visit the citizen portal.
                    </span>
                </div>
            </div>

            <!-- Login Card -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-success text-white py-4 px-4 text-center border-0">
                    <div class="bg-white text-success rounded-circle d-inline-flex align-items-center justify-content-center p-3 mb-2 shadow-sm" style="width: 56px; height: 56px;">
                        <i class="bi bi-shield-lock-fill fs-3"></i>
                    </div>
                    <h4 class="fw-bold mb-1">అధికారుల లాగిన్</h4>
                    <p class="mb-0 text-white-50 small">Internal Staff & Officer Portal</p>
                </div>

                <div class="card-body p-4 p-md-5">
                    <form method="POST" action="<?= url('login') ?>" autocomplete="off">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold text-secondary small">Email Address / ఈమెయిల్ చిరునామా</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control bg-light border-start-0" id="email" name="email" value="<?= e($old_email ?? '') ?>" placeholder="name@smartgram.local" required autofocus>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label fw-semibold text-secondary small">Password / పాస్‌వర్డ్</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-key"></i></span>
                                <input type="password" class="form-control bg-light border-start-0" id="password" name="password" placeholder="••••••••" required>
                                <button class="btn btn-light border border-start-0 text-muted" type="button" id="togglePassword">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success w-100 py-2 rounded-3 fw-semibold shadow-sm d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-box-arrow-in-right"></i> లాగిన్ అవ్వండి (Sign In)
                        </button>
                    </form>

                    <!-- Beginner-Friendly Quick-Fill Test Accounts -->
                    <div class="mt-4 pt-4 border-top">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Quick Fill Test Accounts</span>
                            <span class="badge bg-light text-muted border">Beginner Helper</span>
                        </div>
                        <div class="d-flex flex-column gap-2">
                            <button type="button" class="btn btn-sm btn-outline-primary text-start d-flex justify-content-between align-items-center py-2 px-3 rounded-3" onclick="fillCreds('admin@smartgram.local', 'Admin123!')">
                                <span><i class="bi bi-person-gear me-2"></i><strong>Admin:</strong> admin@smartgram.local</span>
                                <span class="badge bg-primary-subtle text-primary">Admin123!</span>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-success text-start d-flex justify-content-between align-items-center py-2 px-3 rounded-3" onclick="fillCreds('staff@smartgram.local', 'Staff123!')">
                                <span><i class="bi bi-person-badge me-2"></i><strong>Staff:</strong> staff@smartgram.local</span>
                                <span class="badge bg-success-subtle text-success">Staff123!</span>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-warning text-dark text-start d-flex justify-content-between align-items-center py-2 px-3 rounded-3" onclick="fillCreds('officer@smartgram.local', 'Officer123!')">
                                <span><i class="bi bi-person-workspace me-2"></i><strong>Officer:</strong> officer@smartgram.local</span>
                                <span class="badge bg-warning-subtle text-dark">Officer123!</span>
                            </button>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<script>
function fillCreds(email, password) {
    document.getElementById('email').value = email;
    document.getElementById('password').value = password;
}

document.getElementById('togglePassword').addEventListener('click', function() {
    const input = document.getElementById('password');
    const icon = this.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('bi-eye', 'bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('bi-eye-slash', 'bi-eye');
    }
});
</script>

<?php view('layouts/footer'); ?>
