<?php
// includes/db.php

$host = 'localhost';
$dbname = 'glamnailstudio';
$username = 'root'; // Change if using live host
$password = ''; // Set password if needed

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Enable exceptions
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Database Connection Failed: ' . $e->getMessage());
}
?>
