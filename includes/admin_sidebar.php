<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}
?>

<div class="d-flex flex-column flex-shrink-0 p-3 bg-light shadow-sm" style="width: 300px; min-height: 100vh;">

    <a href="dashboard.php" class="d-flex align-items-center mb-4 text-dark text-decoration-none">
        <i class="fas fa-cogs fs-3 me-2 ms-4 pt-4"></i>
        <span class="fs-4 fw-bold pt-4 ms-2">ADMIN PANEL</span>
    </a>

            <ul class="nav nav-pills flex-column ms-4 mb-auto pt-2">
                <li class="nav-item mb-2">
                    <a href="dashboard.php" class="nav-link link-dark d-flex align-items-center rounded py-2 px-3 hover-shadow">
                        <i class="fas fa-home me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="manage_authors.php" class="nav-link link-dark d-flex align-items-center rounded py-2 px-3 hover-shadow">
                        <i class="fas fa-user-edit me-2"></i> Manage Authors
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="manage_books.php" class="nav-link link-dark d-flex align-items-center rounded py-2 px-3 hover-shadow">
                        <i class="fas fa-book me-2"></i> Manage Books
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="manage_orders.php" class="nav-link link-dark d-flex align-items-center rounded py-2 px-3 hover-shadow">
                        <i class="fas fa-shopping-cart me-2"></i> Manage Orders
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="manage_users.php" class="nav-link link-dark d-flex align-items-center rounded py-2 px-3 hover-shadow">
                        <i class="fas fa-users me-2"></i> Manage Users
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="manage_reviews.php" class="nav-link link-dark d-flex align-items-center rounded py-2 px-3 hover-shadow">
                        <i class="fas fa-star me-2"></i> Manage Reviews
                    </a>
                </li>
                <li class="nav-item">
                    <a href="manage_coupons.php" class="nav-link link-dark d-flex align-items-center rounded py-2 px-3 hover-shadow">
                        <i class="fas fa-ticket me-2"></i> Manage Vouchers
                    </a>
                </li>
            </ul>

    <hr class="my-3">
</div>

<style>
.hover-shadow:hover {
    background-color: #f8f9fa;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    transition: all 0.2s ease-in-out;
}
</style>
