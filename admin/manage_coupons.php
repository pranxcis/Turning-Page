<?php
session_start();

include('../config/database.php');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    $_SESSION['message'] = "Access denied. Admins only.";
    header("Location: ../user/login.php");
    exit;
}

$keyword = '';
if(isset($_GET['search'])) {
    $keyword = strtolower(trim($_GET['search']));
}

$filter = $_GET['filter'] ?? 'all'; 

$sql = "SELECT * FROM coupons WHERE 1 ";

if($keyword) {
    $sql .= " AND LOWER(code) LIKE '%{$keyword}%' ";
}

if($filter !== 'all') {
    $sql .= " AND type = '{$filter}' ";
}

$sql .= " ORDER BY expires_at ASC ";

$result = mysqli_query($conn, $sql);

$coupons = [];
while ($row = mysqli_fetch_assoc($result)) {
    $coupons[] = $row;
}

$couponCount = count($coupons);

include('../includes/header.php');
?>

<div class="d-flex">
    <?php include('../includes/admin_sidebar.php'); ?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Manage Coupons (<?= $couponCount ?>)</h2>
        <a href="coupons/create_coupon.php" class="btn btn-primary btn-lg">
            <i class="fa-solid fa-plus"></i> Add Coupon
        </a>
    </div>

    <div class="d-flex mb-4 align-items-center">
        <div class="me-3">
            <a href="?filter=all&search=<?= urlencode($keyword) ?>" class="btn <?= ($filter==='all') ? 'btn-primary' : 'btn-outline-primary' ?> me-2">All</a>
            <a href="?filter=discount&search=<?= urlencode($keyword) ?>" class="btn <?= ($filter==='discount') ? 'btn-primary' : 'btn-outline-primary' ?> me-2">Discount</a>
            <a href="?filter=free_shipping&search=<?= urlencode($keyword) ?>" class="btn <?= ($filter==='free_shipping') ? 'btn-primary' : 'btn-outline-primary' ?>">Free Shipping</a>
        </div>

        <form class="flex-grow-1" method="GET" action="">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search by coupon code..." value="<?= htmlspecialchars($keyword) ?>">
                <button class="btn btn-outline-secondary" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
            </div>
        </form>
    </div>

    <?php if($couponCount > 0): ?>
        <div class="row g-4">
            <?php foreach($coupons as $coupon): ?>
                <div class="col-12">
                    <div class="card shadow-sm p-4 border-1">
                        <div class="d-flex justify-content-between align-items-center">

                            <div class="flex-grow-1">
                                <h5 class="mb-2"><?= htmlspecialchars($coupon['code']) ?> 
                                    <small class="text-muted">(ID: <?= $coupon['id'] ?>)</small>
                                </h5>
                                <p class="mb-1"><strong>Type:</strong> <?= htmlspecialchars($coupon['type']) ?></p>
                                <p class="mb-1"><strong>Amount:</strong> <?= htmlspecialchars($coupon['amount']) ?></p>
                                <p class="mb-1"><strong>Minimum Order:</strong> <?= htmlspecialchars($coupon['min_order']) ?></p>
                                <p class="mb-0"><strong>Expires At:</strong> <?= htmlspecialchars($coupon['expires_at']) ?></p>
                            </div>

                            <div class="d-flex flex-column ms-3">
                                <a href="coupons/edit_coupon.php?id=<?= $coupon['id'] ?>" class="btn btn-outline-primary btn-sm mb-2">
                                    <i class="fa-regular fa-pen-to-square"></i> Edit
                                </a>
                                <a href="coupons/delete_coupon.php?id=<?= $coupon['id'] ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to delete this coupon?');">
                                    <i class="fa-solid fa-trash"></i> Delete
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-warning text-center">No coupons found.</div>
    <?php endif; ?>
</div>
</div>

<?php include('../includes/footer.php'); ?>
