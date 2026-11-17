<?php
$baseUrl = \Config\Env::get('APP_URL', 'http://localhost/Myjasagwuehhhhhhhhhhhhhhhhhhhhh');
$todayDate = date('Y-m-d');
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
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 p-4 bg-primary text-white rounded-4 shadow-sm">
                <div>
                    <span class="badge bg-white text-primary font-semibold mb-2 px-3 py-1 rounded-pill">
                        <i class="bi bi-plus-circle-fill me-1"></i> Form Pemesanan Jasa
                    </span>
                    <h2 class="fw-bold mb-1">Pesan Jasa Perbaikan Peralatan Rumah Tangga</h2>
                    <p class="text-white-50 mb-0 small">Isi formulir di bawah ini untuk mengundang teknisi berpengalaman ke lokasi Anda.</p>
                </div>
                <div>
                    <a href="<?= $baseUrl ?>/customer/requests" class="btn btn-outline-light rounded-pill px-3">
                        <i class="bi bi-arrow-left me-1"></i> Pesanan Saya
                    </a>
                </div>
            </div>

            <!-- Booking Form Card -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-body p-4 p-md-5">

                    <form action="<?= $baseUrl ?>/customer/requests/create" method="POST" enctype="multipart/form-data" id="bookingForm">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                        
                        <?php if (!empty($aiConsultation)): ?>
                            <input type="hidden" name="ai_consultation_id" value="<?= htmlspecialchars($aiConsultation['id']) ?>">
                            <div class="alert alert-primary border-primary-subtle rounded-3 shadow-sm mb-4 d-flex align-items-start">
                                <i class="bi bi-robot fs-3 me-3 text-primary"></i>
                                <div>
                                    <h6 class="fw-bold mb-1">Melanjutkan dari Konsultasi AI</h6>
                                    <p class="mb-0 small">Kategori dan keluhan Anda telah diisi otomatis. AI merekomendasikan: <strong><?= htmlspecialchars($aiConsultation['recommendation'] ?? '') ?></strong>.</p>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Langkah 1: Kategori & Layanan -->
                        <div class="mb-4">
                            <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">
                                <span class="badge bg-primary rounded-circle me-2">1</span> Pilih Peralatan & Jenis Layanan
                            </h5>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="category_id" class="form-label font-semibold">1. Kategori Peralatan <span class="text-danger">*</span></label>
                                    <select class="form-select form-select-lg rounded-3" id="category_id" required>
                                        <option value="" disabled <?= empty($aiConsultation) ? 'selected' : '' ?>>-- Pilih Kategori Peralatan --</option>
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="<?= $cat['id'] ?>" <?= (!empty($aiConsultation) && $aiConsultation['category_id'] == $cat['id']) ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-text small">Pilih jenis peralatan rumah tangga yang mengalami kendala.</div>
                                </div>

                                <div class="col-md-6">
                                    <label for="service_id" class="form-label font-semibold">2. Jenis Layanan Perbaikan <span class="text-danger">*</span></label>
                                    <select class="form-select form-select-lg rounded-3" id="service_id" name="service_id" required disabled>
                                        <option value="" disabled selected>-- Pilih Kategori Terlebih Dahulu --</option>
                                    </select>
                                    <div class="form-text small" id="service_price_hint">Pilihan layanan akan otomatis disesuaikan dengan kategori.</div>
                                </div>
                            </div>

                            <!-- Tarif Preview Alert -->
                            <div class="alert alert-info-subtle border-info-subtle text-info-emphasis rounded-3 mt-3 d-none" id="price_preview_box">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <strong id="preview_service_name"></strong>
                                        <div class="small" id="preview_service_desc"></div>
                                    </div>
                                    <div class="text-end">
                                        <div class="text-muted small">Estimasi Tarif:</div>
                                        <span class="fs-4 fw-bold text-primary" id="preview_service_price"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Langkah 2: Detail Keluhan & Foto -->
                        <div class="mb-4">
                            <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">
                                <span class="badge bg-primary rounded-circle me-2">2</span> Detail Keluhan & Lokasi
                            </h5>

                            <div class="mb-3">
                                <label for="complaint" class="form-label font-semibold">Deskripsi Keluhan Kerusakan <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="complaint" name="complaint" rows="4" placeholder="Jelaskan secara rinci kendala/gejala kerusakan peralatan Anda (contoh: AC kamar kurang dingin, berbunyi bising saat dinyalakan)..." required><?= !empty($aiConsultation) ? htmlspecialchars($aiConsultation['question']) : '' ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="photo" class="form-label font-semibold"><i class="bi bi-camera me-1"></i> Foto Kerusakan (Opsional)</label>
                                <input type="file" class="form-control" id="photo" name="photo" accept="image/jpeg,image/png,image/webp,image/gif">
                                <div class="form-text small">Unggah foto unit/gejala kerusakan (max 5MB). Format: JPG, PNG, WEBP, GIF.</div>
                                <!-- Photo Preview Box -->
                                <div id="photoPreviewBox" class="mt-2 d-none">
                                    <div class="position-relative d-inline-block">
                                        <img id="photoPreview" src="" alt="Preview" class="img-fluid rounded-3 shadow-sm border" style="max-height: 200px; object-fit: cover;">
                                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 rounded-circle" onclick="clearPhotoPreview()" title="Hapus foto">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </div>
                                    <div class="small text-success mt-1"><i class="bi bi-check-circle me-1"></i> <span id="photoFileName"></span></div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="address" class="form-label font-semibold">Alamat Lokasi Kunjungan <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="address" name="address" rows="3" required><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                            </div>
                        </div>

                        <!-- Langkah 3: Jadwal Kunjungan -->
                        <div class="mb-4">
                            <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">
                                <span class="badge bg-primary rounded-circle me-2">3</span> Jadwal Kunjungan Teknisi
                            </h5>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="booking_date" class="form-label font-semibold">Tanggal Kunjungan <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control form-control-lg" id="booking_date" name="booking_date" min="<?= $todayDate ?>" value="<?= $todayDate ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="booking_time" class="form-label font-semibold">Waktu Kunjungan <span class="text-danger">*</span></label>
                                    <select class="form-select form-select-lg" id="booking_time" name="booking_time" required>
                                        <option value="09:00">Pagi (09:00 WIB)</option>
                                        <option value="11:00" selected>Siang (11:00 WIB)</option>
                                        <option value="14:00">Siang Hari (14:00 WIB)</option>
                                        <option value="16:00">Sore (16:00 WIB)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Langkah 4: Pilih Mitra Teknisi -->
                        <div class="mb-4">
                            <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">
                                <span class="badge bg-primary rounded-circle me-2">4</span> Pilihan Mitra Teknisi
                            </h5>
                            <div class="mb-3">
                                <label for="technician_id" class="form-label font-semibold"><i class="bi bi-tools text-warning me-1"></i> Tugaskan ke Teknisi</label>
                                <select class="form-select form-select-lg rounded-3" id="technician_id" name="technician_id">
                                    <option value="auto" selected>✨ Otomatis Tugaskan ke Mitra Teknisi Terbaik yang Tersedia (Disarankan)</option>
                                    <?php if (!empty($technicians)): ?>
                                        <?php foreach ($technicians as $tech): ?>
                                            <option value="<?= $tech['id'] ?>">
                                                👤 <?= htmlspecialchars($tech['name']) ?> (HP: <?= htmlspecialchars($tech['phone'] ?? '-') ?>) <?= !empty($tech['address']) ? ' - ' . htmlspecialchars($tech['address']) : '' ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <div class="form-text small">Pesanan yang Anda buat akan langsung muncul di menu <strong>Job Baru</strong> akun teknisi yang bersangkutan.</div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-3 border-top d-flex justify-content-end gap-2">
                            <a href="<?= $baseUrl ?>/customer/dashboard" class="btn btn-outline-secondary rounded-pill px-4">Batal</a>
                            <button type="submit" class="btn btn-primary font-semibold rounded-pill px-5 shadow-sm">
                                <i class="bi bi-send-fill me-1"></i> Submit Pesanan Sekarang
                            </button>
                        </div>
                    </form>

                </div>
            </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const categorySelect = document.getElementById('category_id');
    const serviceSelect  = document.getElementById('service_id');
    const pricePreviewBox = document.getElementById('price_preview_box');
    const previewName  = document.getElementById('preview_service_name');
    const previewDesc  = document.getElementById('preview_service_desc');
    const previewPrice = document.getElementById('preview_service_price');

    let currentServices = [];

    categorySelect.addEventListener('change', function() {
        const catId = this.value;
        serviceSelect.disabled = true;
        serviceSelect.innerHTML = '<option value="" disabled selected>Memuat layanan...</option>';
        pricePreviewBox.classList.add('d-none');

        if (!catId) return;

        fetch(`<?= $baseUrl ?>/customer/api/services-by-category?category_id=${catId}`)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success' && data.services.length > 0) {
                    currentServices = data.services;
                    serviceSelect.innerHTML = '<option value="" disabled selected>-- Pilih Jenis Layanan --</option>';
                    data.services.forEach(s => {
                        const priceFormatted = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(s.price);
                        serviceSelect.innerHTML += `<option value="${s.id}">${s.name} - ${priceFormatted}</option>`;
                    });
                    serviceSelect.disabled = false;
                } else {
                    serviceSelect.innerHTML = '<option value="" disabled selected>Tidak ada layanan aktif pada kategori ini</option>';
                }
            })
            .catch(err => {
                console.error('Fetch error:', err);
                serviceSelect.innerHTML = '<option value="" disabled selected>Gagal memuat layanan</option>';
            });
    });

    // Trigger category change if it is pre-selected by AI
    if (categorySelect.value) {
        categorySelect.dispatchEvent(new Event('change'));
    }

    serviceSelect.addEventListener('change', function() {
        const selectedId = parseInt(this.value);
        const selectedService = currentServices.find(s => parseInt(s.id) === selectedId);

        if (selectedService) {
            previewName.textContent = selectedService.name;
            previewDesc.textContent = selectedService.description || 'Estimasi durasi: ' + (selectedService.duration || '60 menit');
            previewPrice.textContent = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(selectedService.price);
            pricePreviewBox.classList.remove('d-none');
        }
    });

    // Photo Preview Handler
    const photoInput = document.getElementById('photo');
    const photoPreviewBox = document.getElementById('photoPreviewBox');
    const photoPreview = document.getElementById('photoPreview');
    const photoFileName = document.getElementById('photoFileName');

    if (photoInput) {
        photoInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                if (file.size > 5 * 1024 * 1024) {
                    alert('Ukuran foto maksimal 5 MB!');
                    this.value = '';
                    photoPreviewBox.classList.add('d-none');
                    return;
                }
                const reader = new FileReader();
                reader.onload = function(e) {
                    photoPreview.src = e.target.result;
                    photoFileName.textContent = file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';
                    photoPreviewBox.classList.remove('d-none');
                };
                reader.readAsDataURL(file);
            } else {
                photoPreviewBox.classList.add('d-none');
            }
        });
    }
});
</script>
<script>
function clearPhotoPreview() {
    const photoInput = document.getElementById('photo');
    const photoPreviewBox = document.getElementById('photoPreviewBox');
    if (photoInput) photoInput.value = '';
    if (photoPreviewBox) photoPreviewBox.classList.add('d-none');
}
</script>
