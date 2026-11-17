<?php
$baseUrl = \Config\Env::get('APP_URL', 'http://localhost/Myjasagwuehhhhhhhhhhhhhhhhhhhhh');
?>

<div class="container-fluid py-4">
    <div class="row g-4">

        <!-- Sidebar Navigation Area -->
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

            <!-- Page Header Banner -->
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 p-4 bg-dark text-white rounded-4 shadow-sm">
                <div>
                    <div class="badge bg-warning text-dark px-3 py-1 rounded-pill mb-2">
                        <i class="bi bi-wrench-adjustable me-1"></i> Detail Tugas Teknisi #JOB-<?= $job['id'] ?>
                    </div>
                    <h3 class="fw-bold mb-1"><?= htmlspecialchars($service['name'] ?? 'Layanan Perbaikan') ?></h3>
                    <p class="text-white-50 mb-0 small">Kategori: <?= htmlspecialchars($category['name'] ?? 'Elektronik / Alat Rumah Tangga') ?></p>
                </div>
                <div class="d-flex gap-2">
                    <a href="<?= $baseUrl ?>/technician/jobs" class="btn btn-outline-light rounded-pill px-3">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Job
                    </a>
                </div>
            </div>

            <!-- Status Workflow Progress Bar -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-dark mb-3"><i class="bi bi-diagram-3-fill me-2 text-primary"></i> Progress Status Pengerjaan</h6>
                    
                    <?php
                        $statusMap = [
                            'assigned'   => 1,
                            'accepted'   => 2,
                            'on_the_way' => 3,
                            'repairing'  => 4,
                            'completed'  => 5,
                            'pending'    => 0,
                            'cancelled'  => 0
                        ];
                        $currentStep = $statusMap[$job['status']] ?? 0;
                    ?>

                    <div class="position-relative m-3">
                        <div class="progress" style="height: 4px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: <?= ($currentStep > 0) ? (($currentStep - 1) / 4) * 100 : 0 ?>%;"></div>
                        </div>

                        <div class="d-flex justify-content-between position-relative mt-n3" style="margin-top: -14px;">
                            <!-- Step 1: Assigned -->
                            <div class="text-center">
                                <div class="rounded-circle d-inline-flex align-items-center justify-content-center <?= $currentStep >= 1 ? 'bg-primary text-white' : 'bg-light text-muted border' ?>" style="width: 28px; height: 28px; font-size: 12px; font-weight: bold;">1</div>
                                <div class="small mt-1 <?= $currentStep >= 1 ? 'fw-bold text-primary' : 'text-muted' ?>">Ditugaskan</div>
                            </div>
                            <!-- Step 2: Accepted -->
                            <div class="text-center">
                                <div class="rounded-circle d-inline-flex align-items-center justify-content-center <?= $currentStep >= 2 ? 'bg-primary text-white' : 'bg-light text-muted border' ?>" style="width: 28px; height: 28px; font-size: 12px; font-weight: bold;">2</div>
                                <div class="small mt-1 <?= $currentStep >= 2 ? 'fw-bold text-primary' : 'text-muted' ?>">Diterima</div>
                            </div>
                            <!-- Step 3: On The Way -->
                            <div class="text-center">
                                <div class="rounded-circle d-inline-flex align-items-center justify-content-center <?= $currentStep >= 3 ? 'bg-primary text-white' : 'bg-light text-muted border' ?>" style="width: 28px; height: 28px; font-size: 12px; font-weight: bold;">3</div>
                                <div class="small mt-1 <?= $currentStep >= 3 ? 'fw-bold text-primary' : 'text-muted' ?>">Menuju Lokasi</div>
                            </div>
                            <!-- Step 4: Repairing -->
                            <div class="text-center">
                                <div class="rounded-circle d-inline-flex align-items-center justify-content-center <?= $currentStep >= 4 ? 'bg-primary text-white' : 'bg-light text-muted border' ?>" style="width: 28px; height: 28px; font-size: 12px; font-weight: bold;">4</div>
                                <div class="small mt-1 <?= $currentStep >= 4 ? 'fw-bold text-primary' : 'text-muted' ?>">Perbaikan</div>
                            </div>
                            <!-- Step 5: Completed -->
                            <div class="text-center">
                                <div class="rounded-circle d-inline-flex align-items-center justify-content-center <?= $currentStep >= 5 ? 'bg-success text-white' : 'bg-light text-muted border' ?>" style="width: 28px; height: 28px; font-size: 12px; font-weight: bold;">5</div>
                                <div class="small mt-1 <?= $currentStep >= 5 ? 'fw-bold text-success' : 'text-muted' ?>">Selesai</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detail Grid Layout -->
            <div class="row g-4">

                <!-- Left Column: Customer Info, Complaint & Address -->
                <div class="col-lg-8">

                    <!-- Customer Info Card -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-person-circle text-primary me-2"></i> Informasi Pelanggan (Customer)</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="text-muted small fw-semibold text-uppercase d-block">Nama Customer</label>
                                    <strong class="fs-6 text-dark"><?= htmlspecialchars($customer['name'] ?? 'Pelanggan') ?></strong>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small fw-semibold text-uppercase d-block">Telepon / WhatsApp</label>
                                    <div class="d-flex align-items-center gap-2 mt-1">
                                        <span class="fw-bold text-dark"><i class="bi bi-telephone-fill text-success me-1"></i><?= htmlspecialchars($customer['phone'] ?? '-') ?></span>
                                        <?php if (!empty($customer['phone'])): ?>
                                            <?php
                                                $cleanPhone = preg_replace('/[^0-9]/', '', $customer['phone']);
                                                if (str_starts_with($cleanPhone, '0')) {
                                                    $cleanPhone = '62' . substr($cleanPhone, 1);
                                                }
                                            ?>
                                            <a href="https://wa.me/<?= $cleanPhone ?>" target="_blank" class="btn btn-sm btn-success rounded-pill px-2 py-0" style="font-size: 12px;">
                                                <i class="bi bi-whatsapp me-1"></i> Chat WA
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small fw-semibold text-uppercase d-block">Email Customer</label>
                                    <span class="text-dark"><i class="bi bi-envelope me-1 text-secondary"></i><?= htmlspecialchars($customer['email'] ?? '-') ?></span>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small fw-semibold text-uppercase d-block">ID Request</label>
                                    <span class="badge bg-secondary-subtle text-secondary rounded-pill font-semibold">#REQ-<?= $job['id'] ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Complaint & Address Card -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-file-earmark-text text-danger me-2"></i> Keluhan & Alamat Kunjungan</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-4">
                                <label class="text-muted small fw-semibold text-uppercase d-block mb-1">Deskripsi Keluhan Kerusakan</label>
                                <div class="bg-light p-3 rounded-3 text-dark fw-normal" style="white-space: pre-line;">
                                    <?= htmlspecialchars($job['complaint']) ?>
                                </div>
                            </div>

                            <?php if (!empty($job['complaint_photo'])): ?>
                            <div class="mb-4">
                                <label class="text-muted small fw-semibold text-uppercase d-block mb-2"><i class="bi bi-camera-fill text-primary me-1"></i> Foto Kerusakan dari Customer</label>
                                <div class="position-relative d-inline-block">
                                    <img src="<?= $baseUrl ?>/<?= htmlspecialchars($job['complaint_photo']) ?>" alt="Foto Kerusakan Customer" class="img-fluid rounded-3 shadow-sm border" style="max-height: 300px; object-fit: cover; cursor: pointer;" onclick="window.open(this.src, '_blank')">
                                    <span class="position-absolute bottom-0 start-0 m-2 badge bg-dark bg-opacity-75 text-white rounded-pill px-2 py-1 small"><i class="bi bi-zoom-in me-1"></i>Klik untuk memperbesar</span>
                                </div>
                            </div>
                            <?php endif; ?>

                            <div>
                                <label class="text-muted small fw-semibold text-uppercase d-block mb-1">Alamat Lokasi Perbaikan</label>
                                <div class="p-3 bg-danger-subtle text-dark rounded-3 font-semibold">
                                    <i class="bi bi-geo-alt-fill text-danger fs-5 me-2"></i>
                                    <?= htmlspecialchars($job['address']) ?>
                                </div>
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

                    <!-- Completion Result Details (Displayed when Status is Completed) -->
                    <?php if ($job['status'] === 'completed'): ?>
                        <div class="card border-0 shadow-sm rounded-4 mb-4 border-start border-4 border-success">
                            <div class="card-header bg-white py-3">
                                <h5 class="fw-bold mb-0 text-success"><i class="bi bi-check-circle-fill me-2"></i> Laporan Hasil Perbaikan Teknisi</h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-4 mb-3">
                                    <div class="col-md-6">
                                        <label class="text-muted small fw-semibold text-uppercase d-block mb-1">Hasil Pemeriksaan</label>
                                        <div class="bg-light p-3 rounded-3 text-dark" style="white-space: pre-line;">
                                            <?= htmlspecialchars($job['inspection_result'] ?? '-') ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-muted small fw-semibold text-uppercase d-block mb-1">Tindakan Perbaikan</label>
                                        <div class="bg-light p-3 rounded-3 text-dark" style="white-space: pre-line;">
                                            <?= htmlspecialchars($job['repair_action'] ?? '-') ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-md-4">
                                        <div class="p-3 bg-light rounded-3">
                                            <span class="text-muted small d-block">Biaya Jasa:</span>
                                            <strong class="text-dark fs-6">Rp <?= number_format($job['service_fee'] ?? 0, 0, ',', '.') ?></strong>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 bg-light rounded-3">
                                            <span class="text-muted small d-block">Biaya Sparepart:</span>
                                            <strong class="text-dark fs-6">Rp <?= number_format($job['sparepart_fee'] ?? 0, 0, ',', '.') ?></strong>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 bg-success-subtle text-success rounded-3">
                                            <span class="text-muted small d-block">Total Biaya Final:</span>
                                            <strong class="fs-5">Rp <?= number_format($job['final_price'] ?? 0, 0, ',', '.') ?></strong>
                                        </div>
                                    </div>
                                </div>

                                <?php if (!empty($job['technician_note'])): ?>
                                    <div class="mb-3">
                                        <label class="text-muted small fw-semibold text-uppercase d-block mb-1">Catatan Tambahan</label>
                                        <div class="bg-light p-3 rounded-3 text-secondary" style="white-space: pre-line;">
                                            <?= htmlspecialchars($job['technician_note']) ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($job['completion_photo'])): ?>
                                    <div>
                                        <label class="text-muted small fw-semibold text-uppercase d-block mb-2">Foto Hasil Pekerjaan</label>
                                        <div class="text-start">
                                            <img src="<?= $baseUrl ?>/<?= htmlspecialchars($job['completion_photo']) ?>" alt="Foto Pengerjaan" class="img-fluid rounded-4 shadow-sm" style="max-height: 300px; object-fit: cover;">
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                </div>

                <!-- Right Column: Status Actions Control Box & Schedule Info -->
                <div class="col-lg-4">

                    <!-- Actions Control Box -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-lightning-charge-fill text-warning me-2"></i> Aksi Pekerjaan</h5>
                        </div>
                        <div class="card-body p-4">
                            
                            <!-- Current Status Badge -->
                            <div class="mb-4 text-center p-3 bg-light rounded-3">
                                <label class="text-muted small fw-semibold text-uppercase d-block mb-1">Status Pekerjaan Saat Ini</label>
                                <span class="badge <?= match($job['status']) { 
                                    'completed'  => 'bg-success', 
                                    'repairing'  => 'bg-warning text-dark', 
                                    'on_the_way' => 'bg-info text-dark', 
                                    'accepted'   => 'bg-primary', 
                                    'assigned'   => 'bg-danger', 
                                    default      => 'bg-secondary' 
                                } ?> px-3 py-2 fs-6 rounded-pill">
                                    <?= htmlspecialchars(strtoupper(str_replace('_', ' ', $job['status']))) ?>
                                </span>
                            </div>

                            <!-- Action 0: If Status is PENDING or UNASSIGNED (Ambil & Terima Tugas) -->
                            <?php if ($job['status'] === 'pending' || empty($job['technician_id'])): ?>
                                <div class="d-grid gap-2">
                                    <form action="<?= $baseUrl ?>/technician/jobs/accept" method="POST">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                        <input type="hidden" name="job_id" value="<?= $job['id'] ?>">
                                        <button type="submit" class="btn btn-success font-semibold py-3 rounded-pill w-100 shadow-sm" onclick="return confirm('Apakah Anda yakin ingin MENGAMBIL & MENERIMA pekerjaan ini?');">
                                            <i class="bi bi-hand-index-thumb-fill me-1"></i> Ambil & Terima Tugas Ini
                                        </button>
                                    </form>
                                    <div class="small text-muted text-center mt-1">Pekerjaan ini belum diambil teknisi lain. Klik tombol di atas untuk menjadikannya tugas aktif Anda.</div>
                                </div>

                            <!-- Action 1: If Status is ASSIGNED (Accept / Reject) -->
                            <?php elseif ($job['status'] === 'assigned'): ?>
                                <div class="d-grid gap-2">
                                    <!-- Accept Form -->
                                    <form action="<?= $baseUrl ?>/technician/jobs/accept" method="POST">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                        <input type="hidden" name="job_id" value="<?= $job['id'] ?>">
                                        <button type="submit" class="btn btn-success font-semibold py-2 rounded-pill w-100 shadow-sm" onclick="return confirm('Apakah Anda yakin ingin MENERIMA pekerjaan ini?');">
                                            <i class="bi bi-check-circle-fill me-1"></i> Accept Job (Terima Tugas)
                                        </button>
                                    </form>

                                    <!-- Reject Button (Triggers Modal) -->
                                    <button type="button" class="btn btn-outline-danger font-semibold py-2 rounded-pill w-100" data-bs-toggle="modal" data-bs-target="#rejectJobModal">
                                        <i class="bi bi-x-circle-fill me-1"></i> Reject Job (Tolak Tugas)
                                    </button>
                                </div>

                            <!-- Action 2: If Status is ACCEPTED (Update to ON_THE_WAY) -->
                            <?php elseif ($job['status'] === 'accepted'): ?>
                                <form action="<?= $baseUrl ?>/technician/jobs/update-status" method="POST">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                    <input type="hidden" name="job_id" value="<?= $job['id'] ?>">
                                    <input type="hidden" name="status" value="on_the_way">
                                    <button type="submit" class="btn btn-info text-dark font-semibold py-3 rounded-pill w-100 shadow-sm" onclick="return confirm('Update status menjadi [Dalam Perjalanan]?');">
                                        <i class="bi bi-truck me-1"></i> Update Status: Dalam Perjalanan
                                    </button>
                                </form>

                            <!-- Action 3: If Status is ON_THE_WAY (Update to REPAIRING) -->
                            <?php elseif ($job['status'] === 'on_the_way'): ?>
                                <form action="<?= $baseUrl ?>/technician/jobs/update-status" method="POST">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                    <input type="hidden" name="job_id" value="<?= $job['id'] ?>">
                                    <input type="hidden" name="status" value="repairing">
                                    <button type="submit" class="btn btn-warning text-dark font-semibold py-3 rounded-pill w-100 shadow-sm" onclick="return confirm('Update status menjadi [Sedang Melakukan Perbaikan]?');">
                                        <i class="bi bi-tools me-1"></i> Update Status: Mulai Perbaikan
                                    </button>
                                </form>

                            <!-- Action 4: If Status is REPAIRING (Go to Completion Form) -->
                            <?php elseif ($job['status'] === 'repairing'): ?>
                                <a href="<?= $baseUrl ?>/technician/jobs/complete?id=<?= $job['id'] ?>" class="btn btn-success font-semibold py-3 rounded-pill w-100 shadow-sm">
                                    <i class="bi bi-check-all me-1"></i> Isi Form Penyelesaian Job
                                </a>

                            <!-- Action 5: If Status is COMPLETED -->
                            <?php elseif ($job['status'] === 'completed'): ?>
                                <div class="alert alert-success text-center rounded-3 mb-0">
                                    <i class="bi bi-check-circle-fill fs-4 d-block mb-1"></i>
                                    <strong>Pekerjaan Telah Selesai</strong>
                                    <div class="small">Data perbaikan & biaya sudah tersimpan.</div>
                                </div>
                            <?php endif; ?>

                        </div>
                    </div>

                    <!-- Status Timeline Tracking Card -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-map me-2 text-primary"></i> Timeline Tracking Status</h5>
                        </div>
                        <div class="card-body p-4">
                            <?php
                                $currentStatus = $job['status'];
                                require __DIR__ . '/../../layouts/partials/status_timeline.php';
                            ?>
                        </div>
                    </div>

                    <!-- Schedule Info Card -->
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-calendar-check text-primary me-2"></i> Jadwal & Estimasi</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <label class="text-muted small fw-semibold text-uppercase d-block mb-1">Jadwal Kunjungan</label>
                                <div class="fw-bold text-dark fs-6">
                                    <i class="bi bi-clock-fill text-primary me-2"></i><?= htmlspecialchars($job['schedule']) ?>
                                </div>
                            </div>
                            <hr>
                            <div>
                                <label class="text-muted small fw-semibold text-uppercase d-block mb-1">Perkiraan Biaya Awal</label>
                                <div class="fw-bold text-success fs-5">
                                    Rp <?= number_format($job['estimated_price'], 0, ',', '.') ?>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>
</div>

<!-- Modal Reject Job -->
<div class="modal fade" id="rejectJobModal" tabindex="-1" aria-labelledby="rejectJobModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-danger text-white rounded-top-4">
                <h5 class="modal-title fw-bold" id="rejectJobModalLabel"><i class="bi bi-exclamation-triangle-fill me-2"></i> Konfirmasi Penolakan Pekerjaan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= $baseUrl ?>/technician/jobs/reject" method="POST">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <input type="hidden" name="job_id" value="<?= $job['id'] ?>">

                <div class="modal-body p-4">
                    <p class="text-muted small">Pekerjaan #JOB-<?= $job['id'] ?> akan dikembalikan ke daftar antrean admin untuk ditugaskan ke teknisi lain. Harap masukkan alasan penolakan secara jelas.</p>
                    
                    <div class="mb-3">
                        <label for="rejection_note" class="form-label font-semibold">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="rejection_note" name="rejection_note" rows="3" placeholder="Contoh: Lokasi terlalu jauh / Jadwal bentrok dengan pekerjaan lain..." required minlength="5"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 font-semibold">
                        <i class="bi bi-x-circle me-1"></i> Tolak Pekerjaan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
