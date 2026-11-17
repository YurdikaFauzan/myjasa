<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center gy-4">
            <div class="col-lg-7">
                <div class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill mb-3">
                    <i class="bi bi-gear-fill me-1"></i> Fondasi Sistem MVC PHP Native
                </div>
                <h1 class="display-4 hero-title mb-3">
                    Solusi Perbaikan Peralatan Rumah Tangga <span class="text-gradient">SmartService AI</span>
                </h1>
                <p class="lead text-muted mb-4">
                    Platform terpadu untuk membantu perbaikan dan perawatan peralatan rumah tangga seperti AC, Kulkas, Mesin Cuci, dan alat elektronik lainnya dengan efisien.
                </p>
                <div class="d-flex flex-wrap gap-2 mb-4">
                    <span class="appliance-badge"><i class="bi bi-wind text-info fs-5"></i> Pendingin AC</span>
                    <span class="appliance-badge"><i class="bi bi-snow text-primary fs-5"></i> Kulkas / Freezer</span>
                    <span class="appliance-badge"><i class="bi bi-water text-primary fs-5"></i> Mesin Cuci</span>
                    <span class="appliance-badge"><i class="bi bi-lightning-charge text-warning fs-5"></i> Pompa Air</span>
                    <span class="appliance-badge"><i class="bi bi-fire text-danger fs-5"></i> Kompor Gas</span>
                    <span class="appliance-badge"><i class="bi bi-tv text-dark fs-5"></i> TV & Elektronik</span>
                </div>
            </div>
            <div class="col-lg-5">
                <!-- System Connection Status Card -->
                <div id="status" class="card shadow-lg border-0 rounded-4 overflow-hidden">
                    <div class="card-header bg-dark text-white p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="mb-0 fs-6 fw-bold"><i class="bi bi-hdd-network me-2 text-info"></i> Status Fondasi System</h5>
                            <span class="badge bg-primary">v1.0 MVC</span>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-semibold text-uppercase">Arsitektur Aplikasi</label>
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                <span class="fw-bold text-dark">Native PHP (MVC Pattern)</span>
                            </div>
                        </div>

                        <hr class="my-3 text-muted">

                        <div class="mb-3">
                            <label class="form-label text-muted small fw-semibold text-uppercase">Koneksi Database MySQL</label>
                            <?php if (!empty($dbStatus['connected'])): ?>
                                <div class="alert alert-success d-flex align-items-start gap-2 mb-0 py-2 px-3 border-0">
                                    <i class="bi bi-database-check fs-5"></i>
                                    <div>
                                        <strong>Terhubung!</strong>
                                        <div class="small"><?= htmlspecialchars($dbStatus['message']) ?></div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-warning d-flex align-items-start gap-2 mb-0 py-2 px-3 border-0">
                                    <i class="bi bi-exclamation-triangle-fill fs-5 text-warning"></i>
                                    <div>
                                        <strong>Perhatian: Database belum terpasang</strong>
                                        <div class="small mt-1"><?= htmlspecialchars($dbStatus['message']) ?></div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="bg-light p-3 rounded-3 border">
                            <div class="d-flex justify-content-between align-items-center small text-muted">
                                <span>Driver Database:</span>
                                <span class="badge bg-secondary">PDO MySQL</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center small text-muted mt-1">
                                <span>Environment File:</span>
                                <span class="badge bg-light text-dark border">.env</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Peran Pengguna (Roles Section) -->
<section id="roles" class="py-5 bg-white">
    <div class="container py-4">
        <div class="text-center max-w-2xl mx-auto mb-5">
            <h2 class="fw-bold mb-2">3 Peran Utama Pengguna (Roles)</h2>
            <p class="text-muted">Aplikasi dirancang untuk melayani 3 aktor utama dalam ekosistem perbaikan peralatan rumah tangga:</p>
        </div>

        <div class="row g-4">
            <?php if (!empty($roles) && is_array($roles)): ?>
                <?php foreach ($roles as $role): ?>
                    <div class="col-md-4">
                        <div class="role-card">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="role-icon-box">
                                    <i class="<?= htmlspecialchars($role['icon']) ?>"></i>
                                </div>
                                <span class="badge <?= htmlspecialchars($role['badge']) ?> px-3 py-2 rounded-pill fs-6">
                                    <?= htmlspecialchars($role['name']) ?>
                                </span>
                            </div>
                            <h4 class="fw-bold h5 mb-2"><?= htmlspecialchars($role['name']) ?> Role</h4>
                            <p class="text-muted small mb-0">
                                <?= htmlspecialchars($role['description']) ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- System Architecture Overview Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <div class="card-body">
                        <h3 class="fw-bold h5 mb-3 text-primary"><i class="bi bi-folder-symlink me-2"></i> Struktur Folder Fondasi Project</h3>
                        <p class="text-muted small mb-4">Struktur MVC yang telah disiapkan untuk mendukung pengembangan modul lebih lanjut:</p>
                        
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="p-3 bg-white border rounded-3 h-100">
                                    <div class="fw-bold text-dark mb-1"><i class="bi bi-folder-fill text-warning me-1"></i> Controllers/</div>
                                    <span class="small text-muted">Logika bisnis dan kontrol request HTTP.</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 bg-white border rounded-3 h-100">
                                    <div class="fw-bold text-dark mb-1"><i class="bi bi-folder-fill text-warning me-1"></i> Models/</div>
                                    <span class="small text-muted">Interaksi database dan entitas data.</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 bg-white border rounded-3 h-100">
                                    <div class="fw-bold text-dark mb-1"><i class="bi bi-folder-fill text-warning me-1"></i> Views/</div>
                                    <span class="small text-muted">Antarmuka HTML & Bootstrap layout.</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 bg-white border rounded-3 h-100">
                                    <div class="fw-bold text-dark mb-1"><i class="bi bi-folder-fill text-warning me-1"></i> Config/</div>
                                    <span class="small text-muted">Konstruksi PDO MySQL & parser Env.</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 bg-white border rounded-3 h-100">
                                    <div class="fw-bold text-dark mb-1"><i class="bi bi-folder-fill text-warning me-1"></i> Routes/</div>
                                    <span class="small text-muted">Routing URL aplikasi ke Controller.</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 bg-white border rounded-3 h-100">
                                    <div class="fw-bold text-dark mb-1"><i class="bi bi-folder-fill text-warning me-1"></i> Assets/</div>
                                    <span class="small text-muted">File CSS, JavaScript, dan Gambar.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
