<?php
include '../includes/session.php';
include '../includes/db.php';

$id = $_GET['id'];
$stmt = $pdo->prepare('SELECT * FROM services WHERE id = ?');
$stmt->execute([$id]);
$service = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Service</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card shadow p-4 w-100" style="max-width: 600px;">
            <h2 class="text-center mb-4">Edit Service</h2>

            <form action="../actions/update_service.php" method="POST">
                <input type="hidden" name="id" value="<?= $service['id'] ?>">

                <div class="mb-3">
                    <label class="form-label">Service Title</label>
                    <input type="text" name="title" class="form-control" value="<?= $service['title'] ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3"><?= $service['description'] ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Price ($)</label>
                    <input type="number" step="0.01" name="price" class="form-control"
                        value="<?= $service['price'] ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Image Filename</label>
                    <input type="text" name="image" class="form-control" value="<?= $service['image'] ?>">
                </div>

                <button type="submit" class="btn btn-primary w-100">Update Service</button>
            </form>

            <div class="text-center mt-3">
                <a href="services.php" class="btn btn-secondary">← Back to Services</a>
            </div>
        </div>
    </div>
</body>

</html>
