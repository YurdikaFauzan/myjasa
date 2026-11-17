<?php
$baseUrl = \Config\Env::get('APP_URL', 'http://localhost/Myjasagwuehhhhhhhhhhhhhhhhhhhhh');
$assetUrl = rtrim($baseUrl, '/') . '/Assets';
?>

<div class="welcome-page-wrapper">
    <!-- 1. Background Slideshow (5 Rooms with Crossfade) -->
    <div class="welcome-bg-slideshow" aria-hidden="true">
        <div class="welcome-bg-slide" style="background-image: url('<?= $assetUrl ?>/images/rooms/room-1-keluarga.jpg');"></div>
        <div class="welcome-bg-slide" style="background-image: url('<?= $assetUrl ?>/images/rooms/room-2-dapur.jpg');"></div>
        <div class="welcome-bg-slide" style="background-image: url('<?= $assetUrl ?>/images/rooms/room-3-laundry.jpg');"></div>
        <div class="welcome-bg-slide" style="background-image: url('<?= $assetUrl ?>/images/rooms/room-4-kamar.jpg');"></div>
        <div class="welcome-bg-slide" style="background-image: url('<?= $assetUrl ?>/images/rooms/room-5-servis.jpg');"></div>
        <div class="welcome-bg-overlay"></div>
    </div>

    <!-- Floating Room Indicator Badge (Syncs with 3s Slideshow) -->
    <div class="welcome-room-badge" id="welcome-room-badge" title="Ruangan aktif">
        <span class="welcome-room-badge-dot"></span>
        <span id="welcome-room-text"><i class="bi bi-house-heart-fill me-1 text-warning"></i> Ruang Keluarga</span>
    </div>

    <!-- 2. Main Stage (Stage Grid: Left Scene, Center Login & Mascot, Right Scene) -->
    <div class="welcome-main-stage">
        <div class="welcome-stage-grid">
            
            <!-- LEFT SCENE: AC, Kulkas, Mesin Cuci, & Tanaman Berdaun Goyang -->
            <div class="welcome-scene-left" aria-hidden="true">
                <!-- Wall AC Unit with Cool Air Breeze -->
                <div class="welcome-ac-unit">
                    <svg viewBox="0 0 170 80" width="170" height="80" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <linearGradient id="acGrad" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#FFFFFF" />
                                <stop offset="100%" stop-color="#E2E8F0" />
                            </linearGradient>
                            <linearGradient id="breezeGrad" x1="0" y1="0" x2="1" y2="0">
                                <stop offset="0%" stop-color="#38BDF8" stop-opacity="0.8" />
                                <stop offset="100%" stop-color="#BAE6FD" stop-opacity="0.1" />
                            </linearGradient>
                        </defs>
                        <!-- AC Body -->
                        <rect x="8" y="10" width="154" height="46" rx="8" fill="url(#acGrad)" stroke="#743E1F" stroke-width="2" />
                        <!-- Air Vent -->
                        <rect x="14" y="44" width="142" height="8" rx="2" fill="#CBD5E1" stroke="#743E1F" stroke-width="1.2" />
                        <line x1="18" y1="48" x2="152" y2="48" stroke="#64748B" stroke-width="1.5" />
                        <!-- Brand logo & Power Dot -->
                        <circle cx="148" cy="24" r="2.5" fill="#22C55E" />
                        <rect x="74" y="22" width="22" height="4" rx="2" fill="#94A3B8" />
                        <!-- Cool Air Breeze Flow Lines -->
                        <path d="M40 58 Q70 68 110 65" stroke="url(#breezeGrad)" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-dasharray="30 10" class="ac-breeze-line" />
                        <path d="M55 64 Q90 74 135 70" stroke="url(#breezeGrad)" stroke-width="2" fill="none" stroke-linecap="round" stroke-dasharray="25 8" class="ac-breeze-line ac-breeze-line-2" />
                        <path d="M70 70 Q110 78 150 76" stroke="url(#breezeGrad)" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-dasharray="20 6" class="ac-breeze-line ac-breeze-line-3" />
                    </svg>
                </div>

                <!-- Appliances: Kulkas & Mesin Cuci -->
                <div class="welcome-appliances-group">
                    <svg class="welcome-fridge-washer-svg" viewBox="0 0 250 250" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <linearGradient id="fridgeGrad" x1="0" y1="0" x2="1" y2="1">
                                <stop offset="0%" stop-color="#FFFFFF" />
                                <stop offset="100%" stop-color="#E2E8F0" />
                            </linearGradient>
                            <linearGradient id="washerGrad" x1="0" y1="0" x2="1" y2="1">
                                <stop offset="0%" stop-color="#FFFFFF" />
                                <stop offset="100%" stop-color="#F1F5F9" />
                            </linearGradient>
                            <radialGradient id="drumGlass" cx="40%" cy="40%" r="60%">
                                <stop offset="0%" stop-color="#38BDF8" stop-opacity="0.4" />
                                <stop offset="70%" stop-color="#0284C7" stop-opacity="0.8" />
                                <stop offset="100%" stop-color="#0369A1" />
                            </radialGradient>
                        </defs>

                        <!-- REFRIGERATOR (Tall Left) -->
                        <g id="appliance-refrigerator">
                            <!-- Outer Shell -->
                            <rect x="10" y="20" width="115" height="215" rx="14" fill="url(#fridgeGrad)" stroke="#743E1F" stroke-width="2.5" />
                            <!-- Top Freezer Door -->
                            <rect x="10" y="20" width="115" height="74" rx="14" fill="#FFFFFF" stroke="#743E1F" stroke-width="2" />
                            <line x1="12" y1="94" x2="123" y2="94" stroke="#743E1F" stroke-width="2.5" />
                            <!-- Handles -->
                            <rect x="110" y="55" width="4" height="28" rx="2" fill="#743E1F" />
                            <rect x="110" y="105" width="4" height="42" rx="2" fill="#743E1F" />
                            <!-- Cute Fridge Magnets / Sticky Notes -->
                            <rect x="25" y="42" width="14" height="14" rx="2" fill="#FEF08A" stroke="#EAB308" stroke-width="1" />
                            <rect x="45" y="48" width="12" height="12" rx="2" fill="#BAE6FD" stroke="#38BDF8" stroke-width="1" />
                            <circle cx="85" cy="50" r="4" fill="#F87171" />
                            <!-- Feet -->
                            <rect x="22" y="235" width="14" height="6" rx="2" fill="#475569" />
                            <rect x="96" y="235" width="14" height="6" rx="2" fill="#475569" />
                        </g>

                        <!-- WASHING MACHINE (Right of Fridge) -->
                        <g id="appliance-washing-machine">
                            <!-- Outer Body -->
                            <rect x="135" y="90" width="108" height="145" rx="14" fill="url(#washerGrad)" stroke="#743E1F" stroke-width="2.5" />
                            <!-- Control Panel -->
                            <rect x="135" y="90" width="108" height="34" rx="14" fill="#FFFFFF" stroke="#743E1F" stroke-width="2" />
                            <!-- Dial Knob -->
                            <circle cx="160" cy="107" r="8" fill="#E2E8F0" stroke="#743E1F" stroke-width="1.8" />
                            <circle cx="160" cy="107" r="3" fill="#743E1F" />
                            <!-- Digital Screen -->
                            <rect x="195" y="100" width="34" height="14" rx="3" fill="#1E293B" stroke="#475569" stroke-width="1" />
                            <circle cx="204" cy="107" r="1.5" fill="#38BDF8" />
                            <circle cx="212" cy="107" r="1.5" fill="#38BDF8" />
                            <circle cx="220" cy="107" r="1.5" fill="#38BDF8" />
                            <!-- Large Circular Porthole Door -->
                            <circle cx="189" cy="172" r="38" fill="#E2E8F0" stroke="#743E1F" stroke-width="2.5" />
                            <circle cx="189" cy="172" r="28" fill="url(#drumGlass)" stroke="#0369A1" stroke-width="2" />
                            <!-- Water Swirl Hint -->
                            <path d="M174 176 Q189 188 204 176 Q189 164 174 176 Z" fill="#E0F2FE" opacity="0.6" />
                            <!-- Door Handle -->
                            <path d="M221 163 Q226 172 221 181" stroke="#743E1F" stroke-width="3" stroke-linecap="round" fill="none" />
                            <!-- Feet -->
                            <rect x="146" y="235" width="14" height="6" rx="2" fill="#475569" />
                            <rect x="218" y="235" width="14" height="6" rx="2" fill="#475569" />
                        </g>
                    </svg>

                    <!-- POTTED PLANT KIRI (Tanaman Berdaun Goyang Halus) -->
                    <svg class="welcome-plant-left" viewBox="0 0 90 140" xmlns="http://www.w3.org/2000/svg">
                        <!-- Terracotta Pot (Statis) -->
                        <path d="M24 96 L66 96 L58 135 L32 135 Z" fill="#C47A47" stroke="#743E1F" stroke-width="2" />
                        <rect x="20" y="90" width="50" height="8" rx="3" fill="#D68A55" stroke="#743E1F" stroke-width="1.8" />
                        <ellipse cx="45" cy="92" rx="22" ry="3" fill="#542B12" opacity="0.4" />

                        <!-- Daun-Daun Independen Bergerak Bergoyang -->
                        <!-- Daun 1 (Lengkung Kiri Bawah) -->
                        <g class="plant-leaf-1">
                            <path d="M44 94 C26 84 10 74 14 46 C28 58 42 76 44 94 Z" fill="#6B9D6E" stroke="#426945" stroke-width="1.5" />
                            <path d="M44 94 C32 80 22 68 18 52" stroke="#426945" stroke-width="1.2" fill="none" />
                        </g>
                        <!-- Daun 2 (Tengah Tinggi) -->
                        <g class="plant-leaf-2">
                            <path d="M45 92 C38 62 36 34 45 12 C54 34 52 62 45 92 Z" fill="#7EAF81" stroke="#426945" stroke-width="1.5" />
                            <line x1="45" y1="92" x2="45" y2="20" stroke="#426945" stroke-width="1.2" />
                        </g>
                        <!-- Daun 3 (Lengkung Kanan) -->
                        <g class="plant-leaf-3">
                            <path d="M46 93 C60 76 74 62 70 36 C56 50 48 72 46 93 Z" fill="#5E8F61" stroke="#375539" stroke-width="1.5" />
                            <path d="M46 93 C56 78 64 64 66 42" stroke="#375539" stroke-width="1.2" fill="none" />
                        </g>
                        <!-- Daun 4 (Kiri Samping) -->
                        <g class="plant-leaf-4">
                            <path d="M43 95 C25 90 18 80 24 64 C32 76 40 86 43 95 Z" fill="#8CBD90" stroke="#426945" stroke-width="1.2" />
                        </g>
                        <!-- Daun 5 (Kanan Bawah) -->
                        <g class="plant-leaf-5">
                            <path d="M47 95 C62 90 70 82 66 66 C58 76 50 86 47 95 Z" fill="#6B9D6E" stroke="#426945" stroke-width="1.2" />
                        </g>
                    </svg>
                </div>
            </div>

            <!-- CENTER SECTION: MASCOT ROBOT UTAMA, BANNER, SUBTITLE, & FORM LOGIN -->
            <div class="welcome-scene-center">
                
                <!-- ROBOT UTAMA DENGAN ANIMASI LOOP -->
                <div class="welcome-mascot-container">
                    <svg class="welcome-mascot-svg" id="welcome-mascot-svg" viewBox="0 0 200 180" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <linearGradient id="mascotBodyGrad" x1="0" y1="0" x2="1" y2="1">
                                <stop offset="0%" stop-color="#FFFFFF" />
                                <stop offset="100%" stop-color="#F7EFE4" />
                            </linearGradient>
                            <linearGradient id="mascotEarGrad" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#9C5F34" />
                                <stop offset="100%" stop-color="#743E1F" />
                            </linearGradient>
                            <linearGradient id="mascotBadgeGrad" x1="0" y1="0" x2="1" y2="1">
                                <stop offset="0%" stop-color="#BA7640" />
                                <stop offset="100%" stop-color="#8A4F27" />
                            </linearGradient>
                        </defs>

                        <!-- Sparkles Sekitar Kepala Robot -->
                        <g id="mascot-sparkles">
                            <!-- Sparkle 1 (Kiri Atas) -->
                            <path d="M42 22 L45 28 L51 31 L45 34 L42 40 L39 34 L33 31 L39 28 Z" fill="#F59E0B" class="mascot-sparkle mascot-sparkle-1" />
                            <!-- Sparkle 2 (Kanan Atas) -->
                            <path d="M156 18 L158 24 L164 26 L158 28 L156 34 L154 28 L148 26 L154 24 Z" fill="#F59E0B" class="mascot-sparkle mascot-sparkle-2" />
                            <!-- Sparkle 3 (Kiri Bawah) -->
                            <path d="M28 62 L30 66 L34 68 L30 70 L28 74 L26 70 L22 68 L26 66 Z" fill="#FCD34D" class="mascot-sparkle mascot-sparkle-3" />
                            <!-- Sparkle 4 (Kanan Bawah) -->
                            <path d="M170 58 L172 63 L177 65 L172 67 L170 72 L168 67 L163 65 L168 63 Z" fill="#FCD34D" class="mascot-sparkle mascot-sparkle-4" />
                        </g>

                        <!-- Tangan Melambai Robot (Kiri Penonton / Kanan Robot) -->
                        <g id="mascot-wave-arm">
                            <!-- Lengan Bahu Menekuk Naik -->
                            <path d="M68 116 Q46 102 44 82" stroke="#743E1F" stroke-width="6.5" stroke-linecap="round" fill="none" />
                            <!-- Telapak Tangan Bulat Terbuka -->
                            <circle cx="43" cy="78" r="8" fill="#F7EFE4" stroke="#743E1F" stroke-width="2" />
                            <!-- Jari-Jari Robot Melambai 👋 -->
                            <path d="M38 72 L36 62" stroke="#743E1F" stroke-width="3" stroke-linecap="round" />
                            <path d="M43 71 L43 59" stroke="#743E1F" stroke-width="3" stroke-linecap="round" />
                            <path d="M48 72 L51 61" stroke="#743E1F" stroke-width="3" stroke-linecap="round" />
                            <path d="M35 78 L29 74" stroke="#743E1F" stroke-width="2.6" stroke-linecap="round" />
                            <circle cx="43" cy="78" r="3" fill="#BA7640" />
                        </g>

                        <!-- Tangan Kanan Penonton (Istirahat di Atas Banner) -->
                        <g id="mascot-rest-arm">
                            <path d="M132 116 Q150 114 154 126" stroke="#743E1F" stroke-width="6.5" stroke-linecap="round" fill="none" />
                            <circle cx="154" cy="126" r="6" fill="#F7EFE4" stroke="#743E1F" stroke-width="2" />
                        </g>

                        <!-- Tubuh Robot (Torso) -->
                        <g id="mascot-torso-group">
                            <rect x="66" y="100" width="68" height="48" rx="20" fill="url(#mascotBodyGrad)" stroke="#743E1F" stroke-width="2.5" />
                            <!-- Emblem Logo Chip pada Dada -->
                            <rect x="85" y="112" width="30" height="22" rx="6" fill="url(#mascotBadgeGrad)" stroke="#743E1F" stroke-width="1.5" />
                            <!-- Pola Sirkuit Chip -->
                            <circle cx="100" cy="123" r="3.5" fill="#FFFFFF" />
                            <line x1="93" y1="123" x2="97" y2="123" stroke="#FFFFFF" stroke-width="1.5" stroke-linecap="round" />
                            <line x1="103" y1="123" x2="107" y2="123" stroke="#FFFFFF" stroke-width="1.5" stroke-linecap="round" />
                            <line x1="100" y1="116" x2="100" y2="120" stroke="#FFFFFF" stroke-width="1.5" stroke-linecap="round" />
                            <line x1="100" y1="126" x2="100" y2="130" stroke="#FFFFFF" stroke-width="1.5" stroke-linecap="round" />
                        </g>

                        <!-- Leher Robot -->
                        <rect x="92" y="92" width="16" height="12" rx="3" fill="#64748B" stroke="#743E1F" stroke-width="1.5" />

                        <!-- Kepala Robot (Bobbing Up & Down) -->
                        <g id="mascot-head-group">
                            <!-- Telinga Kiri & Kanan -->
                            <rect x="36" y="48" width="9" height="22" rx="4" fill="url(#mascotEarGrad)" stroke="#743E1F" stroke-width="2" />
                            <rect x="155" y="48" width="9" height="22" rx="4" fill="url(#mascotEarGrad)" stroke="#743E1F" stroke-width="2" />
                            <circle cx="40.5" cy="59" r="2" fill="#E2E8F0" />
                            <circle cx="159.5" cy="59" r="2" fill="#E2E8F0" />

                            <!-- Antena Atas -->
                            <line x1="100" y1="26" x2="100" y2="12" stroke="#743E1F" stroke-width="3.5" stroke-linecap="round" />
                            <circle cx="100" cy="10" r="7.5" fill="#F59E0B" stroke="#743E1F" stroke-width="2" />
                            <circle cx="98" cy="8" r="2.5" fill="#FFFBEB" />

                            <!-- Casing Kepala Membulat Halus -->
                            <rect x="42" y="24" width="116" height="74" rx="34" fill="url(#mascotBodyGrad)" stroke="#743E1F" stroke-width="3" />
                            <!-- Highlight Atas Kepala -->
                            <path d="M56 32 Q100 24 144 32" stroke="#FFFFFF" stroke-width="2.5" fill="none" opacity="0.8" stroke-linecap="round" />

                            <!-- Layar Wajah (Visor Screen Cokelat Tua Glossy) -->
                            <rect x="58" y="40" width="84" height="44" rx="18" fill="#241710" stroke="#3D2517" stroke-width="1.5" />

                            <!-- Mata Robot Berkedip (^ ^) -->
                            <g id="mascot-eyes-group">
                                <path d="M72 63 Q80 50 88 63" fill="none" stroke="#FFF7ED" stroke-width="3.8" stroke-linecap="round" />
                                <path d="M112 63 Q120 50 128 63" fill="none" stroke="#FFF7ED" stroke-width="3.8" stroke-linecap="round" />
                            </g>

                            <!-- Pipi Merona Rosy Cheeks -->
                            <ellipse cx="69" cy="70" rx="5" ry="3" fill="#F87171" opacity="0.8" />
                            <ellipse cx="131" cy="70" rx="5" ry="3" fill="#F87171" opacity="0.8" />

                            <!-- Senyum Manis Robot -->
                            <path d="M94 69 Q100 77 106 69" fill="none" stroke="#FFF7ED" stroke-width="2.6" stroke-linecap="round" />
                        </g>
                    </svg>

                    <!-- BANNER "Selamat Datang di Jasaku" -->
                    <div class="welcome-banner-badge">
                        <span class="welcome-banner-sub">Selamat Datang di</span>
                        <div class="welcome-banner-title">
                            <i class="bi bi-stars welcome-banner-sparkle"></i>
                            <span>Jasaku</span>
                            <i class="bi bi-stars welcome-banner-sparkle"></i>
                        </div>
                    </div>
                </div>

                <!-- Subtitle -->
                <p class="welcome-stage-subtitle">
                    Temukan teknisi terbaik untuk membantu memperbaiki peralatan rumah tangga Anda dengan mudah dan cepat.
                </p>

                <!-- CARD FORM LOGIN (Dual Portals: Customer & Teknisi) -->
                <div class="welcome-login-card" id="welcome-login-card">
                    <!-- Segmented Dual-Portal Switcher Tabs -->
                    <div class="login-portal-switcher-wrapper">
                        <div class="login-portal-switcher" role="tablist" aria-label="Pilih Portal Login">
                            <button type="button" class="portal-tab-btn <?= ($activePortal ?? 'customer') === 'customer' ? 'active' : '' ?>" id="tab-btn-customer" data-portal="customer" role="tab" aria-selected="<?= ($activePortal ?? 'customer') === 'customer' ? 'true' : 'false' ?>">
                                <i class="bi bi-person-circle"></i>
                                <span>Login Customer</span>
                            </button>
                            <button type="button" class="portal-tab-btn <?= ($activePortal ?? 'customer') === 'technician' ? 'active' : '' ?>" id="tab-btn-technician" data-portal="technician" role="tab" aria-selected="<?= ($activePortal ?? 'customer') === 'technician' ? 'true' : 'false' ?>">
                                <i class="bi bi-tools"></i>
                                <span>Login Teknisi</span>
                            </button>
                        </div>
                    </div>

                    <!-- Dynamic Portal Info Box -->
                    <div class="portal-info-box portal-theme-<?= ($activePortal ?? 'customer') === 'technician' ? 'technician' : 'customer' ?>" id="portal-info-box">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="portal-badge" id="portal-badge">
                                <?php if (($activePortal ?? 'customer') === 'technician'): ?>
                                    <i class="bi bi-wrench-adjustable-circle-fill text-warning me-1"></i> Portal Mitra Teknisi
                                <?php else: ?>
                                    <i class="bi bi-person-heart text-primary me-1"></i> Portal Pelanggan (Customer)
                                <?php endif; ?>
                            </span>
                            <span class="portal-role-pill" id="portal-role-tag">
                                <?= ($activePortal ?? 'customer') === 'technician' ? 'Mitra Servis' : 'Pemesan Jasa' ?>
                            </span>
                        </div>
                        <p class="portal-desc-text small mb-0" id="portal-desc-text">
                            <?php if (($activePortal ?? 'customer') === 'technician'): ?>
                                Masuk untuk melihat penugasan pekerjaan, update status pengerjaan, dan submit rincian biaya.
                            <?php else: ?>
                                Masuk untuk memesan perbaikan, tracking status teknisi, dan konsultasi AI diagnosis.
                            <?php endif; ?>
                        </p>
                    </div>

                    <!-- Flash Message Alert -->
                    <?php if (!empty($flash)): ?>
                        <div class="alert alert-<?= htmlspecialchars($flash['type']) ?> alert-dismissible fade show rounded-3 small py-2 px-3 mb-3 border-0" role="alert">
                            <i class="bi bi-info-circle-fill me-1"></i>
                            <?= htmlspecialchars($flash['message']) ?>
                            <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form action="<?= $baseUrl ?>/login" method="POST" id="welcome-login-form">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
                        <input type="hidden" name="login_portal" id="login_portal" value="<?= htmlspecialchars($activePortal ?? 'customer') ?>">

                        <!-- Input Username / Email -->
                        <div class="welcome-form-group">
                            <div class="welcome-input-wrapper">
                                <i class="bi bi-person-fill welcome-input-icon" id="welcome-user-icon"></i>
                                <input type="text" name="email" id="email" class="welcome-input" placeholder="<?= ($activePortal ?? 'customer') === 'technician' ? 'Email / ID Mitra Teknisi' : 'Username / Email Customer' ?>" required autofocus autocomplete="username">
                            </div>
                        </div>

                        <!-- Input Password dengan Toggle Mata -->
                        <div class="welcome-form-group">
                            <div class="welcome-input-wrapper">
                                <i class="bi bi-lock-fill welcome-input-icon"></i>
                                <input type="password" name="password" id="password" class="welcome-input" placeholder="Password Akun" required autocomplete="current-password">
                                <button type="button" class="welcome-btn-toggle-eye" id="toggle-password-btn" aria-label="Toggle Password Visibility">
                                    <i class="bi bi-eye-slash" id="toggle-password-icon"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Tombol Submit Login -->
                        <button type="submit" class="welcome-btn-login <?= ($activePortal ?? 'customer') === 'technician' ? 'btn-portal-technician' : 'btn-portal-customer' ?>" id="welcome-submit-btn">
                            <i class="<?= ($activePortal ?? 'customer') === 'technician' ? 'bi bi-tools' : 'bi bi-box-arrow-in-right' ?>" id="submit-icon"></i>
                            <span id="submit-text"><?= ($activePortal ?? 'customer') === 'technician' ? 'Masuk sebagai Teknisi' : 'Masuk sebagai Customer' ?></span>
                        </button>

                        <!-- Opsi Login Cepat via WhatsApp OTP Fonte -->
                        <div class="welcome-divider-or my-2 text-center position-relative">
                            <span class="px-2 small text-muted bg-white position-relative" style="z-index: 1;">atau masuk instan</span>
                            <hr class="position-absolute w-100 top-50 start-0 m-0 border-secondary-subtle" style="z-index: 0;">
                        </div>

                        <a href="<?= $baseUrl ?>/otp/login?role=<?= ($activePortal ?? 'customer') ?>" class="btn w-100 py-2 rounded-pill fw-bold text-white shadow-sm d-flex align-items-center justify-content-center gap-2 mb-3" style="background: linear-gradient(135deg, #128C7E 0%, #25D366 100%); border: none;">
                            <i class="bi bi-whatsapp fs-5"></i>
                            <span>Masuk Cepat via WhatsApp OTP</span>
                        </a>

                        <!-- Link Register & Help -->
                        <div class="welcome-register-hint" id="portal-register-hint">
                            <?php if (($activePortal ?? 'customer') === 'technician'): ?>
                                Ingin bermitra jadi Teknisi? <a href="<?= $baseUrl ?>/register?role=technician" class="welcome-register-link">Daftar Akun Teknisi</a> &bull; <a href="<?= $baseUrl ?>/forgot-password" class="welcome-register-link">Lupa Sandi</a>
                            <?php else: ?>
                                Belum punya akun? <a href="<?= $baseUrl ?>/register?role=customer" class="welcome-register-link">Daftar Customer</a> &bull; <a href="<?= $baseUrl ?>/forgot-password" class="welcome-register-link">Lupa Sandi</a>
                            <?php endif; ?>
                        </div>
                    </form>

                    <!-- Akses Khusus Administrator -->
                    <div class="text-center mt-3 pt-2 border-top border-light-subtle">
                        <a href="<?= $baseUrl ?>/login/admin" class="text-decoration-none small text-muted d-inline-flex align-items-center gap-1 admin-portal-link" style="transition: color 0.2s ease;">
                            <i class="bi bi-shield-lock-fill text-dark"></i>
                            <span>Akses Khusus Administrator</span>
                        </a>
                    </div>
                </div>

            </div>

            <!-- RIGHT SCENE: TOOLBOX, PERBAIKAN LEBIH MUDAH & TANAMAN POT KANAN -->
            <div class="welcome-scene-right" aria-hidden="true">
                <!-- Playful Curved Quote & Dashed Flight Line -->
                <div class="welcome-curved-quote">
                    <span class="welcome-quote-text">
                        Perbaikan Lebih Mudah Bersama Kami!
                    </span>
                    <!-- Dashed Swoosh SVG Line -->
                    <svg class="welcome-quote-dashed" viewBox="0 0 60 40" xmlns="http://www.w3.org/2000/svg">
                        <path d="M5 35 Q30 5 55 20" stroke="#C47A47" stroke-width="2" stroke-dasharray="4 3" fill="none" opacity="0.7" />
                        <polygon points="55,17 58,23 52,21" fill="#C47A47" />
                    </svg>
                </div>

                <!-- Toolbox Group -->
                <div class="welcome-toolbox-group">
                    <svg class="welcome-toolbox-svg" viewBox="0 0 240 210" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <linearGradient id="toolboxGrad" x1="0" y1="0" x2="1" y2="1">
                                <stop offset="0%" stop-color="#BA7640" />
                                <stop offset="100%" stop-color="#8A4F27" />
                            </linearGradient>
                            <linearGradient id="chromeToolGrad" x1="0" y1="0" x2="1" y2="0">
                                <stop offset="0%" stop-color="#F1F5F9" />
                                <stop offset="50%" stop-color="#CBD5E1" />
                                <stop offset="100%" stop-color="#94A3B8" />
                            </linearGradient>
                        </defs>

                        <!-- Chrome Wrench Inside Box (Vibrates subtly) -->
                        <g class="toolbox-tool-wrench">
                            <g transform="translate(68, 48) rotate(-30)">
                                <rect x="8" y="15" width="8" height="42" rx="2" fill="url(#chromeToolGrad)" stroke="#475569" stroke-width="1.2" />
                                <!-- Wrench Head -->
                                <path d="M4 18 C4 8 20 8 20 18 L16 18 C16 12 8 12 8 18 Z" fill="url(#chromeToolGrad)" stroke="#475569" stroke-width="1.2" />
                                <circle cx="12" cy="58" r="6" fill="url(#chromeToolGrad)" stroke="#475569" stroke-width="1.2" />
                            </g>
                        </g>

                        <!-- Screwdriver Inside Box -->
                        <g class="toolbox-tool-driver">
                            <g transform="translate(130, 46) rotate(22)">
                                <!-- Handle -->
                                <rect x="6" y="24" width="12" height="34" rx="4" fill="#E58A3C" stroke="#743E1F" stroke-width="1.5" />
                                <line x1="12" y1="28" x2="12" y2="52" stroke="#743E1F" stroke-width="1.5" />
                                <!-- Shaft -->
                                <rect x="9.5" y="4" width="5" height="22" fill="url(#chromeToolGrad)" stroke="#475569" stroke-width="1" />
                                <polygon points="9.5,4 14.5,4 12,0" fill="#475569" />
                            </g>
                        </g>

                        <!-- Main Toolbox Box Body -->
                        <g id="toolbox-body">
                            <!-- Box Base -->
                            <rect x="25" y="96" width="185" height="90" rx="14" fill="url(#toolboxGrad)" stroke="#743E1F" stroke-width="2.8" />
                            <!-- Lid Rim -->
                            <rect x="22" y="90" width="191" height="18" rx="5" fill="#9C5F34" stroke="#743E1F" stroke-width="2.2" />
                            <!-- Handle on Top -->
                            <path d="M92 78 L144 78 L140 92 L96 92 Z" fill="#475569" stroke="#1E293B" stroke-width="2" />
                            <rect x="104" y="74" width="28" height="6" rx="2" fill="#94A3B8" />
                            <!-- Silver Clasp Lock -->
                            <rect x="110" y="94" width="16" height="20" rx="3" fill="#E2E8F0" stroke="#64748B" stroke-width="1.5" />
                            <circle cx="118" cy="104" r="2" fill="#475569" />
                            <!-- White Wrench Insignia on Front Face -->
                            <g transform="translate(100, 130) scale(0.9)">
                                <path d="M8 26 L26 8 C29 11 31 10 33 8 C30 5 30 3 33 0 C38 2 40 7 38 12 L22 28 C20 30 15 28 12 31 C9 28 11 26 8 26 Z" fill="#FFFFFF" opacity="0.9" />
                            </g>
                        </g>

                        <!-- Screwdriver Resting on Floor in Front -->
                        <g transform="translate(14, 182) rotate(-8)">
                            <rect x="0" y="4" width="22" height="9" rx="3" fill="#743E1F" stroke="#432210" stroke-width="1.2" />
                            <rect x="22" y="6" width="24" height="5" fill="url(#chromeToolGrad)" stroke="#475569" stroke-width="1" />
                            <polygon points="46,6 46,11 49,8.5" fill="#475569" />
                        </g>
                    </svg>

                    <!-- POTTED PLANT KANAN (Tanaman Berdaun Goyang Halus) -->
                    <svg class="welcome-plant-right" viewBox="0 0 95 140" xmlns="http://www.w3.org/2000/svg">
                        <!-- Terracotta Pot -->
                        <path d="M28 96 L70 96 L62 135 L36 135 Z" fill="#C47A47" stroke="#743E1F" stroke-width="2" />
                        <rect x="24" y="90" width="50" height="8" rx="3" fill="#D68A55" stroke="#743E1F" stroke-width="1.8" />
                        <ellipse cx="49" cy="92" rx="22" ry="3" fill="#542B12" opacity="0.4" />

                        <!-- Daun-daun Mandiri Bergoyang -->
                        <!-- Daun Kanan 1 (Tinggi Tengah) -->
                        <g class="plant-leaf-2">
                            <path d="M49 92 C42 62 42 32 50 14 C58 32 58 62 49 92 Z" fill="#7EAF81" stroke="#426945" stroke-width="1.5" />
                            <line x1="49" y1="92" x2="50" y2="22" stroke="#426945" stroke-width="1.2" />
                        </g>
                        <!-- Daun Kanan 2 (Lengkung Kiri) -->
                        <g class="plant-leaf-1">
                            <path d="M48 94 C32 78 18 64 22 40 C36 52 46 72 48 94 Z" fill="#6B9D6E" stroke="#426945" stroke-width="1.5" />
                        </g>
                        <!-- Daun Kanan 3 (Lengkung Kanan) -->
                        <g class="plant-leaf-3">
                            <path d="M50 93 C66 78 78 64 74 38 C60 52 52 74 50 93 Z" fill="#5E8F61" stroke="#375539" stroke-width="1.5" />
                        </g>
                        <!-- Daun Kanan 4 (Samping Rendah) -->
                        <g class="plant-leaf-4">
                            <path d="M51 95 C66 90 74 80 70 66 C62 76 54 86 51 95 Z" fill="#8CBD90" stroke="#426945" stroke-width="1.2" />
                        </g>
                    </svg>
                </div>
            </div>

        </div>

        <!-- 3. ROBOT TEKNISI KEDUA YANG SIBUK BEKERJA (WALKING & REPAIRING LOOP) -->
        <div class="welcome-technician-lane" aria-hidden="true" title="Teknisi robot sedang berpatroli servis!">
            <div class="technician-walking-actor">
                <svg viewBox="0 0 80 80" width="70" height="70" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <linearGradient id="techBodyGrad" x1="0" y1="0" x2="1" y2="1">
                            <stop offset="0%" stop-color="#FFFFFF" />
                            <stop offset="100%" stop-color="#F5EBE1" />
                        </linearGradient>
                        <linearGradient id="techHatGrad" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#BA7640" />
                            <stop offset="100%" stop-color="#8A4F27" />
                        </linearGradient>
                    </defs>

                    <!-- Walking Legs / Treads -->
                    <g class="tech-actor-legs">
                        <ellipse cx="28" cy="72" rx="9" ry="4" fill="#475569" stroke="#743E1F" stroke-width="1.5" />
                        <ellipse cx="50" cy="72" rx="9" ry="4" fill="#334155" stroke="#743E1F" stroke-width="1.5" />
                    </g>

                    <!-- Body & Head Group with Bobbing -->
                    <g class="tech-actor-body-group">
                        <!-- Torso -->
                        <rect x="22" y="38" width="34" height="28" rx="12" fill="url(#techBodyGrad)" stroke="#743E1F" stroke-width="2" />
                        <!-- Technician Apron / Tool Belt -->
                        <rect x="26" y="50" width="26" height="15" rx="4" fill="#9C5F34" stroke="#743E1F" stroke-width="1.2" />
                        <rect x="34" y="54" width="10" height="8" rx="2" fill="#743E1F" />
                        
                        <!-- Neck -->
                        <rect x="34" y="33" width="10" height="6" rx="2" fill="#64748B" />

                        <!-- Head -->
                        <rect x="18" y="8" width="42" height="30" rx="13" fill="url(#techBodyGrad)" stroke="#743E1F" stroke-width="2" />
                        <!-- Ear Bolts -->
                        <rect x="14" y="18" width="4" height="10" rx="2" fill="#9C5F34" />
                        <rect x="60" y="18" width="4" height="10" rx="2" fill="#9C5F34" />

                        <!-- Antenna -->
                        <line x1="39" y1="8" x2="39" y2="2" stroke="#743E1F" stroke-width="2" stroke-linecap="round" />
                        <circle cx="39" cy="2" r="2.5" fill="#F59E0B" />

                        <!-- Technician Hardhat / Cap -->
                        <path d="M20 15 Q39 4 58 15 Q39 10 20 15 Z" fill="url(#techHatGrad)" stroke="#743E1F" stroke-width="1.5" />
                        <circle cx="39" cy="11" r="2" fill="#FDE047" />

                        <!-- Visor Face Screen -->
                        <rect x="24" y="16" width="30" height="18" rx="8" fill="#241710" />
                        <!-- Concentrating / Happy Eyes (^ ^) -->
                        <path d="M29 24 Q33 19 37 24" fill="none" stroke="#FFF7ED" stroke-width="2" stroke-linecap="round" />
                        <path d="M41 24 Q45 19 49 24" fill="none" stroke="#FFF7ED" stroke-width="2" stroke-linecap="round" />
                        <!-- Cheeks -->
                        <circle cx="28" cy="29" r="1.5" fill="#F87171" />
                        <circle cx="50" cy="29" r="1.5" fill="#F87171" />

                        <!-- Arm Holding Wrench / Tool (Tinkering Movement) -->
                        <g class="tech-actor-wrench-arm">
                            <path d="M22 44 Q14 48 10 54" stroke="#743E1F" stroke-width="4" stroke-linecap="round" fill="none" />
                            <circle cx="10" cy="54" r="3.5" fill="#9C5F34" />
                            <!-- Mini Wrench Tool -->
                            <g transform="translate(0, 42) rotate(-35)">
                                <rect x="4" y="5" width="3.5" height="18" rx="1" fill="#E2E8F0" stroke="#475569" stroke-width="1" />
                                <path d="M2 6 C2 1 9 1 9 6 L7 6 C7 4 4 4 4 6 Z" fill="#E2E8F0" stroke="#475569" stroke-width="0.8" />
                            </g>
                        </g>

                        <!-- Arm 2 (Front Holding Screwdriver / Steer) -->
                        <path d="M54 44 Q62 50 64 56" stroke="#743E1F" stroke-width="4" stroke-linecap="round" fill="none" />
                        <circle cx="64" cy="56" r="3.5" fill="#9C5F34" />

                        <!-- Mini Work Spark (Kelap-Kelip) -->
                        <path d="M6 38 L8 42 L12 43 L8 45 L6 49 L5 45 L1 43 L5 42 Z" fill="#FCD34D" class="tech-actor-spark" />
                    </g>
                </svg>
            </div>
        </div>
    </div>

    <!-- 4. KEUNGGULAN LAYANAN (BOTTOM FEATURE WAVY SECTION) -->
    <div class="welcome-bottom-features">
        <!-- SVG Wave Divider on Top -->
        <div class="welcome-bottom-wave" aria-hidden="true">
            <svg viewBox="0 0 1200 35" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M0,25 C150,35 350,5 600,20 C850,35 1050,5 1200,25 L1200,35 L0,35 Z" fill="#A06138" />
            </svg>
        </div>

        <div class="welcome-features-container">
            <!-- Feature 1: Aman & Terpercaya -->
            <div class="welcome-feature-card">
                <div class="welcome-feature-icon-box">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div>
                    <h6 class="welcome-feature-title">Aman & Terpercaya</h6>
                </div>
            </div>

            <!-- Feature 2: Proses Mudah -->
            <div class="welcome-feature-card">
                <div class="welcome-feature-icon-box">
                    <i class="bi bi-lightning-charge-fill"></i>
                </div>
                <div>
                    <h6 class="welcome-feature-title">Proses Mudah</h6>
                </div>
            </div>

            <!-- Feature 3: Teknisi Profesional -->
            <div class="welcome-feature-card">
                <div class="welcome-feature-icon-box">
                    <i class="bi bi-person-fill-gear"></i>
                </div>
                <div>
                    <h6 class="welcome-feature-title">Teknisi Profesional</h6>
                </div>
            </div>

            <!-- Feature 4: Pelayanan Cepat -->
            <div class="welcome-feature-card">
                <div class="welcome-feature-icon-box">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div>
                    <h6 class="welcome-feature-title">Pelayanan Cepat</h6>
                </div>
            </div>
        </div>
    </div>
</div>
