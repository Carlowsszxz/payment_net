<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
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

    $sql = "UPDATE transactions SET client='$client', amount=$amount, date='$date'";
    if ($photoFile) {
        $sql .= ", photo='$photoFile'";
    }
    $sql .= " WHERE id=$id";

    if ($conn->query($sql)) {
        header('Location: index.php');
        exit();
    } else {
        echo 'Error: ' . $conn->error;
    }
} else {
    $id = intval($_GET['id']);
    $result = $conn->query("SELECT * FROM transactions WHERE id=$id");
    $transaction = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Transaction</title>
</head>
<body>
    <h1>Edit Transaction</h1>
    <form action="edit_transaction.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $transaction['id']; ?>">
        <input type="text" name="client" value="<?php echo htmlspecialchars($transaction['client']); ?>" required>
        <input type="number" name="amount" value="<?php echo $transaction['amount']; ?>" required>
        <input type="date" name="date" value="<?php echo $transaction['date']; ?>" required>
        <input type="file" name="photo" accept="image/*">
        <button type="submit">Update</button>
    </form>
</body>
</html>