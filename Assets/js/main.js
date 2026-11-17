/**
 * SmartService AI - Core Frontend Logic
 */
document.addEventListener('DOMContentLoaded', () => {
    console.log('SmartService AI System Ready.');

    // Initialize tooltips if Bootstrap is present
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
    }

    // Floating Chatbot Widget Logic
    const chatbotToggleBtn = document.getElementById('chatbot-toggle-btn');
    const chatbotCloseBtn = document.getElementById('chatbot-close-btn');
    const chatbotWindow = document.getElementById('chatbot-window');
    const chatbotForm = document.getElementById('chatbot-form');
    const chatbotInput = document.getElementById('chatbot-input');
    const chatbotMessages = document.getElementById('chatbot-messages');
    
    if (chatbotToggleBtn && chatbotWindow && chatbotForm) {
        chatbotToggleBtn.addEventListener('click', () => {
            chatbotWindow.classList.remove('d-none');
            chatbotToggleBtn.classList.add('d-none');
            chatbotInput.focus();
        });

        chatbotCloseBtn.addEventListener('click', () => {
            chatbotWindow.classList.add('d-none');
            chatbotToggleBtn.classList.remove('d-none');
        });

        chatbotForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const message = chatbotInput.value.trim();
            if (message.length < 2) return;
            
            const csrfToken = document.getElementById('chatbot-csrf').value;
            
            // Add User Message
            addMessage(message, 'user');
            chatbotInput.value = '';
            
            // Add Typing Indicator
            const typingId = showTypingIndicator();
            
            try {
                const formData = new FormData();
                formData.append('message', message); // changed from question
                formData.append('csrf_token', csrfToken);

                // Base URL context
                const baseUrl = window.APP_BASE_URL || (window.location.origin + window.location.pathname.split('/').slice(0, 2).join('/'));
                
                const response = await fetch(`${baseUrl}/api/chatbot`, {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                
                removeTypingIndicator(typingId);
                
                const data = await response.json();
                
                if (data.status === 'success') {
                    addMessage(data.reply, 'ai');
                } else {
                    addMessage('Maaf, ' + (data.message || 'terjadi kesalahan jaringan.'), 'ai');
                }
            } catch (err) {
                removeTypingIndicator(typingId);
                addMessage('Maaf, saya tidak dapat terhubung ke server saat ini.', 'ai');
                console.error(err);
            }
        });

        // Chatbot Quick Model Switcher Logic
        const modelSwitchItems = document.querySelectorAll('.chatbot-switch-model-item');
        const activeModelNameEl = document.getElementById('chatbot-active-model-name');
        const barModelTextEl = document.getElementById('chatbot-bar-model-text');

        modelSwitchItems.forEach(item => {
            item.addEventListener('click', async (e) => {
                e.preventDefault();
                const provider = item.getAttribute('data-provider');
                const modelId = item.getAttribute('data-model');
                const provName = item.getAttribute('data-name') || provider;
                const csrfToken = document.getElementById('chatbot-csrf')?.value || '';
                const resolvedBaseUrl = window.APP_BASE_URL || (window.location.origin + window.location.pathname.split('/').slice(0, 2).join('/'));

                try {
                    const fd = new FormData();
                    fd.append('provider', provider);
                    fd.append('model_id', modelId);
                    fd.append('csrf_token', csrfToken);

                    // Add transition message in chat
                    addMessage(`🔄 Mengalihkan model AI ke <strong>${provName} (${modelId})</strong>...`, 'ai');

                    const resp = await fetch(`${resolvedBaseUrl}/api/chatbot/switch-model`, {
                        method: 'POST',
                        body: fd,
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });

                    const res = await resp.json();

                    if (res.status === 'success') {
                        // Update UI button and bar
                        if (activeModelNameEl) activeModelNameEl.textContent = res.provider_name;
                        if (barModelTextEl) barModelTextEl.textContent = `${res.provider_name} (${res.model_id})`;

                        // Update active class in dropdown items
                        modelSwitchItems.forEach(i => i.classList.remove('active', 'fw-bold'));
                        item.classList.add('active', 'fw-bold');

                        addMessage(`✅ <strong>Model AI Aktif:</strong> Berhasil beralih ke <strong>${res.provider_name}</strong> (<code>${res.model_id}</code>). Siap melayani pertanyaan atau konsultasi Anda! 😊`, 'ai');
                    } else {
                        addMessage(`⚠️ Gagal beralih: ${res.message || 'Terjadi kesalahan'}. Silakan atur API Key di <a href="${resolvedBaseUrl}/admin/ai-config" class="fw-bold">Menu Konfigurasi AI</a>.`, 'ai');
                    }
                } catch (err) {
                    console.error('Error switching AI model:', err);
                    addMessage('⚠️ Terjadi gangguan koneksi saat beralih model AI.', 'ai');
                }
            });
        });
        
        function addMessage(text, sender) {
            const msgDiv = document.createElement('div');
            msgDiv.className = `chat-message ${sender}-message`;
            
            const contentDiv = document.createElement('div');
            contentDiv.className = 'message-content';
            
            // Clean asterisk (*) symbols for AI messages
            let cleanedText = text;
            if (sender === 'ai' && typeof cleanedText === 'string') {
                cleanedText = cleanedText.replace(/\*/g, '');
            }
            
            contentDiv.innerHTML = cleanedText;
            
            msgDiv.appendChild(contentDiv);
            chatbotMessages.appendChild(msgDiv);
            chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
        }
        
        function showTypingIndicator() {
            const id = 'typing-' + Date.now();
            const msgDiv = document.createElement('div');
            msgDiv.className = `chat-message ai-message`;
            msgDiv.id = id;
            
            const contentDiv = document.createElement('div');
            contentDiv.className = 'message-content typing-indicator';
            contentDiv.innerHTML = '<div class="typing-dot"></div><div class="typing-dot"></div><div class="typing-dot"></div>';
            
            msgDiv.appendChild(contentDiv);
            chatbotMessages.appendChild(msgDiv);
            chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
            return id;
        }
        
        function removeTypingIndicator(id) {
            const el = document.getElementById(id);
            if (el) el.remove();
        }
    }

    // Number Count-Up Animation for Dashboard Metric Cards
    const countElements = document.querySelectorAll('.stat-card .fs-2, .stat-card .fs-3');
    countElements.forEach((el) => {
        const text = el.innerText.trim();
        const target = parseInt(text, 10);
        if (!isNaN(target) && target > 0) {
            let current = 0;
            const duration = 800; // ms
            const step = Math.max(1, Math.ceil(target / (duration / 20)));
            el.innerText = '0';
            const timer = setInterval(() => {
                current += step;
                if (current >= target) {
                    el.innerText = target;
                    clearInterval(timer);
                } else {
                    el.innerText = current;
                }
            }, 20);
        }
    });

    // Smooth Scroll for Internal Page Anchors
    document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
        anchor.addEventListener('click', function (e) {
            const targetId = this.getAttribute('href');
            if (targetId && targetId.length > 1 && !targetId.includes('tab-')) {
                const targetEl = document.querySelector(targetId);
                if (targetEl) {
                    e.preventDefault();
                    targetEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }
        });
    });

    // Technician Robot Mascot Interaction (Pojok Kiri)
    const robotWidget = document.getElementById('technician-robot-widget');
    const robotSpeech = document.getElementById('robot-speech-text');
    if (robotWidget && robotSpeech) {
        const phrases = [
            "Halo! Siap bantu servis 🛠️",
            "Kulkas/AC bermasalah? Tanya yuk! 😊",
            "Butuh diagnosis AI? Buka Chatbot di kanan 👉",
            "Teknisi siap panggilan ke rumah! 🚀",
            "Peralatan rumah tangga aman bersama kami! ✨"
        ];
        let phraseIdx = 0;

        robotWidget.addEventListener('click', () => {
            phraseIdx = (phraseIdx + 1) % phrases.length;
            robotSpeech.innerHTML = `<span>${phrases[phraseIdx]}</span>`;
            robotSpeech.style.transform = 'scale(1.15)';
            setTimeout(() => {
                robotSpeech.style.transform = 'scale(1)';
            }, 250);
        });
    }
});


