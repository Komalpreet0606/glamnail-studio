<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="admin.php">GlamNail Admin</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="adminNavbar">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage == 'admin.php' ? 'active' : '' ?>"
                        href="admin.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage == 'admin_services.php' ? 'active' : '' ?>"
                        href="admin_services.php">Services</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage == 'admin_bookings.php' ? 'active' : '' ?>"
                        href="admin_bookings.php">Bookings</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage == 'discounts.php' ? 'active' : '' ?>"
                        href="discounts.php">Discounts</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage == 'users.php' ? 'active' : '' ?>" href="users.php">Users</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-danger" href="../actions/logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
