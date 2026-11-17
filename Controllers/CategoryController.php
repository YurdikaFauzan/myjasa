<?php

namespace Controllers;

use Config\Env;
use Middleware\AuthMiddleware;
use Models\User;
use Models\Category;

class CategoryController extends BaseController
{
    private Category $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new Category();
    }

    /**
     * Display Category Management Page (Admin only)
     */
    public function index(): void
    {
        $user = AuthMiddleware::requireRole(User::ROLE_ADMIN);

        $categories = $this->categoryModel->all('id', 'ASC');

        // Attach services count for each category
        foreach ($categories as &$cat) {
            $cat['services_count'] = count($this->categoryModel->services((int)$cat['id']));
        }

        $this->render('admin/categories/index', [
            'pageTitle'  => 'Kelola Kategori Peralatan - SmartService AI',
            'user'       => $user,
            'flash'      => AuthMiddleware::getFlash(),
            'categories' => $categories,
            'csrfToken'  => AuthMiddleware::generateCsrfToken()
        ]);
    }

    /**
     * Create New Category (Admin only)
     */
    public function create(): void
    {
        AuthMiddleware::requireRole(User::ROLE_ADMIN);

        $csrfToken = $_POST['csrf_token'] ?? null;
        if (!AuthMiddleware::verifyCsrfToken($csrfToken)) {
            AuthMiddleware::setFlash('danger', 'Token CSRF tidak valid.');
            $this->redirectTo('/admin/categories');
            return;
        }

        $name        = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $status      = $_POST['status'] ?? Category::STATUS_ACTIVE;

        $errors = $this->categoryModel->validate([
            'name' => $name
        ]);

        if (!empty($errors)) {
            AuthMiddleware::setFlash('danger', reset($errors));
            $this->redirectTo('/admin/categories');
            return;
        }

        $newId = $this->categoryModel->create([
            'name'        => $name,
            'description' => $description,
            'status'      => in_array($status, [Category::STATUS_ACTIVE, Category::STATUS_INACTIVE]) ? $status : Category::STATUS_ACTIVE
        ]);

        if ($newId) {
            AuthMiddleware::setFlash('success', "Kategori '{$name}' berhasil ditambahkan.");
        } else {
            AuthMiddleware::setFlash('danger', 'Gagal menambahkan kategori.');
        }

        $this->redirectTo('/admin/categories');
    }

    /**
     * Edit Category (Admin only)
     */
    public function edit(): void
    {
        AuthMiddleware::requireRole(User::ROLE_ADMIN);

        $csrfToken = $_POST['csrf_token'] ?? null;
        if (!AuthMiddleware::verifyCsrfToken($csrfToken)) {
            AuthMiddleware::setFlash('danger', 'Token CSRF tidak valid.');
            $this->redirectTo('/admin/categories');
            return;
        }

        $id          = (int)($_POST['id'] ?? 0);
        $name        = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $status      = $_POST['status'] ?? Category::STATUS_ACTIVE;

        $category = $this->categoryModel->find($id);
        if (!$category) {
            AuthMiddleware::setFlash('danger', 'Kategori tidak ditemukan.');
            $this->redirectTo('/admin/categories');
            return;
        }

        $errors = $this->categoryModel->validate(['name' => $name], $id);
        if (!empty($errors)) {
            AuthMiddleware::setFlash('danger', reset($errors));
            $this->redirectTo('/admin/categories');
            return;
        }

        $updated = $this->categoryModel->update($id, [
            'name'        => $name,
            'description' => $description,
            'status'      => in_array($status, [Category::STATUS_ACTIVE, Category::STATUS_INACTIVE]) ? $status : Category::STATUS_ACTIVE
        ]);

        if ($updated) {
            AuthMiddleware::setFlash('success', "Kategori '{$name}' berhasil diperbarui.");
        } else {
            AuthMiddleware::setFlash('danger', 'Gagal memperbarui kategori.');
        }

        $this->redirectTo('/admin/categories');
    }

    /**
     * Toggle Category Status Active/Inactive (Admin only)
     */
    public function toggleStatus(): void
    {
        AuthMiddleware::requireRole(User::ROLE_ADMIN);

        $id = (int)($_POST['id'] ?? 0);
        $category = $this->categoryModel->find($id);

        if (!$category) {
            AuthMiddleware::setFlash('danger', 'Kategori tidak ditemukan.');
            $this->redirectTo('/admin/categories');
            return;
        }

        $newStatus = ($category['status'] === Category::STATUS_ACTIVE) ? Category::STATUS_INACTIVE : Category::STATUS_ACTIVE;

        $updated = $this->categoryModel->update($id, ['status' => $newStatus]);

        if ($updated) {
            $statusLabel = ($newStatus === Category::STATUS_ACTIVE) ? 'diaktifkan' : 'dinonaktifkan';
            AuthMiddleware::setFlash('success', "Status kategori '{$category['name']}' berhasil {$statusLabel}.");
        } else {
            AuthMiddleware::setFlash('danger', 'Gagal mengubah status kategori.');
        }

        $this->redirectTo('/admin/categories');
    }

    /**
     * Delete Category (Admin only)
     */
    public function delete(): void
    {
        AuthMiddleware::requireRole(User::ROLE_ADMIN);

        $csrfToken = $_POST['csrf_token'] ?? null;
        if ($csrfToken !== null && !AuthMiddleware::verifyCsrfToken($csrfToken)) {
            AuthMiddleware::setFlash('danger', 'Token CSRF tidak valid. Silakan ulangi.');
            $this->redirectTo('/admin/categories');
            return;
        }

        $id = (int)($_POST['id'] ?? 0);
        $category = $this->categoryModel->find($id);

        if (!$category) {
            AuthMiddleware::setFlash('danger', 'Kategori tidak ditemukan.');
            $this->redirectTo('/admin/categories');
            return;
        }

        try {
            $deleted = $this->categoryModel->delete($id);

            if ($deleted) {
                AuthMiddleware::setFlash('success', "Kategori '{$category['name']}' berhasil dihapus.");
            } else {
                AuthMiddleware::setFlash('danger', 'Gagal menghapus kategori.');
            }
        } catch (\PDOException $e) {
            if ($e->getCode() === '23000' || str_contains($e->getMessage(), '1451')) {
                $this->categoryModel->update($id, ['status' => Category::STATUS_INACTIVE]);
                AuthMiddleware::setFlash('warning', "Kategori '{$category['name']}' memiliki data layanan terkait sehingga otomatis dinonaktifkan.");
            } else {
                AuthMiddleware::setFlash('danger', 'Terjadi kesalahan basis data: ' . $e->getMessage());
            }
        } catch (\Throwable $e) {
            AuthMiddleware::setFlash('danger', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }

        $this->redirectTo('/admin/categories');
    }
}

