<?php

namespace Services;

use Config\Env;

class FonteService implements OtpSenderInterface
{
    private string $apiToken;
    private string $baseUrl;
    private int $timeout;

    public function __construct(?string $apiToken = null, ?string $baseUrl = null, int $timeout = 15)
    {
        $this->apiToken = $apiToken ?? Env::get('FONTE_API_TOKEN', '');
        $this->baseUrl  = rtrim($baseUrl ?? Env::get('FONTE_API_BASE_URL', 'https://api.fonnte.com'), '/');
        $this->timeout  = $timeout;
    }

    /**
     * Kirim kode OTP WhatsApp melalui API resmi Fonte (Fonnte)
     *
     * @param string $target Nomor WhatsApp tujuan (format 628xxxxxxxx)
     * @param string $otpCode Kode OTP numerik
     * @param array $metadata Informasi pelengkap
     * @return array [success => bool, message => string, data => ?array]
     */
    public function sendOtp(string $target, string $otpCode, array $metadata = []): array
    {
        $appName = $metadata['app_name'] ?? Env::get('APP_NAME', 'SmartService AI');
        $expiryMinutes = $metadata['expiry_minutes'] ?? 5;
        $action = $metadata['action'] ?? 'autentikasi';

        $actionText = match ($action) {
            'login'        => 'masuk (login)',
            'register'     => 'registrasi akun baru',
            'verification' => 'verifikasi nomor telepon',
            default        => 'verifikasi'
        };

        // Format pesan OTP resmi dan aman
        $message = "🔐 *{$appName} - Kode OTP*\n\n"
                 . "Kode verifikasi Anda untuk {$actionText} adalah:\n\n"
                 . "👉 *{$otpCode}*\n\n"
                 . "⚠️ Kode ini berlaku selama *{$expiryMinutes} menit*.\n"
                 . "JANGAN membagikan kode ini kepada siapa pun demi keamanan akun Anda.\n\n"
                 . "Jika Anda tidak meminta kode ini, abaikan pesan ini.\n"
                 . "Salam hangat, Tim {$appName}.";

        return $this->sendMessage($target, $message);
    }

