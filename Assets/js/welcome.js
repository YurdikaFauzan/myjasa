/**
 * SmartService AI (Jasaku) - Welcome / Login Interactive Logic
 * Handles 3s room crossfade slideshow, password toggle, and demo shortcuts.
 */

document.addEventListener('DOMContentLoaded', () => {
    // -------------------------------------------------------------------------
    // 1. BACKGROUND ROOMS CROSSFADE SLIDESHOW (3s Interval)
    // -------------------------------------------------------------------------
    const slides = document.querySelectorAll('.welcome-bg-slide');
    const roomBadge = document.getElementById('welcome-room-badge');
    const roomBadgeText = document.getElementById('welcome-room-text');

    const roomsInfo = [
        { name: 'Ruang Keluarga', icon: 'bi-house-heart-fill' },
        { name: 'Dapur', icon: 'bi-fire' },
        { name: 'Ruang Laundry', icon: 'bi-water' },
        { name: 'Ruang Kerja / Kamar', icon: 'bi-laptop' },
        { name: 'Area Servis Peralatan', icon: 'bi-tools' }
    ];

    let currentSlideIdx = 0;
    let slideshowInterval = null;

    function showSlide(index) {
        if (!slides || slides.length === 0) return;

        // Transition out current slide
        slides.forEach((slide, i) => {
            if (i === index) {
                slide.classList.add('active');
            } else {
                slide.classList.remove('active');
            }
        });

        // Update Room Badge with smooth crossfade
        if (roomBadge && roomBadgeText && roomsInfo[index]) {
            roomBadge.style.opacity = '0.3';
            roomBadge.style.transform = 'translateY(-2px)';
            setTimeout(() => {
                const info = roomsInfo[index];
                roomBadgeText.innerHTML = `<i class="bi ${info.icon} me-1 text-warning"></i> ${info.name}`;
                roomBadge.style.opacity = '1';
                roomBadge.style.transform = 'translateY(0)';
            }, 250);
        }
    }

    function nextSlide() {
        if (!slides || slides.length === 0) return;
        currentSlideIdx = (currentSlideIdx + 1) % slides.length;
        showSlide(currentSlideIdx);
    }

    // Initialize first slide
    if (slides.length > 0) {
        showSlide(0);
        // Start 3000ms loop
        slideshowInterval = setInterval(nextSlide, 3000);
    }

    // Clean timer if page unloads
    window.addEventListener('beforeunload', () => {
        if (slideshowInterval) clearInterval(slideshowInterval);
    });

    // -------------------------------------------------------------------------
    // 2. PASSWORD VISIBILITY TOGGLE
    // -------------------------------------------------------------------------
    const togglePasswordBtn = document.getElementById('toggle-password-btn');
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('toggle-password-icon');

    if (togglePasswordBtn && passwordInput && toggleIcon) {
        togglePasswordBtn.addEventListener('click', (e) => {
            e.preventDefault();
            const isPassword = passwordInput.getAttribute('type') === 'password';
            if (isPassword) {
                passwordInput.setAttribute('type', 'text');
                toggleIcon.classList.remove('bi-eye-slash');
                toggleIcon.classList.add('bi-eye');
                togglePasswordBtn.setAttribute('title', 'Sembunyikan password');
            } else {
                passwordInput.setAttribute('type', 'password');
                toggleIcon.classList.remove('bi-eye');
                toggleIcon.classList.add('bi-eye-slash');
                togglePasswordBtn.setAttribute('title', 'Tampilkan password');
            }
        });
    }

    // -------------------------------------------------------------------------
    // 3. DUAL-PORTAL SWITCHER LOGIC (CUSTOMER & TEKNISI)
    // -------------------------------------------------------------------------
    const tabBtnCustomer = document.getElementById('tab-btn-customer');
    const tabBtnTechnician = document.getElementById('tab-btn-technician');
    const loginPortalInput = document.getElementById('login_portal');
    const portalInfoBox = document.getElementById('portal-info-box');
    const portalBadge = document.getElementById('portal-badge');
    const portalRoleTag = document.getElementById('portal-role-tag');
    const portalDescText = document.getElementById('portal-desc-text');
    const emailInput = document.getElementById('email');
    const userIcon = document.getElementById('welcome-user-icon');
    const submitBtn = document.getElementById('welcome-submit-btn');
    const submitText = document.getElementById('submit-text');
    const submitIcon = document.getElementById('submit-icon');
    const portalRegisterHint = document.getElementById('portal-register-hint');

    // Base URL calculation for client links
    const baseUrl = window.location.origin + window.location.pathname.split('/').slice(0, 2).join('/');

    function switchPortal(portalName, updateUrl = true) {
        if (!tabBtnCustomer || !tabBtnTechnician) return;

        if (portalName === 'technician') {
            tabBtnTechnician.classList.add('active');
            tabBtnTechnician.setAttribute('aria-selected', 'true');
            tabBtnCustomer.classList.remove('active');
            tabBtnCustomer.setAttribute('aria-selected', 'false');

            if (loginPortalInput) loginPortalInput.value = 'technician';

            if (portalInfoBox) {
                portalInfoBox.className = 'portal-info-box portal-theme-technician';
            }
            if (portalBadge) {
                portalBadge.innerHTML = '<i class="bi bi-wrench-adjustable-circle-fill text-warning me-1"></i> Portal Mitra Teknisi';
            }
            if (portalRoleTag) {
                portalRoleTag.textContent = 'Mitra Servis';
            }
            if (portalDescText) {
                portalDescText.textContent = 'Masuk untuk melihat penugasan pekerjaan, update status pengerjaan, dan submit rincian biaya.';
            }
            if (emailInput) {
                emailInput.placeholder = 'Email / ID Mitra Teknisi';
            }
            if (userIcon) {
                userIcon.className = 'bi bi-tools welcome-input-icon';
            }
            if (submitBtn) {
                submitBtn.className = 'welcome-btn-login btn-portal-technician';
            }
            if (submitIcon) {
                submitIcon.className = 'bi bi-tools';
            }
            if (submitText) {
                submitText.textContent = 'Masuk sebagai Teknisi';
            }
            if (portalRegisterHint) {
                portalRegisterHint.innerHTML = 'Ingin bermitra jadi Teknisi? <a href="' + baseUrl + '/register?role=technician" class="welcome-register-link">Daftar Akun Teknisi</a> &bull; <a href="' + baseUrl + '/forgot-password" class="welcome-register-link">Lupa Sandi</a>';
            }

            if (updateUrl) {
                try {
                    window.history.replaceState({ portal: 'technician' }, '', baseUrl + '/login?role=technician');
                } catch (e) {}
            }
        } else {
            tabBtnCustomer.classList.add('active');
            tabBtnCustomer.setAttribute('aria-selected', 'true');
            tabBtnTechnician.classList.remove('active');
            tabBtnTechnician.setAttribute('aria-selected', 'false');

            if (loginPortalInput) loginPortalInput.value = 'customer';

            if (portalInfoBox) {
                portalInfoBox.className = 'portal-info-box portal-theme-customer';
            }
            if (portalBadge) {
                portalBadge.innerHTML = '<i class="bi bi-person-heart text-primary me-1"></i> Portal Pelanggan (Customer)';
            }
            if (portalRoleTag) {
                portalRoleTag.textContent = 'Pemesan Jasa';
            }
            if (portalDescText) {
                portalDescText.textContent = 'Masuk untuk memesan perbaikan, tracking status teknisi, dan konsultasi AI diagnosis.';
            }
            if (emailInput) {
                emailInput.placeholder = 'Username / Email Customer';
            }
            if (userIcon) {
                userIcon.className = 'bi bi-person-fill welcome-input-icon';
            }
            if (submitBtn) {
                submitBtn.className = 'welcome-btn-login btn-portal-customer';
            }
            if (submitIcon) {
                submitIcon.className = 'bi bi-box-arrow-in-right';
            }
            if (submitText) {
                submitText.textContent = 'Masuk sebagai Customer';
            }
            if (portalRegisterHint) {
                portalRegisterHint.innerHTML = 'Belum punya akun? <a href="' + baseUrl + '/register?role=customer" class="welcome-register-link">Daftar Customer</a> &bull; <a href="' + baseUrl + '/forgot-password" class="welcome-register-link">Lupa Sandi</a>';
            }

            if (updateUrl) {
                try {
                    window.history.replaceState({ portal: 'customer' }, '', baseUrl + '/login?role=customer');
                } catch (e) {}
            }
        }
    }

    if (tabBtnCustomer) {
        tabBtnCustomer.addEventListener('click', () => switchPortal('customer'));
    }
    if (tabBtnTechnician) {
        tabBtnTechnician.addEventListener('click', () => switchPortal('technician'));
    }



    // -------------------------------------------------------------------------
    // 4. MASCOT INTERACTIVE GREETING
    // -------------------------------------------------------------------------
    const mascotSvg = document.getElementById('welcome-mascot-svg');
    if (mascotSvg) {
        mascotSvg.style.cursor = 'pointer';
        mascotSvg.addEventListener('click', () => {
            // Speed up wave on click for fun
            const waveArm = document.getElementById('mascot-wave-arm');
            if (waveArm) {
                waveArm.style.animationDuration = '0.7s';
                setTimeout(() => {
                    waveArm.style.animationDuration = '2.1s';
                }, 2100);
            }
        });
    }
});
