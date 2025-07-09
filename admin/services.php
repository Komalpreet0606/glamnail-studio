<?php
include '../includes/session.php';
include '../includes/db.php';

// Ensure admin
if ($_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

// Fetch all services
$stmt = $pdo->query('SELECT * FROM services');
$services = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Services</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container py-5">
        <div class="card shadow p-4">
            <h2 class="text-center mb-4">Manage Services</h2>

            <h4>Add New Service</h4>
            <form action="../actions/add_service.php" method="POST" class="row g-3 mb-4">
                <div class="col-md-6">
                    <input type="text" name="title" class="form-control" placeholder="Service Title" required>
                </div>
                <div class="col-md-6">
                    <input type="number" step="0.01" name="price" class="form-control" placeholder="Price"
                        required>
                </div>
                <div class="col-md-12">
                    <textarea name="description" class="form-control" placeholder="Description" rows="3"></textarea>
                </div>
                <div class="col-md-12">
                    <input type="text" name="image" class="form-control" placeholder="Image URL (optional)">
                </div>
                <div class="col-md-12 d-grid">
                    <button type="submit" class="btn btn-primary">Add Service</button>
                </div>
            </form>

            <h4 class="mt-4">Existing Services</h4>
            <table class="table table-striped table-bordered mt-2">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Price ($)</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($services as $s): ?>
                    <tr>
                        <td><?= $s['id'] ?></td>
                        <td><?= $s['title'] ?></td>
                        <td>$<?= number_format($s['price'], 2) ?></td>
                        <td>
                            <a href="edit_service.php?id=<?= $s['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="../actions/delete_service.php?id=<?= $s['id'] ?>" class="btn btn-sm btn-danger"
                                onclick="return confirm('Delete this service?')">Delete</a>
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
