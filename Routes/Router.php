<?php

namespace Routes;

class Router
{
    private static array $routes = [];

    /**
     * Register a GET route
     *
     * @param string $path
     * @param callable|array|string $handler
     */
    public static function get(string $path, callable|array|string $handler): void
    {
        self::addRoute('GET', $path, $handler);
    }

    /**
     * Register a POST route
     *
     * @param string $path
     * @param callable|array|string $handler
     */
    public static function post(string $path, callable|array|string $handler): void
    {
        self::addRoute('POST', $path, $handler);
    }

    /**
     * Add route to collection
     */
    private static function addRoute(string $method, string $path, callable|array|string $handler): void
    {
        // Normalize path
        $path = '/' . trim($path, '/');
        if ($path !== '/') {
            $path = rtrim($path, '/');
        }

        self::$routes[] = [
            'method'  => strtoupper($method),
            'path'    => $path,
            'handler' => $handler
        ];
    }

    /**
     * Resolve incoming request
     */
    public static function dispatch(): void
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $requestUri    = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

        // Decode URL encoding (e.g. %20 -> space)
        $requestUri = urldecode($requestUri);

        // Tentukan base path dari APP_URL (lebih akurat dari dirname SCRIPT_NAME)
        $appUrl  = \Config\Env::get('APP_URL', '');
        $basePath = '';

        if (!empty($appUrl)) {
            $parsed = parse_url(rtrim($appUrl, '/'));
            $basePath = $parsed['path'] ?? '';
        }

        // Fallback ke dirname(SCRIPT_NAME) jika APP_URL tidak tersedia
        if (empty($basePath)) {
            $basePath = dirname($_SERVER['SCRIPT_NAME'] ?? '/');
            $basePath = str_replace('\\', '/', $basePath);
        }

        // Strip base path dari request URI
        $basePath = rtrim($basePath, '/');
        if ($basePath !== '' && str_starts_with($requestUri, $basePath)) {
            $requestUri = substr($requestUri, strlen($basePath));
        }

        // Normalize URI
        $requestUri = '/' . trim($requestUri, '/');
        if ($requestUri !== '/') {
            $requestUri = rtrim($requestUri, '/');
        }

        foreach (self::$routes as $route) {
            if ($route['method'] === $requestMethod && $route['path'] === $requestUri) {
                self::executeHandler($route['handler']);
                return;
            }
        }

        // 404 Not Found fallback
        http_response_code(404);
        echo "<div style='text-align:center; padding: 50px; font-family: sans-serif;'>";
        echo "<h1>404 - Halaman Tidak Ditemukan</h1>";
        echo "<p>Rute untuk <code>" . htmlspecialchars($requestUri) . "</code> tidak ditemukan pada sistem SmartService AI.</p>";
        echo "<a href='" . \Config\Env::get('APP_URL', '/') . "' style='color: #0d6efd; text-decoration: none;'>Kembali ke Beranda</a>";
        echo "</div>";
    }

    /**
     * Execute controller action or callback
     */
    private static function executeHandler(callable|array|string $handler): void
    {
        if (is_callable($handler)) {
            call_user_func($handler);
            return;
        }

        if (is_array($handler) && count($handler) === 2) {
            list($class, $method) = $handler;
            if (class_exists($class)) {
                $controller = new $class();
                if (method_exists($controller, $method)) {
                    $controller->$method();
                    return;
                }
            }
        }

        if (is_string($handler) && str_contains($handler, '@')) {
            list($class, $method) = explode('@', $handler);
            $fullClass = "\\Controllers\\" . $class;
            if (class_exists($fullClass)) {
                $controller = new $fullClass();
                if (method_exists($controller, $method)) {
                    $controller->$method();
                    return;
                }
            }
        }

        http_response_code(500);
        echo "500 Internal Server Error: Handler route tidak valid.";
    }
}
