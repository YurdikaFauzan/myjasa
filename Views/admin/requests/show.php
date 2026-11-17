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

            <!-- Page Header Card -->
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 p-4 bg-dark text-white rounded-4 shadow-sm">
                <div>
                    <div class="badge bg-primary px-3 py-1 rounded-pill mb-2">
                        <i class="bi bi-ticket-perforated me-1"></i> Admin View: Request #REQ-<?= $request['id'] ?>
                    </div>
                    <h3 class="fw-bold mb-1"><?= htmlspecialchars($service['name'] ?? 'Perbaikan Peralatan') ?></h3>
                    <p class="text-white-50 mb-0 small">Kategori: <?= htmlspecialchars($category['name'] ?? 'Peralatan Rumah Tangga') ?></p>
                </div>
                <div class="d-flex gap-2">
                    <a href="<?= $baseUrl ?>/admin/requests" class="btn btn-outline-light rounded-pill px-3">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Request
                    </a>
                </div>
            </div>

            <!-- Detail Grid Layout -->
            <div class="row g-4">

                <!-- Left Column: Data Customer, Keluhan, & Foto Kerusakan -->
                <div class="col-lg-8">

                    <!-- Data Customer Card -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-person-circle text-primary me-2"></i> Informasi Pelanggan (Customer)</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="text-muted small fw-semibold text-uppercase d-block">Nama Lengkap</label>
                                    <strong class="fs-6 text-dark"><?= htmlspecialchars($customer['name'] ?? 'Customer') ?></strong>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small fw-semibold text-uppercase d-block">Alamat Email</label>
                                    <span class="text-dark"><?= htmlspecialchars($customer['email'] ?? '-') ?></span>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small fw-semibold text-uppercase d-block">Nomor Telepon / WA</label>
                                    <span class="text-dark"><i class="bi bi-telephone me-1 text-success"></i><?= htmlspecialchars($customer['phone'] ?? '-') ?></span>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small fw-semibold text-uppercase d-block">Status Akun</label>
                                    <span class="badge bg-success-subtle text-success rounded-pill"><?= htmlspecialchars($customer['status'] ?? 'active') ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detail Keluhan & Alamat Card -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-file-text text-danger me-2"></i> Detail Keluhan Kerusakan & Alamat</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-4">
                                <label class="text-muted small fw-semibold text-uppercase d-block mb-1">Rincian Keluhan</label>
                                <div class="bg-light p-3 rounded-3 text-secondary" style="white-space: pre-line;">
                                    <?= htmlspecialchars($request['complaint']) ?>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="text-muted small fw-semibold text-uppercase d-block mb-1">Alamat Kunjungan Lokasi</label>
                                <div class="fw-semibold text-dark"><i class="bi bi-geo-alt-fill me-1 text-danger"></i><?= htmlspecialchars($request['address']) ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Diagnosis Awal AI Card -->
                    <?php if (!empty($aiConsultation) && !empty($aiConsultation['ai_result'])): ?>
                    <?php $ai = $aiConsultation['ai_result']; ?>
                    <div class="card border-0 shadow-sm rounded-4 mb-4 border-start border-4 border-info">
                        <div class="card-header bg-white py-3">
                            <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-robot text-info me-2"></i> Diagnosis Awal AI</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="alert alert-warning py-2 mb-3 small d-flex align-items-center">
                                <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                                <div>
                                    <strong>Diagnosis Awal AI</strong> — hasil ini bukan pengganti pemeriksaan teknisi.
                                </div>
                            </div>
                            
                            <h6 class="fw-bold text-dark mb-2">Kemungkinan Masalah</h6>
                            <p class="small text-secondary mb-3"><?= htmlspecialchars($ai['possible_issue'] ?? '') ?></p>
                            
                            <h6 class="fw-bold text-dark mb-2">Penyebab</h6>
                            <ul class="small text-secondary mb-3">
                                <?php foreach ((array)($ai['causes'] ?? []) as $cause): ?>
                                    <li><?= htmlspecialchars($cause) ?></li>
                                <?php endforeach; ?>
                            </ul>
                            
                            <h6 class="fw-bold text-dark mb-2">Pemeriksaan Sederhana</h6>
                            <ul class="small text-secondary mb-3">
                                <?php foreach ((array)($ai['simple_checks'] ?? []) as $check): ?>
                                    <li><?= htmlspecialchars($check) ?></li>
                                <?php endforeach; ?>
                            </ul>
                            
                            <h6 class="fw-bold text-dark mb-2">Rekomendasi Layanan</h6>
                            <p class="small text-secondary mb-0">
                                <span class="badge bg-primary text-white rounded-pill px-3 py-2"><?= htmlspecialchars($ai['recommendation'] ?? '') ?></span>
                            </p>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Catatan & Hasil Perbaikan Teknisi -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-journal-check text-success me-2"></i> Laporan Pengerjaan & Hasil Teknisi</h5>
                        </div>
                        <div class="card-body p-4">
                            <?php if (!empty($request['inspection_result']) || !empty($request['repair_action'])): ?>
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="text-muted small fw-semibold text-uppercase d-block mb-1">Hasil Pemeriksaan</label>
                                        <div class="bg-light p-3 rounded-3 text-dark small" style="white-space: pre-line;">
                                            <?= htmlspecialchars($request['inspection_result'] ?? '-') ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-muted small fw-semibold text-uppercase d-block mb-1">Tindakan Perbaikan</label>
                                        <div class="bg-light p-3 rounded-3 text-dark small" style="white-space: pre-line;">
                                            <?= htmlspecialchars($request['repair_action'] ?? '-') ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-md-4">
                                        <div class="p-2 bg-light rounded-3 text-center">
                                            <span class="text-muted small d-block">Biaya Jasa</span>
                                            <strong class="text-dark">Rp <?= number_format($request['service_fee'] ?? 0, 0, ',', '.') ?></strong>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-2 bg-light rounded-3 text-center">
                                            <span class="text-muted small d-block">Biaya Sparepart</span>
                                            <strong class="text-dark">Rp <?= number_format($request['sparepart_fee'] ?? 0, 0, ',', '.') ?></strong>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-2 bg-success-subtle text-success rounded-3 text-center">
                                            <span class="text-muted small d-block">Total Final</span>
                                            <strong class="fs-6">Rp <?= number_format($request['final_price'] ?? 0, 0, ',', '.') ?></strong>
                                        </div>
                                    </div>
                                </div>

                                <?php if (!empty($request['technician_note'])): ?>
                                    <div class="mb-3">
                                        <label class="text-muted small fw-semibold text-uppercase d-block mb-1">Catatan Tambahan</label>
                                        <div class="bg-light p-3 rounded-3 text-dark small" style="white-space: pre-line;"><?= htmlspecialchars($request['technician_note']) ?></div>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($request['completion_photo'])): ?>
                                    <div>
                                        <label class="text-muted small fw-semibold text-uppercase d-block mb-2">Foto Hasil Pekerjaan</label>
                                        <img src="<?= $baseUrl ?>/<?= htmlspecialchars($request['completion_photo']) ?>" alt="Foto Pengerjaan" class="img-fluid rounded-3 shadow-sm" style="max-height: 200px; object-fit: cover;">
                                    </div>
                                <?php endif; ?>

                            <?php elseif (!empty($request['technician_note'])): ?>
                                <div class="bg-light p-3 rounded-3 text-dark" style="white-space: pre-line;"><?= htmlspecialchars($request['technician_note']) ?></div>
                            <?php else: ?>
                                <p class="mb-0 text-muted small italic"><i class="bi bi-hourglass me-1"></i> Belum ada laporan pengerjaan dari teknisi.</p>
                            <?php endif; ?>

                            <?php if (!empty($request['rejection_note'])): ?>
                                <div class="alert alert-danger rounded-3 mt-3 mb-0 small">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i> <strong>Catatan Penolakan Sebelumnya:</strong> <?= htmlspecialchars($request['rejection_note']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>

                <!-- Right Column: Assign Technician Widget, Status, & Price -->
                <div class="col-lg-4">

                    <!-- Status Timeline Tracking Card -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-map me-2 text-primary"></i> Timeline Tracking Status</h5>
                        </div>
                        <div class="card-body p-4">
                            <?php
                                $currentStatus = $request['status'];
                                require __DIR__ . '/../../layouts/partials/status_timeline.php';
                            ?>
                        </div>
                    </div>

                    <!-- Technician Assignment Widget Card -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4 border-start border-4 border-warning">
                        <div class="card-header bg-white py-3">
                            <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-person-plus text-warning me-2"></i> Penugasan Teknisi</h5>
                        </div>
                        <div class="card-body p-4">
                            <?php if (!empty($technician)): ?>
                                <div class="alert alert-warning-subtle border-warning-subtle text-dark rounded-3 p-3 mb-3">
                                    <div class="small text-muted mb-1">Teknisi Ditugaskan:</div>
                                    <strong class="fs-6 d-block mb-1"><i class="bi bi-person-wrench me-1"></i><?= htmlspecialchars($technician['name']) ?></strong>
                                    <div class="small"><i class="bi bi-telephone me-1"></i><?= htmlspecialchars($technician['phone'] ?? '-') ?></div>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-secondary text-secondary rounded-3 small mb-3">
                                    <i class="bi bi-info-circle me-1"></i> Pesanan belum memiliki teknisi bertugas (Status: PENDING).
                                </div>
                            <?php endif; ?>

                            <!-- Assign / Reassign Form (Only enabled if pending or assigned) -->
                            <?php if (in_array($request['status'], ['pending', 'assigned'])): ?>
                                <form action="<?= $baseUrl ?>/admin/requests/assign" method="POST">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                    <input type="hidden" name="request_id" value="<?= $request['id'] ?>">

                                    <div class="mb-3">
                                        <label for="technician_id_detail" class="form-label small font-semibold">
                                            <?= empty($request['technician_id']) ? 'Pilih Teknisi Baru' : 'Ganti Teknisi Bertugas' ?>
                                        </label>
                                        <select class="form-select" id="technician_id_detail" name="technician_id" required>
                                            <option value="" disabled selected>-- Pilih Teknisi --</option>
                                            <?php foreach ($technicians as $tech): ?>
                                                <option value="<?= $tech['id'] ?>" <?= (int)$request['technician_id'] === (int)$tech['id'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($tech['name']) ?> (<?= htmlspecialchars($tech['phone'] ?? '-') ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <button type="submit" class="btn btn-warning text-dark font-semibold rounded-pill w-100 shadow-sm">
                                        <i class="bi bi-check-circle me-1"></i> <?= empty($request['technician_id']) ? 'Tugaskan Teknisi' : 'Simpan Perubahan Teknisi' ?>
                                    </button>
                                </form>
                            <?php else: ?>
                                <div class="alert alert-info rounded-3 small mb-0">
                                    <i class="bi bi-lock me-1"></i> Penugasan teknisi terkunci karena pekerjaan sudah dimulai / selesai.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Status & Jadwal Card -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-clock-history text-info me-2"></i> Status & Jadwal</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <label class="text-muted small fw-semibold text-uppercase d-block mb-1">Status Pesanan</label>
                                <span class="badge <?= match($request['status']) { 'completed'=>'bg-success', 'repairing'=>'bg-warning text-dark', 'pending'=>'bg-danger', 'assigned'=>'bg-primary', default=>'bg-info' } ?> px-3 py-2 fs-6 rounded-pill">
                                    <?= htmlspecialchars(strtoupper($request['status'])) ?>
                                </span>
                            </div>

                            <hr class="my-3">

                            <div class="mb-3">
                                <label class="text-muted small fw-semibold text-uppercase d-block mb-1">Jadwal Kunjungan</label>
                                <div class="fw-bold text-dark fs-6"><i class="bi bi-calendar-event me-2 text-primary"></i><?= htmlspecialchars($request['schedule']) ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Rincian Biaya Card -->
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-cash-stack text-success me-2"></i> Estimasi Biaya</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Estimasi Biaya:</span>
                                <strong class="text-dark">Rp <?= number_format($request['estimated_price'], 0, ',', '.') ?></strong>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted">Biaya Final:</span>
                                <?php if ($request['final_price'] !== null): ?>
                                    <strong class="text-success fs-5">Rp <?= number_format($request['final_price'], 0, ',', '.') ?></strong>
                                <?php else: ?>
                                    <span class="text-muted small">(Menunggu pengerjaan)</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>
</div>
