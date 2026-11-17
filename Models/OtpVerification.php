<?php

namespace Models;

use PDO;

class OtpVerification extends BaseModel
{
    protected string $table = 'otp_verifications';

    public const STATUS_PENDING  = 'pending';
    public const STATUS_VERIFIED = 'verified';
    public const STATUS_EXPIRED  = 'expired';
    public const STATUS_FAILED   = 'failed';

    public const ACTION_LOGIN        = 'login';
    public const ACTION_REGISTER     = 'register';
    public const ACTION_VERIFICATION = 'verification';

    public const MAX_ATTEMPTS   = 5;
    public const OTP_TTL        = 300; // 5 menit
    public const COOLDOWN_SECS  = 60;  // 60 detik
    public const MAX_REQ_PHONE  = 5;   // Max 5 OTP per 10 menit per nomor
    public const MAX_REQ_IP     = 10;  // Max 10 OTP per 10 menit per IP

    /**
     * Normalisasi nomor telepon ke format internasional Fonte (628xxxxxxxxxx)
     *
     * @param string $phone
     * @return string|null
     */
    public static function normalizePhone(string $phone): ?string
    {
        // Bersihkan semua karakter selain angka dan tanda plus
        $clean = preg_replace('/[^\d+]/', '', trim($phone));
        if (empty($clean)) {
            return null;
        }

        // Tangani awalan +62
        if (str_starts_with($clean, '+62')) {
            $clean = substr($clean, 1);
        } elseif (str_starts_with($clean, '0')) {
            // Awalan 08xxx diubah menjadi 628xxx
            $clean = '62' . substr($clean, 1);
        } elseif (str_starts_with($clean, '8')) {
            // Awalan 8xxx diubah menjadi 628xxx
            $clean = '62' . $clean;
        }

        // Cek validitas format nomor seluler Indonesia (628xxxxxxxx)
        // Panjang digit total: 10 sampai 15 digit
        if (preg_match('/^628[1-9][0-9]{6,11}$/', $clean)) {
            return $clean;
        }

        return null;
    }

    /**
     * Format tampilan nomor untuk pengguna (contoh: +62 812-3456-7890)
     *
     * @param string $phone
     * @return string
     */
    public static function formatDisplayPhone(string $phone): string
    {
        $normalized = self::normalizePhone($phone);
        if (!$normalized) {
            return htmlspecialchars($phone);
        }

        // Format: +62 812-xxxx-xxxx
        $sub = substr($normalized, 2); // 812...
        if (strlen($sub) >= 9) {
            $p1 = substr($sub, 0, 3);
            $p2 = substr($sub, 3, 4);
            $p3 = substr($sub, 7);
            return "+62 {$p1}-{$p2}-{$p3}";
        }

        return "+{$normalized}";
    }

    /**
     * Sensor nomor telepon untuk tampilan aman (contoh: +62 812-****-7890)
     *
     * @param string $phone
     * @return string
     */
    public static function maskPhone(string $phone): string
    {
        $normalized = self::normalizePhone($phone);
        if (!$normalized) {
            return '****';
        }

        $length = strlen($normalized);
        if ($length <= 6) {
            return substr($normalized, 0, 2) . '***' . substr($normalized, -1);
        }

        $prefix = substr($normalized, 0, 5); // misal 62812
        $suffix = substr($normalized, -3);   // 3 digit terakhir
        return "+{$prefix}****{$suffix}";
    }

