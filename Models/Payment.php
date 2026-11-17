<?php

namespace Models;

class Payment extends BaseModel
{
    protected string $table = 'payments';

    public const STATUS_PENDING  = 'pending';
    public const STATUS_PAID     = 'paid';
    public const STATUS_FAILED   = 'failed';
    public const STATUS_REFUNDED = 'refunded';

    public static function getValidStatuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_PAID,
            self::STATUS_FAILED,
            self::STATUS_REFUNDED
        ];
    }

    /**
     * Validate payment data
     *
     * @param array $data
     * @return array
     */
    public function validate(array $data): array
    {
        $errors = [];

        if (empty($data['service_request_id']) || (int)$data['service_request_id'] <= 0) {
            $errors['service_request_id'] = 'ID Pesanan layanan wajib diisi.';
        }

        if (!isset($data['amount']) || (float)$data['amount'] < 0) {
            $errors['amount'] = 'Jumlah pembayaran (amount) harus bernilai positif.';
        }

        if (!empty($data['payment_status']) && !in_array($data['payment_status'], self::getValidStatuses())) {
            $errors['payment_status'] = 'Status pembayaran tidak valid.';
        }

        return $errors;
    }

    /**
     * Relationship: Payment belongs to ServiceRequest
     *
     * @param int $requestId
     * @return array|null
     */
    public function serviceRequest(int $requestId): ?array
    {
        return $this->belongsTo(ServiceRequest::class, 'service_request_id', $requestId);
    }
}
