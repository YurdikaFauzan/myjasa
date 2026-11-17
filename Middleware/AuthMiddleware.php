<?php

namespace Middleware;

use Config\Env;

class AuthMiddleware
{
    /**
     * Get currently logged-in user data from session
     *
     * @return array|null
     */
    public static function user(): ?array
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['user']) && is_array($_SESSION['user'])) {
            return $_SESSION['user'];
        }

        return null;
    }

    /**
     * Check if user is logged in
     *
     * @return bool
     */
    public static function check(): bool
    {
        return self::user() !== null;
    }

    /**
     * Require user to be logged in
     *
     * @return array Returns user array if authenticated, otherwise redirects
     */
    public static function requireLogin(): array
    {
        if (!self::check()) {
            self::setFlash('danger', 'Akses ditolak. Silakan login terlebih dahulu untuk melanjutkan.');
            $baseUrl = Env::get('APP_URL', '');
            header('Location: ' . rtrim($baseUrl, '/') . '/login');
            exit;
        }

        return self::user();
    }

    /**
     * Require user to have specific role(s)
     *
     * @param string|array $allowedRoles
     * @return array
     */
    public static function requireRole(string|array $allowedRoles): array
    {
        $user = self::requireLogin();
        $roles = is_array($allowedRoles) ? $allowedRoles : [$allowedRoles];

        if (!in_array($user['role'], $roles)) {
            self::setFlash('danger', "Akses Ditolak (Unauthorized): Peran Anda ({$user['role']}) tidak diizinkan mengakses halaman tersebut.");

            // Redirect user to their own dashboard based on their role
            $baseUrl = rtrim(Env::get('APP_URL', ''), '/');
            $redirectUrl = match ($user['role']) {
                'admin'      => $baseUrl . '/admin/dashboard',
                'customer'   => $baseUrl . '/customer/dashboard',
                'technician' => $baseUrl . '/technician/dashboard',
                default      => $baseUrl . '/login'
            };

            header('Location: ' . $redirectUrl);
            exit;
        }

        return $user;
    }

    /**
     * Generate CSRF Token for Forms
     *
     * @return string
     */
    public static function generateCsrfToken(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    /**
     * Verify CSRF Token from POST request
     *
     * @param string|null $token
     * @return bool
     */
    public static function verifyCsrfToken(?string $token): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }

        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Set Flash Alert Message
     *
     * @param string $type (success, danger, warning, info)
     * @param string $message
     */
    public static function setFlash(string $type, string $message): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['flash'] = [
            'type'    => $type,
            'message' => $message
        ];
    }

    /**
     * Get and clear Flash Alert Message
     *
     * @return array|null
     */
    public static function getFlash(): ?array
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }

        return null;
    }
}
