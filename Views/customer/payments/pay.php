<?php
$baseUrl = \Config\Env::get('APP_URL', 'http://localhost/Myjasagwuehhhhhhhhhhhhhhhhhhhhh');
$payableAmount = (float)($request['final_price'] ?? $request['estimated_price']);
$currentMethod = $payment['payment_method'] ?? 'qris';
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">

            <!-- Flash Alert -->
            <?php if (!empty($flash)): ?>
                <div class="alert alert-<?= htmlspecialchars($flash['type']) ?> alert-dismissible fade show rounded-4 shadow-sm mb-4" role="alert">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    <?= htmlspecialchars($flash['message']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Page Header Card -->
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <a href="<?= $baseUrl ?>/customer/requests/detail?id=<?= $request['id'] ?>" class="text-decoration-none text-muted small fw-semibold">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Detail Pesanan
                    </a>
                    <h3 class="fw-bold text-dark mt-2 mb-0">Pembayaran Pesanan</h3>
                </div>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-2 font-semibold">
                    #REQ-<?= $request['id'] ?>
                </span>
            </div>

            <!-- Order Summary Card -->
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
                <div class="d-flex align-items-center gap-3 mb-3 border-bottom pb-3">
                    <div class="p-3 bg-primary-subtle text-primary rounded-4 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                        <i class="bi bi-tools fs-3"></i>
                    </div>
                    <div>
                        <span class="badge bg-secondary-subtle text-secondary rounded-pill small mb-1">
                            <?= htmlspecialchars($category['name'] ?? 'Peralatan') ?>
                        </span>
                        <h5 class="fw-bold text-dark mb-0"><?= htmlspecialchars($service['name'] ?? 'Layanan Servis') ?></h5>
                        <small class="text-muted">Teknisi: <?= htmlspecialchars($technician['name'] ?? 'Mitra Teknisi') ?></small>
                    </div>
                </div>

                <div class="row g-2 small text-muted mb-3">
                    <div class="col-6">
                        <span>Jadwal Pengerjaan:</span>
                        <div class="fw-semibold text-dark"><?= htmlspecialchars($request['schedule']) ?></div>
                    </div>
                    <div class="col-6">
                        <span>Status Pengerjaan:</span>
                        <div>
                            <span class="badge <?= match($request['status']) { 'completed'=>'bg-success', 'repairing'=>'bg-warning text-dark', default=>'bg-info text-dark' } ?> rounded-pill">
                                <?= strtoupper(str_replace('_', ' ', $request['status'])) ?>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Price Breakdown -->
                <div class="bg-light p-3 rounded-4">
                    <?php if (!empty($request['final_price'])): ?>
                        <div class="d-flex justify-content-between mb-1 small">
                            <span class="text-muted">Biaya Jasa:</span>
                            <span class="text-dark">Rp <?= number_format($request['service_fee'] ?? 0, 0, ',', '.') ?></span>
                        </div>
                        <?php if (!empty($request['sparepart_fee']) && (float)$request['sparepart_fee'] > 0): ?>
                            <div class="d-flex justify-content-between mb-1 small">
                                <span class="text-muted">Biaya Penggantian Sparepart:</span>
                                <span class="text-dark">Rp <?= number_format($request['sparepart_fee'], 0, ',', '.') ?></span>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="d-flex justify-content-between mb-1 small">
                            <span class="text-muted">Estimasi Biaya Standar Layanan:</span>
                            <span class="text-dark">Rp <?= number_format($request['estimated_price'] ?? 0, 0, ',', '.') ?></span>
                        </div>
                    <?php endif; ?>

                    <hr class="my-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <strong class="text-dark fs-6">Total Tagihan Pembayaran:</strong>
                        <strong class="text-primary fs-4">Rp <?= number_format($payableAmount, 0, ',', '.') ?></strong>
                    </div>
                </div>
            </div>

            <!-- Payment Form Card -->
            <div class="card border-0 shadow-lg rounded-4 p-4 bg-white mb-4">
                <h5 class="fw-bold text-dark mb-1"><i class="bi bi-wallet2 text-success me-2"></i>Pilih Metode Pembayaran</h5>
                <p class="text-muted small mb-4">Tersedia ragam kanal pembayaran praktis & aman.</p>

                <form action="<?= $baseUrl ?>/customer/requests/pay" method="POST" id="paymentForm">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                    <input type="hidden" name="service_request_id" value="<?= $request['id'] ?>">

                    <div class="row g-3 mb-4">

                        <!-- QRIS Instant -->
                        <div class="col-12 col-md-6">
                            <label class="payment-method-card border rounded-4 p-3 d-flex align-items-center gap-3 w-100 cursor-pointer h-100">
                                <input type="radio" name="payment_method" value="qris" class="form-check-input mt-0" <?= $currentMethod === 'qris' ? 'checked' : '' ?> onchange="togglePaymentInstructions('qris')">
                                <div class="p-2 bg-danger-subtle text-danger rounded-3 fs-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                    <i class="bi bi-qr-code-scan"></i>
                                </div>
                                <div>
                                    <strong class="d-block text-dark small fw-bold">QRIS (Semua E-Wallet)</strong>
                                    <small class="text-muted" style="font-size: 11px;">GoPay, OVO, Dana, ShopeePay, BCA</small>
                                </div>
                            </label>
                        </div>

                        <!-- Transfer Bank Virtual Account -->
                        <div class="col-12 col-md-6">
                            <label class="payment-method-card border rounded-4 p-3 d-flex align-items-center gap-3 w-100 cursor-pointer h-100">
                                <input type="radio" name="payment_method" value="transfer_bank" class="form-check-input mt-0" <?= $currentMethod === 'transfer_bank' ? 'checked' : '' ?> onchange="togglePaymentInstructions('transfer_bank')">
                                <div class="p-2 bg-primary-subtle text-primary rounded-3 fs-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                    <i class="bi bi-bank2"></i>
                                </div>
                                <div>
                                    <strong class="d-block text-dark small fw-bold">Transfer Bank / VA</strong>
                                    <small class="text-muted" style="font-size: 11px;">BCA, Mandiri, BNI, BRI</small>
                                </div>
                            </label>
                        </div>

                        <!-- E-Wallet Langsung -->
                        <div class="col-12 col-md-6">
                            <label class="payment-method-card border rounded-4 p-3 d-flex align-items-center gap-3 w-100 cursor-pointer h-100">
                                <input type="radio" name="payment_method" value="e-wallet" class="form-check-input mt-0" <?= $currentMethod === 'e-wallet' ? 'checked' : '' ?> onchange="togglePaymentInstructions('e-wallet')">
                                <div class="p-2 bg-info-subtle text-info rounded-3 fs-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                    <i class="bi bi-phone"></i>
                                </div>
                                <div>
                                    <strong class="d-block text-dark small fw-bold">E-Wallet Direct</strong>
                                    <small class="text-muted" style="font-size: 11px;">Dana, GoPay, OVO</small>
                                </div>
                            </label>
                        </div>

                        <!-- Cash / Tunai ke Teknisi -->
                        <div class="col-12 col-md-6">
                            <label class="payment-method-card border rounded-4 p-3 d-flex align-items-center gap-3 w-100 cursor-pointer h-100">
                                <input type="radio" name="payment_method" value="cash" class="form-check-input mt-0" <?= $currentMethod === 'cash' ? 'checked' : '' ?> onchange="togglePaymentInstructions('cash')">
                                <div class="p-2 bg-success-subtle text-success rounded-3 fs-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                    <i class="bi bi-cash"></i>
                                </div>
                                <div>
                                    <strong class="d-block text-dark small fw-bold">Tunai (Cash) ke Teknisi</strong>
                                    <small class="text-muted" style="font-size: 11px;">Bayar langsung saat servis tuntas</small>
                                </div>
                            </label>
                        </div>

                    </div>

                    <!-- Instruction Box Dynamic -->
                    <div class="card border-0 rounded-4 p-3 mb-4 bg-light" id="instructionBox">
                        <!-- QRIS Instruction Content -->
                        <div id="inst-qris" class="text-center py-2 instruction-content">
                            <div class="badge bg-danger text-white rounded-pill px-3 py-1 mb-2">Simulasi QRIS Nasional</div>
                            <h6 class="fw-bold text-dark mb-1">Scan Kode QR Berikut</h6>
                            <p class="text-muted small mb-3">Buka aplikasi mobile banking atau e-wallet Anda lalu scan QRIS di bawah ini:</p>
                            <div class="d-inline-block bg-white p-3 rounded-4 shadow-sm border mb-2">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=SMARTSERVICE-REQ-<?= $request['id'] ?>-RP-<?= (int)$payableAmount ?>" alt="QRIS Code" class="img-fluid" style="width: 180px; height: 180px;">
                            </div>
                            <div class="small fw-semibold text-dark">NMID: ID1029384756102 (SmartService AI)</div>
                        </div>

                        <!-- Transfer Bank Instruction Content -->
                        <div id="inst-transfer_bank" class="instruction-content d-none py-2">
                            <div class="badge bg-primary text-white rounded-pill px-3 py-1 mb-2">Virtual Account Bank</div>
                            <h6 class="fw-bold text-dark mb-2">Nomor Rekening Virtual Account</h6>
                            <div class="p-3 bg-white rounded-3 border d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <small class="text-muted d-block">Bank Central Asia (BCA)</small>
                                    <strong class="fs-5 text-dark font-monospace">88019<?= str_pad((string)$request['id'], 6, '0', STR_PAD_LEFT) ?></strong>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" onclick="navigator.clipboard.writeText('88019<?= str_pad((string)$request['id'], 6, '0', STR_PAD_LEFT) ?>'); alert('Nomor VA berhasil disalin!');">
                                    <i class="bi bi-copy me-1"></i> Salin
                                </button>
                            </div>
                            <small class="text-muted">Atas nama: <strong>PT SmartService Solusi AI</strong></small>
                        </div>

                        <!-- E-Wallet Instruction Content -->
                        <div id="inst-e-wallet" class="instruction-content d-none py-2">
                            <div class="badge bg-info text-white rounded-pill px-3 py-1 mb-2">E-Wallet Transfer</div>
                            <h6 class="fw-bold text-dark mb-2">Nomor Akun E-Wallet Resmi</h6>
                            <div class="p-3 bg-white rounded-3 border d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <small class="text-muted d-block">DANA / OVO / GoPay</small>
                                    <strong class="fs-5 text-dark font-monospace">0812-9988-7711</strong>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" onclick="navigator.clipboard.writeText('081299887711'); alert('Nomor e-wallet disalin!');">
                                    <i class="bi bi-copy me-1"></i> Salin
                                </button>
                            </div>
                            <small class="text-muted">Pemberitahuan otomatis terverifikasi setelah konfirmasi.</small>
                        </div>

                        <!-- Cash Instruction Content -->
                        <div id="inst-cash" class="instruction-content d-none py-2 text-center">
                            <div class="badge bg-success text-white rounded-pill px-3 py-1 mb-2">Pembayaran Tunai</div>
                            <h6 class="fw-bold text-dark mb-1">Bayar Tunai ke Mitra Teknisi</h6>
                            <p class="text-muted small mb-0">Serahkan uang tunai sebesar <strong>Rp <?= number_format($payableAmount, 0, ',', '.') ?></strong> langsung ke teknisi <?= htmlspecialchars($technician['name'] ?? 'SmartService') ?> saat pekerjaan selesai.</p>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-success font-semibold py-3 rounded-pill w-100 shadow-sm fs-6" onclick="return confirm('Apakah Anda yakin ingin memproses konfirmasi pembayaran ini?');">
                        <i class="bi bi-check-circle-fill me-2"></i> Konfirmasi Pembayaran Sekarang
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>

<style>
.cursor-pointer { cursor: pointer; }
.payment-method-card {
    transition: all 0.2s ease-in-out;
}
.payment-method-card:hover {
    border-color: #3b82f6 !important;
    background-color: #f8fafc;
}
</style>

<script>
function togglePaymentInstructions(method) {
    document.querySelectorAll('.instruction-content').forEach(el => el.classList.add('d-none'));
    const target = document.getElementById('inst-' + method);
    if (target) {
        target.classList.remove('d-none');
    }
}
// Init default selection
document.addEventListener('DOMContentLoaded', () => {
    const checked = document.querySelector('input[name="payment_method"]:checked');
    if (checked) {
        togglePaymentInstructions(checked.value);
    }
});
</script>

