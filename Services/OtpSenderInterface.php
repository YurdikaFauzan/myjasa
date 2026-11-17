<?php

namespace Services;

interface OtpSenderInterface
{
    /**
     * Kirim kode OTP ke nomor tujuan
     *
     * @param string $target Nomor tujuan WhatsApp
     * @param string $otpCode Kode OTP numerik
     * @param array $metadata Informasi tambahan (nama aplikasi, masa berlaku, dsb.)
     * @return array [success => bool, message => string, data => ?array]
     */
    public function sendOtp(string $target, string $otpCode, array $metadata = []): array;

    /**
     * Kirim pesan WhatsApp umum
     *
     * @param string $target Nomor tujuan
     * @param string $message Isi pesan
     * @return array [success => bool, message => string, data => ?array]
     */
    public function sendMessage(string $target, string $message): array;

    /**
     * Periksa status koneksi perangkat ke gateway
     *
     * @return array [connected => bool, device => ?string, status => string, message => string]
     */
    public function checkStatus(): array;
}
