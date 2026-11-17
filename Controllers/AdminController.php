<?php

namespace Controllers;

use Config\Env;
use Middleware\AuthMiddleware;
use Models\User;
use Models\ServiceRequest;
use Models\Category;
use Models\Service;
use Models\AiConfiguration;
use Services\GroqService;

class AdminController extends BaseController
{
    private User $userModel;
    private ServiceRequest $requestModel;
    private Category $categoryModel;
    private Service $serviceModel;
    private AiConfiguration $aiConfigModel;
    private GroqService $groqService;

    public function __construct()
    {
        $this->userModel     = new User();
        $this->requestModel  = new ServiceRequest();
        $this->categoryModel = new Category();
        $this->serviceModel  = new Service();
        $this->aiConfigModel = new AiConfiguration();
        $this->groqService   = new GroqService();
    }

    /**
     * Admin Dashboard Overview
     */
    public function dashboard(): void
    {
        $user = AuthMiddleware::requireRole(User::ROLE_ADMIN);

        $allUsers    = $this->userModel->all();
        $allRequests = $this->requestModel->all();
        $allCats     = $this->categoryModel->all();
        $allServices = $this->serviceModel->all();

        $totalCustomer     = count(array_filter($allUsers, fn($u) => $u['role'] === User::ROLE_CUSTOMER));
        $totalTechnician   = count(array_filter($allUsers, fn($u) => $u['role'] === User::ROLE_TECHNICIAN));
        $totalCategories   = count($allCats);
        $totalServices     = count($allServices);
        $totalRequests     = count($allRequests);
        $pendingRequests   = count(array_filter($allRequests, fn($r) => $r['status'] === ServiceRequest::STATUS_PENDING));
        $completedRequests = count(array_filter($allRequests, fn($r) => $r['status'] === ServiceRequest::STATUS_COMPLETED));

        $this->render('admin/dashboard', [
            'pageTitle'         => 'Admin Dashboard - SmartService AI',
            'user'              => $user,
            'flash'             => AuthMiddleware::getFlash(),
            'totalCustomer'     => $totalCustomer,
            'totalTechnician'   => $totalTechnician,
            'totalCategories'   => $totalCategories,
            'totalServices'     => $totalServices,
            'totalRequests'     => $totalRequests,
            'pendingRequests'   => $pendingRequests,
            'completedRequests' => $completedRequests,
            'recentRequests'    => array_slice($allRequests, 0, 5),
            'recentUsers'       => array_slice($allUsers, 0, 5),
            'requestModel'      => $this->requestModel,
            'activeAiConfig'    => \Services\Ai\AiManager::getActiveConfig()
        ]);
    }

    /**
     * Halaman Pengelolaan "Permintaan Jasa" dengan Multi-Filter
     */
    public function listRequests(): void
    {
        $user = AuthMiddleware::requireRole(User::ROLE_ADMIN);

        $allRequests  = $this->requestModel->all('id', 'DESC');
        $categories   = $this->categoryModel->all('name', 'ASC');
        $allUsers     = $this->userModel->all('name', 'ASC');
        $technicians  = array_filter($allUsers, fn($u) => $u['role'] === User::ROLE_TECHNICIAN && $u['status'] === 'active');

        // Capture Filter Parameters from GET
        $filterStatus     = $_GET['status'] ?? '';
        $filterCategory   = $_GET['category_id'] ?? '';
        $filterTechnician = $_GET['technician_id'] ?? '';
        $filterDate       = $_GET['date'] ?? '';

        // Apply Filters
        $filteredRequests = array_filter($allRequests, function($req) use ($filterStatus, $filterCategory, $filterTechnician, $filterDate) {
            if (!empty($filterStatus) && $req['status'] !== $filterStatus) {
                return false;
            }

            if (!empty($filterTechnician)) {
                if ($filterTechnician === 'unassigned' && !empty($req['technician_id'])) {
                    return false;
                }
                if ($filterTechnician !== 'unassigned' && (int)$req['technician_id'] !== (int)$filterTechnician) {
                    return false;
                }
            }

            if (!empty($filterDate)) {
                $reqDate = date('Y-m-d', strtotime($req['schedule']));
                if ($reqDate !== $filterDate) {
                    return false;
                }
            }

            if (!empty($filterCategory)) {
                $service = $this->serviceModel->find((int)$req['service_id']);
                if (!$service || (int)$service['category_id'] !== (int)$filterCategory) {
                    return false;
                }
            }

            return true;
        });

        // Enrich requests with Customer, Service, Category, and Technician information
        foreach ($filteredRequests as &$req) {
            $req['customer']   = $this->userModel->find((int)$req['customer_id']);
            $req['service']    = $this->serviceModel->find((int)$req['service_id']);
            $req['category']   = $req['service'] ? $this->categoryModel->find((int)$req['service']['category_id']) : null;
            $req['technician'] = $req['technician_id'] ? $this->userModel->find((int)$req['technician_id']) : null;
        }

        $this->render('admin/requests/index', [
            'pageTitle'        => 'Manajemen Permintaan Jasa - SmartService AI',
            'user'             => $user,
            'flash'            => AuthMiddleware::getFlash(),
            'requests'         => array_values($filteredRequests),
            'categories'       => $categories,
            'technicians'      => array_values($technicians),
            'filterStatus'     => $filterStatus,
            'filterCategory'   => $filterCategory,
            'filterTechnician' => $filterTechnician,
            'filterDate'       => $filterDate,
            'csrfToken'        => AuthMiddleware::generateCsrfToken()
        ]);
    }

