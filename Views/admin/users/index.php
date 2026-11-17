<?php
$baseUrl = \Config\Env::get('APP_URL', 'http://localhost/Myjasagwuehhhhhhhhhhhhhhhhhhhhh');
$filterRole = $filterRole ?? '';
$filterStatus = $filterStatus ?? '';
$search = $search ?? '';
?>

<div class="container-fluid py-4">
    <div class="row g-4">

        <!-- Sidebar Navigation -->
        <div class="col-lg-3 col-xl-2">
            <?php require __DIR__ . '/../../layouts/sidebar.php'; ?>
        </div>

        <!-- Main Content Area -->
        <div class="col-lg-9 col-xl-10">

            <!-- Flash Alert -->
            <?php if (!empty($flash)): ?>
                <div class="alert alert-<?= htmlspecialchars($flash['type']) ?> alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    <?= htmlspecialchars($flash['message']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Page Header -->
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 p-4 bg-white rounded-4 shadow-sm border">
                <div>
                    <h3 class="fw-bold text-dark mb-1"><i class="bi bi-people-fill text-primary me-2"></i> Manajemen Pengguna & Pendaftaran Akun</h3>
                    <p class="text-muted small mb-0">Kelola akun pengguna, lihat daftar akun, dan daftarkan akun baru (Admin, Teknisi, Customer).</p>
                </div>
                <div>
                    <button type="button" class="btn btn-primary rounded-pill font-semibold px-4 shadow-sm d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addUserModal">
                        <i class="bi bi-person-plus-fill fs-5"></i>
                        <span>Daftarkan Akun Baru</span>
                    </button>
                </div>
            </div>

            <!-- Role Summary Badges -->
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 p-3 border-start border-4 border-dark">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-muted small fw-semibold text-uppercase">Admin</div>
                                <div class="fs-4 fw-bold text-dark"><?= $roleCounts['admin'] ?? 0 ?></div>
                            </div>
                            <div class="p-2 bg-dark text-white rounded-3">
                                <i class="bi bi-shield-lock-fill fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 p-3 border-start border-4 border-warning">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-muted small fw-semibold text-uppercase">Teknisi</div>
                                <div class="fs-4 fw-bold text-dark"><?= $roleCounts['technician'] ?? 0 ?></div>
                            </div>
                            <div class="p-2 bg-warning text-dark rounded-3">
                                <i class="bi bi-tools fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 p-3 border-start border-4 border-primary">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-muted small fw-semibold text-uppercase">Customer</div>
                                <div class="fs-4 fw-bold text-dark"><?= $roleCounts['customer'] ?? 0 ?></div>
                            </div>
                            <div class="p-2 bg-primary text-white rounded-3">
                                <i class="bi bi-people-fill fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 p-3 border-start border-4 border-success">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-muted small fw-semibold text-uppercase">Total Pengguna</div>
                                <div class="fs-4 fw-bold text-dark"><?= count($users) ?></div>
                            </div>
                            <div class="p-2 bg-success text-white rounded-3">
                                <i class="bi bi-person-check-fill fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter & Search Bar -->
            <div class="card border-0 shadow-sm rounded-4 mb-4 p-3 bg-white">
                <form action="<?= $baseUrl ?>/admin/users" method="GET" class="row g-2 align-items-center">
                    <div class="col-12 col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-search"></i></span>
                            <input type="text" name="search" class="form-control border-start-0 bg-light" placeholder="Cari nama, email, telepon..." value="<?= htmlspecialchars($search) ?>">
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <select name="role" class="form-select bg-light">
                            <option value="">-- Semua Role --</option>
                            <option value="admin" <?= $filterRole === 'admin' ? 'selected' : '' ?>>Admin</option>
                            <option value="technician" <?= $filterRole === 'technician' ? 'selected' : '' ?>>Teknisi</option>
                            <option value="customer" <?= $filterRole === 'customer' ? 'selected' : '' ?>>Customer</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-3">
                        <select name="status" class="form-select bg-light">
                            <option value="">-- Semua Status --</option>
                            <option value="active" <?= $filterStatus === 'active' ? 'selected' : '' ?>>Aktif</option>
                            <option value="inactive" <?= $filterStatus === 'inactive' ? 'selected' : '' ?>>Nonaktif</option>
                            <option value="suspended" <?= $filterStatus === 'suspended' ? 'selected' : '' ?>>Ditangguhkan</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary rounded-pill w-100 font-semibold">
                            <i class="bi bi-filter me-1"></i> Filter
                        </button>
                        <?php if (!empty($search) || !empty($filterRole) || !empty($filterStatus)): ?>
                            <a href="<?= $baseUrl ?>/admin/users" class="btn btn-outline-secondary rounded-pill" title="Reset Filter">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Users Data Table -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-card-list me-2 text-primary"></i> Daftar Akun Pengguna</h5>
                    <span class="badge bg-primary-subtle text-primary font-semibold rounded-pill px-3 py-2"><?= count($users) ?> Akun</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 70px;">ID</th>
                                <th>Nama Lengkap</th>
                                <th>Email</th>
                                <th style="width: 140px;">Role</th>
                                <th>No. Telepon</th>
                                <th>Alamat</th>
                                <th style="width: 120px;">Status</th>
                                <th style="width: 180px;" class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($users)): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <i class="bi bi-person-x fs-1 d-block mb-2"></i>
                                        Tidak ada akun pengguna yang sesuai dengan kriteria filter.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($users as $u): ?>
                                    <tr>
                                        <td class="fw-bold text-muted">#<?= $u['id'] ?></td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white font-semibold <?= match($u['role']) { 'admin'=>'bg-dark', 'technician'=>'bg-warning text-dark', 'customer'=>'bg-primary', default=>'bg-secondary' } ?>" style="width: 36px; height: 36px; font-size: 0.85rem;">
                                                    <?= strtoupper(substr($u['name'], 0, 1)) ?>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark"><?= htmlspecialchars($u['name']) ?></div>
                                                    <small class="text-muted">Terdaftar: <?= date('d M Y', strtotime($u['created_at'])) ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="mailto:<?= htmlspecialchars($u['email']) ?>" class="text-decoration-none text-dark small">
                                                <i class="bi bi-envelope text-muted me-1"></i><?= htmlspecialchars($u['email']) ?>
                                            </a>
                                        </td>
                                        <td>
                                            <span class="badge <?= match($u['role']) {
                                                'admin'      => 'bg-dark text-white',
                                                'technician' => 'bg-warning text-dark',
                                                'customer'   => 'bg-primary text-white',
                                                default      => 'bg-secondary'
                                            } ?> rounded-pill px-3 py-1">
                                                <i class="<?= match($u['role']) {
                                                    'admin'      => 'bi bi-shield-lock-fill',
                                                    'technician' => 'bi bi-tools',
                                                    'customer'   => 'bi bi-person-fill',
                                                    default      => 'bi bi-person'
                                                } ?> me-1"></i>
                                                <?= strtoupper($u['role']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if (!empty($u['phone'])): ?>
                                                <span class="small text-dark">
                                                    <i class="bi bi-whatsapp text-success me-1"></i><?= htmlspecialchars($u['phone']) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted small">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <small class="text-muted" title="<?= htmlspecialchars($u['address'] ?? '') ?>">
                                                <?= htmlspecialchars(mb_strimwidth($u['address'] ?? '-', 0, 25, '...')) ?>
                                            </small>
                                        </td>
                                        <td>
                                            <?php if ($u['status'] === 'active'): ?>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">
                                                    <i class="bi bi-check-circle me-1"></i> Aktif
                                                </span>
                                            <?php elseif ($u['status'] === 'inactive'): ?>
                                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill">
                                                    <i class="bi bi-dash-circle me-1"></i> Nonaktif
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill">
                                                    <i class="bi bi-slash-circle me-1"></i> Ditangguhkan
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end">
                                            <div class="d-inline-flex gap-1">
                                                <!-- Edit User Button -->
                                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill btn-edit-user"
                                                        data-id="<?= $u['id'] ?>"
                                                        data-name="<?= htmlspecialchars($u['name'], ENT_QUOTES) ?>"
                                                        data-email="<?= htmlspecialchars($u['email'], ENT_QUOTES) ?>"
                                                        data-role="<?= $u['role'] ?>"
                                                        data-phone="<?= htmlspecialchars($u['phone'] ?? '', ENT_QUOTES) ?>"
                                                        data-address="<?= htmlspecialchars($u['address'] ?? '', ENT_QUOTES) ?>"
                                                        data-status="<?= $u['status'] ?>"
                                                        data-bs-toggle="modal" data-bs-target="#editUserModal" title="Edit Akun">
                                                    <i class="bi bi-pencil"></i>
                                                </button>

                                                <!-- Delete User (Hanya jika bukan akun sendiri) -->
                                                <?php if ((int)$u['id'] !== (int)$currentUser['id']): ?>
                                                    <form action="<?= $baseUrl ?>/admin/users/delete" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun \'<?= htmlspecialchars(addslashes($u['name'])) ?>\'? Seluruh riwayat yang terhubung mungkin terpengaruh.');">
                                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
                                                        <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill" title="Hapus Akun">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Modal Daftarkan Akun Baru -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title font-semibold" id="addUserModalLabel">
                    <i class="bi bi-person-plus-fill me-2"></i> Pendaftaran Akun Baru oleh Admin
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= $baseUrl ?>/admin/users/create" method="POST" autocomplete="off">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                
                <div class="modal-body p-4">
                    <div class="alert alert-info border-info rounded-3 small mb-4 d-flex align-items-start gap-2">
                        <i class="bi bi-info-circle-fill fs-5 mt-1"></i>
                        <span>Admin dapat mendaftarkan akun baru secara instan untuk peran <strong>Administrator</strong>, <strong>Mitra Teknisi</strong>, atau <strong>Customer</strong>.</span>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="add_role" class="form-label font-semibold">Peran / Role Akun <span class="text-danger">*</span></label>
                            <select class="form-select" id="add_role" name="role" required>
                                <option value="admin">Administrator (Akses Penuh)</option>
                                <option value="technician">Mitra Teknisi (Pekerjaan & Servis)</option>
                                <option value="customer" selected>Customer (Pelanggan)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="add_status" class="form-label font-semibold">Status Akun</label>
                            <select class="form-select" id="add_status" name="status">
                                <option value="active" selected>Aktif</option>
                                <option value="inactive">Nonaktif</option>
                                <option value="suspended">Ditangguhkan</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="add_name" class="form-label font-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="bi bi-person"></i></span>
                                <input type="text" class="form-control" id="add_name" name="name" placeholder="Contoh: Budi Pratama" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="add_email" class="form-label font-semibold">Alamat Email <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control" id="add_email" name="email" placeholder="nama@smartservice.ai" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="add_password" class="form-label font-semibold">Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="bi bi-lock"></i></span>
                                <input type="password" class="form-control" id="add_password" name="password" placeholder="Minimal 6 karakter" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="add_phone" class="form-label font-semibold">Nomor Telepon / WhatsApp</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="bi bi-telephone"></i></span>
                                <input type="tel" class="form-control" id="add_phone" name="phone" placeholder="Contoh: 081234567890">
                            </div>
                        </div>

                        <div class="col-12">
                            <label for="add_address" class="form-label font-semibold">Alamat / Area Domisili</label>
                            <textarea class="form-control" id="add_address" name="address" rows="2" placeholder="Alamat lengkap domisili atau jangkauan wilayah servis..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light border-0 py-3">
                    <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 font-semibold">
                        <i class="bi bi-check-circle me-1"></i> Daftarkan Akun
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Akun Pengguna -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-dark text-white py-3">
                <h5 class="modal-title font-semibold" id="editUserModalLabel">
                    <i class="bi bi-pencil-square me-2"></i> Perbarui Data Akun Pengguna
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= $baseUrl ?>/admin/users/edit" method="POST" autocomplete="off">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <input type="hidden" name="id" id="edit_id">

                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="edit_role" class="form-label font-semibold">Peran / Role Akun <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_role" name="role" required>
                                <option value="admin">Administrator</option>
                                <option value="technician">Mitra Teknisi</option>
                                <option value="customer">Customer</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_status" class="form-label font-semibold">Status Akun</label>
                            <select class="form-select" id="edit_status" name="status">
                                <option value="active">Aktif</option>
                                <option value="inactive">Nonaktif</option>
                                <option value="suspended">Ditangguhkan</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="edit_name" class="form-label font-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="bi bi-person"></i></span>
                                <input type="text" class="form-control" id="edit_name" name="name" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="edit_email" class="form-label font-semibold">Alamat Email <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control" id="edit_email" name="email" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="edit_password" class="form-label font-semibold">
                                Ganti Password <small class="text-muted fw-normal">(Kosongkan jika tidak diubah)</small>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="bi bi-key"></i></span>
                                <input type="password" class="form-control" id="edit_password" name="password" placeholder="Password baru minimal 6 karakter">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="edit_phone" class="form-label font-semibold">Nomor Telepon / WhatsApp</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="bi bi-telephone"></i></span>
                                <input type="tel" class="form-control" id="edit_phone" name="phone">
                            </div>
                        </div>

                        <div class="col-12">
                            <label for="edit_address" class="form-label font-semibold">Alamat / Area Domisili</label>
                            <textarea class="form-control" id="edit_address" name="address" rows="2"></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light border-0 py-3">
                    <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 font-semibold">
                        <i class="bi bi-check-circle me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.btn-edit-user').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('edit_id').value = this.dataset.id;
            document.getElementById('edit_name').value = this.dataset.name;
            document.getElementById('edit_email').value = this.dataset.email;
            document.getElementById('edit_role').value = this.dataset.role;
            document.getElementById('edit_status').value = this.dataset.status;
            document.getElementById('edit_phone').value = this.dataset.phone;
            document.getElementById('edit_address').value = this.dataset.address;
            document.getElementById('edit_password').value = '';
        });
    });
});
</script>

