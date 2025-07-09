<?php
include 'includes/db.php';
$today = date('Y-m-d');
$stmt = $pdo->prepare('SELECT * FROM discounts WHERE valid_from <= ? AND valid_to >= ?');
$stmt->execute([$today, $today]);
$offers = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>GlamNail Studio - Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #fffafc;
        }

        .hero {
            background: url('images/banner3.webp') center/cover no-repeat;
            height: 85vh;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.5);
            text-align: center;
        }

        .section-title {
            color: #d63384;
            font-weight: bold;
            margin-bottom: 1rem;
        }

        .highlight {
            color: #d63384;
            font-weight: 600;
        }

        .testimonial-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            padding: 20px;
        }

        .gallery img {
            object-fit: cover;
            height: 200px;
            width: 100%;
            border-radius: 8px;
        }

        .btn-cta {
            background: linear-gradient(to right, #ff8abf, #d669b5);
            border: none;
            color: white;
        }

        .btn-cta:hover {
            background: linear-gradient(to right, #e75da8, #b158a1);
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">GlamNail Studio</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a href="index.php" class="nav-link active">Home</a></li>
                    <li class="nav-item"><a href="services.php" class="nav-link">Services</a></li>
                    <li class="nav-item"><a href="booking.php" class="nav-link">Book Now</a></li>
                    <li class="nav-item"><a href="support.php" class="nav-link">Support</a></li>
                    <li class="nav-item"><a href="auth/login.php" class="nav-link">Login</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- HERO -->
    <section class="hero">
        <h1 class="display-4 fw-bold">Your Glam Journey Begins Here 💅</h1>
        <p class="lead">Elegant nails, relaxing experience, unforgettable service.</p>
        <a href="booking.php" class="btn btn-cta btn-lg mt-3 px-4">Book Your Appointment</a>
    </section>

    <!-- OFFERS -->
    <div class="container py-5">
        <h2 class="text-center section-title">🎁 Special Offers</h2>
        <?php if (count($offers) > 0): ?>
        <div class="row justify-content-center">
            <?php foreach ($offers as $offer): ?>
            <div class="col-md-6 mb-3">
                <div class="alert alert-success shadow-sm">
                    <strong><?= $offer['title'] ?>:</strong> <?= $offer['percentage'] ?>% off — valid till
                    <strong><?= $offer['valid_to'] ?></strong>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="alert alert-info text-center">No active offers at the moment. Stay tuned!</div>
        <?php endif; ?>
    </div>

    <!-- FEATURED SERVICES -->
    <div class="container services-preview mb-5">
        <h2 class="text-center section-title">💎 Featured Services</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100">
                    <img src="images/MeniandPedi.png" class="card-img-top" alt="Mani Pedi">
                    <div class="card-body">
                        <h5 class="card-title">Manicure & Pedicure</h5>
                        <p class="card-text">A relaxing and rejuvenating treatment for hands and feet.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100">
                    <img src="images/Shellac.png" class="card-img-top" alt="Shellac">
                    <div class="card-body">
                        <h5 class="card-title">Shellac Nails</h5>
                        <p class="card-text">Glossy, long-lasting nails with UV protection and shine.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100">
                    <img src="images/Pedicure.png" class="card-img-top" alt="Nail Art">
                    <div class="card-body">
                        <h5 class="card-title">Creative Nail Art</h5>
                        <p class="card-text">Stand out with personalized nail designs & brush art.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center mt-4">
            <a href="services.php" class="btn btn-outline-primary btn-lg">View All Services</a>
        </div>
    </div>

    <!-- WHY US -->
    <div class="container my-5">
        <h2 class="text-center section-title">🌟 Why GlamNail?</h2>
        <div class="row text-center">
            <div class="col-md-4">
                <i class="bi bi-star-fill fs-2 text-warning"></i>
                <h5 class="fw-semibold mt-2">Top Rated Artists</h5>
                <p>Our team includes certified experts and stylists with over 5 years of experience.</p>
            </div>
            <div class="col-md-4">
                <i class="bi bi-heart-fill fs-2 text-danger"></i>
                <h5 class="fw-semibold mt-2">Loved by Clients</h5>
                <p>We take pride in thousands of happy customers who trust our salon service.</p>
            </div>
            <div class="col-md-4">
                <i class="bi bi-gem fs-2 text-info"></i>
                <h5 class="fw-semibold mt-2">Premium Products</h5>
                <p>We only use high-quality, skin-safe products that promote healthy nails.</p>
            </div>
        </div>
    </div>

    <!-- TESTIMONIALS -->
    <div class="container py-5">
        <h2 class="text-center section-title">💬 What Clients Say</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="testimonial-card">
                    <p>“Absolutely loved my nails! So elegant and neat. The artist was so gentle too.”</p>
                    <strong>- Priya K.</strong>
                </div>
            </div>
            <div class="col-md-4">
                <div class="testimonial-card">
                    <p>“Great vibes, clean studio, and my nail art turned out amazing. Highly recommend!”</p>
                    <strong>- Harleen M.</strong>
                </div>
            </div>
            <div class="col-md-4">
                <div class="testimonial-card">
                    <p>“I keep coming back every month. Affordable and high-class service every time.”</p>
                    <strong>- Ayesha R.</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- GALLERY -->
    <div class="container my-5">
        <h2 class="text-center section-title">📸 Our Gallery</h2>
        <div class="row gallery g-3">
            <div class="col-md-3"><img src="images/gallery1.jpg" alt="Design 1"></div>
            <div class="col-md-3"><img src="images/gallery2.jpg" alt="Design 2"></div>
            <div class="col-md-3"><img src="images/gallery3.jpg" alt="Design 3"></div>
            <div class="col-md-3"><img src="images/gallery4.jpg" alt="Design 4"></div>
        </div>
    </div>

    <!-- CTA SECTION -->
    <div class="container text-center my-5">
        <div class="p-5 bg-light rounded shadow">
            <h3 class="fw-bold">Ready to get pampered?</h3>
            <p class="lead">Schedule your next appointment with GlamNail and feel the difference!</p>
            <a href="booking.php" class="btn btn-cta btn-lg mt-2">Book Now</a>
        </div>
    </div>

    <!-- FOOTER -->
    <footer class="bg-dark text-white text-center py-4">
        <p class="mb-0">&copy; <?= date('Y') ?> GlamNail Studio. All rights reserved.</p>
        <small>Designed with 💖 by Team GlamNail</small>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
