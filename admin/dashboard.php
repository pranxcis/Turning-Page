<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include header and database
include("../includes/header.php");
include("../config/database.php");

// Check if user is logged in and is admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php"); // Redirect to login if not admin
    exit();
}

$pageTitle = "Admin Dashboard";

// Fetch quick stats for dashboard
$stats = [];
$query = "SELECT 
    (SELECT COUNT(*) FROM users) AS total_users,
    (SELECT COUNT(*) FROM authors) AS total_authors,
    (SELECT COUNT(*) FROM books) AS total_books,
    (SELECT COUNT(*) FROM orders) AS total_orders,
    (SELECT COUNT(*) FROM reviews) AS total_reviews";
$result = mysqli_query($conn, $query);
if ($result) {
    $stats = mysqli_fetch_assoc($result);
}
?>

<div class="d-flex">
    <?php include('../includes/admin_sidebar.php'); ?>

    <!-- MAIN CONTENT -->
    <div class="container mt-5 mb-5">
        <!-- Admin Dashboard Title -->
        <h1 class="mb-2">Admin Dashboard</h1>
        <!-- Welcome message directly under title -->
        <p class="mb-4">Welcome, <?php echo htmlspecialchars($_SESSION['user']['name'] ?? 'Admin'); ?>! Manage your site's content below.</p>
        <!-- QUICK STATS -->
        <div class="row mb-4">
            <div class="col-12">
                <h3 class="mb-3">Quick Stats</h3>
                <div class="d-flex flex-wrap justify-content-start gap-3">
                    <div class="card text-center" style="width:120px;">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $stats['total_users'] ?? 0; ?></h5>
                            <p class="card-text">Users</p>
                        </div>
                    </div>
                    <div class="card text-center" style="width:120px;">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $stats['total_authors'] ?? 0; ?></h5>
                            <p class="card-text">Authors</p>
                        </div>
                    </div>
                    <div class="card text-center" style="width:120px;">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $stats['total_books'] ?? 0; ?></h5>
                            <p class="card-text">Books</p>
                        </div>
                    </div>
                    <div class="card text-center" style="width:120px;">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $stats['total_orders'] ?? 0; ?></h5>
                            <p class="card-text">Orders</p>
                        </div>
                    </div>
                    <div class="card text-center" style="width:120px;">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $stats['total_reviews'] ?? 0; ?></h5>
                            <p class="card-text">Reviews</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- MANAGEMENT CARDS -->
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Manage Authors</h5>
                        <p class="card-text">Add, edit, or delete authors.</p>
                        <a href="manage_authors.php" class="btn btn-primary">Go to Authors</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Manage Books</h5>
                        <p class="card-text">Add, edit, or delete books.</p>
                        <a href="manage_books.php" class="btn btn-primary">Go to Books</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Manage Orders</h5>
                        <p class="card-text">View and manage customer orders.</p>
                        <a href="manage_orders.php" class="btn btn-primary">Go to Orders</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Manage Reviews</h5>
                        <p class="card-text">Moderate and manage reviews.</p>
                        <a href="manage_reviews.php" class="btn btn-primary">Go to Reviews</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Manage Users</h5>
                        <p class="card-text">View and manage user accounts.</p>
                        <a href="manage_users.php" class="btn btn-primary">Go to Users</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Manage Coupons</h5>
                        <p class="card-text">Create, edit, or delete discount coupons and vouchers.</p>
                        <a href="manage_coupons.php" class="btn btn-primary">Go to Coupons</a>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- container -->
</div> <!-- d-flex -->


<?php include("../includes/footer.php"); ?>
