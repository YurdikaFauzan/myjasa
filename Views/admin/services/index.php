<?php
$baseUrl = \Config\Env::get('APP_URL', 'http://localhost/Myjasagwuehhhhhhhhhhhhhhhhhhhhh');
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
                    <h3 class="fw-bold text-dark mb-1"><i class="bi bi-tools text-warning me-2"></i> Manajemen Katalog Layanan Perbaikan</h3>
                    <p class="text-muted small mb-0">Kelola tarif, deskripsi, dan estimasi durasi jasa perbaikan peralatan rumah tangga</p>
                </div>
                <div>
                    <button type="button" class="btn btn-warning text-dark rounded-pill font-semibold px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#addServiceModal">
                        <i class="bi bi-plus-lg me-1"></i> Tambah Layanan Baru
                    </button>
                </div>
            </div>

            <!-- Services Data Table -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark">Katalog Layanan Terdaftar</h5>
                    <span class="badge bg-warning-subtle text-dark font-semibold rounded-pill px-3 py-2"><?= count($services) ?> Jenis Layanan</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 60px;">ID</th>
                                <th style="width: 140px;">Kategori</th>
                                <th style="width: 220px;">Nama Layanan</th>
                                <th>Deskripsi</th>
                                <th style="width: 140px;">Harga (Rp)</th>
                                <th style="width: 120px;">Durasi</th>
                                <th style="width: 110px;">Status</th>
                                <th style="width: 220px;" class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($services)): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">Belum ada data layanan perbaikan.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($services as $s): ?>
                                    <tr>
                                        <td class="fw-bold text-muted">#<?= $s['id'] ?></td>
                                        <td>
                                            <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill">
                                                <i class="bi bi-tag me-1"></i><?= htmlspecialchars($s['category']['name'] ?? 'Unassigned') ?>
                                            </span>
                                        </td>
                                        <td class="fw-bold text-dark"><?= htmlspecialchars($s['name']) ?></td>
                                        <td><small class="text-muted"><?= htmlspecialchars($s['description'] ?? '-') ?></small></td>
                                        <td class="fw-bold text-primary">Rp <?= number_format($s['price'], 0, ',', '.') ?></td>
                                        <td><small class="text-muted"><i class="bi bi-clock me-1"></i><?= htmlspecialchars($s['duration'] ?? '-') ?></small></td>
                                        <td>
                                            <?php if ($s['status'] === 'active'): ?>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">
                                                    <i class="bi bi-check-circle me-1"></i> Aktif
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill">
                                                    <i class="bi bi-x-circle me-1"></i> Nonaktif
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end">
                                            <div class="d-inline-flex gap-1">

                                                <!-- Toggle Status Button -->
                                                <form action="<?= $baseUrl ?>/admin/services/toggle-status" method="POST" class="d-inline">
                                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
                                                    <input type="hidden" name="id" value="<?= $s['id'] ?>">
                                                    <button type="submit" class="btn btn-sm <?= $s['status'] === 'active' ? 'btn-outline-warning' : 'btn-outline-success' ?> rounded-pill" title="Ubah Status">
                                                        <i class="bi bi-power"></i> <?= $s['status'] === 'active' ? 'Nonaktifkan' : 'Aktifkan' ?>
                                                    </button>
                                                </form>

                                                <!-- Edit Button -->
                                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill btn-edit-service"
                                                        data-id="<?= $s['id'] ?>"
                                                        data-category_id="<?= $s['category_id'] ?>"
                                                        data-name="<?= htmlspecialchars($s['name'], ENT_QUOTES) ?>"
                                                        data-description="<?= htmlspecialchars($s['description'] ?? '', ENT_QUOTES) ?>"
                                                        data-price="<?= $s['price'] ?>"
                                                        data-duration="<?= htmlspecialchars($s['duration'] ?? '', ENT_QUOTES) ?>"
                                                        data-status="<?= $s['status'] ?>"
                                                        data-bs-toggle="modal" data-bs-target="#editServiceModal">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </button>

                                                <!-- Delete Button -->
                                                <form action="<?= $baseUrl ?>/admin/services/delete" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus layanan ini?');">
                                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
                                                    <input type="hidden" name="id" value="<?= $s['id'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>

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

<!-- Modal Tambah Layanan -->
<div class="modal fade" id="addServiceModal" tabindex="-1" aria-labelledby="addServiceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-warning text-dark py-3">
                <h5 class="modal-title font-semibold" id="addServiceModalLabel"><i class="bi bi-plus-circle me-1"></i> Tambah Layanan Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= $baseUrl ?>/admin/services/create" method="POST">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="add_category_id" class="form-label font-semibold">Kategori Peralatan <span class="text-danger">*</span></label>
                        <select class="form-select" id="add_category_id" name="category_id" required>
                            <option value="" disabled selected>-- Pilih Kategori --</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="add_name" class="form-label font-semibold">Nama Layanan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="add_name" name="name" placeholder="Contoh: Cuci AC Standard, Servis Kulkas Tidak Dingin..." required>
                    </div>
                    <div class="mb-3">
                        <label for="add_description" class="form-label font-semibold">Deskripsi Layanan</label>
                        <textarea class="form-control" id="add_description" name="description" rows="3" placeholder="Rincian cakupan perbaikan..."></textarea>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="add_price" class="form-label font-semibold">Harga (Rp) <span class="text-danger">*</span></label>
                            <input type="number" step="1000" class="form-control" id="add_price" name="price" placeholder="75000" required>
                        </div>
                        <div class="col-md-6">
                            <label for="add_duration" class="form-label font-semibold">Estimasi Durasi</label>
                            <input type="text" class="form-control" id="add_duration" name="duration" placeholder="Contoh: 45 menit">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="add_status" class="form-label font-semibold">Status</label>
                        <select class="form-select" id="add_status" name="status">
                            <option value="active" selected>Aktif</option>
                            <option value="inactive">Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-3">
                    <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning text-dark font-semibold rounded-pill px-4">Simpan Layanan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Layanan -->
<div class="modal fade" id="editServiceModal" tabindex="-1" aria-labelledby="editServiceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-dark text-white py-3">
                <h5 class="modal-title font-semibold" id="editServiceModalLabel"><i class="bi bi-pencil-square me-1"></i> Edit Layanan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= $baseUrl ?>/admin/services/edit" method="POST">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="edit_category_id" class="form-label font-semibold">Kategori Peralatan <span class="text-danger">*</span></label>
                        <select class="form-select" id="edit_category_id" name="category_id" required>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_name" class="form-label font-semibold">Nama Layanan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_description" class="form-label font-semibold">Deskripsi Layanan</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="edit_price" class="form-label font-semibold">Harga (Rp) <span class="text-danger">*</span></label>
                            <input type="number" step="1000" class="form-control" id="edit_price" name="price" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_duration" class="form-label font-semibold">Estimasi Durasi</label>
                            <input type="text" class="form-control" id="edit_duration" name="duration">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_status" class="form-label font-semibold">Status</label>
                        <select class="form-select" id="edit_status" name="status">
                            <option value="active">Aktif</option>
                            <option value="inactive">Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-3">
                    <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Update Layanan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.btn-edit-service').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('edit_id').value = this.dataset.id;
            document.getElementById('edit_category_id').value = this.dataset.category_id;
            document.getElementById('edit_name').value = this.dataset.name;
            document.getElementById('edit_description').value = this.dataset.description;
            document.getElementById('edit_price').value = this.dataset.price;
            document.getElementById('edit_duration').value = this.dataset.duration;
            document.getElementById('edit_status').value = this.dataset.status;
        });
    });
});
</script>
