<?php

namespace Models;

class User extends BaseModel
{
    protected string $table = 'users';

    public const ROLE_ADMIN      = 'admin';
    public const ROLE_CUSTOMER   = 'customer';
    public const ROLE_TECHNICIAN = 'technician';

    public const STATUS_ACTIVE    = 'active';
    public const STATUS_INACTIVE  = 'inactive';
    public const STATUS_SUSPENDED = 'suspended';

    /**
     * Get available user roles
     *
     * @return array
     */
    public static function getAvailableRoles(): array
    {
        return [
            self::ROLE_ADMIN,
            self::ROLE_CUSTOMER,
            self::ROLE_TECHNICIAN
        ];
    }

    /**
     * Get role card metadata for views
     *
     * @return array
     */
    public static function getRoleCards(): array
    {
        return [
            [
                'id'          => self::ROLE_CUSTOMER,
                'name'        => 'Customer',
                'badge'       => 'bg-primary',
                'icon'        => 'bi bi-person-heart',
                'description' => 'Pelanggan yang membutuhkan jasa perbaikan / perawatan peralatan rumah tangga seperti AC, Kulkas, Mesin Cuci, dll.'
            ],
            [
                'id'          => self::ROLE_TECHNICIAN,
                'name'        => 'Technician (Teknisi)',
                'badge'       => 'bg-warning text-dark',
                'icon'        => 'bi bi-tools',
                'description' => 'Mitra penyedia jasa perbaikan profesional yang menerima pesanan layanan dan melakukan perbaikan lokasi.'
            ],
            [
                'id'          => self::ROLE_ADMIN,
                'name'        => 'Admin',
                'badge'       => 'bg-dark',
                'icon'        => 'bi bi-shield-lock-fill',
                'description' => 'Pengelola sistem yang memantau pengguna, jenis layanan, transaksi, dan performa teknisi secara menyeluruh.'
            ]
        ];
    }


    /**
     * Data Validation for User Model
     *
     * @param array $data
     * @param int|null $userId (for unique email check on update)
     * @return array Returns array of error messages, empty if valid
     */
    public function validate(array $data, ?int $userId = null): array
    {
        $errors = [];

        // Name Validation
        if (empty($data['name']) || strlen(trim($data['name'])) < 3) {
            $errors['name'] = 'Nama lengkap minimal 3 karakter.';
        }

        // Email Validation
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Format alamat email tidak valid.';
        } else {
            // Check Unique Email
            $existing = $this->findBy('email', $data['email']);
            if ($existing && ($userId === null || (int)$existing['id'] !== $userId)) {
                $errors['email'] = 'Alamat email sudah terdaftar dalam sistem.';
            }
        }

        // Password Validation (if provided or new user)
        if ($userId === null && empty($data['password'])) {
            $errors['password'] = 'Password wajib diisi.';
        } elseif (!empty($data['password']) && strlen($data['password']) < 6) {
            $errors['password'] = 'Password minimal 6 karakter.';
        }

        // Role Validation
        if (!empty($data['role']) && !in_array($data['role'], self::getAvailableRoles())) {
            $errors['role'] = 'Role pengguna tidak valid.';
        }

        return $errors;
    }

    /**
     * Relationship: User has many ServiceRequests (as customer)
     *
     * @param int $userId
     * @return array
     */
    public function serviceRequestsAsCustomer(int $userId): array
    {
        return $this->hasMany(ServiceRequest::class, 'customer_id', $userId);
    }

    /**
     * Relationship: User has many ServiceRequests (as technician)
     *
     * @param int $userId
     * @return array
     */
    public function serviceRequestsAsTechnician(int $userId): array
    {
        return $this->hasMany(ServiceRequest::class, 'technician_id', $userId);
    }

    /**
     * Relationship: User has many Reviews
     *
     * @param int $userId
     * @return array
     */
    public function reviews(int $userId): array
    {
        return $this->hasMany(Review::class, 'customer_id', $userId);
    }

    /**
     * Relationship: User has many AI Consultations
     *
     * @param int $userId
     * @return array
     */
    public function consultations(int $userId): array
    {
        return $this->hasMany(AiConsultation::class, 'customer_id', $userId);
    }

    /**
     * Cari pengguna berdasarkan nomor telepon dengan normalisasi ragam format (08xx, 628xx, +628xx)
     *
     * @param string $phone
     * @return array|null
     */
    public function findByPhone(string $phone): ?array
    {
        if (!$this->db) {
            return null;
        }

        $normalized = OtpVerification::normalizePhone($phone);
        $cleanDigits = preg_replace('/[^\d]/', '', $phone);

        $candidates = array_unique(array_filter([
            $phone,
            $normalized,
            $cleanDigits,
            $normalized ? '0' . substr($normalized, 2) : null,
            $normalized ? '+' . $normalized : null,
        ]));

        if (empty($candidates)) {
            return null;
        }

        $placeholders = [];
        $params = [];
        foreach (array_values($candidates) as $i => $cand) {
            $key = "p{$i}";
            $placeholders[] = ":{$key}";
            $params[$key] = $cand;
        }

        $sql = "SELECT * FROM `{$this->table}` WHERE `phone` IN (" . implode(', ', $placeholders) . ") LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $user ?: null;
    }
}
