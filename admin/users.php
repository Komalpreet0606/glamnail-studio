<?php
include '../includes/db.php';
$users = $pdo->query('SELECT * FROM users ORDER BY id DESC')->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <?php include '../includes/admin_navbar.php'; ?>

    <div class="container">
        <div class="bg-white p-4 rounded shadow-sm">
            <h3 class="mb-4 text-primary">👥 Registered Users</h3>
            <input type="text" id="searchInput" class="form-control mb-3"
                placeholder="Search users by name or email...">

            <table class="table table-bordered align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?= $u['id'] ?></td>
                        <td><?= htmlspecialchars($u['name']) ?></td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td><?= $u['phone'] ?></td>
                        <td><span
                                class="badge bg-<?= $u['role'] === 'admin' ? 'danger' : 'secondary' ?>"><?= ucfirst($u['role']) ?></span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script>
        document.getElementById("searchInput").addEventListener("input", function() {
            const query = this.value.toLowerCase();
            const rows = document.querySelectorAll("tbody tr");

            rows.forEach(row => {
                const name = row.cells[1].textContent.toLowerCase();
                const email = row.cells[2].textContent.toLowerCase();
                row.style.display = (name.includes(query) || email.includes(query)) ? "" : "none";
            });
        });
    </script>

</body>

</html>
