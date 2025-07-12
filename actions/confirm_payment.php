
<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['booking']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../booking.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$booking = $_SESSION['booking'];
$service_id = $booking['service_id'];
$date = $booking['appointment_date'];
$time = $booking['appointment_time'];
$discount_id = $booking['discount_id'] ?? null;

// Get service price
$stmt = $pdo->prepare('SELECT price FROM services WHERE id = ?');
$stmt->execute([$service_id]);
$base_price = $stmt->fetchColumn();

if (!$base_price) {
    $_SESSION['error'] = '❌ Service not found.';
    header('Location: ../checkout.php');
    exit();
}

// Discount validation
$discount = 0;
if ($discount_id) {
    $stmt = $pdo->prepare('SELECT percentage FROM discounts WHERE id = ? AND valid_from <= CURDATE() AND valid_to >= CURDATE()');
    $stmt->execute([$discount_id]);
    $discount = $stmt->fetchColumn() ?: 0;
}
$final_price = $base_price - ($base_price * $discount / 100);
$tax = $final_price * 0.13;
$total = $final_price + $tax;

// Save appointment
$stmt = $pdo->prepare('INSERT INTO appointments (user_id, service_id, appointment_date, appointment_time, discount_id, final_price)
                       VALUES (?, ?, ?, ?, ?, ?)');
$stmt->execute([$user_id, $service_id, $date, $time, $discount_id, $total]);
$appointment_id = $pdo->lastInsertId();

// Save address and details
$stmt = $pdo->prepare('INSERT INTO appointment_details (appointment_id, full_name, email, address, phone, note)
                       VALUES (?, ?, ?, ?, ?, ?)');
$stmt->execute([
    $appointment_id,
    $_POST['full_name'],
    $_POST['email'],
    $_POST['address'],
    $_POST['phone'],
    $_POST['note'] ?? null
]);

// Clear session
unset($_SESSION['booking']);

$_SESSION['success'] = "✅ Booking confirmed! Your appointment has been scheduled.";
header('Location: ../booking.php');
exit();
?>