    /**
     * Kirim pesan WhatsApp menggunakan endpoint resmi Fonte (POST /send)
     *
     * @param string $target Nomor penerima
     * @param string $message Teks pesan
     * @return array [success => bool, message => string, data => ?array]
     */
    public function sendMessage(string $target, string $message): array
    {
        // Validasi konfigurasi token
        if (empty($this->apiToken) || $this->isPlaceholderToken($this->apiToken)) {
            return [
                'success' => false,
                'message' => 'Konfigurasi FONTE_API_TOKEN belum diisi atau masih berupa placeholder pada file .env.',
                'data'    => null
            ];
        }

        // Endpoint resmi Fonte: https://api.fonnte.com/send
        $endpoint = $this->baseUrl . '/send';

        $payload = [
            'target'      => $target,
            'message'     => $message,
            'countryCode' => '62'
        ];

        $headers = [
            'Authorization: ' . $this->apiToken
        ];

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $endpoint,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => $this->timeout,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2
        ]);

        $responseBody = curl_exec($ch);
        $curlError    = curl_error($ch);
        $httpCode     = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (!empty($curlError)) {
            // Sanitasi pesan error cURL agar tidak mengandung token
            return [
                'success' => false,
                'message' => 'Gagal menghubungi server WhatsApp Gateway Fonte (koneksi jaringan bermasalah).',
                'data'    => ['curl_error' => $this->sanitizeError($curlError)]
            ];
        }

        $decoded = json_decode($responseBody, true);
        if (!is_array($decoded)) {
            return [
                'success' => false,
                'message' => 'Respons tidak valid dari server Fonte (HTTP ' . $httpCode . ').',
                'data'    => null
            ];
        }

        // Analisis status keberhasilan berdasarkan spesifikasi API Fonte:
        // Berhasil: {"detail": "success! message in queue", "id": [...], "target": [...], "status": true}
        // Gagal: {"status": false, "reason": "...", ...}
        $isSuccess = false;
        if (isset($decoded['status']) && $decoded['status'] === true) {
            $isSuccess = true;
        } elseif (isset($decoded['detail']) && stripos($decoded['detail'], 'success') !== false) {
            $isSuccess = true;
        }

        if ($isSuccess) {
            return [
                'success' => true,
                'message' => 'Pesan OTP berhasil dikirim ke nomor WhatsApp Anda.',
                'data'    => [
                    'id'      => $decoded['id'] ?? null,
                    'process' => $decoded['process'] ?? 'pending',
                    'target'  => $decoded['target'] ?? $target
                ]
            ];
        }

        // Ambil alasan kegagalan dari API Fonte
        $reason = $decoded['reason'] ?? ($decoded['message'] ?? 'Permintaan pesan ditolak oleh gateway Fonte.');
        $friendlyReason = $this->translateFonteReason((string)$reason);

        return [
            'success' => false,
            'message' => $friendlyReason,
            'data'    => ['reason' => $reason]
        ];
    }

    /**
     * Periksa status koneksi perangkat ke gateway Fonte (POST /device)
     *
     * @return array
     */
    public function checkStatus(): array
    {
        if (empty($this->apiToken) || $this->isPlaceholderToken($this->apiToken)) {
            return [
                'connected' => false,
                'status'    => 'unconfigured',
                'device'    => null,
                'message'   => 'Token API Fonte belum dikonfigurasi di file .env.'
            ];
        }

        $endpoint = $this->baseUrl . '/device';

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $endpoint,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => ['Authorization: ' . $this->apiToken],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 8,
            CURLOPT_SSL_VERIFYPEER => true
        ]);

        $res = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            return [
                'connected' => false,
                'status'    => 'network_error',
                'device'    => null,
                'message'   => 'Tidak dapat terhubung ke server Fonte: ' . $this->sanitizeError($err)
            ];
        }

        $data = json_decode($res, true);
        if (is_array($data) && !empty($data['status'])) {
            $deviceStatus = $data['device_status'] ?? 'disconnect';
            $isConnected = ($deviceStatus === 'connect');

            return [
                'connected' => $isConnected,
                'status'    => $deviceStatus,
                'device'    => $data['device'] ?? null,
                'name'      => $data['name'] ?? null,
                'package'   => $data['package'] ?? null,
                'quota'     => $data['quota'] ?? 0,
                'expired'   => $data['expired'] ?? null,
                'message'   => $isConnected ? 'Perangkat WhatsApp terhubung aktif.' : 'Perangkat WhatsApp Fonte sedang terputus (disconnect).'
            ];
        }

        $reason = $data['reason'] ?? 'Respons status perangkat tidak valid.';
        return [
            'connected' => false,
            'status'    => 'error',
            'device'    => null,
            'message'   => $this->translateFonteReason((string)$reason)
        ];
    }

    /**
     * Cek apakah token masih berupa placeholder default
     */
    private function isPlaceholderToken(string $token): bool
    {
        $token = trim($token);
        return empty($token)
            || str_starts_with($token, 'ISI_TOKEN')
            || str_starts_with($token, 'your_fonte')
            || $token === 'YOUR_TOKEN';
    }

    /**
     * Terjemahkan pesan alasan dari Fonte ke pesan ramah pengguna bahasa Indonesia
     */
    private function translateFonteReason(string $reason): string
    {
        $low = strtolower($reason);
        if (str_contains($low, 'token invalid')) {
            return 'Token otorisasi Fonte tidak valid. Periksa konfigurasi di file .env.';
        }
        if (str_contains($low, 'devices must belong')) {
            return 'Perangkat Fonte tidak terdaftar pada akun token ini.';
        }
        if (str_contains($low, 'device disconnect') || str_contains($low, 'disconnected')) {
            return 'WhatsApp gateway Fonte sedang offline/terputus. Mohon hubungi admin.';
        }
        if (str_contains($low, 'quota') || str_contains($low, 'limit')) {
            return 'Batas kuota pengiriman pesan WhatsApp Fonte telah habis.';
        }
        if (str_contains($low, 'target invalid') || str_contains($low, 'number invalid')) {
            return 'Nomor WhatsApp tujuan tidak valid atau tidak terdaftar di WhatsApp.';
        }

        return 'Gagal mengirim pesan WhatsApp: ' . htmlspecialchars($reason);
    }

    /**
     * Sanitasi error string untuk mencegah token bocor
     */
    private function sanitizeError(string $error): string
    {
        if (!empty($this->apiToken)) {
            $error = str_replace($this->apiToken, '***HIDDEN***', $error);
        }
        return htmlspecialchars($error);
    }
}
