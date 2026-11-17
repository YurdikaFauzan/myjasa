<?php

namespace Controllers;

use Config\Env;
use Middleware\AuthMiddleware;
use Models\AiConsultation;
use Models\Category;
use Models\User;
use Services\AiDiagnosisService;

class AiConsultationController extends BaseController
{
    private AiConsultation $consultModel;
    private Category $categoryModel;
    private AiDiagnosisService $aiService;

    public function __construct()
    {
        $this->consultModel  = new AiConsultation();
        $this->categoryModel = new Category();
        $this->aiService     = new AiDiagnosisService();
    }

    /**
     * Halaman UI Konsultasi AI untuk Customer (GET /customer/ai-consultation)
     */
    public function index(): void
    {
        $user       = AuthMiddleware::requireRole(User::ROLE_CUSTOMER);
        $categories = array_filter($this->categoryModel->all('name', 'ASC'), fn($c) => $c['status'] === Category::STATUS_ACTIVE);

        // Fetch recent consultations for sidebar history
        $myConsultations = $this->consultModel->where('customer_id', (int)$user['id']);
        usort($myConsultations, fn($a, $b) => $b['id'] <=> $a['id']);

        foreach ($myConsultations as &$c) {
            $c['category']  = $c['category_id'] ? $this->categoryModel->find((int)$c['category_id']) : null;
            $c['ai_result'] = json_decode($c['ai_result'] ?? '{}', true) ?? [];
        }

        $this->render('customer/ai_consultation', [
            'pageTitle'       => 'Konsultasi AI Diagnosis Kerusakan - SmartService AI',
            'user'            => $user,
            'flash'           => AuthMiddleware::getFlash(),
            'categories'      => array_values($categories),
            'consultations'   => array_slice($myConsultations, 0, 10),
            'csrfToken'       => AuthMiddleware::generateCsrfToken()
        ]);
    }

    /**
     * Process Konsultasi / Diagnosis AJAX Request (POST /customer/ai-consultation)
     */
    public function diagnose(): void
    {
        $user = AuthMiddleware::requireRole(User::ROLE_CUSTOMER);
        header('Content-Type: application/json; charset=utf-8');

        $csrfToken = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        if (!AuthMiddleware::verifyCsrfToken($csrfToken)) {
            http_response_code(403);
            echo json_encode(['status' => 'error', 'message' => 'Token CSRF tidak valid.']);
            exit;
        }

        $categoryId  = (int)($_POST['category_id'] ?? 0);
        $question    = trim($_POST['question'] ?? '');
        $chatHistory = isset($_POST['chat_history']) ? json_decode($_POST['chat_history'], true) : [];

        if (strlen($question) < 4) {
            http_response_code(422);
            echo json_encode(['status' => 'error', 'message' => 'Deskripsi keluhan terlalu pendek. Minimal 4 karakter.']);
            exit;
        }

        $category = $categoryId > 0 ? $this->categoryModel->find($categoryId) : null;
        $categoryName = $category['name'] ?? 'Peralatan Rumah Tangga';

        // Call AI Diagnosis Service
        $diagnosis = $this->aiService->diagnose($categoryName, $question, is_array($chatHistory) ? $chatHistory : []);

        // Prepare structured AI result for DB
        $aiResult = [
            'possible_issues'        => $diagnosis['possible_issues'] ?? [],
            'possible_causes'        => $diagnosis['possible_causes'] ?? [],
            'simple_checks'          => $diagnosis['simple_checks'] ?? [],
            'service_recommendation' => $diagnosis['service_recommendation'] ?? [],
            'urgency_level'          => $diagnosis['urgency_level'] ?? 'Sedang',
            'needs_technician'       => $diagnosis['needs_technician'] ?? 'Ya',
            'safety_warning'         => $diagnosis['safety_warning'] ?? null,
            'consultation_questions' => $diagnosis['consultation_questions'] ?? [],
            'summary'                => $diagnosis['summary'] ?? ''
        ];

        $recommendationText = implode(' | ', $diagnosis['service_recommendation'] ?? []);

        // Save consultation into database `ai_consultations`
        $consultId = $this->consultModel->create([
            'customer_id'    => (int)$user['id'],
            'category_id'    => $categoryId > 0 ? $categoryId : null,
            'question'       => $question,
            'answer'         => $diagnosis['summary'] ?? '',
            'ai_result'      => json_encode($aiResult, JSON_UNESCAPED_UNICODE),
            'recommendation' => $recommendationText
        ]);

        echo json_encode([
            'status'         => 'success',
            'consultation_id'=> $consultId,
            'category_name'  => $categoryName,
            'diagnosis'      => $aiResult
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Halaman / JSON History Konsultasi AI (GET /customer/ai-consultation/history)
     */
    public function history(): void
    {
        $user = AuthMiddleware::requireRole(User::ROLE_CUSTOMER);

        $myConsultations = $this->consultModel->where('customer_id', (int)$user['id']);
        usort($myConsultations, fn($a, $b) => $b['id'] <=> $a['id']);

        foreach ($myConsultations as &$c) {
            $c['category']  = $c['category_id'] ? $this->categoryModel->find((int)$c['category_id']) : null;
            $c['ai_result'] = json_decode($c['ai_result'] ?? '{}', true) ?? [];
        }

        // Return JSON if requested via AJAX or Header
        if (isset($_GET['format']) && $_GET['format'] === 'json') {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['status' => 'success', 'history' => array_values($myConsultations)], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // Otherwise render UI Page
        $this->render('customer/ai_consultation', [
            'pageTitle'     => 'Riwayat Konsultasi AI - SmartService AI',
            'user'          => $user,
            'flash'         => AuthMiddleware::getFlash(),
            'categories'    => array_values($this->categoryModel->all('name', 'ASC')),
            'consultations' => array_values($myConsultations),
            'csrfToken'     => AuthMiddleware::generateCsrfToken()
        ]);
    }
}
