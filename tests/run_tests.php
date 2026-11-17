<?php

/**
 * SmartService AI - Standalone Local Test Runner
 *
 * Runs all unit tests locally without requiring Composer or global PHPUnit installation.
 * Compatible with PHPUnit test case classes.
 *
 * Usage:
 *   php tests/run_tests.php
 *   d:\xampp\php\php.exe tests/run_tests.php
 */

require_once __DIR__ . '/bootstrap.php';

$startTime = microtime(true);
$unitDir   = __DIR__ . '/Unit';
$files     = glob($unitDir . '/*Test.php');

$totalTests      = 0;
$passedTests     = 0;
$failedTests     = 0;
$totalAssertions = 0;
$failures        = [];

echo "\n";
echo "============================================================\n";
echo "  SmartService AI - Local Unit Test Suite Runner\n";
echo "============================================================\n\n";

foreach ($files as $file) {
    require_once $file;

    $className = 'Tests\\Unit\\' . basename($file, '.php');
    if (!class_exists($className)) {
        continue;
    }

    $shortName = basename($file, '.php');
    echo "• Suite: {$shortName}\n";

    $refClass = new ReflectionClass($className);
    $methods  = $refClass->getMethods(ReflectionMethod::IS_PUBLIC);

    foreach ($methods as $method) {
        if (!str_starts_with($method->getName(), 'test')) {
            continue;
        }

        $testName = $method->getName();
        $totalTests++;

        /** @var \PHPUnit\Framework\TestCase $instance */
        $instance = new $className();
        if (method_exists($instance, 'resetAssertionCount')) {
            $instance->resetAssertionCount();
        }

        $methodStart = microtime(true);
        $status = 'PASS';
        $errorMsg = null;

        try {
            $setUp = new ReflectionMethod($className, 'setUp');
            $setUp->setAccessible(true);
            $setUp->invoke($instance);

            $method->invoke($instance);

            $tearDown = new ReflectionMethod($className, 'tearDown');
            $tearDown->setAccessible(true);
            $tearDown->invoke($instance);
        } catch (\Throwable $e) {
            $status = 'FAIL';
            $errorMsg = $e->getMessage() . "\n" . $e->getFile() . ':' . $e->getLine();
            $failures[] = [
                'suite'   => $shortName,
                'test'    => $testName,
                'message' => $e->getMessage(),
                'trace'   => $e->getFile() . ':' . $e->getLine()
            ];
        }

        $duration = round((microtime(true) - $methodStart) * 1000, 2);
        $assertions = method_exists($instance, 'getAssertionCount') ? $instance->getAssertionCount() : 1;
        $totalAssertions += $assertions;

        if ($status === 'PASS') {
            $passedTests++;
            echo "   [OK]   {$testName} ({$duration} ms)\n";
        } else {
            $failedTests++;
            echo "   [FAIL] {$testName} ({$duration} ms)\n";
            echo "          Error: {$errorMsg}\n";
        }
    }

    echo "\n";
}

$totalTime = round((microtime(true) - $startTime) * 1000, 2);

echo "============================================================\n";
echo "  TEST RESULTS SUMMARY\n";
echo "============================================================\n";
echo "Total Tests     : {$totalTests}\n";
echo "Passed          : {$passedTests}\n";
echo "Failed          : {$failedTests}\n";
echo "Assertions      : {$totalAssertions}\n";
echo "Execution Time  : {$totalTime} ms\n";
echo "============================================================\n";

if ($failedTests > 0) {
    echo "\nFAILURES DETECTED ({$failedTests}):\n";
    foreach ($failures as $f) {
        echo " - [{$f['suite']}] {$f['test']}: {$f['message']}\n";
        echo "   at {$f['trace']}\n";
    }
    echo "\nSTATUS: FAILED\n";
    exit(1);
} else {
    echo "\nSTATUS: ALL TESTS PASSED SUCCESSFULLY! (SUCCESS)\n";
    exit(0);
}

