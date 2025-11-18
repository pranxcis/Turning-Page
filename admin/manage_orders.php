<?php
session_start();
include('../includes/header.php');
include('../config/database.php');

// ------------------------
// ADMIN ACCESS ONLY
// ------------------------
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    $_SESSION['message'] = "Access denied. Admins only.";
    header("Location: ../login.php");
    exit;
}

// ------------------------
// SEARCH (by order id, user email, or status)
// ------------------------
$keyword = strtolower(trim($_GET['search'] ?? ''));

// ------------------------
// FETCH ORDERS WITH ITEM COUNT & TOTALS
// ------------------------
if ($keyword) {
    $sql = "
        SELECT 
            o.id, o.user_id, o.status, o.created_at,
            u.username AS username, u.email,
            COUNT(oi.id) AS item_count,
            COALESCE(SUM(oi.price * oi.quantity),0) AS total_amount
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.id
        LEFT JOIN order_items oi ON o.id = oi.order_id
        WHERE 
            LOWER(o.status) LIKE '%{$keyword}%'
            OR LOWER(u.email) LIKE '%{$keyword}%'
            OR o.id LIKE '%{$keyword}%'
        GROUP BY o.id
        ORDER BY o.created_at DESC
    ";
} else {
    $sql = "
        SELECT 
            o.id, o.user_id, o.status, o.created_at,
            u.username AS username, u.email,
            COUNT(oi.id) AS item_count,
            COALESCE(SUM(oi.price * oi.quantity),0) AS total_amount
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.id
        LEFT JOIN order_items oi ON o.id = oi.order_id
        GROUP BY o.id
        ORDER BY o.created_at DESC
    ";
}

$result = mysqli_query($conn, $sql);
$orderCount = mysqli_num_rows($result);
?>

<div class="d-flex">
    <?php include('../includes/admin_sidebar.php'); ?>

<div class="container my-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Orders Management (<?= $orderCount ?>)</h2>
    </div>

    <!-- SEARCH -->
    <form class="mb-4" method="GET" action="">
        <div class="input-group">
            <input 
                type="text" 
                name="search" 
                class="form-control" 
                placeholder="Search by Order ID, Customer Email, or Status..."
                value="<?= htmlspecialchars($keyword) ?>"
            >
            <button class="btn btn-outline-secondary" type="submit">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </div>
    </form>

    <?php if ($orderCount > 0): ?>
        <div class="row g-4">
            <?php while ($order = mysqli_fetch_assoc($result)): ?>
                <div class="col-12">
                    <div class="card p-3 shadow-sm d-flex flex-row justify-content-between align-items-center">

                        <!-- ORDER DETAILS -->
                        <div class="flex-grow-1">

                            <h5 class="mb-2">Order #<?= $order['id'] ?></h5>

                            <p class="mb-1">
                                <strong>Customer:</strong>
                                <?= htmlspecialchars($order['username']) ?> 
                                (<?= htmlspecialchars($order['email']) ?>)
                            </p>

                            <p class="mb-1">
                                <strong>Items:</strong> <?= $order['item_count'] ?> item(s)
                            </p>

                            <p class="mb-1">
                                <strong>Total Amount:</strong> ₱<?= number_format($order['total_amount'], 2) ?>
                            </p>

                            <p class="mb-1">
                                <strong>Status:</strong> 
                                <?php
                                    $status = strtolower($order['status']);
                                    $badgeClass = match($status) {
                                        'pending' => 'bg-warning',
                                        'processing' => 'bg-primary',
                                        'completed' => 'bg-success',
                                        'cancelled', 'declined' => 'bg-danger',
                                        default => 'bg-secondary'
                                    };
                                ?>
                                <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($order['status']) ?></span>
                            </p>

                            <p class="mb-1">
                                <strong>Date:</strong> 
                                <?= date("F d, Y h:i A", strtotime($order['created_at'])) ?>
                            </p>
                        </div>

                        <!-- ACTIONS -->
                        <div class="d-flex flex-column ms-3">
                            <a href="orders/view_order.php?id=<?= $order['id'] ?>" 
                               class="btn btn-outline-primary btn-sm mb-2">
                                <i class="fa-regular fa-eye"></i> View
                            </a>

                            <a href="orders/update_order.php?id=<?= $order['id'] ?>" 
                               class="btn btn-outline-warning btn-sm mb-2">
                                <i class="fa-regular fa-pen-to-square"></i> Update
                            </a>

                            <a href="orders/delete_order.php?id=<?= $order['id'] ?>" 
                               class="btn btn-outline-danger btn-sm"
                               onclick="return confirm('Are you sure you want to delete this order?');">
                                <i class="fa-solid fa-trash"></i> Delete
                            </a>
                        </div>

                    </div>
                </div>
            <?php endwhile; ?>
        </div>

    <?php else: ?>
        <div class="alert alert-warning text-center">No orders found.</div>
    <?php endif; ?>

</div>
</div>

<?php include('../includes/footer.php'); ?>
