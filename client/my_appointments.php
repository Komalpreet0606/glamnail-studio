<?php
include '../includes/session.php';
include '../includes/db.php';

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT a.*, s.title AS service_name
    FROM appointments a
    JOIN services s ON a.service_id = s.id
    WHERE a.user_id = ?
    ORDER BY a.appointment_date DESC
");
$stmt->execute([$user_id]);
$appointments = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Appointments - GlamNail Studio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container py-5">
        <div class="card shadow p-4">
            <h2 class="mb-4 text-center">My Appointments</h2>

            <?php if (count($appointments) === 0): ?>
            <p class="text-center">You haven't booked any appointments yet.</p>
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
                <a href="profile.php" class="btn btn-secondary">← Back to Dashboard</a>
            </div>
        </div>
    </div>

</body>

</html>
