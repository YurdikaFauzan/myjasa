<?php

namespace Models;

class Category extends BaseModel
{
    protected string $table = 'categories';

    public const STATUS_ACTIVE   = 'active';
    public const STATUS_INACTIVE = 'inactive';

    /**
     * Validate category data
     *
     * @param array $data
     * @param int|null $categoryId
     * @return array
     */
    public function validate(array $data, ?int $categoryId = null): array
    {
        $errors = [];

        if (empty($data['name']) || strlen(trim($data['name'])) < 2) {
            $errors['name'] = 'Nama kategori minimal 2 karakter.';
        } else {
            $existing = $this->findBy('name', trim($data['name']));
            if ($existing && ($categoryId === null || (int)$existing['id'] !== $categoryId)) {
                $errors['name'] = 'Nama kategori sudah ada.';
            }
        }

        return $errors;
    }

    /**
     * Relationship: Category has many Services
     *
     * @param int $categoryId
     * @return array
     */
    public function services(int $categoryId): array
    {
        return $this->hasMany(Service::class, 'category_id', $categoryId);
    }

    /**
     * Relationship: Category has many AiConsultations
     *
     * @param int $categoryId
     * @return array
     */
    public function consultations(int $categoryId): array
    {
        return $this->hasMany(AiConsultation::class, 'category_id', $categoryId);
    }
}
