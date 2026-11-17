<?php
$baseUrl = \Config\Env::get('APP_URL', 'http://localhost/Myjasagwuehhhhhhhhhhhhhhhhhhhhh');
?>

<div class="container-fluid py-4">
    <div class="row g-4">

        <!-- Sidebar Navigation Area -->
        <div class="col-lg-3 col-xl-2">
            <?php require __DIR__ . '/../layouts/sidebar.php'; ?>
        </div>

        <!-- Main Dashboard Content Area -->
        <div class="col-lg-9 col-xl-10">

            <!-- Flash Alert -->
            <?php if (!empty($flash)): ?>
                <div class="alert alert-<?= htmlspecialchars($flash['type']) ?> alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    <?= htmlspecialchars($flash['message']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Technician Hero Banner -->
            <div class="card border-0 bg-dark text-white rounded-4 shadow-sm p-4 mb-4">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <span class="badge bg-warning text-dark font-semibold mb-2 px-3 py-1 rounded-pill">
                            <i class="bi bi-tools me-1"></i> Technician Workstation
                        </span>
                        <h2 class="fw-bold mb-1">Dashboard Teknisi: <?= htmlspecialchars($user['name']) ?></h2>
                        <p class="text-white-50 mb-0 small">
                            <i class="bi bi-telephone-fill me-1 text-success"></i> Kontak: <?= htmlspecialchars($user['phone'] ?? '-') ?> |
                            <i class="bi bi-envelope-fill me-1 text-info ms-2"></i> <?= htmlspecialchars($user['email']) ?>
                        </p>
                    </div>
                    <div>
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill font-semibold">
                            <i class="bi bi-check-circle-fill me-1"></i> Status: Siap Bertugas
                        </span>
                    </div>
                </div>
            </div>

            <!-- New Job Notification Alert (when there are assigned jobs pending acceptance) -->
            <?php if ($newJobs > 0): ?>
            <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4 py-3 px-4 d-flex flex-wrap align-items-center justify-content-between gap-3" style="animation: pulseGlow 2s infinite;">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-danger text-white p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; animation: bellRing 1.5s infinite;">
                        <i class="bi bi-bell-fill fs-4"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-danger">🔔 <?= $newJobs ?> Tugas Baru Menunggu Konfirmasi!</h6>
                        <p class="mb-0 small text-dark">Ada pekerjaan baru yang ditugaskan kepada Anda. Segera terima atau tolak untuk memperbarui jadwal.</p>
                    </div>
                </div>
                <a href="<?= $baseUrl ?>/technician/jobs?status=assigned" class="btn btn-danger rounded-pill px-4 fw-bold shadow-sm">
                    <i class="bi bi-eye-fill me-1"></i> Lihat Job Baru
                </a>
            </div>
            <style>
                @keyframes pulseGlow {
                    0%, 100% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.25); }
                    50% { box-shadow: 0 0 20px 5px rgba(220, 53, 69, 0.15); }
                }
                @keyframes bellRing {
                    0%, 100% { transform: rotate(0deg); }
                    10% { transform: rotate(14deg); }
                    20% { transform: rotate(-14deg); }
                    30% { transform: rotate(10deg); }
                    40% { transform: rotate(-8deg); }
                    50% { transform: rotate(4deg); }
                    60%, 100% { transform: rotate(0deg); }
                }
            </style>
            <?php endif; ?>

            <!-- 4 Technician Metric Stat Cards Grid -->
            <div class="row g-3 mb-4">

                <!-- 1. Job Baru -->
                <div class="col-6 col-lg-3">
                    <a href="<?= $baseUrl ?>/technician/jobs?status=assigned" class="text-decoration-none">
                        <div class="stat-card border-start border-4 border-danger shadow-sm rounded-4 bg-white p-3 h-100 transition-hover">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="text-muted small fw-semibold text-uppercase">Job Baru</div>
                                    <div class="fs-2 fw-bold text-danger mb-0"><?= $newJobs ?></div>
                                    <small class="text-danger fw-semibold"><i class="bi bi-bell-fill me-1"></i>Perlu Diterima</small>
                                </div>
                                <div class="stat-icon bg-danger-subtle text-danger p-3 rounded-circle fs-3">
                                    <i class="bi bi-bell-fill"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- 2. Job Diterima -->
                <div class="col-6 col-lg-3">
                    <a href="<?= $baseUrl ?>/technician/jobs?status=accepted" class="text-decoration-none">
                        <div class="stat-card border-start border-4 border-info shadow-sm rounded-4 bg-white p-3 h-100 transition-hover">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="text-muted small fw-semibold text-uppercase">Job Diterima</div>
                                    <div class="fs-2 fw-bold text-info mb-0"><?= $acceptedJobs ?? 0 ?></div>
                                    <small class="text-info fw-semibold"><i class="bi bi-check-circle-fill me-1"></i>Siap Berangkat</small>
                                </div>
                                <div class="stat-icon bg-info-subtle text-info p-3 rounded-circle fs-3">
                                    <i class="bi bi-check-circle-fill"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- 3. Job Sedang Dikerjakan -->
                <div class="col-6 col-lg-3">
                    <a href="<?= $baseUrl ?>/technician/jobs?status=active" class="text-decoration-none">
                        <div class="stat-card border-start border-4 border-primary shadow-sm rounded-4 bg-white p-3 h-100 transition-hover">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="text-muted small fw-semibold text-uppercase">Dalam Proses</div>
                                    <div class="fs-2 fw-bold text-primary mb-0"><?= $activeJobs ?></div>
                                    <small class="text-primary fw-semibold"><i class="bi bi-tools me-1"></i>Pengerjaan</small>
                                </div>
                                <div class="stat-icon bg-primary-subtle text-primary p-3 rounded-circle fs-3">
                                    <i class="bi bi-gear-wide-connected"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- 4. Job Selesai -->
                <div class="col-6 col-lg-3">
                    <a href="<?= $baseUrl ?>/technician/jobs?status=completed" class="text-decoration-none">
                        <div class="stat-card border-start border-4 border-success shadow-sm rounded-4 bg-white p-3 h-100 transition-hover">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="text-muted small fw-semibold text-uppercase">Job Selesai</div>
                                    <div class="fs-2 fw-bold text-success mb-0"><?= $completedJobs ?></div>
                                    <small class="text-success fw-semibold"><i class="bi bi-check-all me-1"></i>Telah Tuntas</small>
                                </div>
                                <div class="stat-icon bg-success-subtle text-success p-3 rounded-circle fs-3">
                                    <i class="bi bi-check-circle-fill"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

            </div>

            <!-- Assigned Repair Jobs Table -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-wrench-adjustable me-2 text-warning"></i> Daftar Tugas Perbaikan
                    </h5>
                    <a href="<?= $baseUrl ?>/technician/jobs" class="btn btn-sm btn-outline-dark rounded-pill">
                        Lihat Semua Job <i class="bi bi-arrow-right me-1"></i>
                    </a>
                </div>

                <?php if (empty($assignedJobs)): ?>
                    <div class="card-body text-center py-5 text-muted">
                        <i class="bi bi-check2-circle fs-1 d-block mb-2 text-success"></i>
                        <p class="mb-0">Saat ini belum ada tugas perbaikan yang ditugaskan kepada Anda.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID Job</th>
                                    <th>Customer & HP</th>
                                    <th>Kategori & Layanan</th>
                                    <th>Alamat Location</th>
                                    <th>Jadwal Kunjungan</th>
                                    <th>Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($assignedJobs as $job): ?>
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
                                            <small class="text-muted d-block text-truncate" style="max-width: 200px;" title="<?= htmlspecialchars($job['address']) ?>">
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
