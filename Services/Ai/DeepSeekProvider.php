<?php

namespace Services\Ai;

class DeepSeekProvider extends BaseAiProvider
{
    public function getKey(): string
    {
        return 'deepseek';
    }

    public function getName(): string
    {
        return 'DeepSeek AI';
    }

    public function getDefaultModel(): string
    {
        return 'deepseek-chat';
    }

    public function getDefaultBaseUrl(): string
    {
        return 'https://api.deepseek.com';
    }

    public function getDefaultTemperature(): float
    {
        return 0.70;
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
        return 'https://api-docs.deepseek.com';
    }

    public function getEnvKeyName(): string
    {
        return 'DEEPSEEK_API_KEY';
    }

    public function getCuratedModels(): array
    {
        return [
            [
                'id'          => 'deepseek-chat',
                'name'        => 'deepseek-chat',
                'owned_by'    => 'DeepSeek',
                'description' => 'DeepSeek-V3: Model serbaguna cerdas, responsif, dan sangat ekonomis.',
                'badge'       => 'Default Rekomendasi (DeepSeek-V3)',
                'is_live'     => false
            ],
            [
                'id'          => 'deepseek-reasoner',
                'name'        => 'deepseek-reasoner',
                'owned_by'    => 'DeepSeek',
                'description' => 'DeepSeek-R1: Model penalaran mendalam dengan kemampuan pemecahan masalah kompleks.',
                'badge'       => 'DeepSeek-R1 Reasoning',
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

            $badge = match ($id) {
                'deepseek-chat'     => 'Rekomendasi Utama (V3)',
                'deepseek-reasoner' => 'Reasoning Engine (R1)',
                default             => null
            };

            $result[] = [
                'id'          => $id,
                'name'        => $id,
                'owned_by'    => 'DeepSeek',
                'description' => "Model resmi DeepSeek ID: {$id}",
                'badge'       => $badge,
                'is_live'     => true
            ];
        }

        return !empty($result) ? $result : $this->getCuratedModels();
    }
}
