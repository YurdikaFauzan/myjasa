<?php
$baseUrl = \Config\Env::get('APP_URL', 'http://localhost/Myjasagwuehhhhhhhhhhhhhhhhhhhhh');
?>

<div class="container-fluid px-lg-5 py-4">

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
                        <i class="bi bi-ticket-perforated me-1"></i> Detail Pesanan #REQ-<?= $request['id'] ?>
                    </div>
                    <h3 class="fw-bold mb-1"><?= htmlspecialchars($service['name'] ?? 'Perbaikan Peralatan') ?></h3>
                    <p class="text-white-50 mb-0 small">Kategori: <?= htmlspecialchars($category['name'] ?? 'Peralatan Rumah Tangga') ?></p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <!-- Cancel Button (only for pending/assigned) -->
                    <?php if (in_array($request['status'], ['pending', 'assigned'])): ?>
                        <button type="button" class="btn btn-outline-danger rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#cancelModal">
                            <i class="bi bi-x-circle me-1"></i> Batalkan Pesanan
                        </button>
                    <?php endif; ?>
                    <a href="<?= $baseUrl ?>/customer/requests" class="btn btn-outline-light rounded-pill px-3">
                        <i class="bi bi-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>

            <div class="row g-4">

                <!-- Left Column: Details & Technician Notes -->
                <div class="col-lg-8">

                    <!-- Detail Keluhan Card -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-file-text text-primary me-2"></i> Rincian Keluhan & Gejala Kerusakan</h5>
                        </div>
                        <div class="card-body p-4">
                            <p class="mb-0 text-secondary lead fs-6" style="white-space: pre-line;">
                                <?= htmlspecialchars($request['complaint']) ?>
                            </p>
                            <?php if (!empty($request['complaint_photo'])): ?>
                                <hr class="my-3">
                                <label class="text-muted small fw-semibold text-uppercase d-block mb-2"><i class="bi bi-camera-fill text-primary me-1"></i> Foto Kerusakan (Diunggah saat Booking)</label>
                                <div class="position-relative d-inline-block">
                                    <img src="<?= $baseUrl ?>/<?= htmlspecialchars($request['complaint_photo']) ?>" alt="Foto Kerusakan" class="img-fluid rounded-3 shadow-sm" style="max-height: 280px; object-fit: cover; cursor: pointer;" onclick="window.open(this.src, '_blank')">
                                    <span class="position-absolute bottom-0 start-0 m-2 badge bg-dark bg-opacity-75 text-white rounded-pill px-2 py-1 small"><i class="bi bi-zoom-in me-1"></i>Klik untuk memperbesar</span>
                                </div>
                            <?php endif; ?>
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

                    <!-- Alamat Kunjungan Card -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-geo-alt text-danger me-2"></i> Alamat Lokasi Kunjungan</h5>
                        </div>
                        <div class="card-body p-4">
                            <p class="mb-0 text-dark font-semibold">
                                <i class="bi bi-geo-alt-fill text-danger me-2"></i><?= htmlspecialchars($request['address']) ?>
                            </p>
                        </div>
                    </div>

                    <!-- Laporan & Hasil Perbaikan Teknisi -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-journal-check text-success me-2"></i> Laporan & Hasil Perbaikan Teknisi</h5>
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
                                    <div class="col-4">
                                        <div class="p-2 bg-light rounded-3 text-center">
                                            <span class="text-muted small d-block">Biaya Jasa</span>
                                            <strong class="text-dark">Rp <?= number_format($request['service_fee'] ?? 0, 0, ',', '.') ?></strong>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="p-2 bg-light rounded-3 text-center">
                                            <span class="text-muted small d-block">Biaya Sparepart</span>
                                            <strong class="text-dark">Rp <?= number_format($request['sparepart_fee'] ?? 0, 0, ',', '.') ?></strong>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="p-2 bg-success-subtle text-success rounded-3 text-center">
                                            <span class="text-muted small d-block">Total Biaya</span>
                                            <strong class="fs-6">Rp <?= number_format($request['final_price'] ?? 0, 0, ',', '.') ?></strong>
                                        </div>
                                    </div>
                                </div>
                                <?php if (!empty($request['technician_note'])): ?>
                                    <div class="mb-3">
                                        <label class="text-muted small fw-semibold text-uppercase d-block mb-1">Catatan Tambahan Teknisi</label>
                                        <div class="bg-light p-3 rounded-3 text-dark small" style="white-space: pre-line;"><?= htmlspecialchars($request['technician_note']) ?></div>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($request['completion_photo'])): ?>
                                    <div>
                                        <label class="text-muted small fw-semibold text-uppercase d-block mb-2">Foto Hasil Pekerjaan</label>
                                        <img src="<?= $baseUrl ?>/<?= htmlspecialchars($request['completion_photo']) ?>" alt="Foto Pengerjaan" class="img-fluid rounded-3 shadow-sm" style="max-height: 250px; object-fit: cover;">
                                    </div>
                                <?php endif; ?>
                            <?php elseif (!empty($request['technician_note'])): ?>
                                <div class="bg-light p-3 rounded-3 text-dark" style="white-space: pre-line;"><?= htmlspecialchars($request['technician_note']) ?></div>
                            <?php else: ?>
                                <p class="mb-0 text-muted small fst-italic"><i class="bi bi-hourglass me-1"></i> Belum ada catatan dari teknisi.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Review Card: Show existing review OR form to submit -->
                    <?php if ($request['status'] === 'completed'): ?>
                        <?php if (!empty($review)): ?>
                            <!-- Existing Review Display -->
                            <div class="card border-0 shadow-sm rounded-4 mb-4 border-start border-4 border-warning">
                                <div class="card-header bg-white py-3">
                                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-star-fill text-warning me-2"></i> Ulasan Anda</h5>
                                </div>
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center gap-1 mb-3">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="bi bi-star-fill fs-4 <?= $i <= (int)$review['rating'] ? 'text-warning' : 'text-muted' ?>"></i>
                                        <?php endfor; ?>
                                        <span class="ms-2 fw-bold text-dark fs-5"><?= $review['rating'] ?>/5</span>
                                    </div>
                                    <?php if (!empty($review['comment'])): ?>
                                        <p class="mb-0 text-dark" style="white-space: pre-line;"><?= htmlspecialchars($review['comment']) ?></p>
                                    <?php else: ?>
                                        <p class="mb-0 text-muted fst-italic">Tidak ada komentar.</p>
                                    <?php endif; ?>
                                    <div class="text-muted small mt-2"><i class="bi bi-clock me-1"></i><?= htmlspecialchars(date('d M Y, H:i', strtotime($review['created_at']))) ?></div>
                                </div>
                            </div>
                        <?php else: ?>
                            <!-- Review Submission Form -->
                            <div class="card border-0 shadow-sm rounded-4 mb-4 border-start border-4 border-warning">
                                <div class="card-header bg-white py-3">
                                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-star-fill text-warning me-2"></i> Berikan Ulasan & Rating</h5>
                                </div>
                                <div class="card-body p-4">
                                    <p class="text-muted small mb-3">Bagaimana pengalaman Anda dengan layanan perbaikan ini? Ulasan Anda sangat berarti untuk teknisi kami.</p>

                                    <form action="<?= $baseUrl ?>/customer/requests/review" method="POST">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                        <input type="hidden" name="service_request_id" value="<?= $request['id'] ?>">

                                        <!-- Star Rating Input -->
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Rating <span class="text-danger">*</span></label>
                                            <div class="d-flex gap-2 align-items-center star-rating-input" id="starRatingWidget">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <i class="bi bi-star fs-2 text-muted star-btn" data-rating="<?= $i ?>" style="cursor: pointer;" title="<?= $i ?> Bintang"></i>
                                                <?php endfor; ?>
                                                <span class="ms-2 small text-muted" id="ratingText">Pilih rating</span>
                                            </div>
                                            <input type="hidden" name="rating" id="ratingInput" value="">
                                        </div>

                                        <!-- Comment -->
                                        <div class="mb-4">
                                            <label for="review_comment" class="form-label fw-semibold">Komentar (Opsional)</label>
                                            <textarea class="form-control rounded-3" id="review_comment" name="comment" rows="3"
                                                placeholder="Ceritakan pengalaman Anda dengan layanan ini..."></textarea>
                                        </div>

                                        <button type="submit" id="submitReviewBtn" class="btn btn-warning text-dark fw-bold rounded-pill px-4 shadow-sm" disabled>
                                            <i class="bi bi-send-fill me-1"></i> Kirim Ulasan
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                </div>

                <!-- Right Column: Tracking Timeline, Status, Technician, Price -->
                <div class="col-lg-4">

                    <!-- Status Timeline Tracking Card -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-map me-2 text-primary"></i> Tracking Status Pesanan</h5>
                        </div>
                        <div class="card-body p-4">
                            <?php
                                $currentStatus = $request['status'];
                                require __DIR__ . '/../../layouts/partials/status_timeline.php';
                            ?>
                        </div>
                    </div>

                    <!-- Status & Jadwal Card -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-info-circle me-2 text-info"></i> Status & Jadwal</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <label class="text-muted small fw-semibold text-uppercase d-block mb-1">Status Pesanan saat ini</label>
                                <span class="badge <?= match($request['status']) { 'completed'=>'bg-success', 'repairing'=>'bg-warning text-dark', 'pending'=>'bg-secondary', 'cancelled'=>'bg-danger', 'assigned'=>'bg-primary', 'accepted'=>'bg-info text-dark', 'on_the_way'=>'bg-warning text-dark', default=>'bg-secondary' } ?> px-3 py-2 fs-6 rounded-pill">
                                    <?= htmlspecialchars(strtoupper(str_replace('_', ' ', $request['status']))) ?>
                                </span>
                            </div>
                            <hr>
                            <div>
                                <label class="text-muted small fw-semibold text-uppercase d-block mb-1">Jadwal Kunjungan Teknisi</label>
                                <div class="fw-bold text-dark fs-6"><i class="bi bi-calendar-check me-2 text-primary"></i><?= htmlspecialchars($request['schedule']) ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Info Teknisi Bertugas Card -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-person-wrench me-2 text-warning"></i> Teknisi Bertugas</h5>
                        </div>
                        <div class="card-body p-4">
                            <?php if (!empty($technician)): ?>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-warning-subtle text-dark p-3 rounded-circle fs-4">
                                        <i class="bi bi-tools"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-1"><?= htmlspecialchars($technician['name']) ?></h6>
                                        <div class="small text-muted mb-1"><i class="bi bi-telephone me-1"></i><?= htmlspecialchars($technician['phone'] ?? '-') ?></div>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill small">Teknisi Terverifikasi</span>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="text-muted small">
                                    <i class="bi bi-clock me-1 text-warning"></i> Admin sedang menugaskan teknisi terdekat ke lokasi Anda.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Rincian Biaya Card -->
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-cash-stack me-2 text-success"></i> Rincian Biaya</h5>
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
                                    <span class="text-muted small">(Menunggu hasil pemeriksaan)</span>
                                <?php endif; ?>
                            </div>
                            <hr class="my-3">
                            <div class="bg-light p-3 rounded-3 mb-3">
                                <div class="d-flex justify-content-between align-items-center small">
                                    <span class="text-muted">Status Pembayaran:</span>
                                    <?php if (!empty($payment)): ?>
                                        <span class="badge <?= match($payment['payment_status']) { 'paid'=>'bg-success', 'pending'=>'bg-warning text-dark', default=>'bg-secondary' } ?> rounded-pill">
                                            <?= htmlspecialchars(strtoupper($payment['payment_status'])) ?> (<?= htmlspecialchars(strtoupper($payment['payment_method'])) ?>)
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-secondary rounded-pill">Belum Dibayar</span>
                                    <?php endif; ?>
                                </div>
                                <?php if (!empty($payment['paid_at'])): ?>
                                    <div class="small text-muted mt-2">
                                        <i class="bi bi-clock-history me-1"></i> Dibayar: <?= date('d M Y, H:i', strtotime($payment['paid_at'])) ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Payment Action Button -->
                            <?php $isPaid = (!empty($payment) && $payment['payment_status'] === 'paid'); ?>
                            <?php if (!$isPaid && $request['status'] !== 'cancelled'): ?>
                                <a href="<?= $baseUrl ?>/customer/requests/pay?id=<?= $request['id'] ?>" class="btn btn-success rounded-pill w-100 py-2 fw-semibold shadow-sm d-flex align-items-center justify-content-center gap-2">
                                    <i class="bi bi-credit-card-2-front-fill fs-5"></i>
                                    <span>Bayar Pesanan Sekarang</span>
                                </a>
                            <?php elseif ($isPaid): ?>
                                <div class="alert alert-success d-flex align-items-center gap-2 mb-0 rounded-pill py-2 px-3 small">
                                    <i class="bi bi-check-circle-fill fs-5 text-success"></i>
                                    <span class="fw-semibold">Pembayaran Lunas</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
            </div>

