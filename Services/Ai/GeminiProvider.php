<?php

namespace Services\Ai;

class GeminiProvider extends BaseAiProvider
{
    public function getKey(): string
    {
        return 'gemini';
    }

    public function getName(): string
    {
        return 'Google Gemini AI';
    }

    public function getDefaultModel(): string
    {
        return 'gemini-3.5-flash';
    }

    public function getDefaultBaseUrl(): string
    {
        return 'https://generativelanguage.googleapis.com/v1beta/openai';
    }

    public function getDefaultTemperature(): float
    {
        return 0.50;
    }

    public function getDefaultMaxTokens(): int
    {
        return 700;
    }

    public function getDefaultTimeout(): int
    {
        return 25;
    }

    public function getDocumentationUrl(): string
    {
        return 'https://ai.google.dev/gemini-api/docs/models/gemini';
    }

    public function getEnvKeyName(): string
    {
        return 'GEMINI_API_KEY';
    }

    public function getCuratedModels(): array
    {
        return [
            [
                'id'          => 'gemini-3.5-flash',
                'name'        => 'gemini-3.5-flash',
                'owned_by'    => 'Google DeepMind',
                'description' => 'Model multimodal generasi Gemini 3.5 berkecepatan tinggi, hemat kuota, dan sangat cerdas.',
                'badge'       => 'Default Rekomendasi (Cepat & Stabil)',
                'is_live'     => false
            ],
            [
                'id'          => 'gemini-3.5-flash-lite',
                'name'        => 'gemini-3.5-flash-lite',
                'owned_by'    => 'Google DeepMind',
                'description' => 'Versi ultra ringan Gemini 3.5 dengan latensi ultra rendah untuk percakapan instan.',
                'badge'       => 'Ultra Cepat',
                'is_live'     => false
            ],
            [
                'id'          => 'gemini-3.8-flash',
                'name'        => 'gemini-3.8-flash',
                'owned_by'    => 'Google DeepMind',
                'description' => 'Model Gemini 3.8 Flash generasi mutakhir dengan pemahaman instruksi kompleks.',
                'badge'       => 'Generasi Baru (3.8 Flash)',
                'is_live'     => false
            ],
            [
                'id'          => 'gemini-flash-latest',
                'name'        => 'gemini-flash-latest',
                'owned_by'    => 'Google DeepMind',
                'description' => 'Alias model Gemini Flash resmi terbaru yang selalu otomatis terbarui oleh Google.',
                'badge'       => 'Flash Auto-Latest',
                'is_live'     => false
            ],
            [
                'id'          => 'gemini-pro-latest',
                'name'        => 'gemini-pro-latest',
                'owned_by'    => 'Google DeepMind',
                'description' => 'Versi Gemini Pro terbaru untuk penalaran tingkat lanjut dan analisis mendalam.',
                'badge'       => 'Pro Reasoning',
                'is_live'     => false
            ],
            [
                'id'          => 'gemini-2.5-pro',
                'name'        => 'gemini-2.5-pro',
                'owned_by'    => 'Google DeepMind',
                'description' => 'Model Gemini 2.5 Pro dengan kapasitas penalaran tinggi.',
                'badge'       => 'Kapasitas Besar',
                'is_live'     => false
            ]
        ];
    }

    public function chat(array $messages, array $options = []): array
    {
        $apiKey = trim($options['apiKey'] ?? '');
        if (empty($apiKey)) {
            return [
                'success'    => false,
                'output'     => '',
                'latency_ms' => 0,
                'usage'      => null,
                'error'      => "API Key untuk Google Gemini belum dikonfigurasi.",
                'http_code'  => 400
            ];
        }

        // Try via OpenAI-compatible endpoint first
        $res = parent::chat($messages, $options);
        if ($res['success']) {
            return $res;
        }

        // Fallback to Native Gemini REST API if OpenAI-compatible endpoint failed
        return $this->chatNativeGemini($messages, $options);
    }

    /**
     * Fallback to native Google Generative Language REST endpoint
     */
    private function chatNativeGemini(array $messages, array $options): array
    {
        $apiKey  = trim($options['apiKey'] ?? '');
        $model   = trim($options['model'] ?? $this->getDefaultModel());
        $timeout = isset($options['timeout']) ? (int)$options['timeout'] : $this->getDefaultTimeout();

        // Clean model name if user provided e.g. "models/gemini-1.5-flash"
        $cleanModel = str_replace('models/', '', $model);

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$cleanModel}:generateContent?key=" . urlencode($apiKey);

