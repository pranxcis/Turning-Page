<?php
session_start();
include('../../includes/header.php');
include('../../config/database.php');

// ------------------------
// ADMIN ACCESS ONLY
// ------------------------
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    $_SESSION['message'] = "Access denied. Admins only.";
    header("Location: ../login.php");
    exit;
}

// Get order ID from URL
$order_id = intval($_GET['id'] ?? 0);
if ($order_id <= 0) {
    $_SESSION['message'] = "Invalid order ID.";
    header("Location: ../manage_orders.php");
    exit;
}

// Fetch order
$stmt = $conn->prepare("SELECT id, status FROM orders WHERE id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
$stmt->close();

if (!$order) {
    $_SESSION['message'] = "Order not found.";
    header("Location: ../manage_orders.php");
    exit;
}

// ENUM options
$statuses = ['Pending','Paid','Shipped','Delivered','Cancelled'];
?>

<div class="d-flex">
    <div class="container my-5">
        <div class="card shadow-sm p-4 mx-auto" style="max-width: 500px;">
            <h3 class="mb-4 text-center">Edit Order #<?= $order['id'] ?></h3>

            <form action="update_order.php" method="POST">
                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">

                <div class="mb-3">
                    <label for="status" class="form-label">Order Status</label>
                    <select name="status" id="status" class="form-select" required>
                        <?php foreach ($statuses as $statusOption): ?>
                            <option value="<?= $statusOption ?>" <?= $order['status'] === $statusOption ? 'selected' : '' ?>>
                                <?= $statusOption ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-success">Update Status</button>
                    <a href="../manage_orders.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include('../../includes/footer.php'); ?>
