<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Models\OtpVerification;

class OtpVerificationTest extends TestCase
{
    public function testNormalizePhoneWithStandardLeadingZero(): void
    {
        $normalized = OtpVerification::normalizePhone('081234567890');
        $this->assertEquals('6281234567890', $normalized);
    }

    public function testNormalizePhoneWithPlusSixtyTwo(): void
    {
        $normalized = OtpVerification::normalizePhone('+6281234567890');
        $this->assertEquals('6281234567890', $normalized);
    }

    public function testNormalizePhoneWithLeadingEight(): void
    {
        $normalized = OtpVerification::normalizePhone('81234567890');
        $this->assertEquals('6281234567890', $normalized);
    }

    public function testNormalizePhoneWithPunctuationAndDashes(): void
    {
        $normalized = OtpVerification::normalizePhone('0812-3456-7890');
        $this->assertEquals('6281234567890', $normalized);
    }

    public function testNormalizePhoneRejectsInvalidNumbers(): void
    {
        $this->assertNull(OtpVerification::normalizePhone(''));
        $this->assertNull(OtpVerification::normalizePhone('12345'));
        $this->assertNull(OtpVerification::normalizePhone('abcdefghijk'));
        $this->assertNull(OtpVerification::normalizePhone('021123456')); // Bukan nomor seluler 08
    }

    public function testFormatDisplayPhone(): void
    {
        $formatted = OtpVerification::formatDisplayPhone('081234567890');
        $this->assertEquals('+62 812-3456-7890', $formatted);
    }

    public function testMaskPhoneHidesMiddleDigits(): void
    {
        $masked = OtpVerification::maskPhone('081234567890');
        $this->assertStringContainsString('****', $masked);
        $this->assertStringContainsString('62812', $masked);
        $this->assertStringContainsString('890', $masked);
    }
}

