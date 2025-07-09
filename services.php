<?php
include 'includes/db.php';
$stmt = $pdo->query('SELECT * FROM services');
$services = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Services - GlamNail Studio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: #f9f9fb;
            font-family: 'Segoe UI', sans-serif;
        }

        .hero {
            background: linear-gradient(rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.95)), url('images/banner1.webp') center/cover no-repeat;
            padding: 100px 0;
            text-align: center;
        }

        .hero h1 {
            font-size: 3rem;
            font-weight: 700;
            color: #d63384;
        }

        .filter-bar {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            padding: 20px;
            margin-top: -40px;
            z-index: 2;
            position: relative;
        }

        .service-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: 0.3s ease;
        }

        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.1);
        }

        .service-card img {
            height: 200px;
            object-fit: cover;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
        }

        .badge-price {
            background-color: #d63384;
            color: white;
            padding: 5px 12px;
            border-radius: 30px;
            font-size: 0.85rem;
        }

        .btn-book {
            background: linear-gradient(to right, #ff6ec4, #7873f5);
            border: none;
            color: white;
        }

        .btn-book:hover {
            background: linear-gradient(to right, #f347c6, #645df1);
        }

        .section-description {
            max-width: 700px;
            margin: 0 auto;
            font-size: 1.1rem;
            color: #666;
        }

        .testimonial-card,
        .faqs,
        .cta-banner {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            padding: 30px;
            margin-top: 60px;
        }
    </style>
</head>

<body>
    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">GlamNail Studio</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a href="index.php" class="nav-link">Home</a></li>
                    <li class="nav-item"><a href="services.php" class="nav-link active">Services</a></li>
                    <li class="nav-item"><a href="booking.php" class="nav-link">Book</a></li>
                    <li class="nav-item"><a href="support.php" class="nav-link">Support</a></li>
                    <li class="nav-item"><a href="auth/login.php" class="nav-link">Login</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- HERO SECTION -->
    <section class="hero">
        <div class="container">
            <h1>Explore Our Nail Services</h1>
            <p class="lead">Find the perfect style, treatment, and glam for you.</p>
        </div>
    </section>

    <!-- SECTION INTRO -->
    <div class="container text-center my-5">
        <h2 class="fw-bold mb-3">Why Choose GlamNail?</h2>
        <p class="section-description">
            Our certified professionals are passionate about nail artistry. Whether you're looking for a classic
            manicure, a glamorous extension, or trendy nail art, our services are crafted to pamper, impress, and
            empower.
        </p>
    </div>

    <!-- FILTER BAR (STATIC PLACEHOLDER) -->
    <div class="container">
        <div class="filter-bar shadow-sm d-flex justify-content-between align-items-center flex-wrap gap-3">
            <input type="text" class="form-control w-25" placeholder="Search services...">
            <select class="form-select w-25">
                <option selected>Category</option>
                <option>Manicure</option>
                <option>Pedicure</option>
                <option>Extensions</option>
            </select>
            <select class="form-select w-25">
                <option selected>Sort by</option>
                <option>Price Low to High</option>
                <option>Price High to Low</option>
            </select>
            <button class="btn btn-outline-secondary">Reset</button>
        </div>
    </div>

    <!-- SERVICE GRID -->
    <div class="container py-5">
        <div class="row g-4">
            <?php foreach ($services as $s): ?>
            <div class="col-md-6 col-lg-4">
                <div class="service-card">
                    <img src="images/<?= $s['image'] ?: 'default.jpg' ?>" class="w-100" alt="<?= $s['title'] ?>">
                    <div class="p-4 d-flex flex-column">
                        <h5 class="fw-bold mb-2"><?= $s['title'] ?></h5>
                        <p class="small text-muted flex-grow-1"><?= $s['description'] ?></p>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <span class="badge badge-price">$<?= number_format($s['price'], 2) ?></span>
                            <a href="booking.php" class="btn btn-sm btn-book">Book Now</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- TESTIMONIAL SECTION -->
    <div class="container testimonial-card">
        <h3 class="text-center mb-4">Client Testimonials</h3>
        <div class="row">
            <div class="col-md-4">
                <p>“Amazing work! I’ve never felt more confident in my nails.”<br><strong>- Simran T.</strong></p>
            </div>
            <div class="col-md-4">
                <p>“Super relaxing and professional. I love the vibe here.”<br><strong>- Neha G.</strong></p>
            </div>
            <div class="col-md-4">
                <p>“Beautiful designs every time. Highly recommended!”<br><strong>- Riya K.</strong></p>
            </div>
        </div>
    </div>

    <!-- FAQ SECTION -->
    <div class="container faqs">
        <h3 class="text-center mb-4">Frequently Asked Questions</h3>
        <div class="accordion" id="faqAccordion">
            <div class="accordion-item">
                <h2 class="accordion-header" id="faq1">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapse1">
                        How long does a nail session take?
                    </button>
                </h2>
                <div id="collapse1" class="accordion-collapse collapse show">
                    <div class="accordion-body">On average, a session lasts 45–60 minutes depending on the service.
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="faq2">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapse2">
                        Are walk-ins accepted?
                    </button>
                </h2>
                <div id="collapse2" class="accordion-collapse collapse">
                    <div class="accordion-body">Yes, but we recommend booking online to ensure your spot.</div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="faq3">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapse3">
                        Do you offer combo packages?
                    </button>
                </h2>
                <div id="collapse3" class="accordion-collapse collapse">
                    <div class="accordion-body">Absolutely! Check our discounts and offers page for latest packages.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA -->
    <div class="container text-center cta-banner">
        <h3 class="fw-bold">Pamper Yourself Today</h3>
        <p>Ready for a transformation? Book your appointment online with GlamNail Studio!</p>
        <a href="booking.php" class="btn btn-book btn-lg">Book Now</a>
    </div>

    <!-- FOOTER -->
    <footer class="bg-dark text-white text-center py-4 mt-5">
        <p class="mb-0">&copy; <?= date('Y') ?> GlamNail Studio. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
