<?php
// actions/login_process.php
session_start();
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    echo '<pre>';
    print_r($user);
    echo '</pre>';

    echo 'Password entered: ' . $password . '<br>';
    echo 'Password in DB: ' . $user['password'] . '<br>';

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['name'];

        if ($user['role'] === 'admin') {
            header('Location: ../admin/admin.php');
        } else {
            header('Location: ../index.php');
        }
    } else {
        echo 'Invalid email or password';
    }
}
