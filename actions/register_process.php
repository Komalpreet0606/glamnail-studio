<?php
// actions/register_process.php
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare('INSERT INTO users (name, email, phone, password) VALUES (?, ?, ?, ?)');
    try {
        $stmt->execute([$name, $email, $phone, $password]);
        header('Location: ../auth/login.php?success=1');
    } catch (PDOException $e) {
        echo 'Error: ' . $e->getMessage();
    }
}
