<?php
$baseUrl = \Config\Env::get('APP_URL', 'http://localhost/Myjasagwuehhhhhhhhhhhhhhhhhhhhh');
$assetUrl = rtrim($baseUrl, '/') . '/Assets';
$phone = $phone ?? '';
$displayPhone = $displayPhone ?? htmlspecialchars($phone);
$maskedPhone = $maskedPhone ?? htmlspecialchars($phone);
$remainingExpiry = (int)($remainingExpiry ?? 300);
$remainingCooldown = (int)($remainingCooldown ?? 60);
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-5">

            <!-- Flash Message Alert -->
            <?php if (!empty($flash)): ?>
                <div class="alert alert-<?= htmlspecialchars($flash['type']) ?> alert-dismissible fade show rounded-4 shadow-sm border-0 mb-4" role="alert">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi <?= $flash['type'] === 'success' ? 'bi-check-circle-fill' : ($flash['type'] === 'danger' ? 'bi-exclamation-triangle-fill' : 'bi-info-circle-fill') ?> fs-5"></i>
                        <div class="small fw-semibold"><?= htmlspecialchars($flash['message']) ?></div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Card Utama Verifikasi OTP -->
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden" style="background: #ffffff;">
                
                <!-- Header Card dengan Nuansa WhatsApp -->
                <div class="p-4 text-center text-white" style="background: linear-gradient(135deg, #075E54 0%, #128C7E 50%, #25D366 100%);">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-white text-success shadow-sm mb-3" style="width: 64px; height: 64px;">
                        <i class="bi bi-chat-left-dots-fill fs-2 text-success"></i>
                    </div>
                    <h4 class="fw-bold mb-1">Verifikasi Kode OTP</h4>
                    <p class="small text-white-50 mb-0">
                        Masukkan 6 digit kode yang dikirimkan ke nomor WhatsApp Anda
                    </p>

                    <!-- Nomor WhatsApp Penerima -->
                    <div class="mt-3">
                        <span class="badge bg-white text-dark rounded-pill px-3 py-2 shadow-sm fs-6 fw-bold d-inline-flex align-items-center gap-2">
                            <i class="bi bi-whatsapp text-success"></i>
                            <span id="masked-phone-text"><?= htmlspecialchars($maskedPhone) ?></span>
                        </span>
                    </div>
                </div>

                <!-- Body Card -->
                <div class="card-body p-4 p-md-5">

                    <!-- Countdown Timer Expiry Banner -->
                    <div class="d-flex align-items-center justify-content-between p-3 rounded-3 mb-4 border bg-light">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-clock-history fs-5 text-warning"></i>
                            <div>
                                <div class="small fw-bold text-dark">Masa Berlaku Kode</div>
                                <div class="text-muted" style="font-size: 0.75rem;">Kode OTP otomatis hangus setelah waktu habis</div>
                            </div>
                        </div>
                        <div class="fs-5 fw-bold text-primary font-monospace" id="expiry-timer">05:00</div>
                    </div>

                    <!-- Formulir Input Kode OTP 6 Digit -->
                    <form action="<?= $baseUrl ?>/otp/verify" method="POST" id="form-verify-otp">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
                        
                        <div class="mb-4 text-center">
                            <label class="form-label fw-bold text-dark small text-uppercase letter-spacing-1 mb-3">
                                Masukkan 6 Digit Kode OTP
                            </label>

                            <!-- Segmented 6 Box Inputs -->
                            <div class="d-flex justify-content-center gap-2 mb-3" id="otp-digit-boxes">
                                <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" class="form-control text-center fw-bold fs-3 otp-box rounded-3 border" style="width: 48px; height: 58px;" data-index="0" autofocus>
                                <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" class="form-control text-center fw-bold fs-3 otp-box rounded-3 border" style="width: 48px; height: 58px;" data-index="1">
                                <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" class="form-control text-center fw-bold fs-3 otp-box rounded-3 border" style="width: 48px; height: 58px;" data-index="2">
                                <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" class="form-control text-center fw-bold fs-3 otp-box rounded-3 border" style="width: 48px; height: 58px;" data-index="3">
                                <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" class="form-control text-center fw-bold fs-3 otp-box rounded-3 border" style="width: 48px; height: 58px;" data-index="4">
                                <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" class="form-control text-center fw-bold fs-3 otp-box rounded-3 border" style="width: 48px; height: 58px;" data-index="5">
                            </div>

                            <!-- Hidden Master OTP input submitted to backend -->
                            <input type="hidden" name="otp_code" id="master-otp-input" value="" required>

                            <div class="form-text small text-muted">
                                <i class="bi bi-shield-lock me-1"></i> Jangan bagikan kode ini kepada siapapun demi keamanan akun.
                            </div>
                        </div>

                        <!-- Tombol Verifikasi OTP -->
                        <button type="submit" 
                                class="btn w-100 py-3 rounded-pill fw-bold text-white shadow d-flex align-items-center justify-content-center gap-2 mb-3" 
                                id="btn-verify-submit"
                                style="background: linear-gradient(135deg, #075E54 0%, #128C7E 100%); border: none;">
                            <span class="spinner-border spinner-border-sm d-none" id="verify-spinner" role="status" aria-hidden="true"></span>
                            <i class="bi bi-check-circle-fill" id="verify-icon"></i>
                            <span id="verify-text">Verifikasi Kode OTP</span>
                        </button>
                    </form>

                    <!-- Baris Tombol Kirim Ulang & Ganti Nomor -->
                    <div class="d-flex flex-column flex-sm-row align-items-center justify-content-between gap-2 pt-3 border-top mt-4">
                        
                        <!-- Form Kirim Ulang OTP -->
                        <form action="<?= $baseUrl ?>/otp/resend" method="POST" id="form-resend-otp" class="w-100 w-sm-auto mb-2 mb-sm-0">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
                            <button type="submit" 
                                    class="btn btn-outline-success btn-sm rounded-pill px-3 py-2 w-100 fw-semibold d-inline-flex align-items-center justify-content-center gap-1" 
                                    id="btn-resend-otp"
                                    disabled>
                                <span class="spinner-border spinner-border-sm d-none" id="resend-spinner"></span>
                                <i class="bi bi-arrow-repeat" id="resend-icon"></i>
                                <span id="resend-text">Kirim Ulang (<?= $remainingCooldown ?>s)</span>
                            </button>
                        </form>

                        <!-- Tombol Ganti Nomor Telepon -->
                        <a href="<?= $baseUrl ?>/otp/cancel" 
                           class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-2 w-100 w-sm-auto fw-semibold d-inline-flex align-items-center justify-content-center gap-1"
                           id="btn-change-phone">
                            <i class="bi bi-arrow-left"></i>
                            <span>Ganti Nomor</span>
                        </a>

                    </div>

                    <!-- Link Bantuan ke Login Konvensional -->
                    <div class="text-center mt-4 pt-2">
                        <a href="<?= $baseUrl ?>/login" class="text-muted small text-decoration-none">
                            <i class="bi bi-arrow-left-circle me-1"></i>Kembali ke Halaman Login Standar
                        </a>
                    </div>

                </div>

                <!-- Footer Keamanan -->
                <div class="card-footer bg-light py-3 px-4 border-top text-center small text-muted">
                    <i class="bi bi-shield-check text-success me-1"></i>
                    Verifikasi resmi ditenagai oleh WhatsApp Gateway Fonte & SmartService AI
                </div>

            </div>

        </div>
    </div>
