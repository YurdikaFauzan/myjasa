<?php

namespace Services\Ai;

interface AiProviderInterface
{
    /**
     * Unique identifier for the provider (e.g. 'groq', 'openai', 'gemini', 'deepseek')
     */
    public function getKey(): string;

    /**
     * Human-readable provider name
     */
    public function getName(): string;

    /**
     * Default model identifier
     */
    public function getDefaultModel(): string;

    /**
     * Official default base URL
     */
    public function getDefaultBaseUrl(): string;

    /**
     * Default temperature (0.0 - 2.0)
     */
    public function getDefaultTemperature(): float;

    /**
     * Default maximum output tokens
     */
    public function getDefaultMaxTokens(): int;

    /**
     * Default request timeout in seconds
     */
    public function getDefaultTimeout(): int;

    /**
     * Official documentation URL
     */
    public function getDocumentationUrl(): string;

    /**
     * Environment variable key name for fallback (e.g. 'GROQ_API_KEY')
     */
    public function getEnvKeyName(): string;

    /**
     * Curated list of verified models for this provider
     *
     * @return array Array of ['id' => string, 'name' => string, 'description' => string, 'badge' => ?string, 'owned_by' => string]
     */
    public function getCuratedModels(): array;

    /**
     * Fetch live list of models from provider API
     *
     * @param string $apiKey
     * @param string|null $baseUrl
     * @return array
     */
    public function fetchModelsFromApi(string $apiKey, ?string $baseUrl = null): array;

    /**
     * Execute chat completion request
     *
     * @param array $messages Array of ['role' => string, 'content' => string]
     * @param array $options ['model' => string, 'apiKey' => string, 'baseUrl' => ?string, 'temperature' => ?float, 'max_tokens' => ?int, 'timeout' => ?int]
     * @return array ['success' => bool, 'output' => string, 'latency_ms' => int, 'usage' => ?array, 'error' => ?string, 'http_code' => int]
     */
    public function chat(array $messages, array $options = []): array;

    /**
     * Test connection to provider with given model and prompt
     *
     * @param string $modelId
     * @param string $testPrompt
     * @param array $options
     * @return array ['success' => bool, 'model' => string, 'latency_ms' => int, 'output' => string, 'error' => ?string, 'usage' => ?array]
     */
    public function testConnection(string $modelId, string $testPrompt, array $options = []): array;
}