</div>

<!-- Modal Cancel Booking -->
<?php if (in_array($request['status'], ['pending', 'assigned'])): ?>
<div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-danger text-white rounded-top-4">
                <h5 class="modal-title fw-bold" id="cancelModalLabel"><i class="bi bi-exclamation-triangle-fill me-2"></i> Konfirmasi Pembatalan Pesanan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted">Apakah Anda yakin ingin membatalkan pesanan <strong>#REQ-<?= $request['id'] ?></strong>? Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="modal-footer bg-light rounded-bottom-4">
                <form action="<?= $baseUrl ?>/customer/requests/cancel" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                    <input type="hidden" name="request_id" value="<?= $request['id'] ?>">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tidak, Pertahankan</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">
                        <i class="bi bi-x-circle me-1"></i> Ya, Batalkan Pesanan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
// Interactive Star Rating Widget
document.addEventListener('DOMContentLoaded', function() {
    const stars     = document.querySelectorAll('.star-btn');
    const ratingInput  = document.getElementById('ratingInput');
    const ratingText   = document.getElementById('ratingText');
    const submitBtn    = document.getElementById('submitReviewBtn');
    const ratingLabels = { 1: '⭐ Sangat Kurang', 2: '⭐⭐ Kurang', 3: '⭐⭐⭐ Cukup', 4: '⭐⭐⭐⭐ Bagus', 5: '⭐⭐⭐⭐⭐ Luar Biasa!' };

    if (!stars.length) return;

    stars.forEach(star => {
        star.addEventListener('mouseover', function() {
            const rating = parseInt(this.dataset.rating);
            highlightStars(rating);
        });
        star.addEventListener('mouseout', function() {
            const current = parseInt(ratingInput?.value || 0);
            highlightStars(current);
        });
        star.addEventListener('click', function() {
            const rating = parseInt(this.dataset.rating);
            if (ratingInput) ratingInput.value = rating;
            if (ratingText) ratingText.textContent = ratingLabels[rating] || '';
            if (submitBtn) submitBtn.disabled = false;
            highlightStars(rating);
        });
    });

    function highlightStars(count) {
        stars.forEach((s, i) => {
            s.classList.toggle('bi-star-fill', i < count);
            s.classList.toggle('bi-star', i >= count);
            s.classList.toggle('text-warning', i < count);
            s.classList.toggle('text-muted', i >= count);
        });
    }
});
</script>
