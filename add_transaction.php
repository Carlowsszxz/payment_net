<?php
// add_transaction.php - Handles form submission and inserts data into the database
require 'db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client = $conn->real_escape_string($_POST['client']);
    $amount = floatval($_POST['amount']);
    $date = $conn->real_escape_string($_POST['date']);

    $photoFile = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $uploadsDir = 'uploads/';
        if (!is_dir($uploadsDir)) {
            mkdir($uploadsDir, 0777, true);
        }

        $tmpName = $_FILES['photo']['tmp_name'];
        $originalName = basename($_FILES['photo']['name']);
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($ext, $allowed)) {
            $photoFile = uniqid('photo_', true) . '.' . $ext;
            move_uploaded_file($tmpName, $uploadsDir . $photoFile);
        }
    }

    $sql = "INSERT INTO transactions (client, amount, date, photo) VALUES ('$client', $amount, '$date', " . ($photoFile ? "'$photoFile'" : "NULL") . ")";

    if ($conn->query($sql)) {
        header('Location: index.php');
        exit();
    } else {
        echo 'Error: ' . $conn->error;
    }
}
?>