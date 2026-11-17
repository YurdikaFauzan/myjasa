<?php

namespace Controllers;

use Config\Env;
use Middleware\AuthMiddleware;
use Models\User;
use Models\ServiceRequest;
use Models\Category;
use Models\Service;
use Models\Payment;

class TechnicianController extends BaseController
{
    private User $userModel;
    private ServiceRequest $requestModel;
    private Category $categoryModel;
    private Service $serviceModel;
    private Payment $paymentModel;

    public function __construct()
    {
        $this->userModel     = new User();
        $this->requestModel  = new ServiceRequest();
        $this->categoryModel = new Category();
        $this->serviceModel  = new Service();
        $this->paymentModel  = new Payment();
    }

    /**
     * Technician Dashboard Overview
     */
    public function dashboard(): void
    {
        $user = AuthMiddleware::requireRole(User::ROLE_TECHNICIAN);
        $techId = (int)$user['id'];

        // Get jobs assigned to this technician
        $assignedJobs = $this->requestModel->where('technician_id', $techId);

        // Also get unassigned/pending jobs that are waiting for any technician
        $unassignedJobs = array_values(array_filter(
            $this->requestModel->where('status', ServiceRequest::STATUS_PENDING),
            fn($j) => empty($j['technician_id'])
        ));

        // Metric Statistics
        $newJobs = count(array_filter($assignedJobs, fn($j) => $j['status'] === ServiceRequest::STATUS_ASSIGNED))
                 + count($unassignedJobs);

        $acceptedJobs = count(array_filter($assignedJobs, fn($j) => $j['status'] === ServiceRequest::STATUS_ACCEPTED));

        $activeStatuses = [
            ServiceRequest::STATUS_ACCEPTED,
            ServiceRequest::STATUS_ON_THE_WAY,
            ServiceRequest::STATUS_REPAIRING
        ];
        $activeJobs = count(array_filter($assignedJobs, fn($j) => in_array($j['status'], $activeStatuses)));

        $completedJobs = count(array_filter($assignedJobs, fn($j) => $j['status'] === ServiceRequest::STATUS_COMPLETED));

        // Combine for table display on dashboard: assigned jobs + unassigned jobs
        $allDisplayJobs = array_merge($assignedJobs, $unassignedJobs);
        usort($allDisplayJobs, fn($a, $b) => $b['id'] <=> $a['id']);

        // Enrich recent jobs for dashboard view
        foreach ($allDisplayJobs as &$job) {
            $job['customer'] = $this->userModel->find((int)$job['customer_id']);
            $job['service']  = $this->serviceModel->find((int)$job['service_id']);
            $job['category'] = $job['service'] ? $this->categoryModel->find((int)$job['service']['category_id']) : null;
        }

        $data = [
            'pageTitle'     => 'Technician Dashboard - SmartService AI',
            'user'          => $user,
            'flash'         => AuthMiddleware::getFlash(),
            'newJobs'       => $newJobs,
            'acceptedJobs'  => $acceptedJobs,
            'activeJobs'    => $activeJobs,
            'completedJobs' => $completedJobs,
            'assignedJobs'  => $allDisplayJobs,
            'requestModel'  => $this->requestModel,
            'csrfToken'     => AuthMiddleware::generateCsrfToken()
        ];

        $this->render('technician/dashboard', $data);
    }

