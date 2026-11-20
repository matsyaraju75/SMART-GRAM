</main>

<!-- Footer -->
<footer class="bg-white border-top py-4 mt-5">
    <div class="container text-center text-muted small">
        <div class="row align-items-center gy-3">
            <div class="col-md-6 text-md-start">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="fw-bold text-success fs-6"><?= e(__('common.app_name')) ?></span>
                    <span class="text-secondary">| <?= e(__('common.app_title')) ?></span>
                </div>
                <div class="text-secondary"><?= e(__('common.footer_tagline')) ?></div>
                <div class="text-muted" style="font-size: 0.78rem;"><?= e(__('common.footer_panchayat')) ?></div>
            </div>
            <div class="col-md-6 text-md-end">
                <div class="mb-2">
                    <span class="text-muted">&copy; <?= date('Y') ?> Smart Gram. <?= e(__('common.copyright')) ?>.</span>
                </div>
                <div class="d-flex justify-content-md-end justify-content-center gap-2 align-items-center">
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill">
                        Phase 2: Bilingual UI Active
                    </span>
                    <a href="<?= switch_lang_url(current_lang() === 'te' ? 'en' : 'te') ?>" class="text-decoration-none text-muted small">
                        <i class="bi bi-translate"></i> <?= current_lang() === 'te' ? 'Switch to English' : 'తెలుగులోకి మార్చండి' ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Smart Gram AI Chatbot Widget -->
