<?php

namespace Services\Ai;

class NvidiaProvider extends BaseAiProvider
{
    public function getKey(): string
    {
        return 'nvidia';
    }

    public function getName(): string
    {
        return 'NVIDIA NIM (GPU Cloud Inference)';
    }

    public function getDefaultModel(): string
    {
        return 'nvidia/nemotron-3.5-bolt-e4b';
    }

    public function getDefaultBaseUrl(): string
    {
        return 'https://integrate.api.nvidia.com/v1';
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
        return 'https://build.nvidia.com/explore/discover';
    }

    public function getEnvKeyName(): string
    {
        return 'NVIDIA_API_KEY';
    }

    public function getCuratedModels(): array
    {
        return [
            [
                'id'          => 'nvidia/nemotron-3.5-bolt-e4b',
                'name'        => 'nvidia/nemotron-3.5-bolt-e4b',
                'owned_by'    => 'NVIDIA',
                'description' => 'Model generasi terbaru Nemotron 3.5 resmi dari NVIDIA. Sangat cerdas, stabil, dan memiliki latensi ultra cepat (~600ms) untuk customer service dan konsultasi diagnosis perbaikan.',
                'badge'       => 'Default Rekomendasi (Super Cepat & Stabil)',
                'is_live'     => false
            ],
            [
                'id'          => 'nvidia/nemotron-3.5-lightning-30b-a3b',
                'name'        => 'nvidia/nemotron-3.5-lightning-30b-a3b',
                'owned_by'    => 'NVIDIA',
                'description' => 'Model Nemotron 3.5 arsitektur 30B parameter terakselerasi GPU NVIDIA untuk pemrosesan teks dan instruksi cepat.',
                'badge'       => 'Nemotron 3.5 (30B)',
                'is_live'     => false
            ],
            [
                'id'          => 'nvidia/nemotron-3-nano-omni-30b-a3b-reasoning',
                'name'        => 'nvidia/nemotron-3-nano-omni-30b-a3b-reasoning',
                'owned_by'    => 'NVIDIA',
                'description' => 'Model penalaran reasoning teknis mendalam khusus pemecahan masalah kerusakan dan kalkulasi perbaikan.',
                'badge'       => 'Reasoning 30B',
                'is_live'     => false
            ],
            [
                'id'          => 'mistralai/mistral-large-4-1t-a52b',
                'name'        => 'mistralai/mistral-large-4-1t-a52b',
                'owned_by'    => 'Mistral AI / NVIDIA NIM',
                'description' => 'Model flagship Mistral Large generasi 4 di infrastruktur NVIDIA NIM dengan kapasitas konteks luas.',
                'badge'       => 'Mistral Large 4',
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

            // Saring model embedding, reranking, audio, visual, guardrail yang bukan chat completion
            $idLower = strtolower($id);
            if (
                str_contains($idLower, 'embed') ||
                str_contains($idLower, 'rerank') ||
                str_contains($idLower, 'bge-') ||
                str_contains($idLower, 'clip') ||
                str_contains($idLower, 'whisper') ||
                str_contains($idLower, 'tts') ||
                str_contains($idLower, 'safety') ||
                str_contains($idLower, 'guard') ||
                str_contains($idLower, 'detector') ||
                str_contains($idLower, 'reward')
            ) {
                continue;
            }

            $ownedBy = $item['owned_by'] ?? 'NVIDIA NIM Partner';
            $desc = "Developer / Catalog: {$ownedBy}";

            $badge = match ($id) {
                'nvidia/nemotron-3.5-bolt-e4b'                  => 'Default Rekomendasi (Super Cepat & Stabil)',
                'nvidia/nemotron-3.5-lightning-30b-a3b'         => 'Nemotron 3.5 (30B)',
                'nvidia/nemotron-3-nano-omni-30b-a3b-reasoning' => 'Reasoning 30B',
                'mistralai/mistral-large-4-1t-a52b'             => 'Mistral Large 4',
                default                                         => null
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
