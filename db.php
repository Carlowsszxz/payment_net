<?php
// Database connection using PDO
$dsn = 'pgsql:host=dpg-d6iga7hdrdic73d3srog-a;port=5432;dbname=transactions_ukam';
$user = 'transactions_ukam_user';
$pass = 'G4slRMjvH0Wc0CmxNbGowtSJM58ZpfkB';

try {
    $conn = new PDO($dsn, $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully!";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>