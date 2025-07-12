<?php
include '../includes/session.php';
include '../includes/db.php';

// Redirect if not admin
if ($_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

// Dashboard counts
$totalUsers = $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
$totalServices = $pdo->query('SELECT COUNT(*) FROM services')->fetchColumn();
$totalBookings = $pdo->query('SELECT COUNT(*) FROM appointments')->fetchColumn();
$discountStats = $pdo
    ->query(
        "
    SELECT d.title, COUNT(a.discount_id) as used
    FROM discounts d
    LEFT JOIN appointments a ON d.id = a.discount_id
    GROUP BY d.id
",
    )
    ->fetchAll();
$monthly = $pdo
    ->query(
        "
    SELECT DATE_FORMAT(appointment_date, '%b') as month, COUNT(*) as total
    FROM appointments
    GROUP BY month
    ORDER BY MIN(appointment_date)
",
    )
    ->fetchAll();

$labels = array_column($monthly, 'month');
$data = array_column($monthly, 'total');

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - GlamNail Studio</title>
    <title>Admin Dashboard - GlamNail Studio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- ✅ LOAD EARLY -->

    <style>
        .card-box {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
            padding: 20px;
            transition: 0.3s;
        }

        .card-box:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
        }

        .icon-box {
            font-size: 30px;
            margin-bottom: 10px;
        }

        .section-box {
            background: white;
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.04);
            padding: 20px;
            margin-bottom: 30px;
        }

        .badge-pill {
            border-radius: 50px;
            padding: 6px 14px;
            font-size: 14px;
            background-color: #e9ecef;
            margin: 4px 6px 4px 0;
            display: inline-block;
        }

        .chart-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.04);
            padding: 25px;
            margin-top: 30px;
        }
    </style>
</head>

<body class="bg-light">

    <?php include '../includes/admin_navbar.php'; ?>

    <div class="container my-5">
        <div class="text-center mb-4">
            <h2 class="fw-bold">🛠️ Admin Dashboard</h2>
            <p>Welcome, <strong><?= $_SESSION['name'] ?></strong> (Admin)</p>
        </div>

        <div class="row text-center mb-5">
            <div class="col-md-4 mb-3">
                <div class="card-box bg-light border border-primary">
                    <div class="icon-box text-primary"><i class="bi bi-people-fill"></i></div>
                    <h4><?= $totalUsers ?></h4>
                    <p>Total Users</p>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card-box bg-light border border-success">
                    <div class="icon-box text-success"><i class="bi bi-star-fill"></i></div>
                    <h4><?= $totalServices ?></h4>
                    <p>Total Services</p>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card-box bg-light border border-danger">
                    <div class="icon-box text-danger"><i class="bi bi-calendar-check-fill"></i></div>
                    <h4><?= $totalBookings ?></h4>
                    <p>Total Appointments</p>
                </div>
            </div>
        </div>
        <div class="row mt-5">
            <!-- Recent Users -->
            <div class="col-md-4">
                <div class="section-box">
                    <h5 class="mb-3">🧑‍🤝‍🧑 Latest Users</h5>
                    <ul class="list-group list-group-flush">
                        <?php
                $recentUsers = $pdo->query("SELECT name, email FROM users ORDER BY id DESC LIMIT 5")->fetchAll();
                foreach ($recentUsers as $u): ?>
                        <li class="list-group-item">
                            <?= htmlspecialchars($u['name']) ?>
                            <small class="text-muted d-block"><?= $u['email'] ?></small>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <!-- Recent Bookings -->
            <div class="col-md-4">
                <div class="section-box">
                    <h5 class="mb-3">📅 Recent Bookings</h5>
                    <ul class="list-group list-group-flush">
                        <?php
                $recentBookings = $pdo->query("
                    SELECT a.appointment_date, s.title
                    FROM appointments a
                    JOIN services s ON a.service_id = s.id
                    ORDER BY a.id DESC LIMIT 5
                ")->fetchAll();
                foreach ($recentBookings as $b): ?>
                        <li class="list-group-item">
                            <?= $b['title'] ?> <br>
                            <small class="text-muted"><?= $b['appointment_date'] ?></small>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <div class="col-md-4">
                <div class="section-box">
                    <h5 class="mb-3">🎁 Discount Usage</h5>
                    <?php foreach ($discountStats as $d): ?>
                    <span class="badge-pill">
                        <?= htmlspecialchars($d['title']) ?>: <?= $d['used'] ?> used
                    </span>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="chart-container">
                <h5 class="mb-3">📈 Monthly Appointments</h5>
                <canvas id="bookingChart" height="120"></canvas>
            </div>


        </div>

    </div>

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <script>
        const ctx = document.getElementById('bookingChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($labels) ?>,
                datasets: [{
                    label: 'Bookings',
                    data: <?= json_encode($data) ?>,
                    backgroundColor: '#0d6efd'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>

</html>
