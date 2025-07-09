<?php
include '../includes/db.php';

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare('DELETE FROM services WHERE id = ?');
    $stmt->execute([$_GET['id']]);
}

header('Location: ../admin/services.php');
