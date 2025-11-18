<?php
session_start();
include('../../config/database.php');
include('../../includes/header.php');

// Admin access only
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    $_SESSION['message'] = "Access denied. Admins only.";
    header("Location: ../login.php");
    exit;
}

$order_id = intval($_GET['id'] ?? 0);
if ($order_id <= 0) {
    $_SESSION['message'] = "Invalid order ID.";
    header("Location: ../manage_orders.php");
    exit;
}

// Fetch order info
$stmt = $conn->prepare("
    SELECT o.*, u.username, u.email
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.id
    WHERE o.id=?
");
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

// Fetch order items
$stmt = $conn->prepare("
    SELECT oi.*, b.title
    FROM order_items oi
    LEFT JOIN books b ON oi.book_id = b.id
    WHERE oi.order_id=?
");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$items_result = $stmt->get_result();
$stmt->close();
?>

<div class="container my-5">
    <h2>Order #<?= $order['id'] ?> Details</h2>
    <p><strong>Customer:</strong> <?= htmlspecialchars($order['username']) ?> (<?= htmlspecialchars($order['email']) ?>)</p>
    <p><strong>Status:</strong> <?= htmlspecialchars($order['status']) ?></p>
    <p><strong>Date:</strong> <?= date("F d, Y h:i A", strtotime($order['created_at'])) ?></p>

    <h4 class="mt-4">Items</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Book</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $total = 0;
            while ($item = $items_result->fetch_assoc()): 
                $subtotal = $item['price'] * $item['quantity'];
                $total += $subtotal;
            ?>
                <tr>
                    <td><?= htmlspecialchars($item['title']) ?></td>
                    <td>₱<?= number_format($item['price'],2) ?></td>
                    <td><?= $item['quantity'] ?></td>
                    <td>₱<?= number_format($subtotal,2) ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="text-end">Total:</th>
                <th>₱<?= number_format($total,2) ?></th>
            </tr>
        </tfoot>
    </table>

    <a href="../manage_orders.php" class="btn btn-secondary mt-3">Back to Orders</a>
</div>

<?php include('../../includes/footer.php'); ?>
