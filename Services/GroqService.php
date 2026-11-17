<?php

namespace Services\Ai;

// Re-export / forward to root Services\GroqService for backwards compatibility
namespace Services;

use Services\Ai\AiManager;

class GroqService
{
    private string $apiKey;
    private string $baseUrl;
    private int $timeout;

    public function __construct(?string $apiKey = null, string $baseUrl = 'https://api.groq.com/openai/v1', int $timeout = 20)
    {
        // Resolve key through AiManager (DB encrypted key or .env key)
        $resolved = AiManager::resolveApiKey('groq');
        $this->apiKey  = $apiKey ?: $resolved;
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->timeout = $timeout;
    }

    /**
     * Dapatkan daftar model AI Groq resmi yang kompatibel untuk chat completions
     * Mengambil secara live dari endpoint API resmi Groq (/models) dengan fallback terkurasi
     *
     * @return array
     */
    public function getAvailableModels(): array
    {
        return AiManager::fetchModels('groq', $this->apiKey, $this->baseUrl);
    }

    /**
     * Panggil endpoint resmi Groq: GET /models
     *
     * @return array
     */
    public function fetchModelsFromApi(): array
    {
        $provider = AiManager::getProvider('groq');
        return $provider ? $provider->fetchModelsFromApi($this->apiKey, $this->baseUrl) : [];
    }

    /**
     * Daftar model terkurasi berdasarkan dokumentasi resmi Groq (console.groq.com/docs)
     *
     * @return array
     */
    public function getCuratedFallbackModels(): array
    {
        $provider = AiManager::getProvider('groq');
        return $provider ? $provider->getCuratedModels() : [];
    }

    /**
     * Uji fungsionalitas model AI Groq menggunakan endpoint chat completions sebenarnya
     *
     * @param string $modelId ID model yang diuji
     * @param string $testMessage Prompt singkat pengujian
     * @return array [success => bool, model => string, latency_ms => int, output => string, error => ?string]
     */
    public function testModel(string $modelId, string $testMessage = 'Halo! Jawab dalam 5 kata: Apakah Anda siap membantu pelanggan SmartService AI?'): array
    {
        return AiManager::testConnection('groq', $modelId, $this->apiKey, $this->baseUrl, null, null, $this->timeout, $testMessage);
    }
}