</div>

<style>
.otp-box:focus {
    border-color: #25D366 !important;
    box-shadow: 0 0 0 0.25rem rgba(37, 211, 102, 0.25) !important;
    outline: none;
}
.otp-box.filled {
    background-color: #f0fdf4;
    border-color: #86efac;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const boxes = document.querySelectorAll('.otp-box');
    const masterInput = document.getElementById('master-otp-input');
    const verifyForm = document.getElementById('form-verify-otp');
    const verifyBtn = document.getElementById('btn-verify-submit');
    const verifySpinner = document.getElementById('verify-spinner');
    const verifyIcon = document.getElementById('verify-icon');
    const verifyText = document.getElementById('verify-text');

    const resendForm = document.getElementById('form-resend-otp');
    const resendBtn = document.getElementById('btn-resend-otp');
    const resendSpinner = document.getElementById('resend-spinner');
    const resendIcon = document.getElementById('resend-icon');
    const resendText = document.getElementById('resend-text');

    const expiryTimerEl = document.getElementById('expiry-timer');

    // Sync boxes to master input
    function updateMasterInput() {
        let code = '';
        boxes.forEach(box => {
            code += box.value;
            if (box.value) {
                box.classList.add('filled');
            } else {
                box.classList.remove('filled');
            }
        });
        masterInput.value = code;
    }

    // Handle segmented boxes typing, backspace, and paste
    boxes.forEach((box, index) => {
        box.addEventListener('input', function (e) {
            this.value = this.value.replace(/[^0-9]/g, '');
            if (this.value.length >= 1) {
                this.value = this.value.charAt(0);
                if (index < boxes.length - 1) {
                    boxes[index + 1].focus();
                }
            }
            updateMasterInput();
            if (masterInput.value.length === 6) {
                // Auto submit when all 6 digits are typed
                verifyForm.requestSubmit();
            }
        });

        box.addEventListener('keydown', function (e) {
            if (e.key === 'Backspace') {
                if (!this.value && index > 0) {
                    boxes[index - 1].focus();
                    boxes[index - 1].value = '';
                }
                updateMasterInput();
            } else if (e.key === 'ArrowLeft' && index > 0) {
                boxes[index - 1].focus();
            } else if (e.key === 'ArrowRight' && index < boxes.length - 1) {
                boxes[index + 1].focus();
            }
        });

        // Paste support: paste full 6-digit code
        box.addEventListener('paste', function (e) {
            e.preventDefault();
            const pastedData = (e.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g, '');
            if (pastedData.length > 0) {
                for (let i = 0; i < boxes.length; i++) {
                    if (i < pastedData.length) {
                        boxes[i].value = pastedData[i];
                    }
                }
                const nextFocus = Math.min(pastedData.length, boxes.length - 1);
                boxes[nextFocus].focus();
                updateMasterInput();
                if (masterInput.value.length === 6) {
                    verifyForm.requestSubmit();
                }
            }
        });
    });

    // Verification submit loading
    if (verifyForm) {
        verifyForm.addEventListener('submit', function (e) {
            updateMasterInput();
            if (masterInput.value.length !== 6) {
                e.preventDefault();
                alert('Silakan masukkan 6 digit kode OTP secara lengkap.');
                boxes[0].focus();
                return;
            }
            verifyBtn.disabled = true;
            verifySpinner.classList.remove('d-none');
            verifyIcon.classList.add('d-none');
            verifyText.textContent = 'Memverifikasi...';
        });
    }

    // Resend submit loading
    if (resendForm) {
        resendForm.addEventListener('submit', function () {
            resendBtn.disabled = true;
            resendSpinner.classList.remove('d-none');
            resendIcon.classList.add('d-none');
            resendText.textContent = 'Mengirim Ulang...';
        });
    }

    // 1. Expiration Countdown Timer
    let expirySecs = <?= $remainingExpiry ?>;
    function updateExpiryDisplay() {
        if (expirySecs <= 0) {
            expiryTimerEl.textContent = 'Kedaluwarsa';
            expiryTimerEl.classList.remove('text-primary');
            expiryTimerEl.classList.add('text-danger');
            boxes.forEach(b => b.disabled = true);
            verifyBtn.disabled = true;
            verifyBtn.textContent = 'Kode OTP Telah Kedaluwarsa';
            return;
        }
        const m = Math.floor(expirySecs / 60).toString().padStart(2, '0');
        const s = (expirySecs % 60).toString().padStart(2, '0');
        expiryTimerEl.textContent = `${m}:${s}`;
        expirySecs--;
    }
    updateExpiryDisplay();
    const expiryInterval = setInterval(updateExpiryDisplay, 1000);

    // 2. Cooldown Countdown Timer for Resend Button
    let cooldownSecs = <?= $remainingCooldown ?>;
    function updateCooldownDisplay() {
        if (cooldownSecs <= 0) {
            resendBtn.disabled = false;
            resendText.textContent = 'Kirim Ulang Kode OTP';
            clearInterval(cooldownInterval);
            return;
        }
        resendBtn.disabled = true;
        resendText.textContent = `Kirim Ulang (${cooldownSecs}s)`;
        cooldownSecs--;
    }
    updateCooldownDisplay();
    const cooldownInterval = setInterval(updateCooldownDisplay, 1000);
});
</script>
