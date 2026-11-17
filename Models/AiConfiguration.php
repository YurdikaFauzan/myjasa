<?php

namespace Models;

use Config\Database;
use Config\Env;
use PDO;

class AiConfiguration extends BaseModel
{
    protected string $table = 'ai_configurations';

    public const DEFAULT_PROVIDER = 'groq';
    public const DEFAULT_MODEL    = 'qwen/qwen3.8-27b';

    private static ?string $cachedActiveModel = null;
    private static ?array  $cachedActiveConfig = null;

    /**
     * Secret key used to encrypt API keys stored in database
     */
    private static function getSecretKey(): string
    {
        $appKey = Env::get('APP_KEY', 'SmartServiceAI_Secret_Key_2026_Secure');
        return hash('sha256', $appKey, true);
    }

    /**
     * Encrypt an API key using AES-256-CBC
     */
    public static function encryptApiKey(string $plain): string
    {
        $clean = trim($plain);
        if ($clean === '') {
            return '';
        }
        $ivLen = openssl_cipher_iv_length('aes-256-cbc');
        $iv = openssl_random_pseudo_bytes($ivLen);
        $encrypted = openssl_encrypt($clean, 'aes-256-cbc', self::getSecretKey(), OPENSSL_RAW_DATA, $iv);
        return 'enc:' . base64_encode($iv . $encrypted);
    }

    /**
     * Decrypt an API key stored in database
     */
    public static function decryptApiKey(?string $cipher): ?string
    {
        if (empty($cipher)) {
            return null;
        }

        if (!str_starts_with($cipher, 'enc:')) {
            // Unencrypted legacy plain text
            return trim($cipher);
        }

        $raw = base64_decode(substr($cipher, 4));
        $ivLen = openssl_cipher_iv_length('aes-256-cbc');
        if (strlen($raw) < $ivLen) {
            return null;
        }

        $iv = substr($raw, 0, $ivLen);
        $encrypted = substr($raw, $ivLen);
        $decrypted = openssl_decrypt($encrypted, 'aes-256-cbc', self::getSecretKey(), OPENSSL_RAW_DATA, $iv);

        return $decrypted !== false ? trim($decrypted) : null;
    }

    /**
     * Mask API key for secure display in UI (e.g. gsk_••••••••••••••••••••••••••••••••••••••••••••mn1)
     */
    public static function maskApiKey(?string $key): string
    {
        if (empty($key)) {
            return '';
        }
        $len = strlen($key);
        if ($len <= 8) {
            return str_repeat('•', max(4, $len));
        }

        $prefixLen = min(4, (int)floor($len / 4));
        $suffixLen = min(4, (int)floor($len / 4));
        $prefix = substr($key, 0, $prefixLen);
        $suffix = substr($key, -$suffixLen);
        $bulletCount = min(28, max(8, $len - $prefixLen - $suffixLen));

        return $prefix . str_repeat('•', $bulletCount) . $suffix;
    }

    /**
     * Dapatkan Model ID yang sedang aktif (baik global aktif atau provider tertentu)
     *
     * @param string|null $provider
     * @return string
     */
    public static function getActiveModel(?string $provider = null): string
    {
        if ($provider === null && self::$cachedActiveModel !== null) {
            return self::$cachedActiveModel;
        }

        $config = self::getActiveConfig($provider);
        $model = $config['model_id'] ?? self::DEFAULT_MODEL;

        if ($provider === null) {
            self::$cachedActiveModel = $model;
        }

        return $model;
    }

