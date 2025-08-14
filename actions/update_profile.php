<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';
include '../includes/db.php';

use Cloudinary\Cloudinary;

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$phone = trim($_POST['phone'] ?? '');
$address = trim($_POST['address'] ?? '');
$note = htmlspecialchars(trim($_POST['note'] ?? ''));
$errors = [];

// =======================
// ✅ VALIDATIONS START
// =======================

if (empty($phone)) {
    $errors[] = 'Phone number is required.';
} elseif (!preg_match('/^\d{10,15}$/', $phone)) {
    $errors[] = 'Phone must be 10–15 digits only.';
}

if (!empty($address) && strlen($address) > 255) {
    $errors[] = 'Address must be less than 255 characters.';
}

$hasImage = !empty($_FILES['profile_picture']['name']);
if ($hasImage) {
    $ext = strtolower(pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION));
    $size = $_FILES['profile_picture']['size'];
    $allowedTypes = ['jpg', 'jpeg', 'png'];

    if (!in_array($ext, $allowedTypes)) {
        $errors[] = 'Only JPG, JPEG, and PNG files are allowed.';
    } elseif ($size > 2 * 1024 * 1024) {
        $errors[] = 'Profile picture must be less than 2MB.';
    }
}

if (!empty($errors)) {
    $_SESSION['error'] = implode('<br>', $errors);
    header('Location: ../profile.php');
    exit();
}

// =======================
// ✅ DATABASE UPDATE
// =======================

// Update user's phone
$pdo->prepare('UPDATE users SET phone = ? WHERE id = ?')->execute([$phone, $user_id]);

// Update latest appointment detail if it exists
$stmt = $pdo->prepare('SELECT id FROM appointment_details WHERE appointment_id IN (
    SELECT id FROM appointments WHERE user_id = ?
) ORDER BY id DESC LIMIT 1');
$stmt->execute([$user_id]);
$detail = $stmt->fetch();

if ($detail) {
    $pdo->prepare('UPDATE appointment_details SET phone = ?, address = ?, note = ? WHERE id = ?')->execute([$phone, $address, $note, $detail['id']]);
}

// =======================
// ✅ Upload to Cloudinary (if image present)
// =======================
if ($hasImage && !empty($_FILES['profile_picture']['tmp_name'])) {
    try {
        $cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => $_ENV['CLOUDINARY_CLOUD_NAME'],
                'api_key' => $_ENV['CLOUDINARY_API_KEY'],
                'api_secret' => $_ENV['CLOUDINARY_API_SECRET'],
            ],
        ]);

        $uploadResult = $cloudinary->uploadApi()->upload($_FILES['profile_picture']['tmp_name'], [
            'folder' => 'glamnail_users',
            'public_id' => 'user_' . $user_id,
            'overwrite' => true,
            'resource_type' => 'image',
        ]);

        $imageUrl = $uploadResult['secure_url'];
        $_SESSION['profile_pic'] = $imageUrl;

        $pdo->prepare('UPDATE users SET profile_image = ? WHERE id = ?')->execute([$imageUrl, $user_id]);
    } catch (Exception $e) {
        $_SESSION['error'] = 'Profile picture upload failed: ' . $e->getMessage();
        header('Location: ../profile.php');
        exit();
    }
}

$_SESSION['success'] = 'Profile updated successfully!';
header('Location: ../profile.php');
exit();
