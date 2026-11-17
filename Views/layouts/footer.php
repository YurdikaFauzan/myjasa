<?php
$baseUrl = \Config\Env::get('APP_URL', 'http://localhost/Myjasagwuehhhhhhhhhhhhhhhhhhhhh');
$assetUrl = rtrim($baseUrl, '/') . '/Assets';
?>
    </main>

    <?php if (empty($isWelcomePage)): ?>
    <!-- Footer -->
    <footer class="footer-custom mt-auto">
        <div class="container">
            <div class="row gy-4 align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    <div class="d-flex align-items-center justify-content-center justify-content-md-start gap-2 mb-2">
                        <i class="bi bi-cpu-fill text-primary"></i>
                        <strong class="text-white">SmartService AI</strong>
                    </div>
                    <p class="small mb-0 text-secondary">
                        Sistem Pemesanan Perbaikan & Perawatan Peralatan Rumah Tangga berbasis Web MVC.
                    </p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <p class="small mb-1 text-secondary">&copy; <?= date('Y') ?> SmartService AI. All rights reserved.</p>
                    <div class="d-inline-flex gap-3 small">
                        <span class="badge bg-secondary text-light">Fondasi System Version 1.0</span>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <?php endif; ?>

    <?php if (empty($hideFloatingWidgets)): ?>
    <!-- Floating Technician Robot Mascot (Pojok Kiri) -->
    <div id="technician-robot-widget" class="technician-robot-widget" title="Klik saya untuk bertegur sapa!">
        <div class="robot-speech-bubble" id="robot-speech-text">
            <span>Halo! Siap bantu servis 🛠️</span>
        </div>
        <svg class="technician-robot-svg" viewBox="0 0 160 210" width="125" height="165" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <!-- Casing Grad: Warm Caramel Chocolate -->
                <linearGradient id="robotCasingGrad" x1="0" y1="0" x2="1" y2="1">
                    <stop offset="0%" stop-color="#8c4d22" />
                    <stop offset="100%" stop-color="#5a2d10" />
                </linearGradient>
                
                <!-- White/Cream Bezel Grad -->
                <linearGradient id="robotBezelGrad" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#ffffff" />
                    <stop offset="100%" stop-color="#f4eae1" />
                </linearGradient>

                <!-- Screen Visor Grad -->
                <linearGradient id="robotScreenGrad" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#221610" />
                    <stop offset="100%" stop-color="#160e0a" />
                </linearGradient>

                <!-- Antenna Bulb Grad (Solid/Shaded, strictly NO GLOW) -->
                <linearGradient id="antennaBulbGrad" x1="0" y1="0" x2="1" y2="1">
                    <stop offset="0%" stop-color="#f6a028" />
                    <stop offset="100%" stop-color="#d35400" />
                </linearGradient>

                <!-- Thruster Flame Grad -->
                <linearGradient id="thrusterFireGrad" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#ffd166" />
                    <stop offset="55%" stop-color="#f78c3b" />
                    <stop offset="100%" stop-color="rgba(247, 140, 59, 0)" />
                </linearGradient>

                <!-- Subtle Clean Body Depth Drop Shadow (Tanpa Efek Cahaya / Non-glowing) -->
                <filter id="robotSubtleShadow" x="-10%" y="-10%" width="130%" height="130%">
                    <feDropShadow dx="0" dy="4" stdDeviation="3" flood-color="#4a220a" flood-opacity="0.18"/>
                </filter>
            </defs>

            <!-- Ground Shadow (statis di lantai, animasi scale & fade sinkron 3.0s) -->
            <ellipse cx="80" cy="198" rx="34" ry="6" fill="rgba(90, 45, 16, 0.22)" class="robot-ground-shadow" />

            <!-- Entire Floating Group (Badan naik-turun ±3s per loop) -->
            <g class="robot-float-group">
                <!-- Thruster Flame -->
                <g class="robot-thruster-flame">
                    <path d="M72 150 Q80 186 88 150 Z" fill="url(#thrusterFireGrad)" />
                    <path d="M75 151 Q80 172 85 151 Z" fill="#fff9db" />
                </g>

                <!-- Jet Thruster Cone (Nozzle) -->
                <path d="M69 138 L91 138 L86 152 L74 152 Z" fill="#475569" stroke="#334155" stroke-width="1.2" />
                <ellipse cx="80" cy="151.5" rx="6.5" ry="2" fill="#1e293b" />

                <!-- Torso / Body -->
                <g filter="url(#robotSubtleShadow)">
                    <!-- Outer body capsule -->
                    <rect x="52" y="90" width="56" height="50" rx="22" fill="url(#robotCasingGrad)" stroke="#452009" stroke-width="2.2" />
                    <!-- Cream tummy patch -->
                    <rect x="62" y="96" width="36" height="38" rx="14" fill="url(#robotBezelGrad)" stroke="#e2d4c8" stroke-width="1.2" />
                    <!-- Mini tech square on tummy -->
                    <rect x="73" y="105" width="14" height="14" rx="4" fill="#debfa8" stroke="#be9e85" stroke-width="1" />
                </g>

                <!-- Left Arm (Viewer's Left - Rests naturally down with breathing) -->
                <g class="robot-left-arm">
                    <path d="M54 104 Q40 114 38 132" fill="none" stroke="url(#robotCasingGrad)" stroke-width="8" stroke-linecap="round" />
                    <path d="M54 104 Q40 114 38 132" fill="none" stroke="#452009" stroke-width="1.8" stroke-linecap="round" />
                    <!-- Mitten Paw -->
                    <circle cx="38" cy="132" r="6" fill="#fcf9f5" stroke="#452009" stroke-width="1.5" />
                    <circle cx="34" cy="130" r="2.5" fill="#fcf9f5" />
                </g>

                <!-- Right Arm (Viewer's Right - Waving Arm, pivot at 108, 104) -->
                <g class="robot-right-arm">
                    <path d="M106 104 Q120 114 122 132" fill="none" stroke="url(#robotCasingGrad)" stroke-width="8" stroke-linecap="round" />
                    <path d="M106 104 Q120 114 122 132" fill="none" stroke="#452009" stroke-width="1.8" stroke-linecap="round" />
                    <!-- Mitten Paw -->
                    <circle cx="122" cy="132" r="6" fill="#fcf9f5" stroke="#452009" stroke-width="1.5" />
                    <circle cx="126" cy="130" r="2.5" fill="#fcf9f5" />
                </g>

                <!-- Neck Joint -->
                <rect x="74" y="84" width="12" height="8" rx="3" fill="#3e2312" />

                <!-- Head Group -->
                <g filter="url(#robotSubtleShadow)">
                    <!-- Ear Caps -->
                    <rect x="29" y="45" width="10" height="26" rx="5" fill="#5a2d10" stroke="#452009" stroke-width="1.5" />
                    <rect x="32" y="49" width="4" height="18" rx="2" fill="#debfa8" opacity="0.6" />
                    
                    <rect x="121" y="45" width="10" height="26" rx="5" fill="#5a2d10" stroke="#452009" stroke-width="1.5" />
                    <rect x="124" y="49" width="4" height="18" rx="2" fill="#debfa8" opacity="0.6" />

                    <!-- Antenna (Tanpa Cahaya / Solid Clean Ball) -->
                    <line x1="80" y1="26" x2="80" y2="12" stroke="#452009" stroke-width="3" stroke-linecap="round" />
                    <circle cx="80" cy="10" r="6" fill="url(#antennaBulbGrad)" stroke="#452009" stroke-width="2" class="robot-antenna-bulb" />

                    <!-- Outer Helmet Casing -->
                    <rect x="37" y="26" width="86" height="62" rx="24" fill="url(#robotCasingGrad)" stroke="#452009" stroke-width="2.2" />

                    <!-- Cream Bezel Frame -->
                    <rect x="44" y="32" width="72" height="50" rx="19" fill="url(#robotBezelGrad)" stroke="#dfd2c6" stroke-width="1.2" />

                    <!-- Dark Screen Visor -->
                    <rect x="49" y="36" width="62" height="42" rx="15" fill="url(#robotScreenGrad)" />

                    <!-- Cheeks (Blush) -->
                    <circle cx="58" cy="63" r="4" fill="#ff7675" opacity="0.75" />
                    <circle cx="102" cy="63" r="4" fill="#ff7675" opacity="0.75" />

                    <!-- Eyes Group (Mata Berkedip / Winking Animation) -->
                    <g class="robot-eyes-group">
                        <!-- Left Eye (Always smiling arc, natural squinch on blink) -->
                        <path class="robot-eye-left" d="M58 55 Q64 47 70 55" fill="none" stroke="#fff7ed" stroke-width="3.2" stroke-linecap="round" />

                        <!-- Right Eye Normal (Smiling arc ^) -->
                        <path class="robot-eye-right-normal" d="M90 55 Q96 47 102 55" fill="none" stroke="#fff7ed" stroke-width="3.2" stroke-linecap="round" />

                        <!-- Right Eye Wink (> / closed happy wink in Frame 3) -->
                        <path class="robot-eye-right-wink" d="M89 56 Q96 61 103 56" fill="none" stroke="#fff7ed" stroke-width="3.2" stroke-linecap="round" />
                    </g>

                    <!-- Smiling Mouth -->
                    <path d="M74 63 Q80 70 86 63" fill="none" stroke="#fff7ed" stroke-width="2.2" stroke-linecap="round" />
                </g>
            </g>
        </svg>
    </div>
    <?php endif; ?>

    <?php if (empty($hideChatbotWidget)): ?>
    <!-- Floating Chatbot Widget (Customer Service & Database AI Assistant) -->
    <div id="ai-chatbot-widget" class="chatbot-widget-container">
        <!-- Bubble Button -->
        <button id="chatbot-toggle-btn" class="chatbot-toggle-btn shadow-lg" title="Buka Asisten AI SmartService">
            <i class="bi bi-robot fs-3"></i>
        </button>

        <!-- Chat Window -->
        <div id="chatbot-window" class="chatbot-window shadow-lg d-none">
            <div class="chatbot-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-robot fs-4"></i>
                    <div>
                        <h6 class="mb-0 fw-bold">SmartService AI</h6>
                        <small class="text-white-50" style="font-size: 0.72rem;">CS & Database Assistant</small>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <?php 
                    $chatActiveAi = \Services\Ai\AiManager::getActiveConfig(); 
                    $currProvKey = $chatActiveAi['provider'] ?? 'groq';
                    $currModelId = $chatActiveAi['model_id'] ?? 'qwen/qwen3.8-27b';
                    $currProvName = match($currProvKey) {
                        'groq'   => 'Groq',
                        'nvidia' => 'NVIDIA',
                        default  => 'AI'
                    };
                    ?>
                    <!-- Menu Ganti Model AI di Chatbot Popup -->
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-light rounded-pill py-1 px-2 small dropdown-toggle d-inline-flex align-items-center gap-1 shadow-sm" 
                                type="button" 
                                data-bs-toggle="dropdown" 
                                data-bs-display="static"
                                aria-expanded="false" 
                                id="chatbot-model-dropdown-btn" 
                                style="font-size: 0.70rem; background: rgba(255,255,255,0.18); border-color: rgba(255,255,255,0.4);" 
                                title="Klik untuk ganti model AI">
                            <i class="bi bi-cpu-fill"></i>
                            <span id="chatbot-active-model-name"><?= htmlspecialchars($currProvName) ?></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 mt-2 py-2 small" style="min-width: 270px; font-size: 0.8rem; z-index: 9999;">
                            <li class="px-3 py-1 text-muted fw-bold text-uppercase d-flex justify-content-between align-items-center" style="font-size: 0.68rem; letter-spacing: 0.5px;">
                                <span>Ganti Model AI</span>
                                <span class="badge bg-primary-subtle text-primary rounded-pill">Realtime</span>
                            </li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center justify-content-between py-2 chatbot-switch-model-item <?= ($currProvKey === 'groq') ? 'active fw-bold' : '' ?>" href="#" data-provider="groq" data-model="qwen/qwen3.8-27b" data-name="Groq">
                                    <span><i class="bi bi-lightning-charge-fill text-warning me-2"></i>Groq LPU</span>
                                    <span class="badge bg-warning-subtle text-dark rounded-pill">qwen3.8-27b</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center justify-content-between py-2 chatbot-switch-model-item <?= ($currProvKey === 'nvidia') ? 'active fw-bold' : '' ?>" href="#" data-provider="nvidia" data-model="nvidia/nemotron-3.5-bolt-e4b" data-name="NVIDIA">
                                    <span><i class="bi bi-gpu-card text-success me-2"></i>NVIDIA NIM</span>
                                    <span class="badge bg-success-subtle text-success rounded-pill">nemotron-3.5-bolt</span>
                                </a>
                            </li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <a class="dropdown-item text-primary py-2 d-flex align-items-center gap-2 fw-semibold" href="<?= $baseUrl ?>/admin/ai-config">
                                    <i class="bi bi-sliders"></i>
                                    <span>Kelola Konfigurasi Lengkap & API Key</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <button id="chatbot-close-btn" class="btn btn-sm text-white border-0 p-1"><i class="bi bi-x-lg"></i></button>
                </div>
            </div>

            <!-- Model Status Bar -->
            <div class="chatbot-model-bar px-3 py-1 bg-light border-bottom d-flex align-items-center justify-content-between" style="font-size: 0.73rem;">
                <div class="d-flex align-items-center gap-1 text-truncate">
                    <span class="text-muted"><i class="bi bi-cpu me-1 text-primary"></i>Model:</span>
                    <strong class="text-primary text-truncate" id="chatbot-bar-model-text"><?= htmlspecialchars($currProvName) ?> (<?= htmlspecialchars($currModelId) ?>)</strong>
                </div>
                <a href="<?= $baseUrl ?>/admin/ai-config" class="text-decoration-none text-primary fw-semibold text-nowrap ms-2" title="Kelola Konfigurasi AI">
                    <i class="bi bi-sliders me-1"></i>Atur
                </a>
            </div>
            
            <div id="chatbot-messages" class="chatbot-messages">
                <div class="chat-message ai-message">
                    <div class="message-content">
                        <?php $activeUser = \Middleware\AuthMiddleware::user(); ?>
                        <?php if ($activeUser): ?>
                            Halo <strong><?= htmlspecialchars($activeUser['name']) ?></strong>! 👋 Saya asisten AI SmartService. Saya mengenali akun Anda sebagai <strong><?= htmlspecialchars(ucfirst($activeUser['role'])) ?></strong>. Tanyakan status pesanan Anda, info database sistem, katalog servis, atau tarif perbaikan! 😊
                        <?php else: ?>
                            Halo! 👋 Saya asisten AI SmartService. Ceritakan keluhan alat rumah tangga Anda, atau tanyakan info database, katalog layanan, dan tarif resmi perbaikan kami! 😊
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="chatbot-input-area">
                <form id="chatbot-form" class="d-flex gap-2 mb-0">
                    <input type="hidden" id="chatbot-csrf" value="<?= \Middleware\AuthMiddleware::generateCsrfToken() ?>">
                    <input type="text" id="chatbot-input" class="form-control form-control-sm" placeholder="Tanya pesanan, tarif, atau data database..." autocomplete="off">
                    <button type="submit" class="btn btn-primary btn-sm rounded-circle px-2" title="Kirim Pesan">
                        <i class="bi bi-send-fill"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

    <script>window.APP_BASE_URL = '<?= $baseUrl ?>';</script>
    <!-- Custom JS -->
    <script src="<?= $assetUrl ?>/js/main.js?v=<?= time() ?>"></script>
    <?php if (!empty($extraJs) && is_array($extraJs)): ?>
        <?php foreach ($extraJs as $jsFile): ?>
            <script src="<?= $assetUrl ?>/js/<?= htmlspecialchars($jsFile) ?>?v=<?= time() ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
