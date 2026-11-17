<?php
$baseUrl = \Config\Env::get('APP_URL', 'http://localhost/Myjasagwuehhhhhhhhhhhhhhhhhhhhh');
$assetUrl = rtrim($baseUrl, '/') . '/Assets';
$user = \Middleware\AuthMiddleware::user();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'SmartService AI') ?></title>
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= $assetUrl ?>/css/style.css?v=<?= time() ?>">
    <?php if (!empty($extraCss) && is_array($extraCss)): ?>
        <?php foreach ($extraCss as $cssFile): ?>
            <link rel="stylesheet" href="<?= $assetUrl ?>/css/<?= htmlspecialchars($cssFile) ?>?v=<?= time() ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>

    <!-- Main Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-custom sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="<?= $baseUrl ?>">
                <div class="bg-primary text-white rounded-3 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                    <i class="bi bi-cpu-fill fs-5"></i>
                </div>
                <span class="navbar-brand-text">SmartService <span class="text-gradient">AI</span></span>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-3">
                    <?php if (!$user || $user['role'] !== 'customer'): ?>
                        <li class="nav-item">
                            <a class="nav-link font-semibold" href="<?= $baseUrl ?>"><i class="bi bi-house-door me-1"></i> Beranda</a>
                        </li>
                    <?php endif; ?>
                </ul>

                <div class="d-flex align-items-center gap-2">
                    <?php if ($user && $user['role'] === 'customer'): ?>
                        <a href="<?= $baseUrl ?>/admin/ai-config" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-semibold shadow-sm" title="Buka Pengaturan & Ganti Model AI">
                            <i class="bi bi-cpu-fill me-1"></i> Ganti Model AI
                        </a>
                        <a href="<?= $baseUrl ?>/profile" class="user-profile-badge border bg-white px-3 py-1 rounded-pill small fw-semibold text-dark d-inline-flex align-items-center gap-1 shadow-sm text-decoration-none" title="Buka Profil Saya">
                            <i class="bi bi-person-circle text-primary fs-6"></i>
                            <span><?= htmlspecialchars($user['name']) ?></span>
                        </a>
                        <a href="<?= $baseUrl ?>/logout" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-semibold">
                            <i class="bi bi-box-arrow-right me-1"></i> Logout
                        </a>
                    <?php elseif ($user): ?>
                        <?php
                            $dashboardUrl = match($user['role']) {
                                'admin'      => $baseUrl . '/admin/dashboard',
                                'technician' => $baseUrl . '/technician/dashboard',
                                default      => $baseUrl . '/'
                            };
                            $roleBadge = match($user['role']) {
                                'admin'      => 'bg-dark',
                                'technician' => 'bg-warning text-dark',
                                default      => 'bg-secondary'
                            };
                        ?>
                        <a href="<?= $dashboardUrl ?>" class="btn btn-light btn-sm border rounded-pill px-3 me-1">
                            <i class="bi bi-speedometer2 me-1 text-primary"></i> Dashboard
                        </a>
                        <a href="<?= $baseUrl ?>/profile" class="badge <?= $roleBadge ?> rounded-pill px-3 py-2 small text-decoration-none shadow-sm d-inline-flex align-items-center gap-1" title="Buka Profil Saya">
                            <i class="bi bi-person-circle me-1"></i> <?= htmlspecialchars($user['name']) ?>
                        </a>
                        <a href="<?= $baseUrl ?>/logout" class="btn btn-outline-danger btn-sm rounded-pill px-3">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                    <?php elseif (empty($hideAuthButtons)): ?>
                        <div class="dropdown d-inline-block">
                            <button class="btn btn-outline-primary btn-sm rounded-pill px-3 dropdown-toggle fw-semibold" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Login
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-4 mt-2 py-2" style="min-width: 250px;">
                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="<?= $baseUrl ?>/login/customer">
                                        <div class="bg-primary text-white p-2 rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                            <i class="bi bi-person-fill"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark small">Login Customer</div>
                                            <div class="text-muted" style="font-size: 0.75rem;">Pesan perbaikan & tracking</div>
                                        </div>
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider my-1"></li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="<?= $baseUrl ?>/login/technician">
                                        <div class="bg-warning text-dark p-2 rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                            <i class="bi bi-tools"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark small">Login Teknisi</div>
                                            <div class="text-muted" style="font-size: 0.75rem;">Portal penugasan mitra servis</div>
                                        </div>
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider my-1"></li>
                                <li><hr class="dropdown-divider my-1"></li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="<?= $baseUrl ?>/login/admin">
                                        <div class="bg-dark text-white p-2 rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                            <i class="bi bi-shield-lock-fill"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark small">Login Administrator</div>
                                            <div class="text-muted" style="font-size: 0.75rem;">Konsol pengelola sistem</div>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="dropdown d-inline-block">
                            <button class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm fw-semibold dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-plus me-1"></i> Register
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-4 mt-2 py-2" style="min-width: 260px;">
                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="<?= $baseUrl ?>/register?role=customer">
                                        <div class="bg-primary text-white p-2 rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                            <i class="bi bi-person-fill"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark small">Daftar Customer</div>
                                            <div class="text-muted" style="font-size: 0.75rem;">Sebagai pemesan jasa perbaikan</div>
                                        </div>
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider my-1"></li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="<?= $baseUrl ?>/register?role=technician">
                                        <div class="bg-warning text-dark p-2 rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                            <i class="bi bi-tools"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark small">Daftar Mitra Teknisi</div>
                                            <div class="text-muted" style="font-size: 0.75rem;">Sebagai penyedia jasa perbaikan</div>
                                        </div>
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider my-1"></li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="<?= $baseUrl ?>/register/admin">
                                        <div class="bg-dark text-white p-2 rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                            <i class="bi bi-shield-plus"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark small">Daftar Administrator</div>
                                            <div class="text-muted" style="font-size: 0.75rem;">Konsol pengelola internal</div>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <?php if ($user && $user['role'] === 'customer'): ?>
        <?php
            $currentUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
            $isDashboard = str_contains($currentUri, '/customer/dashboard');
            $isCreateBooking = str_contains($currentUri, '/customer/requests/create');
            $isActiveRequests = (str_contains($currentUri, '/customer/requests') && !str_contains($currentUri, '/customer/requests/create'));
            $isHistory = str_contains($currentUri, '/customer/history');
            $isAiConsult = str_contains($currentUri, '/customer/ai-consultation');
            $isProfile = str_contains($currentUri, '/profile');
        ?>
        <!-- Customer Secondary Horizontal Navigation Bar -->
        <nav class="customer-subnav">
            <div class="container-fluid px-lg-5">
                <div class="customer-subnav-inner">
                    <a href="<?= $baseUrl ?>/customer/dashboard" class="customer-nav-item <?= $isDashboard ? 'active' : '' ?>">
                        <i class="bi bi-grid-fill me-2"></i>
                        <span>Dashboard Saya</span>
                    </a>
                    <div class="customer-nav-divider"></div>
                    <a href="<?= $baseUrl ?>/customer/requests/create" class="customer-nav-item <?= $isCreateBooking ? 'active' : '' ?>">
                        <i class="bi bi-tools me-2"></i>
                        <span>Pesan Jasa Perbaikan</span>
                    </a>
                    <div class="customer-nav-divider"></div>
                    <a href="<?= $baseUrl ?>/customer/requests" class="customer-nav-item <?= $isActiveRequests ? 'active' : '' ?>">
                        <i class="bi bi-clipboard-check me-2"></i>
                        <span>Pesanan Aktif</span>
                    </a>
                    <div class="customer-nav-divider"></div>
                    <a href="<?= $baseUrl ?>/customer/history" class="customer-nav-item <?= $isHistory ? 'active' : '' ?>">
                        <i class="bi bi-clock-history me-2"></i>
                        <span>Riwayat Pesanan</span>
                    </a>
                    <div class="customer-nav-divider"></div>
                    <a href="<?= $baseUrl ?>/customer/ai-consultation" class="customer-nav-item <?= $isAiConsult ? 'active' : '' ?>">
                        <i class="bi bi-robot me-2"></i>
                        <span>Konsultasi AI Kerusakan</span>
                    </a>
                    <div class="customer-nav-divider"></div>
                    <a href="<?= $baseUrl ?>/profile" class="customer-nav-item <?= $isProfile ? 'active' : '' ?>">
                        <i class="bi bi-person-circle me-2"></i>
                        <span>Profil Saya</span>
                    </a>
                </div>
            </div>
        </nav>
    <?php endif; ?>
    <main>
