<?php
session_start();
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['booking'] = [
        'service_id' => $_POST['service_id'],
        'appointment_date' => $_POST['appointment_date'],
        'appointment_time' => $_POST['appointment_time'],
        'discount_id' => $_POST['discount_id'] ?? null,
    ];
    header('Location: checkout.php');
    exit();
}

if (!isset($_SESSION['booking'])) {
    header('Location: booking.php');
    exit();
}

$booking = $_SESSION['booking'];
$service_id = $booking['service_id'];
$date = $booking['appointment_date'];
$time = $booking['appointment_time'];
$discount_id = $booking['discount_id'] ?? null;

// Get service details
$stmt = $pdo->prepare('SELECT title, price FROM services WHERE id = ?');
$stmt->execute([$service_id]);
$service = $stmt->fetch();

// Get discount
$discount = 0;
if ($discount_id) {
    $stmt = $pdo->prepare('SELECT percentage FROM discounts WHERE id = ? AND valid_from <= CURDATE() AND valid_to >= CURDATE()');
    $stmt->execute([$discount_id]);
    $discount = $stmt->fetchColumn();
}

$price = $service['price'];
$discount_amount = $price * ($discount / 100);
$subtotal = $price - $discount_amount;
$tax = $subtotal * 0.13;
$total = $subtotal + $tax;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Checkout - GlamNail Studio</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&family=Lato&display=swap"
        rel="stylesheet">

    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Lato', sans-serif;
            background-color: #fffafc;
            color: #333;
        }

        h1,
        h2,
        h3,
        h4 {
            font-family: 'Playfair Display', serif;
            color: #b76e79;
        }

        .btn-cta {
            background: linear-gradient(to right, #ff8abf, #d669b5);
            border: none;
            color: white;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(214, 105, 181, 0.4);
            transition: all 0.3s ease-in-out;
        }

        .btn-cta:hover {
            background: linear-gradient(to right, #e75da8, #b158a1);
            box-shadow: 0 6px 18px rgba(214, 105, 181, 0.6);
            transform: translateY(-2px);
        }

        .card-style {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.07);
            padding: 30px;
            transition: all 0.3s ease;
        }

        .section-title {
            color: #d63384;
            font-weight: bold;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .form-label {
            font-weight: 500;
        }

        .list-group-item {
            background: #fff9fb;
        }
    </style>
</head>

<body>
    <!-- NAVBAR -->
    <?php include 'includes/navbar.php'; ?>

    <!-- Checkout Section -->
    <div class="container py-5" data-aos="fade-up">
        <h2 class="section-title">Checkout</h2>
        <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
        <?php unset($_SESSION['error']); endif; ?>

        <div class="row g-4 justify-content-center">
            <!-- Left: Appointment Summary -->
            <div class="col-lg-6" data-aos="fade-right">
                <div class="card-style h-100">
                    <h5 class="mb-3">Appointment Summary</h5>
                    <ul class="list-group mb-4">
                        <li class="list-group-item"><strong>Service:</strong> <?= htmlspecialchars($service['title']) ?>
                        </li>
                        <li class="list-group-item"><strong>Date:</strong> <?= htmlspecialchars($date) ?></li>
                        <li class="list-group-item"><strong>Time:</strong> <?= htmlspecialchars($time) ?></li>
                        <li class="list-group-item"><strong>Price:</strong> $<?= number_format($price, 2) ?></li>
                        <li class="list-group-item"><strong>Discount:</strong> <?= $discount ?>%</li>
                        <li class="list-group-item"><strong>Tax (13%):</strong> $<?= number_format($tax, 2) ?></li>
                        <li class="list-group-item"><strong>Total:</strong>
                            <strong>$<?= number_format($total, 2) ?></strong>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Right: Customer Info Form -->
            <div class="col-lg-6" data-aos="fade-left">
                <div class="card-style h-100">
                    <h5 class="mb-3">Your Information</h5>
                    <form action="actions/create-checkout-session.php" method="POST" id="stripePayForm">
                        <div class="mb-3">
                            <label for="full_name" class="form-label">Full Name</label>
                            <input type="text" name="full_name" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="text" name="phone" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Full Address</label>
                            <textarea name="address" class="form-control"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="note" class="form-label">Note (optional)</label>
                            <textarea name="note" class="form-control"></textarea>
                        </div>
                        <button type="submit" class="btn btn-cta w-100">Pay with Stripe</button>
                    </form>
                </div>
            </div>
        </div>

    </div>

    <!-- FOOTER -->
    <footer class="bg-dark text-white text-center py-4">
        <p class="mb-0">&copy; <?= date('Y') ?> GlamNail Studio. All rights reserved.</p>
        <small>Designed with <i class="bi bi-heart-fill text-danger"></i> by Team GlamNail</small>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>
</body>

</html>
