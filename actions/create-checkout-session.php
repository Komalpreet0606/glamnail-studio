<?php
session_start();
require_once '../vendor/autoload.php';

$root = dirname(__DIR__);
if (is_file($root . '/.env')) {
    // Local/dev: load from .env if it exists
    Dotenv\Dotenv::createImmutable($root)->safeLoad(); // ✅ no exception if missing
}

// Read secrets from platform env (Render/Railway)
// Load API key
$secretKey = $_ENV['STRIPE_SECRET_KEY'] ?? (getenv('STRIPE_SECRET_KEY') ?? '');
if (!$secretKey) {
    http_response_code(500);
    exit('Missing STRIPE_SECRET_KEY in environment');
}

// Set the API key for all future Stripe calls
\Stripe\Stripe::setApiKey($secretKey);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    function clean($input)
    {
        return htmlspecialchars(trim($input));
    }

    $fullName = clean($_POST['full_name'] ?? '');
    $email = clean($_POST['email'] ?? '');
    $phone = clean($_POST['phone'] ?? '');
    $address = clean($_POST['address'] ?? '');
    $note = clean($_POST['note'] ?? '');

    // Basic validations
    if (empty($fullName) || strlen($fullName) < 3 || strlen($fullName) > 100 || !filter_var($email, FILTER_VALIDATE_EMAIL) || empty($phone) || !preg_match('/^[0-9\-\+\s\(\)]+$/', $phone) || empty($address) || strlen($address) < 10) {
        $_SESSION['error'] = '❌ Please fill all fields correctly.';
        header('Location: ../checkout.php');
        exit();
    }

    // Save validated inputs
    $_SESSION['customer'] = [
        'full_name' => $fullName,
        'email' => $email,
        'phone' => $phone,
        'address' => $address,
        'note' => $note,
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
