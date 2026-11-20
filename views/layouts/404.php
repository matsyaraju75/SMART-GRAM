<?php view('layouts/header', ['title' => 'Page Not Found - Smart Gram']); ?>

<div class="container py-5 text-center">
    <div class="py-5">
        <div class="display-1 fw-bold text-success mb-3">404</div>
        <h2 class="fw-bold mb-3">Page Not Found / పేజీ కనుగొనబడలేదు</h2>
        <p class="text-muted mb-4">The page you requested could not be found or has moved.</p>
        <a href="<?= url('') ?>" class="btn btn-success px-4 py-2 rounded-pill">
            <i class="bi bi-house me-1"></i> Return Home (హోమ్‌కి వెళ్లండి)
        </a>
    </div>
</div>

<?php view('layouts/footer'); ?>
