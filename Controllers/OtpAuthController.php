<?php

namespace Controllers;

use Config\Env;
use Middleware\AuthMiddleware;
use Models\OtpVerification;
use Models\User;
use Services\FonteService;
use Services\OtpSenderInterface;

class OtpAuthController extends BaseController
{
    private OtpVerification $otpModel;
    private User $userModel;
    private OtpSenderInterface $fonteService;

    public function __construct(?OtpSenderInterface $fonteService = null)
    {
        $this->otpModel     = new OtpVerification();
        $this->userModel    = new User();
        $this->fonteService = $fonteService ?? new FonteService();
    }

    /**
     * Tampilkan formulir permintaan OTP (Input nomor WhatsApp)
     */
    public function showPhoneForm(): void
    {
        if (AuthMiddleware::check()) {
            $user = AuthMiddleware::user();
            $this->redirectToDashboard($user['role']);
            return;
        }

        $action = strtolower(trim($_GET['action'] ?? 'login'));
        if (!in_array($action, [OtpVerification::ACTION_LOGIN, OtpVerification::ACTION_REGISTER])) {
            $action = OtpVerification::ACTION_LOGIN;
        }

        $role = strtolower(trim($_GET['role'] ?? 'customer'));

        $this->render('auth/otp-login', [
            'pageTitle'           => 'Autentikasi OTP WhatsApp - SmartService AI',
            'csrfToken'           => AuthMiddleware::generateCsrfToken(),
            'flash'               => AuthMiddleware::getFlash(),
            'action'              => $action,
            'role'                => $role,
            'fonteStatus'         => $this->fonteService->checkStatus(),
            'hideAuthButtons'     => false,
            'hideFloatingWidgets' => false,
        ]);
    }

