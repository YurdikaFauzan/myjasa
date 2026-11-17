<?php

namespace Controllers;

use Services\ChatbotService;
use Middleware\AuthMiddleware;

class ChatbotController
{
    private ChatbotService $chatbotService;

    public function __construct()
    {
        $this->chatbotService = new ChatbotService();
    }

    /**
     * AJAX endpoint for Chatbot (CS Assistant)
     * POST /api/chatbot
     */
    public function chat(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        // Chatbot is a public endpoint - no CSRF enforcement needed

        $message = trim($_POST['message'] ?? '');

        if (strlen($message) < 2) {
            http_response_code(422);
            echo json_encode(['status' => 'error', 'message' => 'Pesan terlalu pendek.']);
            exit;
        }

        // Retrieve current authenticated user from session (if logged in)
        $currentUser = AuthMiddleware::user();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Prevent cross-user history leak if user session changed
        $currentUserId = $currentUser ? (int)$currentUser['id'] : 0;
        $prevUserId    = $_SESSION['chatbot_user_id'] ?? null;
        if ($prevUserId !== null && $prevUserId !== $currentUserId) {
            $_SESSION['chatbot_history'] = [];
        }
        $_SESSION['chatbot_user_id'] = $currentUserId;

        // Retrieve conversation history from session
        $history = $_SESSION['chatbot_history'] ?? [];
        if (!is_array($history)) {
            $history = [];
        }

        $reply = $this->chatbotService->chat($message, $currentUser, $history);

        // Update conversation history (preserve up to 6 turns: 3 user, 3 assistant)
        $plainReply = strip_tags(str_replace(['<br />', '<br>', '<br/>'], "\n", $reply));
        $history[] = ['role' => 'user', 'content' => $message];
        $history[] = ['role' => 'assistant', 'content' => $plainReply];
        if (count($history) > 6) {
            $history = array_slice($history, -6);
        }
        $_SESSION['chatbot_history'] = $history;

        echo json_encode([
            'status' => 'success',
            'reply'  => $reply,
            'user'   => $currentUser ? [
                'id'    => $currentUser['id'],
                'name'  => $currentUser['name'],
                'email' => $currentUser['email'],
                'role'  => $currentUser['role']
            ] : null
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * AJAX endpoint to reset conversation history
     * POST /api/chatbot/reset
     */
    public function reset(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['chatbot_history'] = [];
        echo json_encode(['status' => 'success', 'message' => 'Riwayat percakapan berhasil direset.']);
        exit;
    }

    /**
     * AJAX endpoint to switch AI Model directly from Chatbot Widget
     * POST /api/chatbot/switch-model
     */
    public function switchModel(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $provider = strtolower(trim($_POST['provider'] ?? ''));
        $modelId  = trim($_POST['model_id'] ?? '');

        $supported = \Services\Ai\AiManager::getSupportedProviders();
        if (!isset($supported[$provider])) {
            http_response_code(422);
            echo json_encode(['status' => 'error', 'message' => "Provider '{$provider}' tidak didukung."]);
            exit;
        }

        if (empty($modelId)) {
            $modelId = $supported[$provider]['default_model'];
        }

        $currentUser = AuthMiddleware::user();
        $adminId = $currentUser ? (int)$currentUser['id'] : null;
        $adminName = $currentUser ? $currentUser['name'] : 'Chatbot Quick Switch';

        $aiConfigModel = new \Models\AiConfiguration();
        $ok = $aiConfigModel->saveConfig([
            'provider'  => $provider,
            'model_id'  => $modelId,
            'is_active' => 1
        ], $adminId, $adminName);

        if ($ok) {
            $provName = $supported[$provider]['name'];
            echo json_encode([
                'status'        => 'success',
                'message'       => "Model AI berhasil dialihkan ke {$provName} ({$modelId}).",
                'provider'      => $provider,
                'provider_name' => $provName,
                'model_id'      => $modelId
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Gagal mengubah model AI di sistem.']);
        }
        exit;
    }
}
