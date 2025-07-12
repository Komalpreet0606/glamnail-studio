<?php
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $title = trim($_POST['title']);
    $desc = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $oldImage = $_POST['old_image'];

    $newImageName = $oldImage;

    if (!empty($_FILES['image']['name'])) {
        $targetDir = '../images/';
        $imageFile = $_FILES['image'];
        $ext = strtolower(pathinfo($imageFile['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($ext, $allowed) && $imageFile['size'] <= 2 * 1024 * 1024) {
            $newImageName = time() . '_' . basename($imageFile['name']);
            move_uploaded_file($imageFile['tmp_name'], $targetDir . $newImageName);
        }
    }

    $stmt = $pdo->prepare('UPDATE services SET title = ?, description = ?, price = ?, image = ? WHERE id = ?');
    $stmt->execute([$title, $desc, $price, $newImageName, $id]);

    header('Location: ../admin/admin_services.php');
    exit();
}
