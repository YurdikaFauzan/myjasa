<?php
$baseUrl = \Config\Env::get('APP_URL', 'http://localhost/Myjasagwuehhhhhhhhhhhhhhhhhhhhh');
?>

<div class="container-fluid py-4">
    <div class="row g-4">

        <!-- Sidebar Navigation Area (Responsive Desktop / Mobile) -->
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

            <!-- Header Section -->
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 p-4 bg-dark text-white rounded-4 shadow-sm">
                <div>
                    <div class="badge bg-primary text-white mb-2 px-3 py-1 rounded-pill">
                        <i class="bi bi-shield-lock-fill me-1"></i> Admin Operations Panel
                    </div>
                    <h2 class="fw-bold mb-1">Administrator Dashboard</h2>
                    <p class="text-white-50 mb-0 small">Ringkasan statistik pengguna, katalog layanan, dan status transaksi perbaikan.</p>
                </div>
                <div class="d-flex gap-2">
                    <span class="badge bg-secondary px-3 py-2 rounded-pill font-semibold">
                        <i class="bi bi-person-fill me-1"></i> <?= htmlspecialchars($user['name']) ?>
                    </span>
            </div>

            <!-- AI Engine Live Status Banner -->
            <?php if (!empty($activeAiConfig)): ?>
                <div class="card border-0 shadow-sm rounded-4 p-3 mb-4 bg-white border-start border-4 border-primary">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="p-2 bg-primary-subtle text-primary rounded-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                <i class="bi bi-cpu-fill fs-4"></i>
                            </div>
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <h6 class="fw-bold mb-0 text-dark">AI Chatbot Engine &bull; <?= htmlspecialchars($activeAiConfig['provider_name'] ?? 'AI') ?></h6>
                                    <span class="badge bg-success-subtle text-success border border-success rounded-pill px-2 py-1 small">
                                        <i class="bi bi-check-circle-fill me-1"></i> Aktif Melayani
                                    </span>
                                </div>
                                <div class="text-muted small">
                                    Model Aktif: <code class="fw-semibold text-primary"><?= htmlspecialchars($activeAiConfig['model_id'] ?? 'Default') ?></code>
                                    &bull; Suhu (Temp): <?= htmlspecialchars($activeAiConfig['temperature'] ?? '0.50') ?>
                                    &bull; Maks Token: <?= htmlspecialchars($activeAiConfig['max_tokens'] ?? '650') ?>
                                </div>
                            </div>
                        </div>
                        <div>
                            <a href="<?= $baseUrl ?>/admin/ai-config" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center gap-2">
                                <i class="bi bi-sliders"></i>
                                <span>Konfigurasi Model AI</span>
                                <i class="bi bi-chevron-right small"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- 7 Metric Stat Cards Grid (Bootstrap Responsive Row) -->
            <div class="row g-3 mb-4">

                <!-- 1. Total Customer -->
                <div class="col-6 col-md-4 col-xl-3">
                    <div class="stat-card border-start border-4 border-primary">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-muted small fw-semibold text-uppercase">Total Customer</div>
                                <div class="fs-3 fw-bold text-dark"><?= $totalCustomer ?></div>
                            </div>
                            <div class="stat-icon bg-primary-subtle text-primary">
                                <i class="bi bi-people-fill"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Total Teknisi -->
                <div class="col-6 col-md-4 col-xl-3">
                    <div class="stat-card border-start border-4 border-warning">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-muted small fw-semibold text-uppercase">Total Teknisi</div>
                                <div class="fs-3 fw-bold text-dark"><?= $totalTechnician ?></div>
                            </div>
                            <div class="stat-icon bg-warning-subtle text-warning">
                                <i class="bi bi-tools"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Total Kategori -->
                <div class="col-6 col-md-4 col-xl-3">
                    <div class="stat-card border-start border-4 border-info">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-muted small fw-semibold text-uppercase">Total Kategori</div>
                                <div class="fs-3 fw-bold text-dark"><?= $totalCategories ?></div>
                            </div>
                            <div class="stat-icon bg-info-subtle text-info">
                                <i class="bi bi-tags-fill"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. Total Layanan -->
                <div class="col-6 col-md-4 col-xl-3">
                    <div class="stat-card border-start border-4 border-secondary">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-muted small fw-semibold text-uppercase">Total Layanan</div>
                                <div class="fs-3 fw-bold text-dark"><?= $totalServices ?></div>
                            </div>
                            <div class="stat-icon bg-secondary-subtle text-secondary">
                                <i class="bi bi-gear-wide-connected"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 5. Total Permintaan Jasa -->
                <div class="col-6 col-md-4 col-xl-4">
                    <div class="stat-card border-start border-4 border-dark">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-muted small fw-semibold text-uppercase">Total Permintaan Jasa</div>
                                <div class="fs-3 fw-bold text-dark"><?= $totalRequests ?></div>
                            </div>
                            <div class="stat-icon bg-dark-subtle text-dark">
                                <i class="bi bi-clipboard-data-fill"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 6. Permintaan Pending -->
                <div class="col-6 col-md-4 col-xl-4">
                    <div class="stat-card border-start border-4 border-danger">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-muted small fw-semibold text-uppercase">Permintaan Pending</div>
                                <div class="fs-3 fw-bold text-danger"><?= $pendingRequests ?></div>
                            </div>
                            <div class="stat-icon bg-danger-subtle text-danger">
                                <i class="bi bi-hourglass-split"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 7. Permintaan Selesai -->
                <div class="col-12 col-md-4 col-xl-4">
                    <div class="stat-card border-start border-4 border-success">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-muted small fw-semibold text-uppercase">Permintaan Selesai</div>
                                <div class="fs-3 fw-bold text-success"><?= $completedRequests ?></div>
                            </div>
                            <div class="stat-icon bg-success-subtle text-success">
                                <i class="bi bi-check-circle-fill"></i>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Recent Requests Table -->
            <div id="requests-section" class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-clipboard-check me-2 text-primary"></i> Permintaan Jasa Perbaikan Terbaru</h5>
                    <span class="badge bg-primary rounded-pill"><?= count($recentRequests) ?> Data</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>No. Req</th>
                                <th>Customer</th>
                                <th>Teknisi</th>
                                <th>Layanan</th>
                                <th>Keluhan</th>
                                <th>Jadwal</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentRequests as $req): ?>
                                <?php
                                    $cust = $requestModel->customer((int)$req['customer_id']);
                                    $tech = $requestModel->technician($req['technician_id'] ? (int)$req['technician_id'] : null);
                                    $serv = $requestModel->service((int)$req['service_id']);
                                ?>
                                <tr>
                                    <td class="fw-bold">#REQ-<?= $req['id'] ?></td>
                                    <td><?= htmlspecialchars($cust['name'] ?? 'N/A') ?></td>
                                    <td>
                                        <?php if ($tech): ?>
                                            <span class="badge bg-warning-subtle text-dark border border-warning-subtle rounded-pill">
                                                <i class="bi bi-tools me-1"></i> <?= htmlspecialchars($tech['name']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary-subtle text-secondary rounded-pill">Belum Ditugaskan</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($serv['name'] ?? 'N/A') ?></td>
                                    <td><small class="text-muted"><?= htmlspecialchars($req['complaint']) ?></small></td>
                                    <td><small><?= htmlspecialchars($req['schedule']) ?></small></td>
                                    <td>
                                        <span class="badge <?= match($req['status']) { 'completed'=>'bg-success', 'repairing'=>'bg-warning text-dark', 'pending'=>'bg-danger', default=>'bg-info' } ?> rounded-pill">
                                            <?= htmlspecialchars(strtoupper($req['status'])) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Users Table -->
            <div id="users-section" class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-people me-2 text-info"></i> Daftar Pengguna Terbaru</h5>
                    <a href="<?= $baseUrl ?>/admin/users" class="btn btn-sm btn-outline-primary rounded-pill px-3 font-semibold">
                        <i class="bi bi-person-plus me-1"></i> Kelola & Daftarkan Akun
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Nama Pengguna</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Telepon</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentUsers as $u): ?>
                                <tr>
                                    <td>#<?= $u['id'] ?></td>
                                    <td class="fw-bold"><?= htmlspecialchars($u['name']) ?></td>
                                    <td><?= htmlspecialchars($u['email']) ?></td>
                                    <td>
                                        <span class="badge <?= match($u['role']) { 'admin'=>'bg-dark', 'customer'=>'bg-primary', 'technician'=>'bg-warning text-dark', default=>'bg-secondary' } ?> rounded-pill">
                                            <?= htmlspecialchars(strtoupper($u['role'])) ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($u['phone'] ?? '-') ?></td>
                                    <td>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">
                                            <?= htmlspecialchars($u['status']) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