    /**
     * Proses pengiriman kode OTP via API Fonte
     */
    public function sendOtp(): void
    {
        $isAjax = $this->isAjaxRequest();

        // 1. Validasi CSRF Token
        $csrfToken = $_POST['csrf_token'] ?? null;
        if (!AuthMiddleware::verifyCsrfToken($csrfToken)) {
            $msg = 'Token CSRF tidak valid. Silakan muat ulang halaman.';
            if ($isAjax) {
                $this->json(['success' => false, 'message' => $msg], 403);
            }
            AuthMiddleware::setFlash('danger', $msg);
            $this->redirectTo('/otp/login');
            return;
        }

        // Hapus token CSRF untuk mencegah replay
        unset($_SESSION['csrf_token']);

        $rawPhone = trim($_POST['phone'] ?? '');
        $action   = strtolower(trim($_POST['action'] ?? OtpVerification::ACTION_LOGIN));
        if (!in_array($action, [OtpVerification::ACTION_LOGIN, OtpVerification::ACTION_REGISTER])) {
            $action = OtpVerification::ACTION_LOGIN;
        }
        $role = strtolower(trim($_POST['role'] ?? 'customer'));

        // 2. Validasi & Normalisasi Nomor Telepon Indonesia
        $normalizedPhone = OtpVerification::normalizePhone($rawPhone);
        if (!$normalizedPhone) {
            $msg = 'Format nomor WhatsApp tidak valid. Gunakan nomor seluler Indonesia yang aktif (contoh: 081234567890 atau 6281234567890).';
            if ($isAjax) {
                $this->json(['success' => false, 'message' => $msg], 422);
            }
            AuthMiddleware::setFlash('danger', $msg);
            $this->redirectTo('/otp/login?action=' . $action . '&role=' . $role);
            return;
        }

        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

        // 3. Security: Rate Limiting & Cooldown
        $ipRequests = $this->otpModel->countRecentRequestsByIp($ipAddress, 600);
        if ($ipRequests >= OtpVerification::MAX_REQ_IP) {
            $msg = 'Terlalu banyak permintaan OTP dari jaringan Anda. Mohon tunggu 10 menit sebelum mencoba kembali.';
            if ($isAjax) {
                $this->json(['success' => false, 'message' => $msg], 429);
            }
            AuthMiddleware::setFlash('danger', $msg);
            $this->redirectTo('/otp/login');
            return;
        }

        $phoneRequests = $this->otpModel->countRecentRequestsByPhone($normalizedPhone, 600);
        if ($phoneRequests >= OtpVerification::MAX_REQ_PHONE) {
            $msg = 'Batas pengiriman OTP untuk nomor ini telah tercapai. Mohon tunggu 10 menit demi alasan keamanan.';
            if ($isAjax) {
                $this->json(['success' => false, 'message' => $msg], 429);
            }
            AuthMiddleware::setFlash('danger', $msg);
            $this->redirectTo('/otp/login');
            return;
        }

        $cooldown = $this->otpModel->getSecondsUntilCooldown($normalizedPhone, $action, OtpVerification::COOLDOWN_SECS);
        if ($cooldown > 0) {
            $msg = "Mohon tunggu {$cooldown} detik sebelum meminta pengiriman ulang kode OTP.";
            if ($isAjax) {
                $this->json(['success' => false, 'message' => $msg, 'cooldown' => $cooldown], 429);
            }
            AuthMiddleware::setFlash('warning', $msg);
            $this->redirectTo('/otp/verify');
            return;
        }

        // 4. Periksa akun jika ada
        $user = $this->userModel->findByPhone($normalizedPhone);
        if ($user && $user['status'] !== 'active') {
            $msg = 'Akun terkait nomor telepon ini berstatus nonaktif atau ditangguhkan. Silakan hubungi Administrator.';
            if ($isAjax) {
                $this->json(['success' => false, 'message' => $msg], 403);
            }
            AuthMiddleware::setFlash('danger', $msg);
            $this->redirectTo('/otp/login');
            return;
        }

        // 5. Generate Kode OTP 6-Digit Numerik Kriptografis Aman
        try {
            $otpCode = str_pad((string)random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
        } catch (\Exception $e) {
            $otpCode = str_pad((string)mt_rand(100000, 999999), 6, '0', STR_PAD_LEFT);
        }

        // 6. Simpan Hash Sesi OTP ke Database (Plaintext OTP TIDAK disimpan di database)
        $sessionId = $this->otpModel->createOtpSession(
            $normalizedPhone,
            $otpCode,
            $action,
            $user ? (int)$user['id'] : null,
            $ipAddress,
            $userAgent,
            OtpVerification::OTP_TTL
        );

        if (!$sessionId) {
            $msg = 'Terjadi kesalahan sistem saat membuat sesi verifikasi. Silakan coba kembali.';
            if ($isAjax) {
                $this->json(['success' => false, 'message' => $msg], 500);
            }
            AuthMiddleware::setFlash('danger', $msg);
            $this->redirectTo('/otp/login');
            return;
        }

        // 7. Kirim Pesan OTP via Fonte WhatsApp Gateway API
        $fonteResult = $this->fonteService->sendOtp($normalizedPhone, $otpCode, [
            'app_name'       => Env::get('APP_NAME', 'SmartService AI'),
            'action'         => $action,
            'expiry_minutes' => 5
        ]);

        if (!$fonteResult['success']) {
            // Tandai sesi gagal jika API gateway gagal
            $this->otpModel->update($sessionId, ['status' => OtpVerification::STATUS_FAILED]);

            $errorMsg = 'Gagal mengirim OTP via WhatsApp: ' . $fonteResult['message'];
            if ($isAjax) {
                $this->json(['success' => false, 'message' => $errorMsg], 502);
            }
            AuthMiddleware::setFlash('danger', $errorMsg);
            $this->redirectTo('/otp/login?action=' . $action . '&role=' . $role);
            return;
        }

        // 8. Simpan State OTP Pending di Session Pengguna
        $_SESSION['otp_pending'] = [
            'session_id'     => $sessionId,
            'phone'          => $normalizedPhone,
            'action'         => $action,
            'role'           => $role,
            'expires_at'     => time() + OtpVerification::OTP_TTL,
            'cooldown_until' => time() + OtpVerification::COOLDOWN_SECS
        ];

        $maskedPhone = OtpVerification::maskPhone($normalizedPhone);
        $successMsg = "Kode OTP 6-digit berhasil dikirimkan via WhatsApp ke {$maskedPhone}. Silakan periksa pesan masuk WhatsApp Anda.";

        if ($isAjax) {
            $this->json([
                'success'      => true,
                'message'      => $successMsg,
                'redirect_url' => rtrim(Env::get('APP_URL', ''), '/') . '/otp/verify'
            ]);
        }

        AuthMiddleware::setFlash('success', $successMsg);
        $this->redirectTo('/otp/verify');
    }

    /**
     * Tampilkan formulir input kode verifikasi OTP
     */
    public function showVerifyForm(): void
    {
        if (AuthMiddleware::check()) {
            $user = AuthMiddleware::user();
            $this->redirectToDashboard($user['role']);
            return;
        }

        $pending = $_SESSION['otp_pending'] ?? null;
        if (!$pending || empty($pending['session_id']) || empty($pending['phone'])) {
            AuthMiddleware::setFlash('info', 'Silakan masukkan nomor WhatsApp Anda terlebih dahulu.');
            $this->redirectTo('/otp/login');
            return;
        }

        $remainingExpiry   = max(0, (int)$pending['expires_at'] - time());
        $remainingCooldown = max(0, (int)($pending['cooldown_until'] ?? 0) - time());

        $this->render('auth/otp-verify', [
            'pageTitle'           => 'Verifikasi Kode OTP WhatsApp - SmartService AI',
            'csrfToken'           => AuthMiddleware::generateCsrfToken(),
            'flash'               => AuthMiddleware::getFlash(),
            'phone'               => $pending['phone'],
            'displayPhone'        => OtpVerification::formatDisplayPhone($pending['phone']),
            'maskedPhone'         => OtpVerification::maskPhone($pending['phone']),
            'action'              => $pending['action'] ?? OtpVerification::ACTION_LOGIN,
            'remainingExpiry'     => $remainingExpiry,
            'remainingCooldown'   => $remainingCooldown,
            'hideAuthButtons'     => false,
            'hideFloatingWidgets' => false,
        ]);
    }

    /**
     * Proses verifikasi kode OTP yang diinput pengguna
     */
    public function verifyOtp(): void
    {
        $isAjax = $this->isAjaxRequest();

        // 1. Validasi CSRF Token
        $csrfToken = $_POST['csrf_token'] ?? null;
        if (!AuthMiddleware::verifyCsrfToken($csrfToken)) {
            $msg = 'Token CSRF tidak valid. Silakan coba kembali.';
            if ($isAjax) {
                $this->json(['success' => false, 'message' => $msg], 403);
            }
            AuthMiddleware::setFlash('danger', $msg);
            $this->redirectTo('/otp/verify');
            return;
        }

        unset($_SESSION['csrf_token']);

        // 2. Periksa apakah ada sesi pending
        $pending = $_SESSION['otp_pending'] ?? null;
        if (!$pending || empty($pending['session_id'])) {
            $msg = 'Sesi OTP tidak valid atau telah berakhir. Silakan minta kode baru.';
            if ($isAjax) {
                $this->json(['success' => false, 'message' => $msg], 400);
            }
            AuthMiddleware::setFlash('warning', $msg);
            $this->redirectTo('/otp/login');
            return;
        }

        $sessionId = (int)$pending['session_id'];
        $phone     = $pending['phone'];
        $action    = $pending['action'] ?? OtpVerification::ACTION_LOGIN;

        // 3. Validasi Format Kode OTP (6 digit angka)
        $inputCode = trim($_POST['otp_code'] ?? '');
        $inputCode = preg_replace('/[^\d]/', '', $inputCode);

        if (strlen($inputCode) !== 6) {
            $msg = 'Kode OTP harus terdiri dari 6 digit angka.';
            if ($isAjax) {
                $this->json(['success' => false, 'message' => $msg], 422);
            }
            AuthMiddleware::setFlash('danger', $msg);
            $this->redirectTo('/otp/verify');
            return;
        }

        // 4. Verifikasi OTP via Model
        $verificationResult = $this->otpModel->verifyOtp($sessionId, $inputCode);

        if (!$verificationResult['success']) {
            $errorMsg = $verificationResult['message'];
            if ($isAjax) {
                $this->json(['success' => false, 'message' => $errorMsg], 400);
            }
            AuthMiddleware::setFlash('danger', $errorMsg);
            $this->redirectTo('/otp/verify');
            return;
        }

        // 5. Verifikasi Berhasil! Bersihkan sesi OTP pending
        unset($_SESSION['otp_pending']);

        // 6. Tangani Alur Login vs Registrasi
        $user = $this->userModel->findByPhone($phone);

        if ($user) {
            // USER DITEMUKAN: Lakukan Login Langsung
            if ($user['status'] !== 'active') {
                $msg = 'Akun Anda sedang nonaktif atau ditangguhkan. Hubungi Administrator.';
                if ($isAjax) {
                    $this->json(['success' => false, 'message' => $msg], 403);
                }
                AuthMiddleware::setFlash('warning', $msg);
                $this->redirectTo('/login');
                return;
            }

            // Regenerasi Session ID untuk Mencegah Session Fixation
            session_regenerate_id(true);

            // Simpan Data Pengguna ke Session
            $_SESSION['user'] = [
                'id'      => (int) $user['id'],
                'name'    => $user['name'],
                'email'   => $user['email'],
                'role'    => $user['role'],
                'phone'   => $user['phone'],
                'address' => $user['address']
            ];

            // Update status phone_verified_at di database
            $this->userModel->update((int)$user['id'], [
                'phone_verified_at' => date('Y-m-d H:i:s')
            ]);

            $roleLabel = match ($user['role']) {
                User::ROLE_CUSTOMER   => 'Customer (Pelanggan)',
                User::ROLE_TECHNICIAN => 'Mitra Teknisi',
                User::ROLE_ADMIN      => 'Administrator',
                default               => ucfirst($user['role'])
            };

            $welcomeMsg = "Selamat datang kembali, {$user['name']}! Login via WhatsApp OTP berhasil sebagai {$roleLabel}.";
            AuthMiddleware::setFlash('success', $welcomeMsg);

            $dashboardPath = match ($user['role']) {
                User::ROLE_ADMIN      => '/admin/dashboard',
                User::ROLE_TECHNICIAN => '/technician/dashboard',
                default               => '/customer/dashboard'
            };

            if ($isAjax) {
                $this->json([
                    'success'      => true,
                    'message'      => $welcomeMsg,
                    'redirect_url' => rtrim(Env::get('APP_URL', ''), '/') . $dashboardPath
                ]);
            }

            $this->redirectTo($dashboardPath);
            return;

        } else {
            // USER BELUM TERDAFTAR: Alihkan ke Registrasi Customer dengan status nomor terverifikasi
            $_SESSION['verified_registration'] = [
                'phone'       => $phone,
                'verified_at' => time()
            ];

            $msg = "Nomor WhatsApp " . OtpVerification::formatDisplayPhone($phone) . " berhasil diverifikasi! Silakan lengkapi formulir pendaftaran akun berikut.";
            AuthMiddleware::setFlash('success', $msg);

            $registerPath = '/register?role=customer';

            if ($isAjax) {
                $this->json([
                    'success'      => true,
                    'message'      => $msg,
                    'redirect_url' => rtrim(Env::get('APP_URL', ''), '/') . $registerPath
                ]);
            }

            $this->redirectTo($registerPath);
            return;
        }
    }

    /**
     * Kirim ulang kode OTP dengan proteksi cooldown
     */
    public function resendOtp(): void
    {
        $isAjax = $this->isAjaxRequest();

        $csrfToken = $_POST['csrf_token'] ?? null;
        if (!AuthMiddleware::verifyCsrfToken($csrfToken)) {
            $msg = 'Token CSRF tidak valid.';
            if ($isAjax) {
                $this->json(['success' => false, 'message' => $msg], 403);
            }
            AuthMiddleware::setFlash('danger', $msg);
            $this->redirectTo('/otp/verify');
            return;
        }

        unset($_SESSION['csrf_token']);

        $pending = $_SESSION['otp_pending'] ?? null;
        if (!$pending || empty($pending['phone'])) {
            $msg = 'Sesi OTP tidak ditemukan. Silakan masukkan nomor kembali.';
            if ($isAjax) {
                $this->json(['success' => false, 'message' => $msg], 400);
            }
            AuthMiddleware::setFlash('warning', $msg);
            $this->redirectTo('/otp/login');
            return;
        }

        $phone  = $pending['phone'];
        $action = $pending['action'] ?? OtpVerification::ACTION_LOGIN;

        // Cek cooldown
        $cooldown = $this->otpModel->getSecondsUntilCooldown($phone, $action, OtpVerification::COOLDOWN_SECS);
        if ($cooldown > 0) {
            $msg = "Mohon tunggu {$cooldown} detik sebelum meminta pengiriman ulang kode OTP.";
            if ($isAjax) {
                $this->json(['success' => false, 'message' => $msg, 'cooldown' => $cooldown], 429);
            }
            AuthMiddleware::setFlash('warning', $msg);
            $this->redirectTo('/otp/verify');
            return;
        }

        // Rate limiting check
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        if ($this->otpModel->countRecentRequestsByPhone($phone, 600) >= OtpVerification::MAX_REQ_PHONE) {
            $msg = 'Batas pengiriman OTP untuk nomor ini telah tercapai. Mohon tunggu 10 menit.';
            if ($isAjax) {
                $this->json(['success' => false, 'message' => $msg], 429);
            }
            AuthMiddleware::setFlash('danger', $msg);
            $this->redirectTo('/otp/verify');
            return;
        }

        // Generate OTP baru
        try {
            $newOtpCode = str_pad((string)random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
        } catch (\Exception $e) {
            $newOtpCode = str_pad((string)mt_rand(100000, 999999), 6, '0', STR_PAD_LEFT);
        }

        $user = $this->userModel->findByPhone($phone);
        $newSessionId = $this->otpModel->createOtpSession(
            $phone,
            $newOtpCode,
            $action,
            $user ? (int)$user['id'] : null,
            $ipAddress,
            $_SERVER['HTTP_USER_AGENT'] ?? null,
            OtpVerification::OTP_TTL
        );

        if (!$newSessionId) {
            $msg = 'Gagal membuat sesi OTP baru.';
            if ($isAjax) {
                $this->json(['success' => false, 'message' => $msg], 500);
            }
            AuthMiddleware::setFlash('danger', $msg);
            $this->redirectTo('/otp/verify');
            return;
        }

        // Kirim via Fonte
        $fonteResult = $this->fonteService->sendOtp($phone, $newOtpCode, [
            'app_name'       => Env::get('APP_NAME', 'SmartService AI'),
            'action'         => $action,
            'expiry_minutes' => 5
        ]);

        if (!$fonteResult['success']) {
            $this->otpModel->update($newSessionId, ['status' => OtpVerification::STATUS_FAILED]);
            $errorMsg = 'Gagal mengirim ulang OTP via WhatsApp: ' . $fonteResult['message'];
            if ($isAjax) {
                $this->json(['success' => false, 'message' => $errorMsg], 502);
            }
            AuthMiddleware::setFlash('danger', $errorMsg);
            $this->redirectTo('/otp/verify');
            return;
        }

        // Perbarui Session
        $_SESSION['otp_pending']['session_id']     = $newSessionId;
        $_SESSION['otp_pending']['expires_at']     = time() + OtpVerification::OTP_TTL;
        $_SESSION['otp_pending']['cooldown_until'] = time() + OtpVerification::COOLDOWN_SECS;

        $msg = 'Kode OTP baru berhasil dikirim ulang ke nomor WhatsApp Anda!';
        if ($isAjax) {
            $this->json([
                'success'           => true,
                'message'           => $msg,
                'cooldown_seconds'  => OtpVerification::COOLDOWN_SECS,
                'expiry_seconds'    => OtpVerification::OTP_TTL
            ]);
        }

        AuthMiddleware::setFlash('success', $msg);
        $this->redirectTo('/otp/verify');
    }

    /**
     * Batalkan sesi OTP dan ganti nomor telepon
     */
    public function cancelOtp(): void
    {
        if (isset($_SESSION['otp_pending']['session_id'])) {
            $this->otpModel->update((int)$_SESSION['otp_pending']['session_id'], [
                'status' => OtpVerification::STATUS_EXPIRED
            ]);
        }

        unset($_SESSION['otp_pending']);
        AuthMiddleware::setFlash('info', 'Sesi verifikasi sebelumnya telah dibatalkan. Silakan masukkan nomor baru.');
        $this->redirectTo('/otp/login');
    }

    /**
     * Endpoint API status koneksi Fonte Gateway
     */
    public function apiGatewayStatus(): void
    {
        $status = $this->fonteService->checkStatus();
        $this->json($status);
    }

    /**
     * Deteksi apakah request berasal dari AJAX / Fetch API
     */
    private function isAjaxRequest(): bool
    {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            return true;
        }
        if (!empty($_SERVER['HTTP_ACCEPT']) && str_contains(strtolower($_SERVER['HTTP_ACCEPT']), 'application/json')) {
            return true;
        }
        return false;
    }

    /**
     * Helper redirect ke dashboard berdasarkan role
     */
    private function redirectToDashboard(string $role): void
    {
        $baseUrl = rtrim(Env::get('APP_URL', ''), '/');
        $url = match ($role) {
            User::ROLE_ADMIN      => $baseUrl . '/admin/dashboard',
            User::ROLE_CUSTOMER   => $baseUrl . '/customer/dashboard',
            User::ROLE_TECHNICIAN => $baseUrl . '/technician/dashboard',
            default               => $baseUrl . '/'
        };

        header('Location: ' . $url);
        exit;
    }
}

