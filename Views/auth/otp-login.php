<?php
$baseUrl = \Config\Env::get('APP_URL', 'http://localhost/Myjasagwuehhhhhhhhhhhhhhhhhhhhh');
$assetUrl = rtrim($baseUrl, '/') . '/Assets';
$action = $action ?? 'login';
$role = $role ?? 'customer';
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

            <!-- Card Utama OTP -->
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden" style="background: #ffffff;">
                
                <!-- Header Card dengan Gradien Elegan & Aksen WhatsApp -->
                <div class="p-4 text-center text-white" style="background: linear-gradient(135deg, #075E54 0%, #128C7E 50%, #25D366 100%);">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-white text-success shadow-sm mb-3" style="width: 64px; height: 64px;">
                        <i class="bi bi-whatsapp fs-1 text-success"></i>
                    </div>
                    <h4 class="fw-bold mb-1">Masuk via WhatsApp OTP</h4>
                    <p class="small text-white-50 mb-0">
                        Login cepat & aman tanpa perlu mengingat kata sandi
                    </p>
                    
                    <!-- Live Gateway Indicator -->
                    <div class="mt-3">
                        <?php if (!empty($fonteStatus['connected'])): ?>
                            <span class="badge bg-white text-dark rounded-pill px-3 py-1 shadow-sm small fw-semibold d-inline-flex align-items-center gap-2" style="font-size: 0.78rem;">
                                <span class="spinner-grow spinner-grow-sm text-success" style="width: 8px; height: 8px;" role="status"></span>
                                WhatsApp Gateway Fonte Online
                            </span>
                        <?php else: ?>
                            <span class="badge bg-dark bg-opacity-75 text-white rounded-pill px-3 py-1 small fw-semibold d-inline-flex align-items-center gap-2" style="font-size: 0.78rem;">
                                <i class="bi bi-shield-lock-fill text-warning"></i>
                                Gateway Terenkripsi
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Body Card -->
                <div class="card-body p-4 p-md-5">
                    
                    <!-- Info Alert -->
                    <div class="alert alert-light border rounded-3 p-3 mb-4 d-flex align-items-start gap-2 shadow-none" style="background-color: #f8fafc;">
                        <i class="bi bi-shield-check fs-4 text-success mt-0"></i>
                        <div class="small text-muted">
                            Masukkan nomor WhatsApp aktif Anda. Kami akan mengirimkan <strong>6 digit kode verifikasi (OTP)</strong> melalui pesan resmi WhatsApp.
                        </div>
                    </div>

                    <!-- Formulir Kirim OTP -->
                    <form action="<?= $baseUrl ?>/otp/send" method="POST" id="form-send-otp">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
                        <input type="hidden" name="action" value="<?= htmlspecialchars($action) ?>">
                        <input type="hidden" name="role" value="<?= htmlspecialchars($role) ?>">

                        <div class="mb-4">
                            <label for="phone" class="form-label fw-bold text-dark small text-uppercase letter-spacing-1">
                                Nomor WhatsApp <span class="text-danger">*</span>
                            </label>
                            
                            <div class="input-group input-group-lg shadow-sm rounded-3 overflow-hidden border">
                                <span class="input-group-text bg-light border-0 px-3 fw-bold text-secondary d-flex align-items-center gap-1">
                                    <span style="font-size: 1.15rem;">🇮🇩</span>
                                    <span>+62</span>
                                </span>
                                <input type="tel" 
                                       name="phone" 
                                       id="phone" 
                                       class="form-control border-0 ps-2 fs-6 fw-semibold" 
                                       placeholder="812-3456-7890" 
                                       required 
                                       autofocus 
                                       autocomplete="tel"
                                       pattern="[0-9\s\-+]{9,16}"
                                       title="Masukkan nomor telepon seluler Indonesia yang valid">
                            </div>
                            <div class="form-text small mt-2 text-muted">
                                <i class="bi bi-info-circle me-1"></i> Format dapat diawali dengan <code>08...</code> atau langsung <code>8...</code>
                            </div>
                        </div>

                        <!-- Tombol Kirim OTP dengan Loading State -->
                        <button type="submit" 
                                class="btn w-100 py-3 rounded-pill fw-bold text-white shadow d-flex align-items-center justify-content-center gap-2 mb-3" 
                                id="btn-submit-otp"
                                style="background: linear-gradient(135deg, #128C7E 0%, #25D366 100%); border: none;">
                            <span class="spinner-border spinner-border-sm d-none" id="btn-spinner" role="status" aria-hidden="true"></span>
                            <i class="bi bi-send-fill" id="btn-icon"></i>
                            <span id="btn-text">Kirim Kode OTP via WhatsApp</span>
                        </button>
                    </form>

                    <!-- Navigasi Alternatif -->
                    <div class="border-top pt-3 mt-4 text-center">
                        <div class="d-flex flex-column gap-2 small">
                            <div>
                                <span class="text-muted">Lebih memilih kata sandi?</span>
                                <a href="<?= $baseUrl ?>/login" class="fw-bold text-decoration-none ms-1" style="color: #075E54;">
                                    <i class="bi bi-key-fill me-1"></i>Login dengan Email & Password
                                </a>
                            </div>
                            <div>
                                <span class="text-muted">Belum punya akun?</span>
                                <a href="<?= $baseUrl ?>/register?role=customer" class="fw-bold text-primary text-decoration-none ms-1">
                                    Daftar Customer Baru
                                </a>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Footer Keamanan -->
                <div class="card-footer bg-light py-3 px-4 border-top text-center small text-muted">
                    <i class="bi bi-lock-fill text-success me-1"></i>
                    Kode OTP dilindungi enkripsi satu arah & dibatasi batas waktu demi privasi Anda.
                </div>

            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('form-send-otp');
    const phoneInput = document.getElementById('phone');
    const submitBtn = document.getElementById('btn-submit-otp');
    const btnSpinner = document.getElementById('btn-spinner');
    const btnIcon = document.getElementById('btn-icon');
    const btnText = document.getElementById('btn-text');

    // Form submission loading state
    if (form) {
        form.addEventListener('submit', function () {
            submitBtn.disabled = true;
            btnSpinner.classList.remove('d-none');
            btnIcon.classList.add('d-none');
            btnText.textContent = 'Menghubungkan ke Fonte...';
        });
    }
});
</script>
