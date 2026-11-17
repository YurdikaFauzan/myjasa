<?php

namespace Controllers;

use Config\Env;
use Middleware\AuthMiddleware;
use Models\User;
use Models\ServiceRequest;
use Models\Category;
use Models\Service;
use Models\Review;

class CustomerController extends BaseController
{
    private User $userModel;
    private ServiceRequest $requestModel;
    private Category $categoryModel;
    private Service $serviceModel;

    public function __construct()
    {
        $this->userModel     = new User();
        $this->requestModel  = new ServiceRequest();
        $this->categoryModel = new Category();
        $this->serviceModel  = new Service();
    }

    /**
     * Customer Dashboard Overview
     */
    public function dashboard(): void
    {
        $user = AuthMiddleware::requireRole(User::ROLE_CUSTOMER);

        $myRequests = (new User())->serviceRequestsAsCustomer((int)$user['id']);

        $activeStatuses = [
            ServiceRequest::STATUS_ASSIGNED,
            ServiceRequest::STATUS_ACCEPTED,
            ServiceRequest::STATUS_ON_THE_WAY,
            ServiceRequest::STATUS_REPAIRING
        ];
        $activeOrders    = count(array_filter($myRequests, fn($r) => in_array($r['status'], $activeStatuses)));
        $pendingOrders   = count(array_filter($myRequests, fn($r) => $r['status'] === ServiceRequest::STATUS_PENDING));
        $completedOrders = count(array_filter($myRequests, fn($r) => $r['status'] === ServiceRequest::STATUS_COMPLETED));

        $this->render('customer/dashboard', [
            'pageTitle'       => 'Customer Dashboard - SmartService AI',
            'user'            => $user,
            'flash'           => AuthMiddleware::getFlash(),
            'activeOrders'    => $activeOrders,
            'pendingOrders'   => $pendingOrders,
            'completedOrders' => $completedOrders,
            'myRequests'      => $myRequests,
            'requestModel'    => $this->requestModel
        ]);
    }

    /**
     * Halaman "Pesanan Saya" (Active Bookings)
     */
    public function listBookings(): void
    {
        $user = AuthMiddleware::requireRole(User::ROLE_CUSTOMER);

        $myRequests = (new User())->serviceRequestsAsCustomer((int)$user['id']);

        // Filter status if requested
        $statusFilter = $_GET['status'] ?? '';
        if (!empty($statusFilter)) {
            $myRequests = array_filter($myRequests, fn($r) => $r['status'] === $statusFilter);
        }

        // Enrich with Category, Service, Technician, Review, and Payment info
        foreach ($myRequests as &$req) {
            $req['service']    = $this->serviceModel->find((int)$req['service_id']);
            $req['category']   = $req['service'] ? $this->categoryModel->find((int)$req['service']['category_id']) : null;
            $req['technician'] = $req['technician_id'] ? (new User())->find((int)$req['technician_id']) : null;
            $req['review']     = $this->requestModel->review((int)$req['id']);
            $req['payment']    = $this->requestModel->payment((int)$req['id']);
        }

        $this->render('customer/requests/index', [
            'pageTitle'    => 'Pesanan Saya - SmartService AI',
            'user'         => $user,
            'flash'        => AuthMiddleware::getFlash(),
            'myRequests'   => array_values($myRequests),
            'statusFilter' => $statusFilter,
            'csrfToken'    => AuthMiddleware::generateCsrfToken()
        ]);
    }



    /**
     * Form Pemesanan Jasa Baru (Create Booking Page)
     */
    public function showBookingForm(): void
    {
        $user = AuthMiddleware::requireRole(User::ROLE_CUSTOMER);

        $categories = array_filter($this->categoryModel->all('name', 'ASC'), fn($c) => $c['status'] === Category::STATUS_ACTIVE);
        $services   = array_filter($this->serviceModel->all('name', 'ASC'), fn($s) => $s['status'] === Service::STATUS_ACTIVE);
        $technicians = array_values(array_filter(
            $this->userModel->where('role', User::ROLE_TECHNICIAN),
            fn($t) => $t['status'] === 'active'
        ));

        $consultationId = (int)($_GET['consultation_id'] ?? 0);
        $aiConsultation = null;
        if ($consultationId > 0) {
            $consultModel = new \Models\AiConsultation();
            $aiConsultation = $consultModel->find($consultationId);
            if ($aiConsultation && $aiConsultation['ai_result']) {
                $aiConsultation['ai_result'] = json_decode($aiConsultation['ai_result'], true);
            }
        }

        $this->render('customer/requests/create', [
            'pageTitle'  => 'Pesan Jasa Perbaikan - SmartService AI',
            'user'       => $user,
            'flash'      => AuthMiddleware::getFlash(),
            'categories' => $categories,
            'services'   => array_values($services),
            'technicians' => $technicians,
            'aiConsultation' => $aiConsultation,
            'csrfToken'  => AuthMiddleware::generateCsrfToken()
        ]);
    }

