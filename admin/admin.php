<?php
include '../includes/session.php';
include '../includes/db.php';

// Redirect if not admin
if ($_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

// Count users, bookings, services
$users = $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
$services = $pdo->query('SELECT COUNT(*) FROM services')->fetchColumn();
$bookings = $pdo->query('SELECT COUNT(*) FROM appointments')->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - GlamNail Studio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container mt-5">
        <div class="card shadow p-4">
            <h2 class="mb-4 text-center">Admin Dashboard</h2>
            <p class="text-center">Welcome, <strong><?= $_SESSION['name'] ?></strong> (Admin)</p>

            <div class="row text-center my-4">
                <div class="col-md-4">
                    <div class="border p-3 rounded bg-white">
                        <h4><?= $users ?></h4>
                        <p>Total Users</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border p-3 rounded bg-white">
                        <h4><?= $services ?></h4>
                        <p>Total Services</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border p-3 rounded bg-white">
                        <h4><?= $bookings ?></h4>
                        <p>Total Appointments</p>
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2 col-6 mx-auto">
                <a href="services.php" class="btn btn-primary">Manage Services</a>
                <a href="bookings.php" class="btn btn-secondary">View Bookings</a>
                <a href="../actions/logout.php" class="btn btn-danger">Logout</a>
            </div>
        </div>
    </div>

</body>

</html>
