<?php
$baseUrl   = \Config\Env::get('APP_URL', 'http://localhost/Myjasagwuehhhhhhhhhhhhhhhhhhhhh');
$csrfToken = \Middleware\AuthMiddleware::generateCsrfToken();
?>

<div class="container-fluid px-lg-5 py-4">
    <?php if (!empty($flash)): ?>
        <div class="alert alert-<?= htmlspecialchars($flash['type']) ?> alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
            <i class="bi bi-info-circle-fill me-2"></i>
            <?= htmlspecialchars($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Hero Banner (Jasaku Theme) -->
    <div class="card border-0 text-white rounded-4 shadow-sm p-4 mb-4" style="background: linear-gradient(135deg, #A06138 0%, #8A4F27 100%);">
        <div class="row align-items-center gy-3">
            <div class="col-md-7">
                <span class="badge bg-white font-semibold mb-2 px-3 py-1 rounded-pill shadow-sm" style="color: #743E1F !important;">
                    <i class="bi bi-person-fill me-1" style="color: #9C5F34;"></i> Customer Portal
                </span>
                <h2 class="fw-bold mb-1">Selamat Datang, <?= htmlspecialchars($user['name']) ?>!</h2>
                <p class="text-white-50 mb-0 small"><i class="bi bi-geo-alt-fill me-1"></i><?= htmlspecialchars($user['address'] ?? 'Alamat belum diisi') ?></p>
            </div>
            <div class="col-md-5 text-md-end">
                <div class="d-flex flex-wrap gap-2 justify-content-md-end">
                    <a href="<?= $baseUrl ?>/customer/requests/create" class="btn btn-light font-semibold rounded-pill px-4 py-2 shadow-sm" style="color: #743E1F;">
                        <i class="bi bi-plus-circle-fill me-1" style="color: #9C5F34;"></i> Pesan Jasa
                    </a>
                    <button type="button" class="btn btn-outline-light font-semibold rounded-pill px-4 py-2"
                            data-bs-toggle="modal" data-bs-target="#aiDiagnosisModal">
                        <i class="bi bi-robot me-1"></i> Konsultasi AI
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards Row (Mockup Layout) -->
    <div class="row g-3 mb-4">
        <!-- Pesanan Aktif -->
        <div class="col-md-4">
            <div class="card bg-white border rounded-4 p-4 shadow-sm h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.5px;">Pesanan Aktif</div>
                        <div class="fs-1 fw-bold" style="color: #351D0E;"><?= $activeOrders ?></div>
                    </div>
                    <div class="d-flex align-items-center justify-content-center rounded-4" style="width: 58px; height: 58px; background-color: #F5ECE1; color: #8A4F27; font-size: 1.6rem;">
                        <i class="bi bi-gear-fill"></i>
                    </div>
                </div>
            </div>
        </div>
        <!-- Pesanan Pending -->
        <div class="col-md-4">
            <div class="card bg-white border rounded-4 p-4 shadow-sm h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.5px;">Pesanan Pending</div>
                        <div class="fs-1 fw-bold text-warning"><?= $pendingOrders ?></div>
                    </div>
                    <div class="d-flex align-items-center justify-content-center rounded-4" style="width: 58px; height: 58px; background-color: #FEF9C3; color: #D97706; font-size: 1.6rem;">
                        <i class="bi bi-clock-history"></i>
                    </div>
                </div>
            </div>
        </div>
        <!-- Pesanan Selesai -->
        <div class="col-md-4">
            <div class="card bg-white border rounded-4 p-4 shadow-sm h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.5px;">Pesanan Selesai</div>
                        <div class="fs-1 fw-bold text-success"><?= $completedOrders ?></div>
                    </div>
                    <div class="d-flex align-items-center justify-content-center rounded-4" style="width: 58px; height: 58px; background-color: #DCFCE7; color: #16A34A; font-size: 1.6rem;">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Cards Row (Mockup Layout) -->
    <div class="row g-3 mb-4">
        <!-- Pesan Jasa Perbaikan -->
        <div class="col-md-6" id="pesan-jasa">
            <div class="card border rounded-4 p-4 bg-white h-100 shadow-sm">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="d-flex align-items-center justify-content-center text-white rounded-3 fs-4" style="width: 54px; height: 54px; background-color: #9C5F34;">
                        <i class="bi bi-tools"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">Pesan Jasa Perbaikan</h5>
                        <p class="text-muted small mb-0">Pilih alat rumah tangga &amp; jadwalkan kunjungan teknisi</p>
                    </div>
                </div>
                <a href="<?= $baseUrl ?>/customer/requests/create" class="btn btn-outline-primary rounded-pill w-100 py-2 font-semibold">
                    <i class="bi bi-calendar-plus me-1"></i> Buat Pesanan Baru
                </a>
            </div>
        </div>
        <!-- Konsultasi AI Diagnosis -->
        <div class="col-md-6" id="konsultasi-ai">
            <div class="card border-0 rounded-4 p-4 h-100 text-white shadow-sm" style="background: linear-gradient(135deg, #8A4F27 0%, #743E1F 100%);">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="d-flex align-items-center justify-content-center rounded-3 fs-4" style="width: 54px; height: 54px; background-color: #FFFFFF; color: #743E1F;">
                        <i class="bi bi-cpu-fill"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0 text-white">Konsultasi AI Diagnosis</h5>
                        <p class="text-white-50 small mb-0">Tanyakan gejala kerusakan untuk analisis cepat &amp; akurat</p>
                    </div>
                </div>
                <button type="button" class="btn btn-light rounded-pill w-100 py-2 font-semibold shadow-sm" style="color: #743E1F;"
                        data-bs-toggle="modal" data-bs-target="#aiDiagnosisModal">
                    <i class="bi bi-chat-dots-fill me-1" style="color: #9C5F34;"></i> Mulai Diagnosis AI Sekarang
                </button>
            </div>
        </div>
    </div>

    <!-- Requests Table (Mockup Layout) -->
    <div id="riwayat-pesanan" class="card border rounded-4 shadow-sm overflow-hidden mb-4 bg-white">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
            <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-receipt me-2 text-primary"></i>Daftar Pesanan Perbaikan Saya</h5>
            <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2"><?= count($myRequests) ?> Total</span>
        </div>
        <?php if (empty($myRequests)): ?>
            <div class="card-body text-center py-5 text-muted">
                <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                <p class="mb-0">Belum ada pesanan perbaikan peralatan rumah tangga.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">No.</th>
                            <th>Layanan Jasa</th>
                            <th>Keluhan</th>
                            <th>Teknisi</th>
                            <th>Jadwal</th>
                            <th class="text-center">Status</th>
                            <th class="pe-4 text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($myRequests as $req): ?>
                            <?php
                                $serv = $requestModel->service((int)$req['service_id']);
                                $tech = $requestModel->technician($req['technician_id'] ? (int)$req['technician_id'] : null);
                                $paym = $requestModel->payment((int)$req['id']);
                                $isPaid = (!empty($paym) && $paym['payment_status'] === 'paid');
                            ?>
                            <tr>
                                <td class="fw-bold ps-4">#REQ-<?= $req['id'] ?></td>
                                <td class="fw-semibold text-dark"><?= htmlspecialchars($serv['name'] ?? 'Perbaikan Alat') ?></td>
                                <td><small class="text-muted"><?= htmlspecialchars($req['complaint']) ?></small></td>
                                <td>
                                    <?php if ($tech): ?>
                                        <span class="badge bg-warning-subtle text-dark border border-warning-subtle rounded-pill px-3 py-2">
                                            <i class="bi bi-person-wrench me-1"></i><?= htmlspecialchars($tech['name']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-2">Menunggu Teknisi</span>
                                    <?php endif; ?>
                                </td>
                                <td><small class="text-muted"><?= htmlspecialchars($req['schedule']) ?></small></td>
                                <td class="text-center">
                                    <?php
                                        $statusClass = match(strtolower($req['status'])) {
                                            'completed' => 'bg-success text-white',
                                            'repairing' => 'bg-warning text-dark',
                                            'cancelled' => 'bg-info text-white',
                                            'pending'   => 'bg-secondary text-white',
                                            default     => 'bg-primary text-white'
                                        };
                                    ?>
                                    <span class="badge <?= $statusClass ?> rounded-pill px-3 py-2 font-semibold">
                                        <?= htmlspecialchars(strtoupper($req['status'])) ?>
                                    </span>
                                </td>
                                <td class="pe-4 text-end">
                                    <div class="d-inline-flex gap-1 align-items-center">
                                        <?php if (!$isPaid && $req['status'] !== 'cancelled'): ?>
                                            <a href="<?= $baseUrl ?>/customer/requests/pay?id=<?= $req['id'] ?>" class="btn btn-sm btn-success rounded-pill font-semibold px-3" title="Bayar Sekarang">
                                                <i class="bi bi-wallet2 me-1"></i> Bayar
                                            </a>
                                        <?php elseif ($isPaid): ?>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1 small">
                                                <i class="bi bi-check-circle-fill me-1"></i> Lunas
                                            </span>
                                        <?php endif; ?>
                                        <a href="<?= $baseUrl ?>/customer/requests/detail?id=<?= $req['id'] ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                            Detail
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

<!-- AI DIAGNOSIS MODAL -->
<div class="modal fade" id="aiDiagnosisModal" tabindex="-1" aria-labelledby="aiDiagnosisModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-0 pb-0" style="background:linear-gradient(135deg, #9c5f34, #c47735);border-radius:1rem 1rem 0 0;">
                <div class="d-flex align-items-center gap-3 text-white w-100">
                    <div class="bg-white bg-opacity-20 rounded-3 p-2 fs-4"><i class="bi bi-cpu-fill"></i></div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0 text-white" id="aiDiagnosisModalLabel">AI Diagnosis Peralatan Rumah Tangga</h5>
                        <small class="text-white-50">Deskripsikan gejala kerusakan, AI akan menganalisis secara cepat</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <ul class="nav nav-pills nav-fill mb-4 bg-light rounded-3 p-1" id="aiTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active rounded-2 fw-semibold" id="tab-diagnose" data-bs-toggle="pill" data-bs-target="#pane-diagnose" type="button">
                            <i class="bi bi-search-heart me-1"></i> Diagnosis Baru
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-2 fw-semibold" id="tab-history" data-bs-toggle="pill" data-bs-target="#pane-history" type="button" onclick="loadAiHistory()">
                            <i class="bi bi-clock-history me-1"></i> Riwayat Konsultasi
                        </button>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="pane-diagnose" role="tabpanel">
                        <div class="alert alert-info-subtle border-info-subtle rounded-3 small d-flex gap-2 align-items-start mb-3">
                            <i class="bi bi-info-circle-fill text-info mt-1 flex-shrink-0"></i>
                            <span>Ceritakan <strong>peralatan yang bermasalah</strong> dan <strong>gejala yang Anda alami</strong>. Contoh: <em>"AC saya tidak dingin dan berbunyi bising sejak kemarin"</em></span>
                        </div>
                        <form id="aiDiagnosisForm">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Deskripsikan Keluhan / Gejala Kerusakan</label>
                                <textarea class="form-control rounded-3" id="aiQuestion" name="question" rows="4"
                                    placeholder="Contoh: Kulkas saya tidak dingin padahal listrik menyala, ada bunyi berdengung dari bagian belakang..." required minlength="5"></textarea>
                                <div class="form-text">Semakin detail deskripsi Anda, semakin akurat hasil diagnosis AI.</div>
                            </div>
                            <div class="mb-3">
                                <div class="form-label fw-semibold small">Pilih Cepat Gejala Umum:</div>
                                <div class="d-flex flex-wrap gap-2">
                                    <?php
                                    $quickTags = ['AC tidak dingin','AC berbunyi bising','AC bocor air','Kulkas tidak dingin','Kulkas berbunyi keras','Mesin cuci tidak berputar','TV mati total','TV layar bergaris','Dispenser air tidak panas','Kompor api kecil','Pompa air tidak keluar','Mesin cuci bocor'];
                                    foreach ($quickTags as $tag): ?>
                                        <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3"
                                                onclick="appendToQuestion('<?= htmlspecialchars($tag, ENT_QUOTES) ?>')">
                                            <?= htmlspecialchars($tag) ?>
                                        </button>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-2 rounded-pill fw-bold" id="btnDiagnose">
                                <i class="bi bi-robot me-1"></i> Analisis Sekarang
                            </button>
                        </form>
                        <div id="aiLoading" class="text-center py-5 d-none">
                            <div class="spinner-border text-primary mb-3" style="width:3rem;height:3rem;" role="status"></div>
                            <p class="fw-semibold text-muted mb-1">AI sedang menganalisis keluhan Anda...</p>
                            <small class="text-muted">Memproses data diagnosis peralatan</small>
                        </div>
                        <div id="aiResult" class="d-none mt-4"></div>
                    </div>
                    <div class="tab-pane fade" id="pane-history" role="tabpanel">
                        <div id="aiHistoryContent">
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-hourglass-split fs-1 d-block mb-2"></i>
                                <p>Memuat riwayat konsultasi...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function(){
    'use strict';
    const BASE = '<?= $baseUrl ?>';

    window.appendToQuestion = function(text) {
        const ta = document.getElementById('aiQuestion');
        ta.value = ta.value ? ta.value.trimEnd() + '. ' + text : text;
        ta.focus();
    };

    document.getElementById('aiDiagnosisForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const question = document.getElementById('aiQuestion').value.trim();
        if (question.length < 5) { alert('Mohon deskripsikan keluhan lebih detail.'); return; }

        const btn = document.getElementById('btnDiagnose');
        const loading = document.getElementById('aiLoading');
        const result = document.getElementById('aiResult');
        const form = document.getElementById('aiDiagnosisForm');

        btn.disabled = true;
        form.classList.add('d-none');
        loading.classList.remove('d-none');
        result.classList.add('d-none');

        try {
            const resp = await fetch(BASE + '/customer/ai-consultation', { method:'POST', body: new FormData(this) });
            const data = await resp.json();
            if (data.status === 'success') {
                renderResult(data.diagnosis, question);
                result.classList.remove('d-none');
            } else {
                result.innerHTML = `<div class="alert alert-danger rounded-3"><i class="bi bi-exclamation-triangle me-2"></i>${esc(data.message || 'Terjadi kesalahan.')}</div>`;
                result.classList.remove('d-none');
            }
        } catch(err) {
            result.innerHTML = `<div class="alert alert-danger rounded-3"><i class="bi bi-wifi-off me-2"></i>Gagal menghubungi server.</div>`;
            result.classList.remove('d-none');
        } finally {
            loading.classList.add('d-none');
            btn.disabled = false;
        }
    });

    function renderResult(d, question) {
        const urgColor = {'Darurat':'danger','Tinggi':'warning','Sedang':'info','Rendah':'success'}[d.urgency_level] || 'secondary';
        const urgIcon  = {'Darurat':'exclamation-octagon-fill','Tinggi':'exclamation-triangle-fill','Sedang':'info-circle-fill','Rendah':'check-circle-fill'}[d.urgency_level] || 'info-circle-fill';
        const causesHtml = (d.possible_causes||[]).map((c,i)=>`<li class="d-flex gap-2 mb-1"><span class="badge bg-danger-subtle text-danger rounded-circle fw-bold flex-shrink-0" style="width:20px;height:20px;line-height:20px">${i+1}</span><span class="small">${esc(c)}</span></li>`).join('');
        const recsHtml   = (d.recommendations||[]).map(r=>`<li class="d-flex gap-2 mb-2"><i class="bi bi-check2-circle text-success flex-shrink-0 mt-1"></i><span class="small">${esc(r)}</span></li>`).join('');
        const sympHtml   = (d.detected_symptoms||[]).map(s=>`<span class="badge bg-primary-subtle text-primary rounded-pill me-1">${esc(s)}</span>`).join('');
        document.getElementById('aiResult').innerHTML = `
        <div class="border rounded-4 overflow-hidden shadow-sm">
            <div class="p-3 d-flex align-items-center justify-content-between" style="background:linear-gradient(135deg, #9c5f34, #c47735);">
                <div class="text-white"><div class="fw-bold"><i class="bi bi-cpu-fill me-2"></i>Hasil Diagnosis AI</div><div class="small text-white-50">${esc(d.device_name)}</div></div>
                <span class="badge bg-${urgColor} rounded-pill px-3 py-2 fw-semibold"><i class="bi bi-${urgIcon} me-1"></i>Urgensi: ${esc(d.urgency_level)}</span>
            </div>
            <div class="p-4 bg-white">
                <div class="bg-light rounded-3 p-3 mb-3 small"><span class="fw-semibold text-muted">Keluhan Anda:</span><p class="mb-0 mt-1">"${esc(question)}"</p></div>
                <div class="mb-3"><div class="fw-semibold small text-muted text-uppercase mb-2">Gejala Terdeteksi</div><div>${sympHtml}</div></div>
                <div class="row g-3 mb-3">
                    <div class="col-md-6"><div class="card border-0 bg-danger-subtle rounded-3 h-100"><div class="card-body p-3"><div class="fw-semibold text-danger mb-2"><i class="bi bi-bug-fill me-1"></i>Kemungkinan Penyebab</div><ul class="list-unstyled mb-0">${causesHtml}</ul></div></div></div>
                    <div class="col-md-6"><div class="card border-0 bg-success-subtle rounded-3 h-100"><div class="card-body p-3"><div class="fw-semibold text-success mb-2"><i class="bi bi-lightbulb-fill me-1"></i>Rekomendasi Tindakan</div><ul class="list-unstyled mb-0">${recsHtml}</ul></div></div></div>
                </div>
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 p-3 bg-primary-subtle rounded-3">
                    <div><div class="small text-muted fw-semibold">Estimasi Biaya Perbaikan</div><div class="fw-bold text-primary fs-6">${esc(d.estimated_cost)}</div></div>
                    <a href="${BASE}/customer/requests/create" class="btn btn-primary rounded-pill px-4 fw-semibold shadow-sm"><i class="bi bi-calendar-plus me-1"></i>Pesan Teknisi Sekarang</a>
                </div>
                <div class="text-center mt-3"><button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" onclick="resetForm()"><i class="bi bi-arrow-counterclockwise me-1"></i>Diagnosis Ulang</button></div>
            </div>
        </div>`;
    }

    window.resetForm = function() {
        document.getElementById('aiDiagnosisForm').classList.remove('d-none');
        document.getElementById('aiResult').classList.add('d-none');
        document.getElementById('aiQuestion').value = '';
        document.getElementById('aiQuestion').focus();
    };

    window.loadAiHistory = async function() {
        const c = document.getElementById('aiHistoryContent');
        c.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 text-muted small">Memuat riwayat...</p></div>';
        try {
            const data = await (await fetch(BASE + '/customer/ai-consultation/history')).json();
            if (!data.history || !data.history.length) {
                c.innerHTML = `<div class="text-center py-5 text-muted"><i class="bi bi-chat-square-dots fs-1 d-block mb-2"></i><p>Belum ada riwayat konsultasi.</p><button class="btn btn-primary btn-sm rounded-pill" onclick="document.getElementById('tab-diagnose').click()"><i class="bi bi-robot me-1"></i>Mulai Konsultasi</button></div>`;
                return;
            }
            const items = data.history.map(h => {
                const ai = h.ai_result || {};
                const uc = {'Darurat':'danger','Tinggi':'warning','Sedang':'info','Rendah':'success'}[ai.urgency_level]||'secondary';
                const dt = new Date(h.created_at).toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit'});
                return `<div class="card border-0 bg-light rounded-3 mb-3"><div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="fw-semibold small">${esc(ai.device_name||'Peralatan')} &mdash; ${esc(ai.device_category||'')}</div>
                        <div class="d-flex gap-2 align-items-center"><span class="badge bg-${uc}-subtle text-${uc} rounded-pill small">${esc(ai.urgency_level||'-')}</span><span class="text-muted" style="font-size:.75rem">${dt}</span></div>
                    </div>
                    <p class="small text-muted fst-italic mb-2">"${esc(h.question)}"</p>
                    <p class="small mb-0">${esc((h.answer||'').substring(0,180)+(h.answer&&h.answer.length>180?'...':''))}</p>
                </div></div>`;
            }).join('');
            c.innerHTML = `<div class="mb-2 d-flex justify-content-between align-items-center"><h6 class="fw-bold mb-0">10 Konsultasi Terakhir</h6><span class="badge bg-primary-subtle text-primary rounded-pill">${data.history.length} konsultasi</span></div>${items}`;
        } catch(err) {
            c.innerHTML = `<div class="alert alert-danger rounded-3 small"><i class="bi bi-exclamation-triangle me-2"></i>Gagal memuat riwayat.</div>`;
        }
    };

    function esc(s) { return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
})();
</script>
