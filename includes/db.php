<?php
// includes/db.php

// Load Composer autoload if it exists (for vlucas/phpdotenv, Stripe, etc.)
$autoload = __DIR__ . '/../vendor/autoload.php';
if (is_file($autoload)) {
    require $autoload;
    // Load .env from project root for local/dev
    $root = dirname(__DIR__);
    if (is_file($root . '/.env')) {
        Dotenv\Dotenv::createImmutable($root)->safeLoad();
    }
}

// Prefer DB_* names (Render), fall back to Railway MYSQL* or local defaults
$host = $_ENV['DB_HOST'] ?? ($_ENV['MYSQLHOST'] ?? '127.0.0.1');
$port = (int) ($_ENV['DB_PORT'] ?? ($_ENV['MYSQLPORT'] ?? 3306));
$db = $_ENV['DB_NAME'] ?? ($_ENV['MYSQLDATABASE'] ?? 'glamnailstudio');
$user = $_ENV['DB_USER'] ?? ($_ENV['MYSQLUSER'] ?? 'root');
$pass = $_ENV['DB_PASS'] ?? ($_ENV['MYSQLPASSWORD'] ?? '');

$dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    exit('Database Connection Failed.');
}
