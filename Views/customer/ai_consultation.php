<?php
$baseUrl = \Config\Env::get('APP_URL', 'http://localhost/Myjasagwuehhhhhhhhhhhhhhhhhhhhh');
$activeAi = \Services\Ai\AiManager::getActiveConfig();
$activeProvName = match($activeAi['provider'] ?? 'gemini') {
    'gemini'   => 'Google Gemini',
    'groq'     => 'Groq LPU',
    'openai'   => 'OpenAI',
    'deepseek' => 'DeepSeek AI',
    default    => ucfirst($activeAi['provider'] ?? 'AI')
};
?>

<div class="container-fluid px-lg-5 py-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
                <div>
                    <h3 class="mb-1 fw-bold">Konsultasi <span class="text-primary">AI</span></h3>
                    <p class="text-muted small mb-0">Diagnosis otomatis didukung oleh model AI <strong><?= htmlspecialchars($activeProvName) ?> (<?= htmlspecialchars($activeAi['model_id'] ?? '') ?>)</strong></p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-success-subtle text-success border border-success border-opacity-25 rounded-pill px-3 py-2 shadow-sm">
                        <i class="bi bi-cpu-fill me-1"></i> <?= htmlspecialchars($activeProvName) ?>: <code><?= htmlspecialchars($activeAi['model_id'] ?? '') ?></code>
                    </span>
                    <a href="<?= $baseUrl ?>/admin/ai-config" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm fw-semibold">
                        <i class="bi bi-sliders me-1"></i> Ganti Model AI
                    </a>
                </div>
            </div>

            <?php if (!empty($flash)): ?>
                <div class="alert alert-<?= htmlspecialchars($flash['type']) ?> alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    <?= htmlspecialchars($flash['message']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="row g-4">
                <!-- Consultation Area -->
                <div class="col-lg-8">
                    <!-- Chat Form Card -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-3"><i class="bi bi-chat-square-dots-fill text-primary me-2"></i>Mulai Diagnosis Baru</h5>
                            <p class="text-muted small mb-4">Ceritakan masalah peralatan Anda, AI kami akan menganalisis gejala dan memberikan diagnosis awal.</p>
                            
                            <form id="aiConsultationForm">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold text-secondary">Kategori Peralatan</label>
                                    <select class="form-select form-select-lg rounded-3" name="category_id" id="categoryId" required>
                                        <option value="" disabled selected>Pilih Kategori...</option>
                                        <?php if (!empty($categories)): ?>
                                            <?php foreach ($categories as $cat): ?>
                                                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                        <option value="0">Peralatan Lainnya</option>
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-semibold text-secondary">Jelaskan Gejala / Masalah</label>
                                    <textarea class="form-control rounded-3" name="question" id="question" rows="3" placeholder="Contoh: AC kamar saya mengeluarkan angin tapi tidak dingin sama sekali..." required minlength="4"></textarea>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg rounded-pill fw-semibold shadow-sm" id="btnDiagnose">
                                        <i class="bi bi-magic me-2"></i> Analisis Sekarang
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Diagnosis Result Area (Hidden initially) -->
                    <div id="resultArea" style="display: none;">
                        <div class="card border-0 shadow rounded-4 overflow-hidden mb-4 border-top border-4 border-primary">
                            <div class="card-header bg-white p-4 border-0 pb-0">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h4 class="fw-bold mb-0">Hasil Diagnosis AI</h4>
                                    <div id="urgencyBadge"></div>
                                </div>
                                <p class="text-muted small" id="diagnosisSummary"></p>
                            </div>
                            <div class="card-body p-4 pt-2">
                                
                                <!-- Safety Warning Alert -->
                                <div id="safetyWarningBox" class="alert alert-danger d-none flex-column gap-2 rounded-3 shadow-sm mb-4 border-0">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-exclamation-triangle-fill fs-3 me-3 text-danger"></i>
                                        <h6 class="mb-0 fw-bold text-danger">PERINGATAN DARURAT KESELAMATAN</h6>
                                    </div>
                                    <p class="mb-0 mt-2 small fw-medium" id="safetyWarningText"></p>
                                </div>

                                <div class="row g-4 mb-4">
                                    <div class="col-md-6">
                                        <div class="p-3 bg-light rounded-4 h-100">
                                            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-search text-warning me-2"></i>Kemungkinan Masalah</h6>
                                            <ul class="list-unstyled mb-0 small" id="possibleIssuesList"></ul>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 bg-light rounded-4 h-100">
                                            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-tools text-secondary me-2"></i>Kemungkinan Penyebab</h6>
                                            <ul class="list-unstyled mb-0 small" id="possibleCausesList"></ul>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4" id="checksSection">
                                    <h6 class="fw-bold text-dark mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i>Pemeriksaan Sederhana yang Aman</h6>
                                    <div class="list-group list-group-flush rounded-3 border" id="simpleChecksList"></div>
                                </div>

                                <div class="bg-primary-subtle p-3 rounded-4 mb-3 border border-primary border-opacity-25">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-info-circle-fill text-primary me-2"></i>
                                        <h6 class="fw-bold text-primary mb-0">Rekomendasi Layanan</h6>
                                    </div>
                                    <ul class="mb-0 small fw-medium text-dark" id="recommendationList"></ul>
                                </div>
                                
                                <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded-pill border">
                                    <span class="fw-semibold small"><i class="bi bi-person-workspace me-2"></i>Perlu bantuan Teknisi?</span>
                                    <span class="badge fs-6 rounded-pill px-3" id="needsTechBadge"></span>
                                </div>

                            </div>
                            <div class="card-footer bg-white border-0 p-4 pt-0 text-end">
                                <a href="<?= $baseUrl ?>/customer/requests/create" class="btn btn-primary rounded-pill px-4 shadow-sm" id="btnBookService">
                                    Pesan Teknisi Sekarang <i class="bi bi-arrow-right ms-2"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Interactive Chat Simulating Questions -->
                        <div id="aiChatQuestions" class="d-none mb-4">
                            <h6 class="fw-bold mb-3 text-secondary ps-2">AI Ingin Mengetahui Lebih Lanjut:</h6>
                            <div class="d-flex flex-column gap-3" id="chatBubbles">
                                <!-- Chat bubbles inserted here -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- History Area (Right Sidebar) -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-header bg-white border-0 p-4 pb-2">
                            <h6 class="fw-bold mb-0"><i class="bi bi-clock-history text-secondary me-2"></i>Riwayat Konsultasi</h6>
                        </div>
                        <div class="card-body p-4 pt-0">
                            <?php if (empty($consultations)): ?>
                                <div class="text-center text-muted p-4 border rounded-3 bg-light">
                                    <i class="bi bi-chat-square-text fs-2 mb-2 text-secondary opacity-50"></i>
                                    <p class="small mb-0">Belum ada riwayat konsultasi.</p>
                                </div>
                            <?php else: ?>
                                <div class="list-group list-group-flush border rounded-3">
                                    <?php foreach ($consultations as $c): ?>
                                        <div class="list-group-item px-3 py-3">
                                            <div class="d-flex justify-content-between align-items-start mb-1">
                                                <span class="badge bg-light text-dark border">
                                                    <?= htmlspecialchars($c['category']['name'] ?? 'Lainnya') ?>
                                                </span>
                                                <small class="text-muted" style="font-size: 0.75rem;">
                                                    <?= date('d M Y', strtotime($c['created_at'])) ?>
                                                </small>
                                            </div>
                                            <p class="mb-1 fw-medium text-dark small text-truncate" title="<?= htmlspecialchars($c['question']) ?>">
                                                <?= htmlspecialchars($c['question']) ?>
                                            </p>
                                            <p class="text-muted mb-0" style="font-size: 0.8rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                                <?= htmlspecialchars($c['answer']) ?>
                                            </p>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <div class="mt-3 text-center">
                                    <a href="<?= $baseUrl ?>/customer/ai-consultation/history" class="btn btn-sm btn-outline-secondary rounded-pill px-3">Lihat Semua Riwayat</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('aiConsultationForm');
    const btnDiagnose = document.getElementById('btnDiagnose');
    const resultArea = document.getElementById('resultArea');
    
    // UI Elements for Result
    const urgencyBadge = document.getElementById('urgencyBadge');
    const diagnosisSummary = document.getElementById('diagnosisSummary');
    const safetyWarningBox = document.getElementById('safetyWarningBox');
    const safetyWarningText = document.getElementById('safetyWarningText');
    const possibleIssuesList = document.getElementById('possibleIssuesList');
    const possibleCausesList = document.getElementById('possibleCausesList');
    const simpleChecksList = document.getElementById('simpleChecksList');
    const checksSection = document.getElementById('checksSection');
    const recommendationList = document.getElementById('recommendationList');
    const needsTechBadge = document.getElementById('needsTechBadge');
    
    // UI for Chat Questions
    const aiChatQuestions = document.getElementById('aiChatQuestions');
    const chatBubbles = document.getElementById('chatBubbles');

    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Reset state
        const originalBtnHtml = btnDiagnose.innerHTML;
        btnDiagnose.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Sedang Menganalisis...';
        btnDiagnose.disabled = true;
        resultArea.style.display = 'none';
        
        const formData = new FormData(form);
        
        try {
            const response = await fetch('<?= $baseUrl ?>/customer/ai-consultation', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest' // Penting untuk deteksi AJAX jika dibutuhkan
                }
            });
            
            const data = await response.json();
            
            if (data.status === 'success' && data.diagnosis) {
                renderDiagnosis(data.diagnosis);
                
                // Update Book Service Button Link
                const btnBookService = document.getElementById('btnBookService');
                if (btnBookService && data.consultation_id) {
                    btnBookService.href = `<?= $baseUrl ?>/customer/requests/create?consultation_id=${data.consultation_id}`;
                }

                resultArea.style.display = 'block';
                // Scroll to result smoothly
                resultArea.scrollIntoView({ behavior: 'smooth', block: 'start' });
                
                // Refresh the page history dynamically if we wanted to, or just leave it for next page load.
            } else {
                alert('Gagal: ' + (data.message || 'Terjadi kesalahan pada server AI.'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Gagal menghubungi server. Silakan coba lagi.');
        } finally {
            btnDiagnose.innerHTML = originalBtnHtml;
            btnDiagnose.disabled = false;
        }
    });

    function renderDiagnosis(diag) {
        // 1. Urgency Badge
        const urLevel = diag.urgency_level || 'Sedang';
        let badgeColor = 'bg-warning text-dark';
        if (urLevel === 'Tinggi') badgeColor = 'bg-danger text-white';
        if (urLevel === 'Rendah') badgeColor = 'bg-success text-white';
        urgencyBadge.innerHTML = `<span class="badge ${badgeColor} px-3 py-2 rounded-pill"><i class="bi bi-activity me-1"></i> Urgensi: ${urLevel}</span>`;

        // 2. Summary
        diagnosisSummary.textContent = diag.summary || '';

        // 3. Safety Warning
        if (diag.safety_warning) {
            safetyWarningBox.classList.remove('d-none');
            safetyWarningBox.classList.add('d-flex');
            safetyWarningText.textContent = diag.safety_warning;
            checksSection.style.display = 'none'; // Hide checks if dangerous
        } else {
            safetyWarningBox.classList.add('d-none');
            safetyWarningBox.classList.remove('d-flex');
            checksSection.style.display = 'block';
        }

        // 4. Issues & Causes
        renderList(possibleIssuesList, diag.possible_issues, 'bi-exclamation-circle text-warning');
        renderList(possibleCausesList, diag.possible_causes, 'bi-dot text-secondary');

        // 5. Simple Checks
        simpleChecksList.innerHTML = '';
        if (diag.simple_checks && diag.simple_checks.length > 0) {
            diag.simple_checks.forEach(check => {
                const li = document.createElement('div');
                li.className = 'list-group-item bg-transparent px-3 py-2 border-bottom-0 small';
                li.innerHTML = `<i class="bi bi-arrow-right-short text-success fs-5 me-1 align-middle"></i> ${check}`;
                simpleChecksList.appendChild(li);
            });
        } else {
            simpleChecksList.innerHTML = '<div class="list-group-item bg-transparent text-muted small">Tidak ada pemeriksaan manual yang aman.</div>';
        }

        // 6. Recommendations
        recommendationList.innerHTML = '';
        if (diag.service_recommendation && diag.service_recommendation.length > 0) {
            diag.service_recommendation.forEach(rec => {
                const li = document.createElement('li');
                li.className = 'mb-1';
                li.textContent = rec;
                recommendationList.appendChild(li);
            });
        } else {
            recommendationList.innerHTML = '<li>Pengecekan dan perbaikan umum oleh teknisi</li>';
        }

        // 7. Needs Technician
        const nt = (diag.needs_technician || 'Ya').toLowerCase();
        if (nt === 'ya') {
            needsTechBadge.className = 'badge bg-primary px-3 py-2 rounded-pill';
            needsTechBadge.innerHTML = 'Ya, Disarankan';
        } else {
            needsTechBadge.className = 'badge bg-secondary px-3 py-2 rounded-pill';
            needsTechBadge.innerHTML = 'Tidak Mutlak, Bisa Mandiri';
        }

        // 8. Interactive Chat Questions (mock)
        chatBubbles.innerHTML = '';
        if (diag.consultation_questions && diag.consultation_questions.length > 0) {
            aiChatQuestions.classList.remove('d-none');
            
            // Render each question as an AI chat bubble
            diag.consultation_questions.forEach(q => {
                const aiBubble = `
                    <div class="d-flex align-items-start gap-3 w-75">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px;">
                            <i class="bi bi-robot fs-6"></i>
                        </div>
                        <div class="bg-white border shadow-sm rounded-4 p-3 rounded-top-0 position-relative">
                            <p class="mb-0 small fw-medium text-dark">${q}</p>
                        </div>
                    </div>
                `;
                chatBubbles.innerHTML += aiBubble;
            });
            
        } else {
            aiChatQuestions.classList.add('d-none');
        }
    }

    function renderList(element, items, iconClass) {
        element.innerHTML = '';
        if (items && items.length > 0) {
            items.forEach(item => {
                const li = document.createElement('li');
                li.className = 'mb-2 pb-1 border-bottom border-light';
                li.innerHTML = `<i class="bi ${iconClass} me-2 align-middle"></i> ${item}`;
                element.appendChild(li);
            });
        } else {
            element.innerHTML = '<li class="text-muted small">Tidak terdeteksi.</li>';
        }
    }
});
</script>
