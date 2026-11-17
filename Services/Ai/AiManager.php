<?php

namespace Services\Ai;

use Config\Env;
use Models\AiConfiguration;

class AiManager
{
    /** @var array<string, AiProviderInterface> */
    private static array $providers = [];

    /**
     * Get instance of a specific provider
     */
    public static function getProvider(string $key): ?AiProviderInterface
    {
        $key = strtolower(trim($key));

        if (!isset(self::$providers[$key])) {
            self::$providers[$key] = match ($key) {
                'groq'   => new GroqProvider(),
                'nvidia' => new NvidiaProvider(),
                default  => null
            };
        }

        return self::$providers[$key];
    }

    /**
     * Get list of all supported providers with UI metadata (Hanya Groq dan Nvidia)
     */
    public static function getSupportedProviders(): array
    {
        $keys = ['groq', 'nvidia'];
        $list = [];

        foreach ($keys as $k) {
            $p = self::getProvider($k);
            if ($p) {
                $list[$k] = [
                    'key'                 => $p->getKey(),
                    'name'                => $p->getName(),
                    'default_model'       => $p->getDefaultModel(),
                    'default_base_url'    => $p->getDefaultBaseUrl(),
                    'default_temperature' => $p->getDefaultTemperature(),
                    'default_max_tokens'  => $p->getDefaultMaxTokens(),
                    'default_timeout'     => $p->getDefaultTimeout(),
                    'doc_url'             => $p->getDocumentationUrl(),
                    'env_key'             => $p->getEnvKeyName(),
                    'curated_models'      => $p->getCuratedModels(),
                    'badge'               => match ($k) {
                        'groq'   => 'Aktif Default (LPU Ultra Cepat)',
                        'nvidia' => 'NVIDIA GPU Cloud Inference (NIM)',
                        default  => ''
                    },
                    'icon'                => match ($k) {
                        'groq'   => 'bi-lightning-charge-fill',
                        'nvidia' => 'bi-gpu-card',
                        default  => 'bi-robot'
                    },
                    'color'               => match ($k) {
                        'groq'   => 'warning',
                        'nvidia' => 'success',
                        default  => 'secondary'
                    }
                ];
            }
        }

        return $list;
    }

    /**
     * Resolve API key for a provider (DB encrypted key priority -> .env key fallback)
     */
    public static function resolveApiKey(string $providerKey, ?string $dbCipherOrPlain = null): string
    {
        // 1. Try DB key first if provided
        if (!empty($dbCipherOrPlain)) {
            $decrypted = AiConfiguration::decryptApiKey($dbCipherOrPlain);
            if (!empty($decrypted)) {
                return trim($decrypted);
            }
        }

        // 2. Fallback to .env variable
        $provider = self::getProvider($providerKey);
        $envKey = $provider ? $provider->getEnvKeyName() : strtoupper($providerKey) . '_API_KEY';

        $val = $_ENV[$envKey] ?? getenv($envKey) ?: Env::get($envKey, '');
        return trim((string)$val);
    }

    /**
     * Check source of API key for UI indicators
     *
     * @return array ['has_key' => bool, 'source' => 'db'|'env'|'none', 'masked' => string]
     */
    public static function getApiKeyStatus(string $providerKey, ?string $dbCipherOrPlain = null): array
    {
        if (!empty($dbCipherOrPlain)) {
            $decrypted = AiConfiguration::decryptApiKey($dbCipherOrPlain);
            if (!empty($decrypted)) {
                return [
                    'has_key' => true,
                    'source'  => 'db',
                    'masked'  => AiConfiguration::maskApiKey($decrypted)
                ];
            }
        }

        $provider = self::getProvider($providerKey);
        $envKey = $provider ? $provider->getEnvKeyName() : strtoupper($providerKey) . '_API_KEY';
        $envVal = $_ENV[$envKey] ?? getenv($envKey) ?: Env::get($envKey, '');

        if (!empty($envVal)) {
            return [
                'has_key' => true,
                'source'  => 'env',
                'masked'  => AiConfiguration::maskApiKey($envVal)
            ];
        }

        return [
            'has_key' => false,
            'source'  => 'none',
            'masked'  => ''
        ];
    }

    /**
     * Get fully resolved Active AI Configuration
     */
    public static function getActiveConfig(): array
    {
        $config = AiConfiguration::getActiveConfig();
        $providerKey = $config['provider'] ?? 'groq';
        $provider = self::getProvider($providerKey) ?: self::getProvider('groq');

        $resolvedApiKey = self::resolveApiKey($providerKey, $config['api_key'] ?? null);

        $baseUrl = !empty($config['base_url']) ? $config['base_url'] : $provider->getDefaultBaseUrl();
        $model = !empty($config['model_id']) ? $config['model_id'] : $provider->getDefaultModel();
        $temperature = isset($config['temperature']) ? (float)$config['temperature'] : $provider->getDefaultTemperature();
        $maxTokens = !empty($config['max_tokens']) ? (int)$config['max_tokens'] : $provider->getDefaultMaxTokens();
        $timeout = !empty($config['timeout']) ? (int)$config['timeout'] : $provider->getDefaultTimeout();

        return array_merge($config, [
            'provider'           => $provider->getKey(),
            'provider_name'      => $provider->getName(),
            'model_id'           => $model,
            'base_url'           => $baseUrl,
            'temperature'        => $temperature,
            'max_tokens'         => $maxTokens,
            'timeout'            => $timeout,
            'resolved_api_key'   => $resolvedApiKey,
            'has_api_key'        => !empty($resolvedApiKey),
            'masked_api_key'     => AiConfiguration::maskApiKey($resolvedApiKey),
            'doc_url'            => $provider->getDocumentationUrl(),
        ]);
    }

