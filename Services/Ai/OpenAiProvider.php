<?php

namespace Services\Ai;

class OpenAiProvider extends BaseAiProvider
{
    public function getKey(): string
    {
        return 'openai';
    }

    public function getName(): string
    {
        return 'OpenAI (GPT Engine)';
    }

    public function getDefaultModel(): string
    {
        return 'gpt-4o-mini';
    }

    public function getDefaultBaseUrl(): string
    {
        return 'https://api.openai.com/v1';
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
        return 'https://platform.openai.com/docs/models';
    }

    public function getEnvKeyName(): string
    {
        return 'OPENAI_API_KEY';
    }

    public function getCuratedModels(): array
    {
        return [
            [
                'id'          => 'gpt-4o-mini',
                'name'        => 'gpt-4o-mini',
                'owned_by'    => 'OpenAI',
                'description' => 'Model cerdas, sangat cepat, dan efisien biaya untuk customer service harian.',
                'badge'       => 'Default Rekomendasi (Cepat & Hemat)',
                'is_live'     => false
            ],
            [
                'id'          => 'gpt-4o',
                'name'        => 'gpt-4o',
                'owned_by'    => 'OpenAI',
                'description' => 'Model unggulan flagship paling cerdas dengan penalaran kompleks.',
                'badge'       => 'Flagship Pintar',
                'is_live'     => false
            ],
            [
                'id'          => 'gpt-4-turbo',
                'name'        => 'gpt-4-turbo',
                'owned_by'    => 'OpenAI',
                'description' => 'Model GPT-4 dengan jendela konteks 128k token dan kemampuan analisis mendalam.',
                'badge'       => 'Konteks Luas 128k',
                'is_live'     => false
            ],
            [
                'id'          => 'gpt-3.5-turbo',
                'name'        => 'gpt-3.5-turbo',
                'owned_by'    => 'OpenAI',
                'description' => 'Model klasik cepat dan teruji untuk percakapan standar.',
                'badge'       => 'Klasik Cepat',
                'is_live'     => false
            ],
            [
                'id'          => 'o1-mini',
                'name'        => 'o1-mini',
                'owned_by'    => 'OpenAI',
                'description' => 'Model penalaran cepat khusus untuk pemecahan masalah teknis dan kalkulasi.',
                'badge'       => 'Penalaran Teknis',
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

            // Only include chat completion models
            $isChat = str_starts_with($id, 'gpt-') || str_starts_with($id, 'o1') || str_starts_with($id, 'o3');
            $isExcluded = str_contains($id, 'audio') || str_contains($id, 'realtime') || str_contains($id, 'instruct');

            if (!$isChat || $isExcluded) {
                continue;
            }

            $ownedBy = $item['owned_by'] ?? 'OpenAI';
            $badge = match ($id) {
                'gpt-4o-mini' => 'Rekomendasi Utama',
                'gpt-4o'      => 'Flagship',
                'gpt-4-turbo' => 'High Performance',
                'o1-mini'     => 'Reasoning',
                default       => null
            };

            $result[] = [
                'id'          => $id,
                'name'        => $id,
                'owned_by'    => $ownedBy,
                'description' => "Model OpenAI resmi ID: {$id}",
                'badge'       => $badge,
                'is_live'     => true
            ];
        }

        // Sort so gpt-4o-mini / gpt-4o appear first
        usort($result, function ($a, $b) {
            if ($a['id'] === 'gpt-4o-mini') return -1;
            if ($b['id'] === 'gpt-4o-mini') return 1;
            if ($a['id'] === 'gpt-4o') return -1;
            if ($b['id'] === 'gpt-4o') return 1;
            return strcmp($a['id'], $b['id']);
        });

        return $result;
    }
}
