<?php
$baseUrl = \Config\Env::get('APP_URL', 'http://localhost/Myjasagwuehhhhhhhhhhhhhhhhhhhhh');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Pendaftaran Administrator - SmartService AI') ?></title>
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        :root {
            --admin-bg-primary: #0a0f1d;
            --admin-bg-surface: rgba(17, 24, 39, 0.85);
            --admin-border: rgba(255, 255, 255, 0.1);
            --admin-accent: #3b82f6;
            --admin-accent-glow: rgba(59, 130, 246, 0.35);
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: radial-gradient(circle at 50% 20%, #172554 0%, #0a0f1d 70%);
            min-height: 100vh;
            color: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 16px;
            margin: 0;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.15) 0%, rgba(0, 0, 0, 0) 70%);
            top: -150px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 0;
            pointer-events: none;
        }

        .admin-register-wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 520px;
        }

        .admin-brand-header {
            text-align: center;
            margin-bottom: 24px;
        }

        .admin-badge-icon {
            width: 64px;
            height: 64px;
            border-radius: 20px;
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.85rem;
            color: #ffffff;
            box-shadow: 0 10px 25px -5px var(--admin-accent-glow), 0 0 0 1px rgba(255, 255, 255, 0.15);
            margin-bottom: 14px;
        }

        .admin-title {
            font-size: 1.5rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            color: #ffffff;
            margin-bottom: 4px;
        }

        .admin-subtitle {
            font-size: 0.85rem;
            color: #94a3b8;
            margin-bottom: 0;
        }

        .admin-card {
            background: var(--admin-bg-surface);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--admin-border);
            border-radius: 24px;
            padding: 32px 28px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(255, 255, 255, 0.05);
        }

        .form-label {
            font-size: 0.8rem;
            font-weight: 600;
            color: #cbd5e1;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .input-group-admin {
            position: relative;
            display: flex;
            align-items: center;
        }

        .admin-input {
            background: rgba(15, 23, 42, 0.75);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 14px !important;
            padding: 11px 14px 11px 42px;
            color: #ffffff;
            font-size: 0.92rem;
            width: 100%;
            transition: all 0.2s ease;
        }

        .admin-input:focus {
            background: rgba(15, 23, 42, 0.95);
            border-color: var(--admin-accent);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25);
            color: #ffffff;
            outline: none;
        }

        .admin-input::placeholder {
            color: #64748b;
        }

        .admin-input-icon {
            position: absolute;
            left: 15px;
            color: #64748b;
            font-size: 1.1rem;
            z-index: 2;
            pointer-events: none;
        }

        .admin-btn-submit {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #ffffff;
            font-weight: 700;
            font-size: 0.95rem;
            padding: 13px;
            border-radius: 14px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 10px 20px -5px var(--admin-accent-glow);
            transition: all 0.2s ease;
            cursor: pointer;
            margin-top: 14px;
        }

        .admin-btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 26px -5px rgba(59, 130, 246, 0.5);
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: #ffffff;
        }

        .admin-footer-links {
            text-align: center;
            margin-top: 24px;
            font-size: 0.85rem;
        }

        .admin-footer-links a {
            color: #94a3b8;
            text-decoration: none;
            transition: color 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .admin-footer-links a:hover {
            color: #ffffff;
        }
    </style>
</head>
<body>

    <div class="admin-register-wrapper">
        <!-- Brand Header -->
        <div class="admin-brand-header">
            <div class="admin-badge-icon">
                <i class="bi bi-shield-plus"></i>
            </div>
            <h1 class="admin-title">Daftar Administrator Baru</h1>
            <p class="admin-subtitle">Registrasi Akun Administrator SmartService AI</p>
        </div>

        <!-- Card Register Admin -->
        <div class="admin-card">
            <?php if (!empty($flash)): ?>
                <div class="alert alert-<?= htmlspecialchars($flash['type'] ?? 'info') ?> border-0 rounded-4 py-2 px-3 small d-flex align-items-center gap-2 mb-4" role="alert">
                    <i class="bi <?= ($flash['type'] ?? '') === 'danger' ? 'bi-exclamation-triangle-fill text-danger' : 'bi-info-circle-fill text-primary' ?> fs-5"></i>
                    <div><?= $flash['message'] ?? '' ?></div>
                </div>
            <?php endif; ?>

            <form action="<?= $baseUrl ?>/register/admin" method="POST" autocomplete="off">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">

                <!-- Nama Lengkap -->
                <div class="mb-3">
                    <label for="admin-name" class="form-label">
                        <i class="bi bi-person me-1"></i> Nama Lengkap Administrator
                    </label>
                    <div class="input-group-admin">
                        <i class="bi bi-person admin-input-icon"></i>
                        <input type="text" name="name" id="admin-name" class="admin-input" placeholder="Contoh: Admin Operasional" required autofocus>
                    </div>
                </div>

                <!-- Email Input -->
                <div class="mb-3">
                    <label for="admin-email" class="form-label">
                        <i class="bi bi-envelope-at me-1"></i> Email Administrator
                    </label>
                    <div class="input-group-admin">
                        <i class="bi bi-person-badge admin-input-icon"></i>
                        <input type="email" name="email" id="admin-email" class="admin-input" placeholder="admin.baru@smartservice.ai" required>
                    </div>
                </div>

                <!-- Password & Konfirmasi -->
                <div class="row g-2 mb-3">
                    <div class="col-sm-6">
                        <label for="admin-password" class="form-label">
                            <i class="bi bi-key me-1"></i> Password
                        </label>
                        <div class="input-group-admin">
                            <i class="bi bi-lock admin-input-icon"></i>
                            <input type="password" name="password" id="admin-password" class="admin-input" placeholder="Min. 6 karakter" required>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <label for="admin-confirm-password" class="form-label">
                            <i class="bi bi-shield-check me-1"></i> Konfirmasi
                        </label>
                        <div class="input-group-admin">
                            <i class="bi bi-lock-fill admin-input-icon"></i>
                            <input type="password" name="confirm_password" id="admin-confirm-password" class="admin-input" placeholder="Ulangi sandi" required>
                        </div>
                    </div>
                </div>

                <!-- Nomor Telepon -->
                <div class="mb-3">
                    <label for="admin-phone" class="form-label">
                        <i class="bi bi-telephone me-1"></i> Nomor HP / WhatsApp
                    </label>
                    <div class="input-group-admin">
                        <i class="bi bi-telephone admin-input-icon"></i>
                        <input type="tel" name="phone" id="admin-phone" class="admin-input" placeholder="0812xxxxxxxx">
                    </div>
                </div>

                <!-- Kode Otorisasi Admin (Security Key) -->
                <div class="mb-4">
                    <label for="admin-secret-key" class="form-label">
                        <i class="bi bi-shield-lock me-1"></i> Kunci Registrasi Administrator
                    </label>
                    <div class="input-group-admin">
                        <i class="bi bi-key-fill admin-input-icon"></i>
                        <input type="password" name="admin_secret_key" id="admin-secret-key" class="admin-input" placeholder="Default: admin123" required>
                    </div>
                    <div class="form-text small text-white-50 mt-1" style="font-size: 0.75rem;">
                        <i class="bi bi-info-circle me-1"></i> Keamanan: Kunci bawaan adalah <code>admin123</code> (atau sesuaikan dengan ADMIN_SECRET_KEY).
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="admin-btn-submit">
                    <i class="bi bi-person-check-fill"></i>
                    <span>Daftarkan Akun Administrator</span>
                </button>
            </form>
        </div>

        <!-- Footer Links -->
        <div class="admin-footer-links">
            <a href="<?= $baseUrl ?>/login/admin">
                <i class="bi bi-arrow-left"></i> Sudah punya akun? Masuk Login Admin
            </a>
            <span class="mx-2 text-muted">&bull;</span>
            <a href="<?= $baseUrl ?>">
                <i class="bi bi-house"></i> Beranda
            </a>
        </div>
    </div>

</body>
</html>

