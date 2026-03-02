<?php
// db.php - Database connection
$host = 'mysql'; // Updated host for Docker compatibility
$user = 'root';
$pass = '';
$db = 'payment_tracker';
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}
?>