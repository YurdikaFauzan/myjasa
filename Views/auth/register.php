<?php
$baseUrl = \Config\Env::get('APP_URL', 'http://localhost/Myjasagwuehhhhhhhhhhhhhhhhhhhhh');
$activeRole = $activeRole ?? 'customer';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">

            <!-- Flash Alert Message -->
            <?php if (!empty($flash)): ?>
                <div class="alert alert-<?= htmlspecialchars($flash['type']) ?> alert-dismissible fade show rounded-4 shadow-sm mb-4 border-0" role="alert">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    <?= htmlspecialchars($flash['message']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Segmented Role Switcher Tabs -->
            <div class="card border-0 shadow-sm rounded-4 mb-4 p-2 bg-light">
                <div class="d-flex gap-2" role="tablist" aria-label="Pilih Peran Pendaftaran">
                    <button type="button" class="btn flex-fill py-2 rounded-3 font-semibold d-flex align-items-center justify-content-center gap-2 <?= $activeRole === 'customer' ? 'btn-primary shadow-sm' : 'btn-outline-secondary border-0 text-muted' ?>" id="reg-tab-customer">
                        <i class="bi bi-person-circle fs-5"></i>
                        <span>Daftar Customer</span>
                    </button>
                    <button type="button" class="btn flex-fill py-2 rounded-3 font-semibold d-flex align-items-center justify-content-center gap-2 <?= $activeRole === 'technician' ? 'btn-warning text-dark shadow-sm' : 'btn-outline-secondary border-0 text-muted' ?>" id="reg-tab-technician">
                        <i class="bi bi-tools fs-5"></i>
                        <span>Daftar Mitra Teknisi</span>
                    </button>
                </div>
            </div>

            <div class="card shadow-lg border-0 rounded-4 overflow-hidden" id="register-card">
                <!-- Header Card (Dynamic Theme) -->
                <div class="card-header text-white text-center py-4 <?= $activeRole === 'technician' ? 'bg-dark border-bottom border-warning border-3' : 'bg-dark border-bottom border-primary border-3' ?>" id="register-header">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2 shadow-sm <?= $activeRole === 'technician' ? 'bg-warning text-dark' : 'bg-primary text-white' ?>" id="header-icon-box" style="width: 56px; height: 56px;">
                        <i class="<?= $activeRole === 'technician' ? 'bi bi-tools' : 'bi bi-person-plus-fill' ?> fs-3" id="header-icon"></i>
                    </div>
                    <h4 class="fw-bold mb-1" id="header-title">
                        <?= $activeRole === 'technician' ? 'Pendaftaran Akun Mitra Teknisi' : 'Pendaftaran Akun Customer' ?>
                    </h4>
                    <p class="small text-white-50 mb-0" id="header-desc">
                        <?= $activeRole === 'technician' ? 'Bergabung sebagai mitra teknisi profesional untuk menerima order & pekerjaan servis' : 'Daftar sebagai pelanggan untuk memesan jasa perbaikan & perawatan peralatan rumah tangga' ?>
                    </p>
                </div>

                <div class="card-body p-4 p-md-5">
                    <!-- Dynamic Info Notice -->
                    <div class="alert <?= $activeRole === 'technician' ? 'alert-warning border-warning' : 'alert-info border-info' ?> rounded-3 small mb-4 d-flex align-items-start gap-2 shadow-none" id="role-info-alert">
                        <i class="bi bi-info-circle-fill fs-5 mt-1"></i>
                        <span id="role-info-text">
                            <?php if ($activeRole === 'technician'): ?>
                                Akun ini akan didaftarkan sebagai <strong>Mitra Teknisi</strong>. Anda dapat menerima pesanan servis, mengonfirmasi kedatangan, dan mengelola pengerjaan perbaikan di lokasi pelanggan.
                            <?php else: ?>
                                Akun ini akan didaftarkan sebagai <strong>Customer (Pelanggan)</strong>. Anda dapat memesan jasa perbaikan AC, Kulkas, Mesin Cuci, dll., serta konsultasi AI diagnosis gratis.
                            <?php endif; ?>
                        </span>
                    </div>

                    <form action="<?= $baseUrl ?>/register" method="POST" id="register-form">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                        <input type="hidden" name="role" id="register_role_input" value="<?= htmlspecialchars($activeRole) ?>">

                        <div class="mb-3">
                            <label for="name" class="form-label font-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="bi bi-person"></i></span>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Contoh: Budi Santoso" required autofocus>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label font-semibold">Alamat Email <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email" placeholder="nama@email.com" required>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="password" class="form-label font-semibold">Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i class="bi bi-lock"></i></span>
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Minimal 6 karakter" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="confirm_password" class="form-label font-semibold">Konfirmasi Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i class="bi bi-shield-check"></i></span>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Ulangi password" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label font-semibold">
                                Nomor Telepon / WhatsApp <span class="text-danger">*</span>
                                <?php if (!empty($verifiedPhone)): ?>
                                    <span class="badge bg-success ms-2"><i class="bi bi-patch-check-fill me-1"></i> Terverifikasi via WhatsApp</span>
                                <?php endif; ?>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="bi bi-telephone"></i></span>
                                <input type="tel" class="form-control <?= !empty($verifiedPhone) ? 'is-valid bg-light' : '' ?>" id="phone" name="phone" value="<?= htmlspecialchars($verifiedPhone ?? '') ?>" placeholder="Contoh: 081234567890" required <?= !empty($verifiedPhone) ? 'readonly' : '' ?>>
                            </div>
                            <?php if (empty($verifiedPhone)): ?>
                                <div class="form-text small mt-1">
                                    <a href="<?= $baseUrl ?>/otp/login?action=register" class="text-decoration-none text-success fw-semibold">
                                        <i class="bi bi-whatsapp me-1"></i>Verifikasi nomor terlebih dahulu via WhatsApp OTP Fonte
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-4">
                            <label for="address" class="form-label font-semibold" id="address-label">
                                <?= $activeRole === 'technician' ? 'Area Jangkauan Layanan / Alamat Teknisi' : 'Alamat Rumah / Domisili Pelanggan' ?>
                            </label>
                            <textarea class="form-control" id="address" name="address" rows="3" placeholder="<?= $activeRole === 'technician' ? 'Contoh: Jakarta Barat, Kebon Jeruk, Kembangan (Area domisili jangkauan kerja servis)' : 'Contoh: Jl. Sudirman No. 45, Kelurahan, Kecamatan, Kota' ?>"></textarea>
                        </div>

                        <button type="submit" class="btn w-100 py-3 rounded-pill font-semibold shadow-sm mb-3 <?= $activeRole === 'technician' ? 'btn-warning text-dark' : 'btn-primary' ?>" id="submit-reg-btn">
                            <i class="<?= $activeRole === 'technician' ? 'bi bi-tools' : 'bi bi-check-circle' ?> me-1" id="submit-reg-icon"></i>
                            <span id="submit-reg-text">
                                <?= $activeRole === 'technician' ? 'Mendaftar sebagai Mitra Teknisi' : 'Mendaftar sebagai Customer' ?>
                            </span>
                        </button>

                        <div class="text-center text-muted small" id="login-redirect-hint">
                            <?php if ($activeRole === 'technician'): ?>
                                Sudah terdaftar sebagai teknisi? <a href="<?= $baseUrl ?>/login?role=technician" class="text-warning-emphasis fw-bold text-decoration-none">Masuk di Login Teknisi</a>
                            <?php else: ?>
                                Sudah memiliki akun? <a href="<?= $baseUrl ?>/login?role=customer" class="text-primary fw-bold text-decoration-none">Masuk di Login Customer</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const tabCustomer = document.getElementById('reg-tab-customer');
    const tabTechnician = document.getElementById('reg-tab-technician');
    const roleInput = document.getElementById('register_role_input');
    const headerTitle = document.getElementById('header-title');
    const headerDesc = document.getElementById('header-desc');
    const headerIconBox = document.getElementById('header-icon-box');
    const headerIcon = document.getElementById('header-icon');
    const registerHeader = document.getElementById('register-header');
    const roleInfoAlert = document.getElementById('role-info-alert');
    const roleInfoText = document.getElementById('role-info-text');
    const addressLabel = document.getElementById('address-label');
    const addressInput = document.getElementById('address');
    const submitBtn = document.getElementById('submit-reg-btn');
    const submitIcon = document.getElementById('submit-reg-icon');
    const submitText = document.getElementById('submit-reg-text');
    const loginHint = document.getElementById('login-redirect-hint');

    const baseUrl = '<?= $baseUrl ?>';

    function setRole(role, updateUrl = true) {
        if (role === 'technician') {
            // Tab states
            tabTechnician.className = 'btn flex-fill py-2 rounded-3 font-semibold d-flex align-items-center justify-content-center gap-2 btn-warning text-dark shadow-sm';
            tabCustomer.className = 'btn flex-fill py-2 rounded-3 font-semibold d-flex align-items-center justify-content-center gap-2 btn-outline-secondary border-0 text-muted';
            
            // Hidden input
            roleInput.value = 'technician';

            // Header card
            registerHeader.className = 'card-header text-white text-center py-4 bg-dark border-bottom border-warning border-3';
            headerIconBox.className = 'rounded-circle d-inline-flex align-items-center justify-content-center mb-2 shadow-sm bg-warning text-dark';
            headerIcon.className = 'bi bi-tools fs-3';
            headerTitle.textContent = 'Pendaftaran Akun Mitra Teknisi';
            headerDesc.textContent = 'Bergabung sebagai mitra teknisi profesional untuk menerima order & pekerjaan servis';

            // Notice
            roleInfoAlert.className = 'alert alert-warning border-warning rounded-3 small mb-4 d-flex align-items-start gap-2 shadow-none';
            roleInfoText.innerHTML = 'Akun ini akan didaftarkan sebagai <strong>Mitra Teknisi</strong>. Anda dapat menerima pesanan servis, mengonfirmasi kedatangan, dan mengelola pengerjaan perbaikan di lokasi pelanggan.';

            // Address
            addressLabel.textContent = 'Area Jangkauan Layanan / Alamat Teknisi';
            addressInput.placeholder = 'Contoh: Jakarta Barat, Kebon Jeruk, Kembangan (Area domisili jangkauan kerja servis)';

            // Button
            submitBtn.className = 'btn w-100 py-3 rounded-pill font-semibold shadow-sm mb-3 btn-warning text-dark';
            submitIcon.className = 'bi bi-tools me-1';
            submitText.textContent = 'Mendaftar sebagai Mitra Teknisi';

            // Footer hint
            loginHint.innerHTML = 'Sudah terdaftar sebagai teknisi? <a href="' + baseUrl + '/login?role=technician" class="text-warning-emphasis fw-bold text-decoration-none">Masuk di Login Teknisi</a>';

            if (updateUrl) {
                try {
                    window.history.replaceState({ role: 'technician' }, '', baseUrl + '/register?role=technician');
                } catch(e) {}
            }
        } else {
            // Tab states
            tabCustomer.className = 'btn flex-fill py-2 rounded-3 font-semibold d-flex align-items-center justify-content-center gap-2 btn-primary shadow-sm';
            tabTechnician.className = 'btn flex-fill py-2 rounded-3 font-semibold d-flex align-items-center justify-content-center gap-2 btn-outline-secondary border-0 text-muted';

            // Hidden input
            roleInput.value = 'customer';

            // Header card
            registerHeader.className = 'card-header text-white text-center py-4 bg-dark border-bottom border-primary border-3';
            headerIconBox.className = 'rounded-circle d-inline-flex align-items-center justify-content-center mb-2 shadow-sm bg-primary text-white';
            headerIcon.className = 'bi bi-person-plus-fill fs-3';
            headerTitle.textContent = 'Pendaftaran Akun Customer';
            headerDesc.textContent = 'Daftar sebagai pelanggan untuk memesan jasa perbaikan & perawatan peralatan rumah tangga';

            // Notice
            roleInfoAlert.className = 'alert alert-info border-info rounded-3 small mb-4 d-flex align-items-start gap-2 shadow-none';
            roleInfoText.innerHTML = 'Akun ini akan didaftarkan sebagai <strong>Customer (Pelanggan)</strong>. Anda dapat memesan jasa perbaikan AC, Kulkas, Mesin Cuci, dll., serta konsultasi AI diagnosis gratis.';

            // Address
            addressLabel.textContent = 'Alamat Rumah / Domisili Pelanggan';
            addressInput.placeholder = 'Contoh: Jl. Sudirman No. 45, Kelurahan, Kecamatan, Kota';

            // Button
            submitBtn.className = 'btn w-100 py-3 rounded-pill font-semibold shadow-sm mb-3 btn-primary';
            submitIcon.className = 'bi bi-check-circle me-1';
            submitText.textContent = 'Mendaftar sebagai Customer';

            // Footer hint
            loginHint.innerHTML = 'Sudah memiliki akun? <a href="' + baseUrl + '/login?role=customer" class="text-primary fw-bold text-decoration-none">Masuk di Login Customer</a>';

            if (updateUrl) {
                try {
                    window.history.replaceState({ role: 'customer' }, '', baseUrl + '/register?role=customer');
                } catch(e) {}
            }
        }
    }

    if (tabCustomer) {
        tabCustomer.addEventListener('click', () => setRole('customer'));
    }
    if (tabTechnician) {
        tabTechnician.addEventListener('click', () => setRole('technician'));
    }
});
</script>