    /**
     * Daftar Job Pekerjaan Teknisi dengan Filter Status
     */
    public function listJobs(): void
    {
        $user = AuthMiddleware::requireRole(User::ROLE_TECHNICIAN);
        $techId = (int)$user['id'];

        $allAssigned = $this->requestModel->where('technician_id', $techId);
        $unassignedPending = array_values(array_filter(
            $this->requestModel->where('status', ServiceRequest::STATUS_PENDING),
            fn($j) => empty($j['technician_id'])
        ));

        $allJobs = array_merge($allAssigned, $unassignedPending);
        usort($allJobs, fn($a, $b) => $b['id'] <=> $a['id']);

        $statusFilter = $_GET['status'] ?? '';

        $filteredJobs = array_filter($allJobs, function($job) use ($statusFilter) {
            if (empty($statusFilter)) {
                return true;
            }

            if ($statusFilter === 'assigned') {
                return $job['status'] === ServiceRequest::STATUS_ASSIGNED || 
                       ($job['status'] === ServiceRequest::STATUS_PENDING && empty($job['technician_id']));
            }

            if ($statusFilter === 'active') {
                return in_array($job['status'], [
                    ServiceRequest::STATUS_ACCEPTED,
                    ServiceRequest::STATUS_ON_THE_WAY,
                    ServiceRequest::STATUS_REPAIRING
                ]);
            }

            if ($statusFilter === 'completed') {
                return $job['status'] === ServiceRequest::STATUS_COMPLETED;
            }

            return $job['status'] === $statusFilter;
        });

        foreach ($filteredJobs as &$job) {
            $job['customer'] = $this->userModel->find((int)$job['customer_id']);
            $job['service']  = $this->serviceModel->find((int)$job['service_id']);
            $job['category'] = $job['service'] ? $this->categoryModel->find((int)$job['service']['category_id']) : null;
        }

        $this->render('technician/jobs/index', [
            'pageTitle'    => 'Daftar Tugas Perbaikan - Teknisi SmartService AI',
            'user'         => $user,
            'flash'        => AuthMiddleware::getFlash(),
            'jobs'         => array_values($filteredJobs),
            'statusFilter' => $statusFilter,
            'csrfToken'    => AuthMiddleware::generateCsrfToken()
        ]);
    }

    /**
     * Detail Pekerjaan untuk Teknisi (Hanya pekerjaan milik teknisi bersangkutan)
     */
    public function showJobDetail(): void
    {
        $user = AuthMiddleware::requireRole(User::ROLE_TECHNICIAN);

        $id  = (int)($_GET['id'] ?? 0);
        $job = $this->verifyJobAccess($id, (int)$user['id']);
        if (!$job) return;

        $customer   = $this->userModel->find((int)$job['customer_id']);
        $service    = $this->serviceModel->find((int)$job['service_id']);
        $category   = $service ? $this->categoryModel->find((int)$service['category_id']) : null;
        $payment    = $this->requestModel->payment((int)$job['id']);
        $statusLogs = $this->requestModel->getStatusLogs((int)$job['id']);

        $aiConsultation = null;
        if (!empty($job['ai_consultation_id'])) {
            $aiConsultation = (new \Models\AiConsultation())->find((int)$job['ai_consultation_id']);
            if ($aiConsultation && !empty($aiConsultation['ai_result'])) {
                $aiConsultation['ai_result'] = json_decode($aiConsultation['ai_result'], true);
            }
        }

        $this->render('technician/jobs/show', [
            'pageTitle'  => "Detail Tugas #JOB-{$job['id']} - SmartService AI",
            'user'       => $user,
            'flash'      => AuthMiddleware::getFlash(),
            'job'        => $job,
            'customer'   => $customer,
            'service'    => $service,
            'category'   => $category,
            'aiConsultation' => $aiConsultation,
            'payment'    => $payment,
            'statusLogs' => $statusLogs,
            'csrfToken'  => AuthMiddleware::generateCsrfToken()
        ]);
    }

