<?php
session_start();
require '../includes/jwt_config.php';
require '../vendor/autoload.php';
include '../includes/db.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Auth check
if (!isset($_SESSION['jwt'])) {
    $_SESSION['error'] = '❌ Please log in to book.';
    header('Location: ../auth/login.php');
    exit();
}

try {
    $decoded = JWT::decode($_SESSION['jwt'], new Key(JWT_SECRET, 'HS256'));
    $user_id = $decoded->sub;
} catch (Exception $e) {
    $_SESSION['error'] = '❌ Authentication error: ' . $e->getMessage();
    header('Location: ../auth/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $service_id = (int) ($_POST['service_id'] ?? 0);
    $date = $_POST['appointment_date'] ?? '';
    $time = $_POST['appointment_time'] ?? '';
    $discount_id = $_POST['discount_id'] ?? null;

    $errors = [];

    // Service validation
    $stmt = $pdo->prepare('SELECT price FROM services WHERE id = ?');
    $stmt->execute([$service_id]);
    $service = $stmt->fetchColumn();
    if (!$service) {
        $errors[] = '❌ Invalid service selected.';
    }

    // Date validation
    if (empty($date)) {
        $errors[] = '❌ Appointment date is required.';
    } elseif (strtotime($date) < strtotime(date('Y-m-d'))) {
        $errors[] = '❌ Cannot book in the past.';
    } else {
        // Check holiday
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM holidays WHERE date = ?');
        $stmt->execute([$date]);
        if ($stmt->fetchColumn() > 0) {
            $errors[] = '❌ The studio is closed on this date.';
        }
    }

    // Time validation
    if (empty($time)) {
        $errors[] = '❌ Appointment time is required.';
    } else {
        $hour = intval(date('H', strtotime($time)));
        $minute = intval(date('i', strtotime($time)));
        if ($hour < 10 || $hour >= 19) {
            $errors[] = '❌ Bookings allowed between 10 AM and 7 PM only.';
        } elseif ($minute % 15 !== 0) {
            $errors[] = '❌ Please choose a time in 15-minute intervals.';
        }
    }

    // Optional discount validation
    if (!empty($discount_id)) {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM discounts WHERE id = ? AND valid_from <= ? AND valid_to >= ?');
        $stmt->execute([$discount_id, $date, $date]);
        if ($stmt->fetchColumn() == 0) {
            $errors[] = '❌ Selected discount is not valid for this date.';
        }
    }

    // Duplicate checks
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM appointments WHERE appointment_date = ? AND appointment_time = ?');
    $stmt->execute([$date, $time]);
    if ($stmt->fetchColumn() > 0) {
        $errors[] = '❌ Time slot already booked.';
    }

    $stmt = $pdo->prepare('SELECT COUNT(*) FROM appointments WHERE user_id = ? AND appointment_date = ? AND appointment_time = ?');
    $stmt->execute([$user_id, $date, $time]);
    if ($stmt->fetchColumn() > 0) {
        $errors[] = '❌ You already have a booking at this time.';
    }

    $stmt = $pdo->prepare('SELECT COUNT(*) FROM appointments WHERE user_id = ? AND appointment_date = ?');
    $stmt->execute([$user_id, $date]);
    if ($stmt->fetchColumn() >= 1) {
        $errors[] = '❌ You can only book one appointment per day.';
    }

    // Handle errors
    if (!empty($errors)) {
        $_SESSION['error'] = implode('<br>', $errors);
        header('Location: ../booking.php');
        exit();
    }

    // ✅ All good – store booking temporarily
    $_SESSION['booking'] = [
        'service_id' => $service_id,
        'appointment_date' => $date,
        'appointment_time' => $time,
        'discount_id' => $discount_id,
    ];

    header('Location: ../checkout.php');
    exit();
}
?>
