<?php

namespace Controllers;

use Config\Env;
use Middleware\AuthMiddleware;
use Models\User;
use Models\Service;
use Models\Category;

class ServiceController extends BaseController
{
    private Service $serviceModel;
    private Category $categoryModel;

    public function __construct()
    {
        $this->serviceModel  = new Service();
        $this->categoryModel = new Category();
    }

    /**
     * Display Service Management Page (Admin only)
     */
    public function index(): void
    {
        $user = AuthMiddleware::requireRole(User::ROLE_ADMIN);

        $services   = $this->serviceModel->all('id', 'ASC');
        $categories = $this->categoryModel->all('name', 'ASC');

        // Attach Category object info for each service
        foreach ($services as &$s) {
            $s['category'] = $this->categoryModel->find((int)$s['category_id']);
        }

        $this->render('admin/services/index', [
            'pageTitle'  => 'Kelola Layanan Perbaikan - SmartService AI',
            'user'       => $user,
            'flash'      => AuthMiddleware::getFlash(),
            'services'   => $services,
            'categories' => $categories,
            'csrfToken'  => AuthMiddleware::generateCsrfToken()
        ]);
    }

    /**
     * Create New Service (Admin only)
     */
    public function create(): void
    {
        AuthMiddleware::requireRole(User::ROLE_ADMIN);

        $csrfToken = $_POST['csrf_token'] ?? null;
        if (!AuthMiddleware::verifyCsrfToken($csrfToken)) {
            AuthMiddleware::setFlash('danger', 'Token CSRF tidak valid.');
            $this->redirectTo('/admin/services');
            return;
        }

        $categoryId  = (int)($_POST['category_id'] ?? 0);
        $name        = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $price       = (float)($_POST['price'] ?? 0);
        $duration    = trim($_POST['duration'] ?? '');
        $status      = $_POST['status'] ?? Service::STATUS_ACTIVE;

        $errors = $this->serviceModel->validate([
            'category_id' => $categoryId,
            'name'        => $name,
            'price'       => $price
        ]);

        if (!empty($errors)) {
            AuthMiddleware::setFlash('danger', reset($errors));
            $this->redirectTo('/admin/services');
            return;
        }

        $newId = $this->serviceModel->create([
            'category_id' => $categoryId,
            'name'        => $name,
            'description' => $description,
            'price'       => $price,
            'duration'    => $duration,
            'status'      => in_array($status, [Service::STATUS_ACTIVE, Service::STATUS_INACTIVE]) ? $status : Service::STATUS_ACTIVE
        ]);

        if ($newId) {
            AuthMiddleware::setFlash('success', "Layanan '{$name}' berhasil ditambahkan.");
        } else {
            AuthMiddleware::setFlash('danger', 'Gagal menambahkan layanan baru.');
        }

        $this->redirectTo('/admin/services');
    }

    /**
     * Edit Service (Admin only)
     */
    public function edit(): void
    {
        AuthMiddleware::requireRole(User::ROLE_ADMIN);

        $csrfToken = $_POST['csrf_token'] ?? null;
        if (!AuthMiddleware::verifyCsrfToken($csrfToken)) {
            AuthMiddleware::setFlash('danger', 'Token CSRF tidak valid.');
            $this->redirectTo('/admin/services');
            return;
        }

        $id          = (int)($_POST['id'] ?? 0);
        $categoryId  = (int)($_POST['category_id'] ?? 0);
        $name        = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $price       = (float)($_POST['price'] ?? 0);
        $duration    = trim($_POST['duration'] ?? '');
        $status      = $_POST['status'] ?? Service::STATUS_ACTIVE;

        $service = $this->serviceModel->find($id);
        if (!$service) {
            AuthMiddleware::setFlash('danger', 'Layanan tidak ditemukan.');
            $this->redirectTo('/admin/services');
            return;
        }

        $errors = $this->serviceModel->validate([
            'category_id' => $categoryId,
            'name'        => $name,
            'price'       => $price
        ]);

        if (!empty($errors)) {
            AuthMiddleware::setFlash('danger', reset($errors));
            $this->redirectTo('/admin/services');
            return;
        }

        $updated = $this->serviceModel->update($id, [
            'category_id' => $categoryId,
            'name'        => $name,
            'description' => $description,
            'price'       => $price,
            'duration'    => $duration,
            'status'      => in_array($status, [Service::STATUS_ACTIVE, Service::STATUS_INACTIVE]) ? $status : Service::STATUS_ACTIVE
        ]);

        if ($updated) {
            AuthMiddleware::setFlash('success', "Layanan '{$name}' berhasil diperbarui.");
        } else {
            AuthMiddleware::setFlash('danger', 'Gagal memperbarui layanan.');
        }

        $this->redirectTo('/admin/services');
    }

    /**
     * Toggle Service Status Active/Inactive (Admin only)
     */
    public function toggleStatus(): void
    {
        AuthMiddleware::requireRole(User::ROLE_ADMIN);

        $id = (int)($_POST['id'] ?? 0);
        $service = $this->serviceModel->find($id);

        if (!$service) {
            AuthMiddleware::setFlash('danger', 'Layanan tidak ditemukan.');
            $this->redirectTo('/admin/services');
            return;
        }

        $newStatus = ($service['status'] === Service::STATUS_ACTIVE) ? Service::STATUS_INACTIVE : Service::STATUS_ACTIVE;

        $updated = $this->serviceModel->update($id, ['status' => $newStatus]);

        if ($updated) {
            $statusLabel = ($newStatus === Service::STATUS_ACTIVE) ? 'diaktifkan' : 'dinonaktifkan';
            AuthMiddleware::setFlash('success', "Status layanan '{$service['name']}' berhasil {$statusLabel}.");
        } else {
            AuthMiddleware::setFlash('danger', 'Gagal mengubah status layanan.');
        }

        $this->redirectTo('/admin/services');
    }

    /**
     * Delete Service (Admin only)
     */
    public function delete(): void
    {
        AuthMiddleware::requireRole(User::ROLE_ADMIN);

        $csrfToken = $_POST['csrf_token'] ?? null;
        if ($csrfToken !== null && !AuthMiddleware::verifyCsrfToken($csrfToken)) {
            AuthMiddleware::setFlash('danger', 'Token CSRF tidak valid. Silakan ulangi.');
            $this->redirectTo('/admin/services');
            return;
        }

        $id = (int)($_POST['id'] ?? 0);
        $service = $this->serviceModel->find($id);

        if (!$service) {
            AuthMiddleware::setFlash('danger', 'Layanan tidak ditemukan.');
            $this->redirectTo('/admin/services');
            return;
        }

        try {
            $deleted = $this->serviceModel->delete($id);

            if ($deleted) {
                AuthMiddleware::setFlash('success', "Layanan '{$service['name']}' berhasil dihapus.");
            } else {
                AuthMiddleware::setFlash('danger', 'Gagal menghapus layanan dari basis data.');
            }
        } catch (\PDOException $e) {
            if ($e->getCode() === '23000' || str_contains($e->getMessage(), '1451')) {
                $this->serviceModel->update($id, ['status' => Service::STATUS_INACTIVE]);
                AuthMiddleware::setFlash('warning', "Layanan '{$service['name']}' terhubung dengan riwayat pesanan pelanggan sehingga otomatis dinonaktifkan demi menjaga keutuhan data transaksi.");
            } else {
                AuthMiddleware::setFlash('danger', 'Terjadi kesalahan basis data: ' . $e->getMessage());
            }
        } catch (\Throwable $e) {
            AuthMiddleware::setFlash('danger', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }

        $this->redirectTo('/admin/services');
    }
}

