<?php
$baseUrl = \Config\Env::get('APP_URL', 'http://localhost/Myjasagwuehhhhhhhhhhhhhhhhhhhhh');
$user = \Middleware\AuthMiddleware::user();
$role = $user['role'] ?? 'guest';
$requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
?>

<div class="sidebar-custom p-3">
    <div class="mb-4 text-center text-lg-start px-2 pt-2">
        <span class="badge <?= match($role) { 'admin'=>'bg-dark', 'customer'=>'bg-primary', 'technician'=>'bg-warning text-dark', default=>'bg-secondary' } ?> px-3 py-2 rounded-pill font-semibold">
            <i class="bi bi-person-badge me-1"></i> Peran: <?= strtoupper($role) ?>
        </span>
    </div>

    <ul class="nav flex-column">
        <!-- Menu Navigasi Admin -->
        <?php if ($role === 'admin'): ?>
            <li class="nav-item">
                <a class="nav-link <?= str_contains($requestUri, '/admin/dashboard') ? 'active' : '' ?>" href="<?= $baseUrl ?>/admin/dashboard">
                    <i class="bi bi-grid-1x2-fill me-2 text-primary"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= str_contains($requestUri, '/admin/users') ? 'active' : '' ?>" href="<?= $baseUrl ?>/admin/users">
                    <i class="bi bi-people-fill me-2 text-info"></i> Kelola Pengguna & Akun
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= str_contains($requestUri, '/admin/categories') ? 'active' : '' ?>" href="<?= $baseUrl ?>/admin/categories">
                    <i class="bi bi-tags-fill me-2 text-info"></i> Kelola Kategori
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= str_contains($requestUri, '/admin/services') ? 'active' : '' ?>" href="<?= $baseUrl ?>/admin/services">
                    <i class="bi bi-tools me-2 text-warning"></i> Kelola Layanan
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= str_contains($requestUri, '/admin/requests') ? 'active' : '' ?>" href="<?= $baseUrl ?>/admin/requests">
                    <i class="bi bi-clipboard-check-fill me-2 text-success"></i> Permintaan Jasa
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= str_contains($requestUri, '/admin/history') ? 'active' : '' ?>" href="<?= $baseUrl ?>/admin/history">
                    <i class="bi bi-clock-history me-2 text-secondary"></i> Riwayat Seluruh Layanan
                </a>
            </li>
            <li class="nav-item mt-3">
                <div class="text-uppercase text-muted px-3 py-1 font-semibold" style="font-size: 0.72rem; letter-spacing: 0.8px;">
                    Pengaturan Sistem
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= str_contains($requestUri, '/admin/ai-config') ? 'active' : '' ?>" href="<?= $baseUrl ?>/admin/ai-config">
                    <i class="bi bi-cpu-fill me-2 text-primary"></i> Konfigurasi Model AI
                </a>
            </li>

        <!-- Menu Navigasi Customer -->
        <?php elseif ($role === 'customer'): ?>
            <li class="nav-item">
                <a class="nav-link <?= str_contains($requestUri, '/customer/dashboard') ? 'active' : '' ?>" href="<?= $baseUrl ?>/customer/dashboard">
                    <i class="bi bi-grid-1x2-fill me-2 text-primary"></i> Dashboard Saya
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= str_contains($requestUri, '/customer/requests/create') ? 'active' : '' ?>" href="<?= $baseUrl ?>/customer/requests/create">
                    <i class="bi bi-plus-circle-fill me-2 text-success"></i> Pesan Jasa Perbaikan
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($requestUri === '/customer/requests' && empty($_GET['status'])) ? 'active' : '' ?>" href="<?= $baseUrl ?>/customer/requests">
                    <i class="bi bi-list-check me-2 text-primary"></i> Pesanan Aktif
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= str_contains($requestUri, '/customer/history') ? 'active' : '' ?>" href="<?= $baseUrl ?>/customer/history">
                    <i class="bi bi-clock-history me-2 text-secondary"></i> Riwayat Pesanan
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= str_contains($requestUri, '/customer/ai-consultation') ? 'active' : '' ?>" href="<?= $baseUrl ?>/customer/ai-consultation">
                    <i class="bi bi-robot me-2 text-info"></i> Konsultasi AI Kerusakan
                </a>
            </li>
            <li class="nav-item mt-2">
                <div class="text-uppercase text-muted px-3 py-1 font-semibold" style="font-size: 0.72rem; letter-spacing: 0.8px;">
                    Pengaturan AI
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= str_contains($requestUri, '/admin/ai-config') ? 'active' : '' ?>" href="<?= $baseUrl ?>/admin/ai-config">
                    <i class="bi bi-cpu-fill me-2 text-primary"></i> Konfigurasi Model AI
                </a>
            </li>

        <!-- Menu Navigasi Teknisi -->
        <?php elseif ($role === 'technician'): ?>
            <?php
                // Fetch badge counts for technician navigation
                $techReqModel = new \Models\ServiceRequest();
                $allTechJobs = $techReqModel->where('technician_id', (int)($user['id'] ?? 0));
                $openUnassigned = array_filter(
                    $techReqModel->where('status', \Models\ServiceRequest::STATUS_PENDING),
                    fn($j) => empty($j['technician_id'])
                );
                $sbNewJobsCount = count(array_filter($allTechJobs, fn($j) => $j['status'] === \Models\ServiceRequest::STATUS_ASSIGNED)) + count($openUnassigned);
                $sbAcceptedJobsCount = count(array_filter($allTechJobs, fn($j) => $j['status'] === \Models\ServiceRequest::STATUS_ACCEPTED));
                $sbActiveJobsCount = count(array_filter($allTechJobs, fn($j) => in_array($j['status'], [
                    \Models\ServiceRequest::STATUS_ACCEPTED,
                    \Models\ServiceRequest::STATUS_ON_THE_WAY,
                    \Models\ServiceRequest::STATUS_REPAIRING
                ])));
            ?>
            <li class="nav-item">
                <a class="nav-link <?= str_contains($requestUri, '/technician/dashboard') ? 'active' : '' ?>" href="<?= $baseUrl ?>/technician/dashboard">
                    <i class="bi bi-grid-1x2-fill me-2 text-warning"></i> Dashboard Tugas
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex justify-content-between align-items-center <?= ($requestUri === '/technician/jobs' && ($_GET['status'] ?? '') === 'assigned') ? 'active' : '' ?>" href="<?= $baseUrl ?>/technician/jobs?status=assigned">
                    <span><i class="bi bi-bell-fill me-2 text-danger"></i> Job Baru</span>
                    <?php if ($sbNewJobsCount > 0): ?>
                        <span class="badge bg-danger rounded-pill px-2"><?= $sbNewJobsCount ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex justify-content-between align-items-center <?= ($requestUri === '/technician/jobs' && ($_GET['status'] ?? '') === 'accepted') ? 'active' : '' ?>" href="<?= $baseUrl ?>/technician/jobs?status=accepted">
                    <span><i class="bi bi-check-circle-fill me-2 text-info"></i> Job Diterima</span>
                    <?php if ($sbAcceptedJobsCount > 0): ?>
                        <span class="badge bg-info text-dark rounded-pill px-2"><?= $sbAcceptedJobsCount ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex justify-content-between align-items-center <?= ($requestUri === '/technician/jobs' && ($_GET['status'] ?? '') === 'active') ? 'active' : '' ?>" href="<?= $baseUrl ?>/technician/jobs?status=active">
                    <span><i class="bi bi-gear-wide-connected me-2 text-primary"></i> Sedang Dikerjakan</span>
                    <?php if ($sbActiveJobsCount > 0): ?>
                        <span class="badge bg-primary rounded-pill px-2"><?= $sbActiveJobsCount ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($requestUri === '/technician/jobs' && ($_GET['status'] ?? '') === 'completed') ? 'active' : '' ?>" href="<?= $baseUrl ?>/technician/jobs?status=completed">
                    <i class="bi bi-check-all me-2 text-success"></i> Job Selesai
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($requestUri === '/technician/jobs' && empty($_GET['status'])) ? 'active' : '' ?>" href="<?= $baseUrl ?>/technician/jobs">
                    <i class="bi bi-list-task me-2 text-info"></i> Semua Daftar Job
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= str_contains($requestUri, '/technician/history') ? 'active' : '' ?>" href="<?= $baseUrl ?>/technician/history">
                    <i class="bi bi-clock-history me-2 text-secondary"></i> Riwayat Pekerjaan
                </a>
            </li>
            <li class="nav-item mt-2">
                <div class="text-uppercase text-muted px-3 py-1 font-semibold" style="font-size: 0.72rem; letter-spacing: 0.8px;">
                    Pengaturan AI
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= str_contains($requestUri, '/admin/ai-config') ? 'active' : '' ?>" href="<?= $baseUrl ?>/admin/ai-config">
                    <i class="bi bi-cpu-fill me-2 text-primary"></i> Konfigurasi Model AI
                </a>
            </li>
        <?php endif; ?>

        <li class="nav-item mt-3">
            <div class="text-uppercase text-muted px-3 py-1 font-semibold" style="font-size: 0.72rem; letter-spacing: 0.8px;">
                Akun Pengguna
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= str_contains($requestUri, '/profile') ? 'active' : '' ?>" href="<?= $baseUrl ?>/profile">
                <i class="bi bi-person-circle me-2 text-primary"></i> Profil Saya
            </a>
        </li>

        <hr class="my-3 text-muted">

        <li class="nav-item">
            <a class="nav-link text-danger" href="<?= $baseUrl ?>/logout">
                <i class="bi bi-box-arrow-right me-2"></i> Keluar (Logout)
            </a>
        </li>
    </ul>
</div>