    /**
     * API JSON: Get active services by Category ID
     */
    public function getServicesByCategory(): void
    {
        AuthMiddleware::requireRole(User::ROLE_CUSTOMER);

        $categoryId = (int)($_GET['category_id'] ?? 0);
        if ($categoryId <= 0) {
            $this->json(['status' => 'error', 'services' => []]);
            return;
        }

        $allServices = $this->serviceModel->where('category_id', $categoryId);
        $activeServices = array_values(array_filter($allServices, fn($s) => $s['status'] === Service::STATUS_ACTIVE));

        $this->json([
            'status'   => 'success',
            'services' => $activeServices
        ]);
    }

    /**
     * Process POST Pemesanan Jasa Baru
     */
    public function createBooking(): void
    {
        $user = AuthMiddleware::requireRole(User::ROLE_CUSTOMER);

        $csrfToken = $_POST['csrf_token'] ?? null;
        if (!AuthMiddleware::verifyCsrfToken($csrfToken)) {
            AuthMiddleware::setFlash('danger', 'Token CSRF tidak valid.');
            $this->redirectTo('/customer/requests/create');
            return;
        }

        $serviceId   = (int)($_POST['service_id'] ?? 0);
        $complaint   = trim($_POST['complaint'] ?? '');
        $address     = trim($_POST['address'] ?? '');
        $bookingDate = trim($_POST['booking_date'] ?? '');
        $bookingTime = trim($_POST['booking_time'] ?? '');
        $aiConsultationId = (int)($_POST['ai_consultation_id'] ?? 0);

        if ($serviceId <= 0) {
            AuthMiddleware::setFlash('danger', 'Pilihlah jenis layanan perbaikan.');
            $this->redirectTo('/customer/requests/create');
            return;
        }

        $service = $this->serviceModel->find($serviceId);
        if (!$service || $service['status'] !== Service::STATUS_ACTIVE) {
            AuthMiddleware::setFlash('danger', 'Layanan yang dipilih tidak tersedia.');
            $this->redirectTo('/customer/requests/create');
            return;
        }

        if (empty($complaint) || strlen($complaint) < 5) {
            AuthMiddleware::setFlash('danger', 'Deskripsi keluhan kerusakan minimal 5 karakter.');
            $this->redirectTo('/customer/requests/create');
            return;
        }

        if (empty($address) || strlen($address) < 5) {
            AuthMiddleware::setFlash('danger', 'Alamat lokasi kunjungan wajib diisi lengkap.');
            $this->redirectTo('/customer/requests/create');
            return;
        }

        if (empty($bookingDate) || empty($bookingTime)) {
            AuthMiddleware::setFlash('danger', 'Tanggal dan waktu jadwal kunjungan wajib ditentukan.');
            $this->redirectTo('/customer/requests/create');
            return;
        }

        $scheduleString = $bookingDate . ' ' . $bookingTime . ':00';

        // Handle complaint photo upload
        $complaintPhotoPath = null;
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $file     = $_FILES['photo'];
            $fileTmp  = $file['tmp_name'];
            $fileName = $file['name'];
            $fileSize = $file['size'];

            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
            $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            if (!in_array($ext, $allowedExtensions)) {
                AuthMiddleware::setFlash('danger', 'Format foto tidak valid. Gunakan format JPG, JPEG, PNG, WEBP, atau GIF.');
                $this->redirectTo('/customer/requests/create');
                return;
            }

            if ($fileSize > 5 * 1024 * 1024) { // 5 MB max
                AuthMiddleware::setFlash('danger', 'Ukuran foto maksimal 5 MB.');
                $this->redirectTo('/customer/requests/create');
                return;
            }

            $uploadDir = __DIR__ . '/../Assets/uploads/complaints';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $newFileName = 'complaint_' . (int)$user['id'] . '_' . time() . '.' . $ext;
            $targetFile  = $uploadDir . '/' . $newFileName;

            if (move_uploaded_file($fileTmp, $targetFile)) {
                $complaintPhotoPath = 'Assets/uploads/complaints/' . $newFileName;
            }
        }

