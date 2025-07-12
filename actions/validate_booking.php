<?php
session_start();
require '../includes/jwt_config.php';
require '../vendor/autoload.php';
include '../includes/db.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

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
    $service_id = $_POST['service_id'];
    $date = $_POST['appointment_date'];
    $time = $_POST['appointment_time'];
    $discount_id = $_POST['discount_id'] ?? null;

    // All the same validations from book_service.php
    if (strtotime($date) < strtotime(date('Y-m-d'))) {
        $_SESSION['error'] = '❌ Cannot book in the past.';
        header('Location: ../booking.php');
        exit();
    }

    $hour = intval(date('H', strtotime($time)));
    if ($hour < 10 || $hour >= 19) {
        $_SESSION['error'] = '❌ Bookings allowed between 10 AM and 7 PM only.';
        header('Location: ../booking.php');
        exit();
    }

    $stmt = $pdo->prepare('SELECT COUNT(*) FROM holidays WHERE date = ?');
    $stmt->execute([$date]);
    if ($stmt->fetchColumn() > 0) {
        $_SESSION['error'] = '❌ The studio is closed on this date.';
        header('Location: ../booking.php');
        exit();
    }

    $stmt = $pdo->prepare('SELECT price FROM services WHERE id = ?');
    $stmt->execute([$service_id]);
    $base_price = $stmt->fetchColumn();
    if (!$base_price) {
        $_SESSION['error'] = '❌ Invalid service.';
        header('Location: ../booking.php');
        exit();
    }

    $stmt = $pdo->prepare('SELECT COUNT(*) FROM appointments WHERE appointment_date = ? AND appointment_time = ?');
    $stmt->execute([$date, $time]);
    if ($stmt->fetchColumn() > 0) {
        $_SESSION['error'] = '❌ Time slot already booked.';
        header('Location: ../booking.php');
        exit();
    }

    $stmt = $pdo->prepare('SELECT COUNT(*) FROM appointments WHERE user_id = ? AND appointment_date = ? AND appointment_time = ?');
    $stmt->execute([$user_id, $date, $time]);
    if ($stmt->fetchColumn() > 0) {
        $_SESSION['error'] = '❌ You already have a booking at this time.';
        header('Location: ../booking.php');
        exit();
    }

    $stmt = $pdo->prepare('SELECT COUNT(*) FROM appointments WHERE user_id = ? AND appointment_date = ?');
    $stmt->execute([$user_id, $date]);
    if ($stmt->fetchColumn() >= 1) {
        $_SESSION['error'] = '❌ You can only book 1 appointment per day.';
        header('Location: ../booking.php');
        exit();
    }

    // Passed ✅ → Save to session and go to checkout
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