    /**
     * Terima Pekerjaan (Accept Job: assigned -> accepted)
     */
    public function acceptJob(): void
    {
        $user = AuthMiddleware::requireRole(User::ROLE_TECHNICIAN);

        if (!AuthMiddleware::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            AuthMiddleware::setFlash('danger', 'Token keamanan CSRF tidak valid.');
            $this->redirectTo('/technician/dashboard');
            return;
        }

        $jobId = (int)($_POST['job_id'] ?? 0);
        $job   = $this->verifyJobAccess($jobId, (int)$user['id']);
        if (!$job) return;

        $isAssigned = ($job['status'] === ServiceRequest::STATUS_ASSIGNED);
        $isOpenJob  = ($job['status'] === ServiceRequest::STATUS_PENDING && empty($job['technician_id']));

        if (!$isAssigned && !$isOpenJob) {
            AuthMiddleware::setFlash('danger', "Pekerjaan #JOB-{$jobId} tidak dapat diterima (Status saat ini: {$job['status']}).");
            $this->redirectTo("/technician/jobs/detail?id={$jobId}");
            return;
        }

        $updated = $this->requestModel->update($jobId, [
            'technician_id' => (int)$user['id'],
            'status'        => ServiceRequest::STATUS_ACCEPTED
        ]);

        if ($updated) {
            $logMsg = $isOpenJob 
                ? "Job diambil & diterima oleh Mitra Teknisi {$user['name']}."
                : "Diterima oleh Mitra Teknisi {$user['name']}.";
            $this->requestModel->logStatusChange($jobId, ServiceRequest::STATUS_ACCEPTED, $logMsg);

            AuthMiddleware::setFlash('success', "Pekerjaan #JOB-{$jobId} berhasil DITERIMA (ACCEPTED). Silakan perbarui status menjadi 'Dalam Perjalanan' saat siap berangkat.");
        } else {
            AuthMiddleware::setFlash('danger', "Gagal memperbarui status pekerjaan.");
        }

        $this->redirectTo("/technician/jobs/detail?id={$jobId}");
    }

    /**
     * Tolak Pekerjaan (Reject Job: assigned -> pending, reset technician_id, simpan alasan penolakan)
     */
    public function rejectJob(): void
    {
        $user = AuthMiddleware::requireRole(User::ROLE_TECHNICIAN);

        if (!AuthMiddleware::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            AuthMiddleware::setFlash('danger', 'Token keamanan CSRF tidak valid.');
            $this->redirectTo('/technician/dashboard');
            return;
        }

        $jobId = (int)($_POST['job_id'] ?? 0);
        $job   = $this->verifyJobAccess($jobId, (int)$user['id']);
        if (!$job) return;

        if ($job['status'] !== ServiceRequest::STATUS_ASSIGNED) {
            AuthMiddleware::setFlash('danger', "Pekerjaan #JOB-{$jobId} tidak berstatus [ASSIGNED] sehingga tidak dapat ditolak.");
            $this->redirectTo("/technician/jobs/detail?id={$jobId}");
            return;
        }

        $rejectionNote = trim($_POST['rejection_note'] ?? '');
        if (strlen($rejectionNote) < 5) {
            AuthMiddleware::setFlash('danger', 'Catatan alasan penolakan wajib diisi minimal 5 karakter.');
            $this->redirectTo("/technician/jobs/detail?id={$jobId}");
            return;
        }

        $logNote = "[Ditolak oleh Teknisi {$user['name']} pada " . date('Y-m-d H:i') . "]: {$rejectionNote}";

        // Return job to pending status, reset technician_id to NULL, log rejection
        $updated = $this->requestModel->update($jobId, [
            'status'          => ServiceRequest::STATUS_PENDING,
            'technician_id'   => null,
            'rejection_note'  => $rejectionNote,
            'technician_note' => $logNote
        ]);

        if ($updated) {
            $this->requestModel->logStatusChange($jobId, ServiceRequest::STATUS_PENDING, "Ditolak oleh Teknisi {$user['name']}: {$rejectionNote}");
            AuthMiddleware::setFlash('warning', "Pekerjaan #JOB-{$jobId} telah DITOLAK dan dikembalikan ke antrean Admin (status: PENDING).");
            $this->redirectTo('/technician/jobs?status=assigned');
        } else {
            AuthMiddleware::setFlash('danger', 'Gagal memproses penolakan pekerjaan.');
            $this->redirectTo("/technician/jobs/detail?id={$jobId}");
        }
    }

