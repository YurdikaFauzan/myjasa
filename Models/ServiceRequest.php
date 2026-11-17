<?php

namespace Models;

class ServiceRequest extends BaseModel
{
    protected string $table = 'service_requests';

    public const STATUS_PENDING    = 'pending';
    public const STATUS_ASSIGNED   = 'assigned';
    public const STATUS_ACCEPTED   = 'accepted';
    public const STATUS_ON_THE_WAY = 'on_the_way';
    public const STATUS_REPAIRING  = 'repairing';
    public const STATUS_COMPLETED  = 'completed';
    public const STATUS_CANCELLED  = 'cancelled';

    /**
     * Get valid statuses
     *
     * @return array
     */
    public static function getValidStatuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_ASSIGNED,
            self::STATUS_ACCEPTED,
            self::STATUS_ON_THE_WAY,
            self::STATUS_REPAIRING,
            self::STATUS_COMPLETED,
            self::STATUS_CANCELLED
        ];
    }

    /**
     * Validate service request data
     *
     * @param array $data
     * @return array
     */
    public function validate(array $data): array
    {
        $errors = [];

        if (empty($data['customer_id']) || (int)$data['customer_id'] <= 0) {
            $errors['customer_id'] = 'ID Customer wajib diisi.';
        }

        if (empty($data['service_id']) || (int)$data['service_id'] <= 0) {
            $errors['service_id'] = 'Layanan perbaikan wajib dipilih.';
        }

        if (empty($data['complaint']) || strlen(trim($data['complaint'])) < 5) {
            $errors['complaint'] = 'Deskripsi keluhan minimal 5 karakter.';
        }

        if (empty($data['address']) || strlen(trim($data['address'])) < 5) {
            $errors['address'] = 'Alamat lokasi perbaikan wajib diisi dengan lengkap.';
        }

        if (empty($data['schedule'])) {
            $errors['schedule'] = 'Jadwal kunjungan teknisi wajib diisi.';
        }

        if (!empty($data['status']) && !in_array($data['status'], self::getValidStatuses())) {
            $errors['status'] = 'Status pesanan tidak valid.';
        }

        return $errors;
    }

    /**
     * Relationship: ServiceRequest belongs to Customer (User)
     *
     * @param int $customerId
     * @return array|null
     */
    public function customer(int $customerId): ?array
    {
        return $this->belongsTo(User::class, 'customer_id', $customerId);
    }

    /**
     * Relationship: ServiceRequest belongs to Technician (User)
     *
     * @param int|null $technicianId
     * @return array|null
     */
    public function technician(?int $technicianId): ?array
    {
        return $this->belongsTo(User::class, 'technician_id', $technicianId);
    }

    /**
     * Relationship: ServiceRequest belongs to Service
     *
     * @param int $serviceId
     * @return array|null
     */
    public function service(int $serviceId): ?array
    {
        return $this->belongsTo(Service::class, 'service_id', $serviceId);
    }

    /**
     * Relationship: ServiceRequest has one Payment
     *
     * @param int $requestId
     * @return array|null
     */
    public function payment(int $requestId): ?array
    {
        return $this->hasOne(Payment::class, 'service_request_id', $requestId);
    }

    /**
     * Relationship: ServiceRequest has one Review
     *
     * @param int $requestId
     * @return array|null
     */
    public function review(int $requestId): ?array
    {
        return $this->hasOne(Review::class, 'service_request_id', $requestId);
    }

    /**
     * Relationship: ServiceRequest belongs to AiConsultation
     *
     * @param int|null $consultationId
     * @return array|null
     */
    public function aiConsultation(?int $consultationId): ?array
    {
        return $this->belongsTo(AiConsultation::class, 'ai_consultation_id', $consultationId);
    }

    /**
     * Record a status change entry in status_logs table
     *
     * @param int $requestId
     * @param string $status
     * @param string|null $note
     * @return bool
     */
    public function logStatusChange(int $requestId, string $status, ?string $note = null): bool
    {
        if (!$this->db) return false;
        $stmt = $this->db->prepare("INSERT INTO `status_logs` (`service_request_id`, `status`, `note`, `created_at`) VALUES (:req_id, :status, :note, CURRENT_TIMESTAMP)");
        return $stmt->execute([
            'req_id' => $requestId,
            'status' => $status,
            'note'   => $note
        ]);
    }

    /**
     * Get all status transition logs for a service request
     *
     * @param int $requestId
     * @return array
     */
    public function getStatusLogs(int $requestId): array
    {
        if (!$this->db) return [];
        $stmt = $this->db->prepare("SELECT * FROM `status_logs` WHERE `service_request_id` = :req_id ORDER BY `id` ASC");
        $stmt->execute(['req_id' => $requestId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Update request status and record log entry atomically
     *
     * @param int $requestId
     * @param string $newStatus
     * @param string|null $note
     * @param array $additionalData
     * @return bool
     */
    public function updateStatusWithLog(int $requestId, string $newStatus, ?string $note = null, array $additionalData = []): bool
    {
        $updateData = array_merge($additionalData, ['status' => $newStatus]);
        $updated = $this->update($requestId, $updateData);
        if ($updated) {
            $this->logStatusChange($requestId, $newStatus, $note);
        }
        return $updated;
    }
}