<div id="smartgramChatbotContainer" class="chatbot-container position-fixed bottom-0 end-0 p-3" style="z-index: 1080;">
    <!-- Floating Trigger Button -->
    <button id="chatbotToggleBtn" class="btn btn-success rounded-circle shadow-lg d-flex align-items-center justify-content-center chatbot-trigger-btn" aria-label="Open Smart Gram Assistant" title="<?= e(current_lang() === 'te' ? 'స్మార్ట్ గ్రామ్ AI సహాయకుడు' : 'Smart Gram AI Assistant') ?>">
        <i class="bi bi-robot fs-3 chatbot-icon-open"></i>
        <i class="bi bi-x-lg fs-3 chatbot-icon-close d-none"></i>
        <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle chatbot-badge-pulse">
            <span class="visually-hidden">Active assistant</span>
        </span>
    </button>

    <!-- Chat Window Card -->
    <div id="chatbotWindow" class="card shadow-lg border-0 rounded-4 chatbot-window d-none mt-2">
        <!-- Chat Header -->
        <div class="card-header bg-success text-white py-3 px-3 rounded-top-4 d-flex align-items-center justify-content-between border-0 shadow-sm" style="background: linear-gradient(135deg, var(--primary) 0%, var(--primary-deep) 100%) !important;">
            <div class="d-flex align-items-center gap-2">
                <div class="position-relative">
                    <div class="avatar-circle bg-white text-success d-flex align-items-center justify-content-center rounded-circle fw-bold shadow-sm" style="width: 38px; height: 38px;">
                        <i class="bi bi-robot fs-5"></i>
                    </div>
                    <span class="position-absolute bottom-0 end-0 p-1 bg-success border border-white rounded-circle"></span>
                </div>
                <div>
                    <h6 class="mb-0 fw-bold lh-1 text-white"><?= e(current_lang() === 'te' ? 'గ్రామ సహాయకుడు (AI)' : 'Smart Gram AI Assistant') ?></h6>
                    <small class="text-white-50 opacity-75" style="font-size: 0.75rem;">
                        <i class="bi bi-circle-fill text-accent small me-1"></i><?= e(current_lang() === 'te' ? '24/7 అందుబాటులో ఉన్నారు' : '24x7 Active Support') ?>
                    </small>
                </div>
            </div>
            <div class="d-flex align-items-center gap-1">
                <button type="button" class="btn btn-sm btn-link text-white text-decoration-none p-1 opacity-75 hover-opacity-100" id="chatbotResetBtn" title="Reset Chat">
                    <i class="bi bi-arrow-counterclockwise fs-6"></i>
                </button>
                <button type="button" class="btn-close btn-close-white ms-1" id="chatbotCloseBtn" aria-label="Close"></button>
            </div>
        </div>

        <!-- Chat Body -->
        <div class="card-body p-3 overflow-y-auto chatbot-messages-body" id="chatbotMessages" style="height: 340px; background-color: #f8fafc;">
            <!-- Welcome Message -->
            <div class="d-flex align-items-start gap-2 mb-3">
                <div class="avatar-circle bg-success text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 30px; height: 30px;">
                    <i class="bi bi-robot small"></i>
                </div>
                <div class="bg-white p-3 rounded-3 shadow-sm border border-light text-dark chatbot-msg-bubble">
                    <p class="mb-1 fw-medium">
                        <?= e(current_lang() === 'te' 
                            ? 'నమస్కారం! 🙏 నేను స్మార్ట్ గ్రామ్ AI సహాయకుడిని.' 
                            : 'Hello! 🙏 Welcome to Smart Gram AI Assistant.') ?>
                    </p>
                    <p class="mb-0 text-muted small">
                        <?= e(current_lang() === 'te'
                            ? 'తాగునీరు, విద్యుత్, రోడ్లు, చెత్త సమస్యలపై ఫిర్యాదు చేయడానికి లేదా మీ ఫిర్యాదు స్థితి తెలుసుకోవడానికి నన్ను అడగండి.'
                            : 'Ask me to track a grievance, guide you through submission, or check village emergency numbers.') ?>
                    </p>
                </div>
            </div>

            <!-- Quick Action Chips -->
            <div class="mb-3 ps-4" id="chatbotInitialQuickReplies">
                <div class="d-flex flex-wrap gap-1">
                    <button class="btn btn-sm btn-outline-success rounded-pill bg-white text-dark shadow-sm px-2 py-1 small chatbot-chip" data-action="track_prompt">
                        🔍 <?= e(current_lang() === 'te' ? 'ఫిర్యాదు స్థితి' : 'Track Status') ?>
                    </button>
                    <button class="btn btn-sm btn-outline-success rounded-pill bg-white text-dark shadow-sm px-2 py-1 small chatbot-chip" data-action="how_to_submit">
                        📝 <?= e(current_lang() === 'te' ? 'ఫిర్యాదు ఇవ్వడం ఎలా?' : 'How to File') ?>
                    </button>
                    <button class="btn btn-sm btn-outline-success rounded-pill bg-white text-dark shadow-sm px-2 py-1 small chatbot-chip" data-action="helpline">
                        📞 <?= e(current_lang() === 'te' ? 'హెల్ప్‌లైన్ నంబర్లు' : 'Helplines') ?>
                    </button>
                    <button class="btn btn-sm btn-outline-success rounded-pill bg-white text-dark shadow-sm px-2 py-1 small chatbot-chip" data-action="working_hours">
                        ⏰ <?= e(current_lang() === 'te' ? 'పని వేళలు' : 'Working Hours') ?>
                    </button>
                </div>
            </div>
        </div>

        <!-- Dynamic Quick Replies Container -->
        <div class="px-3 py-2 bg-light border-top border-light d-none" id="chatbotQuickReplies">
            <div class="d-flex flex-wrap gap-1"></div>
        </div>

        <!-- Chat Input Footer -->
        <div class="card-footer bg-white p-2 border-top border-light rounded-bottom-4">
            <form id="chatbotForm" class="d-flex align-items-center gap-2 m-0" data-csrf="<?= e(csrf_token()) ?>">
                <input type="hidden" name="_csrf_token" value="<?= e(csrf_token()) ?>">
                <input type="text" id="chatbotInput" class="form-control form-control-sm rounded-pill border-light-subtle px-3 py-2" 
                       placeholder="<?= e(current_lang() === 'te' ? 'సందేశం లేదా GRV-2026-00001 టైప్ చేయండి...' : 'Type message or GRV-2026-00001...') ?>" 
                       autocomplete="off" required>
                <button type="submit" id="chatbotSendBtn" class="btn btn-success rounded-circle p-0 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 36px; height: 36px;">
                    <i class="bi bi-send-fill text-white fs-6"></i>
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap 5 Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Custom JavaScript -->
<script src="<?= asset('js/main.js') ?>"></script>
</body>
</html>