    /**
     * Halaman "Detail Request" untuk Admin
     */
    public function showRequestDetail(): void
    {
        $user = AuthMiddleware::requireRole(User::ROLE_ADMIN);

        $id = (int)($_GET['id'] ?? 0);
        $request = $this->requestModel->find($id);

        if (!$request) {
            AuthMiddleware::setFlash('danger', 'Permintaan jasa tidak ditemukan.');
            $this->redirectTo('/admin/requests');
            return;
        }

        $customer   = $this->userModel->find((int)$request['customer_id']);
        $service    = $this->serviceModel->find((int)$request['service_id']);
        $category   = $service ? $this->categoryModel->find((int)$service['category_id']) : null;
        $technician = $request['technician_id'] ? $this->userModel->find((int)$request['technician_id']) : null;
        $payment    = $this->requestModel->payment((int)$request['id']);
        $review     = $this->requestModel->review((int)$request['id']);
        $statusLogs = $this->requestModel->getStatusLogs((int)$request['id']);

        $aiConsultation = null;
        if (!empty($request['ai_consultation_id'])) {
            $aiConsultation = (new \Models\AiConsultation())->find((int)$request['ai_consultation_id']);
            if ($aiConsultation && !empty($aiConsultation['ai_result'])) {
                $aiConsultation['ai_result'] = json_decode($aiConsultation['ai_result'], true);
            }
        }

        $allUsers    = $this->userModel->all('name', 'ASC');
        $technicians = array_values(array_filter($allUsers, fn($u) => $u['role'] === User::ROLE_TECHNICIAN && $u['status'] === 'active'));

        $this->render('admin/requests/show', [
            'pageTitle'   => "Detail Request #REQ-{$id} - SmartService AI",
            'user'        => $user,
            'flash'       => AuthMiddleware::getFlash(),
            'request'     => $request,
            'customer'    => $customer,
            'service'     => $service,
            'category'    => $category,
            'technician'  => $technician,
            'aiConsultation' => $aiConsultation,
            'payment'     => $payment,
            'review'      => $review,
            'statusLogs'  => $statusLogs,
            'technicians' => $technicians,
            'csrfToken'   => AuthMiddleware::generateCsrfToken()
        ]);
    }

