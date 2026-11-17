<?php

namespace Controllers;

use Config\Database;
use Middleware\AuthMiddleware;
use Models\User;
use PDO;

class ProfileController extends BaseController
{
    private User $userModel;
    private ?PDO $db;

    public function __construct()
    {
        $this->userModel = new User();
        $this->db = Database::getConnection();
    }

    /**
     * Show Profile Page for Authenticated User (Customer, Technician, Admin)
     */
    public function show(): void
    {
        if (!AuthMiddleware::check()) {
            AuthMiddleware::setFlash('warning', 'Silakan login terlebih dahulu untuk mengakses profil Anda.');
            $this->redirectTo('/login');
            return;
        }

        $sessionUser = AuthMiddleware::user();
        $userId = (int)$sessionUser['id'];

        // Get fresh user data directly from database
        $user = $this->userModel->find($userId);
        if (!$user) {
            AuthMiddleware::setFlash('danger', 'Data akun tidak ditemukan.');
            $this->redirectTo('/logout');
            return;
        }

        // Calculate statistics based on role
        $stats = $this->getUserStats($userId, $user['role']);

        $this->render('profile/index', [
            'pageTitle'           => 'Profil Saya - SmartService AI',
            'user'                => $user,
            'stats'               => $stats,
            'csrfToken'           => AuthMiddleware::generateCsrfToken(),
            'flash'               => AuthMiddleware::getFlash(),
            'hideFloatingWidgets' => false,
        ]);
    }