    /**
     * Update Status Pekerjaan (accepted -> on_the_way -> repairing)
     */
    public function updateStatus(): void
    {
        $user = AuthMiddleware::requireRole(User::ROLE_TECHNICIAN);

        if (!AuthMiddleware::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            AuthMiddleware::setFlash('danger', 'Token keamanan CSRF tidak valid.');
            $this->redirectTo('/technician/dashboard');
            return;
        }

        $jobId        = (int)($_POST['job_id'] ?? 0);
        $targetStatus = $_POST['status'] ?? '';

        $job = $this->verifyJobAccess($jobId, (int)$user['id']);
        if (!$job) return;

        // Valid transition checks
        $allowedTransitions = [
            ServiceRequest::STATUS_ACCEPTED   => [ServiceRequest::STATUS_ON_THE_WAY],
            ServiceRequest::STATUS_ON_THE_WAY => [ServiceRequest::STATUS_REPAIRING],
        ];

        $currentStatus = $job['status'];

        if ($targetStatus === ServiceRequest::STATUS_COMPLETED) {
            // Must use completion form endpoint
            $this->redirectTo("/technician/jobs/complete?id={$jobId}");
            return;
        }

        if (!isset($allowedTransitions[$currentStatus]) || !in_array($targetStatus, $allowedTransitions[$currentStatus])) {
            AuthMiddleware::setFlash('danger', "Transisi status dari '" . strtoupper($currentStatus) . "' ke '" . strtoupper($targetStatus) . "' tidak diizinkan.");
            $this->redirectTo("/technician/jobs/detail?id={$jobId}");
            return;
        }

        $statusLabels = [
            ServiceRequest::STATUS_ON_THE_WAY => 'Dalam Perjalanan ke Lokasi Pelanggan',
            ServiceRequest::STATUS_REPAIRING  => 'Sedang Melakukan Perbaikan'
        ];
        $label = $statusLabels[$targetStatus] ?? strtoupper($targetStatus);

        $updated = $this->requestModel->updateStatusWithLog(
            $jobId,
            $targetStatus,
            "{$label} (Teknisi: {$user['name']})"
        );

        if ($updated) {
            AuthMiddleware::setFlash('success', "Status pekerjaan #JOB-{$jobId} berhasil diperbarui: {$label}.");
        } else {
            AuthMiddleware::setFlash('danger', 'Gagal memperbarui status pengerjaan.');
        }

        $this->redirectTo("/technician/jobs/detail?id={$jobId}");
    }

    /**
     * Halaman Form Penyelesaian Pekerjaan (Status: repairing -> completed)
     */
    public function showCompletionForm(): void
    {
        $user = AuthMiddleware::requireRole(User::ROLE_TECHNICIAN);

        $id  = (int)($_GET['id'] ?? 0);
        $job = $this->verifyJobAccess($id, (int)$user['id']);
        if (!$job) return;

        if ($job['status'] !== ServiceRequest::STATUS_REPAIRING) {
            AuthMiddleware::setFlash('warning', "Penyelesaian pekerjaan hanya dapat dilakukan saat status pekerjaan [REPAIRING] (Sedang Diperbaiki). Status saat ini: " . strtoupper($job['status']));
            $this->redirectTo("/technician/jobs/detail?id={$id}");
            return;
        }

        $customer = $this->userModel->find((int)$job['customer_id']);
        $service  = $this->serviceModel->find((int)$job['service_id']);
        $category = $service ? $this->categoryModel->find((int)$service['category_id']) : null;

        $this->render('technician/jobs/complete', [
            'pageTitle' => "Form Penyelesaian #JOB-{$job['id']} - SmartService AI",
            'user'      => $user,
            'flash'     => AuthMiddleware::getFlash(),
            'job'       => $job,
            'customer'  => $customer,
            'service'   => $service,
            'category'  => $category,
            'csrfToken' => AuthMiddleware::generateCsrfToken()
        ]);
    }