    /**
     * Process Assign Technician POST Request (Admin only)
     */
    public function assignTechnician(): void
    {
        AuthMiddleware::requireRole(User::ROLE_ADMIN);

        $csrfToken = $_POST['csrf_token'] ?? null;
        if (!AuthMiddleware::verifyCsrfToken($csrfToken)) {
            AuthMiddleware::setFlash('danger', 'Token CSRF tidak valid.');
            $this->redirectTo('/admin/requests');
            return;
        }

        $requestId    = (int)($_POST['request_id'] ?? 0);
        $technicianId = (int)($_POST['technician_id'] ?? 0);

        $request = $this->requestModel->find($requestId);
        if (!$request) {
            AuthMiddleware::setFlash('danger', 'Permintaan jasa tidak ditemukan.');
            $this->redirectTo('/admin/requests');
            return;
        }

        $technician = $this->userModel->find($technicianId);
        if (!$technician || $technician['role'] !== User::ROLE_TECHNICIAN) {
            AuthMiddleware::setFlash('danger', 'Teknisi yang dipilih tidak valid.');
            $this->redirectTo('/admin/requests');
            return;
        }

        // Logic Assignment:
        // Update technician_id and if status is 'pending', change status to 'assigned'
        $updateData = [
            'technician_id' => $technicianId
        ];

        if ($request['status'] === ServiceRequest::STATUS_PENDING) {
            $updateData['status'] = ServiceRequest::STATUS_ASSIGNED;
        }

        $updated = $this->requestModel->update($requestId, $updateData);

        if ($updated) {
            $this->requestModel->logStatusChange($requestId, ServiceRequest::STATUS_ASSIGNED, "Ditugaskan oleh Admin ke Teknisi: {$technician['name']}");
            AuthMiddleware::setFlash('success', "Teknisi '{$technician['name']}' berhasil ditugaskan untuk pesanan #REQ-{$requestId}. Status pesanan sekarang [ASSIGNED].");
        } else {
            AuthMiddleware::setFlash('danger', 'Gagal menugaskan teknisi.');
        }

        // Redirect back to request detail or list
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        if (str_contains($referer, '/admin/requests/detail')) {
            $this->redirectTo("/admin/requests/detail?id={$requestId}");
        } else {
            $this->redirectTo('/admin/requests');
        }
    }

    /**
     * Halaman Riwayat Layanan Sistem (Admin Service History)
     */
    public function history(): void
    {
        $user = AuthMiddleware::requireRole(User::ROLE_ADMIN);

        $allRequests = $this->requestModel->all('id', 'DESC');
        $categories  = $this->categoryModel->all('name', 'ASC');
        $allUsers    = $this->userModel->all('name', 'ASC');
        $technicians = array_values(array_filter($allUsers, fn($u) => $u['role'] === User::ROLE_TECHNICIAN));

        // Filters
        $filterStatus     = $_GET['status'] ?? '';
        $filterCategory   = $_GET['category_id'] ?? '';
        $filterTechnician = $_GET['technician_id'] ?? '';
        $filterDate       = $_GET['date'] ?? '';

        $filtered = array_filter($allRequests, function($req) use ($filterStatus, $filterCategory, $filterTechnician, $filterDate) {
            if (!empty($filterStatus) && $req['status'] !== $filterStatus) {
                return false;
            }
            if (!empty($filterTechnician) && (int)$req['technician_id'] !== (int)$filterTechnician) {
                return false;
            }
            if (!empty($filterDate) && date('Y-m-d', strtotime($req['schedule'])) !== $filterDate) {
                return false;
            }
            if (!empty($filterCategory)) {
                $service = $this->serviceModel->find((int)$req['service_id']);
                if (!$service || (int)$service['category_id'] !== (int)$filterCategory) {
                    return false;
                }
            }
            return true;
        });

        foreach ($filtered as &$req) {
            $req['customer']   = $this->userModel->find((int)$req['customer_id']);
            $req['service']    = $this->serviceModel->find((int)$req['service_id']);
            $req['category']   = $req['service'] ? $this->categoryModel->find((int)$req['service']['category_id']) : null;
            $req['technician'] = $req['technician_id'] ? $this->userModel->find((int)$req['technician_id']) : null;
            $req['review']     = $this->requestModel->review((int)$req['id']);
            $req['payment']    = $this->requestModel->payment((int)$req['id']);
        }

        $this->render('admin/history', [
            'pageTitle'        => 'Riwayat Seluruh Perbaikan - Admin SmartService AI',
            'user'             => $user,
            'flash'            => AuthMiddleware::getFlash(),
            'requests'         => array_values($filtered),
            'categories'       => $categories,
            'technicians'      => $technicians,
            'filterStatus'     => $filterStatus,
            'filterCategory'   => $filterCategory,
            'filterTechnician' => $filterTechnician,
            'filterDate'       => $filterDate,
            'csrfToken'        => AuthMiddleware::generateCsrfToken()
        ]);
    }

