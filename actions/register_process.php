<?php
session_start();
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare('INSERT INTO users (name, email, phone, password) VALUES (?, ?, ?, ?)');

    try {
        $stmt->execute([$name, $email, $phone, $password]);
        $_SESSION['success'] = 'Registration successful! You can now log in.';
        header('Location: ../auth/register.php');
    } catch (PDOException $e) {
        if (str_contains($e->getMessage(), 'Integrity constraint violation')) {
            $_SESSION['error'] = 'Email already exists. Try logging in.';
        } else {
            $_SESSION['error'] = 'Something went wrong. Please try again.';
        }
        header('Location: ../auth/register.php');
    }
    exit();
}
