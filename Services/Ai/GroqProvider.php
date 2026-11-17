<?php

namespace Services\Ai;

class GroqProvider extends BaseAiProvider
{
    public function getKey(): string
    {
        return 'groq';
    }

    public function getName(): string
    {
        return 'Groq (Cloud LPU™ Inference)';
    }

    public function getDefaultModel(): string
    {
        return 'qwen/qwen3.8-27b';
    }

    public function getDefaultBaseUrl(): string
    {
        return 'https://api.groq.com/openai/v1';
    }

    public function getDefaultTemperature(): float
    {
        return 0.50;
    }

    public function getDefaultMaxTokens(): int
    {
        return 650;
    }

    public function getDefaultTimeout(): int
    {
        return 20;
    }

    public function getDocumentationUrl(): string
    {
        return 'https://console.groq.com/docs/models';
    }

    public function getEnvKeyName(): string
    {
        return 'GROQ_API_KEY';
    }

    public function getCuratedModels(): array
    {
        return [
            [
                'id'          => 'qwen/qwen3.8-27b',
                'name'        => 'qwen/qwen3.8-27b',
                'owned_by'    => 'Alibaba Cloud',
                'description' => 'Model cepat, cerdas, dan stabil untuk konsultasi servis peralatan rumah tangga.',
                'badge'       => 'Default Rekomendasi (Cepat & Stabil)',
                'is_live'     => false
            ],
            [
                'id'          => 'openai/gpt-oss-120b',
                'name'        => 'openai/gpt-oss-120b',
                'owned_by'    => 'OpenAI (Open Weight on Groq)',
                'description' => 'Model open weight kapasitas besar (120B parameter) berkecepatan tinggi di Groq LPU.',
                'badge'       => 'Kapasitas Besar (120B)',
                'is_live'     => false
            ],
            [
                'id'          => 'openai/gpt-oss-20b',
                'name'        => 'openai/gpt-oss-20b',
                'owned_by'    => 'OpenAI (Open Weight on Groq)',
                'description' => 'Model open weight 20B efisien, responsif, dan hemat latensi.',
                'badge'       => 'Efisien',
                'is_live'     => false
            ],
            [
                'id'          => 'llama-3.3-70b-versatile',
                'name'        => 'llama-3.3-70b-versatile',
                'owned_by'    => 'Meta',
                'description' => 'Meta Llama 3.3 70B parameter serbaguna untuk instruksi kompleks.',
                'badge'       => 'Meta Llama 3.3 70B',
                'is_live'     => false
            ],
            [
                'id'          => 'llama-3.1-8b-instant',
                'name'        => 'llama-3.1-8b-instant',
                'owned_by'    => 'Meta',
                'description' => 'Meta Llama 3.1 8B latensi ultra-rendah untuk percakapan instan.',
                'badge'       => 'Ultra Cepat',
                'is_live'     => false
            ],
            [
                'id'          => 'mixtral-8x7b-32768',
                'name'        => 'mixtral-8x7b-32768',
                'owned_by'    => 'Mistral AI',
                'description' => 'Mixture of Experts 8x7B dengan konteks 32k token.',
                'badge'       => 'MoE 32k',
                'is_live'     => false
            ],
            [
                'id'          => 'allam-2-7b',
                'name'        => 'allam-2-7b',
                'owned_by'    => 'SDAIA',
                'description' => 'Model 7B parameter cepat untuk interaksi ringan.',
                'badge'       => null,
                'is_live'     => false
            ]
        ];
    }

    public function fetchModelsFromApi(string $apiKey, ?string $baseUrl = null): array
    {
        $baseUrl = rtrim($baseUrl ?: $this->getDefaultBaseUrl(), '/');
        $url = $baseUrl . '/models';

        $res = $this->executeCurl($url, null, ['Authorization: Bearer ' . $apiKey], 15, 'GET');

        if ($res['http_code'] !== 200 || empty($res['body'])) {
            return [];
        }

        $decoded = json_decode($res['body'], true);
        if (!isset($decoded['data']) || !is_array($decoded['data'])) {
            return [];
        }

        $result = [];
        foreach ($decoded['data'] as $item) {
            $id = $item['id'] ?? '';
            if (empty($id)) continue;

            // Filter audio & guardrail models
            if (str_contains($id, 'whisper') || str_contains($id, 'prompt-guard') || str_contains($id, 'distil-whisper')) {
                continue;
            }

            $ownedBy = $item['owned_by'] ?? 'Groq Partner';
            $contextWindow = $item['context_window'] ?? null;

            $desc = "Developer: {$ownedBy}";
            if ($contextWindow) {
                $desc .= " • Konteks: " . number_format($contextWindow) . " token";
            }

            $badge = match ($id) {
                'qwen/qwen3.8-27b'    => 'Rekomendasi Utama (Cepat & Stabil)',
                'openai/gpt-oss-120b' => 'Kapasitas Besar (120B)',
                'openai/gpt-oss-20b'  => 'Efisien (20B)',
                'llama-3.3-70b-versatile' => 'Meta Llama 3.3 70B',
                'llama-3.1-8b-instant' => 'Ultra Cepat',
                default               => null
            };

            $result[] = [
                'id'          => $id,
                'name'        => $id,
                'owned_by'    => $ownedBy,
                'description' => $desc,
                'badge'       => $badge,
                'is_live'     => true
            ];
        }

        return $result;
    }
}
