<?php
session_start();
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $service_id = $_POST['service_id'];
    $date = $_POST['appointment_date'];
    $time = $_POST['appointment_time'];
    $discount_id = $_POST['discount_id'] ?? null;

    // Validate past date
    if (strtotime($date) < strtotime(date('Y-m-d'))) {
        exit('❌ Cannot book an appointment in the past.');
    }

    // Validate working hours (10AM–7PM)
    $hour = intval(date('H', strtotime($time)));
    if ($hour < 10 || $hour >= 19) {
        exit('❌ Appointments must be between 10 AM and 7 PM.');
    }

    // Check holidays
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM holidays WHERE date = ?');
    $stmt->execute([$date]);
    if ($stmt->fetchColumn() > 0) {
        exit('❌ The studio is closed on this date.');
    }

    // Validate service
    $stmt = $pdo->prepare('SELECT price FROM services WHERE id = ?');
    $stmt->execute([$service_id]);
    $base_price = $stmt->fetchColumn();
    if (!$base_price) {
        exit('❌ Invalid service selected.');
    }

    // Check global time slot conflict
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM appointments WHERE appointment_date = ? AND appointment_time = ?');
    $stmt->execute([$date, $time]);
    if ($stmt->fetchColumn() > 0) {
        exit('❌ This time slot is already booked. Please choose another.');
    }

    // Check user personal clash
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM appointments WHERE user_id = ? AND appointment_date = ? AND appointment_time = ?');
    $stmt->execute([$user_id, $date, $time]);
    if ($stmt->fetchColumn() > 0) {
        exit('❌ You already have a booking at this time.');
    }

    // Max 2 bookings per day per user
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM appointments WHERE user_id = ? AND appointment_date = ?');
    $stmt->execute([$user_id, $date]);
    if ($stmt->fetchColumn() >= 2) {
        exit('❌ You can only book 2 appointments per day.');
    }

    // Discount checks
    if ($discount_id) {
        // Already used?
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM appointments WHERE user_id = ? AND discount_id = ?');
        $stmt->execute([$user_id, $discount_id]);
        if ($stmt->fetchColumn() > 0) {
            $discount_id = null;
        }

        // Global limit
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM appointments WHERE discount_id = ?');
        $stmt->execute([$discount_id]);
        if ($stmt->fetchColumn() >= 100) {
            $discount_id = null;
        }
    }

    // Calculate price
    $final_price = $base_price;
    $discount = 0;

    if ($discount_id) {
        $stmt = $pdo->prepare('SELECT percentage FROM discounts WHERE id = ? AND valid_from <= CURDATE() AND valid_to >= CURDATE()');
        $stmt->execute([$discount_id]);
        $discount = $stmt->fetchColumn();
        if ($discount) {
            $final_price -= $final_price * ($discount / 100);
        } else {
            $discount_id = null;
        }
    }

    $tax_rate = 0.13;
    $tax_amount = $final_price * $tax_rate;
    $total_payable = $final_price + $tax_amount;

    // Save booking
    $stmt = $pdo->prepare('INSERT INTO appointments (user_id, service_id, appointment_date, appointment_time, discount_id, final_price) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->execute([$user_id, $service_id, $date, $time, $discount_id, $total_payable]);

    echo "✅ Booking successful! Final Price (incl. tax): $" . number_format($total_payable, 2) . " <br><a href='../index.php'>Go back</a>";
}
?>
