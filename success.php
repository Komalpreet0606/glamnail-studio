<?php
session_start();
include 'includes/db.php';

require_once 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__, '.env');
$dotenv->load();

\Stripe\Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);
if (!isset($_GET['session_id']) || !isset($_SESSION['booking']) || !isset($_SESSION['customer'])) {
    $_SESSION['error'] = 'Something went wrong. Booking not confirmed.';
    header('Location: booking.php');
    exit();
}

$session_id = $_GET['session_id'];
$booking = $_SESSION['booking'];
$customer = $_SESSION['customer'];

try {
    // Retrieve Stripe Checkout Session and PaymentIntent
    $session = \Stripe\Checkout\Session::retrieve($session_id);
    $paymentIntent = \Stripe\PaymentIntent::retrieve($session->payment_intent);

    // Validate payment status
    if ($paymentIntent->status !== 'succeeded') {
        $_SESSION['error'] = 'Payment was not successful.';
        header('Location: booking.php');
        exit();
    }

    // Extract Stripe payment details
    $stripe_payment_intent = $paymentIntent->id;
    $amount_paid = $paymentIntent->amount_received / 100;
    $currency = strtoupper($paymentIntent->currency);
    $payment_status = $paymentIntent->status;
    $method_type = $paymentIntent->payment_method_types[0];
} catch (Exception $e) {
    $_SESSION['error'] = 'Unable to verify payment: ' . $e->getMessage();
    header('Location: booking.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$service_id = $booking['service_id'];
$date = $booking['appointment_date'];
$time = $booking['appointment_time'];
$discount_id = $booking['discount_id'] ?? null;

// Get service price
$stmt = $pdo->prepare('SELECT price FROM services WHERE id = ?');
$stmt->execute([$service_id]);
$base_price = $stmt->fetchColumn();

$discount = 0;
if ($discount_id) {
    $stmt = $pdo->prepare('SELECT percentage FROM discounts WHERE id = ? AND valid_from <= CURDATE() AND valid_to >= CURDATE()');
    $stmt->execute([$discount_id]);
    $discount = $stmt->fetchColumn() ?: 0;
}

$final_price = $base_price - ($base_price * $discount) / 100;
$tax = $final_price * 0.13;
$total = $final_price + $tax;

// Save appointment
$stmt = $pdo->prepare('INSERT INTO appointments (user_id, service_id, appointment_date, appointment_time, status, discount_id, final_price)
                       VALUES (?, ?, ?, ?, ?, ?, ?)');
$stmt->execute([$user_id, $service_id, $date, $time, 'confirmed', $discount_id, $total]);

$appointment_id = $pdo->lastInsertId();

// Save customer details
$stmt = $pdo->prepare('INSERT INTO appointment_details (appointment_id, full_name, email, address, phone, note)
                       VALUES (?, ?, ?, ?, ?, ?)');
$stmt->execute([$appointment_id, $customer['full_name'], $customer['email'], $customer['address'], $customer['phone'], $customer['note']]);

// Save Stripe payment details
$stmt = $pdo->prepare('INSERT INTO payments (appointment_id, stripe_payment_intent, amount, currency, payment_status, method_type)
                       VALUES (?, ?, ?, ?, ?, ?)');
$stmt->execute([$appointment_id, $stripe_payment_intent, $amount_paid, $currency, $payment_status, $method_type]);

// Clear session
unset($_SESSION['booking']);
unset($_SESSION['customer']);

$_SESSION['success'] = '✅ Your booking and payment have been confirmed!';
header('Location: booking.php');
exit();
?>
