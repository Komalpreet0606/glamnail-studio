<?php
include '../includes/session.php';
include '../includes/db.php';

if ($_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

// Fetch bookings with user & service info
$stmt = $pdo->query("
    SELECT a.id, u.name AS user_name, s.title AS service_name,
           a.appointment_date, a.appointment_time, a.status
    FROM appointments a
    JOIN users u ON a.user_id = u.id
    JOIN services s ON a.service_id = s.id
    ORDER BY a.appointment_date, a.appointment_time
");

$bookings = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>All Bookings - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container py-5">
        <div class="card shadow p-4">
            <h2 class="text-center mb-4">All Bookings</h2>

            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Service</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $b): ?>
                    <tr>
                        <td><?= $b['id'] ?></td>
                        <td><?= $b['user_name'] ?></td>
                        <td><?= $b['service_name'] ?></td>
                        <td><?= $b['appointment_date'] ?></td>
                        <td><?= $b['appointment_time'] ?></td>
                        <td>
                            <span
                                class="badge bg-<?= $b['status'] === 'confirmed' ? 'success' : ($b['status'] === 'pending' ? 'warning' : 'danger') ?>">
                                <?= ucfirst($b['status']) ?>
                            </span>
                        </td>
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