    /**
     * Halaman Konfigurasi Model AI Multi-Provider (Khusus Admin)
     */
    public function aiConfig(): void
    {
        $user = AuthMiddleware::requireLogin();

        $activeConfig = \Services\Ai\AiManager::getActiveConfig();
        $supportedProviders = \Services\Ai\AiManager::getSupportedProviders();
        $allConfigs = $this->aiConfigModel->getAllConfigs();

        // Siapkan kartu data untuk setiap provider
        $providerCards = [];
        foreach ($supportedProviders as $k => $info) {
            $saved = $allConfigs[$k] ?? null;
            $keyStatus = \Services\Ai\AiManager::getApiKeyStatus($k, $saved['api_key'] ?? null);
            $providerCards[$k] = array_merge($info, [
                'is_active'         => !empty($saved['is_active']),
                'saved_model'       => $saved['model_id'] ?? $info['default_model'],
                'saved_base_url'    => $saved['base_url'] ?? $info['default_base_url'],
                'saved_temperature' => isset($saved['temperature']) ? (float)$saved['temperature'] : $info['default_temperature'],
                'saved_max_tokens'  => isset($saved['max_tokens']) ? (int)$saved['max_tokens'] : $info['default_max_tokens'],
                'saved_timeout'     => isset($saved['timeout']) ? (int)$saved['timeout'] : $info['default_timeout'],
                'has_api_key'       => $keyStatus['has_key'],
                'key_source'        => $keyStatus['source'],
                'masked_api_key'    => $keyStatus['masked'],
                'last_tested_at'    => $saved['last_tested_at'] ?? null,
                'last_test_status'  => $saved['last_test_status'] ?? null,
                'last_test_latency' => $saved['last_test_latency'] ?? null,
                'last_test_message' => $saved['last_test_message'] ?? null,
                'updated_at'        => $saved['updated_at'] ?? null,
                'updated_by_name'   => $saved['updated_by_name'] ?? null
            ]);
        }

        $this->render('admin/ai-config', [
            'pageTitle'          => 'Konfigurasi Model AI - Admin SmartService AI',
            'user'               => $user,
            'flash'              => AuthMiddleware::getFlash(),
            'activeConfig'       => $activeConfig,
            'supportedProviders' => $supportedProviders,
            'providerCards'      => $providerCards,
            'csrfToken'          => AuthMiddleware::generateCsrfToken()
        ]);
    }