        // Handle Technician Assignment (Spesifik atau Otomatis)
        $selectedTechId = trim($_POST['technician_id'] ?? 'auto');
        $assignedTechId = null;
        $initialStatus  = ServiceRequest::STATUS_PENDING;
        $assignLogNote  = 'Pemesanan perbaikan dibuat oleh pelanggan.';

        if (!empty($selectedTechId) && $selectedTechId !== 'auto') {
            $techUser = $this->userModel->find((int)$selectedTechId);
            if ($techUser && $techUser['role'] === User::ROLE_TECHNICIAN && $techUser['status'] === 'active') {
                $assignedTechId = (int)$techUser['id'];
                $initialStatus  = ServiceRequest::STATUS_ASSIGNED;
                $assignLogNote  = "Pesanan baru dibuat customer dan ditugaskan ke Mitra Teknisi {$techUser['name']}.";
            }
        }

        // Jika mode otomatis ("auto") atau belum ditentukan, tugaskan otomatis ke salah satu teknisi aktif yang siap bertugas
        if (!$assignedTechId) {
            $activeTechs = array_values(array_filter(
                $this->userModel->where('role', User::ROLE_TECHNICIAN),
                fn($t) => $t['status'] === 'active'
            ));

            if (!empty($activeTechs)) {
                // Prioritaskan teknisi dengan beban tugas aktif paling sedikit
                $techWorkloads = [];
                foreach ($activeTechs as $at) {
                    $tid = (int)$at['id'];
                    $activeJobsCount = count($this->requestModel->where('technician_id', $tid));
                    $techWorkloads[$tid] = $activeJobsCount;
                }
                asort($techWorkloads);
                $bestTechId = array_key_first($techWorkloads);
                $bestTech   = $this->userModel->find($bestTechId);

                if ($bestTech) {
                    $assignedTechId = (int)$bestTechId;
                    $initialStatus  = ServiceRequest::STATUS_ASSIGNED;
                    $assignLogNote  = "Pesanan baru berhasil dibuat dan sistem otomatis menugaskan ke Mitra Teknisi {$bestTech['name']}.";
                }
            }
        }

        $newRequestId = $this->requestModel->create([
            'customer_id'     => (int)$user['id'],
            'technician_id'   => $assignedTechId,
            'ai_consultation_id' => $aiConsultationId > 0 ? $aiConsultationId : null,
            'service_id'      => $serviceId,
            'complaint'       => $complaint,
            'complaint_photo' => $complaintPhotoPath,
            'address'         => $address,
            'schedule'        => $scheduleString,
            'status'          => $initialStatus,
            'estimated_price' => (float)$service['price'],
            'final_price'     => null,
            'technician_note' => null
        ]);

