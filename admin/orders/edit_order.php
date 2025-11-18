<?php
session_start();
include('../includes/header.php');
include('../config/database.php');

// ADMIN ONLY
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    $_SESSION['message'] = "Access denied. Admins only.";
    header("Location: ../login.php");
    exit;
}

$orderId = intval($_GET['id'] ?? 0);
if ($orderId <= 0) {
    $_SESSION['message'] = "Invalid order ID.";
    header("Location: ../admin/manage_orders.php");
    exit;
}

// Fetch order
$order = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM orders WHERE id = $orderId"));
if (!$order) {
    $_SESSION['message'] = "Order not found.";
    header("Location: ../admin/manage_orders.php");
    exit;
}
?>

<div class="container my-5">
    <h2>Edit Order #<?= $order['id'] ?></h2>
    <form method="POST" action="update_order.php">
        <input type="hidden" name="id" value="<?= $order['id'] ?>">
        <div class="mb-3">
            <label>User ID</label>
            <input type="number" name="user_id" class="form-control" value="<?= $order['user_id'] ?>" required>
        </div>
        <div class="mb-3">
            <label>Total Amount</label>
            <input type="number" step="0.01" name="total_amount" class="form-control" value="<?= $order['total_amount'] ?>" required>
        </div>
        <div class="mb-3">
            <label>Status</label>
            <input type="text" name="status" class="form-control" value="<?= $order['status'] ?>" required>
        </div>
        <button class="btn btn-warning">Update Order</button>
    </form>
</div>

<?php include('../includes/footer.php'); ?>
