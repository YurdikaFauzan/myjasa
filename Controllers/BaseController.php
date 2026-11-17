<?php

namespace Controllers;

abstract class BaseController
{
    /**
     * Render a view file with optional data and wrapper layouts
     *
     * @param string $view Relative view filename (e.g. 'home' or 'home.php')
     * @param array $data Data array passed to view
     * @param bool $useLayout Whether to wrap with header/footer layouts
     */
    protected function render(string $view, array $data = [], bool $useLayout = true): void
    {
        extract($data);

        $viewPath = __DIR__ . '/../Views/' . rtrim($view, '.php') . '.php';

        if (!file_exists($viewPath)) {
            http_response_code(500);
            echo "Error: View '{$view}' tidak ditemukan di " . htmlspecialchars($viewPath);
            return;
        }

        if ($useLayout) {
            $headerPath = __DIR__ . '/../Views/layouts/header.php';
            $footerPath = __DIR__ . '/../Views/layouts/footer.php';

            if (file_exists($headerPath)) {
                require $headerPath;
            }

            require $viewPath;

            if (file_exists($footerPath)) {
                require $footerPath;
            }
        } else {
            require $viewPath;
        }
    }

    /**
     * Send JSON response helper
     *
     * @param mixed $data
     * @param int $statusCode
     */
    protected function json(mixed $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        exit;
    }

    /**
     * Helper to redirect to a relative application path
     *
     * @param string $path
     */
    protected function redirectTo(string $path): void
    {
        $baseUrl = rtrim(\Config\Env::get('APP_URL', 'http://localhost/Myjasagwuehhhhhhhhhhhhhhhhhhhhh'), '/');
        header('Location: ' . $baseUrl . '/' . ltrim($path, '/'));
        exit;
    }
}
