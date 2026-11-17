<?php
$baseUrl = \Config\Env::get('APP_URL', 'http://localhost/Myjasagwuehhhhhhhhhhhhhhhhhhhhh');

$statusBadge = fn(string $s): string => match($s) {
    'pending'    => '<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2 py-1 small">Pending</span>',
    'assigned'   => '<span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-1 small">Assigned</span>',
    'accepted'   => '<span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2 py-1 small">Accepted</span>',
    'on_the_way' => '<span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2 py-1 small">Perjalanan</span>',
    'repairing'  => '<span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2 py-1 small">Diperbaiki</span>',
    'completed'  => '<span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1 small">Selesai</span>',
    'cancelled'  => '<span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-1 small">Dibatalkan</span>',
    default      => '<span class="badge bg-secondary rounded-pill small">' . strtoupper($s) . '</span>',
};
?>

<div class="container-fluid py-4">
    <div class="row g-4">

        <!-- Sidebar Navigation -->
        <div class="col-lg-3 col-xl-2">
            <?php require __DIR__ . '/../layouts/sidebar.php'; ?>
        </div>

        <!-- Main Content Area -->
        <div class="col-lg-9 col-xl-10">

            <!-- Flash Alert -->
            <?php if (!empty($flash)): ?>
                <div class="alert alert-<?= htmlspecialchars($flash['type']) ?> alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    <?= htmlspecialchars($flash['message']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Page Header -->
            <div class="p-4 mb-4 bg-dark text-white rounded-4 shadow-sm d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div>
                    <div class="badge bg-secondary px-3 py-1 rounded-pill mb-2">
                        <i class="bi bi-clock-history me-1"></i> Riwayat Lengkap
                    </div>
                    <h3 class="fw-bold mb-1">Riwayat Seluruh Layanan Perbaikan</h3>
                    <p class="text-white-50 mb-0 small">Tampilan semua request layanan sistem SmartService AI</p>
                </div>
                <a href="<?= $baseUrl ?>/admin/requests" class="btn btn-outline-light rounded-pill px-4 fw-semibold">
                    <i class="bi bi-clipboard-check me-1"></i> Kelola Permintaan Aktif
                </a>
            </div>

            <!-- Summary Stats -->
            <?php
                $total     = count($requests);
                $completed = count(array_filter($requests, fn($r) => $r['status'] === 'completed'));
                $pending   = count(array_filter($requests, fn($r) => $r['status'] === 'pending'));
                $cancelled = count(array_filter($requests, fn($r) => $r['status'] === 'cancelled'));
                
                // Akumulasi pendapatan dari transaksi pembayaran lunas dan pesanan selesai
                $revenue = 0;
                foreach ($requests as $r) {
                    if (!empty($r['payment']) && $r['payment']['payment_status'] === 'paid') {
                        $revenue += (float)$r['payment']['amount'];
                    } elseif ($r['status'] === 'completed' && $r['final_price'] !== null) {
                        $revenue += (float)$r['final_price'];
                    }
                }
            ?>
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 text-center p-3 border-start border-3 border-primary">
                        <div class="fw-bold fs-2 text-primary"><?= $total ?></div>
                        <div class="text-muted small">Total Request</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 text-center p-3 border-start border-3 border-success">
                        <div class="fw-bold fs-2 text-success"><?= $completed ?></div>
                        <div class="text-muted small">Selesai</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 text-center p-3 border-start border-3 border-warning">
                        <div class="fw-bold fs-2 text-warning"><?= $pending ?></div>
                        <div class="text-muted small">Menunggu (Pending)</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 text-center p-3 border-start border-3 border-info">
                        <div class="fw-bold fs-2 text-info">Rp <?= number_format($revenue, 0, ',', '.') ?></div>
                        <div class="text-muted small">Total Pendapatan</div>
                    </div>
                </div>
            </div>

            <!-- Filter Card -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-funnel me-2 text-primary"></i> Filter Riwayat</h6>
                </div>
                <div class="card-body p-4 pt-0">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold text-muted">Status</label>
                            <select name="status" class="form-select form-select-sm rounded-3">
                                <option value="">Semua Status</option>
                                <?php foreach (['pending','assigned','accepted','on_the_way','repairing','completed','cancelled'] as $s): ?>
                                    <option value="<?= $s ?>" <?= $filterStatus === $s ? 'selected' : '' ?>>
                                        <?= strtoupper(str_replace('_', ' ', $s)) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold text-muted">Kategori</label>
                            <select name="category_id" class="form-select form-select-sm rounded-3">
                                <option value="">Semua Kategori</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= (string)$filterCategory === (string)$cat['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold text-muted">Teknisi</label>
                            <select name="technician_id" class="form-select form-select-sm rounded-3">
                                <option value="">Semua Teknisi</option>
                                <?php foreach ($technicians as $tech): ?>
                                    <option value="<?= $tech['id'] ?>" <?= (string)$filterTechnician === (string)$tech['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($tech['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold text-muted">Tanggal Jadwal</label>
                            <input type="date" name="date" class="form-control form-control-sm rounded-3" value="<?= htmlspecialchars($filterDate) ?>">
                        </div>
                        <div class="col-12 d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 fw-semibold">
                                <i class="bi bi-search me-1"></i> Terapkan Filter
                            </button>
                            <a href="<?= $baseUrl ?>/admin/history" class="btn btn-outline-secondary btn-sm rounded-pill px-4">
                                <i class="bi bi-x-circle me-1"></i> Reset Filter
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Results Table -->
            <?php if (empty($requests)): ?>
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-inbox fs-1 d-block mb-3 opacity-50"></i>
                    <h5 class="fw-semibold">Tidak ada data yang cocok dengan filter</h5>
                    <a href="<?= $baseUrl ?>/admin/history" class="btn btn-outline-secondary rounded-pill mt-2">Reset Filter</a>
                </div>
            <?php else: ?>
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4 fw-semibold text-muted small">#</th>
                                    <th class="fw-semibold text-muted small">Pelanggan</th>
                                    <th class="fw-semibold text-muted small">Layanan</th>
                                    <th class="fw-semibold text-muted small">Teknisi</th>
                                    <th class="fw-semibold text-muted small">Jadwal</th>
                                    <th class="fw-semibold text-muted small">Total</th>
                                    <th class="fw-semibold text-muted small">Status</th>
                                    <th class="fw-semibold text-muted small">Rating</th>
                                    <th class="fw-semibold text-muted small pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($requests as $req): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <span class="badge bg-dark-subtle text-dark border small rounded-pill">#REQ-<?= $req['id'] ?></span>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-dark small"><?= htmlspecialchars($req['customer']['name'] ?? '—') ?></div>
                                            <div class="text-muted" style="font-size: 11px;"><?= htmlspecialchars($req['customer']['phone'] ?? '') ?></div>
                                        </td>
                                        <td>
                                            <div class="small text-dark"><?= htmlspecialchars($req['service']['name'] ?? '—') ?></div>
                                            <div class="text-muted" style="font-size: 11px;"><?= htmlspecialchars($req['category']['name'] ?? '') ?></div>
                                        </td>
                                        <td>
                                            <?php if (!empty($req['technician'])): ?>
                                                <div class="small text-dark"><?= htmlspecialchars($req['technician']['name']) ?></div>
                                            <?php else: ?>
                                                <span class="text-muted small">Belum ditugaskan</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="small text-dark"><?= htmlspecialchars(date('d M Y', strtotime($req['schedule']))) ?></div>
                                            <div class="text-muted" style="font-size: 11px;"><?= date('H:i', strtotime($req['schedule'])) ?></div>
                                        </td>
                                        <td>
                                            <?php if (!empty($req['payment']) && $req['payment']['payment_status'] === 'paid'): ?>
                                                <strong class="text-success small">Rp <?= number_format($req['payment']['amount'], 0, ',', '.') ?></strong>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0 ms-1" style="font-size: 10px;">Lunas</span>
                                            <?php elseif ($req['final_price'] !== null): ?>
                                                <strong class="text-success small">Rp <?= number_format($req['final_price'], 0, ',', '.') ?></strong>
                                            <?php elseif (!empty($req['estimated_price'])): ?>
                                                <span class="text-muted small">~Rp <?= number_format($req['estimated_price'], 0, ',', '.') ?></span>
                                            <?php else: ?>
                                                <span class="text-muted small">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= $statusBadge($req['status']) ?></td>
                                        <td>
                                            <?php if (!empty($req['review'])): ?>
                                                <div class="d-flex align-items-center gap-1">
                                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                                        <i class="bi bi-star-fill" style="font-size: 10px; color: <?= $i <= $req['review']['rating'] ? '#f59e0b' : '#e5e7eb' ?>;"></i>
                                                    <?php endfor; ?>
                                                    <span class="small text-muted ms-1"><?= $req['review']['rating'] ?>/5</span>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted small">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="pe-4">
                                            <a href="<?= $baseUrl ?>/admin/requests/detail?id=<?= $req['id'] ?>"
                                                class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                                <i class="bi bi-eye me-1"></i> Detail
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer bg-white border-0 py-3 px-4 text-muted small d-flex justify-content-between align-items-center">
                        <span>Menampilkan <strong><?= count($requests) ?></strong> dari seluruh riwayat layanan.</span>
                        <?php if ($filterStatus || $filterCategory || $filterTechnician || $filterDate): ?>
                            <a href="<?= $baseUrl ?>/admin/history" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                                <i class="bi bi-x-circle me-1"></i> Hapus Filter
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
