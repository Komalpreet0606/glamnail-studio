<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$phone = trim($_POST['phone'] ?? '');
$address = trim($_POST['address'] ?? '');
$note = trim($_POST['note'] ?? '');
$errors = [];

// =======================
// ✅ VALIDATIONS START
// =======================

// Phone: Required, digits only, 10-15 length
if (empty($phone)) {
    $errors[] = 'Phone number is required.';
} elseif (!preg_match('/^\d{10,15}$/', $phone)) {
    $errors[] = 'Phone must be 10–15 digits only.';
}

// Address: Optional but max 255 chars and clean
if (!empty($address) && strlen($address) > 255) {
    $errors[] = 'Address must be less than 255 characters.';
}

// Note: Clean input
$note = htmlspecialchars($note);

// Profile Picture: Optional
if (!empty($_FILES['profile_picture']['name'])) {
    $allowedTypes = ['jpg', 'jpeg', 'png'];
    $ext = strtolower(pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION));
    $size = $_FILES['profile_picture']['size'];

    if (!in_array($ext, $allowedTypes)) {
        $errors[] = 'Only JPG, JPEG, and PNG files are allowed.';
    } elseif ($size > 2 * 1024 * 1024) {
        $errors[] = 'Profile picture must be less than 2MB.';
    }
}

// If there are errors, redirect with error session
if (!empty($errors)) {
    $_SESSION['error'] = implode('<br>', $errors);
    header('Location: ../profile.php');
    exit();
}

// =======================
// ✅ DATABASE UPDATE
// =======================

// Update phone in users table
$stmt = $pdo->prepare('UPDATE users SET phone = ? WHERE id = ?');
$stmt->execute([$phone, $user_id]);

// Get latest appointment detail ID
$stmt = $pdo->prepare('SELECT id FROM appointment_details WHERE appointment_id IN (
    SELECT id FROM appointments WHERE user_id = ?
) ORDER BY id DESC LIMIT 1');
$stmt->execute([$user_id]);
$detail = $stmt->fetch();

if ($detail) {
    $stmt = $pdo->prepare('UPDATE appointment_details SET phone = ?, address = ?, note = ? WHERE id = ?');
    $stmt->execute([$phone, $address, $note, $detail['id']]);
}

// Handle profile picture upload
if (!empty($_FILES['profile_picture']['name'])) {
    $targetDir = '../uploads/';
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    $filename = 'user_' . $user_id . '.' . $ext;
    $targetFile = $targetDir . $filename;

    if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $targetFile)) {
        $_SESSION['profile_pic'] = $filename;
    } else {
        $_SESSION['error'] = 'Profile picture upload failed.';
        header('Location: ../profile.php');
        exit();
    }
}

$_SESSION['success'] = 'Profile updated successfully!';
header('Location: ../profile.php');
exit();
?>
