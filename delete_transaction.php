<?php
require 'db.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $result = $conn->query("SELECT photo FROM transactions WHERE id=$id");
    $transaction = $result->fetch_assoc();

    if ($transaction && !empty($transaction['photo'])) {
        $photoPath = 'uploads/' . $transaction['photo'];
        if (file_exists($photoPath)) {
            unlink($photoPath);
        }
    }

    $conn->query("DELETE FROM transactions WHERE id=$id");
    header('Location: index.php');
    exit();
}
?>