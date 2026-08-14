<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . 'environment.php';

if (!function_exists('database_config')) {
    function database_config()
    {
        static $config;
        if ($config === null) {
            $config = array(
                'host' => (string) app_env('DB_HOST', 'localhost'),
                'port' => (int) app_env('DB_PORT', 3306),
                'name' => (string) app_env('DB_NAME', 'lhp'),
                'user' => (string) app_env('DB_USER', 'root'),
                'password' => (string) app_env('DB_PASSWORD', ''),
                'charset' => (string) app_env('DB_CHARSET', 'utf8mb4'),
            );
        }
        return $config;
    }
}

if (!function_exists('database_mysqli')) {
    function database_mysqli()
    {
        $config = database_config();
        $connection = mysqli_connect(
            $config['host'],
            $config['user'],
            $config['password'],
            $config['name'],
            $config['port']
        );
        if (!$connection) {
            throw new RuntimeException('Database connection failed.');
        }
        mysqli_set_charset($connection, $config['charset']);
        return $connection;
    }
}

if (!function_exists('database_pdo')) {
    function database_pdo()
    {
        $config = database_config();
        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $config['host'],
            $config['port'],
            $config['name'],
            $config['charset']
        );
        return new PDO($dsn, $config['user'], $config['password'], array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ));
    }
}

