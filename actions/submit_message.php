<?php
session_start();
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'] ?? null;
    $subject = trim($_POST['subject']);
    $content = trim($_POST['content']);

    if (!$user_id || !$subject || !$content) {
        $_SESSION['support_error'] = 'Please fill in all fields.';
        header('Location: ../support.php');
        exit();
    }

    try {
        $stmt = $pdo->prepare('INSERT INTO messages (user_id, subject, content) VALUES (?, ?, ?)');
        $stmt->execute([$user_id, $subject, $content]);
        $_SESSION['support_success'] = 'Message submitted successfully!';
    } catch (Exception $e) {
        $_SESSION['support_error'] = 'Error: ' . $e->getMessage();
    }

    header('Location: ../support.php');
    exit();
}
?>