    /**
     * Update Profile Information (Name, Phone, Address)
     */
    public function update(): void
    {
        if (!AuthMiddleware::check()) {
            $this->redirectTo('/login');
            return;
        }

        $csrfToken = $_POST['csrf_token'] ?? null;
        if (!AuthMiddleware::verifyCsrfToken($csrfToken)) {
            AuthMiddleware::setFlash('danger', 'Token CSRF kadaluarsa. Silakan ulangi kembali.');
            $this->redirectTo('/profile');
            return;
        }

        $sessionUser = AuthMiddleware::user();
        $userId = (int)$sessionUser['id'];

        $name    = trim($_POST['name'] ?? '');
        $phone   = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');

        // Validation
        if (strlen($name) < 3) {
            AuthMiddleware::setFlash('danger', 'Nama lengkap minimal 3 karakter.');
            $this->redirectTo('/profile');
            return;
        }

        if (!empty($phone) && !preg_match('/^[0-9\+\-\s]{8,20}$/', $phone)) {
            AuthMiddleware::setFlash('danger', 'Format nomor telepon/WhatsApp tidak valid.');
            $this->redirectTo('/profile');
            return;
        }

        try {
            $updateData = [
                'name'    => $name,
                'phone'   => $phone,
                'address' => $address,
            ];

            $updated = $this->userModel->update($userId, $updateData);

            if ($updated) {
                // Update session state
                $_SESSION['user']['name']    = $name;
                $_SESSION['user']['phone']   = $phone;
                $_SESSION['user']['address'] = $address;

                AuthMiddleware::setFlash('success', 'Profil Anda berhasil diperbarui!');
            } else {
                AuthMiddleware::setFlash('danger', 'Gagal memperbarui data profil ke database.');
            }
        } catch (\Throwable $e) {
            AuthMiddleware::setFlash('danger', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }

        $this->redirectTo('/profile');
    }

    /**
     * Change Account Password
     */
    public function changePassword(): void
    {
        if (!AuthMiddleware::check()) {
            $this->redirectTo('/login');
            return;
        }

        $csrfToken = $_POST['csrf_token'] ?? null;
        if (!AuthMiddleware::verifyCsrfToken($csrfToken)) {
            AuthMiddleware::setFlash('danger', 'Token CSRF kadaluarsa. Silakan coba kembali.');
            $this->redirectTo('/profile');
            return;
        }

        $sessionUser = AuthMiddleware::user();
        $userId = (int)$sessionUser['id'];
        $user = $this->userModel->find($userId);

        if (!$user) {
            AuthMiddleware::setFlash('danger', 'Akun tidak valid.');
            $this->redirectTo('/profile');
            return;
        }

        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword     = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (!password_verify($currentPassword, $user['password'])) {
            AuthMiddleware::setFlash('danger', 'Kata sandi saat ini tidak cocok.');
            $this->redirectTo('/profile');
            return;
        }

        if (strlen($newPassword) < 6) {
            AuthMiddleware::setFlash('danger', 'Kata sandi baru minimal harus 6 karakter.');
            $this->redirectTo('/profile');
            return;
        }

        if ($newPassword !== $confirmPassword) {
            AuthMiddleware::setFlash('danger', 'Konfirmasi kata sandi baru tidak cocok.');
            $this->redirectTo('/profile');
            return;
        }

        try {
            $hashed = password_hash($newPassword, PASSWORD_BCRYPT);
            $this->userModel->update($userId, ['password' => $hashed]);

            AuthMiddleware::setFlash('success', 'Kata sandi akun Anda berhasil diperbarui! Gunakan sandi baru saat login berikutnya.');
        } catch (\Throwable $e) {
            AuthMiddleware::setFlash('danger', 'Gagal mengubah kata sandi: ' . $e->getMessage());
        }

        $this->redirectTo('/profile');
    }

    /**
     * Helper to compute account statistics by role
     */
    private function getUserStats(int $userId, string $role): array
    {
        $stats = [];

        if (!$this->db) {
            return $stats;
        }

        try {
            if ($role === 'customer') {
                // Total Orders
                $stmt = $this->db->prepare("SELECT COUNT(*) FROM service_requests WHERE customer_id = :uid");
                $stmt->execute(['uid' => $userId]);
                $stats['total_orders'] = (int)$stmt->fetchColumn();

                // Active Orders
                $stmt = $this->db->prepare("SELECT COUNT(*) FROM service_requests WHERE customer_id = :uid AND status IN ('pending', 'assigned', 'accepted', 'on_the_way', 'repairing')");
                $stmt->execute(['uid' => $userId]);
                $stats['active_orders'] = (int)$stmt->fetchColumn();

                // Completed Orders
                $stmt = $this->db->prepare("SELECT COUNT(*) FROM service_requests WHERE customer_id = :uid AND status = 'completed'");
                $stmt->execute(['uid' => $userId]);
                $stats['completed_orders'] = (int)$stmt->fetchColumn();

                // AI Consultations
                $stmt = $this->db->prepare("SELECT COUNT(*) FROM ai_consultations WHERE customer_id = :uid");
                $stmt->execute(['uid' => $userId]);
                $stats['ai_consultations'] = (int)$stmt->fetchColumn();
            } elseif ($role === 'technician') {
                // Total Assigned Jobs
                $stmt = $this->db->prepare("SELECT COUNT(*) FROM service_requests WHERE technician_id = :uid");
                $stmt->execute(['uid' => $userId]);
                $stats['total_jobs'] = (int)$stmt->fetchColumn();

                // Active Jobs
                $stmt = $this->db->prepare("SELECT COUNT(*) FROM service_requests WHERE technician_id = :uid AND status IN ('assigned', 'accepted', 'on_the_way', 'repairing')");
                $stmt->execute(['uid' => $userId]);
                $stats['active_jobs'] = (int)$stmt->fetchColumn();

                // Completed Jobs
                $stmt = $this->db->prepare("SELECT COUNT(*) FROM service_requests WHERE technician_id = :uid AND status = 'completed'");
                $stmt->execute(['uid' => $userId]);
                $stats['completed_jobs'] = (int)$stmt->fetchColumn();
            } elseif ($role === 'admin') {
                // Total Users
                $stats['total_users'] = (int)$this->db->query("SELECT COUNT(*) FROM users")->fetchColumn();
                // Total Requests
                $stats['total_requests'] = (int)$this->db->query("SELECT COUNT(*) FROM service_requests")->fetchColumn();
                // Total Services
                $stats['total_services'] = (int)$this->db->query("SELECT COUNT(*) FROM services WHERE status = 'active'")->fetchColumn();
            }
        } catch (\Throwable $e) {
            // Graceful fallback
        }

        return $stats;
    }
}
