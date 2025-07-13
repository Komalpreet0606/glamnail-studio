<?php
session_start();
require_once '../vendor/autoload.php';

\Stripe\Stripe::setApiKey(getenv('STRIPE_SECRET_KEY')); // ⛔ Replace this with your real Stripe Secret Key

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['customer'] = [
        'full_name' => $_POST['full_name'],
        'email' => $_POST['email'],
        'phone' => $_POST['phone'],
        'address' => $_POST['address'],
        'note' => $_POST['note'] ?? '',
    ];
}

if (!isset($_SESSION['booking'])) {
    header('Location: ../booking.php');
    exit();
}

$booking = $_SESSION['booking'];
$service_id = $booking['service_id'];
$date = $booking['appointment_date'];
$time = $booking['appointment_time'];
$discount_id = $booking['discount_id'] ?? null;

include '../includes/db.php';

// Get service price
$stmt = $pdo->prepare('SELECT title, price FROM services WHERE id = ?');
$stmt->execute([$service_id]);
$service = $stmt->fetch();
$price = $service['price'];

// Apply discount
$discount = 0;
if ($discount_id) {
    $stmt = $pdo->prepare('SELECT percentage FROM discounts WHERE id = ? AND valid_from <= CURDATE() AND valid_to >= CURDATE()');
    $stmt->execute([$discount_id]);
    $discount = $stmt->fetchColumn() ?: 0;
}

$discount_amount = $price * ($discount / 100);
$subtotal = $price - $discount_amount;
$tax = $subtotal * 0.13;
$total = $subtotal + $tax;

// Stripe requires amount in cents
$amount_in_cents = intval($total * 100);

$session = \Stripe\Checkout\Session::create([
    'payment_method_types' => ['card'],
    'line_items' => [
        [
            'price_data' => [
                'currency' => 'cad',
                'product_data' => [
                    'name' => $service['title'],
                ],
                'unit_amount' => $amount_in_cents,
            ],
            'quantity' => 1,
        ],
    ],
    'mode' => 'payment',
    'success_url' => 'https://glamnail-studio.onrender.com/success.php?session_id={CHECKOUT_SESSION_ID}',
    'cancel_url' => 'https://glamnail-studio.onrender.com/cancel.php',
]);

header('Location: ' . $session->url);
exit();
