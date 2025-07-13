<?php
// Optional: load .env
// Attempt to load .env
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();
}

// ✅ Provide fallbacks if .env is missing or fails
$host = getenv('DB_HOST') ?: 'localhost';
$port = getenv('DB_PORT') ?: 3306;
$db = getenv('DB_NAME') ?: 'glamnailstudio';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: ''; // default XAMPP password is empty

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

?>
