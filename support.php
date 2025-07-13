<?php
session_start();
include 'includes/db.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Support - GlamNail Studio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Lato&display=swap"
        rel="stylesheet">
    <style>
        body {
            background: #fff0f7;
            font-family: 'Lato', sans-serif;
        }

        .support-hero {
            background: linear-gradient(rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.95)),
                url('images/contact_banner.jpg') center/cover no-repeat;
            padding: 100px 0;
            text-align: center;
        }

        .support-hero h1 {
            font-size: 3rem;
            font-family: 'Playfair Display', serif;
            color: #b76e79;
        }

        .support-hero p {
            font-size: 1.2rem;
            color: #555;
        }

        .support-form {
            max-width: 600px;
            background: #fff;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.08);
            margin: -80px auto 40px auto;
        }

        .form-label {
            font-weight: 600;
            color: #b76e79;
        }

        .btn-submit {
            background: linear-gradient(to right, #ff90c0, #a770ef);
            border: none;
            color: white;
            font-weight: bold;
            padding: 10px 20px;
            transition: all 0.3s ease-in-out;
        }

        .btn-submit:hover {
            background: linear-gradient(to right, #f347c6, #645df1);
            transform: scale(1.02);
        }

        footer {
            margin-top: 80px;
        }
    </style>
</head>

<body>

    <?php include 'includes/navbar.php'; ?>

    <section class="support-hero">
        <div class="container">
            <h1>We're Here to Help 💬</h1>
            <p class="lead">Have a question or concern? Reach out to us anytime!</p>
        </div>
    </section>

    <div class="container">
        <div class="support-form">
            <h3 class="text-center mb-4" style="font-family: 'Playfair Display', serif;">Contact Support</h3>

            <?php if (isset($_SESSION['support_success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= $_SESSION['support_success'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['support_success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['support_error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= $_SESSION['support_error'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['support_error']); ?>
            <?php endif; ?>



            <form action="actions/submit_message.php" method="POST">
                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-chat-left-text-fill me-1"></i> Subject</label>
                    <input type="text" name="subject" class="form-control" placeholder="Subject..." required>
                </div>

                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-pencil-fill me-1"></i> Your Message</label>
                    <textarea name="content" class="form-control" placeholder="Type your message..." rows="5" required></textarea>
                </div>

                <button type="submit" class="btn btn-submit w-100">Submit Message</button>
            </form>

            <div class="text-center mt-3">
                <a href="index.php" class="text-muted"><i class="bi bi-arrow-left"></i> Back to Home</a>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white text-center py-4">
        <p class="mb-0">&copy; <?= date('Y') ?> GlamNail Studio. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
