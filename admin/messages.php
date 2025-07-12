<?php
include '../includes/session.php';
include '../includes/db.php';

if ($_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

$stmt = $pdo->query("
    SELECT m.id, u.name AS user_name, m.subject, m.content, m.date_sent
    FROM messages m
    JOIN users u ON m.user_id = u.id
    ORDER BY m.date_sent DESC
");
$messages = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>User Messages - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <?php include '../includes/admin_navbar.php'; ?>
    <div class="container py-5">
        <div class="card shadow p-4">
            <h2 class="text-center mb-4">User Messages</h2>

            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Subject</th>
                        <th>Message</th>
                        <th>Date Sent</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($messages as $msg): ?>
                    <tr>
                        <td><?= $msg['id'] ?></td>
                        <td><?= $msg['user_name'] ?></td>
                        <td><?= $msg['subject'] ?></td>
                        <td><?= $msg['content'] ?></td>
                        <td><?= $msg['date_sent'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="text-center mt-3">
                <a href="admin.php" class="btn btn-secondary">← Back to Dashboard</a>
            </div>
        </div>
    </div>
</body>

</html>
