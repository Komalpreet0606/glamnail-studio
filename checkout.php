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
    header('Location: checkout.php'); // Refresh to GET mode to prevent resubmit
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
<html>

<head>
    <title>Checkout - GlamNail Studio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Checkout</h2>

        <div class="row">
            <div class="col-md-6 offset-md-3">
                <h5>Appointment Summary</h5>
                <ul class="list-group mb-4">
                    <li class="list-group-item"><strong>Service:</strong> <?= htmlspecialchars($service['title']) ?></li>
                    <li class="list-group-item"><strong>Date:</strong> <?= htmlspecialchars($date) ?></li>
                    <li class="list-group-item"><strong>Time:</strong> <?= htmlspecialchars($time) ?></li>
                    <li class="list-group-item"><strong>Price:</strong> $<?= number_format($price, 2) ?></li>
                    <li class="list-group-item"><strong>Discount:</strong> <?= $discount ?>%</li>
                    <li class="list-group-item"><strong>Tax (13%):</strong> $<?= number_format($tax, 2) ?></li>
                    <li class="list-group-item"><strong>Total:</strong>
                        <strong>$<?= number_format($total, 2) ?></strong>
                    </li>
                </ul>

                <form action="actions/create-checkout-session.php" method="POST" id="stripePayForm">
                    <div class="mb-3">
                        <label for="full_name" class="form-label">Full Name</label>
                        <input type="text" name="full_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="text" name="phone" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Full Address</label>
                        <textarea name="address" class="form-control" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="note" class="form-label">Note (optional)</label>
                        <textarea name="note" class="form-control"></textarea>
                    </div>

                    <!-- 🔐 Stripe Button -->
                    <button type="submit" class="btn btn-success w-100">Pay with Stripe</button>
                </form>

            </div>
        </div>
    </div>
</body>

</html>