        if ($newRequestId) {
            // Log initial status change
            $this->requestModel->logStatusChange($newRequestId, $initialStatus, $assignLogNote);

            $statusMessage = $assignedTechId 
                ? "Pemesanan servis #REQ-{$newRequestId} berhasil dibuat dan langsung masuk ke Job Baru Mitra Teknisi!"
                : "Pemesanan servis #REQ-{$newRequestId} berhasil dibuat! Menunggu konfirmasi teknisi.";

            AuthMiddleware::setFlash('success', $statusMessage);
            $this->redirectTo('/customer/requests');
            return;
        } else {
            AuthMiddleware::setFlash('danger', 'Gagal membuat pesanan jasa perbaikan.');
            $this->redirectTo('/customer/requests/create');
        }
    }

    /**
     * Process Pembatalan Pesanan oleh Pelanggan (Cancel Booking: pending -> cancelled)
     */
    public function cancelBooking(): void
    {
        $user = AuthMiddleware::requireRole(User::ROLE_CUSTOMER);

        if (!AuthMiddleware::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            AuthMiddleware::setFlash('danger', 'Token CSRF tidak valid.');
            $this->redirectTo('/customer/requests');
            return;
        }

        $requestId = (int)($_POST['request_id'] ?? 0);
        $request   = $this->requestModel->find($requestId);

        if (!$request || (int)$request['customer_id'] !== (int)$user['id']) {
            AuthMiddleware::setFlash('danger', 'Akses Ditolak: Pesanan tidak ditemukan atau bukan milik Anda.');
            $this->redirectTo('/customer/requests');
            return;
        }

        if (!in_array($request['status'], [ServiceRequest::STATUS_PENDING, ServiceRequest::STATUS_ASSIGNED])) {
            AuthMiddleware::setFlash('danger', "Pesanan tidak dapat dibatalkan karena statusnya sudah [" . strtoupper($request['status']) . "].");
            $this->redirectTo("/customer/requests/detail?id={$requestId}");
            return;
        }

        $updated = $this->requestModel->updateStatusWithLog($requestId, ServiceRequest::STATUS_CANCELLED, 'Pesanan dibatalkan oleh pelanggan.');

        if ($updated) {
            AuthMiddleware::setFlash('success', "Pesanan #REQ-{$requestId} berhasil dibatalkan.");
        } else {
            AuthMiddleware::setFlash('danger', 'Gagal membatalkan pesanan.');
        }

        $this->redirectTo("/customer/requests/detail?id={$requestId}");
    }

    /**
     * Submit Review / Rating Customer (Scale 1-5 & Comment, One Review per Completed Request)
     */
    public function submitReview(): void
    {
        $user = AuthMiddleware::requireRole(User::ROLE_CUSTOMER);

        if (!AuthMiddleware::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            AuthMiddleware::setFlash('danger', 'Token CSRF tidak valid.');
            $this->redirectTo('/customer/requests');
            return;
        }

        $requestId = (int)($_POST['service_request_id'] ?? 0);
        $request   = $this->requestModel->find($requestId);

        if (!$request || (int)$request['customer_id'] !== (int)$user['id']) {
            AuthMiddleware::setFlash('danger', 'Akses Ditolak: Pesanan tidak ditemukan atau bukan milik Anda.');
            $this->redirectTo('/customer/requests');
            return;
        }

        if ($request['status'] !== ServiceRequest::STATUS_COMPLETED) {
            AuthMiddleware::setFlash('danger', 'Ulasan hanya dapat berikan untuk pesanan perbaikan yang telah selesai dikerjakan (COMPLETED).');
            $this->redirectTo("/customer/requests/detail?id={$requestId}");
            return;
        }

        // Check Unique Review Constraint (One review per request)
        $existingReview = $this->requestModel->review($requestId);
        if ($existingReview) {
            AuthMiddleware::setFlash('danger', 'Anda sudah memberikan ulasan untuk pesanan perbaikan ini sebelumnya.');
            $this->redirectTo("/customer/requests/detail?id={$requestId}");
            return;
        }

        $rating  = (int)($_POST['rating'] ?? 0);
        $comment = trim($_POST['comment'] ?? '');

        if ($rating < 1 || $rating > 5) {
            AuthMiddleware::setFlash('danger', 'Pilihlah skala rating antara 1 sampai 5 bintang.');
            $this->redirectTo("/customer/requests/detail?id={$requestId}");
            return;
        }

        $technicianId = (int)($request['technician_id'] ?? 0);
        if ($technicianId <= 0) {
            AuthMiddleware::setFlash('danger', 'Teknisi untuk pesanan ini tidak ditemukan.');
            $this->redirectTo("/customer/requests/detail?id={$requestId}");
            return;
        }

        $reviewModel = new Review();
        $created = $reviewModel->create([
            'service_request_id' => $requestId,
            'customer_id'        => (int)$user['id'],
            'technician_id'      => $technicianId,
            'rating'             => $rating,
            'comment'            => $comment
        ]);

        if ($created) {
            AuthMiddleware::setFlash('success', "Terima kasih! Ulasan & rating {$rating} bintang Anda berhasil dikirim.");
        } else {
            AuthMiddleware::setFlash('danger', 'Gagal menyimpan ulasan.');
        }

        $this->redirectTo("/customer/requests/detail?id={$requestId}");
    }

    /**
     * Halaman Riwayat Seluruh Pesanan Customer
     */
    public function history(): void
    {
        $user = AuthMiddleware::requireRole(User::ROLE_CUSTOMER);

        $allOrders    = $this->requestModel->where('customer_id', (int)$user['id']);
        usort($allOrders, fn($a, $b) => $b['id'] <=> $a['id']);

        $statusFilter = $_GET['status'] ?? '';

        $filtered = empty($statusFilter)
            ? $allOrders
            : array_values(array_filter($allOrders, fn($o) => $o['status'] === $statusFilter));

        foreach ($filtered as &$order) {
            $svc = $this->serviceModel->find((int)$order['service_id']);
            $order['service']    = $svc;
            $order['category']   = $svc ? $this->categoryModel->find((int)$svc['category_id']) : null;
            $order['technician'] = $order['technician_id'] ? (new User())->find((int)$order['technician_id']) : null;
            $order['review']     = $this->requestModel->review((int)$order['id']);
        }

        $this->render('customer/history', [
            'pageTitle'    => 'Riwayat Pesanan Saya - SmartService AI',
            'user'         => $user,
            'flash'        => AuthMiddleware::getFlash(),
            'orders'       => array_values($filtered),
            'statusFilter' => $statusFilter,
        ]);
    }

    /**
     * Halaman "Detail Pesanan & Timeline Tracking"
     */
    public function showBookingDetail(): void
    {
        $user = AuthMiddleware::requireRole(User::ROLE_CUSTOMER);

        $id = (int)($_GET['id'] ?? 0);
        $request = $this->requestModel->find($id);

        if (!$request) {
            AuthMiddleware::setFlash('danger', 'Pesanan jasa tidak ditemukan.');
            $this->redirectTo('/customer/requests');
            return;
        }

        // STRICT AUTHORIZATION CHECK: Customer can ONLY view their own booking!
        if ((int)$request['customer_id'] !== (int)$user['id']) {
            AuthMiddleware::setFlash('danger', 'Akses Ditolak: Anda tidak berhak melihat rincian pesanan pelanggan lain.');
            $this->redirectTo('/customer/requests');
            return;
        }

        $service    = $this->serviceModel->find((int)$request['service_id']);
        $category   = $service ? $this->categoryModel->find((int)$service['category_id']) : null;
        $technician = $request['technician_id'] ? (new User())->find((int)$request['technician_id']) : null;
        
        $aiConsultation = null;
        if (!empty($request['ai_consultation_id'])) {
            $aiConsultation = (new \Models\AiConsultation())->find((int)$request['ai_consultation_id']);
            if ($aiConsultation && !empty($aiConsultation['ai_result'])) {
                $aiConsultation['ai_result'] = json_decode($aiConsultation['ai_result'], true);
            }
        }

        $payment    = $this->requestModel->payment((int)$request['id']);
        $review     = $this->requestModel->review((int)$request['id']);
        $statusLogs = $this->requestModel->getStatusLogs((int)$request['id']);

        $this->render('customer/requests/show', [
            'pageTitle'  => "Detail Pesanan #REQ-{$id} - SmartService AI",
            'user'       => $user,
            'flash'      => AuthMiddleware::getFlash(),
            'request'    => $request,
            'service'    => $service,
            'category'   => $category,
            'technician' => $technician,
            'aiConsultation' => $aiConsultation,
            'payment'    => $payment,
            'review'     => $review,
            'statusLogs' => $statusLogs,
            'csrfToken'  => AuthMiddleware::generateCsrfToken()
        ]);
    }

    /**
     * Halaman Formulir Pembayaran Pesanan Pelanggan (Payment Page)
     */
    public function showPaymentForm(): void
    {
        $user = AuthMiddleware::requireRole(User::ROLE_CUSTOMER);

        $id = (int)($_GET['id'] ?? 0);
        $request = $this->requestModel->find($id);

        if (!$request) {
            AuthMiddleware::setFlash('danger', 'Pesanan jasa tidak ditemukan.');
            $this->redirectTo('/customer/requests');
            return;
        }

        // Strict Authorization: Hanya pelanggan pemilik pesanan yang boleh membayar
        if ((int)$request['customer_id'] !== (int)$user['id']) {
            AuthMiddleware::setFlash('danger', 'Akses Ditolak: Anda tidak dapat membayar pesanan milik pengguna lain.');
            $this->redirectTo('/customer/requests');
            return;
        }

        if ($request['status'] === ServiceRequest::STATUS_CANCELLED) {
            AuthMiddleware::setFlash('danger', 'Pesanan ini telah dibatalkan dan tidak dapat diproses pembayarannya.');
            $this->redirectTo("/customer/requests/detail?id={$id}");
            return;
        }

        $payment = $this->requestModel->payment($id);
        if ($payment && $payment['payment_status'] === \Models\Payment::STATUS_PAID) {
            AuthMiddleware::setFlash('info', 'Pesanan ini sudah dibayar lunas sebelumnya.');
            $this->redirectTo("/customer/requests/detail?id={$id}");
            return;
        }

        $service    = $this->serviceModel->find((int)$request['service_id']);
        $category   = $service ? $this->categoryModel->find((int)$service['category_id']) : null;
        $technician = $request['technician_id'] ? (new User())->find((int)$request['technician_id']) : null;

        $this->render('customer/payments/pay', [
            'pageTitle'  => "Pembayaran Pesanan #REQ-{$id} - SmartService AI",
            'user'       => $user,
            'flash'      => AuthMiddleware::getFlash(),
            'request'    => $request,
            'service'    => $service,
            'category'   => $category,
            'technician' => $technician,
            'payment'    => $payment,
            'csrfToken'  => AuthMiddleware::generateCsrfToken()
        ]);
    }

    /**
     * Proses Konfirmasi Pembayaran Pesanan oleh Pelanggan
     */
    public function processPayment(): void
    {
        $user = AuthMiddleware::requireRole(User::ROLE_CUSTOMER);

        if (!AuthMiddleware::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            AuthMiddleware::setFlash('danger', 'Token CSRF tidak valid. Silakan coba kembali.');
            $this->redirectTo('/customer/requests');
            return;
        }

        $requestId = (int)($_POST['service_request_id'] ?? 0);
        $request   = $this->requestModel->find($requestId);

        if (!$request || (int)$request['customer_id'] !== (int)$user['id']) {
            AuthMiddleware::setFlash('danger', 'Akses Ditolak: Pesanan tidak ditemukan.');
            $this->redirectTo('/customer/requests');
            return;
        }

        if ($request['status'] === ServiceRequest::STATUS_CANCELLED) {
            AuthMiddleware::setFlash('danger', 'Pesanan ini sudah dibatalkan.');
            $this->redirectTo("/customer/requests/detail?id={$requestId}");
            return;
        }

        $paymentMethod = trim($_POST['payment_method'] ?? 'qris');
        $validMethods = ['qris', 'transfer_bank', 'e-wallet', 'cash'];
        if (!in_array($paymentMethod, $validMethods)) {
            $paymentMethod = 'qris';
        }

        $amount = (float)($request['final_price'] ?? $request['estimated_price']);
        if ($amount <= 0) {
            $service = $this->serviceModel->find((int)$request['service_id']);
            $amount = (float)($service['price'] ?? 0);
        }

        $paymentModel = new \Models\Payment();
        $existingPayment = $paymentModel->findBy('service_request_id', $requestId);

        $now = date('Y-m-d H:i:s');
        $methodLabel = match($paymentMethod) {
            'qris'          => 'QRIS Instant',
            'transfer_bank' => 'Transfer Bank (BCA VA)',
            'e-wallet'      => 'E-Wallet Transfer',
            'cash'          => 'Tunai (Cash) ke Teknisi',
            default         => strtoupper($paymentMethod)
        };

        if ($existingPayment) {
            $saved = $paymentModel->update((int)$existingPayment['id'], [
                'amount'         => $amount,
                'payment_status' => \Models\Payment::STATUS_PAID,
                'payment_method' => $paymentMethod,
                'paid_at'        => $now
            ]);
        } else {
            $saved = $paymentModel->create([
                'service_request_id' => $requestId,
                'amount'             => $amount,
                'payment_status'     => \Models\Payment::STATUS_PAID,
                'payment_method'     => $paymentMethod,
                'paid_at'            => $now
            ]);
        }

        if ($saved) {
            // Catat log status bahwa pembayaran telah lunas
            $this->requestModel->logStatusChange(
                $requestId,
                $request['status'],
                "Pembayaran lunas sebesar Rp " . number_format($amount, 0, ',', '.') . " melalui {$methodLabel}."
            );

            AuthMiddleware::setFlash('success', "Pembayaran sebesar Rp " . number_format($amount, 0, ',', '.') . " menggunakan metode {$methodLabel} berhasil dikonfirmasi!");
        } else {
            AuthMiddleware::setFlash('danger', 'Gagal memproses pembayaran ke sistem.');
        }

        $this->redirectTo("/customer/requests/detail?id={$requestId}");
    }
}

