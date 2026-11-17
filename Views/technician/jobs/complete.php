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

            <!-- Page Header Card -->
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 p-4 bg-dark text-white rounded-4 shadow-sm">
                <div>
                    <div class="badge bg-success px-3 py-1 rounded-pill mb-2">
                        <i class="bi bi-check-all me-1"></i> Form Penyelesaian Pekerjaan #JOB-<?= $job['id'] ?>
                    </div>
                    <h3 class="fw-bold mb-1"><?= htmlspecialchars($service['name'] ?? 'Layanan Perbaikan') ?></h3>
                    <p class="text-white-50 mb-0 small">Customer: <?= htmlspecialchars($customer['name'] ?? 'Pelanggan') ?> | Alamat: <?= htmlspecialchars($job['address']) ?></p>
                </div>
                <div>
                    <a href="<?= $baseUrl ?>/technician/jobs/detail?id=<?= $job['id'] ?>" class="btn btn-outline-light rounded-pill px-3">
                        <i class="bi bi-arrow-left me-1"></i> Batal / Kembali ke Detail
                    </a>
                </div>
            </div>

            <!-- Completion Form Card -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-journal-text text-success me-2"></i> Pengisian Laporan Perbaikan & Biaya
                    </h5>
                </div>

                <div class="card-body p-4">
                    <!-- Keluhan & Foto Kerusakan Awal dari Customer -->
                    <div class="alert alert-light border rounded-3 p-3 mb-4">
                        <h6 class="fw-bold text-dark mb-2"><i class="bi bi-info-circle-fill text-primary me-1"></i> Data Keluhan Awal dari Pelanggan:</h6>
                        <p class="text-secondary small mb-2" style="white-space: pre-line;"><?= htmlspecialchars($job['complaint']) ?></p>
                        <?php if (!empty($job['complaint_photo'])): ?>
                            <div class="mt-2">
                                <span class="small fw-semibold text-muted d-block mb-1"><i class="bi bi-camera me-1"></i> Foto Kerusakan Awal:</span>
                                <img src="<?= $baseUrl ?>/<?= htmlspecialchars($job['complaint_photo']) ?>" alt="Foto Kerusakan Awal" class="img-fluid rounded-3 border shadow-sm" style="max-height: 160px; object-fit: cover; cursor: pointer;" onclick="window.open(this.src, '_blank')">
                            </div>
                        <?php endif; ?>
                    </div>

                    <form action="<?= $baseUrl ?>/technician/jobs/complete" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                        <input type="hidden" name="job_id" value="<?= $job['id'] ?>">

                        <!-- 1. Hasil Pemeriksaan -->
                        <div class="mb-4">
                            <label for="inspection_result" class="form-label font-semibold text-dark">
                                1. Hasil Pemeriksaan / Problem Diagnosis <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control rounded-3" id="inspection_result" name="inspection_result" rows="3" placeholder="Jelaskan kondisi alat saat diperiksa (misal: Kompresor kulkas mati akibat relay rusak, freon berkurang)..." required minlength="5"><?= htmlspecialchars($_POST['inspection_result'] ?? $job['inspection_result'] ?? '') ?></textarea>
                            <div class="form-text">Tuliskan hasil pengecekan atau penyebab kerusakan peralatan.</div>
                        </div>

                        <!-- 2. Tindakan Perbaikan -->
                        <div class="mb-4">
                            <label for="repair_action" class="form-label font-semibold text-dark">
                                2. Tindakan Perbaikan Yang Dilakukan <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control rounded-3" id="repair_action" name="repair_action" rows="3" placeholder="Jelaskan langkah perbaikan yang dilakukan (misal: Mengganti relay kompresor 1/8 HP, mencuci evaporator, dan isi freon R134a)..." required minlength="5"><?= htmlspecialchars($_POST['repair_action'] ?? $job['repair_action'] ?? '') ?></textarea>
                            <div class="form-text">Tuliskan tindakan perbaikan atau komponen yang diganti/diperbaiki.</div>
                        </div>

                        <!-- 3 & 4. Biaya Jasa & Sparepart Grid -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="service_fee" class="form-label font-semibold text-dark">
                                    3. Biaya Jasa Perbaikan (Rp) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">Rp</span>
                                    <input type="number" class="form-control rounded-end-3" id="service_fee" name="service_fee" min="0" step="1000" value="<?= htmlspecialchars($_POST['service_fee'] ?? $job['service_fee'] ?? $service['price'] ?? 0) ?>" required oninput="calculateTotal()">
                                </div>
                                <div class="form-text">Tarif jasa pengerjaan teknisi.</div>
                            </div>

                            <div class="col-md-6">
                                <label for="sparepart_fee" class="form-label font-semibold text-dark">
                                    4. Biaya Sparepart / Bahan (Rp) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">Rp</span>
                                    <input type="number" class="form-control rounded-end-3" id="sparepart_fee" name="sparepart_fee" min="0" step="1000" value="<?= htmlspecialchars($_POST['sparepart_fee'] ?? $job['sparepart_fee'] ?? 0) ?>" required oninput="calculateTotal()">
                                </div>
                                <div class="form-text">Biaya pengadaan komponen / suku cadang (isi 0 jika tidak ada).</div>
                            </div>
                        </div>

                        <!-- 5. Total Biaya Auto Calculated -->
                        <div class="card border-0 bg-success-subtle rounded-3 p-3 mb-4">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                                <div>
                                    <span class="text-success small fw-semibold text-uppercase d-block">5. Total Biaya Keseluruhan (Jasa + Sparepart)</span>
                                    <h3 class="fw-bold text-success mb-0" id="total_cost_display">Rp 0</h3>
                                </div>
                                <div>
                                    <span class="badge bg-success rounded-pill px-3 py-2">
                                        <i class="bi bi-calculator me-1"></i> Terhitung Otomatis
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- 6. Catatan Tambahan -->
                        <div class="mb-4">
                            <label for="technician_note" class="form-label font-semibold text-dark">
                                6. Catatan Tambahan (Opsional)
                            </label>
                            <textarea class="form-control rounded-3" id="technician_note" name="technician_note" rows="2" placeholder="Catatan atau garansi perbaikan untuk pelanggan (misal: Garansi perbaikan 30 hari)..."><?= htmlspecialchars($_POST['technician_note'] ?? $job['technician_note'] ?? '') ?></textarea>
                        </div>

                        <!-- 7. Upload Foto Hasil Pekerjaan -->
                        <div class="mb-4">
                            <label for="completion_photo" class="form-label font-semibold text-dark">
                                7. Foto Hasil Pekerjaan (Opsional)
                            </label>
                            <input type="file" class="form-control rounded-3" id="completion_photo" name="completion_photo" accept="image/jpeg,image/png,image/webp" onchange="previewImage(event)">
                            <div class="form-text">Unggah foto unit/hasil perbaikan (Format: JPG, PNG, WEBP, maks 5 MB).</div>
                            
                            <div id="imagePreviewContainer" class="mt-3 d-none">
                                <span class="small text-muted d-block mb-1">Preview Foto:</span>
                                <img id="imagePreview" src="#" alt="Preview Foto Hasil" class="img-fluid rounded-3 border shadow-sm" style="max-height: 200px; object-fit: cover;">
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Submit Button -->
                        <div class="d-flex justify-content-end gap-3">
                            <a href="<?= $baseUrl ?>/technician/jobs/detail?id=<?= $job['id'] ?>" class="btn btn-light border rounded-pill px-4">
                                Batal
                            </a>
                            <button type="submit" class="btn btn-success rounded-pill px-5 font-semibold shadow-sm">
                                <i class="bi bi-check-circle-fill me-1"></i> Simpan & Selesaikan Pekerjaan
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
function calculateTotal() {
    const serviceFee   = parseFloat(document.getElementById('service_fee').value) || 0;
    const sparepartFee = parseFloat(document.getElementById('sparepart_fee').value) || 0;
    const total        = serviceFee + sparepartFee;

    const formatted = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(total);
    document.getElementById('total_cost_display').innerText = formatted;
}

function previewImage(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('imagePreview').src = e.target.result;
            document.getElementById('imagePreviewContainer').classList.remove('d-none');
        };
        reader.readAsDataURL(file);
    }
}

// Initial calculation on page load
document.addEventListener('DOMContentLoaded', calculateTotal);
</script>
