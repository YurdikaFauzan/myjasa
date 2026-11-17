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

            <!-- Page Header -->
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 p-4 bg-white rounded-4 shadow-sm border">
                <div>
                    <h3 class="fw-bold text-dark mb-1"><i class="bi bi-receipt text-primary me-2"></i> Pesanan Saya</h3>
                    <p class="text-muted small mb-0">Daftar riwayat dan status pemesanan perbaikan peralatan rumah tangga Anda</p>
                </div>
                <div>
                    <a href="<?= $baseUrl ?>/customer/requests/create" class="btn btn-primary rounded-pill font-semibold px-4 shadow-sm">
                        <i class="bi bi-plus-lg me-1"></i> Buat Pesanan Baru
                    </a>
                </div>
            </div>

            <!-- Orders Table -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark">Riwayat Pemesanan Jasa</h5>
                    <span class="badge bg-primary-subtle text-primary font-semibold rounded-pill px-3 py-2"><?= count($myRequests) ?> Total Pesanan</span>
                </div>

                <?php if (empty($myRequests)): ?>
                    <div class="card-body text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-1 d-block mb-3 text-secondary"></i>
                        <h6 class="fw-bold">Belum Ada Pesanan</h6>
                        <p class="small text-muted mb-4">Anda belum melakukan pemesanan jasa perbaikan peralatan rumah tangga.</p>
                        <a href="<?= $baseUrl ?>/customer/requests/create" class="btn btn-primary rounded-pill px-4 font-semibold shadow-sm">
                            <i class="bi bi-tools me-1"></i> Pesan Jasa Sekarang
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID Pesanan</th>
                                    <th>Kategori</th>
                                    <th>Layanan Jasa</th>
                                    <th>Teknisi Bertugas</th>
                                    <th>Jadwal Kunjungan</th>
                                    <th>Status</th>
                                    <th>Estimasi Biaya</th>
                                    <th>Biaya Final</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($myRequests as $req): ?>
                                    <tr>
                                        <td class="fw-bold text-primary">#REQ-<?= $req['id'] ?></td>
                                        <td>
                                            <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill">
                                                <i class="bi bi-tag me-1"></i><?= htmlspecialchars($req['category']['name'] ?? 'Peralatan') ?>
                                            </span>
                                        </td>
                                        <td class="fw-semibold text-dark">
                                            <?= htmlspecialchars($req['service']['name'] ?? 'Perbaikan') ?>
                                            <?php if (!empty($req['complaint_photo'])): ?>
                                                <i class="bi bi-camera-fill text-primary ms-1" title="Foto Kerusakan Terlampir"></i>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($req['technician'])): ?>
                                                <span class="badge bg-warning-subtle text-dark border border-warning-subtle rounded-pill">
                                                    <i class="bi bi-person-wrench me-1"></i> <?= htmlspecialchars($req['technician']['name']) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary-subtle text-secondary rounded-pill">Menunggu Teknisi</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><small class="text-muted"><i class="bi bi-calendar-event me-1"></i><?= htmlspecialchars($req['schedule']) ?></small></td>
                                        <td>
                                            <span class="badge <?= match($req['status']) { 'completed'=>'bg-success', 'repairing'=>'bg-warning text-dark', 'pending'=>'bg-secondary', 'cancelled'=>'bg-danger', default=>'bg-info' } ?> rounded-pill">
                                                <?= htmlspecialchars(strtoupper($req['status'])) ?>
                                            </span>
                                        </td>
                                        <td><small class="fw-semibold">Rp <?= number_format($req['estimated_price'], 0, ',', '.') ?></small></td>
                                        <td>
                                            <?php if ($req['final_price'] !== null): ?>
                                                <strong class="text-success">Rp <?= number_format($req['final_price'], 0, ',', '.') ?></strong>
                                            <?php else: ?>
                                                <span class="text-muted small">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end">
                                            <div class="d-inline-flex gap-1 align-items-center">
                                                <?php 
                                                    $isPaid = (!empty($req['payment']) && $req['payment']['payment_status'] === 'paid'); 
                                                ?>
                                                <?php if (!$isPaid && $req['status'] !== 'cancelled'): ?>
                                                    <a href="<?= $baseUrl ?>/customer/requests/pay?id=<?= $req['id'] ?>" class="btn btn-sm btn-success rounded-pill font-semibold px-3" title="Bayar Sekarang">
                                                        <i class="bi bi-wallet2 me-1"></i> Bayar
                                                    </a>
                                                <?php elseif ($isPaid): ?>
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1 small">
                                                        <i class="bi bi-check-circle-fill me-1"></i> Lunas
                                                    </span>
                                                <?php endif; ?>
                                                <a href="<?= $baseUrl ?>/customer/requests/detail?id=<?= $req['id'] ?>" class="btn btn-sm btn-outline-primary rounded-pill font-semibold px-3">
                                                    <i class="bi bi-eye me-1"></i> Detail
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

</div>
