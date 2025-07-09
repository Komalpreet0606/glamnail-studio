<?php
include '../includes/session.php';
include '../includes/db.php';

// Get current user info
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Get user appointments
$stmt2 = $pdo->prepare("
    SELECT a.*, s.title AS service_name
    FROM appointments a
    JOIN services s ON a.service_id = s.id
    WHERE a.user_id = ?
    ORDER BY a.appointment_date DESC
");
$stmt2->execute([$user_id]);
$appointments = $stmt2->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Profile - GlamNail Studio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container py-5">
        <div class="card shadow p-4 mb-4">
            <h2 class="mb-3 text-center">Welcome, <?= $user['name'] ?></h2>
            <p class="text-center">Email: <?= $user['email'] ?> | Phone: <?= $user['phone'] ?></p>
        </div>

        <div class="card shadow p-4">
            <h4 class="mb-3">My Appointments</h4>

            <?php if (count($appointments) === 0): ?>
            <p>No appointments booked yet.</p>
            <?php else: ?>
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Service</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($appointments as $a): ?>
                    <tr>
                        <td><?= $a['service_name'] ?></td>
                        <td><?= $a['appointment_date'] ?></td>
                        <td><?= $a['appointment_time'] ?></td>
                        <td>
                            <span
                                class="badge bg-<?= $a['status'] === 'confirmed' ? 'success' : ($a['status'] === 'pending' ? 'warning' : 'danger') ?>">
                                <?= ucfirst($a['status']) ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>

            <div class="text-center mt-3">
                <a href="../index.php" class="btn btn-secondary">← Back to Home</a>
            </div>
        </div>
    </div>

</body>

</html>
