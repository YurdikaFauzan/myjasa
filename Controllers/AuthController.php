<?php

namespace Controllers;

use Config\Env;
use Middleware\AuthMiddleware;
use Models\User;

class AuthController extends BaseController
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    /**
     * Render Login Form
    /**
     * Render Login Form (Supports Customer & Technician Portals)
     */
    public function showLogin(): void
    {
        if (AuthMiddleware::check()) {
            $user = AuthMiddleware::user();
            $this->redirectToDashboard($user['role']);
            return;
        }

        $roleParam = strtolower(trim($_GET['role'] ?? 'customer'));
        $activePortal = in_array($roleParam, ['technician', 'teknisi']) ? 'technician' : 'customer';

        $portalTitle = $activePortal === 'technician' ? 'Login Mitra Teknisi' : 'Login Customer';

        $this->render('auth/login', [
            'pageTitle'           => "{$portalTitle} - Jasaku SmartService AI",
            'csrfToken'           => AuthMiddleware::generateCsrfToken(),
            'flash'               => AuthMiddleware::getFlash(),
            'activePortal'        => $activePortal,
            'hideAuthButtons'     => true,
            'isWelcomePage'       => true,
            'hideFloatingWidgets' => false,
            'extraCss'            => ['welcome.css'],
            'extraJs'             => ['welcome.js']
        ]);
    }

    /**
     * Render Dedicated Customer Login Form
     */
    public function showCustomerLogin(): void
    {
        $_GET['role'] = 'customer';
        $this->showLogin();
    }

    /**
     * Render Dedicated Technician Login Form
     */
    public function showTechnicianLogin(): void
    {
        $_GET['role'] = 'technician';
        $this->showLogin();
    }

    /**
     * Render Dedicated Administrator Login Form
     */
    public function showAdminLogin(): void
    {
        if (AuthMiddleware::check()) {
            $user = AuthMiddleware::user();
            if ($user['role'] === 'admin') {
                $this->redirectTo('/admin/dashboard');
            } else {
                $this->redirectToDashboard($user['role']);
            }
            return;
        }

        $this->render('auth/login_admin', [
            'pageTitle'           => 'Portal Login Administrator - SmartService AI',
            'csrfToken'           => AuthMiddleware::generateCsrfToken(),
            'flash'               => AuthMiddleware::getFlash(),
            'hideAuthButtons'     => true,
            'isWelcomePage'       => true,
            'hideFloatingWidgets' => true,
        ]);
    }

    /**
     * Render Dedicated Administrator Register Form
     */
    public function showAdminRegister(): void
    {
        if (AuthMiddleware::check()) {
            $user = AuthMiddleware::user();
            if ($user['role'] === 'admin') {
                $this->redirectTo('/admin/dashboard');
            } else {
                $this->redirectToDashboard($user['role']);
            }
            return;
        }

        $this->render('auth/register_admin', [
            'pageTitle'           => 'Daftar Administrator Baru - SmartService AI',
            'csrfToken'           => AuthMiddleware::generateCsrfToken(),
            'flash'               => AuthMiddleware::getFlash(),
            'hideAuthButtons'     => true,
            'isWelcomePage'       => true,
            'hideFloatingWidgets' => true,
        ]);
    }

    /**
     * Process Administrator Register POST Request
     */
    public function registerAdmin(): void
    {
        $csrfToken = $_POST['csrf_token'] ?? null;
        if (!AuthMiddleware::verifyCsrfToken($csrfToken)) {
            AuthMiddleware::setFlash('danger', 'Token CSRF tidak valid. Silakan coba kembali.');
            $this->redirectTo('/register/admin');
            return;
        }

        unset($_SESSION['csrf_token']);

        $name            = trim($_POST['name'] ?? '');
        $email           = trim($_POST['email'] ?? '');
        $password        = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $phone           = trim($_POST['phone'] ?? '');
        $secretKey       = trim($_POST['admin_secret_key'] ?? '');

        // Validasi Kunci Otorisasi Admin
        $expectedKey = Env::get('ADMIN_SECRET_KEY', 'admin123');
        if ($secretKey !== $expectedKey) {
            AuthMiddleware::setFlash('danger', 'Kunci Otorisasi Pendaftaran Administrator salah. Hubungi pemilik sistem.');
            $this->redirectTo('/register/admin');
            return;
        }

        if (empty($name) || empty($email) || empty($password)) {
            AuthMiddleware::setFlash('danger', 'Nama, email, dan password wajib diisi.');
            $this->redirectTo('/register/admin');
            return;
        }

        if ($password !== $confirmPassword) {
            AuthMiddleware::setFlash('danger', 'Konfirmasi password tidak cocok.');
            $this->redirectTo('/register/admin');
            return;
        }

        if (strlen($password) < 6) {
            AuthMiddleware::setFlash('danger', 'Password minimal 6 karakter.');
            $this->redirectTo('/register/admin');
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            AuthMiddleware::setFlash('danger', 'Format email tidak valid.');
            $this->redirectTo('/register/admin');
            return;
        }

        $existing = $this->userModel->findBy('email', $email);
        if ($existing) {
            AuthMiddleware::setFlash('danger', 'Alamat email sudah terdaftar dalam sistem.');
            $this->redirectTo('/register/admin');
            return;
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $newUserId = $this->userModel->create([
            'name'     => $name,
            'email'    => $email,
            'password' => $hashedPassword,
            'role'     => User::ROLE_ADMIN,
            'phone'    => $phone ?: null,
            'address'  => 'Kantor Administrator SmartService AI',
            'status'   => 'active'
        ]);

        if ($newUserId) {
            AuthMiddleware::setFlash('success', 'Akun Administrator berhasil didaftarkan! Silakan masuk melalui formulir login di bawah.');
            $this->redirectTo('/login/admin');
        } else {
            AuthMiddleware::setFlash('danger', 'Gagal mendaftarkan akun Administrator.');
            $this->redirectTo('/register/admin');
        }
    }

    /**
     * Process Administrator Login POST Request
     */
    public function processAdminLogin(): void
    {
        $csrfToken = $_POST['csrf_token'] ?? null;
        if (!AuthMiddleware::verifyCsrfToken($csrfToken)) {
            AuthMiddleware::setFlash('danger', 'Token CSRF kadaluarsa. Silakan refresh halaman dan coba kembali.');
            $this->redirectTo('/login/admin');
            return;
        }

        unset($_SESSION['csrf_token']);

        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            AuthMiddleware::setFlash('danger', 'Email dan password Administrator wajib diisi.');
            $this->redirectTo('/login/admin');
            return;
        }

        $user = $this->userModel->findBy('email', $email);

        if (!$user || !password_verify($password, $user['password'])) {
            AuthMiddleware::setFlash('danger', 'Kombinasi email atau password Administrator salah.');
            $this->redirectTo('/login/admin');
            return;
        }

        if ($user['role'] !== 'admin') {
            AuthMiddleware::setFlash('danger', 'Akses ditolak! Halaman ini dikhususkan untuk Administrator. Akun Anda terdaftar sebagai ' . ucfirst($user['role']) . '.');
            $this->redirectTo('/login/admin');
            return;
        }

        if ($user['status'] !== 'active') {
            AuthMiddleware::setFlash('warning', 'Akun Administrator ini sedang dinonaktifkan.');
            $this->redirectTo('/login/admin');
            return;
        }

        session_regenerate_id(true);

        $_SESSION['user'] = [
            'id'      => (int) $user['id'],
            'name'    => $user['name'],
            'email'   => $user['email'],
            'role'    => $user['role'],
            'phone'   => $user['phone'],
            'address' => $user['address']
        ];

        AuthMiddleware::setFlash('success', "Selamat datang kembali, Administrator {$user['name']}! Berhasil masuk ke konsol kendali.");
        $this->redirectTo('/admin/dashboard');
    }

    /**
     * Process Login Form POST Request with Role Portal Verification
     */
    public function login(): void
    {
        $portal = strtolower(trim($_POST['login_portal'] ?? $_GET['role'] ?? 'customer'));
        if (!in_array($portal, ['customer', 'technician'])) {
            $portal = 'customer';
        }

        $csrfToken = $_POST['csrf_token'] ?? null;
        if (!AuthMiddleware::verifyCsrfToken($csrfToken)) {
            AuthMiddleware::setFlash('danger', 'Token CSRF tidak valid. Silakan coba kembali.');
            $this->redirectTo('/login?role=' . $portal);
            return;
        }

        // Hapus CSRF token lama agar tidak bisa di-replay
        unset($_SESSION['csrf_token']);

        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            AuthMiddleware::setFlash('danger', 'Email dan password wajib diisi.');
            $this->redirectTo('/login?role=' . $portal);
            return;
        }

        $user = $this->userModel->findBy('email', $email);

        if (!$user || !password_verify($password, $user['password'])) {
            AuthMiddleware::setFlash('danger', 'Kombinasi email atau password salah.');
            $this->redirectTo('/login?role=' . $portal);
            return;
        }

        if ($user['status'] !== 'active') {
            AuthMiddleware::setFlash('warning', 'Akun Anda sedang nonaktif atau ditangguhkan. Hubungi Admin.');
            $this->redirectTo('/login?role=' . $portal);
            return;
        }

        // Akun administrator harus masuk lewat portal khusus admin
        if ($user['role'] === 'admin') {
            AuthMiddleware::setFlash('info', "Akun '{$user['email']}' terdaftar sebagai Administrator. Silakan masuk melalui Portal Khusus Admin di bawah ini.");
            $this->redirectTo('/login/admin');
            return;
        }

        // Verifikasi kesesuaian portal role login
        if ($portal === 'customer' && $user['role'] === 'technician') {
            AuthMiddleware::setFlash('warning', "Akun '{$user['email']}' terdaftar sebagai Mitra Teknisi. Silakan masuk melalui tab 'Login Teknisi'.");
            $this->redirectTo('/login?role=technician');
            return;
        }

        if ($portal === 'technician' && $user['role'] === 'customer') {
            AuthMiddleware::setFlash('warning', "Akun '{$user['email']}' terdaftar sebagai Customer. Silakan masuk melalui tab 'Login Customer'.");
            $this->redirectTo('/login?role=customer');
            return;
        }

        // Simpan flash sebelum regenerate agar tidak hilang
        $roleLabel = match($user['role']) {
            'customer'   => 'Customer (Pelanggan)',
            'technician' => 'Mitra Teknisi',
            'admin'      => 'Administrator',
            default      => ucfirst($user['role'])
        };
        $flashMessage = "Selamat datang kembali, {$user['name']}! Anda berhasil masuk sebagai {$roleLabel}.";

        // Regenerate Session ID for Security (hapus session lama)
        session_regenerate_id(true);

        $_SESSION['user'] = [
            'id'      => (int) $user['id'],
            'name'    => $user['name'],
            'email'   => $user['email'],
            'role'    => $user['role'],
            'phone'   => $user['phone'],
            'address' => $user['address']
        ];

        AuthMiddleware::setFlash('success', $flashMessage);
        $this->redirectToDashboard($user['role']);
    }

    /**
     * Render Register Form (Customer & Technician)
     */
    public function showRegister(): void
    {
        if (AuthMiddleware::check()) {
            $user = AuthMiddleware::user();
            $this->redirectToDashboard($user['role']);
            return;
        }

        $roleParam = strtolower(trim($_GET['role'] ?? 'customer'));
        $activeRole = in_array($roleParam, ['technician', 'teknisi']) ? 'technician' : 'customer';

        $titleRole = $activeRole === 'technician' ? 'Pendaftaran Mitra Teknisi' : 'Registrasi Akun Customer';

        $verifiedPhone = $_SESSION['verified_registration']['phone'] ?? null;

        $this->render('auth/register', [
            'pageTitle'     => "{$titleRole} - SmartService AI",
            'csrfToken'     => AuthMiddleware::generateCsrfToken(),
            'flash'         => AuthMiddleware::getFlash(),
            'activeRole'    => $activeRole,
            'verifiedPhone' => $verifiedPhone
        ]);
    }

    /**
     * Render Customer Register Form
     */
    public function showCustomerRegister(): void
    {
        $_GET['role'] = 'customer';
        $this->showRegister();
    }

    /**
     * Render Technician Register Form
     */
    public function showTechnicianRegister(): void
    {
        $_GET['role'] = 'technician';
        $this->showRegister();
    }

    /**
     * Process Register Form POST Request
     */
    public function register(): void
    {
        $role = strtolower(trim($_POST['role'] ?? 'customer'));
        if (!in_array($role, [User::ROLE_CUSTOMER, User::ROLE_TECHNICIAN])) {
            $role = User::ROLE_CUSTOMER;
        }

        $csrfToken = $_POST['csrf_token'] ?? null;
        if (!AuthMiddleware::verifyCsrfToken($csrfToken)) {
            AuthMiddleware::setFlash('danger', 'Token CSRF tidak valid.');
            $this->redirectTo('/register?role=' . $role);
            return;
        }

        // Hapus CSRF token lama agar tidak bisa di-replay
        unset($_SESSION['csrf_token']);

        $name            = trim($_POST['name'] ?? '');
        $email           = trim($_POST['email'] ?? '');
        $password        = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $phone           = trim($_POST['phone'] ?? '');
        $address         = trim($_POST['address'] ?? '');

        // Validation Checks
        $errors = $this->userModel->validate([
            'name'     => $name,
            'email'    => $email,
            'password' => $password,
            'role'     => $role
        ]);

        if ($password !== $confirmPassword) {
            $errors['confirm_password'] = 'Konfirmasi password tidak cocok dengan password.';
        }

        if (!empty($errors)) {
            $firstError = reset($errors);
            AuthMiddleware::setFlash('danger', $firstError);
            $this->redirectTo('/register?role=' . $role);
            return;
        }

        // Hash Password with BCRYPT
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Periksa apakah nomor telepon telah diverifikasi sebelumnya via OTP Fonte
        $phoneVerifiedAt = null;
        if (!empty($_SESSION['verified_registration'])) {
            $verifiedPhone = $_SESSION['verified_registration']['phone'] ?? '';
            $normInput = \Models\OtpVerification::normalizePhone($phone);
            if ($verifiedPhone && $normInput === $verifiedPhone) {
                $phoneVerifiedAt = date('Y-m-d H:i:s');
                unset($_SESSION['verified_registration']);
            }
        }

        // Create User with selected role (Customer or Technician)
        $newUserId = $this->userModel->create([
            'name'              => $name,
            'email'             => $email,
            'password'          => $hashedPassword,
            'role'              => $role,
            'phone'             => $phone,
            'phone_verified_at' => $phoneVerifiedAt,
            'address'           => $address,
            'status'            => 'active'
        ]);

        if ($newUserId) {
            if ($role === User::ROLE_TECHNICIAN) {
                AuthMiddleware::setFlash('success', 'Registrasi Akun Mitra Teknisi berhasil! Silakan login melalui portal Login Teknisi.');
                $this->redirectTo('/login?role=technician');
            } else {
                AuthMiddleware::setFlash('success', 'Registrasi Akun Customer berhasil! Silakan login melalui portal Login Customer.');
                $this->redirectTo('/login?role=customer');
            }
        } else {
            AuthMiddleware::setFlash('danger', 'Gagal mendaftarkan akun. Silakan coba beberapa saat lagi.');
            $this->redirectTo('/register?role=' . $role);
        }
    }

    /**
     * Process Logout Request
     */
    public function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Bersihkan data session pengguna
        $_SESSION = [];

        // Hapus cookie session di browser
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        // Hancurkan session lama
        session_destroy();

        // Mulai session baru untuk menyimpan flash message
        session_start();

        $_SESSION['flash'] = [
            'type'    => 'info',
            'message' => 'Anda telah berhasil logout dari sistem.'
        ];

        $this->redirectTo('/login');
    }

    /**
     * Render Forgot Password Form
     */
    public function showForgotPassword(): void
    {
        if (AuthMiddleware::check()) {
            $user = AuthMiddleware::user();
            $this->redirectToDashboard($user['role']);
            return;
        }

        $this->render('auth/forgot-password', [
            'pageTitle' => 'Lupa Sandi - SmartService AI',
            'csrfToken' => AuthMiddleware::generateCsrfToken(),
            'flash'     => AuthMiddleware::getFlash()
        ]);
    }

    /**
     * Process Forgot Password
     */
    public function processForgotPassword(): void
    {
        $csrfToken = $_POST['csrf_token'] ?? null;
        if (!AuthMiddleware::verifyCsrfToken($csrfToken)) {
            AuthMiddleware::setFlash('danger', 'Token CSRF tidak valid.');
            $this->redirectTo('/forgot-password');
            return;
        }

        unset($_SESSION['csrf_token']);

        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($email) || empty($phone) || empty($password) || empty($confirmPassword)) {
            AuthMiddleware::setFlash('danger', 'Semua kolom wajib diisi.');
            $this->redirectTo('/forgot-password');
            return;
        }

        if ($password !== $confirmPassword) {
            AuthMiddleware::setFlash('danger', 'Konfirmasi password tidak cocok.');
            $this->redirectTo('/forgot-password');
            return;
        }

        if (strlen($password) < 6) {
            AuthMiddleware::setFlash('danger', 'Password minimal 6 karakter.');
            $this->redirectTo('/forgot-password');
            return;
        }

        $user = $this->userModel->findBy('email', $email);

        if (!$user) {
            AuthMiddleware::setFlash('danger', 'Email atau Nomor HP tidak ditemukan/cocok.');
            $this->redirectTo('/forgot-password');
            return;
        }

        if ($user['phone'] !== $phone) {
            AuthMiddleware::setFlash('danger', 'Email atau Nomor HP tidak ditemukan/cocok.');
            $this->redirectTo('/forgot-password');
            return;
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        
        $updated = $this->userModel->update((int)$user['id'], [
            'password' => $hashedPassword
        ]);

        if ($updated) {
            AuthMiddleware::setFlash('success', 'Password berhasil direset. Silakan login dengan password baru.');
            $this->redirectTo('/login');
        } else {
            AuthMiddleware::setFlash('danger', 'Terjadi kesalahan saat mereset password.');
            $this->redirectTo('/forgot-password');
        }
    }

    /**
     * Helper to redirect by role
     */
    private function redirectToDashboard(string $role): void
    {
        $baseUrl = rtrim(Env::get('APP_URL', ''), '/');
        $url = match ($role) {
            'admin'      => $baseUrl . '/admin/dashboard',
            'customer'   => $baseUrl . '/customer/dashboard',
            'technician' => $baseUrl . '/technician/dashboard',
            default      => $baseUrl . '/'
        };

        header('Location: ' . $url);
        exit;
    }
}


