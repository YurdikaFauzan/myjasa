<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Models\AiConfiguration;

class AiConfigurationTest extends TestCase
{
    public function testEncryptAndDecryptApiKeyRoundTrip(): void
    {
        $originalKey = 'gsk_test_api_key_secret_123456789';
        $encrypted = AiConfiguration::encryptApiKey($originalKey);

        $this->assertNotEquals($originalKey, $encrypted);
        $this->assertStringContainsString('enc:', $encrypted);

        $decrypted = AiConfiguration::decryptApiKey($encrypted);
        $this->assertEquals($originalKey, $decrypted);
    }

    public function testEncryptEmptyKeyReturnsEmpty(): void
    {
        $this->assertEquals('', AiConfiguration::encryptApiKey(''));
        $this->assertEquals('', AiConfiguration::encryptApiKey('   '));
    }

    public function testDecryptNullOrEmptyReturnsNull(): void
    {
        $this->assertNull(AiConfiguration::decryptApiKey(null));
        $this->assertNull(AiConfiguration::decryptApiKey(''));
    }

    public function testDecryptLegacyPlainTextReturnsPlainText(): void
    {
        $plain = 'sk-ant-api-key-legacy';
        $decrypted = AiConfiguration::decryptApiKey($plain);
        $this->assertEquals($plain, $decrypted);
    }

    public function testMaskApiKeyProtectsSecret(): void
    {
        $sampleKey = 'gsk_1234567890abcdefghijk987654321';
        $masked = AiConfiguration::maskApiKey($sampleKey);

        $this->assertNotEquals($sampleKey, $masked);
        $this->assertStringContainsString('•', $masked);
        // Prefix dan suffix tetap terlihat sebagian
        $this->assertStringContainsString('gsk_', $masked);
        $this->assertStringContainsString('321', $masked);
    }

    public function testMaskApiKeyEmptyReturnsEmpty(): void
    {
        $this->assertEquals('', AiConfiguration::maskApiKey(''));
        $this->assertEquals('', AiConfiguration::maskApiKey(null));
    }
}

