<?php
include '../includes/db.php';
$discounts = $pdo->query('SELECT * FROM discounts ORDER BY id DESC')->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Discounts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <?php include '../includes/admin_navbar.php'; ?>

    <div class="container my-5">
        <div class="bg-white p-4 rounded shadow-sm">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="text-primary">🎁 Manage Discounts</h3>
                <a href="add_discount.php" class="btn btn-success">➕ Add Discount</a>
            </div>

            <table class="table table-bordered align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Percentage</th>
                        <th>Valid From</th>
                        <th>Valid To</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($discounts as $d): ?>
                    <tr>
                        <td><?= $d['id'] ?></td>
                        <td><?= htmlspecialchars($d['title']) ?></td>
                        <td><?= $d['percentage'] ?>%</td>
                        <td><?= $d['valid_from'] ?></td>
                        <td><?= $d['valid_to'] ?></td>
                        <td>
                            <a href="edit_discount.php?id=<?= $d['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="../actions/delete_discount.php?id=<?= $d['id'] ?>" class="btn btn-sm btn-danger"
                                onclick="return confirm('Delete this discount?')">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