    /**
     * Send chat completion request using currently active provider & model
     *
     * @param array $messages
     * @param array $customOptions
     * @return array ['success' => bool, 'output' => string, 'latency_ms' => int, 'usage' => ?array, 'error' => ?string]
     */
    public static function chat(array $messages, array $customOptions = []): array
    {
        $config = self::getActiveConfig();
        $provider = self::getProvider($config['provider']);

        if (!$provider) {
            return [
                'success'    => false,
                'output'     => '',
                'latency_ms' => 0,
                'usage'      => null,
                'error'      => "Provider AI '{$config['provider']}' tidak dikenali."
            ];
        }

        $options = [
            'model'       => $customOptions['model'] ?? $config['model_id'],
            'apiKey'      => $customOptions['apiKey'] ?? $config['resolved_api_key'],
            'baseUrl'     => $customOptions['baseUrl'] ?? $config['base_url'],
            'temperature' => $customOptions['temperature'] ?? $config['temperature'],
            'max_tokens'  => $customOptions['max_tokens'] ?? $config['max_tokens'],
            'timeout'     => $customOptions['timeout'] ?? $config['timeout'],
        ];

        if (isset($customOptions['response_format'])) {
            $options['response_format'] = $customOptions['response_format'];
        }

        $res = $provider->chat($messages, $options);
        if ($res['success']) {
            return $res;
        }

        // Automatic Provider Fallback jika provider utama gagal karena kendala jaringan atau reset koneksi
        $fallbackKey = ($config['provider'] === 'groq') ? 'nvidia' : 'groq';
        $fallbackProvider = self::getProvider($fallbackKey);

        if ($fallbackProvider) {
            $fallbackDbConfig = (new AiConfiguration())->getConfigByProvider($fallbackKey);
            $fallbackApiKey   = self::resolveApiKey($fallbackKey, $fallbackDbConfig['api_key'] ?? null);

            if (!empty($fallbackApiKey)) {
                $fallbackOptions = [
                    'model'       => $fallbackDbConfig['model_id'] ?? $fallbackProvider->getDefaultModel(),
                    'apiKey'      => $fallbackApiKey,
                    'baseUrl'     => !empty($fallbackDbConfig['base_url']) ? $fallbackDbConfig['base_url'] : $fallbackProvider->getDefaultBaseUrl(),
                    'temperature' => isset($fallbackDbConfig['temperature']) ? (float)$fallbackDbConfig['temperature'] : $fallbackProvider->getDefaultTemperature(),
                    'max_tokens'  => !empty($fallbackDbConfig['max_tokens']) ? (int)$fallbackDbConfig['max_tokens'] : $fallbackProvider->getDefaultMaxTokens(),
                    'timeout'     => !empty($fallbackDbConfig['timeout']) ? (int)$fallbackDbConfig['timeout'] : $fallbackProvider->getDefaultTimeout(),
                ];

                if (isset($customOptions['response_format'])) {
                    $fallbackOptions['response_format'] = $customOptions['response_format'];
                }

                $fallbackRes = $fallbackProvider->chat($messages, $fallbackOptions);
                if ($fallbackRes['success']) {
                    return $fallbackRes;
                }
            }
        }

        return $res;
    }

    /**
     * Test connection to a specific provider and model
     */
    public static function testConnection(
        string $providerKey,
        string $modelId,
        ?string $apiKey = null,
        ?string $baseUrl = null,
        ?float $temperature = null,
        ?int $maxTokens = null,
        ?int $timeout = null,
        ?string $testPrompt = null
    ): array {
        $provider = self::getProvider($providerKey);
        if (!$provider) {
            return [
                'success'    => false,
                'model'      => $modelId,
                'latency_ms' => 0,
                'output'     => '',
                'error'      => "Provider AI '{$providerKey}' tidak didukung.",
                'usage'      => null
            ];
        }

        // If no explicit apiKey passed, resolve from DB or .env
        if (empty($apiKey)) {
            $dbConfig = (new AiConfiguration())->getConfigByProvider($providerKey);
            $apiKey = self::resolveApiKey($providerKey, $dbConfig['api_key'] ?? null);
        }

        $options = [
            'apiKey'      => $apiKey,
            'baseUrl'     => !empty($baseUrl) ? $baseUrl : $provider->getDefaultBaseUrl(),
            'temperature' => $temperature ?? 0.3,
            'max_tokens'  => $maxTokens ?? 60,
            'timeout'     => $timeout ?? $provider->getDefaultTimeout(),
        ];

        return $provider->testConnection($modelId, $testPrompt ?: '', $options);
    }

    /**
     * Fetch list of models from provider API, or fallback to curated list
     */
    public static function fetchModels(string $providerKey, ?string $apiKey = null, ?string $baseUrl = null): array
    {
        $provider = self::getProvider($providerKey);
        if (!$provider) {
            return [];
        }

        if (empty($apiKey)) {
            $dbConfig = (new AiConfiguration())->getConfigByProvider($providerKey);
            $apiKey = self::resolveApiKey($providerKey, $dbConfig['api_key'] ?? null);
        }

        if (!empty($apiKey)) {
            $liveModels = $provider->fetchModelsFromApi($apiKey, $baseUrl);
            if (!empty($liveModels)) {
                return $liveModels;
            }
        }

        return $provider->getCuratedModels();
    }
}
