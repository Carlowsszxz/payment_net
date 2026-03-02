<?php
// index.php - Main page with form and transaction table
require 'db.php';
$transactions = $conn->query('SELECT * FROM transactions ORDER BY date DESC, id DESC');
$earningsPerTransaction = 20;
$totalEarnings = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Payment Tracker</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f8f9fa; margin: 0; padding: 0; }
        .container { max-width: 800px; margin: 40px auto; background: #fff; padding: 24px; border-radius: 8px; box-shadow: 0 2px 8px #0001; }
        h1 { text-align: center; color: #2c3e50; }
        form { display: flex; flex-wrap: wrap; gap: 16px; margin-bottom: 32px; }
        form input, form button { padding: 8px; font-size: 1em; }
        form input[type="date"] { min-width: 140px; }
        form input, form button { flex: 1 1 180px; }
        form button { background: #27ae60; color: #fff; border: none; border-radius: 4px; cursor: pointer; }
        form button:hover { background: #219150; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { padding: 10px; border-bottom: 1px solid #eee; text-align: left; }
        th { background: #f0f0f0; }
        .summary { font-size: 1.2em; margin: 16px 0; text-align: right; }
        @media (max-width: 600px) {
            body { font-size: 14px; }
            h1 { font-size: 1.5em; }
            .container { padding: 12px; }
            form input, form button { font-size: 1em; padding: 12px; }
            table { display: block; overflow-x: auto; white-space: nowrap; }
            th, td { white-space: nowrap; }
        }
        /* Additional styles for better usability */
        .tooltip {
            position: relative;
            display: inline-block;
            cursor: pointer;
        }

        .tooltip .tooltiptext {
            visibility: hidden;
            width: 120px;
            background-color: #555;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 5px;
            position: absolute;
            z-index: 1;
            bottom: 125%;
            left: 50%;
            margin-left: -60px;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .tooltip:hover .tooltiptext {
            visibility: visible;
            opacity: 1;
        }

        /* Drag-and-drop styles */
        .drop-zone {
            border: 2px dashed #ccc;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            color: #aaa;
            cursor: pointer;
            margin-bottom: 16px;
        }
        .drop-zone.dragover {
            border-color: #333;
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Client Payment Tracker</h1>
        <div class="summary">
            <strong>Total Earnings:</strong> ₱<?php echo $transactions->num_rows * $earningsPerTransaction; ?>
        </div>
        <form action="add_transaction.php" method="POST" enctype="multipart/form-data">
            <input type="text" name="client" placeholder="Client Name" required>
            <input type="number" name="amount" placeholder="Amount Received" min="1" required>
            <input type="date" name="date" required>
            <div class="drop-zone">
                <div class="drop-text">Drag and drop a photo or click to upload</div>
                <img id="drop-preview" src="" alt="Preview" style="display:none; max-width:100%; max-height:140px; margin-top:8px; border-radius:4px;">
            </div>
            <input type="file" name="photo" accept="image/*" style="display: none;">
            <button type="submit">Add Payment</button>
        </form>
        <table>
            <thead>
                <tr>
                    <th>Client Name</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Earnings</th>
                    <th>Photo</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($transactions->num_rows > 0): ?>
                    <?php while ($trx = $transactions->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($trx['client']); ?></td>
                            <td>₱<?php echo number_format($trx['amount'], 2); ?></td>
                            <td><?php echo htmlspecialchars($trx['date']); ?></td>
                            <td>₱<?php echo $earningsPerTransaction; ?></td>
                            <td>
                                <?php if (!empty($trx['photo'])): ?>
                                    <a href="uploads/<?php echo htmlspecialchars($trx['photo']); ?>" target="_blank">
                                        <img src="uploads/<?php echo htmlspecialchars($trx['photo']); ?>" alt="Photo" style="max-width:60px; max-height:60px; border-radius:4px;">
                                    </a>
                                <?php else: ?>
                                    <span style="color:#aaa;">No photo</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="edit_transaction.php?id=<?php echo $trx['id']; ?>" class="tooltip">Edit
                                    <span class="tooltiptext">Edit this transaction</span>
                                </a>
                                <a href="delete_transaction.php?id=<?php echo $trx['id']; ?>" class="tooltip" onclick="return confirm('Are you sure you want to delete this transaction?');">Delete
                                    <span class="tooltiptext">Delete this transaction</span>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6" style="text-align:center;">No transactions yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const dropZone = document.querySelector('.drop-zone');
        const dropText = dropZone ? dropZone.querySelector('.drop-text') : null;
        const preview = document.getElementById('drop-preview');
        const fileInput = document.querySelector('input[name="photo"]');
        let currentPreviewUrl = null;
        if (!dropZone || !fileInput) return;

        // Prevent default behavior for window (optional safety)
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            window.addEventListener(eventName, function(e) {
                e.preventDefault();
            }, false);
        });

        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('dragover');
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('dragover');
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('dragover');
            const files = e.dataTransfer.files;
            if (files && files.length) {
                fileInput.files = files;
                handleFile(files[0]);
            }
        });

        dropZone.addEventListener('click', () => {
            fileInput.click();
        });

        fileInput.addEventListener('change', () => {
            if (fileInput.files.length) {
                handleFile(fileInput.files[0]);
            }
        });

        function handleFile(file) {
            if (!file) return;
            if (file.type && file.type.startsWith('image/')) {
                if (currentPreviewUrl) URL.revokeObjectURL(currentPreviewUrl);
                currentPreviewUrl = URL.createObjectURL(file);
                preview.src = currentPreviewUrl;
                preview.style.display = 'block';
                if (dropText) dropText.style.display = 'none';
            } else {
                if (currentPreviewUrl) URL.revokeObjectURL(currentPreviewUrl);
                currentPreviewUrl = null;
                preview.src = '';
                preview.style.display = 'none';
                if (dropText) dropText.textContent = file.name || 'File selected';
            }
        }
    });
    </script>
</body>
</html>