    /**
     * Simpan Konfigurasi & Aktivasi Provider AI (Khusus Admin)
     */
    public function saveAiConfig(): void
    {
        $user = AuthMiddleware::requireLogin();

        $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
               || (!empty($_SERVER['HTTP_ACCEPT']) && str_contains(strtolower($_SERVER['HTTP_ACCEPT']), 'application/json'));

        $csrfToken = $_POST['csrf_token'] ?? null;
        if (!AuthMiddleware::verifyCsrfToken($csrfToken)) {
            $msg = 'Token CSRF tidak valid. Silakan muat ulang halaman.';
            if ($isAjax) {
                $this->json(['success' => false, 'message' => $msg], 403);
            }
            AuthMiddleware::setFlash('danger', $msg);
            $this->redirectTo('/admin/ai-config');
            return;
        }

        unset($_SESSION['csrf_token']);

        $providerKey = strtolower(trim($_POST['provider'] ?? 'groq'));
        $supported   = \Services\Ai\AiManager::getSupportedProviders();

        if (!isset($supported[$providerKey])) {
            $msg = "Provider AI '{$providerKey}' tidak didukung sistem.";
            if ($isAjax) $this->json(['success' => false, 'message' => $msg], 422);
            AuthMiddleware::setFlash('danger', $msg);
            $this->redirectTo('/admin/ai-config');
            return;
        }

        $selectedModel = trim($_POST['model_id'] ?? '');
        $customModel   = trim($_POST['custom_model_id'] ?? '');
        $targetModel   = ($selectedModel === 'custom') ? $customModel : $selectedModel;

        if (empty($targetModel)) {
            $msg = 'Model AI wajib dipilih atau diisi.';
            if ($isAjax) $this->json(['success' => false, 'message' => $msg], 422);
            AuthMiddleware::setFlash('danger', $msg);
            $this->redirectTo('/admin/ai-config');
            return;
        }

        // Validasi format nama model (alfanumerik, slash, dash, titik, underscore, titik dua)
        if (!preg_match('/^[a-zA-Z0-9_\-\.\/:]+$/', $targetModel)) {
            $msg = 'Format ID Model AI tidak valid. Gunakan format resmi penyedia.';
            if ($isAjax) $this->json(['success' => false, 'message' => $msg], 422);
            AuthMiddleware::setFlash('danger', $msg);
            $this->redirectTo('/admin/ai-config');
            return;
        }

        $apiKey       = trim($_POST['api_key'] ?? '');
        $deleteApiKey = !empty($_POST['delete_api_key']);
        $baseUrl      = trim($_POST['base_url'] ?? '');
        $temperature  = isset($_POST['temperature']) ? (float)$_POST['temperature'] : $supported[$providerKey]['default_temperature'];
        $maxTokens    = !empty($_POST['max_tokens']) ? (int)$_POST['max_tokens'] : $supported[$providerKey]['default_max_tokens'];
        $timeout      = !empty($_POST['timeout']) ? (int)$_POST['timeout'] : $supported[$providerKey]['default_timeout'];
        $setActive    = isset($_POST['set_active']) ? (int)$_POST['set_active'] : 1; // Default aktifkan konfigurasi ini

        $saveData = [
            'provider'       => $providerKey,
            'model_id'       => $targetModel,
            'api_key'        => $apiKey,
            'delete_api_key' => $deleteApiKey,
            'base_url'       => !empty($baseUrl) ? $baseUrl : null,
            'temperature'    => max(0.0, min(2.0, $temperature)),
            'max_tokens'     => max(50, min(8192, $maxTokens)),
            'timeout'        => max(5, min(120, $timeout)),
            'is_active'      => $setActive
        ];

        $saved = $this->aiConfigModel->saveConfig($saveData, (int)$user['id'], $user['name']);

        if ($saved) {
            $provName = $supported[$providerKey]['name'];
            $msg = "Konfigurasi AI {$provName} (Model: '{$targetModel}') berhasil disimpan dan diaktifkan. Chatbot CS dan AI Diagnosis kini langsung menggunakan konfigurasi ini.";
            if ($isAjax) {
                $this->json([
                    'success'    => true,
                    'message'    => $msg,
                    'provider'   => $providerKey,
                    'model_id'   => $targetModel,
                    'is_active'  => $setActive,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => $user['name']
                ]);
            }
            AuthMiddleware::setFlash('success', $msg);
        } else {
            $msg = 'Terjadi kesalahan sistem saat menyimpan konfigurasi AI ke database.';
            if ($isAjax) $this->json(['success' => false, 'message' => $msg], 500);
            AuthMiddleware::setFlash('danger', $msg);
        }

        $this->redirectTo('/admin/ai-config');
    }

