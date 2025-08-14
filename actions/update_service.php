<?php
require_once __DIR__ . '/../vendor/autoload.php';
include '../includes/db.php';
$root = dirname(__DIR__);
if (is_file($root . '/.env')) {
    // Local/dev: load from .env if it exists
    Dotenv\Dotenv::createImmutable($root)->safeLoad(); // ✅ no exception if missing
}
use Cloudinary\Cloudinary;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $title = trim($_POST['title']);
    $desc = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $oldImage = $_POST['old_image'] ?? '';

    $cloudinary = new Cloudinary([
        'cloud' => [
            'cloud_name' => $_ENV['CLOUDINARY_CLOUD_NAME'],
            'api_key' => $_ENV['CLOUDINARY_API_KEY'],
            'api_secret' => $_ENV['CLOUDINARY_API_SECRET'],
        ],
    ]);

    $newImageUrl = $oldImage;

    // Check if new image is uploaded
    if (!empty($_FILES['image']['name'])) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($ext, $allowed, true) && $_FILES['image']['size'] <= 2 * 1024 * 1024) {
            try {
                // Upload new image
                $uploadResult = $cloudinary->uploadApi()->upload($_FILES['image']['tmp_name'], [
                    'folder' => 'glamnail_services',
                    'overwrite' => false,
                    'resource_type' => 'image',
                ]);

                $newImageUrl = $uploadResult['secure_url'];

                // Optional: delete old image from Cloudinary if it was hosted there
                if (strpos($oldImage, 'res.cloudinary.com') !== false) {
                    $publicId = basename(parse_url($oldImage, PHP_URL_PATH), '.' . $ext);
                    $publicId = 'glamnail_services/' . $publicId;
                    $cloudinary->uploadApi()->destroy($publicId);
                }
            } catch (Exception $e) {
                // If upload fails, retain the old image
                $newImageUrl = $oldImage;
            }
        }
    }

    // Update DB
    $stmt = $pdo->prepare('UPDATE services SET title = ?, description = ?, price = ?, image = ? WHERE id = ?');
    $stmt->execute([$title, $desc, $price, $newImageUrl, $id]);

    header('Location: ../admin/admin_services.php');
    exit();
}
