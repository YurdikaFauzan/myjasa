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
            <div class="card border-0 bg-dark text-white rounded-4 shadow-sm p-4 mb-4">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <div class="badge bg-warning text-dark font-semibold mb-2 px-3 py-1 rounded-pill">
                            <i class="bi bi-tools me-1"></i> Modul Teknisi
                        </div>
                        <h3 class="fw-bold mb-1">Daftar Pekerjaan Perbaikan Ditugaskan</h3>
                        <p class="text-white-50 mb-0 small">Kelola dan update status pekerjaan perbaikan lokasi pelanggan Anda.</p>
                    </div>
                    <div>
                        <a href="<?= $baseUrl ?>/technician/dashboard" class="btn btn-outline-light rounded-pill px-3">
                            <i class="bi bi-speedometer2 me-1"></i> Kembali ke Dashboard
                        </a>
                    </div>
                </div>
            </div>

            <!-- Filter Status Navigation Tabs -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-3">
                    <ul class="nav nav-pills gap-2 flex-wrap">
                        <li class="nav-item">
                            <a class="nav-link rounded-pill px-4 font-semibold <?= empty($statusFilter) ? 'active bg-primary' : 'text-dark bg-light' ?>" href="<?= $baseUrl ?>/technician/jobs">
                                <i class="bi bi-grid-fill me-1"></i> Semua Job
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link rounded-pill px-4 font-semibold <?= ($statusFilter === 'assigned') ? 'active bg-danger' : 'text-dark bg-light' ?>" href="<?= $baseUrl ?>/technician/jobs?status=assigned">
                                <i class="bi bi-bell-fill me-1"></i> Job Baru (Perlu Diterima)
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link rounded-pill px-4 font-semibold <?= ($statusFilter === 'accepted') ? 'active bg-info text-dark' : 'text-dark bg-light' ?>" href="<?= $baseUrl ?>/technician/jobs?status=accepted">
                                <i class="bi bi-check-circle-fill me-1"></i> Diterima
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link rounded-pill px-4 font-semibold <?= ($statusFilter === 'active') ? 'active bg-warning text-dark' : 'text-dark bg-light' ?>" href="<?= $baseUrl ?>/technician/jobs?status=active">
                                <i class="bi bi-gear-wide-connected me-1"></i> Dalam Pengerjaan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link rounded-pill px-4 font-semibold <?= ($statusFilter === 'completed') ? 'active bg-success' : 'text-dark bg-light' ?>" href="<?= $baseUrl ?>/technician/jobs?status=completed">
                                <i class="bi bi-check-circle-fill me-1"></i> Job Selesai
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Jobs List Table Card -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-list-task text-primary me-2"></i> Daftar Tugas (<?= count($jobs) ?> Data)
                    </h5>
                </div>

                <?php if (empty($jobs)): ?>
                    <div class="card-body text-center py-5 text-muted">
                        <i class="bi bi-clipboard-x fs-1 d-block mb-2 text-secondary"></i>
                        <p class="mb-0">Tidak ada daftar tugas perbaikan yang sesuai dengan filter ini.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID Job</th>
                                    <th>Customer & Telepon</th>
                                    <th>Kategori & Layanan</th>
                                    <th>Keluhan</th>
                                    <th>Alamat Kunjungan</th>
                                    <th>Jadwal</th>
                                    <th>Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($jobs as $job): ?>
                                    <?php
                                        $cust = $job['customer'];
                                        $serv = $job['service'];
                                        $cat  = $job['category'];
                                    ?>
                                    <tr>
                                        <td class="fw-bold">#JOB-<?= $job['id'] ?></td>
                                        <td>
                                            <div class="fw-semibold text-dark"><?= htmlspecialchars($cust['name'] ?? 'Pelanggan') ?></div>
                                            <small class="text-muted"><i class="bi bi-telephone me-1 text-success"></i><?= htmlspecialchars($cust['phone'] ?? '-') ?></small>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary-subtle text-secondary rounded-pill mb-1"><?= htmlspecialchars($cat['name'] ?? 'Kategori') ?></span>
                                            <div class="fw-semibold text-dark small"><?= htmlspecialchars($serv['name'] ?? 'Layanan') ?></div>
                                        </td>
                                        <td>
                                            <small class="text-muted text-truncate d-block" style="max-width: 180px;" title="<?= htmlspecialchars($job['complaint']) ?>">
                                                <?= htmlspecialchars($job['complaint']) ?>
                                            </small>
                                        </td>
                                        <td>
                                            <small class="text-muted text-truncate d-block" style="max-width: 180px;" title="<?= htmlspecialchars($job['address']) ?>">
                                                <i class="bi bi-geo-alt me-1 text-danger"></i><?= htmlspecialchars($job['address']) ?>
                                            </small>
                                        </td>
                                        <td>
                                            <small class="fw-semibold text-dark"><i class="bi bi-calendar-event me-1 text-primary"></i><?= htmlspecialchars($job['schedule']) ?></small>
                                        </td>
                                        <td>
                                            <?php if (empty($job['technician_id']) && $job['status'] === 'pending'): ?>
                                                <span class="badge bg-warning text-dark rounded-pill px-3 py-1">
                                                    <i class="bi bi-broadcast me-1"></i> TERSEDIA
                                                </span>
                                            <?php else: ?>
                                                <span class="badge <?= match($job['status']) { 
                                                    'completed'  => 'bg-success', 
                                                    'repairing'  => 'bg-warning text-dark', 
                                                    'on_the_way' => 'bg-info text-dark', 
                                                    'accepted'   => 'bg-primary', 
                                                    'assigned'   => 'bg-danger', 
                                                    default      => 'bg-secondary' 
                                                } ?> rounded-pill px-3 py-1">
                                                    <?= htmlspecialchars(strtoupper(str_replace('_', ' ', $job['status']))) ?>
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-1">
                                                <?php if ($job['status'] === 'assigned' || (empty($job['technician_id']) && $job['status'] === 'pending')): ?>
                                                    <form action="<?= $baseUrl ?>/technician/jobs/accept" method="POST" class="d-inline">
                                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                                        <input type="hidden" name="job_id" value="<?= $job['id'] ?>">
                                                        <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 shadow-sm fw-semibold" onclick="return confirm('Apakah Anda yakin ingin MENERIMA tugas perbaikan ini?');">
                                                            <i class="bi bi-check-circle-fill me-1"></i> Terima
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                                <a href="<?= $baseUrl ?>/technician/jobs/detail?id=<?= $job['id'] ?>" class="btn btn-sm btn-primary rounded-pill px-3">
                                                    <i class="bi bi-eye-fill me-1"></i> Detail
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
    </div>
</div>