    /**
     * Submit Form Penyelesaian Pekerjaan (Save inspection, actions, fees, photo, status=completed)
     */
    public function completeJob(): void
    {
        $user = AuthMiddleware::requireRole(User::ROLE_TECHNICIAN);

        if (!AuthMiddleware::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            AuthMiddleware::setFlash('danger', 'Token keamanan CSRF tidak valid.');
            $this->redirectTo('/technician/dashboard');
            return;
        }

        $jobId = (int)($_POST['job_id'] ?? 0);
        $job   = $this->verifyJobAccess($jobId, (int)$user['id']);
        if (!$job) return;

        if ($job['status'] !== ServiceRequest::STATUS_REPAIRING) {
            AuthMiddleware::setFlash('danger', "Penyelesaian pekerjaan gagal: Status pekerjaan saat ini adalah " . strtoupper($job['status']));
            $this->redirectTo("/technician/jobs/detail?id={$jobId}");
            return;
        }

        // Retrieve and validate input fields
        $inspectionResult = trim($_POST['inspection_result'] ?? '');
        $repairAction     = trim($_POST['repair_action'] ?? '');
        $serviceFee       = (float)($_POST['service_fee'] ?? 0);
        $sparepartFee     = (float)($_POST['sparepart_fee'] ?? 0);
        $technicianNote   = trim($_POST['technician_note'] ?? '');

        $errors = [];
        if (strlen($inspectionResult) < 5) {
            $errors[] = "Hasil pemeriksaan wajib diisi minimal 5 karakter.";
        }
        if (strlen($repairAction) < 5) {
            $errors[] = "Tindakan perbaikan wajib diisi minimal 5 karakter.";
        }
        if ($serviceFee < 0) {
            $errors[] = "Biaya jasa tidak boleh bernilai negatif.";
        }
        if ($sparepartFee < 0) {
            $errors[] = "Biaya sparepart tidak boleh bernilai negatif.";
        }

        // Total calculated cost
        $finalPrice = $serviceFee + $sparepartFee;

        // Handle Completion Photo Upload if provided
        $photoPath = $job['completion_photo'] ?? null;

        if (isset($_FILES['completion_photo']) && $_FILES['completion_photo']['error'] === UPLOAD_ERR_OK) {
            $file     = $_FILES['completion_photo'];
            $fileTmp  = $file['tmp_name'];
            $fileName = $file['name'];
            $fileSize = $file['size'];

            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
            $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            if (!in_array($ext, $allowedExtensions)) {
                $errors[] = "Format foto tidak valid. Gunakan format JPG, JPEG, PNG, atau WEBP.";
            }

            if ($fileSize > 5 * 1024 * 1024) { // 5 MB max
                $errors[] = "Ukuran foto maksimal 5 MB.";
            }

            if (empty($errors)) {
                $uploadDir = __DIR__ . '/../Assets/uploads/completions';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $newFileName = 'job_' . $jobId . '_' . time() . '.' . $ext;
                $targetFile  = $uploadDir . '/' . $newFileName;

                if (move_uploaded_file($fileTmp, $targetFile)) {
                    $photoPath = 'Assets/uploads/completions/' . $newFileName;
                } else {
                    $errors[] = "Gagal mengunggah foto hasil pekerjaan.";
                }
            }
        }

        if (!empty($errors)) {
            AuthMiddleware::setFlash('danger', implode('<br>', $errors));
            $this->redirectTo("/technician/jobs/complete?id={$jobId}");
            return;
        }

        // Save completion data into service_requests table
        $updateData = [
            'inspection_result' => $inspectionResult,
            'repair_action'     => $repairAction,
            'service_fee'       => $serviceFee,
            'sparepart_fee'     => $sparepartFee,
            'final_price'       => $finalPrice,
            'technician_note'   => $technicianNote,
            'completion_photo'  => $photoPath,
            'status'            => ServiceRequest::STATUS_COMPLETED
        ];

        $updated = $this->requestModel->update($jobId, $updateData);

        if ($updated) {
            // Log completed status
            $this->requestModel->logStatusChange($jobId, ServiceRequest::STATUS_COMPLETED, "Pekerjaan diselesaikan oleh Teknisi {$user['name']}. Total biaya: Rp " . number_format($finalPrice, 0, ',', '.'));

            // Update or create payment record
            $payment = $this->paymentModel->findBy('service_request_id', $jobId);
            if ($payment) {
                $this->paymentModel->update((int)$payment['id'], ['amount' => $finalPrice]);
            } else {
                $this->paymentModel->create([
                    'service_request_id' => $jobId,
                    'amount'             => $finalPrice,
                    'payment_status'     => Payment::STATUS_PENDING,
                    'payment_method'     => 'cash'
                ]);
            }

            AuthMiddleware::setFlash('success', "Selamat! Pekerjaan perbaikan #JOB-{$jobId} telah berhasil diselesaikan dengan total biaya Rp " . number_format($finalPrice, 0, ',', '.'));
            $this->redirectTo("/technician/jobs/detail?id={$jobId}");
        } else {
            AuthMiddleware::setFlash('danger', 'Gagal menyimpan data penyelesaian pekerjaan.');
            $this->redirectTo("/technician/jobs/complete?id={$jobId}");
        }
    }

