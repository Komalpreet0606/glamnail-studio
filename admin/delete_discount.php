<?php
include '../includes/db.php';

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare('DELETE FROM discounts WHERE id = ?');
    $stmt->execute([$_GET['id']]);
}

header('Location: ../admin/discounts.php');
