<?php
session_start();
include 'includes/db.php';

$limit = 6; // services per page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Get total number of services
$totalStmt = $pdo->query('SELECT COUNT(*) FROM services');
$totalServices = $totalStmt->fetchColumn();
$totalPages = ceil($totalServices / $limit);

// Get paginated services
$stmt = $pdo->prepare('SELECT * FROM services LIMIT :limit OFFSET :offset');
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$services = $stmt->fetchAll();
$imageUrl = $s['image'];
if (!preg_match('/^https?:\/\//', $imageUrl)) {
    $imageUrl = 'images/' . ($imageUrl ?: 'MeniandPedi.png');
}

// ✅ FIX: move category fetch logic here
$catStmt = $pdo->query('SELECT DISTINCT category FROM services');
$categories = $catStmt->fetchAll(PDO::FETCH_COLUMN);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Services - GlamNail Studio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&family=Lato&display=swap"
        rel="stylesheet">

    <!-- AOS Animation CSS -->
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <style>
        body {
            background: #fffafc;
            font-family: 'Lato', sans-serif;
            color: #333;
        }

        h1,
        h2,
        h3,
        h4 {
            font-family: 'Playfair Display', serif;
            color: #b76e79;
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
            transition: transform 0.3s ease;
        }

        .service-card:hover img {
            transform: scale(1.05);
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
    <?php include 'includes/navbar.php'; ?>

    <!-- HERO SECTION -->
    <section class="hero">
        <div class="container">
            <h1>Explore Our Nail Services</h1>
            <p class="lead">Find the perfect style, treatment, and glam for you.</p>
        </div>
    </section>

    <!-- SECTION INTRO -->
    <div class="container text-center my-5" data-aos="fade-up">
        <h2 class="fw-bold mb-3">Why Choose GlamNail?</h2>
        <p class="section-description">
            Our certified professionals are passionate about nail artistry. Whether you're looking for a classic
            manicure, a glamorous extension, or trendy nail art, our services are crafted to pamper, impress, and
            empower.
        </p>
    </div>

    <!-- FILTER BAR (STATIC PLACEHOLDER) -->
    <div class="container" data-aos="fade-up">
        <div class="filter-bar shadow-sm d-flex justify-content-between align-items-center flex-wrap gap-3">
            <input type="text" id="searchInput" class="form-control w-25" placeholder="Search services...">
            <select id="categorySelect" class="form-select w-25">
                <option value="">All Categories</option>
                <?php foreach ($categories as $cat): ?>
                <option value="<?= htmlspecialchars($cat) ?>"><?= ucwords($cat) ?></option>
                <?php endforeach; ?>
            </select>

            <select id="sortSelect" class="form-select w-25">
                <option selected disabled>Sort by</option>
                <option value="low">Price Low to High</option>
                <option value="high">Price High to Low</option>
            </select>

            <button class="btn btn-outline-secondary" id="resetFilters">Reset</button>
        </div>
    </div>

    <!-- SERVICE GRID -->
    <div class="container py-5" data-aos="fade-up">
        <p id="noResults" class="text-center text-muted mt-4" style="display: none;">
            No services match your search.
        </p>

        <div class="row g-4">
            <?php foreach ($services as $index => $s): ?>

            <div class="col-md-6 col-lg-4 service-item" data-title="<?= strtolower($s['title']) ?>"
                data-price="<?= $s['price'] ?>" data-category="<?= strtolower($s['category'] ?? '') ?>"
                data-aos="zoom-in" data-aos-delay="<?= ($index + 1) * 100 ?>">
                <div class="service-card">
                    <img src="<?= htmlspecialchars($imageUrl) ?>" class="w-100"
                        alt="<?= htmlspecialchars($s['title']) ?>" loading="lazy" width="100%" height="200">


                    <div class="p-4 d-flex flex-column">
                        <h5 class="fw-bold mb-2"><?= $s['title'] ?></h5>
                        <p class="small text-muted flex-grow-1"><?= $s['description'] ?></p>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <span class="badge badge-price">$<?= number_format($s['price'], 2) ?></span>
                            <a href="booking.php?service_id=<?= $s['id'] ?>" class="btn btn-sm btn-book">Book Now</a>

                        </div>
                    </div>
                </div>
            </div>

            <?php endforeach; ?>
        </div>
        <?php if ($totalPages > 1): ?>
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center mt-4">
                <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?= $page - 1 ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                </li>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?= $page + 1 ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>

    </div>

    <!-- FAQ SECTION -->
    <div class="container faqs" data-aos="fade-up">
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
    <div class="container text-center cta-banner" data-aos="zoom-in">
        <h3 class="fw-bold">Pamper Yourself Today</h3>
        <p>Ready for a transformation? Book your appointment online with GlamNail Studio!</p>
        <a href="booking.php" class="btn btn-book btn-lg">Book Now</a>
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
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const categorySelect = document.getElementById('categorySelect');
            const sortSelect = document.getElementById('sortSelect');
            const resetBtn = document.getElementById('resetFilters');
            const serviceItems = Array.from(document.querySelectorAll('.service-item'));
            const serviceGrid = document.querySelector('.row.g-4');
            const noResults = document.getElementById('noResults');

            function fadeIn(el) {
                el.style.opacity = 0;
                el.style.display = 'block';
                let last = +new Date();
                const tick = function() {
                    el.style.opacity = +el.style.opacity + (new Date() - last) / 200;
                    last = +new Date();
                    if (+el.style.opacity < 1) {
                        requestAnimationFrame(tick);
                    }
                };
                tick();
            }

            function filterAndSort() {
                const query = searchInput.value.toLowerCase();
                const category = categorySelect.value.toLowerCase();
                const sort = sortSelect.value;

                let filtered = serviceItems.filter(item => {
                    const title = item.getAttribute('data-title');
                    const itemCategory = item.getAttribute('data-category');
                    return title.includes(query) && (!category || itemCategory === category);
                });

                if (sort === 'low') {
                    filtered.sort((a, b) => parseFloat(a.getAttribute('data-price')) - parseFloat(b.getAttribute(
                        'data-price')));
                } else if (sort === 'high') {
                    filtered.sort((a, b) => parseFloat(b.getAttribute('data-price')) - parseFloat(a.getAttribute(
                        'data-price')));
                }

                serviceGrid.innerHTML = '';
                if (filtered.length === 0) {
                    noResults.style.display = 'block';
                } else {
                    noResults.style.display = 'none';
                    filtered.forEach(item => {
                        item.style.opacity = 0;
                        serviceGrid.appendChild(item);
                        fadeIn(item);
                    });
                }
            }

            searchInput.addEventListener('input', filterAndSort);
            categorySelect.addEventListener('change', filterAndSort);
            sortSelect.addEventListener('change', filterAndSort);
            resetBtn.addEventListener('click', () => {
                searchInput.value = '';
                categorySelect.selectedIndex = 0;
                sortSelect.selectedIndex = 0;
                noResults.style.display = 'none';
                serviceItems.forEach(item => {
                    serviceGrid.appendChild(item);
                    item.style.opacity = 1;
                });
            });
        });
    </script>



</body>

</html>
