<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">GlamNail Studio</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a href="index.php" class="nav-link <?= $currentPage === 'index.php' ? 'active' : '' ?>">Home</a>
                </li>
                <li class="nav-item">
                    <a href="services.php"
                        class="nav-link <?= $currentPage === 'services.php' ? 'active' : '' ?>">Services</a>
                </li>
                <li class="nav-item">
                    <a href="booking.php"
                        class="nav-link <?= $currentPage === 'booking.php' ? 'active' : '' ?>">Book</a>
                </li>
                <li class="nav-item">
                    <a href="support.php"
                        class="nav-link <?= $currentPage === 'support.php' ? 'active' : '' ?>">Support</a>
                </li>

                <?php if (isset($_SESSION['user_id'])): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?= in_array($currentPage, ['dashboard.php', 'profile.php']) ? 'active' : '' ?>"
                        href="#" data-bs-toggle="dropdown">My Account</a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="dashboard.php">Dashboard</a></li>
                        <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item text-danger" href="actions/logout.php">Logout</a></li>
                    </ul>
                </li>
                <?php else: ?>
                <li class="nav-item">
                    <a href="auth/login.php"
                        class="nav-link <?= $currentPage === 'login.php' ? 'active' : '' ?>">Login</a>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
