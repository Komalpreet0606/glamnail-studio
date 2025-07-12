
<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../dashboard.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$appointment_id = $_POST['appointment_id'] ?? null;

if (!$appointment_id) {
    $_SESSION['error'] = "Invalid appointment.";
    header('Location: ../dashboard.php');
    exit();
}

// Ensure the appointment belongs to the user and is not already cancelled or past
$stmt = $pdo->prepare("SELECT * FROM appointments WHERE id = ? AND user_id = ?");
$stmt->execute([$appointment_id, $user_id]);
$appointment = $stmt->fetch();

if (!$appointment) {
    $_SESSION['error'] = "Appointment not found.";
} elseif ($appointment['status'] === 'cancelled') {
    $_SESSION['error'] = "Appointment already cancelled.";
} elseif ($appointment['appointment_date'] < date('Y-m-d')) {
    $_SESSION['error'] = "Cannot cancel past appointments.";
} else {
    $update = $pdo->prepare("UPDATE appointments SET status = 'cancelled' WHERE id = ?");
    $update->execute([$appointment_id]);
    $_SESSION['success'] = "Appointment cancelled successfully.";
}

header('Location: ../dashboard.php');
exit();
?>
