<?php
session_start();

require 'includes/jwt_config.php';
require 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

if (!isset($_SESSION['jwt'])) {
    header('Location: auth/login.php?error=unauthorized');
    exit();
}

try {
    $decoded = JWT::decode($_SESSION['jwt'], new Key(JWT_SECRET, 'HS256'));
    $userId = $decoded->sub;
    $userEmail = $decoded->email;
    $userName = $_SESSION['name'] ?? 'User'; // fallback
} catch (Exception $e) {
    echo '<h3>Unauthorized: ' . $e->getMessage() . '</h3>';
    exit();
}

include 'includes/session.php';
include 'includes/db.php';
$preselectedServiceId = isset($_GET['service_id']) ? (int) $_GET['service_id'] : null;

$stmt = $pdo->query('SELECT * FROM services');
$services = $stmt->fetchAll();

$today = date('Y-m-d');
$stmt2 = $pdo->prepare('SELECT * FROM discounts WHERE valid_from <= ? AND valid_to >= ?');
$stmt2->execute([$today, $today]);
$offers = $stmt2->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Book Appointment - GlamNail Studio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Lato&display=swap"
        rel="stylesheet">

    <style>
        body {
            background: #fff0f7;
            font-family: 'Lato', sans-serif;
        }

        .booking-hero {
            background: linear-gradient(rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.9)),
                url('images/booking_banner.jpg') center/cover no-repeat;
            padding: 100px 0;
            text-align: center;
        }

        .booking-hero h1 {
            font-size: 3rem;
            font-family: 'Playfair Display', serif;
            color: #b76e79;
        }

        .booking-hero p {
            font-size: 1.2rem;
            color: #555;
        }

        .form-section {
            max-width: 650px;
            margin: auto;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.06);
            padding: 40px;
            margin-top: -60px;
        }

        .form-label {
            font-weight: 600;
            color: #b76e79;
        }

        .btn-appointment {
            background: linear-gradient(to right, #ff90c0, #a770ef);
            border: none;
            color: white;
            padding: 12px 24px;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .btn-appointment:hover {
            background: linear-gradient(to right, #f347c6, #645df1);
            transform: scale(1.02);
        }

        #summary {
            background: #f9f3f8;
            border-radius: 12px;
            padding: 20px;
            border: 1px dashed #d69ac8;
            font-size: 0.95rem;
            line-height: 1.6;
            color: #444;
        }

        .alert-light {
            background-color: #fff5fa;
            border-left: 5px solid #f78abb;
        }

        footer {
            margin-top: 100px;
        }
    </style>

</head>

<body>


    <?php include 'includes/navbar.php'; ?>


    <section class="booking-hero">
        <div class="container">
            <h1>Schedule Your Glam Session</h1>
            <p class="lead">Pick your service, select a time, and let's glam you up!</p>
        </div>
    </section>

    <div class="container mt-5">
        <div class="form-section">
            <h2 class="text-center mb-4" style="color:#b76e79; font-family:'Playfair Display',serif;">Book Your Glam
                Appointment</h2>
            <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= $_SESSION['error'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['error']); endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= $_SESSION['success'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['success']); endif; ?>

            <form action="actions/validate_booking.php" method="POST">
                <div class="alert alert-light">
                    Booking as: <strong><?= htmlspecialchars($userName) ?></strong>
                    (<?= htmlspecialchars($userEmail) ?>)
                </div>

                <input type="hidden" name="user_id" value="<?= $userId ?>">

                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-stars"></i> Select Service</label>
                    <select name="service_id" id="serviceSelect" class="form-select">
                        <option value="">-- Choose a service --</option>
                        <?php foreach ($services as $s): ?>
                        <option value="<?= $s['id'] ?>" data-price="<?= $s['price'] ?>"
                            <?= $preselectedServiceId && $preselectedServiceId == $s['id'] ? 'selected' : '' ?>>
                            <?= $s['title'] ?> ($<?= $s['price'] ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <?php if ($offers): ?>
                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-tag-fill"></i> Apply Discount</label>
                    <select name="discount_id" id="discountSelect" class="form-select">
                        <option value="">-- Select Offer --</option>
                        <?php foreach ($offers as $o): ?>
                        <option value="<?= $o['id'] ?>" data-percentage="<?= $o['percentage'] ?>">
                            <?= $o['title'] ?> (<?= $o['percentage'] ?>% off)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>

                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-calendar-event"></i> Preferred Date</label>
                    <input type="date" name="appointment_date" class="form-control" min="<?= date('Y-m-d') ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-clock-fill"></i> Preferred Time</label>
                    <input type="time" name="appointment_time" class="form-control">
                    <small class="text-muted">Bookings only allowed between 10:00 AM and 7:00 PM</small>
                </div>

                <div id="summary" class="mb-4">
                    <strong>Summary:</strong>
                    <div>Original Price: $<span id="originalPrice">0.00</span></div>
                    <div>Discount: −$<span id="discountAmount">0.00</span></div>
                    <div>Tax (13%): $<span id="taxAmount">0.00</span></div>
                    <div><strong>Total Payable: $<span id="totalPrice">0.00</span></strong></div>
                </div>

                <button id="submitBtn" type="submit"
                    class="btn btn-appointment w-100 d-flex justify-content-center align-items-center gap-2">
                    <span class="spinner-border spinner-border-sm d-none" id="spinner" role="status"
                        aria-hidden="true"></span>
                    <span id="btnText">Confirm Booking</span>
                </button>
            </form>
        </div>
    </div>

    <!-- FOOTER -->
    <footer class="bg-dark text-white text-center py-4">
        <p class="mb-0">&copy; <?= date('Y') ?> GlamNail Studio. All rights reserved.</p>
        <small>Designed with <i class="bi bi-heart-fill text-danger"></i> by Team GlamNail</small>
    </footer>

    <script>
        const serviceSelect = document.getElementById('serviceSelect');
        const discountSelect = document.getElementById('discountSelect');
        const originalPriceEl = document.getElementById('originalPrice');
        const discountAmountEl = document.getElementById('discountAmount');
        const taxAmountEl = document.getElementById('taxAmount');
        const totalPriceEl = document.getElementById('totalPrice');

        function calculateTotal() {
            const serviceOption = serviceSelect.options[serviceSelect.selectedIndex];
            const discountOption = discountSelect ? discountSelect.options[discountSelect.selectedIndex] : null;

            const basePrice = parseFloat(serviceOption?.dataset.price || 0);
            const discountPercent = discountOption ? parseFloat(discountOption.dataset.percentage || 0) : 0;

            const discountAmount = basePrice * (discountPercent / 100);
            const discountedPrice = basePrice - discountAmount;
            const tax = discountedPrice * 0.13;
            const total = discountedPrice + tax;

            originalPriceEl.textContent = basePrice.toFixed(2);
            discountAmountEl.textContent = discountAmount.toFixed(2);
            taxAmountEl.textContent = tax.toFixed(2);
            totalPriceEl.textContent = total.toFixed(2);
        }

        serviceSelect.addEventListener('change', calculateTotal);
        if (discountSelect) discountSelect.addEventListener('change', calculateTotal);
        calculateTotal();
    </script>
    <script>
        const form = document.querySelector('form');
        const submitBtn = document.getElementById('submitBtn');
        const spinner = document.getElementById('spinner');
        const btnText = document.getElementById('btnText');

        form.addEventListener('submit', function() {
            spinner.classList.remove('d-none');
            btnText.textContent = "Booking...";
            submitBtn.disabled = true;
        });
    </script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
