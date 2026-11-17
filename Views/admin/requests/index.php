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
                    <h3 class="fw-bold text-dark mb-1"><i class="bi bi-clipboard-check text-success me-2"></i> Kelola Permintaan Jasa & Penugasan Teknisi</h3>
                    <p class="text-muted small mb-0">Pantau seluruh permintaan perbaikan, saring berdasarkan status/tanggal, dan tugaskan teknisi aktif</p>
                </div>
                <div>
                    <span class="badge bg-success font-semibold px-3 py-2 rounded-pill fs-6">
                        <?= count($requests) ?> Permintaan Ditampilkan
                    </span>
                </div>
            </div>

            <!-- Multi-Filter Form Card -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-dark mb-3"><i class="bi bi-funnel me-1 text-primary"></i> Filter Permintaan Jasa</h6>
                    <form action="<?= $baseUrl ?>/admin/requests" method="GET" class="row g-3">

                        <!-- Filter Status -->
                        <div class="col-md-3">
                            <label for="status" class="form-label small font-semibold">Status Pesanan</label>
                            <select class="form-select form-select-sm" id="status" name="status">
                                <option value="">-- Semua Status --</option>
                                <option value="pending" <?= $filterStatus === 'pending' ? 'selected' : '' ?>>Pending (Belum Di-assign)</option>
                                <option value="assigned" <?= $filterStatus === 'assigned' ? 'selected' : '' ?>>Assigned (Ditugaskan)</option>
                                <option value="accepted" <?= $filterStatus === 'accepted' ? 'selected' : '' ?>>Accepted (Diterima Teknisi)</option>
                                <option value="on_the_way" <?= $filterStatus === 'on_the_way' ? 'selected' : '' ?>>On The Way (Menuju Lokasi)</option>
                                <option value="repairing" <?= $filterStatus === 'repairing' ? 'selected' : '' ?>>Repairing (Dalam Pengerjaan)</option>
                                <option value="completed" <?= $filterStatus === 'completed' ? 'selected' : '' ?>>Completed (Selesai)</option>
                                <option value="cancelled" <?= $filterStatus === 'cancelled' ? 'selected' : '' ?>>Cancelled (Dibatalkan)</option>
                            </select>
                        </div>

                        <!-- Filter Category -->
                        <div class="col-md-3">
                            <label for="category_id" class="form-label small font-semibold">Kategori Peralatan</label>
                            <select class="form-select form-select-sm" id="category_id" name="category_id">
                                <option value="">-- Semua Kategori --</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= (string)$filterCategory === (string)$cat['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Filter Technician -->
                        <div class="col-md-3">
                            <label for="technician_id" class="form-label small font-semibold">Teknisi Bertugas</label>
                            <select class="form-select form-select-sm" id="technician_id" name="technician_id">
                                <option value="">-- Semua Teknisi --</option>
                                <option value="unassigned" <?= $filterTechnician === 'unassigned' ? 'selected' : '' ?>>Belum Ditugaskan</option>
                                <?php foreach ($technicians as $tech): ?>
                                    <option value="<?= $tech['id'] ?>" <?= (string)$filterTechnician === (string)$tech['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($tech['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Filter Date -->
                        <div class="col-md-2">
                            <label for="date" class="form-label small font-semibold">Tanggal Jadwal</label>
                            <input type="date" class="form-control form-control-sm" id="date" name="date" value="<?= htmlspecialchars($filterDate) ?>">
                        </div>

                        <!-- Filter Buttons -->
                        <div class="col-md-1 d-flex align-items-end gap-1">
                            <button type="submit" class="btn btn-sm btn-primary rounded-3 w-100" title="Terapkan Filter">
                                <i class="bi bi-search"></i>
                            </button>
                            <a href="<?= $baseUrl ?>/admin/requests" class="btn btn-sm btn-outline-secondary rounded-3" title="Reset Filter">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Requests Table -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark">Daftar Permintaan Jasa Perbaikan</h5>
                    <span class="badge bg-secondary-subtle text-secondary font-semibold rounded-pill px-3 py-2">
                        Total Ditampilkan: <?= count($requests) ?>
                    </span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Customer</th>
                                <th>Layanan</th>
                                <th>Kategori</th>
                                <th>Jadwal Kunjungan</th>
                                <th>Status</th>
                                <th>Teknisi Bertugas</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($requests)): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">Tidak ada permintaan jasa yang memenuhi kriteria filter.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($requests as $req): ?>
                                    <tr>
                                        <td class="fw-bold text-primary">#REQ-<?= $req['id'] ?></td>
                                        <td>
                                            <div class="fw-bold text-dark"><?= htmlspecialchars($req['customer']['name'] ?? 'Customer') ?></div>
                                            <div class="small text-muted"><i class="bi bi-telephone me-1"></i><?= htmlspecialchars($req['customer']['phone'] ?? '-') ?></div>
                                        </td>
                                        <td class="fw-semibold"><?= htmlspecialchars($req['service']['name'] ?? 'Perbaikan') ?></td>
                                        <td>
                                            <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill">
                                                <i class="bi bi-tag me-1"></i><?= htmlspecialchars($req['category']['name'] ?? 'Peralatan') ?>
                                            </span>
                                        </td>
                                        <td><small><i class="bi bi-calendar-event me-1 text-muted"></i><?= htmlspecialchars($req['schedule']) ?></small></td>
                                        <td>
                                            <span class="badge <?= match($req['status']) { 'completed'=>'bg-success', 'repairing'=>'bg-warning text-dark', 'pending'=>'bg-danger', 'assigned'=>'bg-primary', default=>'bg-info' } ?> rounded-pill">
                                                <?= htmlspecialchars(strtoupper($req['status'])) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if (!empty($req['technician'])): ?>
                                                <span class="badge bg-warning-subtle text-dark border border-warning-subtle rounded-pill font-semibold">
                                                    <i class="bi bi-person-wrench me-1"></i> <?= htmlspecialchars($req['technician']['name']) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary-subtle text-secondary rounded-pill">Belum Ditugaskan</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end">
                                            <div class="d-inline-flex gap-1">

                                                <!-- Assign Technician Trigger Button -->
                                                <?php if (in_array($req['status'], ['pending', 'assigned'])): ?>
                                                    <button type="button" class="btn btn-sm btn-outline-warning text-dark rounded-pill font-semibold btn-assign-tech"
                                                            data-id="<?= $req['id'] ?>"
                                                            data-customer="<?= htmlspecialchars($req['customer']['name'] ?? '', ENT_QUOTES) ?>"
                                                            data-service="<?= htmlspecialchars($req['service']['name'] ?? '', ENT_QUOTES) ?>"
                                                            data-current_tech="<?= $req['technician_id'] ?? '' ?>"
                                                            data-bs-toggle="modal" data-bs-target="#assignTechnicianModal">
                                                        <i class="bi bi-person-plus me-1"></i> <?= empty($req['technician_id']) ? 'Assign Teknisi' : 'Ganti Teknisi' ?>
                                                    </button>
                                                <?php endif; ?>

                                                <!-- Detail Button -->
                                                <a href="<?= $baseUrl ?>/admin/requests/detail?id=<?= $req['id'] ?>" class="btn btn-sm btn-outline-primary rounded-pill font-semibold">
                                                    <i class="bi bi-eye me-1"></i> Detail
                                                </a>

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

<!-- Modal Assign Technician -->
<div class="modal fade" id="assignTechnicianModal" tabindex="-1" aria-labelledby="assignTechnicianModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-warning text-dark py-3">
                <h5 class="modal-title font-semibold" id="assignTechnicianModalLabel"><i class="bi bi-person-plus-fill me-1"></i> Penugasan Teknisi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= $baseUrl ?>/admin/requests/assign" method="POST">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <input type="hidden" name="request_id" id="assign_request_id">

                <div class="modal-body p-4">
                    <div class="alert alert-info-subtle border-info-subtle text-info-emphasis rounded-3 small mb-3">
                        <div><strong>Pesanan:</strong> <span id="assign_service_text"></span></div>
                        <div><strong>Pelanggan:</strong> <span id="assign_customer_text"></span></div>
                    </div>

                    <div class="mb-3">
                        <label for="technician_id_select" class="form-label font-semibold">Pilih Teknisi Aktif <span class="text-danger">*</span></label>
                        <select class="form-select form-select-lg rounded-3" id="technician_id_select" name="technician_id" required>
                            <option value="" disabled selected>-- Pilih Teknisi --</option>
                            <?php foreach ($technicians as $tech): ?>
                                <option value="<?= $tech['id'] ?>"><?= htmlspecialchars($tech['name']) ?> (<?= htmlspecialchars($tech['phone'] ?? '-') ?>)</option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text small">Status pesanan akan secara otomatis diperbarui dari <strong>[PENDING]</strong> menjadi <strong>[ASSIGNED]</strong>.</div>
                    </div>
                </div>

                <div class="modal-footer bg-light border-0 py-3">
                    <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning text-dark font-semibold rounded-pill px-4">Tugaskan Teknisi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.btn-assign-tech').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('assign_request_id').value = this.dataset.id;
            document.getElementById('assign_customer_text').textContent = this.dataset.customer;
            document.getElementById('assign_service_text').textContent = '#' + this.dataset.id + ' - ' + this.dataset.service;
            if (this.dataset.current_tech) {
                document.getElementById('technician_id_select').value = this.dataset.current_tech;
            } else {
                document.getElementById('technician_id_select').selectedIndex = 0;
            }
        });
    });
});
</script>
