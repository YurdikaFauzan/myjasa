<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Config\Env;

class EnvTest extends TestCase
{
    private string $tempEnvPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tempEnvPath = sys_get_temp_dir() . '/test_' . uniqid() . '.env';
    }

    protected function tearDown(): void
    {
        if (file_exists($this->tempEnvPath)) {
            @unlink($this->tempEnvPath);
        }
        parent::tearDown();
    }

    public function testGetReturnsExistingEnvironmentVariable(): void
    {
        putenv('TEST_UNIT_KEY=smartservice_value');
        $_ENV['TEST_UNIT_KEY'] = 'smartservice_value';

        $value = Env::get('TEST_UNIT_KEY');
        $this->assertEquals('smartservice_value', $value);
    }

    public function testGetReturnsDefaultWhenVariableNotFound(): void
    {
        $value = Env::get('NON_EXISTENT_KEY_' . uniqid(), 'default_fallback');
        $this->assertEquals('default_fallback', $value);
    }

    public function testLoadParsesKeyValuesCorrectly(): void
    {
        $uniqueVal = 'smart_val_' . uniqid();
        $content = <<<ENV
# Ini komentar
TEST_PARSED_A={$uniqueVal}
TEST_PARSED_QUOTED="quoted_value"
TEST_PARSED_SINGLE='single_quoted'
ENV;
        file_put_contents($this->tempEnvPath, $content);

        $loaded = Env::load($this->tempEnvPath);
        $this->assertTrue($loaded);
        $this->assertEquals($uniqueVal, Env::get('TEST_PARSED_A'));
        $this->assertEquals('quoted_value', Env::get('TEST_PARSED_QUOTED'));
        $this->assertEquals('single_quoted', Env::get('TEST_PARSED_SINGLE'));
    }

    public function testLoadReturnsFalseIfFileDoesNotExist(): void
    {
        $loaded = Env::load('/path/to/non_existent_env_' . uniqid() . '.env');
        $this->assertFalse($loaded);
    }
}

