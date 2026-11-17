<?php
$baseUrl = \Config\Env::get('APP_URL', 'http://localhost/Myjasagwuehhhhhhhhhhhhhhhhhhhhh');

$activeProviderKey = $activeConfig['provider'] ?? 'groq';
$activeModel = $activeConfig['model_id'] ?? 'qwen/qwen3.8-27b';
$activeProviderName = $activeConfig['provider_name'] ?? 'Groq';
$updatedByName = $activeConfig['updated_by_name'] ?? 'System Default';
$updatedAt = !empty($activeConfig['updated_at']) ? date('d M Y, H:i', strtotime($activeConfig['updated_at'])) . ' WIB' : '-';
$lastTestedAt = !empty($activeConfig['last_tested_at']) ? date('d M Y, H:i', strtotime($activeConfig['last_tested_at'])) . ' WIB' : 'Belum diuji';
$lastTestStatus = $activeConfig['last_test_status'] ?? null;
$lastTestLatency = $activeConfig['last_test_latency'] ?? null;
$lastTestMessage = $activeConfig['last_test_message'] ?? null;
$hasApiKey = !empty($activeConfig['has_api_key']);
$maskedApiKey = $activeConfig['masked_api_key'] ?? '';
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
                <div class="alert alert-<?= htmlspecialchars($flash['type']) ?> alert-dismissible fade show rounded-4 shadow-sm mb-4 border-0" role="alert">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi <?= $flash['type'] === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill' ?> fs-5"></i>
                        <div class="fw-semibold small"><?= htmlspecialchars($flash['message']) ?></div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Alert Toast Placeholder (Dynamic AJAX) -->
            <div id="ajax-alert-container"></div>

            <!-- Header Section -->
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 p-4 bg-dark text-white rounded-4 shadow-sm">
                <div>
                    <div class="badge bg-primary text-white mb-2 px-3 py-1 rounded-pill">
                        <i class="bi bi-cpu-fill me-1"></i> Pengaturan Sistem &bull; Multi-Provider AI Engine
                    </div>
                    <h2 class="fw-bold mb-1">Konfigurasi Model AI</h2>
                    <p class="text-white-50 mb-0 small">
                        Kelola provider dan model AI yang digunakan oleh Chatbot Customer Service dan Konsultasi Diagnosis Kerusakan Otomatis.
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <?php if ($hasApiKey): ?>
                        <span class="badge bg-success-subtle text-success border border-success px-3 py-2 rounded-pill font-semibold d-inline-flex align-items-center gap-2">
                            <span class="spinner-grow spinner-grow-sm text-success" style="width: 8px; height: 8px;"></span>
                            AI Engine Online &bull; <?= htmlspecialchars($activeProviderName) ?>
                        </span>
                    <?php else: ?>
                        <span class="badge bg-warning-subtle text-warning border border-warning px-3 py-2 rounded-pill font-semibold d-inline-flex align-items-center gap-1">
                            <i class="bi bi-exclamation-circle-fill"></i> API Key Belum Dikonfigurasi
                        </span>
                    <?php endif; ?>

                    <button type="button" class="btn btn-outline-light btn-sm rounded-pill px-3 py-2" data-bs-toggle="modal" data-bs-target="#modal-reset-default" title="Kembalikan ke pengaturan awal sistem">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Pulihkan Default
                    </button>
                </div>
            </div>

            <!-- 4 Stat Information Cards -->
            <div class="row g-3 mb-4">
                
                <!-- 1. Provider AI Aktif -->
                <div class="col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-primary">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted small fw-bold text-uppercase letter-spacing-1">Provider Aktif</span>
                            <div class="p-2 bg-primary-subtle text-primary rounded-circle" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-lightning-charge-fill"></i>
                            </div>
                        </div>
                        <h5 class="fw-bold text-dark mb-1 text-truncate" id="header-active-provider-name">
                            <?= htmlspecialchars($activeProviderName) ?>
                        </h5>
                        <div class="small text-muted">
                            Provider aktif melayani pelanggan secara langsung.
                        </div>
                    </div>
                </div>

                <!-- 2. Model AI Aktif Saat Ini -->
                <div class="col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-success">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted small fw-bold text-uppercase letter-spacing-1">Model Aktif</span>
                            <span class="badge bg-success text-white rounded-pill px-2 py-1 small">Produksi</span>
                        </div>
                        <h5 class="fw-bold text-success text-truncate mb-1" id="active-model-title" title="<?= htmlspecialchars($activeModel) ?>">
                            <?= htmlspecialchars($activeModel) ?>
                        </h5>
                        <div class="small text-muted">
                            Model yang merespons percakapan chatbot.
                        </div>
                    </div>
                </div>

                <!-- 3. Status Koneksi Provider -->
                <div class="col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 <?= $lastTestStatus === 'success' ? 'border-info' : ($lastTestStatus === 'failed' ? 'border-danger' : 'border-secondary') ?>">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted small fw-bold text-uppercase letter-spacing-1">Status Koneksi</span>
                            <div class="p-2 bg-light text-secondary rounded-circle" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-activity"></i>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2 mb-1" id="header-status-badge">
                            <?php if ($lastTestStatus === 'success'): ?>
                                <span class="badge bg-success rounded-pill px-2 py-1 small">
                                    <i class="bi bi-check-circle-fill me-1"></i> Terverifikasi
                                </span>
                                <?php if ($lastTestLatency): ?>
                                    <span class="text-muted small fw-semibold"><?= $lastTestLatency ?> ms</span>
                                <?php endif; ?>
                            <?php elseif ($lastTestStatus === 'failed'): ?>
                                <span class="badge bg-danger rounded-pill px-2 py-1 small">
                                    <i class="bi bi-x-circle-fill me-1"></i> Pengujian Gagal
                                </span>
                            <?php else: ?>
                                <span class="badge bg-secondary rounded-pill px-2 py-1 small">Belum Diuji</span>
                            <?php endif; ?>
                        </div>
                        <div class="small text-muted text-truncate" id="header-status-desc">
                            <?= !empty($lastTestMessage) ? htmlspecialchars($lastTestMessage) : 'Tekan tombol "Tes Koneksi" untuk menguji provider.' ?>
                        </div>
                    </div>
                </div>

                <!-- 4. Riwayat Pembaruan Terakhir -->
                <div class="col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-dark">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted small fw-bold text-uppercase letter-spacing-1">Perubahan Terakhir</span>
                            <div class="p-2 bg-secondary-subtle text-secondary rounded-circle" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-clock-history"></i>
                            </div>
                        </div>
                        <div class="fw-bold text-dark mb-1 small text-truncate" id="last-updated-text"><?= htmlspecialchars($updatedAt) ?></div>
                        <div class="small text-muted text-truncate">
                            Oleh: <strong class="text-dark" id="last-updated-by"><?= htmlspecialchars($updatedByName) ?></strong>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Provider Selection Banner (Visual Cards) -->
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                    <div>
                        <h6 class="fw-bold text-dark mb-1">
                            <i class="bi bi-grid-fill me-2 text-primary"></i>Pilih Provider AI
                        </h6>
                        <p class="text-muted small mb-0">
                            Pilih provider AI yang ingin dikonfigurasi dan diaktifkan untuk Chatbot Customer Service.
                        </p>
                    </div>
                </div>

                <div class="row g-3" id="provider-cards-grid">
                    <?php foreach ($providerCards as $pKey => $pCard): ?>
                        <div class="col-sm-6 col-lg-6">
                            <div class="card h-100 border rounded-4 p-3 provider-option-card <?= ($pKey === $activeProviderKey) ? 'active-provider-card' : '' ?>" 
                                 data-provider="<?= $pKey ?>" 
                                 style="cursor: pointer; transition: all 0.2s ease-in-out;">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="p-2 bg-<?= $pCard['color'] ?>-subtle text-<?= $pCard['color'] ?> rounded-3">
                                            <i class="bi <?= $pCard['icon'] ?> fs-5"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-0"><?= htmlspecialchars($pCard['name']) ?></h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <?php if ($pCard['is_active']): ?>
                                        <span class="badge bg-success rounded-pill px-2 py-1 small">
                                            <i class="bi bi-check2-circle me-1"></i> Sedang Aktif
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-light text-muted border rounded-pill px-2 py-1 small">
                                            Tersimpan
                                        </span>
                                    <?php endif; ?>

                                    <?php if ($pCard['has_api_key']): ?>
                                        <span class="badge bg-primary-subtle text-primary rounded-pill px-2 py-1 small ms-1" title="Kunci API sudah tersimpan">
                                            <i class="bi bi-key-fill"></i> Key OK
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-warning-subtle text-warning rounded-pill px-2 py-1 small ms-1" title="Belum memiliki API Key">
                                            Key Kosong
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div class="small text-muted mt-2 text-truncate" style="font-size: 0.78rem;">
                                    Model: <code><?= htmlspecialchars($pCard['saved_model']) ?></code>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Main Form Section -->
            <div class="row g-4">
                
                <!-- Left Form Column -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-4">
                        
                        <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-sliders fs-5 text-primary"></i>
                                <h5 class="fw-bold mb-0 text-dark" id="form-panel-title">Form Konfigurasi: <?= htmlspecialchars($activeProviderName) ?></h5>
                            </div>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1 small" id="provider-badge-indicator">
                                Provider Terpilih: <strong id="provider-badge-name"><?= strtoupper($activeProviderKey) ?></strong>
                            </span>
                        </div>

                        <div class="card-body p-4">

                            <!-- Informative Alert -->
                            <div class="alert alert-light border rounded-3 p-3 mb-4 d-flex align-items-start gap-2 shadow-none" style="background-color: #f8fafc;">
                                <i class="bi bi-info-circle-fill fs-5 text-primary mt-0"></i>
                                <div class="small text-muted" id="provider-info-description">
                                    Atur kredensial API Key, pilihan model AI, serta parameter inference. Anda dapat <strong>menguji koneksi secara terisolasi</strong> sebelum menyimpan dan mengaktifkan model ke sistem produksi chatbot.
                                </div>
                            </div>

                            <form action="<?= $baseUrl ?>/admin/ai-config/save" method="POST" id="form-ai-config">
                                <input type="hidden" name="csrf_token" id="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
                                <input type="hidden" name="provider" id="input-provider" value="<?= htmlspecialchars($activeProviderKey) ?>">
                                <input type="hidden" name="set_active" id="input-set-active" value="1">
                                <input type="hidden" name="delete_api_key" id="input-delete-api-key" value="0">

                                <!-- A. Pilihan Provider Dropdown (Alternatif Langsung di Form) -->
                                <div class="mb-4">
                                    <label for="select-provider" class="form-label fw-bold text-dark small text-uppercase">
                                        Provider AI <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-secondary"><i class="bi bi-cpu"></i></span>
                                        <select class="form-select fw-semibold" id="select-provider">
                                            <?php foreach ($supportedProviders as $k => $prov): ?>
                                                <option value="<?= $k ?>" <?= ($k === $activeProviderKey) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($prov['name']) ?> (<?= htmlspecialchars($prov['badge']) ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-text small text-muted">
                                        Mengganti provider akan menyesuaikan daftar model, endpoint resmi, dan kredensial secara otomatis.
                                    </div>
                                </div>

                                <!-- B. Pengaturan API Key -->
                                <div class="mb-4">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <label for="input-api-key" class="form-label fw-bold text-dark small text-uppercase mb-0">
                                            Kunci Rahasia API Key <span class="text-danger">*</span>
                                        </label>
                                        <div id="api-key-status-badge">
                                            <?php if (!empty($providerCards[$activeProviderKey]['has_api_key'])): ?>
                                                <span class="badge bg-success-subtle text-success border border-success rounded-pill px-2 py-1 small">
                                                    <i class="bi bi-shield-lock-fill me-1"></i> Tersimpan (<?= htmlspecialchars($providerCards[$activeProviderKey]['masked_api_key']) ?>)
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-warning-subtle text-warning border border-warning rounded-pill px-2 py-1 small">
                                                    <i class="bi bi-exclamation-triangle-fill me-1"></i> Belum Dikonfigurasi
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-secondary"><i class="bi bi-key-fill"></i></span>
                                        <input type="password" 
                                               class="form-control font-monospace" 
                                               id="input-api-key" 
                                               name="api_key" 
                                               placeholder="Masukkan API Key baru atau biarkan kosong jika tetap menggunakan yang tersimpan" 
                                               autocomplete="off">
                                        <button class="btn btn-outline-secondary" type="button" id="btn-toggle-apikey" title="Tampilkan / Sembunyikan API Key">
                                            <i class="bi bi-eye" id="eye-icon"></i>
                                        </button>
                                        <button class="btn btn-outline-danger <?= empty($providerCards[$activeProviderKey]['has_api_key']) ? 'd-none' : '' ?>" 
                                                type="button" 
                                                id="btn-delete-apikey" 
                                                title="Hapus API Key yang tersimpan dari database">
                                            <i class="bi bi-trash3-fill"></i> Hapus Key
                                        </button>
                                    </div>
                                    <div class="form-text small text-muted" id="api-key-help-text">
                                        API Key disimpan secara aman di backend menggunakan enkripsi AES-256 dan tidak pernah dibocorkan ke browser. Kosongkan jika ingin mempertahankan kunci yang sudah tersimpan.
                                    </div>
                                </div>

                                <!-- C. Pengaturan Model AI -->
                                <div class="mb-3">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <label for="select-model" class="form-label fw-bold text-dark small text-uppercase mb-0">
                                            Pilih Model AI <span class="text-danger">*</span>
                                        </label>
                                        <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none text-primary fw-semibold" id="btn-refresh-models">
                                            <i class="bi bi-arrow-repeat me-1" id="refresh-models-icon"></i> <span id="refresh-models-text">Muat Ulang Model dari API</span>
                                        </button>
                                    </div>
                                    <select class="form-select form-select-lg rounded-3 fs-6 fw-semibold" id="select-model" name="model_id" required>
                                        <!-- Will be populated dynamically by JavaScript based on active provider -->
                                    </select>
                                    <div class="small text-muted mt-2" id="model-desc-text">
                                        Pilih model untuk melihat deskripsi spesifikasi dan rekomendasi.
                                    </div>
                                </div>

                                <!-- Manual Model ID Input (Conditional) -->
                                <div class="mb-4 d-none" id="custom-model-wrapper">
                                    <label for="custom_model_id" class="form-label fw-bold text-dark small text-uppercase">
                                        Model ID Manual / Kustom <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group shadow-sm rounded-3">
                                        <span class="input-group-text bg-light text-secondary"><i class="bi bi-pencil-square"></i></span>
                                        <input type="text" 
                                               class="form-control font-monospace" 
                                               id="custom_model_id" 
                                               name="custom_model_id" 
                                               placeholder="Masukkan ID model resmi (contoh: qwen/qwen3.8-27b, gpt-4o, gemini-1.5-flash)">
                                    </div>
                                    <div class="form-text small text-muted" id="custom-model-help-text">
                                        Masukkan ID model yang valid sesuai dokumentasi resmi provider.
                                    </div>
                                </div>

                                <!-- D. Pengaturan Tambahan (Advanced Parameters) Accordion -->
                                <div class="accordion accordion-flush mb-4 rounded-3 border overflow-hidden" id="accordionAdvanced">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingAdvanced">
                                            <button class="accordion-button collapsed py-3 fw-bold small text-uppercase text-secondary bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAdvanced" aria-expanded="false" aria-controls="collapseAdvanced">
                                                <i class="bi bi-sliders2-vertical me-2 text-primary"></i> Pengaturan Tambahan (Endpoint, Temperature & Kuota Token)
                                            </button>
                                        </h2>
                                        <div id="collapseAdvanced" class="accordion-collapse collapse" aria-labelledby="headingAdvanced" data-bs-parent="#accordionAdvanced">
                                            <div class="accordion-body p-4 bg-white">
                                                
                                                <!-- Base URL / Endpoint -->
                                                <div class="mb-3">
                                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                                        <label for="input-base-url" class="form-label small fw-bold text-dark text-uppercase mb-0">Base URL / Endpoint API</label>
                                                        <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none small text-muted" id="btn-reset-base-url">
                                                            Reset ke Default Provider
                                                        </button>
                                                    </div>
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-light text-secondary font-monospace small"><i class="bi bi-globe"></i></span>
                                                        <input type="url" 
                                                               class="form-control font-monospace small" 
                                                               id="input-base-url" 
                                                               name="base_url" 
                                                               placeholder="https://api.provider.com/v1">
                                                    </div>
                                                    <div class="form-text small text-muted">Biarkan sesuai default resmi provider kecuali Anda menggunakan reverse proxy internal.</div>
                                                </div>

                                                <!-- Temperature Slider -->
                                                <div class="mb-3">
                                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                                        <label for="input-temperature" class="form-label small fw-bold text-dark text-uppercase mb-0">
                                                            Temperature: <span class="badge bg-primary rounded-pill px-2" id="temp-display">0.50</span>
                                                        </label>
                                                        <span class="text-muted small">0.0 (Fokus/Konsisten) &mdash; 2.0 (Kreatif)</span>
                                                    </div>
                                                    <input type="range" class="form-range" min="0.0" max="2.0" step="0.05" id="input-temperature" name="temperature" value="0.50">
                                                    <div class="form-text small text-muted">Rekomendasi untuk chatbot teknis perbaikan: 0.30 &ndash; 0.70 agar akurat dan konsisten.</div>
                                                </div>

                                                <div class="row g-3">
                                                    <!-- Maximum Output Tokens -->
                                                    <div class="col-md-6">
                                                        <label for="input-max-tokens" class="form-label small fw-bold text-dark text-uppercase">Maximum Output Tokens</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text bg-light text-secondary"><i class="bi bi-chat-left-dots"></i></span>
                                                            <input type="number" class="form-control" id="input-max-tokens" name="max_tokens" min="50" max="8192" step="50" value="650">
                                                        </div>
                                                        <div class="form-text small text-muted">Batas maksimal token jawaban (1 token &asymp; 4 karakter).</div>
                                                    </div>

                                                    <!-- Timeout Request (Seconds) -->
                                                    <div class="col-md-6">
                                                        <label for="input-timeout" class="form-label small fw-bold text-dark text-uppercase">Request Timeout (Detik)</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text bg-light text-secondary"><i class="bi bi-stopwatch"></i></span>
                                                            <input type="number" class="form-control" id="input-timeout" name="timeout" min="5" max="120" step="1" value="20">
                                                            <span class="input-group-text bg-light text-secondary">detik</span>
                                                        </div>
                                                        <div class="form-text small text-muted">Durasi maksimal backend menunggu respons API.</div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons: Uji Model & Simpan Perubahan -->
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 pt-3 border-top">
                                    <!-- Tombol Tes Koneksi -->
                                    <button type="button" class="btn btn-outline-primary rounded-pill px-4 py-2 fw-bold d-inline-flex align-items-center gap-2" id="btn-test-model">
                                        <span class="spinner-border spinner-border-sm d-none" id="test-spinner" role="status"></span>
                                        <i class="bi bi-play-circle-fill" id="test-icon"></i>
                                        <span id="test-btn-text">Tes Koneksi</span>
                                    </button>

                                    <!-- Tombol Simpan & Aktifkan Konfigurasi -->
                                    <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 fw-bold d-inline-flex align-items-center gap-2 shadow-sm" id="btn-save-config">
                                        <i class="bi bi-check2-circle fs-5"></i>
                                        <span>Simpan & Aktifkan Konfigurasi</span>
                                    </button>
                                </div>

                            </form>

                        </div>

                    </div>

                    <!-- Test Output Visual Card (Interactive) -->
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-4 d-none" id="test-result-card">
                        <div class="card-header py-3 px-4 d-flex align-items-center justify-content-between" id="test-card-header">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-activity fs-5" id="test-result-icon"></i>
                                <h6 class="fw-bold mb-0" id="test-result-title">Hasil Pengujian Model</h6>
                            </div>
                            <span class="badge rounded-pill px-3 py-1 font-semibold" id="test-latency-badge">-</span>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <label class="small text-muted fw-bold text-uppercase">Respons Pengujian Chat Completions:</label>
                                <div class="p-3 bg-light rounded-3 font-monospace small text-dark border" id="test-output-box" style="white-space: pre-wrap; min-height: 50px;">
                                    -
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between small text-muted flex-wrap gap-2">
                                <div><i class="bi bi-clock me-1"></i>Waktu Uji: <span id="test-timestamp">-</span></div>
                                <div id="test-model-name-display">-</div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Right Column: Information & Documentation Card -->
                <div class="col-lg-4">

                    <!-- Official Documentation Card -->
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div class="p-2 bg-dark text-white rounded-3">
                                <i class="bi bi-book-half"></i>
                            </div>
                            <h6 class="fw-bold mb-0 text-dark">Dokumentasi & Referensi Provider</h6>
                        </div>
                        <p class="small text-muted mb-3" id="provider-sidebar-info">
                            SmartService AI mendukung 2 provider resmi AI berkinerja tinggi: <strong>Groq Cloud LPU</strong> (kecepatan inferensi ultra tinggi) dan <strong>NVIDIA NIM</strong> (arsitektur GPU enterprise).
                        </p>
                        <div class="list-group list-group-flush small mb-3">
                            <a href="https://console.groq.com/docs/models" target="_blank" rel="noopener" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between px-0 py-2 border-0">
                                <span><i class="bi bi-lightning-charge text-warning me-2"></i>Katalog Model Groq</span>
                                <i class="bi bi-box-arrow-up-right text-muted small"></i>
                            </a>
                            <a href="https://build.nvidia.com/explore/discover" target="_blank" rel="noopener" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between px-0 py-2 border-0">
                                <span><i class="bi bi-gpu-card text-success me-2"></i>Katalog Model NVIDIA NIM</span>
                                <i class="bi bi-box-arrow-up-right text-muted small"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Security & Protection Card -->
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div class="p-2 bg-success-subtle text-success rounded-3">
                                <i class="bi bi-shield-check"></i>
                            </div>
                            <h6 class="fw-bold mb-0 text-dark">Standar Keamanan Kredensial</h6>
                        </div>
                        <ul class="small text-muted ps-3 mb-0" style="line-height: 1.6;">
                            <li class="mb-2"><strong>Enkripsi Backend AES-256:</strong> Kunci rahasia API disimpan terenkripsi di server dan tidak pernah dikirim dalam respons browser.</li>
                            <li class="mb-2"><strong>Proteksi Penghapusan:</strong> Mengosongkan kolom API Key saat menyimpan tidak akan menghapus kredensial yang sudah tersimpan sebelumnya.</li>
                            <li class="mb-2"><strong>Uji Model Terisolasi:</strong> Mengklik "Tes Koneksi" menguji endpoint tanpa merusak atau mengubah konfigurasi produksi yang sedang aktif.</li>
                            <li class="mb-2"><strong>Fallback Anti-Downtime:</strong> Jika provider eksternal mengalami kendala jaringan, chatbot dan diagnosis tetap memiliki penanganan error yang aman.</li>
                            <li><strong>Hak Akses Terproteksi:</strong> Seluruh endpoint konfigurasi dilindungi oleh <code>AuthMiddleware::requireRole('admin')</code> dan token CSRF.</li>
                        </ul>
                    </div>

                </div>

            </div>

        </div>
    </div>
</div>

<!-- Modal Konfirmasi Pulihkan Default -->
<div class="modal fade" id="modal-reset-default" tabindex="-1" aria-labelledby="modalResetDefaultLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <div class="d-flex align-items-center gap-2 text-warning">
                    <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                    <h5 class="modal-title fw-bold text-dark" id="modalResetDefaultLabel">Konfirmasi Pemulihan Konfigurasi</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-3">
                <p class="text-muted small mb-0">
                    Apakah Anda yakin ingin memulihkan pengaturan AI ke <strong>setelan default sistem</strong>?
                </p>
                <div class="alert alert-light border rounded-3 p-3 my-3 small text-dark">
                    <div>&bull; Provider Aktif: <strong>Groq (Cloud LPU)</strong></div>
                    <div>&bull; Model Default: <strong>qwen/qwen3.8-27b</strong></div>
                    <div>&bull; Temperature: <strong>0.50</strong> | Max Tokens: <strong>650</strong> | Timeout: <strong>20s</strong></div>
                </div>
                <p class="text-danger small mb-0">
                    <i class="bi bi-info-circle me-1"></i> Perubahan ini akan langsung diterapkan ke Chatbot Customer Service dan Konsultasi Diagnosis.
                </p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                <form action="<?= $baseUrl ?>/admin/ai-config/reset" method="POST" id="form-reset-default">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
                    <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Ya, Pulihkan Default
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Pass dynamic provider cards data to JavaScript -->
<script>
const BASE_URL = '<?= rtrim($baseUrl, '/') ?>';
const PROVIDER_DATA = <?= json_encode($providerCards, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
const CURRENT_ACTIVE_PROVIDER = '<?= $activeProviderKey ?>';
const CURRENT_ACTIVE_MODEL = '<?= addslashes($activeModel) ?>';

document.addEventListener('DOMContentLoaded', function () {
    // DOM Elements
    const selectProvider = document.getElementById('select-provider');
    const inputProviderHidden = document.getElementById('input-provider');
    const formPanelTitle = document.getElementById('form-panel-title');
    const providerBadgeName = document.getElementById('provider-badge-name');
    const providerInfoDesc = document.getElementById('provider-info-description');

    const inputApiKey = document.getElementById('input-api-key');
    const apiKeyStatusBadge = document.getElementById('api-key-status-badge');
    const btnToggleApiKey = document.getElementById('btn-toggle-apikey');
    const eyeIcon = document.getElementById('eye-icon');
    const btnDeleteApiKey = document.getElementById('btn-delete-apikey');
    const inputDeleteApiKey = document.getElementById('input-delete-api-key');

    const selectModel = document.getElementById('select-model');
    const customWrapper = document.getElementById('custom-model-wrapper');
    const customInput = document.getElementById('custom_model_id');
    const modelDescText = document.getElementById('model-desc-text');
    const btnRefreshModels = document.getElementById('btn-refresh-models');
    const refreshIcon = document.getElementById('refresh-models-icon');
    const refreshText = document.getElementById('refresh-models-text');

    const inputBaseUrl = document.getElementById('input-base-url');
    const btnResetBaseUrl = document.getElementById('btn-reset-base-url');
    const inputTemperature = document.getElementById('input-temperature');
    const tempDisplay = document.getElementById('temp-display');
    const inputMaxTokens = document.getElementById('input-max-tokens');
    const inputTimeout = document.getElementById('input-timeout');

    const btnTest = document.getElementById('btn-test-model');
    const testSpinner = document.getElementById('test-spinner');
    const testIcon = document.getElementById('test-icon');
    const testBtnText = document.getElementById('test-btn-text');

    const resultCard = document.getElementById('test-result-card');
    const cardHeader = document.getElementById('test-card-header');
    const resultTitle = document.getElementById('test-result-title');
    const resultIcon = document.getElementById('test-result-icon');
    const latencyBadge = document.getElementById('test-latency-badge');
    const outputBox = document.getElementById('test-output-box');
    const timestampEl = document.getElementById('test-timestamp');
    const modelNameDisplay = document.getElementById('test-model-name-display');

    const csrfToken = document.getElementById('csrf_token').value;
    const formAiConfig = document.getElementById('form-ai-config');
    const ajaxAlertContainer = document.getElementById('ajax-alert-container');

    // 1. Temperature Slider Live Update
    inputTemperature.addEventListener('input', function () {
        tempDisplay.textContent = parseFloat(this.value).toFixed(2);
    });

    // 2. Toggle API Key Visibility
    btnToggleApiKey.addEventListener('click', function () {
        if (inputApiKey.type === 'password') {
            inputApiKey.type = 'text';
            eyeIcon.className = 'bi bi-eye-slash';
        } else {
            inputApiKey.type = 'password';
            eyeIcon.className = 'bi bi-eye';
        }
    });

    // 3. Delete API Key button
    if (btnDeleteApiKey) {
        btnDeleteApiKey.addEventListener('click', function () {
            const currentProv = selectProvider.value;
            const provName = PROVIDER_DATA[currentProv]?.name || currentProv;
            if (!confirm(`Apakah Anda yakin ingin menghapus API Key tersimpan untuk ${provName} dari database?`)) {
                return;
            }

            const formData = new FormData();
            formData.append('csrf_token', csrfToken);
            formData.append('provider', currentProv);

            fetch(`${BASE_URL}/admin/ai-config/delete-key`, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    if (PROVIDER_DATA[currentProv]) {
                        PROVIDER_DATA[currentProv].has_api_key = false;
                        PROVIDER_DATA[currentProv].masked_api_key = '';
                        PROVIDER_DATA[currentProv].key_source = 'none';
                    }
                    inputApiKey.value = '';
                    inputDeleteApiKey.value = '1';
                    btnDeleteApiKey.classList.add('d-none');
                    apiKeyStatusBadge.innerHTML = '<span class="badge bg-warning-subtle text-warning border border-warning rounded-pill px-2 py-1 small"><i class="bi bi-exclamation-triangle-fill me-1"></i> Belum Dikonfigurasi</span>';
                    showToastAlert('success', data.message);
                } else {
                    showToastAlert('danger', data.message || 'Gagal menghapus API Key.');
                }
            })
            .catch(err => {
                showToastAlert('danger', 'Terjadi kesalahan jaringan: ' + err.message);
            });
        });
    }

    // 4. Reset Base URL to Provider Default
    btnResetBaseUrl.addEventListener('click', function () {
        const provKey = selectProvider.value;
        const defaultUrl = PROVIDER_DATA[provKey]?.default_base_url || '';
        inputBaseUrl.value = defaultUrl;
        showToastAlert('info', `Base URL direset ke default resmi: ${defaultUrl}`);
    });

    // 5. Populate and Sync Form with Selected Provider
    function switchProvider(provKey) {
        const provData = PROVIDER_DATA[provKey];
        if (!provData) return;

        // Sync Select & Hidden fields
        selectProvider.value = provKey;
        inputProviderHidden.value = provKey;
        inputDeleteApiKey.value = '0';

        // Update titles & badges
        formPanelTitle.textContent = `Form Konfigurasi: ${provData.name}`;
        providerBadgeName.textContent = provKey.toUpperCase();
        providerInfoDesc.innerHTML = `Atur kredensial API Key, pilihan model, dan parameter untuk <strong>${provData.name}</strong>. Lihat dokumentasi resmi: <a href="${provData.doc_url}" target="_blank" rel="noopener">${provData.doc_url}</a>`;

        // Update Visual Selection Cards
        document.querySelectorAll('.provider-option-card').forEach(card => {
            if (card.getAttribute('data-provider') === provKey) {
                card.classList.add('active-provider-card');
                card.style.borderColor = '#0d6efd';
                card.style.boxShadow = '0 0.25rem 0.75rem rgba(13, 110, 253, 0.15)';
            } else {
                card.classList.remove('active-provider-card');
                card.style.borderColor = '#dee2e6';
                card.style.boxShadow = 'none';
            }
        });

        // Update API Key Status
        inputApiKey.value = '';
        if (provData.has_api_key) {
            const srcLabel = provData.key_source === 'env' ? 'Environment Server (.env)' : 'Tersimpan di Database';
            apiKeyStatusBadge.innerHTML = `<span class="badge bg-success-subtle text-success border border-success rounded-pill px-2 py-1 small" title="Sumber: ${srcLabel}"><i class="bi bi-shield-lock-fill me-1"></i> Tersimpan (${provData.masked_api_key})</span>`;
            if (btnDeleteApiKey) btnDeleteApiKey.classList.remove('d-none');
        } else {
            apiKeyStatusBadge.innerHTML = `<span class="badge bg-warning-subtle text-warning border border-warning rounded-pill px-2 py-1 small"><i class="bi bi-exclamation-triangle-fill me-1"></i> Belum Dikonfigurasi</span>`;
            if (btnDeleteApiKey) btnDeleteApiKey.classList.add('d-none');
        }

        // Update Base URL
        inputBaseUrl.value = provData.saved_base_url || provData.default_base_url || '';
        inputBaseUrl.placeholder = provData.default_base_url || '';

        // Update Temperature
        const temp = provData.saved_temperature !== undefined ? provData.saved_temperature : provData.default_temperature;
        inputTemperature.value = temp;
        tempDisplay.textContent = parseFloat(temp).toFixed(2);

        // Update Max Tokens & Timeout
        inputMaxTokens.value = provData.saved_max_tokens || provData.default_max_tokens || 650;
        inputTimeout.value = provData.saved_timeout || provData.default_timeout || 20;

        // Render Model Options
        renderModelOptions(provKey, provData.curated_models, provData.saved_model);
    }

    // 6. Render Model Dropdown
    function renderModelOptions(provKey, modelsList, activeModelId) {
        selectModel.innerHTML = '';

        const optGroupOfficial = document.createElement('optgroup');
        optGroupOfficial.label = `Daftar Model Resmi ${PROVIDER_DATA[provKey]?.name || ''}`;

        let foundSelected = false;

        if (modelsList && modelsList.length > 0) {
            modelsList.forEach(m => {
                const opt = document.createElement('option');
                opt.value = m.id;
                opt.textContent = m.name + (m.badge ? ` (${m.badge})` : '');
                opt.setAttribute('data-desc', m.description || '');
                opt.setAttribute('data-badge', m.badge || '');

                if (m.id === activeModelId) {
                    opt.selected = true;
                    foundSelected = true;
                }
                optGroupOfficial.appendChild(opt);
            });
        }

        selectModel.appendChild(optGroupOfficial);

        // Custom Option
        const optGroupCustom = document.createElement('optgroup');
        optGroupCustom.label = 'Kustom / Input Lainnya';
        const optCustom = document.createElement('option');
        optCustom.value = 'custom';
        optCustom.textContent = '✍️ Ketik Model ID Manual (Kustom)...';

        if (!foundSelected && activeModelId) {
            optCustom.selected = true;
            customInput.value = activeModelId;
        } else {
            customInput.value = '';
        }

        optGroupCustom.appendChild(optCustom);
        selectModel.appendChild(optGroupCustom);

        updateModelDescription();
    }

    // 7. Update Model Description on Model Change
    function updateModelDescription() {
        if (selectModel.value === 'custom') {
            customWrapper.classList.remove('d-none');
            customInput.focus();
            modelDescText.innerHTML = '<span class="text-primary fw-semibold"><i class="bi bi-pencil me-1"></i> Mode Kustom: Masukkan ID model yang valid pada kolom di bawah.</span>';
        } else {
            customWrapper.classList.add('d-none');
            const selectedOpt = selectModel.options[selectModel.selectedIndex];
            if (selectedOpt) {
                const desc = selectedOpt.getAttribute('data-desc') || '';
                const badge = selectedOpt.getAttribute('data-badge') || '';
                let html = '';
                if (badge) {
                    html += `<span class="badge bg-primary-subtle text-primary me-2">${badge}</span>`;
                }
                html += desc ? `<span>${desc}</span>` : '<span>Model resmi terdaftar untuk provider ini.</span>';
                modelDescText.innerHTML = html;
            }
        }
    }

    selectModel.addEventListener('change', updateModelDescription);

    // 8. Visual Cards Click Handler
    document.querySelectorAll('.provider-option-card').forEach(card => {
        card.addEventListener('click', function () {
            const p = this.getAttribute('data-provider');
            if (p) switchProvider(p);
        });
    });

    // 9. Dropdown Provider Change Handler
    selectProvider.addEventListener('change', function () {
        switchProvider(this.value);
    });

    // 10. AJAX Handler for Fetching Models Live from API
    btnRefreshModels.addEventListener('click', function () {
        const provKey = selectProvider.value;
        const currentKey = inputApiKey.value.trim();
        const baseUrl = inputBaseUrl.value.trim();

        btnRefreshModels.disabled = true;
        refreshIcon.className = 'spinner-border spinner-border-sm me-1';
        refreshText.textContent = 'Mengambil daftar model...';

        const formData = new FormData();
        formData.append('csrf_token', csrfToken);
        formData.append('provider', provKey);
        formData.append('api_key', currentKey);
        formData.append('base_url', baseUrl);

        fetch(`${BASE_URL}/admin/ai-config/fetch-models`, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            btnRefreshModels.disabled = false;
            refreshIcon.className = 'bi bi-arrow-repeat me-1';
            refreshText.textContent = 'Muat Ulang Model dari API';

            if (data.success && data.models && data.models.length > 0) {
                // Update local cache
                if (PROVIDER_DATA[provKey]) {
                    PROVIDER_DATA[provKey].curated_models = data.models;
                }
                const activeModel = selectModel.value !== 'custom' ? selectModel.value : customInput.value;
                renderModelOptions(provKey, data.models, activeModel);
                showToastAlert('success', `Berhasil memuat ${data.count} model dari ${PROVIDER_DATA[provKey]?.name || provKey}.`);
            } else {
                showToastAlert('warning', 'Tidak dapat mengambil daftar model secara live dari API. Menggunakan daftar model terkurasi.');
            }
        })
        .catch(err => {
            btnRefreshModels.disabled = false;
            refreshIcon.className = 'bi bi-arrow-repeat me-1';
            refreshText.textContent = 'Muat Ulang Model dari API';
            showToastAlert('danger', 'Gagal menghubungi server untuk mengambil model: ' + err.message);
        });
    });

    // 11. AJAX Handler for Connection Test
    btnTest.addEventListener('click', function () {
        const provKey = selectProvider.value;
        const modelId = selectModel.value;
        const customId = customInput.value.trim();
        const targetModel = (modelId === 'custom') ? customId : modelId;

        if (!targetModel) {
            alert('Silakan pilih atau masukkan Model ID yang akan diuji.');
            if (modelId === 'custom') customInput.focus();
            return;
        }

        const apiKey = inputApiKey.value.trim();
        const baseUrl = inputBaseUrl.value.trim();
        const temp = inputTemperature.value;
        const maxTokens = inputMaxTokens.value;
        const timeout = inputTimeout.value;

        // Loading State
        btnTest.disabled = true;
        testSpinner.classList.remove('d-none');
        testIcon.classList.add('d-none');
        testBtnText.textContent = `Menguji ke ${PROVIDER_DATA[provKey]?.name || provKey}...`;

        resultCard.classList.remove('d-none');
        cardHeader.className = 'card-header py-3 px-4 d-flex align-items-center justify-content-between bg-light text-muted';
        resultTitle.textContent = `Menguji Model ${targetModel} (${provKey})...`;
        resultIcon.className = 'bi bi-arrow-repeat text-primary spinner-border spinner-border-sm';
        latencyBadge.className = 'badge bg-secondary rounded-pill px-3 py-1 font-semibold';
        latencyBadge.textContent = 'Menunggu respons...';
        outputBox.textContent = 'Mengirimkan pesan pengujian chat completions ke server backend...';
        timestampEl.textContent = 'Sedang berlangsung...';
        modelNameDisplay.textContent = targetModel;

        const formData = new FormData();
        formData.append('csrf_token', csrfToken);
        formData.append('provider', provKey);
        formData.append('model_id', modelId);
        formData.append('custom_model_id', customId);
        formData.append('api_key', apiKey);
        formData.append('base_url', baseUrl);
        formData.append('temperature', temp);
        formData.append('max_tokens', maxTokens);
        formData.append('timeout', timeout);

        fetch(`${BASE_URL}/admin/ai-config/test`, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            btnTest.disabled = false;
            testSpinner.classList.add('d-none');
            testIcon.classList.remove('d-none');
            testBtnText.textContent = 'Tes Koneksi';

            if (data.success) {
                cardHeader.className = 'card-header py-3 px-4 d-flex align-items-center justify-content-between bg-success-subtle text-success border-success';
                resultTitle.innerHTML = `<i class="bi bi-check-circle-fill me-1"></i> Pengujian Koneksi Berhasil! Model Siap Digunakan.`;
                resultIcon.className = 'bi bi-check-circle-fill text-success';
                latencyBadge.className = 'badge bg-success text-white rounded-pill px-3 py-1 font-semibold';
                latencyBadge.textContent = `${data.latency_ms} ms Latensi`;
                outputBox.textContent = data.output || '(Model memberikan respons kosong tetapi HTTP 200 OK)';
                outputBox.className = 'p-3 bg-light rounded-3 font-monospace small text-dark border border-success';

                showToastAlert('success', `Tes koneksi ${data.provider} (${data.model}) berhasil! Latensi: ${data.latency_ms} ms.`);
            } else {
                cardHeader.className = 'card-header py-3 px-4 d-flex align-items-center justify-content-between bg-danger-subtle text-danger border-danger';
                resultTitle.innerHTML = `<i class="bi bi-x-circle-fill me-1"></i> Pengujian Koneksi Gagal`;
                resultIcon.className = 'bi bi-x-circle-fill text-danger';
                latencyBadge.className = 'badge bg-danger text-white rounded-pill px-3 py-1 font-semibold';
                latencyBadge.textContent = data.latency_ms ? `${data.latency_ms} ms` : 'Gagal';
                outputBox.textContent = data.error || 'Terjadi kesalahan saat memanggil API provider AI.';
                outputBox.className = 'p-3 bg-light rounded-3 font-monospace small text-danger border border-danger';

                showToastAlert('danger', `Pengujian gagal: ${data.error}`);
            }

            timestampEl.textContent = data.tested_at || new Date().toLocaleString();
            modelNameDisplay.innerHTML = `Model: <code>${data.model}</code>`;
        })
        .catch(err => {
            btnTest.disabled = false;
            testSpinner.classList.add('d-none');
            testIcon.classList.remove('d-none');
            testBtnText.textContent = 'Tes Koneksi';

            cardHeader.className = 'card-header py-3 px-4 d-flex align-items-center justify-content-between bg-danger-subtle text-danger border-danger';
            resultTitle.textContent = 'Koneksi Terputus';
            outputBox.textContent = 'Gagal menghubungi server: ' + err.message;
            showToastAlert('danger', 'Koneksi ke server gagal: ' + err.message);
        });
    });

    // 12. Helper to display clean dismissible toasts/alerts
    function showToastAlert(type, message) {
        const icon = type === 'success' ? 'bi-check-circle-fill' : (type === 'danger' ? 'bi-exclamation-triangle-fill' : 'bi-info-circle-fill');
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show rounded-4 shadow-sm mb-4 border-0" role="alert">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi ${icon} fs-5"></i>
                    <div class="fw-semibold small">${message}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        ajaxAlertContainer.innerHTML = alertHtml;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // Initialize with current active provider
    switchProvider(CURRENT_ACTIVE_PROVIDER);
});
</script>

<style>
.provider-option-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08) !important;
}
.active-provider-card {
    border-color: #0d6efd !important;
    background-color: #f8fbff;
    box-shadow: 0 0.25rem 0.75rem rgba(13, 110, 253, 0.15) !important;
}
</style>
