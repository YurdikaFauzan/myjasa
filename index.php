<?php

/**
 * SmartService AI - Entry Point (Front Controller)
 * MVC Architecture Native PHP
 */

// Set timezone Indonesia (WIB)
date_default_timezone_set('Asia/Jakarta');

// Start session if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Simple PSR-4 Autoloader for project namespaces (Config, Controllers, Models, Routes, Middleware)
spl_autoload_register(function ($class) {
    $baseDir = __DIR__ . '/';
    $file = $baseDir . str_replace('\\', '/', $class) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

// 2. Load Environment Variables from .env file
// Gunakan require_once langsung agar Config\Env pasti dimuat sebelum dipanggil
require_once __DIR__ . '/Config/Env.php';
\Config\Env::load(__DIR__ . '/.env');

// 3. Load Web Routes Definition
require_once __DIR__ . '/Routes/web.php';

// 4. Dispatch Request via Router
\Routes\Router::dispatch();
