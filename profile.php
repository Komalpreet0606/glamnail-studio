<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Get user info
$stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Get latest appointment detail for address/note
$stmt = $pdo->prepare("SELECT address, note FROM appointment_details WHERE appointment_id IN (
    SELECT id FROM appointments WHERE user_id = ? 
) ORDER BY id DESC LIMIT 1");
$stmt->execute([$user_id]);
$detail = $stmt->fetch();

$profileImg = isset($_SESSION['profile_pic']) && $_SESSION['profile_pic'] != '' ? "uploads/{$_SESSION['profile_pic']}" : './images/user.png'; // Default profile image if none is set
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>My Profile - GlamNail Studio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&family=Lato&display=swap"
        rel="stylesheet">
    <style>
        body {
            background-color: #f7f8fa;
            font-family: 'Lato', sans-serif;
            color: #333;
        }

        h3 {
            font-family: 'Playfair Display', serif;
            color: #b76e79;
            font-weight: bold;
        }

        .profile-card {
            background: #fff;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .profile-card:hover {
            box-shadow: 0 16px 32px rgba(0, 0, 0, 0.2);
            transform: translateY(-5px);
        }

        .profile-img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            border: 5px solid #b76e79;
            margin-bottom: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background-color: #b76e79;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 12px;
            transition: background-color 0.3s ease;
            width: 100%;
        }

        .btn-primary:hover {
            background-color: #a05566;
        }

        .form-control {
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .section-title {
            color: #d63384;
            font-weight: bold;
            margin-top: 30px;
            margin-bottom: 15px;
        }

        .alert {
            border-radius: 8px;
            padding: 15px;
        }

        .text-muted {
            color: #6c757d;
        }

        /* Toast styling */
        .toast {
            border-radius: 8px;
        }

        .toast-body {
            font-size: 1rem;
            font-family: 'Lato', sans-serif;
        }

        @media (max-width: 576px) {
            .container {
                padding-left: 20px;
                padding-right: 20px;
            }

            .profile-card {
                padding: 20px;
            }
        }
    </style>
</head>

<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?= $_SESSION['success'] ?>
                </div>
                <?php unset($_SESSION['success']); ?>
                <?php elseif (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <?= $_SESSION['error'] ?>
                </div>
                <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <div class="profile-card">
                    <h3 class="text-center mb-4">👤 My Profile</h3>

                    <div class="text-center mb-4">
                        <img src="<?= $profileImg ?>" class="profile-img shadow" alt="Profile Picture">
                    </div>

                    <form action="actions/update_profile.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Profile Picture</label>
                            <input type="file" name="profile_picture" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" value="<?= htmlspecialchars($user['name']) ?>" class="form-control"
                                readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" value="<?= htmlspecialchars($user['email']) ?>" class="form-control"
                                readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control"
                                value="<?= htmlspecialchars($user['phone']) ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <input type="text" name="address" class="form-control"
                                value="<?= htmlspecialchars($detail['address'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Note (optional)</label>
                            <textarea name="note" class="form-control" placeholder="Any preferences or comments?"><?= htmlspecialchars($detail['note'] ?? '') ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Update Profile</button>
                    </form>

                    <div class="mt-5">
                        <h5 class="section-title">Profile Information</h5>
                        <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                        <p><strong>Phone:</strong> <?= htmlspecialchars($user['phone']) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toasts -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999;">
        <?php if (isset($_SESSION['toast_success'])): ?>
        <div class="toast align-items-center text-white bg-success border-0 show" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    <?= $_SESSION['toast_success'] ?>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
        <?php unset($_SESSION['toast_success']); ?>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toast = document.querySelector('.toast');
            if (toast) {
                new bootstrap.Toast(toast, {
                    delay: 3000
                }).show();
            }
        });
    </script>
</body>

</html>
