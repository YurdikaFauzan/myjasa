<?php
$baseUrl = \Config\Env::get('APP_URL', 'http://localhost/Myjasagwuehhhhhhhhhhhhhhhhhhhhh');
$role = $user['role'] ?? 'customer';
$userInitials = '';
$nameParts = explode(' ', trim($user['name'] ?? 'User'));
foreach (array_slice($nameParts, 0, 2) as $part) {
    $userInitials .= strtoupper(substr($part, 0, 1));
}

$roleTheme = match($role) {
    'admin'      => ['bg' => 'linear-gradient(135deg, #0f172a 0%, #1e293b 100%)', 'badge' => 'bg-dark', 'label' => 'Administrator', 'icon' => 'bi-shield-lock-fill'],
    'technician' => ['bg' => 'linear-gradient(135deg, #b45309 0%, #78350f 100%)', 'badge' => 'bg-warning text-dark', 'label' => 'Mitra Teknisi', 'icon' => 'bi-tools'],
    default      => ['bg' => 'linear-gradient(135deg, #A06138 0%, #743E1F 100%)', 'badge' => 'bg-primary', 'label' => 'Customer', 'icon' => 'bi-person-heart']
};
?>

<div class="<?= in_array($role, ['admin', 'technician']) ? 'container-fluid py-4' : 'container-fluid px-lg-5 py-4' ?>">
    <div class="row g-4">

        <?php if (in_array($role, ['admin', 'technician'])): ?>
            <!-- Sidebar for Admin & Technician -->
            <div class="col-lg-3 col-xl-2">
                <?php require __DIR__ . '/../layouts/sidebar.php'; ?>
            </div>
            <div class="col-lg-9 col-xl-10">
        <?php else: ?>
            <div class="col-12">
        <?php endif; ?>

            <!-- Flash Alert -->
            <?php if (!empty($flash)): ?>
                <div class="alert alert-<?= htmlspecialchars($flash['type'] ?? 'info') ?> alert-dismissible fade show rounded-4 shadow-sm mb-4 border-0" role="alert">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi <?= ($flash['type'] ?? '') === 'danger' ? 'bi-exclamation-triangle-fill text-danger' : 'bi-check-circle-fill text-success' ?> fs-5"></i>
                        <div class="fw-semibold"><?= htmlspecialchars($flash['message'] ?? '') ?></div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Profile Hero Banner -->
            <div class="card border-0 text-white rounded-4 shadow-sm p-4 mb-4 position-relative overflow-hidden" style="background: <?= $roleTheme['bg'] ?>;">
                <div class="position-absolute end-0 bottom-0 opacity-10 pointer-events-none p-3" style="font-size: 14rem; transform: translate(15%, 25%);">
                    <i class="bi <?= $roleTheme['icon'] ?>"></i>
                </div>

                <div class="row align-items-center gy-3 position-relative" style="z-index: 1;">
                    <div class="col-md-auto text-center text-md-start">
                        <!-- Avatar Circle -->
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center text-white fw-bold shadow-lg" 
                             style="width: 86px; height: 86px; font-size: 2rem; background: rgba(255, 255, 255, 0.22); border: 3px solid rgba(255, 255, 255, 0.45); backdrop-filter: blur(8px);">
                            <?= htmlspecialchars($userInitials) ?>
                        </div>
                    </div>

                    <div class="col-md">
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                            <h3 class="fw-bold mb-0 text-white"><?= htmlspecialchars($user['name']) ?></h3>
                            <span class="badge <?= $roleTheme['badge'] ?> rounded-pill px-3 py-1 font-semibold small shadow-sm">
                                <i class="bi <?= $roleTheme['icon'] ?> me-1"></i><?= $roleTheme['label'] ?>
                            </span>
                            <?php if (!empty($user['status']) && $user['status'] === 'active'): ?>
                                <span class="badge bg-success-subtle text-success rounded-pill px-2 py-1 small">
                                    <i class="bi bi-check-circle-fill me-1"></i>Akun Aktif
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="d-flex flex-wrap align-items-center gap-3 text-white-50 small mt-2">
                            <span><i class="bi bi-envelope me-1"></i><?= htmlspecialchars($user['email']) ?></span>
                            <span><i class="bi bi-whatsapp me-1"></i><?= htmlspecialchars($user['phone'] ?? 'Belum diisi') ?></span>
                            <span><i class="bi bi-calendar3 me-1"></i>Bergabung: <?= date('d M Y', strtotime($user['created_at'] ?? 'now')) ?></span>
                        </div>
                    </div>

                    <!-- Role-Specific Action Shortcuts -->
                    <div class="col-md-auto text-md-end">
                        <?php if ($role === 'customer'): ?>
                            <a href="<?= $baseUrl ?>/customer/requests/create" class="btn btn-light rounded-pill px-3 py-2 fw-semibold shadow-sm text-dark">
                                <i class="bi bi-tools text-primary me-1"></i> Pesan Jasa Baru
                            </a>
                        <?php elseif ($role === 'technician'): ?>
                            <a href="<?= $baseUrl ?>/technician/jobs?status=assigned" class="btn btn-light rounded-pill px-3 py-2 fw-semibold shadow-sm text-dark">
                                <i class="bi bi-bell-fill text-warning me-1"></i> Cek Job Masuk
                            </a>
                        <?php elseif ($role === 'admin'): ?>
                            <a href="<?= $baseUrl ?>/admin/dashboard" class="btn btn-light rounded-pill px-3 py-2 fw-semibold shadow-sm text-dark">
                                <i class="bi bi-speedometer2 text-primary me-1"></i> Dashboard Admin
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Activity Statistics Grid -->
            <div class="row g-3 mb-4">
                <?php if ($role === 'customer'): ?>
                    <div class="col-sm-6 col-xl-3">
                        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="text-muted small fw-semibold">Total Pesanan</span>
                                    <h4 class="fw-bold mb-0 mt-1"><?= (int)($stats['total_orders'] ?? 0) ?></h4>
                                </div>
                                <div class="bg-primary-subtle text-primary p-3 rounded-circle">
                                    <i class="bi bi-clipboard-check fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="text-muted small fw-semibold">Pesanan Aktif</span>
                                    <h4 class="fw-bold mb-0 mt-1 text-warning"><?= (int)($stats['active_orders'] ?? 0) ?></h4>
                                </div>
                                <div class="bg-warning-subtle text-warning p-3 rounded-circle">
                                    <i class="bi bi-hourglass-split fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="text-muted small fw-semibold">Servis Selesai</span>
                                    <h4 class="fw-bold mb-0 mt-1 text-success"><?= (int)($stats['completed_orders'] ?? 0) ?></h4>
                                </div>
                                <div class="bg-success-subtle text-success p-3 rounded-circle">
                                    <i class="bi bi-patch-check-fill fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="text-muted small fw-semibold">Konsultasi AI</span>
                                    <h4 class="fw-bold mb-0 mt-1 text-info"><?= (int)($stats['ai_consultations'] ?? 0) ?></h4>
                                </div>
                                <div class="bg-info-subtle text-info p-3 rounded-circle">
                                    <i class="bi bi-robot fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php elseif ($role === 'technician'): ?>
                    <div class="col-sm-4">
                        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="text-muted small fw-semibold">Total Tugas Servis</span>
                                    <h4 class="fw-bold mb-0 mt-1"><?= (int)($stats['total_jobs'] ?? 0) ?></h4>
                                </div>
                                <div class="bg-warning-subtle text-warning p-3 rounded-circle">
                                    <i class="bi bi-tools fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="text-muted small fw-semibold">Pekerjaan Sedang Berjalan</span>
                                    <h4 class="fw-bold mb-0 mt-1 text-primary"><?= (int)($stats['active_jobs'] ?? 0) ?></h4>
                                </div>
                                <div class="bg-primary-subtle text-primary p-3 rounded-circle">
                                    <i class="bi bi-gear-wide-connected fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="text-muted small fw-semibold">Servis Sukses Selesai</span>
                                    <h4 class="fw-bold mb-0 mt-1 text-success"><?= (int)($stats['completed_jobs'] ?? 0) ?></h4>
                                </div>
                                <div class="bg-success-subtle text-success p-3 rounded-circle">
                                    <i class="bi bi-check-circle-fill fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="col-sm-4">
                        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="text-muted small fw-semibold">Total Pengguna Sistem</span>
                                    <h4 class="fw-bold mb-0 mt-1"><?= (int)($stats['total_users'] ?? 0) ?></h4>
                                </div>
                                <div class="bg-primary-subtle text-primary p-3 rounded-circle">
                                    <i class="bi bi-people-fill fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="text-muted small fw-semibold">Total Pesanan Servis</span>
                                    <h4 class="fw-bold mb-0 mt-1 text-info"><?= (int)($stats['total_requests'] ?? 0) ?></h4>
                                </div>
                                <div class="bg-info-subtle text-info p-3 rounded-circle">
                                    <i class="bi bi-clipboard-data-fill fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="text-muted small fw-semibold">Layanan Aktif</span>
                                    <h4 class="fw-bold mb-0 mt-1 text-success"><?= (int)($stats['total_services'] ?? 0) ?></h4>
                                </div>
                                <div class="bg-success-subtle text-success p-3 rounded-circle">
                                    <i class="bi bi-tags-fill fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Profile Forms Tabs Card -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-4">
                <div class="card-header bg-white border-bottom p-3">
                    <ul class="nav nav-pills card-header-pills gap-2" id="profileTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active rounded-pill fw-semibold py-2 px-3 small" id="edit-profile-tab" data-bs-toggle="tab" data-bs-target="#edit-profile-pane" type="button" role="tab">
                                <i class="bi bi-person-lines-fill me-1"></i> Informasi Akun & Edit Profil
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-pill fw-semibold py-2 px-3 small" id="security-tab" data-bs-toggle="tab" data-bs-target="#security-pane" type="button" role="tab">
                                <i class="bi bi-shield-lock-fill me-1"></i> Keamanan & Ganti Sandi
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="card-body p-4">
                    <div class="tab-content" id="profileTabContent">

                        <!-- TAB 1: Edit Profile Form -->
                        <div class="tab-pane fade show active" id="edit-profile-pane" role="tabpanel" tabindex="0">
                            <form action="<?= $baseUrl ?>/profile/update" method="POST" autocomplete="off">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">

                                <div class="row g-3">
                                    <!-- Full Name -->
                                    <div class="col-md-6">
                                        <label for="name" class="form-label fw-semibold small text-muted">Nama Lengkap</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0 rounded-start-3"><i class="bi bi-person text-secondary"></i></span>
                                            <input type="text" name="name" id="name" class="form-control rounded-end-3" value="<?= htmlspecialchars($user['name'] ?? '') ?>" required minlength="3">
                                        </div>
                                    </div>

                                    <!-- Email Address (Read-Only) -->
                                    <div class="col-md-6">
                                        <label for="email" class="form-label fw-semibold small text-muted">
                                            Alamat Email <span class="badge bg-secondary-subtle text-secondary rounded-pill small">Permanen</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0 rounded-start-3"><i class="bi bi-envelope text-secondary"></i></span>
                                            <input type="email" id="email" class="form-control rounded-end-3 bg-light" value="<?= htmlspecialchars($user['email'] ?? '') ?>" readonly>
                                        </div>
                                        <div class="form-text" style="font-size: 0.75rem;">Email berfungsi sebagai identitas unik login dan tidak dapat diubah sembarangan.</div>
                                    </div>

                                    <!-- Phone Number -->
                                    <div class="col-md-6">
                                        <label for="phone" class="form-label fw-semibold small text-muted">Nomor WhatsApp / Telepon</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0 rounded-start-3"><i class="bi bi-telephone text-secondary"></i></span>
                                            <input type="text" name="phone" id="phone" class="form-control rounded-end-3" placeholder="Contoh: 081298765432" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                                        </div>
                                        <div class="form-text" style="font-size: 0.75rem;">Digunakan untuk konfirmasi kedatangan teknisi dan notifikasi OTP.</div>
                                    </div>

                                    <!-- Role & Status -->
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small text-muted">Tipe Akun (Role)</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0 rounded-start-3"><i class="bi bi-shield-check text-secondary"></i></span>
                                            <input type="text" class="form-control rounded-end-3 bg-light" value="<?= $roleTheme['label'] ?>" readonly>
                                        </div>
                                    </div>

                                    <!-- Complete Address -->
                                    <div class="col-12">
                                        <label for="address" class="form-label fw-semibold small text-muted">Alamat Tempat Tinggal / Operasional</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0 rounded-start-3"><i class="bi bi-geo-alt text-secondary"></i></span>
                                            <textarea name="address" id="address" rows="3" class="form-control rounded-end-3" placeholder="Masukkan alamat lengkap (Jalan, RT/RW, Kelurahan, Kecamatan, Kota)..."><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end mt-4">
                                    <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold shadow-sm">
                                        <i class="bi bi-check2-circle me-1"></i> Simpan Perubahan Profil
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- TAB 2: Change Password Form -->
                        <div class="tab-pane fade" id="security-pane" role="tabpanel" tabindex="0">
                            <form action="<?= $baseUrl ?>/profile/change-password" method="POST" autocomplete="off">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">

                                <div class="row g-3" style="max-width: 600px;">
                                    <div class="col-12">
                                        <label for="current_password" class="form-label fw-semibold small text-muted">Kata Sandi Saat Ini</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0 rounded-start-3"><i class="bi bi-key text-secondary"></i></span>
                                            <input type="password" name="current_password" id="current_password" class="form-control" placeholder="••••••••••••" required>
                                            <button class="btn btn-outline-secondary rounded-end-3 toggle-password-visibility" type="button" data-target="current_password">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <label for="new_password" class="form-label fw-semibold small text-muted">Kata Sandi Baru (Min. 6 Karakter)</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0 rounded-start-3"><i class="bi bi-lock text-secondary"></i></span>
                                            <input type="password" name="new_password" id="new_password" class="form-control" placeholder="••••••••••••" required minlength="6">
                                            <button class="btn btn-outline-secondary rounded-end-3 toggle-password-visibility" type="button" data-target="new_password">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <label for="confirm_password" class="form-label fw-semibold small text-muted">Ulangi Kata Sandi Baru</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0 rounded-start-3"><i class="bi bi-shield-lock text-secondary"></i></span>
                                            <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="••••••••••••" required minlength="6">
                                            <button class="btn btn-outline-secondary rounded-end-3 toggle-password-visibility" type="button" data-target="confirm_password">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="col-12 mt-4">
                                        <button type="submit" class="btn btn-danger rounded-pill px-4 py-2 fw-semibold shadow-sm">
                                            <i class="bi bi-shield-check me-1"></i> Perbarui Kata Sandi
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Password visibility toggle handler
    document.querySelectorAll('.toggle-password-visibility').forEach(btn => {
        btn.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);
            const icon = this.querySelector('i');
            if (input && icon) {
                const isPassword = input.type === 'password';
                input.type = isPassword ? 'text' : 'password';
                icon.className = isPassword ? 'bi bi-eye-slash' : 'bi bi-eye';
            }
        });
    });
});
</script>
