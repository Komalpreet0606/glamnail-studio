<?php
include '../includes/db.php';
$id = $_GET['id'];
$stmt = $pdo->prepare('DELETE FROM discounts WHERE id = ?');
$stmt->execute([$id]);
header('Location: ../admin/discounts.php');
exit();
