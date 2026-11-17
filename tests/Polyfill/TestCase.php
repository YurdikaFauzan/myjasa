<?php

namespace PHPUnit\Framework;

use Exception;

/**
 * Lightweight TestCase polyfill for running tests without PHPUnit installed locally.
 * When PHPUnit is installed via Composer, this polyfill is skipped automatically.
 */
abstract class TestCase
{
    private int $assertionCount = 0;

    protected function setUp(): void
    {
    }

    protected function tearDown(): void
    {
    }

    public function getAssertionCount(): int
    {
        return $this->assertionCount;
    }

    public function resetAssertionCount(): void
    {
        $this->assertionCount = 0;
    }

    protected function assertTrue(mixed $condition, string $message = ''): void
    {
        $this->assertionCount++;
        if ($condition !== true) {
            throw new Exception($message ?: "Failed asserting that " . var_export($condition, true) . " is true.");
        }
    }

    protected function assertFalse(mixed $condition, string $message = ''): void
    {
        $this->assertionCount++;
        if ($condition !== false) {
            throw new Exception($message ?: "Failed asserting that " . var_export($condition, true) . " is false.");
        }
    }

    protected function assertEquals(mixed $expected, mixed $actual, string $message = ''): void
    {
        $this->assertionCount++;
        if ($expected != $actual) {
            throw new Exception($message ?: "Failed asserting that " . var_export($actual, true) . " matches expected " . var_export($expected, true) . ".");
        }
    }

    protected function assertNotEquals(mixed $expected, mixed $actual, string $message = ''): void
    {
        $this->assertionCount++;
        if ($expected == $actual) {
            throw new Exception($message ?: "Failed asserting that " . var_export($actual, true) . " is not equal to " . var_export($expected, true) . ".");
        }
    }

    protected function assertSame(mixed $expected, mixed $actual, string $message = ''): void
    {
        $this->assertionCount++;
        if ($expected !== $actual) {
            throw new Exception($message ?: "Failed asserting that " . var_export($actual, true) . " is identical to " . var_export($expected, true) . ".");
        }
    }

    protected function assertNotSame(mixed $expected, mixed $actual, string $message = ''): void
    {
        $this->assertionCount++;
        if ($expected === $actual) {
            throw new Exception($message ?: "Failed asserting that " . var_export($actual, true) . " is not identical to " . var_export($expected, true) . ".");
        }
    }

    protected function assertNull(mixed $actual, string $message = ''): void
    {
        $this->assertionCount++;
        if ($actual !== null) {
            throw new Exception($message ?: "Failed asserting that " . var_export($actual, true) . " is null.");
        }
    }

    protected function assertNotNull(mixed $actual, string $message = ''): void
    {
        $this->assertionCount++;
        if ($actual === null) {
            throw new Exception($message ?: "Failed asserting that value is not null.");
        }
    }

    protected function assertArrayHasKey(string|int $key, array $array, string $message = ''): void
    {
        $this->assertionCount++;
        if (!array_key_exists($key, $array)) {
            throw new Exception($message ?: "Failed asserting that array contains key '{$key}'.");
        }
    }

    protected function assertCount(int $expectedCount, mixed $haystack, string $message = ''): void
    {
        $this->assertionCount++;
        $actualCount = is_countable($haystack) ? count($haystack) : 0;
        if ($actualCount !== $expectedCount) {
            throw new Exception($message ?: "Failed asserting that actual count {$actualCount} matches expected count {$expectedCount}.");
        }
    }

    protected function assertStringContainsString(string $needle, string $haystack, string $message = ''): void
    {
        $this->assertionCount++;
        if (!str_contains($haystack, $needle)) {
            throw new Exception($message ?: "Failed asserting that '{$haystack}' contains '{$needle}'.");
        }
    }

    protected function assertContains(mixed $needle, iterable $haystack, string $message = ''): void
    {
        $this->assertionCount++;
        $found = false;
        foreach ($haystack as $item) {
            if ($item === $needle) {
                $found = true;
                break;
            }
        }
        if (!$found) {
            throw new Exception($message ?: "Failed asserting that collection contains " . var_export($needle, true) . ".");
        }
    }

    protected function assertNotContains(mixed $needle, iterable $haystack, string $message = ''): void
    {
        $this->assertionCount++;
        $found = false;
        foreach ($haystack as $item) {
            if ($item === $needle) {
                $found = true;
                break;
            }
        }
        if ($found) {
            throw new Exception($message ?: "Failed asserting that collection does not contain " . var_export($needle, true) . ".");
        }
    }

    protected function assertMatchesRegularExpression(string $pattern, string $string, string $message = ''): void
    {
        $this->assertionCount++;
        if (!preg_match($pattern, $string)) {
            throw new Exception($message ?: "Failed asserting that '{$string}' matches pattern '{$pattern}'.");
        }
    }
}
