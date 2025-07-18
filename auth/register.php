<?php include '../includes/db.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Register - GlamNail Studio</title>
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- AOS Animation CSS -->
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&family=Lato&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Lato', sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }

        h2 {
            font-family: 'Playfair Display', serif;
            color: #b76e79;
        }

        .register-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: url('../images/home-banner.jpg') center/cover no-repeat;
        }


        .card {
            background: rgba(255, 255, 255, 0.2);
            /* soft white transparency */
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-radius: 16px;
            padding: 40px 30px;
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.25);
            border: 1px solid rgba(255, 255, 255, 0.25);
            width: 100%;
            max-width: 450px;
            z-index: 2;
        }


        .card:hover {
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        .card-title {
            font-size: 1.8rem;
            text-align: center;
            color: #b76e79;
            font-weight: bold;
            margin-bottom: 30px;
        }

        .form-label {
            font-weight: 600;
        }

        .form-control {
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .btn-success {
            background-color: #b76e79;
            color: white;
            border-radius: 8px;
            border: none;
            padding: 12px;
            transition: background-color 0.3s;
        }

        .btn-success:hover {
            background-color: #a05566;
        }

        .text-center a {
            color: #b76e79;
            font-weight: 600;
        }

        .text-center a:hover {
            text-decoration: underline;
        }

        .logo {
            max-height: 80px;
            margin-bottom: 20px;
        }

        /* Mobile responsive */
        @media (max-width: 576px) {
            .register-container {
                padding: 20px;
            }
        }
    </style>
</head>

<body>

    <!-- REGISTER FORM SECTION -->
    <div class="register-container">
        <div class="card" data-aos="fade-up" data-aos-delay="300">
            <div class="text-center mb-4">
                <a href="../index.php">
                    <img src="../images/logo.jpg" alt="GlamNail Studio Logo" class="logo">
                </a>
            </div>

            <h2 class="card-title">Sign Up to GlamNail Studio</h2>

            <form action="../actions/register_process.php" method="POST">
                <div class="mb-3">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" name="name" id="name" class="form-control"
                        placeholder="Enter your full name" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email" class="form-control"
                        placeholder="Enter your email" required>
                </div>

                <div class="mb-3">
                    <label for="phone" class="form-label">Phone</label>
                    <input type="text" name="phone" id="phone" class="form-control"
                        placeholder="Enter your phone number" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" id="password" class="form-control"
                        placeholder="Enter a password" required>
                </div>

                <button type="submit" class="btn btn-success w-100">Sign Up</button>
            </form>

            <p class="text-center mt-3">
                Already have an account? <a href="login.php">Login</a>
            </p>
        </div>
    </div>

    <!-- AOS Animation JS -->
    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>

</body>

</html>