    /**
     * Buat sesi OTP baru dengan hash aman
     *
     * @param string $phone Nomor ternormalisasi
     * @param string $otpCode Kode OTP plaintext (hanya digunakan untuk hash)
     * @param string $action
     * @param int|null $userId
     * @param string|null $ipAddress
     * @param string|null $userAgent
     * @param int $ttlSeconds
     * @return int|bool ID sesi OTP atau false jika gagal
     */
    public function createOtpSession(
        string $phone,
        string $otpCode,
        string $action = self::ACTION_LOGIN,
        ?int $userId = null,
        ?string $ipAddress = null,
        ?string $userAgent = null,
        int $ttlSeconds = self::OTP_TTL
    ): int|bool {
        if (!$this->db) {
            return false;
        }

        // Batalkan / kedaluwarsa sesi pending lama untuk nomor ini
        $this->invalidatePendingSessions($phone, $action);

        $otpHash = password_hash($otpCode, PASSWORD_BCRYPT);
        $expiresAt = date('Y-m-d H:i:s', time() + $ttlSeconds);

        $stmt = $this->db->prepare("
            INSERT INTO `{$this->table}` 
            (`phone`, `otp_hash`, `action`, `user_id`, `attempts`, `max_attempts`, `ip_address`, `user_agent`, `status`, `expires_at`)
            VALUES
            (:phone, :otp_hash, :action, :user_id, 0, :max_attempts, :ip_address, :user_agent, 'pending', :expires_at)
        ");

        $executed = $stmt->execute([
            'phone'        => $phone,
            'otp_hash'     => $otpHash,
            'action'       => $action,
            'user_id'      => $userId,
            'max_attempts' => self::MAX_ATTEMPTS,
            'ip_address'   => $ipAddress,
            'user_agent'   => $userAgent ? substr($userAgent, 0, 255) : null,
            'expires_at'   => $expiresAt
        ]);

        if ($executed) {
            return (int) $this->db->lastInsertId();
        }

        return false;
    }

    /**
     * Dapatkan sesi pending paling baru yang belum kedaluwarsa
     *
     * @param string $phone
     * @param string $action
     * @return array|null
     */
    public function getLatestPending(string $phone, string $action = self::ACTION_LOGIN): ?array
    {
        if (!$this->db) {
            return null;
        }

        $stmt = $this->db->prepare("
            SELECT * FROM `{$this->table}`
            WHERE `phone` = :phone 
              AND `action` = :action 
              AND `status` = 'pending' 
              AND `expires_at` > NOW()
            ORDER BY `id` DESC 
            LIMIT 1
        ");

        $stmt->execute([
            'phone'  => $phone,
            'action' => $action
        ]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Cek sisa detik cooldown pengiriman ulang OTP
     *
     * @param string $phone
     * @param string $action
     * @param int $cooldownSeconds
     * @return int Detik tersisa (0 jika cooldown selesai)
     */
    public function getSecondsUntilCooldown(string $phone, string $action = self::ACTION_LOGIN, int $cooldownSeconds = self::COOLDOWN_SECS): int
    {
        if (!$this->db) {
            return 0;
        }

        $stmt = $this->db->prepare("
            SELECT UNIX_TIMESTAMP(`created_at`) as created_ts 
            FROM `{$this->table}`
            WHERE `phone` = :phone 
              AND `action` = :action
            ORDER BY `id` DESC 
            LIMIT 1
        ");

        $stmt->execute([
            'phone'  => $phone,
            'action' => $action
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return 0;
        }

        $elapsed = time() - (int)$row['created_ts'];
        if ($elapsed < $cooldownSeconds) {
            return $cooldownSeconds - $elapsed;
        }

        return 0;
    }

    /**
     * Hitung jumlah permintaan OTP terkini berdasarkan nomor (Rate Limiting)
     *
     * @param string $phone
     * @param int $windowSeconds
     * @return int
     */
    public function countRecentRequestsByPhone(string $phone, int $windowSeconds = 600): int
    {
        if (!$this->db) {
            return 0;
        }

        $since = date('Y-m-d H:i:s', time() - $windowSeconds);
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM `{$this->table}`
            WHERE `phone` = :phone 
              AND `created_at` >= :since
        ");

        $stmt->execute([
            'phone' => $phone,
            'since' => $since
        ]);

        return (int) $stmt->fetchColumn();
    }

    /**
     * Hitung jumlah permintaan OTP terkini berdasarkan IP (Rate Limiting)
     *
     * @param string $ip
     * @param int $windowSeconds
     * @return int
     */
    public function countRecentRequestsByIp(string $ip, int $windowSeconds = 600): int
    {
        if (!$this->db || empty($ip)) {
            return 0;
        }

        $since = date('Y-m-d H:i:s', time() - $windowSeconds);
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM `{$this->table}`
            WHERE `ip_address` = :ip 
              AND `created_at` >= :since
        ");

        $stmt->execute([
            'ip'    => $ip,
            'since' => $since
        ]);

        return (int) $stmt->fetchColumn();
    }

    /**
     * Verifikasi kode OTP masukan pengguna
     *
     * @param int $sessionId
     * @param string $inputCode
     * @return array [success => bool, message => string, session => ?array]
     */
    public function verifyOtp(int $sessionId, string $inputCode): array
    {
        if (!$this->db) {
            return [
                'success' => false,
                'message' => 'Layanan database tidak tersedia. Silakan coba kembali.'
            ];
        }

        $session = $this->find($sessionId);
        if (!$session) {
            return [
                'success' => false,
                'message' => 'Sesi verifikasi OTP tidak ditemukan atau telah dibatalkan.'
            ];
        }

        // Cek status
        if ($session['status'] === self::STATUS_VERIFIED) {
            return [
                'success' => false,
                'message' => 'Kode OTP ini telah digunakan sebelumnya. Silakan minta kode baru.'
            ];
        }

        if ($session['status'] === self::STATUS_FAILED || (int)$session['attempts'] >= (int)$session['max_attempts']) {
            return [
                'success' => false,
                'message' => 'Batas percobaan verifikasi telah terlampaui. Silakan minta kode OTP baru.'
            ];
        }

        // Cek kedaluwarsa
        if (strtotime($session['expires_at']) <= time()) {
            $this->update($sessionId, ['status' => self::STATUS_EXPIRED]);
            return [
                'success' => false,
                'message' => 'Kode OTP telah kedaluwarsa. Silakan minta kode OTP baru.'
            ];
        }

        // Cek kesesuaian kode OTP dengan hash BCRYPT
        if (password_verify($inputCode, $session['otp_hash'])) {
            // Berhasil diverifikasi! Tandai status verified dan simpan verified_at
            $this->update($sessionId, [
                'status'      => self::STATUS_VERIFIED,
                'verified_at' => date('Y-m-d H:i:s')
            ]);

            // Jika user_id terasosiasi, update kolom phone_verified_at di tabel users
            if (!empty($session['user_id'])) {
                $userModel = new User();
                $userModel->update((int)$session['user_id'], [
                    'phone_verified_at' => date('Y-m-d H:i:s')
                ]);
            }

            return [
                'success' => true,
                'message' => 'Verifikasi nomor WhatsApp berhasil!',
                'session' => $session
            ];
        }

        // Gagal verifikasi: Tambah jumlah percobaan
        $newAttempts = (int)$session['attempts'] + 1;
        $maxAttempts = (int)$session['max_attempts'];
        $remaining = max(0, $maxAttempts - $newAttempts);

        $updateData = ['attempts' => $newAttempts];
        if ($newAttempts >= $maxAttempts) {
            $updateData['status'] = self::STATUS_FAILED;
        }
        $this->update($sessionId, $updateData);

        if ($remaining <= 0) {
            return [
                'success' => false,
                'message' => 'Kode OTP salah. Batas percobaan habis. Silakan kirim ulang kode OTP baru.'
            ];
        }

        return [
            'success' => false,
            'message' => "Kode OTP salah. Anda memiliki sisa {$remaining} kesempatan percobaan."
        ];
    }

    /**
     * Invalidate sesi pending lama untuk nomor dan action yang sama
     *
     * @param string $phone
     * @param string $action
     */
    public function invalidatePendingSessions(string $phone, string $action): void
    {
        if (!$this->db) {
            return;
        }

        $stmt = $this->db->prepare("
            UPDATE `{$this->table}` 
            SET `status` = 'expired'
            WHERE `phone` = :phone 
              AND `action` = :action 
              AND `status` = 'pending'
        ");

        $stmt->execute([
            'phone'  => $phone,
            'action' => $action
        ]);
    }
}
