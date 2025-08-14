<?php
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $desc = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $category = trim($_POST['category']);

    $imageName = 'default.jpg';
    if (!empty($_FILES['image']['name'])) {
        $fsDir = realpath(__DIR__ . '/../images');
        if ($fsDir === false) {
            $fsDir = __DIR__ . '/../images';
        }
        if (!is_dir($fsDir)) {
            @mkdir($fsDir, 0775, true);
        }

        $imageFile = $_FILES['image'];
        $ext = strtolower(pathinfo($imageFile['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($ext, $allowed, true) && $imageFile['size'] <= 2 * 1024 * 1024) {
            $base = preg_replace('/[^A-Za-z0-9_.-]/', '_', pathinfo($imageFile['name'], PATHINFO_FILENAME));
            $base = preg_replace('/_+/', '_', $base);
            $imageName = time() . '_' . $base . '.' . $ext;

            $dest = rtrim($fsDir, '/\\') . DIRECTORY_SEPARATOR . $imageName;
            if (!move_uploaded_file($imageFile['tmp_name'], $dest)) {
                $imageName = 'default.jpg';
            }
        }
    }

    $stmt = $pdo->prepare('INSERT INTO services (title, description, price, category, image) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$title, $desc, $price, $category, $imageName]);

    header('Location: admin_services.php');
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add Service - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <?php include '../includes/admin_navbar.php'; ?>

    <div class="container my-5">
        <div class="bg-white p-4 rounded shadow-sm">
            <h3 class="mb-4 text-primary">➕ Add New Service</h3>
            <form method="POST" action="add_service.php" enctype="multipart/form-data">
                <div class="mb-3">
                    <label>Title</label>
                    <input type="text" name="title" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Description</label>
                    <textarea name="description" class="form-control" required></textarea>
                </div>

                <div class="mb-3">
                    <label>Price ($)</label>
                    <input type="number" name="price" class="form-control" step="0.01" required>
                </div>

                <div class="mb-3">
                    <label>Category</label>
                    <input type="text" name="category" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Upload Image</label>
                    <input type="file" name="image" id="imageInput" class="form-control"
                        accept=".jpg,.jpeg,.png,.webp">
                    <div class="mt-3">
                        <img id="preview" src="#" alt="Image Preview" class="img-thumbnail d-none"
                            style="max-height: 150px;">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Save Service</button>
                <a href="admin_services.php" class="btn btn-secondary">Cancel</a>
            </form>

        </div>
    </div>
    <script>
        document.getElementById('imageInput').addEventListener('change', function(e) {
            const preview = document.getElementById('preview');
            const file = e.target.files[0];

            if (file) {
                preview.src = URL.createObjectURL(file);
                preview.classList.remove('d-none');
            } else {
                preview.src = '';
                preview.classList.add('d-none');
            }
        });
    </script>

</body>

</html>
