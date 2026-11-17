<?php

namespace Models;

class Review extends BaseModel
{
    protected string $table = 'reviews';

    /**
     * Validate review data
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

        if (empty($data['customer_id']) || (int)$data['customer_id'] <= 0) {
            $errors['customer_id'] = 'ID Customer wajib diisi.';
        }

        if (empty($data['technician_id']) || (int)$data['technician_id'] <= 0) {
            $errors['technician_id'] = 'ID Teknisi wajib diisi.';
        }

        if (!isset($data['rating']) || (int)$data['rating'] < 1 || (int)$data['rating'] > 5) {
            $errors['rating'] = 'Rating harus berada dalam skala 1 sampai 5.';
        }

        return $errors;
    }

    /**
     * Relationship: Review belongs to ServiceRequest
     *
     * @param int $requestId
     * @return array|null
     */
    public function serviceRequest(int $requestId): ?array
    {
        return $this->belongsTo(ServiceRequest::class, 'service_request_id', $requestId);
    }

    /**
     * Relationship: Review belongs to Customer (User)
     *
     * @param int $customerId
     * @return array|null
     */
    public function customer(int $customerId): ?array
    {
        return $this->belongsTo(User::class, 'customer_id', $customerId);
    }

    /**
     * Relationship: Review belongs to Technician (User)
     *
     * @param int $technicianId
     * @return array|null
     */
    public function technician(int $technicianId): ?array
    {
        return $this->belongsTo(User::class, 'technician_id', $technicianId);
    }
}