    /**
     * Endpoint AJAX Uji Model & Tes Koneksi Provider (Khusus Admin)
     */
    public function testAiModel(): void
    {
        $user = AuthMiddleware::requireLogin();

        $csrfToken = $_POST['csrf_token'] ?? null;
        if (!AuthMiddleware::verifyCsrfToken($csrfToken)) {
            $this->json(['success' => false, 'message' => 'Token CSRF tidak valid.'], 403);
            return;
        }

        $providerKey   = strtolower(trim($_POST['provider'] ?? 'groq'));
        $selectedModel = trim($_POST['model_id'] ?? '');
        $customModel   = trim($_POST['custom_model_id'] ?? '');
        $targetModel   = ($selectedModel === 'custom') ? $customModel : $selectedModel;
        $apiKey        = trim($_POST['api_key'] ?? '');
        $baseUrl       = trim($_POST['base_url'] ?? '');
        $temperature   = isset($_POST['temperature']) ? (float)$_POST['temperature'] : 0.3;
        $maxTokens     = !empty($_POST['max_tokens']) ? (int)$_POST['max_tokens'] : 60;
        $timeout       = !empty($_POST['timeout']) ? (int)$_POST['timeout'] : 20;
        $testPrompt    = trim($_POST['test_prompt'] ?? '');

        if (empty($targetModel)) {
            $this->json(['success' => false, 'message' => 'Pilih atau masukkan Model ID yang akan diuji.'], 422);
            return;
        }

        $prompt = !empty($testPrompt) ? $testPrompt : 'Halo! Jawab dalam 5 kata: Apakah Anda siap membantu pelanggan SmartService AI?';

        $result = \Services\Ai\AiManager::testConnection(
            $providerKey,
            $targetModel,
            $apiKey ?: null,
            $baseUrl ?: null,
            $temperature,
            $maxTokens,
            $timeout,
            $prompt
        );

        // Catat hasil pengujian ke database untuk provider ini
        $this->aiConfigModel->recordTestResult(
            $targetModel,
            $result['success'],
            $result['latency_ms'],
            $result['success'] ? 'Uji model berhasil (' . $result['latency_ms'] . ' ms)' : ($result['error'] ?? 'Pengujian gagal'),
            $providerKey
        );

        $this->json([
            'success'     => $result['success'],
            'provider'    => $providerKey,
            'model'       => $targetModel,
            'latency_ms'  => $result['latency_ms'],
            'output'      => $result['output'],
            'error'       => $result['error'],
            'tested_at'   => date('Y-m-d H:i:s'),
            'tested_by'   => $user['name']
        ]);
    }

    /**
     * Endpoint AJAX Muat Ulang Model (Live Fetch dari API Provider)
     */
    public function fetchModels(): void
    {
        AuthMiddleware::requireLogin();

        $csrfToken = $_POST['csrf_token'] ?? null;
        if (!AuthMiddleware::verifyCsrfToken($csrfToken)) {
            $this->json(['success' => false, 'message' => 'Token CSRF tidak valid.'], 403);
            return;
        }

        $providerKey = strtolower(trim($_POST['provider'] ?? 'groq'));
        $apiKey      = trim($_POST['api_key'] ?? '');
        $baseUrl     = trim($_POST['base_url'] ?? '');

        $models = \Services\Ai\AiManager::fetchModels($providerKey, $apiKey ?: null, $baseUrl ?: null);

        $this->json([
            'success'  => true,
            'provider' => $providerKey,
            'models'   => $models,
            'count'    => count($models)
        ]);
    }

    /**
     * Endpoint AJAX Hapus Kredensial API Key dari Database
     */
    public function deleteApiKey(): void
    {
        AuthMiddleware::requireLogin();

        $csrfToken = $_POST['csrf_token'] ?? null;
        if (!AuthMiddleware::verifyCsrfToken($csrfToken)) {
            $this->json(['success' => false, 'message' => 'Token CSRF tidak valid.'], 403);
            return;
        }

        $providerKey = strtolower(trim($_POST['provider'] ?? ''));
        if (empty($providerKey)) {
            $this->json(['success' => false, 'message' => 'Provider wajib ditentukan.'], 422);
            return;
        }

        $ok = $this->aiConfigModel->deleteApiKey($providerKey);

        $this->json([
            'success' => $ok,
            'message' => $ok ? "API Key tersimpan untuk {$providerKey} berhasil dihapus dari database." : "Gagal menghapus API Key."
        ]);
    }

    /**
     * Endpoint AJAX / Form Pulihkan Konfigurasi Default Sistem
     */
    public function resetAiConfig(): void
    {
        $user = AuthMiddleware::requireLogin();

        $csrfToken = $_POST['csrf_token'] ?? null;
        if (!AuthMiddleware::verifyCsrfToken($csrfToken)) {
            $this->json(['success' => false, 'message' => 'Token CSRF tidak valid.'], 403);
            return;
        }

        $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
               || (!empty($_SERVER['HTTP_ACCEPT']) && str_contains(strtolower($_SERVER['HTTP_ACCEPT']), 'application/json'));

        $ok = $this->aiConfigModel->restoreDefault((int)$user['id'], $user['name']);
        $msg = $ok ? 'Konfigurasi model AI berhasil dipulihkan ke setelan default sistem (Groq & qwen/qwen3.8-27b).' : 'Gagal memulihkan konfigurasi sistem.';

        if ($isAjax) {
            $this->json(['success' => $ok, 'message' => $msg]);
            return;
        }

        AuthMiddleware::setFlash($ok ? 'success' : 'danger', $msg);
        $this->redirectTo('/admin/ai-config');
    }

