<?php
$baseUrl = \Config\Env::get('APP_URL', 'http://localhost/Myjasagwuehhhhhhhhhhhhhhhhhhhhh');

$statusBadge = fn(string $s): string => match($s) {
    'assigned'   => '<span class="badge bg-primary rounded-pill">Assigned</span>',
    'accepted'   => '<span class="badge bg-info text-dark rounded-pill">Accepted</span>',
    'on_the_way' => '<span class="badge bg-warning text-dark rounded-pill">Dalam Perjalanan</span>',
    'repairing'  => '<span class="badge bg-warning text-dark rounded-pill">Sedang Dikerjakan</span>',
    'completed'  => '<span class="badge bg-success rounded-pill">Selesai</span>',
    'cancelled'  => '<span class="badge bg-danger rounded-pill">Dibatalkan</span>',
    default      => '<span class="badge bg-secondary rounded-pill">' . strtoupper($s) . '</span>',
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
            <div class="p-4 mb-4 rounded-4 shadow-sm d-flex align-items-center justify-content-between flex-wrap gap-3"
                style="background: linear-gradient(135deg, #1a1a2e, #16213e);">
                <div class="text-white">
                    <div class="badge bg-warning text-dark px-3 py-1 rounded-pill mb-2">
                        <i class="bi bi-clock-history me-1"></i> Riwayat Pekerjaan
                    </div>
                    <h3 class="fw-bold mb-1">Riwayat Semua Pekerjaan Saya</h3>
                    <p class="text-white-50 mb-0 small">Seluruh pekerjaan yang pernah ditugaskan kepada Anda</p>
                </div>
                <a href="<?= $baseUrl ?>/technician/jobs" class="btn btn-outline-light rounded-pill px-4 fw-semibold">
                    <i class="bi bi-list-task me-1"></i> Lihat Semua Job Aktif
                </a>
            </div>

            <!-- Statistics Summary Row -->
            <?php
                $allJobsForStats = $jobs;
                $totalJobs       = count($allJobsForStats);
                $completedCount  = count(array_filter($jobs, fn($j) => $j['status'] === 'completed'));
                $activeCount     = count(array_filter($jobs, fn($j) => in_array($j['status'], ['accepted', 'on_the_way', 'repairing'])));
                $totalRevenue    = array_sum(array_column(array_filter($jobs, fn($j) => $j['status'] === 'completed'), 'final_price'));
                $avgRating       = null;
                $reviewedJobs    = array_filter($jobs, fn($j) => !empty($j['review']));
                if (!empty($reviewedJobs)) {
                    $avgRating = array_sum(array_map(fn($j) => $j['review']['rating'], $reviewedJobs)) / count($reviewedJobs);
                }
            ?>
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 text-center p-3">
                        <div class="fw-bold fs-2 text-primary"><?= $totalJobs ?></div>
                        <div class="text-muted small">Total Pekerjaan</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 text-center p-3">
                        <div class="fw-bold fs-2 text-success"><?= $completedCount ?></div>
                        <div class="text-muted small">Berhasil Selesai</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 text-center p-3">
                        <div class="fw-bold fs-2 text-warning"><?= $activeCount ?></div>
                        <div class="text-muted small">Masih Aktif</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 text-center p-3">
                        <?php if ($avgRating !== null): ?>
                            <div class="fw-bold fs-2 text-warning"><?= number_format($avgRating, 1) ?> <i class="bi bi-star-fill fs-5"></i></div>
                        <?php else: ?>
                            <div class="fw-bold fs-2 text-muted">—</div>
                        <?php endif; ?>
                        <div class="text-muted small">Rating Rata-Rata</div>
                    </div>
                </div>
            </div>

            <!-- Filter Tabs -->
            <?php $filters = [
                ''           => ['label' => 'Semua', 'icon' => 'bi-list-ul'],
                'active'     => ['label' => 'Aktif', 'icon' => 'bi-gear-wide-connected'],
                'completed'  => ['label' => 'Selesai', 'icon' => 'bi-check-all'],
                'assigned'   => ['label' => 'Assigned', 'icon' => 'bi-bell'],
                'cancelled'  => ['label' => 'Dibatalkan', 'icon' => 'bi-x-circle'],
            ]; ?>
            <div class="d-flex flex-wrap gap-2 mb-4">
                <?php foreach ($filters as $val => $meta): ?>
                    <a href="?status=<?= $val ?>"
                        class="btn btn-sm rounded-pill px-3 <?= ($statusFilter === $val) ? 'btn-dark fw-bold shadow-sm' : 'btn-outline-secondary' ?>">
                        <i class="<?= $meta['icon'] ?> me-1"></i> <?= $meta['label'] ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Jobs List -->
            <?php if (empty($jobs)): ?>
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-inbox fs-1 d-block mb-3 opacity-50"></i>
                    <h5 class="fw-semibold">Tidak ada data pekerjaan ditemukan</h5>
                    <p class="small">Coba ubah filter atau periksa kembali daftar job aktif.</p>
                    <a href="<?= $baseUrl ?>/technician/jobs?status=assigned" class="btn btn-warning rounded-pill px-4 text-dark fw-bold mt-2">
                        <i class="bi bi-bell me-1"></i> Lihat Job Baru
                    </a>
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
                                    <th class="fw-semibold text-muted small">Jadwal</th>
                                    <th class="fw-semibold text-muted small">Total Biaya</th>
                                    <th class="fw-semibold text-muted small">Status</th>
                                    <th class="fw-semibold text-muted small">Rating</th>
                                    <th class="fw-semibold text-muted small pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($jobs as $job): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <span class="badge bg-dark-subtle text-dark border small rounded-pill">#JOB-<?= $job['id'] ?></span>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-dark small"><?= htmlspecialchars($job['customer']['name'] ?? '—') ?></div>
                                            <div class="text-muted" style="font-size: 11px;"><?= htmlspecialchars($job['customer']['phone'] ?? '') ?></div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-dark small"><?= htmlspecialchars($job['service']['name'] ?? '—') ?></div>
                                            <div class="text-muted" style="font-size: 11px;"><?= htmlspecialchars($job['category']['name'] ?? '') ?></div>
                                        </td>
                                        <td>
                                            <div class="small text-dark"><?= htmlspecialchars(date('d M Y', strtotime($job['schedule']))) ?></div>
                                            <div class="text-muted" style="font-size: 11px;"><?= htmlspecialchars(date('H:i', strtotime($job['schedule']))) ?></div>
                                        </td>
                                        <td>
                                            <?php if ($job['final_price'] !== null): ?>
                                                <strong class="text-success">Rp <?= number_format($job['final_price'], 0, ',', '.') ?></strong>
                                            <?php else: ?>
                                                <span class="text-muted small">~Rp <?= number_format($job['estimated_price'] ?? 0, 0, ',', '.') ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= $statusBadge($job['status']) ?></td>
                                        <td>
                                            <?php if (!empty($job['review'])): ?>
                                                <div class="d-flex align-items-center gap-1">
                                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                                        <i class="bi bi-star-fill" style="font-size: 11px; color: <?= $i <= $job['review']['rating'] ? '#f59e0b' : '#e5e7eb' ?>;"></i>
                                                    <?php endfor; ?>
                                                    <span class="small text-muted ms-1"><?= $job['review']['rating'] ?></span>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted small">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="pe-4">
                                            <a href="<?= $baseUrl ?>/technician/jobs/detail?id=<?= $job['id'] ?>"
                                                class="btn btn-sm btn-outline-warning rounded-pill px-3 text-dark">
                                                <i class="bi bi-eye me-1"></i> Detail
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer bg-white border-0 py-3 px-4 text-muted small">
                        Menampilkan <strong><?= count($jobs) ?></strong> pekerjaan<?= $statusFilter ? ' (filter: <strong>' . strtoupper($statusFilter) . '</strong>)' : '' ?>.
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
