<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$phone = $_POST['phone'] ?? '';
$address = $_POST['address'] ?? '';
$errors = [];

// Update users table phone
$stmt = $pdo->prepare('UPDATE users SET phone = ? WHERE id = ?');
$stmt->execute([$phone, $user_id]);

// Update appointment_details if exists
$stmt = $pdo->prepare('SELECT id FROM appointment_details WHERE appointment_id IN (SELECT id FROM appointments WHERE user_id = ?) ORDER BY id DESC LIMIT 1');
$stmt->execute([$user_id]);
$detail = $stmt->fetch();

if ($detail) {
    $stmt = $pdo->prepare('UPDATE appointment_details SET phone = ?, address = ? WHERE id = ?');
    $stmt->execute([$phone, $address, $detail['id']]);
}

// Handle image upload
if (!empty($_FILES['profile_picture']['name'])) {
    $targetDir = '../uploads/';
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    $ext = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
    $filename = 'user_' . $user_id . '.' . strtolower($ext);
    $targetFile = $targetDir . $filename;

    if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $targetFile)) {
        $_SESSION['profile_pic'] = $filename;
    } else {
        $_SESSION['error'] = 'Profile picture upload failed.';
    }
}

$_SESSION['success'] = 'Profile updated successfully!';
header('Location: ../profile.php');
exit();
?>
