<?php
include 'includes/session.php';
include 'includes/db.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Support - GlamNail Studio</title>
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card shadow p-4 w-100" style="max-width: 500px;">
            <h2 class="text-center mb-4">Need Help? Contact Us</h2>

            <form action="actions/submit_message.php" method="POST">
                <div class="mb-3">
                    <label class="form-label">Subject</label>
                    <input type="text" name="subject" class="form-control" placeholder="Subject" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Your Message</label>
                    <textarea name="content" class="form-control" placeholder="Type your message..." rows="5" required></textarea>
                </div>

                <button type="submit" class="btn btn-primary w-100">Submit</button>
            </form>

            <p class="text-center mt-3">
                <a href="index.php">← Back to Home</a>
            </p>
        </div>
    </div>

</body>

</html>
