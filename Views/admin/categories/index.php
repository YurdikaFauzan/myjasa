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
                    <h3 class="fw-bold text-dark mb-1"><i class="bi bi-tags-fill text-info me-2"></i> Manajemen Kategori Peralatan</h3>
                    <p class="text-muted small mb-0">Kelola kategori alat rumah tangga (AC, Kulkas, Mesin Cuci, Kompor, dll.)</p>
                </div>
                <div>
                    <button type="button" class="btn btn-primary rounded-pill font-semibold px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                        <i class="bi bi-plus-lg me-1"></i> Tambah Kategori Baru
                    </button>
                </div>
            </div>

            <!-- Categories Data Table -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark">Daftar Kategori Terdaftar</h5>
                    <span class="badge bg-info-subtle text-info font-semibold rounded-pill px-3 py-2"><?= count($categories) ?> Kategori</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 70px;">ID</th>
                                <th style="width: 200px;">Nama Kategori</th>
                                <th>Deskripsi</th>
                                <th style="width: 130px;">Jumlah Layanan</th>
                                <th style="width: 120px;">Status</th>
                                <th style="width: 220px;" class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($categories)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Belum ada data kategori.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($categories as $cat): ?>
                                    <tr>
                                        <td class="fw-bold text-muted">#<?= $cat['id'] ?></td>
                                        <td class="fw-bold text-dark"><?= htmlspecialchars($cat['name']) ?></td>
                                        <td><small class="text-muted"><?= htmlspecialchars($cat['description'] ?? '-') ?></small></td>
                                        <td>
                                            <span class="badge bg-secondary-subtle text-secondary rounded-pill">
                                                <i class="bi bi-tools me-1"></i><?= $cat['services_count'] ?> Layanan
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($cat['status'] === 'active'): ?>
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
                                                <form action="<?= $baseUrl ?>/admin/categories/toggle-status" method="POST" class="d-inline">
                                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
                                                    <input type="hidden" name="id" value="<?= $cat['id'] ?>">
                                                    <button type="submit" class="btn btn-sm <?= $cat['status'] === 'active' ? 'btn-outline-warning' : 'btn-outline-success' ?> rounded-pill" title="Ubah Status">
                                                        <i class="bi bi-power"></i> <?= $cat['status'] === 'active' ? 'Nonaktifkan' : 'Aktifkan' ?>
                                                    </button>
                                                </form>

                                                <!-- Edit Button -->
                                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill btn-edit-category" 
                                                        data-id="<?= $cat['id'] ?>"
                                                        data-name="<?= htmlspecialchars($cat['name'], ENT_QUOTES) ?>"
                                                        data-description="<?= htmlspecialchars($cat['description'] ?? '', ENT_QUOTES) ?>"
                                                        data-status="<?= $cat['status'] ?>"
                                                        data-bs-toggle="modal" data-bs-target="#editCategoryModal">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </button>

                                                <!-- Delete Button -->
                                                <form action="<?= $baseUrl ?>/admin/categories/delete" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini?');">
                                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
                                                    <input type="hidden" name="id" value="<?= $cat['id'] ?>">
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

<!-- Modal Tambah Kategori -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title font-semibold" id="addCategoryModalLabel"><i class="bi bi-plus-circle me-1"></i> Tambah Kategori Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= $baseUrl ?>/admin/categories/create" method="POST">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="add_name" class="form-label font-semibold">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="add_name" name="name" placeholder="Contoh: AC, Kulkas, TV..." required>
                    </div>
                    <div class="mb-3">
                        <label for="add_description" class="form-label font-semibold">Deskripsi</label>
                        <textarea class="form-control" id="add_description" name="description" rows="3" placeholder="Penjelasan singkat mengenai kategori..."></textarea>
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
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Kategori</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Kategori -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-dark text-white py-3">
                <h5 class="modal-title font-semibold" id="editCategoryModalLabel"><i class="bi bi-pencil-square me-1"></i> Edit Kategori</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= $baseUrl ?>/admin/categories/edit" method="POST">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label font-semibold">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_description" class="form-label font-semibold">Deskripsi</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
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
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Update Kategori</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.btn-edit-category').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('edit_id').value = this.dataset.id;
            document.getElementById('edit_name').value = this.dataset.name;
            document.getElementById('edit_description').value = this.dataset.description;
            document.getElementById('edit_status').value = this.dataset.status;
        });
    });
});
</script>
