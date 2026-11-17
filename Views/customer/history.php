<?php
$baseUrl = \Config\Env::get('APP_URL', 'http://localhost/Myjasagwuehhhhhhhhhhhhhhhhhhhhh');

$statusBadge = function(string $status): string {
    return match($status) {
        'pending'    => '<span class="badge bg-secondary rounded-pill">Pending</span>',
        'assigned'   => '<span class="badge bg-primary rounded-pill">Assigned</span>',
        'accepted'   => '<span class="badge bg-info text-dark rounded-pill">Accepted</span>',
        'on_the_way' => '<span class="badge bg-warning text-dark rounded-pill">Dalam Perjalanan</span>',
        'repairing'  => '<span class="badge bg-warning text-dark rounded-pill">Diperbaiki</span>',
        'completed'  => '<span class="badge bg-success rounded-pill">Selesai</span>',
        'cancelled'  => '<span class="badge bg-danger rounded-pill">Dibatalkan</span>',
        default      => '<span class="badge bg-secondary rounded-pill">' . strtoupper($status) . '</span>',
    };
};
?>

<div class="container-fluid px-lg-5 py-4">

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
                        <i class="bi bi-clock-history me-1"></i> Riwayat Pesanan
                    </div>
                    <h3 class="fw-bold mb-1">Riwayat Layanan Perbaikan Saya</h3>
                    <p class="text-white-50 mb-0 small">Semua pesanan yang pernah Anda buat di SmartService AI</p>
                </div>
                <a href="<?= $baseUrl ?>/customer/requests/create" class="btn btn-primary rounded-pill px-4 fw-semibold">
                    <i class="bi bi-plus-circle me-1"></i> Pesan Layanan Baru
                </a>
            </div>

            <!-- Filter Tabs -->
            <?php
                $filters = [
                    ''          => ['label' => 'Semua Pesanan', 'icon' => 'bi-list-ul'],
                    'pending'   => ['label' => 'Pending', 'icon' => 'bi-hourglass'],
                    'assigned'  => ['label' => 'Assigned', 'icon' => 'bi-person-check'],
                    'repairing' => ['label' => 'Diperbaiki', 'icon' => 'bi-tools'],
                    'completed' => ['label' => 'Selesai', 'icon' => 'bi-check-all'],
                    'cancelled' => ['label' => 'Dibatalkan', 'icon' => 'bi-x-circle'],
                ];
            ?>
            <div class="d-flex flex-wrap gap-2 mb-4">
                <?php foreach ($filters as $filterVal => $filterMeta): ?>
                    <a href="?status=<?= $filterVal ?>"
                        class="btn btn-sm rounded-pill px-3 <?= ($statusFilter === $filterVal) ? 'btn-dark fw-bold shadow-sm' : 'btn-outline-secondary' ?>">
                        <i class="<?= $filterMeta['icon'] ?> me-1"></i> <?= $filterMeta['label'] ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Pesanan Table / Cards -->
            <?php if (empty($orders)): ?>
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-inbox fs-1 d-block mb-3 opacity-50"></i>
                    <h5 class="fw-semibold">Belum Ada Riwayat Pesanan</h5>
                    <p class="small">Pesanan Anda yang sudah selesai atau dibatalkan akan tampil di sini.</p>
                    <a href="<?= $baseUrl ?>/customer/requests/create" class="btn btn-primary rounded-pill px-4 mt-2">
                        <i class="bi bi-plus-circle me-1"></i> Buat Pesanan Pertama
                    </a>
                </div>
            <?php else: ?>
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4 fw-semibold text-muted small">#</th>
                                    <th class="fw-semibold text-muted small">Layanan</th>
                                    <th class="fw-semibold text-muted small">Jadwal</th>
                                    <th class="fw-semibold text-muted small">Teknisi</th>
                                    <th class="fw-semibold text-muted small">Total Biaya</th>
                                    <th class="fw-semibold text-muted small">Status</th>
                                    <th class="fw-semibold text-muted small">Ulasan</th>
                                    <th class="fw-semibold text-muted small pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <span class="badge bg-dark-subtle text-dark border small rounded-pill">#REQ-<?= $order['id'] ?></span>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-dark small"><?= htmlspecialchars($order['service']['name'] ?? 'Perbaikan') ?></div>
                                            <div class="text-muted" style="font-size: 11px;"><?= htmlspecialchars($order['category']['name'] ?? '-') ?></div>
                                        </td>
                                        <td>
                                            <div class="text-dark small"><i class="bi bi-calendar3 text-primary me-1"></i><?= htmlspecialchars(date('d M Y', strtotime($order['schedule']))) ?></div>
                                            <div class="text-muted" style="font-size: 11px;"><i class="bi bi-clock me-1"></i><?= htmlspecialchars(date('H:i', strtotime($order['schedule']))) ?></div>
                                        </td>
                                        <td>
                                            <?php if (!empty($order['technician'])): ?>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="bg-warning-subtle rounded-circle d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; font-size: 12px;">
                                                        <i class="bi bi-person-fill text-warning"></i>
                                                    </div>
                                                    <span class="small text-dark"><?= htmlspecialchars($order['technician']['name']) ?></span>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted small">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($order['final_price'] !== null): ?>
                                                <strong class="text-success">Rp <?= number_format($order['final_price'], 0, ',', '.') ?></strong>
                                            <?php elseif ($order['estimated_price']): ?>
                                                <span class="text-muted small">~Rp <?= number_format($order['estimated_price'], 0, ',', '.') ?></span>
                                            <?php else: ?>
                                                <span class="text-muted small">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= $statusBadge($order['status']) ?></td>
                                        <td>
                                            <?php if ($order['status'] === 'completed'): ?>
                                                <?php if (!empty($order['review'])): ?>
                                                    <div class="d-flex align-items-center gap-1">
                                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                                            <i class="bi bi-star-fill" style="font-size: 11px; color: <?= $i <= $order['review']['rating'] ? '#f59e0b' : '#e5e7eb' ?>;"></i>
                                                        <?php endfor; ?>
                                                        <span class="small text-muted ms-1"><?= $order['review']['rating'] ?>/5</span>
                                                    </div>
                                                <?php else: ?>
                                                    <a href="<?= $baseUrl ?>/customer/requests/detail?id=<?= $order['id'] ?>" class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill text-decoration-none small">
                                                        <i class="bi bi-star me-1"></i> Beri Ulasan
                                                    </a>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-muted small">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="pe-4">
                                            <a href="<?= $baseUrl ?>/customer/requests/detail?id=<?= $order['id'] ?>"
                                                class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                                <i class="bi bi-eye me-1"></i> Detail
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer bg-white border-0 py-3 px-4 text-muted small">
                        Menampilkan <strong><?= count($orders) ?></strong> pesanan<?= $statusFilter ? ' dengan filter: <strong>' . strtoupper($statusFilter) . '</strong>' : '' ?>.
                    </div>
                </div>
            <?php endif; ?>
</div>
