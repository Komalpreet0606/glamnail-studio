session_start();
require '../includes/db.php';
require '../includes/jwt_config.php';
require '../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

$stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password'])) {
$_SESSION['error'] = 'Invalid email or password.';
header('Location: ../auth/login.php');
exit();
}

// ✅ Generate JWT
$payload = [
'iss' => JWT_ISSUER,
'sub' => $user['id'],
'email' => $user['email'],
'role' => $user['role'],
'exp' => time() + JWT_EXPIRATION,
];

$token = JWT::encode($payload, JWT_SECRET, 'HS256');

// ✅ Store in session
$_SESSION['user_id'] = $user['id'];
$_SESSION['role'] = $user['role'];
$_SESSION['name'] = $user['name'];
$_SESSION['jwt'] = $token;

// ✅ Redirect based on role
if ($user['role'] === 'admin') {
header('Location: ../admin/admin.php');
} else {
header('Location: ../index.php');
}
exit();
}
