<?php
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $desc = $_POST['description'];
    $price = $_POST['price'];
    $image = $_POST['image'];

    $stmt = $pdo->prepare('INSERT INTO services (title, description, price, image) VALUES (?, ?, ?, ?)');
    $stmt->execute([$title, $desc, $price, $image]);

    header('Location: ../admin/services.php');
}
