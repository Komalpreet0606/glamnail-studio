<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Get user info
$stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Appointments + payments
$stmt = $pdo->prepare("SELECT a.*, s.title AS service_title, ad.full_name, ad.phone, ad.address, p.amount, p.currency, p.payment_status, p.method_type, p.created_at
    FROM appointments a
    JOIN services s ON a.service_id = s.id
    LEFT JOIN appointment_details ad ON a.id = ad.appointment_id
    LEFT JOIN payments p ON a.id = p.appointment_id
    WHERE a.user_id = ? ORDER BY a.appointment_date DESC, a.appointment_time DESC");
$stmt->execute([$user_id]);
$appointments = $stmt->fetchAll();

// Stats
$totalBookings = count($appointments);
$totalPaid = 0;
$latestPayment = null;

foreach ($appointments as $a) {
    if ($a['payment_status'] === 'succeeded') {
        $totalPaid += $a['amount'];
        if (!$latestPayment || strtotime($a['created_at']) > strtotime($latestPayment['created_at'])) {
            $latestPayment = $a;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>User Dashboard - GlamNail Studio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f7f8fa;
            font-family: 'Lato', sans-serif;
        }

        .section {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 25px;
            margin-bottom: 30px;
        }

        .stat-box {
            background: #fff0f6;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            transition: transform 0.3s ease;
        }

        .stat-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
        }

        .stat-box h4 {
            margin: 0;
            font-weight: 700;
            color: #d63384;
        }

        .badge {
            font-size: 0.85rem;
        }

        .nav-tabs .nav-link {
            border-radius: 8px;
            margin: 0 5px;
        }

        .tab-content {
            padding-top: 20px;
        }

        .tab-pane {
            transition: opacity 0.5s ease;
        }

        .card {
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 15px;
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }

        .btn-primary {
            background-color: #d63384;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #a05566;
        }

        .list-group-item {
            border-radius: 8px;
        }

        .toast {
            border-radius: 8px;
        }

        .toast-body {
            font-size: 1rem;
            font-family: 'Lato', sans-serif;
        }

        @media (max-width: 576px) {
            .container {
                padding-left: 20px;
                padding-right: 20px;
            }
        }
    </style>
</head>

<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container mt-5">
        <!-- Success/Failure Alerts -->
        <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
        <?php unset($_SESSION['success']); ?>
        <?php elseif (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
        <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- 📊 Quick Stats -->
        <div class="section row text-center">
            <div class="col-md-6 mb-3 stat-box">
                <h4><?= $totalBookings ?></h4>
                <p>Total Appointments</p>
            </div>
            <div class="col-md-6 mb-3 stat-box">
                <h4>$<?= number_format($totalPaid, 2) ?></h4>
                <p>Total Paid</p>
            </div>
        </div>

        <!-- 🔁 Toggle Tabs -->
        <div class="section">
            <ul class="nav nav-tabs" id="apptTabs" role="tablist">
                <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab"
                        data-bs-target="#upcoming">Upcoming</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#past">Past</button>
                </li>
            </ul>
            <div class="tab-content mt-3">
                <!-- Upcoming -->
                <div class="tab-pane fade show active" id="upcoming">
                    <?php
                    $hasUpcoming = false;
                    foreach ($appointments as $a):
                        if ($a['appointment_date'] >= date('Y-m-d')): $hasUpcoming = true; ?>
                    <div class="card mb-3">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1"><?= htmlspecialchars($a['service_title']) ?></h5>
                                <small><?= $a['appointment_date'] ?> @ <?= $a['appointment_time'] ?></small><br>
                                <span
                                    class="badge bg-<?= $a['payment_status'] === 'succeeded' ? 'success' : 'secondary' ?>"><?= strtoupper($a['payment_status'] ?? 'Unpaid') ?></span>
                                <span
                                    class="badge bg-<?= $a['status'] === 'cancelled' ? 'danger' : ($a['status'] === 'confirmed' ? 'primary' : 'warning') ?>"><?= ucfirst($a['status']) ?></span>
                            </div>
                            <div>
                                <strong>$<?= number_format($a['final_price'], 2) ?></strong><br>
                                <?php if ($a['status'] !== 'cancelled'): ?>
                                <form method="POST" action="actions/cancel_appointment.php"
                                    onsubmit="return confirm('Cancel this appointment?');">
                                    <input type="hidden" name="appointment_id" value="<?= $a['id'] ?>">
                                    <button class="btn btn-sm btn-outline-danger mt-2">Cancel</button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; endforeach;
                    if (!$hasUpcoming) echo '<div class="alert alert-info">No upcoming appointments.</div>';
                    ?>
                </div>

                <!-- Past -->
                <div class="tab-pane fade" id="past">
                    <?php
                    $hasPast = false;
                    foreach ($appointments as $a):
                        if ($a['appointment_date'] < date('Y-m-d')): $hasPast = true; ?>
                    <div class="card mb-3">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1"><?= htmlspecialchars($a['service_title']) ?></h5>
                                <small><?= $a['appointment_date'] ?> @ <?= $a['appointment_time'] ?></small><br>
                                <span
                                    class="badge bg-<?= $a['payment_status'] === 'succeeded' ? 'success' : 'secondary' ?>"><?= strtoupper($a['payment_status'] ?? 'Unpaid') ?></span>
                                <span
                                    class="badge bg-<?= $a['status'] === 'cancelled' ? 'danger' : ($a['status'] === 'confirmed' ? 'primary' : 'warning') ?>"><?= ucfirst($a['status']) ?></span>
                            </div>
                            <div>
                                <strong>$<?= number_format($a['final_price'], 2) ?></strong>
                            </div>
                        </div>
                    </div>
                    <?php endif; endforeach;
                    if (!$hasPast) echo '<div class="alert alert-secondary">No past appointments.</div>';
                    ?>
                </div>
            </div>
        </div>

        <!-- 💳 Latest Payment -->
        <div class="section">
            <h4>Latest Payment</h4>
            <?php if ($latestPayment): ?>
            <ul class="list-group">
                <li class="list-group-item"><strong>Service:</strong> <?= $latestPayment['service_title'] ?></li>
                <li class="list-group-item"><strong>Amount:</strong> $<?= number_format($latestPayment['amount'], 2) ?>
                    <?= strtoupper($latestPayment['currency']) ?></li>
                <li class="list-group-item"><strong>Status:</strong> <?= ucfirst($latestPayment['payment_status']) ?>
                </li>
                <li class="list-group-item"><strong>Method:</strong> <?= strtoupper($latestPayment['method_type']) ?>
                </li>
                <li class="list-group-item"><strong>Date:</strong> <?= $latestPayment['created_at'] ?></li>
            </ul>
            <?php else: ?>
            <div class="alert alert-secondary">No completed payments yet.</div>
            <?php endif; ?>
        </div>

    </div>

    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999;">
        <?php if (isset($_SESSION['success'])): ?>
        <div class="toast align-items-center text-white bg-success border-0 show" role="alert">
            <div class="d-flex">
                <div class="toast-body"><?= $_SESSION['success'] ?></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
        <?php unset($_SESSION['success']); endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
        <div class="toast align-items-center text-white bg-danger border-0 show" role="alert">
            <div class="d-flex">
                <div class="toast-body"><?= $_SESSION['error'] ?></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
        <?php unset($_SESSION['error']); endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toastEl = document.querySelector('.toast');
            if (toastEl) {
                new bootstrap.Toast(toastEl, {
                    delay: 3000
                }).show();
            }
        });
    </script>
</body>

</html>