    /**
     * Dapatkan seluruh data konfigurasi AI yang sedang aktif
     *
     * @param string|null $provider
     * @return array
     */
    public static function getActiveConfig(?string $provider = null): array
    {
        if ($provider === null && self::$cachedActiveConfig !== null) {
            return self::$cachedActiveConfig;
        }

        $db = Database::getConnection();
        if (!$db) {
            return self::getDefaultFallbackConfig($provider ?: self::DEFAULT_PROVIDER);
        }

        try {
            if ($provider !== null) {
                $stmt = $db->prepare("
                    SELECT * FROM `ai_configurations` 
                    WHERE `provider` = :provider
                    ORDER BY `id` DESC LIMIT 1
                ");
                $stmt->execute(['provider' => $provider]);
            } else {
                $stmt = $db->prepare("
                    SELECT * FROM `ai_configurations` 
                    WHERE `is_active` = 1 
                    ORDER BY `id` DESC LIMIT 1
                ");
                $stmt->execute();
            }

            $config = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($config) {
                if ($provider === null) {
                    self::$cachedActiveConfig = $config;
                }
                return $config;
            }
        } catch (\PDOException $e) {
            // Return fallback
        }

        return self::getDefaultFallbackConfig($provider ?: self::DEFAULT_PROVIDER);
    }

    /**
     * Dapatkan konfigurasi berdasarkan nama provider
     */
    public function getConfigByProvider(string $provider): ?array
    {
        if (!$this->db) {
            return null;
        }

        try {
            $stmt = $this->db->prepare("
                SELECT * FROM `{$this->table}` 
                WHERE `provider` = :provider 
                ORDER BY `id` DESC LIMIT 1
            ");
            $stmt->execute(['provider' => $provider]);
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            return $res ?: null;
        } catch (\PDOException $e) {
            return null;
        }
    }

    /**
     * Dapatkan semua konfigurasi provider yang tersimpan
     *
     * @return array<string, array>
     */
    public function getAllConfigs(): array
    {
        if (!$this->db) {
            return [];
        }

        try {
            $stmt = $this->db->query("SELECT * FROM `{$this->table}` ORDER BY `id` ASC");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $indexed = [];
            foreach ($rows as $r) {
                $indexed[$r['provider']] = $r;
            }
            return $indexed;
        } catch (\PDOException $e) {
            return [];
        }
    }

    /**
     * Simpan / Perbarui konfigurasi provider AI
     *
     * @param array $data ['provider', 'model_id', 'api_key'?, 'base_url'?, 'temperature'?, 'max_tokens'?, 'timeout'?, 'is_active'?]
     * @param int|null $adminId
     * @param string|null $adminName
     * @return bool
     */
    public function saveConfig(array $data, ?int $adminId = null, ?string $adminName = null): bool
    {
        if (!$this->db) {
            return false;
        }

        $provider = strtolower(trim($data['provider'] ?? 'groq'));
        $modelId  = trim($data['model_id'] ?? '');

        if (empty($provider) || empty($modelId)) {
            return false;
        }

        $now = date('Y-m-d H:i:s');
        $adminName = $adminName ?: 'Administrator';

        // Check existing record
        $existing = $this->getConfigByProvider($provider);

        // API Key logic:
        // If new non-empty key provided, encrypt and store.
        // If delete_api_key flag is true, set null.
        // Otherwise, keep existing stored key.
        $apiKeyToSave = $existing['api_key'] ?? null;
        if (!empty($data['delete_api_key'])) {
            $apiKeyToSave = null;
        } elseif (!empty($data['api_key'])) {
            $apiKeyToSave = self::encryptApiKey($data['api_key']);
        }

        $baseUrl     = !empty($data['base_url']) ? trim($data['base_url']) : ($existing['base_url'] ?? null);
        $temperature = isset($data['temperature']) ? (float)$data['temperature'] : ($existing['temperature'] ?? 0.50);
        $maxTokens   = !empty($data['max_tokens']) ? (int)$data['max_tokens'] : ($existing['max_tokens'] ?? 650);
        $timeout     = !empty($data['timeout']) ? (int)$data['timeout'] : ($existing['timeout'] ?? 20);
        $isActive    = isset($data['is_active']) ? (int)$data['is_active'] : ($existing['is_active'] ?? 0);

        try {
            $this->db->beginTransaction();

            // If this provider is being activated, deactivate all other providers first
            if ($isActive === 1) {
                $deactivate = $this->db->prepare("UPDATE `{$this->table}` SET `is_active` = 0");
                $deactivate->execute();
            }

            if ($existing) {
                $stmt = $this->db->prepare("
                    UPDATE `{$this->table}` 
                    SET `model_id`        = :model_id,
                        `api_key`         = :api_key,
                        `base_url`        = :base_url,
                        `temperature`     = :temperature,
                        `max_tokens`      = :max_tokens,
                        `timeout`         = :timeout,
                        `is_active`       = :is_active,
                        `updated_by`      = :updated_by,
                        `updated_by_name` = :updated_by_name,
                        `updated_at`      = :updated_at
                    WHERE `id` = :id
                ");
                $stmt->execute([
                    'model_id'        => $modelId,
                    'api_key'         => $apiKeyToSave,
                    'base_url'        => $baseUrl,
                    'temperature'     => $temperature,
                    'max_tokens'      => $maxTokens,
                    'timeout'         => $timeout,
                    'is_active'       => $isActive,
                    'updated_by'      => $adminId,
                    'updated_by_name' => $adminName,
                    'updated_at'      => $now,
                    'id'              => $existing['id']
                ]);
            } else {
                $stmt = $this->db->prepare("
                    INSERT INTO `{$this->table}` 
                    (`provider`, `model_id`, `api_key`, `base_url`, `temperature`, `max_tokens`, `timeout`, `is_active`, `updated_by`, `updated_by_name`, `created_at`, `updated_at`)
                    VALUES
                    (:provider, :model_id, :api_key, :base_url, :temperature, :max_tokens, :timeout, :is_active, :updated_by, :updated_by_name, :now, :now)
                ");
                $stmt->execute([
                    'provider'        => $provider,
                    'model_id'        => $modelId,
                    'api_key'         => $apiKeyToSave,
                    'base_url'        => $baseUrl,
                    'temperature'     => $temperature,
                    'max_tokens'      => $maxTokens,
                    'timeout'         => $timeout,
                    'is_active'       => $isActive,
                    'updated_by'      => $adminId,
                    'updated_by_name' => $adminName,
                    'now'             => $now
                ]);
            }

            $this->db->commit();

            // Clear static cache
            self::$cachedActiveModel  = null;
            self::$cachedActiveConfig = null;

            return true;
        } catch (\PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return false;
        }
    }

    /**
     * Hapus API Key tersimpan dari database untuk provider tertentu
     */
    public function deleteApiKey(string $provider): bool
    {
        if (!$this->db) return false;
        try {
            $stmt = $this->db->prepare("UPDATE `{$this->table}` SET `api_key` = NULL, `updated_at` = NOW() WHERE `provider` = :provider");
            $ok = $stmt->execute(['provider' => $provider]);
            self::$cachedActiveConfig = null;
            return $ok;
        } catch (\PDOException $e) {
            return false;
        }
    }

    /**
     * Pulihkan konfigurasi default sistem (Groq + qwen/qwen3.8-27b)
     */
    public function restoreDefault(?int $adminId = null, ?string $adminName = null): bool
    {
        if (!$this->db) return false;
        try {
            $this->db->beginTransaction();

            // Deactivate all
            $this->db->exec("UPDATE `{$this->table}` SET `is_active` = 0");

            // Activate groq default
            $now = date('Y-m-d H:i:s');
            $stmt = $this->db->prepare("
                UPDATE `{$this->table}`
                SET `is_active`       = 1,
                    `model_id`        = :default_model,
                    `temperature`     = 0.50,
                    `max_tokens`      = 650,
                    `timeout`         = 20,
                    `updated_by`      = :updated_by,
                    `updated_by_name` = :updated_by_name,
                    `updated_at`      = :now
                WHERE `provider` = 'groq'
            ");
            $stmt->execute([
                'default_model'   => self::DEFAULT_MODEL,
                'updated_by'      => $adminId,
                'updated_by_name' => $adminName ?: 'System Restore',
                'now'             => $now
            ]);

            $this->db->commit();

            self::$cachedActiveModel  = null;
            self::$cachedActiveConfig = null;

            return true;
        } catch (\PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return false;
        }
    }

    /**
     * Catat hasil pengujian model ke database
     */
    public function recordTestResult(
        string $modelId,
        bool $success,
        ?int $latencyMs = null,
        ?string $message = null,
        string $provider = self::DEFAULT_PROVIDER
    ): bool {
        if (!$this->db) {
            return false;
        }

        try {
            $stmt = $this->db->prepare("
                UPDATE `{$this->table}` 
                SET `last_tested_at`   = :now,
                    `last_test_status` = :status,
                    `last_test_latency`= :latency,
                    `last_test_message`= :msg
                WHERE `provider` = :provider
            ");
            return $stmt->execute([
                'now'      => date('Y-m-d H:i:s'),
                'status'   => $success ? 'success' : 'failed',
                'latency'  => $latencyMs,
                'msg'      => $message ? substr($message, 0, 255) : ($success ? 'Model aktif dan merespons' : 'Pengujian gagal'),
                'provider' => $provider
            ]);
        } catch (\PDOException $e) {
            return false;
        }
    }

    /**
     * Legacy compatibility method
     */
    public function updateActiveModel(
        string $modelId,
        ?int $adminId = null,
        ?string $adminName = null,
        string $provider = self::DEFAULT_PROVIDER
    ): bool {
        return $this->saveConfig([
            'provider'  => $provider,
            'model_id'  => $modelId,
            'is_active' => 1
        ], $adminId, $adminName);
    }

    private static function getDefaultFallbackConfig(string $provider): array
    {
        return [
            'provider'          => $provider,
            'model_id'          => self::DEFAULT_MODEL,
            'api_key'           => null,
            'base_url'          => null,
            'temperature'       => 0.50,
            'max_tokens'        => 650,
            'timeout'           => 20,
            'is_active'         => 1,
            'updated_by_name'   => 'System Default',
            'updated_at'        => date('Y-m-d H:i:s'),
            'last_tested_at'    => null,
            'last_test_status'  => null,
            'last_test_latency' => null,
            'last_test_message' => null
        ];
    }
}
