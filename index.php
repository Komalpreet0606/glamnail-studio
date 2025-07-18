<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'includes/db.php';
$today = date('Y-m-d');
$stmt = $pdo->prepare('SELECT * FROM discounts WHERE valid_from <= ? AND valid_to >= ? AND title LIKE ?');
$stmt->execute([$today, $today, '%Special%']);
$offers = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>GlamNail Studio - Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&family=Lato&display=swap"
        rel="stylesheet">
    <link rel="preload" as="image" href="images/home-banner.jpg">

    <!-- AOS Animation CSS -->
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Lato', sans-serif;
            color: #333;
            background-color: #fffafc;
        }

        h1,
        h2,
        h3,
        h4 {
            font-family: 'Playfair Display', serif;
            color: #b76e79;
        }

        .btn-primary {
            background-color: #b76e79;
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 4px;
            transition: background 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #a05566;
        }

        .section-padding {
            padding: 80px 20px;
        }

        .hero {
            background: url('images/home-banner.jpg') center/cover no-repeat;
            height: 85vh;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.5);
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: inherit;
            transform: scale(1.1);
            animation: kenburns 20s ease-in-out infinite alternate;
            z-index: 0;
            filter: brightness(0.8);
        }

        .hero>* {
            position: relative;
            z-index: 1;
        }

        @keyframes kenburns {
            0% {
                transform: scale(1.05) translate(0, 0);
            }

            100% {
                transform: scale(1.2) translate(-2%, -2%);
            }
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

        .service-card {
            transition: transform 0.4s ease, box-shadow 0.4s ease;
            border-radius: 12px;
            overflow: hidden;
        }

        .service-card:hover {
            transform: scale(1.03);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .testimonial-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.07);
            padding: 30px;
            transition: all 0.3s ease;
        }

        .gallery img {
            object-fit: cover;
            height: 200px;
            width: 100%;
            border-radius: 8px;
            transition: transform 0.4s ease, box-shadow 0.4s ease;
        }

        .gallery img:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
        }

        .cta-box {
            background: linear-gradient(to right, #ffe1ef, #fad1ea);
            border: 1px solid #f3c7de;
            box-shadow: 0 8px 24px rgba(183, 110, 121, 0.1);
        }

        .btn-cta {
            background: linear-gradient(to right, #ff8abf, #d669b5);
            border: none;
            color: white;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(214, 105, 181, 0.4);
            transition: all 0.3s ease-in-out;
        }

        .btn-cta:hover {
            background: linear-gradient(to right, #e75da8, #b158a1);
            box-shadow: 0 6px 18px rgba(214, 105, 181, 0.6);
            transform: translateY(-2px);
        }

        .text-pink {
            color: #d63384 !important;
        }

        .hero-heading {
            color: #fff;
            font-weight: 700;
            text-shadow:
                2px 2px 6px rgba(0, 0, 0, 0.6),
                0 0 12px rgba(183, 110, 121, 0.4);
            /* subtle glam glow */
            letter-spacing: 1px;
            background: linear-gradient(to right, #ffb6d2, #b76e79);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-size: 3rem;
            animation: glamPulse 3s ease-in-out infinite;
        }

        @media (max-width: 768px) {
            .hero-heading {
                font-size: 2rem;
            }
        }

        @keyframes glamPulse {
            0% {
                text-shadow: 0 0 8px rgba(214, 105, 181, 0.3);
            }

            50% {
                text-shadow: 0 0 14px rgba(214, 105, 181, 0.6);
            }

            100% {
                text-shadow: 0 0 8px rgba(214, 105, 181, 0.3);
            }
        }

        .hero-content {
            background: rgba(0, 0, 0, 0.4);
            /* dark overlay */
            padding: 40px 20px;
            border-radius: 12px;
            backdrop-filter: blur(6px);
            /* frosted glass effect */
            -webkit-backdrop-filter: blur(6px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            max-width: 90%;
        }

        .hero h1 {
            font-size: 3rem;
            color: #fff;
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.7);
            margin-bottom: 10px;
        }

        .hero p {
            color: #fff;
            font-weight: 300;
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <?php include 'includes/navbar.php'; ?>

    <!-- HERO -->
    <section class="hero">
        <div class="hero-content text-center">
            <h1 class="display-4 fw-bold">Your Glam Journey Begins Here</h1>
            <p class="lead">Elegant nails, relaxing experience, unforgettable service.</p>
            <a href="booking.php" class="btn btn-cta btn-lg mt-3 px-4">Book Your Appointment</a>
        </div>
    </section>


    <!-- OFFERS -->
    <div class="container py-5" data-aos="fade-up">
        <h2 class="text-center section-title">
            <i class="bi bi-gift-fill text-danger fs-3 me-2"></i>Special Offers
        </h2> <?php if (count($offers) > 0): ?>
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
    <div class="container services-preview mb-5" data-aos="fade-up">
        <h2 class="text-center section-title">
            <i class="bi bi-gem text-primary fs-3 me-2"></i>Featured Services
        </h2>
        <div class="row g-4">
            <div class="col-md-4" data-aos="zoom-in" data-aos-delay="100">
                <div class="card h-100 service-card">

                    <img src="images/MeniandPedi.png" class="card-img-top" alt="Mani Pedi" loading="lazy">
                    <div class="card-body">
                        <h5 class="card-title">Manicure & Pedicure</h5>
                        <p class="card-text">A relaxing and rejuvenating treatment for hands and feet.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4" data-aos="zoom-in" data-aos-delay="100">
                <div class="card h-100 service-card">
                    <img src="images/Shellac.png" class="card-img-top" alt="Shellac" loading="lazy">
                    <div class="card-body">
                        <h5 class="card-title">Shellac Nails</h5>
                        <p class="card-text">Glossy, long-lasting nails with UV protection and shine.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4" data-aos="zoom-in" data-aos-delay="100">
                <div class="card h-100 service-card">
                    <img src="images/Pedicure.png" class="card-img-top" alt="Nail Art" loading="lazy">
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
    <div class="container my-5" data-aos="fade-up">
        <h2 class="text-center section-title">
            <i class="bi bi-stars text-warning fs-3 me-2"></i>Why GlamNail?
        </h2>
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
    <div class="container py-5" data-aos="fade-up">
        <h2 class="text-center section-title">
            <i class="bi bi-chat-left-quote-fill text-secondary fs-3 me-2"></i>What Clients Say
        </h2>
        <div id="testimonialCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">

                <div class="carousel-item active">
                    <div class="testimonial-card text-center mx-auto" style="max-width: 600px;">
                        <p>“Absolutely loved my nails! So elegant and neat. The artist was so gentle too.”</p>
                        <strong>- Priya K.</strong>
                    </div>
                </div>

                <div class="carousel-item">
                    <div class="testimonial-card text-center mx-auto" style="max-width: 600px;">
                        <p>“Great vibes, clean studio, and my nail art turned out amazing. Highly recommend!”</p>
                        <strong>- Harleen M.</strong>
                    </div>
                </div>

                <div class="carousel-item">
                    <div class="testimonial-card text-center mx-auto" style="max-width: 600px;">
                        <p>“I keep coming back every month. Affordable and high-class service every time.”</p>
                        <strong>- Ayesha R.</strong>
                    </div>
                </div>

            </div>

            <!-- Carousel controls -->
            <button class="carousel-control-prev" type="button" data-bs-target="#testimonialCarousel"
                data-bs-slide="prev">
                <span class="carousel-control-prev-icon bg-dark rounded-circle" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>

            <button class="carousel-control-next" type="button" data-bs-target="#testimonialCarousel"
                data-bs-slide="next">
                <span class="carousel-control-next-icon bg-dark rounded-circle" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </div>


    <!-- GALLERY -->
    <div class="container my-5" data-aos="fade-up">
        <h2 class="text-center section-title">
            <i class="bi bi-camera-fill text-info fs-3 me-2"></i>Our Gallery
        </h2>
        <div class="row gallery g-3">
            <div class="col-md-3"><img src="images/gallery_1.jpg" alt="Design 1" loading="lazy"></div>
            <div class="col-md-3"><img src="images/gallery_2.jpg" alt="Design 2" loading="lazy"></div>
            <div class="col-md-3"><img src="images/gallery_3.jpg" alt="Design 3" loading="lazy"></div>
            <div class="col-md-3"><img src="images/gallery_4.jpg" alt="Design 4" loading="lazy"></div>
        </div>
    </div>

    <!-- CTA SECTION -->
    <div class="container text-center my-5" data-aos="zoom-in">
        <div class="p-5 cta-box rounded shadow">

            <h3 class="fw-bold">Ready to get pampered?</h3>
            <p class="lead">Schedule your next appointment with GlamNail and feel the difference!</p>
            <a href="booking.php" class="btn btn-cta btn-lg mt-2">Book Now</a>
        </div>
    </div>

    <!-- FOOTER -->
    <footer class="bg-dark text-white text-center py-4">
        <p class="mb-0">&copy; <?= date('Y') ?> GlamNail Studio. All rights reserved.</p>
        <small>Designed with <i class="bi bi-heart-fill text-danger"></i> by Team GlamNail</small>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AOS Animation JS -->
    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const carousel = document.querySelector('#testimonialCarousel');
            if (carousel) {
                new bootstrap.Carousel(carousel, {
                    interval: 5000,
                    ride: 'carousel'
                });
            }
        });
    </script>

</body>

</html>
