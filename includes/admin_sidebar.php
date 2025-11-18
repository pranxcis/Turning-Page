<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ensure only admins can access
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}
?>

<!-- Admin Sidebar -->
<div class="d-flex flex-column flex-shrink-0 p-3 bg-light shadow-sm" style="width: 350px; min-height: 100vh;">

    <!-- Logo / Title -->
    <a href="dashboard.php" class="d-flex align-items-center ms-4 mt-3 mb-2 mb-md-0 me-md-auto text-dark text-decoration-none">
        <i class="fas fa-cogs fs-3 me-2"></i>
        <span class="fs-4 fw-bold ms-3"> Admin Panel</span>
    </a>
    <hr class="my-4">

    <!-- Navigation -->
    <ul class="nav nav-pills flex-column ms-4 mb-auto pt-1">
        <li class="nav-item">
            <a href="dashboard.php" class="nav-link link-dark d-flex align-items-center rounded py-2 px-3 mb-2 hover-shadow">
                <i class="fas fa-home me-2"></i>  Dashboard
            </a>
        </li>
        <li>
            <a href="manage_authors.php" class="nav-link link-dark d-flex align-items-center rounded py-2 px-3 mb-2 hover-shadow">
                <i class="fas fa-user-edit me-2"></i>  Manage Authors
            </a>
        </li>
        <li>
            <a href="manage_books.php" class="nav-link link-dark d-flex align-items-center rounded py-2 px-3 mb-2 hover-shadow">
                <i class="fas fa-book me-2"></i>  Manage Books
            </a>
        </li>
        <li>
            <a href="manage_orders.php" class="nav-link link-dark d-flex align-items-center rounded py-2 px-3 mb-2 hover-shadow">
                <i class="fas fa-shopping-cart me-2"></i>  Manage Orders
            </a>
        </li>
        <li>
            <a href="manage_users.php" class="nav-link link-dark d-flex align-items-center rounded py-2 px-3 mb-2 hover-shadow">
                <i class="fas fa-users me-2"></i>  Manage Users
            </a>
        </li>
        <li>
            <a href="manage_reviews.php" class="nav-link link-dark d-flex align-items-center rounded py-2 px-3 mb-2 hover-shadow">
                <i class="fas fa-star me-2"></i>  Manage Reviews
            </a>
        </li>
        <li>
            <a href="manage_coupons.php" class="nav-link link-dark d-flex align-items-center rounded py-2 px-3 hover-shadow">
                <i class="fas fa-ticket me-2"></i>  Manage Vouchers
        </li>
    </ul>
    <hr class="my-2">
</div>

<!-- Additional CSS for hover and shadows -->
<style>
.hover-shadow:hover {
    background-color: #f8f9fa;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    transition: all 0.2s ease-in-out;
}
</style>