    /**
     * Halaman Manajemen Pengguna & Pendaftaran Akun (Admin Only)
     */
    public function listUsers(): void
    {
        $currentUser = AuthMiddleware::requireRole(User::ROLE_ADMIN);

        $allUsers = $this->userModel->all('id', 'DESC');

        $filterRole   = trim($_GET['role'] ?? '');
        $filterStatus = trim($_GET['status'] ?? '');
        $search       = trim($_GET['search'] ?? '');

        $roleCounts = [
            'admin'      => 0,
            'technician' => 0,
            'customer'   => 0
        ];

        foreach ($allUsers as $u) {
            $r = $u['role'] ?? '';
            if (isset($roleCounts[$r])) {
                $roleCounts[$r]++;
            }
        }

        // Apply filters & search
        $filteredUsers = array_filter($allUsers, function ($u) use ($filterRole, $filterStatus, $search) {
            if (!empty($filterRole) && $u['role'] !== $filterRole) {
                return false;
            }
            if (!empty($filterStatus) && $u['status'] !== $filterStatus) {
                return false;
            }
            if (!empty($search)) {
                $term = strtolower($search);
                $nameMatch  = str_contains(strtolower($u['name'] ?? ''), $term);
                $emailMatch = str_contains(strtolower($u['email'] ?? ''), $term);
                $phoneMatch = str_contains(strtolower($u['phone'] ?? ''), $term);
                if (!$nameMatch && !$emailMatch && !$phoneMatch) {
                    return false;
                }
            }
            return true;
        });

        $this->render('admin/users/index', [
            'pageTitle'    => 'Manajemen Pengguna & Pendaftaran Akun - Admin SmartService AI',
            'currentUser'  => $currentUser,
            'flash'        => AuthMiddleware::getFlash(),
            'users'        => array_values($filteredUsers),
            'roleCounts'   => $roleCounts,
            'filterRole'   => $filterRole,
            'filterStatus' => $filterStatus,
            'search'       => $search,
            'csrfToken'    => AuthMiddleware::generateCsrfToken()
        ]);
    }

