<?php
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $percentage = intval($_POST['percentage']);
    $from = $_POST['valid_from'];
    $to = $_POST['valid_to'];

    $stmt = $pdo->prepare('INSERT INTO discounts (title, percentage, valid_from, valid_to) VALUES (?, ?, ?, ?)');
    $stmt->execute([$title, $percentage, $from, $to]);

    header('Location: discounts.php');
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Add Discount</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <?php include '../includes/admin_navbar.php'; ?>
    <div class="container my-5">
        <div class="bg-white p-4 rounded shadow-sm">
            <h3 class="mb-4 text-primary">➕ Add Discount</h3>
            <form method="POST">
                <div class="mb-3">
                    <label>Title</label>
                    <input type="text" name="title" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Percentage (%)</label>
                    <input type="number" name="percentage" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Valid From</label>
                    <input type="date" name="valid_from" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Valid To</label>
                    <input type="date" name="valid_to" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-success">Save</button>
                <a href="discounts.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</body>

</html>
