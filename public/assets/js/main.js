// ==============================================================================
// Smart Gram - Interactive Client-side Scripts
// ==============================================================================

document.addEventListener('DOMContentLoaded', () => {
    // ── 1. Scroll-Aware Navbar: elevation + compression + progress bar ────────
    const navbar = document.getElementById('mainSmartNavbar');
    const progressBar = document.getElementById('navProgressBar');

    if (navbar) {
        const handleScroll = () => {
            const scrolled = window.scrollY > 20;
            navbar.classList.toggle('scrolled', scrolled);

            // Update scroll progress bar
            if (progressBar) {
                const docH = document.documentElement.scrollHeight - window.innerHeight;
                const pct  = docH > 0 ? (window.scrollY / docH) * 100 : 0;
                progressBar.style.width = pct + '%';
            }
        };
        window.addEventListener('scroll', handleScroll, { passive: true });
        handleScroll();
    }

    // ── 2. Hamburger ↔ X Animation ────────────────────────────────────────────
    const hamburgerBtn = document.getElementById('mobileHamburgerBtn');
    const mobileDrawer = document.getElementById('mobileNavDrawer');

    if (hamburgerBtn && mobileDrawer) {
        mobileDrawer.addEventListener('show.bs.offcanvas',  () => hamburgerBtn.classList.add('is-open'));
        mobileDrawer.addEventListener('hide.bs.offcanvas',  () => hamburgerBtn.classList.remove('is-open'));
    }

    // ── 3. Accessibility Font Size Controller ─────────────────────────────────
    const body    = document.getElementById('appBody') || document.body;
    const decBtn  = document.getElementById('fontDecBtn');
    const resetBtn= document.getElementById('fontResetBtn');
    const incBtn  = document.getElementById('fontIncBtn');
    const fontBtns= [decBtn, resetBtn, incBtn].filter(Boolean);

    const updateFontButtons = (activeBtn) => {
        fontBtns.forEach(b => b.classList.remove('active'));
        if (activeBtn) activeBtn.classList.add('active');
    };

    const savedFont = localStorage.getItem('smartgram_font_size');
    if (savedFont === 'sm') { body.classList.add('font-size-sm'); updateFontButtons(decBtn); }
    else if (savedFont === 'lg') { body.classList.add('font-size-lg'); updateFontButtons(incBtn); }
    else { updateFontButtons(resetBtn); }

    decBtn?.addEventListener('click', () => {
        body.classList.remove('font-size-lg'); body.classList.add('font-size-sm');
        localStorage.setItem('smartgram_font_size', 'sm'); updateFontButtons(decBtn);
    });
    resetBtn?.addEventListener('click', () => {
        body.classList.remove('font-size-sm', 'font-size-lg');
        localStorage.removeItem('smartgram_font_size'); updateFontButtons(resetBtn);
    });
    incBtn?.addEventListener('click', () => {
        body.classList.remove('font-size-sm'); body.classList.add('font-size-lg');
        localStorage.setItem('smartgram_font_size', 'lg'); updateFontButtons(incBtn);
    });

    // ── 4. Quick Track Keyboard Shortcut (Ctrl+K or '/') ─────────────────────
    document.addEventListener('keydown', (e) => {
        const tag = document.activeElement.tagName;
        if ((e.ctrlKey && e.key.toLowerCase() === 'k') ||
            (e.key === '/' && !['INPUT', 'TEXTAREA', 'SELECT'].includes(tag))) {
            const modalEl = document.getElementById('quickTrackModal');
            if (modalEl && typeof bootstrap !== 'undefined') {
                e.preventDefault();
                const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.show();
                setTimeout(() => {
                    modalEl.querySelector('input[name="tracking_id"]')?.focus();
                }, 380);
            }
        }
        // Escape: close offcanvas if open
        if (e.key === 'Escape' && hamburgerBtn?.classList.contains('is-open')) {
            hamburgerBtn.classList.remove('is-open');
        }
    });

    // ── 5. Quick Track Input Auto-Uppercase ───────────────────────────────────
    document.querySelectorAll('input[name="tracking_id"]').forEach(input => {
        input.addEventListener('input', e => { e.target.value = e.target.value.toUpperCase(); });
    });

    // ── 6. Auto-dismiss Flash Alerts (5 s) ───────────────────────────────────
    document.querySelectorAll('.alert-dismissible').forEach(alert => {
        setTimeout(() => {
            if (typeof bootstrap !== 'undefined') {
                bootstrap.Alert.getOrCreateInstance(alert)?.close();
            }
        }, 5000);
    });

    // ── 7. Active nav link highlight: add subtle underline on current page ────
    const currentPath = window.location.pathname;
    document.querySelectorAll('.nav-link-custom').forEach(link => {
        const href = link.getAttribute('href') || '';
        if (href && currentPath.startsWith(href) && href !== '/') {
            link.classList.add('active');
        }
    });

    // ── 8. Smart Gram AI Chatbot Assistant Client ──────────────────────────────
    const chatbotToggleBtn = document.getElementById('chatbotToggleBtn');
    const chatbotWindow    = document.getElementById('chatbotWindow');
    const chatbotCloseBtn  = document.getElementById('chatbotCloseBtn');
    const chatbotResetBtn  = document.getElementById('chatbotResetBtn');
    const chatbotForm      = document.getElementById('chatbotForm');
    const chatbotInput     = document.getElementById('chatbotInput');
    const chatbotMessages  = document.getElementById('chatbotMessages');
    const iconOpen  = chatbotToggleBtn?.querySelector('.chatbot-icon-open');
    const iconClose = chatbotToggleBtn?.querySelector('.chatbot-icon-close');

    if (chatbotToggleBtn && chatbotWindow) {
        const toggleChatbot = (show) => {
            const isVisible = show !== undefined ? show : chatbotWindow.classList.contains('d-none');
            if (isVisible) {
                chatbotWindow.classList.remove('d-none');
                iconOpen?.classList.add('d-none');
                iconClose?.classList.remove('d-none');
                chatbotInput?.focus();
                scrollToBottom();
            } else {
                chatbotWindow.classList.add('d-none');
                iconOpen?.classList.remove('d-none');
                iconClose?.classList.add('d-none');
            }
        };

        chatbotToggleBtn.addEventListener('click', () => toggleChatbot());
        chatbotCloseBtn?.addEventListener('click', () => toggleChatbot(false));

        chatbotResetBtn?.addEventListener('click', () => {
            if (chatbotMessages) {
                const firstMsg = chatbotMessages.firstElementChild;
                const firstChips = document.getElementById('chatbotInitialQuickReplies');
                chatbotMessages.innerHTML = '';
                if (firstMsg) chatbotMessages.appendChild(firstMsg);
                if (firstChips) chatbotMessages.appendChild(firstChips);
            }
        });

        const scrollToBottom = () => {
            if (chatbotMessages) {
                chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
            }
        };

        const showTypingIndicator = () => {
            removeTypingIndicator();
            const indicatorHtml = `
                <div id="chatbotTypingIndicator" class="d-flex align-items-start gap-2 mb-3">
                    <div class="avatar-circle bg-success text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 30px; height: 30px;">
                        <i class="bi bi-robot small"></i>
                    </div>
                    <div class="bg-white p-3 rounded-3 shadow-sm border border-light text-dark chatbot-msg-bubble d-flex align-items-center gap-1 py-2">
                        <span class="chatbot-typing-dot"></span>
                        <span class="chatbot-typing-dot"></span>
                        <span class="chatbot-typing-dot"></span>
                    </div>
                </div>`;
            chatbotMessages.insertAdjacentHTML('beforeend', indicatorHtml);
            scrollToBottom();
        };

        const removeTypingIndicator = () => {
            document.getElementById('chatbotTypingIndicator')?.remove();
        };

        const appendUserMessage = (msgText) => {
            const safeText = document.createElement('div');
            safeText.textContent = msgText;

            const html = `
                <div class="d-flex justify-content-end mb-3">
                    <div class="chatbot-msg-user p-3 shadow-sm text-white">
                        ${safeText.innerHTML}
                    </div>
                </div>`;
            chatbotMessages.insertAdjacentHTML('beforeend', html);
            scrollToBottom();
        };

        const appendBotMessage = (replyHtml, quickReplies = []) => {
            removeTypingIndicator();

            let chipsHtml = '';
            if (quickReplies && quickReplies.length > 0) {
                chipsHtml = `<div class="mt-2 d-flex flex-wrap gap-1">`;
                quickReplies.forEach(qr => {
                    if (qr.action === 'url') {
                        chipsHtml += `<a href="${qr.url}" class="btn btn-sm btn-success rounded-pill shadow-sm px-2 py-1 small text-decoration-none">${qr.label}</a>`;
                    } else if (qr.action === 'track_prompt' || qr.action === 'quick') {
                        chipsHtml += `<button class="btn btn-sm btn-outline-success rounded-pill bg-white text-dark shadow-sm px-2 py-1 small chatbot-chip" data-action="${qr.action}" data-val="${qr.value || ''}">${qr.label}</button>`;
                    } else {
                        chipsHtml += `<button class="btn btn-sm btn-outline-success rounded-pill bg-white text-dark shadow-sm px-2 py-1 small chatbot-chip" data-action="${qr.action}">${qr.label}</button>`;
                    }
                });
                chipsHtml += `</div>`;
            }

            const html = `
                <div class="d-flex align-items-start gap-2 mb-3">
                    <div class="avatar-circle bg-success text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 30px; height: 30px;">
                        <i class="bi bi-robot small"></i>
                    </div>
                    <div class="bg-white p-3 rounded-3 shadow-sm border border-light text-dark chatbot-msg-bubble">
                        <div>${replyHtml}</div>
                        ${chipsHtml}
                    </div>
                </div>`;
            chatbotMessages.insertAdjacentHTML('beforeend', html);
            scrollToBottom();
        };

        const sendChatbotMessage = async (messageText = '', actionType = '', actionVal = '') => {
            const csrfToken = chatbotForm.getAttribute('data-csrf') || document.querySelector('input[name="_csrf_token"]')?.value || '';

            if (messageText) {
                appendUserMessage(messageText);
                if (chatbotInput) chatbotInput.value = '';
            }

            showTypingIndicator();

            try {
                const response = await fetch('/api/chatbot', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        message: messageText || actionVal,
                        action: actionType,
                        _csrf_token: csrfToken
                    })
                });

                const data = await response.json();
                if (data.success) {
                    appendBotMessage(data.reply, data.quick_replies);
                } else {
                    appendBotMessage(data.reply || 'An error occurred. Please try again.');
                }
            } catch (err) {
                console.error('Chatbot error:', err);
                removeTypingIndicator();
                appendBotMessage('Network connection issue. Please check your connection.');
            }
        };

        // Form Submit
        chatbotForm?.addEventListener('submit', (e) => {
            e.preventDefault();
            const text = chatbotInput?.value.trim();
            if (text) {
                sendChatbotMessage(text);
            }
        });

        // Event delegation for quick reply chips
        document.addEventListener('click', (e) => {
            const chip = e.target.closest('.chatbot-chip');
            if (chip && chatbotWindow.contains(chip)) {
                const action = chip.getAttribute('data-action');
                const val = chip.getAttribute('data-val');
                const label = chip.textContent.trim();
                
                sendChatbotMessage(label, action, val);
            }
        });
    }
});

