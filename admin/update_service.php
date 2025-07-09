<?php
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $desc = $_POST['description'];
    $price = $_POST['price'];
    $image = $_POST['image'];

    $stmt = $pdo->prepare('UPDATE services SET title = ?, description = ?, price = ?, image = ? WHERE id = ?');
    $stmt->execute([$title, $desc, $price, $image, $id]);

    header('Location: ../admin/services.php');
}
