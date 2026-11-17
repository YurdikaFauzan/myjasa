<?php

namespace Config;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;
    private static ?string $lastError = null;

    /**
     * Get singleton PDO Database connection instance
     *
     * @return PDO|null
     */
    public static function getConnection(): ?PDO
    {
        if (self::$instance === null) {
            $host = Env::get('DB_HOST', '127.0.0.1');
            $port = Env::get('DB_PORT', '3306');
            $db   = Env::get('DB_NAME', 'smartservice_ai');
            $user = Env::get('DB_USER', 'root');
            $pass = Env::get('DB_PASS', '');

            $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";

            date_default_timezone_set('Asia/Jakarta');

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_TIMEOUT            => 3,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET time_zone = '+07:00'"
            ];

            try {
                self::$instance = new PDO($dsn, $user, $pass, $options);
                self::$lastError = null;
            } catch (PDOException $e) {
                self::$lastError = $e->getMessage();
                self::$instance = null;
            }
        }

        return self::$instance;
    }

    /**
     * Test connection to database server and check if specific DB exists
     *
     * @return array Status info
     */
    public static function checkStatus(): array
    {
        $conn = self::getConnection();
        if ($conn !== null) {
            return [
                'connected' => true,
                'message'   => 'Terhubung ke database MySQL (' . Env::get('DB_NAME', 'smartservice_ai') . ')',
                'error'     => null
            ];
        }

        // Try connecting to MySQL server without specifying database name to see if server is online
        $host = Env::get('DB_HOST', '127.0.0.1');
        $port = Env::get('DB_PORT', '3306');
        $user = Env::get('DB_USER', 'root');
        $pass = Env::get('DB_PASS', '');

        try {
            $testServer = new PDO("mysql:host={$host};port={$port}", $user, $pass, [PDO::ATTR_TIMEOUT => 2]);
            return [
                'connected' => false,
                'message'   => 'Server MySQL aktif, tetapi database "' . Env::get('DB_NAME', 'smartservice_ai') . '" belum dibuat.',
                'error'     => self::$lastError
            ];
        } catch (PDOException $e) {
            return [
                'connected' => false,
                'message'   => 'Gagal terhubung ke Server MySQL. Pastikan MySQL di XAMPP/server sudah berjalan.',
                'error'     => $e->getMessage()
            ];
        }
    }

    /**
     * Get last connection error message
     *
     * @return string|null
     */
    public static function getLastError(): ?string
    {
        return self::$lastError;
    }
}
