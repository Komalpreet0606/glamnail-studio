<?php
include '../includes/db.php';
$services = $pdo->query('SELECT * FROM services ORDER BY id DESC')->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin - Manage Services</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
        }

        .admin-wrapper {
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .admin-header h2 {
            margin: 0;
        }

        .thumb-img {
            height: 50px;
            border-radius: 6px;
            object-fit: cover;
        }
    </style>
</head>

<body>
    <?php include '../includes/admin_navbar.php'; ?>

    <div class="container my-5">
        <div class="admin-wrapper">
            <div class="admin-header mb-4">
                <h2 class="text-primary">📋 Manage Services</h2>
                <a href="add_service.php" class="btn btn-success">➕ Add New Service</a>
            </div>

            <?php if (count($services) > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Price ($)</th>
                            <th>Image</th>
                            <th style="width: 160px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($services as $s): ?>
                        <tr>
                            <td><?= $s['id'] ?></td>
                            <td><?= htmlspecialchars($s['title']) ?></td>
                            <td><?= htmlspecialchars($s['category']) ?></td>
                            <td><?= number_format($s['price'], 2) ?></td>
                            <td>
                                <?php
                                $imgSrc = $s['image'];
                                // If not full URL, assume it's a local file
                                if (!preg_match('/^https?:\/\//', $imgSrc)) {
                                    $imgSrc = '../images/' . $imgSrc;
                                }
                                ?>
                                <img src="<?= $imgSrc ?>" alt="Service" class="thumb-img" loading="lazy">
                            </td>

                            <td>
                                <a href="edit_service.php?id=<?= $s['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                <a href="../actions/delete_service.php?id=<?= $s['id'] ?>" class="btn btn-sm btn-danger"
                                    onclick="return confirm('Are you sure you want to delete this service?')">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="alert alert-info text-center">No services found. Click "Add New Service" to begin.</div>
            <?php endif; ?>
        </div>
    </div>

</body>

</html>