    /**
     * Riwayat seluruh pekerjaan milik teknisi ini
     */
    public function history(): void
    {
        $user = AuthMiddleware::requireRole(User::ROLE_TECHNICIAN);

        $allJobs = $this->requestModel->where('technician_id', (int)$user['id']);
        usort($allJobs, fn($a, $b) => $b['id'] <=> $a['id']);

        $statusFilter = $_GET['status'] ?? '';

        $filtered = array_filter($allJobs, function($job) use ($statusFilter) {
            if (empty($statusFilter)) return true;
            if ($statusFilter === 'active') {
                return in_array($job['status'], [
                    ServiceRequest::STATUS_ACCEPTED,
                    ServiceRequest::STATUS_ON_THE_WAY,
                    ServiceRequest::STATUS_REPAIRING
                ]);
            }
            return $job['status'] === $statusFilter;
        });

        foreach ($filtered as &$job) {
            $job['customer'] = $this->userModel->find((int)$job['customer_id']);
            $job['service']  = $this->serviceModel->find((int)$job['service_id']);
            $job['category'] = $job['service'] ? $this->categoryModel->find((int)$job['service']['category_id']) : null;
            $job['review']   = $this->requestModel->review((int)$job['id']);
        }

        $this->render('technician/history', [
            'pageTitle'    => 'Riwayat Pekerjaan Saya - Teknisi SmartService AI',
            'user'         => $user,
            'flash'        => AuthMiddleware::getFlash(),
            'jobs'         => array_values($filtered),
            'statusFilter' => $statusFilter,
        ]);
    }

    /**
     * Access security helper: Ensure technician only accesses assigned jobs
     */
    private function verifyJobAccess(int $jobId, int $technicianId): ?array
    {
        if ($jobId <= 0) {
            AuthMiddleware::setFlash('danger', 'ID Pekerjaan tidak valid.');
            $this->redirectTo('/technician/dashboard');
            return null;
        }

        $job = $this->requestModel->find($jobId);

        if (!$job) {
            AuthMiddleware::setFlash('danger', 'Pekerjaan perbaikan tidak ditemukan.');
            $this->redirectTo('/technician/dashboard');
            return null;
        }

        // Allow access if job is an open unassigned pending job waiting to be claimed
        if (empty($job['technician_id']) && $job['status'] === ServiceRequest::STATUS_PENDING) {
            return $job;
        }

        // Strict Access Control: Technician can ONLY access jobs assigned to them
        if ((int)$job['technician_id'] !== $technicianId) {
            AuthMiddleware::setFlash('danger', "Akses Ditolak: Pekerjaan #JOB-{$jobId} tidak ditugaskan kepada Anda.");
            $this->redirectTo('/technician/dashboard');
            return null;
        }

        return $job;
    }
}

