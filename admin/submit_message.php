<?php
session_start();
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $subject = $_POST['subject'];
    $content = $_POST['content'];

    $stmt = $pdo->prepare('INSERT INTO messages (user_id, subject, content) VALUES (?, ?, ?)');
    $stmt->execute([$user_id, $subject, $content]);

    echo "Message submitted successfully! <a href='../index.php'>Back to home</a>";
}
