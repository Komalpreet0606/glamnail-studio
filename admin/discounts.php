<?php
include '../includes/session.php';
include '../includes/db.php';

// Redirect if not admin
if ($_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

// Fetch all discounts
$stmt = $pdo->query('SELECT * FROM discounts ORDER BY valid_from DESC');
$discounts = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Discounts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container py-5">
        <div class="card shadow p-4">
            <h2 class="mb-4 text-center">Manage Discount Offers</h2>

            <h4>Add New Discount</h4>
            <form action="../actions/add_discount.php" method="POST" class="row g-3 mb-4">
                <div class="col-md-6">
                    <input type="text" name="title" class="form-control" placeholder="Discount Title" required>
                </div>
                <div class="col-md-3">
                    <input type="number" name="percentage" class="form-control" placeholder="Percentage (%)" required>
                </div>
                <div class="col-md-3 d-grid">
                    <button type="submit" class="btn btn-primary">Add Offer</button>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Valid From:</label>
                    <input type="date" name="valid_from" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Valid To:</label>
                    <input type="date" name="valid_to" class="form-control" required>
                </div>
            </form>

            <h4>Current Discounts</h4>
            <table class="table table-striped table-bordered mt-3">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>% Off</th>
                        <th>Valid From</th>
                        <th>Valid To</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($discounts as $d): ?>
                    <tr>
                        <td><?= $d['id'] ?></td>
                        <td><?= $d['title'] ?></td>
                        <td><?= $d['percentage'] ?>%</td>
                        <td><?= $d['valid_from'] ?></td>
                        <td><?= $d['valid_to'] ?></td>
                        <td>
                            <a href="../actions/delete_discount.php?id=<?= $d['id'] ?>" class="btn btn-sm btn-danger"
                                onclick="return confirm('Delete this offer?')">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="text-center mt-3">
                <a href="admin.php" class="btn btn-secondary">← Back to Dashboard</a>
            </div>
        </div>
    </div>

</body>

</html>
