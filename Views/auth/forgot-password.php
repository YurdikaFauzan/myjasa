<?php
$baseUrl = \Config\Env::get('APP_URL', 'http://localhost/Myjasagwuehhhhhhhhhhhhhhhhhhhhh');
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">

            <!-- Flash Alert Message -->
            <?php if (!empty($flash)): ?>
                <div class="alert alert-<?= htmlspecialchars($flash['type']) ?> alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    <?= htmlspecialchars($flash['message']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-danger text-white text-center py-4">
                    <div class="bg-white text-danger rounded-circle d-inline-flex align-items-center justify-content-center mb-2 shadow-sm" style="width: 50px; height: 50px;">
                        <i class="bi bi-shield-lock fs-4"></i>
                    </div>
                    <h4 class="fw-bold mb-0">Lupa Sandi</h4>
                    <p class="small text-white-50 mb-0">Reset sandi Anda dengan verifikasi Email dan Nomor HP</p>
                </div>

                <div class="card-body p-4">
                    <form action="<?= $baseUrl ?>/forgot-password" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

                        <div class="mb-3">
                            <label for="email" class="form-label font-semibold">Alamat Email Terdaftar</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email" placeholder="nama@email.com" required autofocus>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label font-semibold">Nomor HP Terdaftar</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-telephone"></i></span>
                                <input type="text" class="form-control" id="phone" name="phone" placeholder="0812xxxxxx" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label font-semibold">Password Baru</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-key"></i></span>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Minimal 6 karakter" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="confirm_password" class="form-label font-semibold">Konfirmasi Password Baru</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-check-circle"></i></span>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Ketik ulang password baru" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-danger w-100 py-2 rounded-pill font-semibold shadow-sm mb-3">
                            <i class="bi bi-arrow-clockwise me-1"></i> Reset Password
                        </button>

                        <div class="text-center text-muted small">
                            Kembali ke <a href="<?= $baseUrl ?>/login" class="text-danger text-decoration-none font-semibold">Halaman Login</a>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
