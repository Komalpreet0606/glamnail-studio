<?php
// includes/session.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    $base_path = dirname($_SERVER['PHP_SELF']);
    header("Location: $base_path/auth/login.php");
    exit();
}
