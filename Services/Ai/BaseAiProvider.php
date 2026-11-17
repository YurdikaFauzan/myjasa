<?php

namespace Services\Ai;

abstract class BaseAiProvider implements AiProviderInterface
{
    /**
     * Perform cURL HTTP Request safely
     *
     * @param string $url
     * @param array|null $postData
     * @param array $headers
     * @param int $timeout
     * @param string $method
     * @return array ['body' => ?string, 'http_code' => int, 'error' => ?string, 'latency_ms' => int]
     */
    protected function executeCurl(
        string $url,
        ?array $postData = null,
        array $headers = [],
        int $timeout = 20,
        string $method = 'POST'
    ): array {
        $startTime = microtime(true);
        $ch = curl_init($url);

        $defaultHeaders = ['Content-Type: application/json'];
        $allHeaders = array_merge($defaultHeaders, $headers);

        $curlOptions = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => max(10, min(120, $timeout)),
            CURLOPT_CONNECTTIMEOUT => 15,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_HTTPHEADER     => $allHeaders,
        ];

        if ($method === 'POST') {
            $curlOptions[CURLOPT_POST] = true;
            if ($postData !== null) {
                $curlOptions[CURLOPT_POSTFIELDS] = json_encode($postData, JSON_UNESCAPED_UNICODE);
            }
        } elseif ($method === 'GET') {
            $curlOptions[CURLOPT_HTTPGET] = true;
        }

        curl_setopt_array($ch, $curlOptions);

        $response  = curl_exec($ch);
        $curlError = curl_error($ch);
        $httpCode  = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $latencyMs = (int) round((microtime(true) - $startTime) * 1000);

        return [
            'body'       => $response !== false ? $response : null,
            'http_code'  => $httpCode,
            'error'      => !empty($curlError) ? $curlError : null,
            'latency_ms' => $latencyMs,
        ];
    }

    /**
     * Standard OpenAI-compatible Chat Completions
     */
    public function chat(array $messages, array $options = []): array
    {
        $apiKey = trim($options['apiKey'] ?? '');
        if (empty($apiKey)) {
            return [
                'success'    => false,
                'output'     => '',
                'latency_ms' => 0,
                'usage'      => null,
                'error'      => "API Key untuk provider {$this->getName()} belum dikonfigurasi.",
                'http_code'  => 400
            ];
        }

        $model       = trim($options['model'] ?? $this->getDefaultModel());
        $baseUrl     = rtrim($options['baseUrl'] ?? $this->getDefaultBaseUrl(), '/');
        $temperature = isset($options['temperature']) ? (float)$options['temperature'] : $this->getDefaultTemperature();
        $maxTokens   = isset($options['max_tokens']) ? (int)$options['max_tokens'] : $this->getDefaultMaxTokens();
        $timeout     = isset($options['timeout']) ? (int)$options['timeout'] : $this->getDefaultTimeout();

        $endpoint = $baseUrl . '/chat/completions';

        $payload = [
            'model'       => $model,
            'messages'    => $messages,
            'temperature' => max(0.0, min(2.0, $temperature)),
            'max_tokens'  => max(10, min(8192, $maxTokens)),
            'stream'      => false,
        ];

        // Provider specific payload tuning
        if (isset($options['response_format'])) {
            $payload['response_format'] = $options['response_format'];
        }

        $headers = [
            'Authorization: Bearer ' . $apiKey,
        ];

        $res = $this->executeCurl($endpoint, $payload, $headers, $timeout, 'POST');

        // Automatic retry on transient network/SSL timeout, reset, or recv failure (up to 2 retries)
        $attempts = 0;
        while (!empty($res['error']) && $attempts < 2 && (
            str_contains(strtolower($res['error']), 'timeout') || 
            str_contains(strtolower($res['error']), 'timed out') || 
            str_contains(strtolower($res['error']), 'could not resolve') ||
            str_contains(strtolower($res['error']), 'connection reset') ||
            str_contains(strtolower($res['error']), 'recv failure') ||
            str_contains(strtolower($res['error']), 'failed to connect')
        )) {
            $attempts++;
            usleep(600000); // 600ms backoff
            $res = $this->executeCurl($endpoint, $payload, $headers, $timeout + 5, 'POST');
        }

        if ($res['error']) {
            return [
                'success'    => false,
                'output'     => '',
                'latency_ms' => $res['latency_ms'],
                'usage'      => null,
                'error'      => "Gagal terhubung ke {$this->getName()}: " . $this->sanitizeError($res['error'], $apiKey),
                'http_code'  => $res['http_code']
            ];
        }

        $decoded = json_decode($res['body'] ?? '', true);

        if ($res['http_code'] === 200 && isset($decoded['choices'][0]['message']['content'])) {
            $content = $decoded['choices'][0]['message']['content'];
            return [
                'success'    => true,
                'output'     => trim($content),
                'latency_ms' => $res['latency_ms'],
                'usage'      => $decoded['usage'] ?? null,
                'error'      => null,
                'http_code'  => 200
            ];
        }

        // Handle error responses from provider
        $rawError = $decoded['error']['message'] ?? $decoded['message'] ?? null;
        if (!$rawError && $res['http_code'] !== 200) {
            $rawError = "Penyedia AI merespons dengan HTTP status {$res['http_code']}.";
        }

        return [
            'success'    => false,
            'output'     => '',
            'latency_ms' => $res['latency_ms'],
            'usage'      => null,
            'error'      => $this->sanitizeError($rawError ?: 'Respons tidak valid dari provider AI', $apiKey),
            'http_code'  => $res['http_code']
        ];
    }

    /**
     * Standard Connection Test implementation
     */
    public function testConnection(string $modelId, string $testPrompt, array $options = []): array
    {
        $messages = [
            [
                'role'    => 'system',
                'content' => 'Anda adalah asisten AI teknisi SmartService AI. Jawab singkat padat maksimal 10 kata dalam Bahasa Indonesia tanpa tanda bintang.'
            ],
            [
                'role'    => 'user',
                'content' => $testPrompt ?: 'Halo! Jawab dalam 5 kata: Apakah Anda siap membantu pelanggan SmartService AI?'
            ]
        ];

        $options['model'] = $modelId;
        $options['max_tokens'] = $options['max_tokens'] ?? 60;
        $options['temperature'] = $options['temperature'] ?? 0.3;

        $result = $this->chat($messages, $options);

        // Sanitize output (remove any asterisk character as per prompt rule)
        $cleanOutput = str_replace('*', '', $result['output'] ?? '');

        return [
            'success'    => $result['success'],
            'model'      => $modelId,
            'latency_ms' => $result['latency_ms'],
            'output'     => $cleanOutput,
            'error'      => $result['error'],
            'usage'      => $result['usage'] ?? null,
            'http_code'  => $result['http_code'] ?? 200
        ];
    }

    /**
     * Helper to sanitize error messages so API keys are never exposed
     */
    public function sanitizeError(?string $error, ?string $apiKey = null): string
    {
        if (empty($error)) {
            return '';
        }

        if (!empty($apiKey)) {
            $error = str_replace($apiKey, '***SECRET_KEY***', $error);
        }

        // Mask any potential bearer tokens or generic keys in error string
        $error = preg_replace('/(Bearer\s+)[A-Za-z0-9_\-\.]{8,}/i', '$1***SECRET_KEY***', $error);
        $error = preg_replace('/(key=)[A-Za-z0-9_\-\.]{8,}/i', '$1***SECRET_KEY***', $error);

        return htmlspecialchars($error, ENT_QUOTES, 'UTF-8');
    }
}
