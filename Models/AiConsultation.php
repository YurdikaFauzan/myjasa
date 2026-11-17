<?php

namespace Models;

class AiConsultation extends BaseModel
{
    protected string $table = 'ai_consultations';

    /**
     * Validate AI consultation data
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

        if (empty($data['question']) || strlen(trim($data['question'])) < 3) {
            $errors['question'] = 'Pertanyaan / deskripsi masalah awal wajib diisi.';
        }

        if (empty($data['answer'])) {
            $errors['answer'] = 'Hasil diagnosis / jawaban AI wajib diisi.';
        }

        return $errors;
    }

    /**
     * Relationship: AiConsultation belongs to Customer (User)
     *
     * @param int $customerId
     * @return array|null
     */
    public function customer(int $customerId): ?array
    {
        return $this->belongsTo(User::class, 'customer_id', $customerId);
    }

    /**
     * Relationship: AiConsultation belongs to Category
     *
     * @param int|null $categoryId
     * @return array|null
     */
    public function category(?int $categoryId): ?array
    {
        return $this->belongsTo(Category::class, 'category_id', $categoryId);
    }
}