    /**
     * Daftarkan Akun Baru dari Konsol Admin
     */
    public function createUser(): void
    {
        AuthMiddleware::requireRole(User::ROLE_ADMIN);

        $csrfToken = $_POST['csrf_token'] ?? null;
        if (!AuthMiddleware::verifyCsrfToken($csrfToken)) {
            AuthMiddleware::setFlash('danger', 'Token CSRF tidak valid.');
            $this->redirectTo('/admin/users');
            return;
        }

        unset($_SESSION['csrf_token']);

        $name     = trim($_POST['name'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $role     = trim($_POST['role'] ?? User::ROLE_CUSTOMER);
        $phone    = trim($_POST['phone'] ?? '');
        $address  = trim($_POST['address'] ?? '');
        $status   = trim($_POST['status'] ?? 'active');

        if (!in_array($role, User::getAvailableRoles())) {
            $role = User::ROLE_CUSTOMER;
        }

        if (!in_array($status, ['active', 'inactive', 'suspended'])) {
            $status = 'active';
        }

        $errors = $this->userModel->validate([
            'name'     => $name,
            'email'    => $email,
            'password' => $password,
            'role'     => $role
        ]);

        if (!empty($errors)) {
            AuthMiddleware::setFlash('danger', reset($errors));
            $this->redirectTo('/admin/users');
            return;
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $newUserId = $this->userModel->create([
            'name'     => $name,
            'email'    => $email,
            'password' => $hashedPassword,
            'role'     => $role,
            'phone'    => $phone ?: null,
            'address'  => $address ?: null,
            'status'   => $status
        ]);

        if ($newUserId) {
            $roleLabel = ucfirst($role);
            AuthMiddleware::setFlash('success', "Akun {$roleLabel} baru '{$name}' ({$email}) berhasil didaftarkan ke sistem!");
        } else {
            AuthMiddleware::setFlash('danger', 'Gagal mendaftarkan akun pengguna ke database.');
        }

        $this->redirectTo('/admin/users');
    }

    /**
     * Edit Akun Pengguna dari Konsol Admin
     */
    public function editUser(): void
    {
        AuthMiddleware::requireRole(User::ROLE_ADMIN);

        $csrfToken = $_POST['csrf_token'] ?? null;
        if (!AuthMiddleware::verifyCsrfToken($csrfToken)) {
            AuthMiddleware::setFlash('danger', 'Token CSRF tidak valid.');
            $this->redirectTo('/admin/users');
            return;
        }

        unset($_SESSION['csrf_token']);

        $id       = (int)($_POST['id'] ?? 0);
        $name     = trim($_POST['name'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $role     = trim($_POST['role'] ?? '');
        $phone    = trim($_POST['phone'] ?? '');
        $address  = trim($_POST['address'] ?? '');
        $status   = trim($_POST['status'] ?? '');

        $targetUser = $this->userModel->find($id);
        if (!$targetUser) {
            AuthMiddleware::setFlash('danger', 'Pengguna tidak ditemukan.');
            $this->redirectTo('/admin/users');
            return;
        }

        if (!in_array($role, User::getAvailableRoles())) {
            $role = $targetUser['role'];
        }

        if (!in_array($status, ['active', 'inactive', 'suspended'])) {
            $status = $targetUser['status'];
        }

        $validateData = [
            'name'  => $name,
            'email' => $email,
            'role'  => $role
        ];
        if (!empty($password)) {
            $validateData['password'] = $password;
        }

        $errors = $this->userModel->validate($validateData, $id);
        if (!empty($errors)) {
            AuthMiddleware::setFlash('danger', reset($errors));
            $this->redirectTo('/admin/users');
            return;
        }

        $updateData = [
            'name'    => $name,
            'email'   => $email,
            'role'    => $role,
            'phone'   => $phone ?: null,
            'address' => $address ?: null,
            'status'  => $status
        ];

        if (!empty($password)) {
            $updateData['password'] = password_hash($password, PASSWORD_BCRYPT);
        }

        $updated = $this->userModel->update($id, $updateData);

        if ($updated) {
            AuthMiddleware::setFlash('success', "Data akun '{$name}' berhasil diperbarui.");
        } else {
            AuthMiddleware::setFlash('danger', 'Gagal memperbarui data akun.');
        }

        $this->redirectTo('/admin/users');
    }

    /**
     * Hapus Akun Pengguna dari Konsol Admin
     */
    public function deleteUser(): void
    {
        $currentUser = AuthMiddleware::requireRole(User::ROLE_ADMIN);

        $csrfToken = $_POST['csrf_token'] ?? null;
        if (!AuthMiddleware::verifyCsrfToken($csrfToken)) {
            AuthMiddleware::setFlash('danger', 'Token CSRF tidak valid.');
            $this->redirectTo('/admin/users');
            return;
        }

        unset($_SESSION['csrf_token']);

        $id = (int)($_POST['id'] ?? 0);

        if ($id === (int)$currentUser['id']) {
            AuthMiddleware::setFlash('danger', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif digunakan.');
            $this->redirectTo('/admin/users');
            return;
        }

        $targetUser = $this->userModel->find($id);
        if (!$targetUser) {
            AuthMiddleware::setFlash('danger', 'Pengguna tidak ditemukan.');
            $this->redirectTo('/admin/users');
            return;
        }

        $deleted = $this->userModel->delete($id);

        if ($deleted) {
            AuthMiddleware::setFlash('success', "Akun '{$targetUser['name']}' berhasil dihapus dari sistem.");
        } else {
            AuthMiddleware::setFlash('danger', 'Gagal menghapus akun pengguna.');
        }

        $this->redirectTo('/admin/users');
    }
}

