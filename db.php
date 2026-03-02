<?php
// db.php - Database connection
$host = 'dpg-d6iga7hdrdic73d3srog-a'; // Render PostgreSQL hostname
$port = 5432; // PostgreSQL port
$user = ''; // No username set
$pass = ''; // No password set
$db = 'transactions_ukam'; // Render PostgreSQL database name

$conn = new mysqli($host, $user, $pass, $db, $port); // Updated connection with port
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}
?>