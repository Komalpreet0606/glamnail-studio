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
        $fsDir = realpath(__DIR__ . '/../images');
        if ($fsDir === false) {
            $fsDir = __DIR__ . '/../images';
        }
        if (!is_dir($fsDir)) {
            @mkdir($fsDir, 0775, true);
        }

        $imageFile = $_FILES['image'];
        $ext = strtolower(pathinfo($imageFile['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($ext, $allowed, true) && $imageFile['size'] <= 2 * 1024 * 1024) {
            $base = preg_replace('/[^A-Za-z0-9_.-]/', '_', pathinfo($imageFile['name'], PATHINFO_FILENAME));
            $base = preg_replace('/_+/', '_', $base);
            $newImageName = time() . '_' . $base . '.' . $ext;

            $dest = rtrim($fsDir, '/\\') . DIRECTORY_SEPARATOR . $newImageName;
            if (!move_uploaded_file($imageFile['tmp_name'], $dest)) {
                $newImageName = $oldImage; // keep old on failure
            } else {
                // Optionally delete old image if replaced
                if ($oldImage && $oldImage !== $newImageName) {
                    $oldPath = rtrim($fsDir, '/\\') . DIRECTORY_SEPARATOR . $oldImage;
                    if (is_file($oldPath)) {
                        @unlink($oldPath);
                    }
                }
            }
        }
    }

    $stmt = $pdo->prepare('UPDATE services SET title = ?, description = ?, price = ?, image = ? WHERE id = ?');
    $stmt->execute([$title, $desc, $price, $newImageName, $id]);

    header('Location: ../admin/admin_services.php');
    exit();
}
