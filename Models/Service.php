<?php

namespace Models;

class Service extends BaseModel
{
    protected string $table = 'services';

    public const STATUS_ACTIVE   = 'active';
    public const STATUS_INACTIVE = 'inactive';

    /**
     * Validate service data
     *
     * @param array $data
     * @return array
     */
    public function validate(array $data): array
    {
        $errors = [];

        if (empty($data['category_id']) || (int)$data['category_id'] <= 0) {
            $errors['category_id'] = 'Kategori layanan wajib dipilih.';
        } else {
            $categoryModel = new Category();
            if (!$categoryModel->find((int)$data['category_id'])) {
                $errors['category_id'] = 'Kategori yang dipilih tidak ditemukan.';
            }
        }

        if (empty($data['name']) || strlen(trim($data['name'])) < 3) {
            $errors['name'] = 'Nama layanan minimal 3 karakter.';
        }

        if (!isset($data['price']) || (float)$data['price'] < 0) {
            $errors['price'] = 'Harga layanan harus bernilai positif (>= 0).';
        }

        return $errors;
    }

    /**
     * Relationship: Service belongs to Category
     *
     * @param int $categoryId
     * @return array|null
     */
    public function category(int $categoryId): ?array
    {
        return $this->belongsTo(Category::class, 'category_id', $categoryId);
    }
}
