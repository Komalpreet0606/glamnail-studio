<?php
session_start();
include 'includes/session.php';
include 'includes/db.php';

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
    <style>
        body {
            background: #fff8fc;
            font-family: 'Segoe UI', sans-serif;
        }

        .booking-hero {
            background: linear-gradient(rgba(255, 255, 255, 0.85), rgba(255, 255, 255, 0.9)), url('images/booking_banner.jpg') center/cover no-repeat;
            padding: 80px 0;
            text-align: center;
        }

        .booking-hero h1 {
            font-size: 2.8rem;
            font-weight: 700;
            color: #d63384;
        }

        .form-section {
            max-width: 600px;
            margin: auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            padding: 30px;
        }

        .btn-appointment {
            background: linear-gradient(to right, #ff6ec4, #7873f5);
            border: none;
            color: white;
        }

        .btn-appointment:hover {
            background: linear-gradient(to right, #f347c6, #645df1);
        }

        #summary {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            font-size: 0.95rem;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">GlamNail Studio</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a href="index.php" class="nav-link">Home</a></li>
                    <li class="nav-item"><a href="services.php" class="nav-link">Services</a></li>
                    <li class="nav-item"><a href="booking.php" class="nav-link active">Book</a></li>
                    <li class="nav-item"><a href="support.php" class="nav-link">Support</a></li>
                    <li class="nav-item"><a href="auth/login.php" class="nav-link">Login</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="booking-hero">
        <div class="container">
            <h1>Schedule Your Glam Session 💅</h1>
            <p class="lead">Pick your service, select a time, and let's glam you up!</p>
        </div>
    </section>

    <div class="container mt-5">
        <div class="form-section">
            <h2 class="text-center mb-4">Book an Appointment</h2>
            <form action="actions/book_service.php" method="POST" id="bookingForm">
                <div class="mb-3">
                    <label class="form-label">Select Service</label>
                    <select name="service_id" id="serviceSelect" class="form-select" required>
                        <option value="">-- Choose a service --</option>
                        <?php foreach ($services as $s): ?>
                        <option value="<?= $s['id'] ?>" data-price="<?= $s['price'] ?>"><?= $s['title'] ?>
                            ($<?= $s['price'] ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <?php if ($offers): ?>
                <div class="mb-3">
                    <label class="form-label">Apply Discount</label>
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
                    <label class="form-label">Preferred Date</label>
                    <input type="date" name="appointment_date" class="form-control" required
                        min="<?= date('Y-m-d') ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Preferred Time</label>
                    <input type="time" name="appointment_time" class="form-control" required>
                    <small class="text-muted">Bookings only allowed between 10:00 AM and 7:00 PM</small>
                </div>

                <div id="summary" class="mb-4">
                    <strong>Summary:</strong>
                    <div>Original Price: $<span id="originalPrice">0.00</span></div>
                    <div>Discount: −$<span id="discountAmount">0.00</span></div>
                    <div>Tax (13%): $<span id="taxAmount">0.00</span></div>
                    <div><strong>Total Payable: $<span id="totalPrice">0.00</span></strong></div>
                </div>

                <button type="submit" class="btn btn-appointment w-100">Confirm Booking</button>
            </form>
        </div>
    </div>

    <footer class="bg-dark text-white text-center py-4 mt-5">
        <p class="mb-0">&copy; <?= date('Y') ?> GlamNail Studio. All rights reserved.</p>
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
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
