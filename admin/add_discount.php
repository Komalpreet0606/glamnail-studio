<?php
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $percent = $_POST['percentage'];
    $from = $_POST['valid_from'];
    $to = $_POST['valid_to'];

    $stmt = $pdo->prepare('INSERT INTO discounts (title, percentage, valid_from, valid_to) VALUES (?, ?, ?, ?)');
    $stmt->execute([$title, $percent, $from, $to]);

    header('Location: ../admin/discounts.php');
}
