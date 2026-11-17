<?php

/**
 * Bootstrap for Unit Tests
 * Initializes autoloading, testing environment variables, and polyfills if needed.
 */

date_default_timezone_set('Asia/Jakarta');

// Set dummy testing environment variables
putenv('APP_ENV=testing');
putenv('APP_KEY=SmartServiceAI_Secret_Key_Testing_2026');
$_ENV['APP_ENV'] = 'testing';
$_ENV['APP_KEY'] = 'SmartServiceAI_Secret_Key_Testing_2026';

// 1. Check if Composer vendor autoloader exists (GitHub Actions or local after composer install)
$composerAutoload = dirname(__DIR__) . '/vendor/autoload.php';
if (file_exists($composerAutoload)) {
    require_once $composerAutoload;
}

// 2. Project PSR-4 Autoloader fallback
spl_autoload_register(function ($class) {
    $baseDir = dirname(__DIR__) . '/';
    $file = $baseDir . str_replace('\\', '/', $class) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

// 3. Tests namespace autoloader fallback
spl_autoload_register(function ($class) {
    if (str_starts_with($class, 'Tests\\')) {
        $rel = substr($class, 6);
        $file = __DIR__ . '/' . str_replace('\\', '/', $rel) . '.php';
        if (file_exists($file)) {
            require_once $file;
        }
    }
});

// 4. Fallback polyfill for PHPUnit TestCase if vendor/bin/phpunit is not present locally
if (!class_exists(\PHPUnit\Framework\TestCase::class)) {
    require_once __DIR__ . '/Polyfill/TestCase.php';
}