        $systemInstruction = null;
        $contents = [];

        foreach ($messages as $msg) {
            $role = $msg['role'] ?? 'user';
            $text = $msg['content'] ?? '';

            if ($role === 'system') {
                $systemInstruction = ['parts' => [['text' => $text]]];
            } else {
                $geminiRole = ($role === 'assistant') ? 'model' : 'user';
                $contents[] = [
                    'role'  => $geminiRole,
                    'parts' => [['text' => $text]]
                ];
            }
        }

        $payload = [
            'contents' => $contents,
            'generationConfig' => [
                'temperature'     => (float)($options['temperature'] ?? 0.5),
                'maxOutputTokens' => (int)($options['max_tokens'] ?? 700),
            ]
        ];

        if ($systemInstruction) {
            $payload['systemInstruction'] = $systemInstruction;
        }

        $curlRes = $this->executeCurl($url, $payload, [], $timeout, 'POST');

        if ($curlRes['error']) {
            return [
                'success'    => false,
                'output'     => '',
                'latency_ms' => $curlRes['latency_ms'],
                'usage'      => null,
                'error'      => "Gagal terhubung ke Google Gemini: " . $this->sanitizeError($curlRes['error'], $apiKey),
                'http_code'  => $curlRes['http_code']
            ];
        }

        $decoded = json_decode($curlRes['body'] ?? '', true);

        if ($curlRes['http_code'] === 200 && isset($decoded['candidates'][0]['content']['parts'][0]['text'])) {
            $content = $decoded['candidates'][0]['content']['parts'][0]['text'];
            return [
                'success'    => true,
                'output'     => trim($content),
                'latency_ms' => $curlRes['latency_ms'],
                'usage'      => $decoded['usageMetadata'] ?? null,
                'error'      => null,
                'http_code'  => 200
            ];
        }

        $rawError = $decoded['error']['message'] ?? "Google API HTTP {$curlRes['http_code']}";

        return [
            'success'    => false,
            'output'     => '',
            'latency_ms' => $curlRes['latency_ms'],
            'usage'      => null,
            'error'      => $this->sanitizeError($rawError, $apiKey),
            'http_code'  => $curlRes['http_code']
        ];
    }

    public function fetchModelsFromApi(string $apiKey, ?string $baseUrl = null): array
    {
        // Query Google Gemini models API
        $url = "https://generativelanguage.googleapis.com/v1beta/models?key=" . urlencode($apiKey);

        $res = $this->executeCurl($url, null, [], 15, 'GET');

        if ($res['http_code'] !== 200 || empty($res['body'])) {
            return [];
        }

        $decoded = json_decode($res['body'], true);
        if (!isset($decoded['models']) || !is_array($decoded['models'])) {
            return [];
        }

        $result = [];
        foreach ($decoded['models'] as $item) {
            $rawName = $item['name'] ?? '';
            // e.g. "models/gemini-1.5-flash"
            $id = str_replace('models/', '', $rawName);
            if (empty($id) || !str_starts_with($id, 'gemini')) {
                continue;
            }

            // Exclude embedding or vision-only legacy
            if (str_contains($id, 'embedding') || str_contains($id, 'aqa')) {
                continue;
            }

            $displayName = $item['displayName'] ?? $id;
            $desc = $item['description'] ?? 'Google Gemini Model';

            $badge = match ($id) {
                'gemini-3.5-flash'      => 'Rekomendasi Utama (3.5 Flash)',
                'gemini-3.5-flash-lite' => 'Ultra Cepat',
                'gemini-3.8-flash'      => 'Gemini 3.8 Flash',
                'gemini-flash-latest'   => 'Flash Auto-Latest',
                'gemini-pro-latest'     => 'Pro Auto-Latest',
                'gemini-2.5-pro'        => 'Pro Reasoning',
                default                 => null
            };

            $result[] = [
                'id'          => $id,
                'name'        => $displayName . " ({$id})",
                'owned_by'    => 'Google',
                'description' => substr($desc, 0, 120),
                'badge'       => $badge,
                'is_live'     => true
            ];
        }

        return $result;
    }
}
