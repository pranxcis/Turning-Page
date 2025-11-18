<?php
session_start();
// Ensure user is logged in
if (!isset($_SESSION['user'])) {
    $_SESSION['message'] = "Please login to continue to checkout.";
    header("Location: ../login.php");
    exit;
}
include('../includes/header.php');
include('../config/database.php');

$order_id = intval($_GET['order_id'] ?? 0);
if ($order_id <= 0) {
    echo "<div class='container my-4'><p>Invalid order ID.</p></div>";
    include('../includes/footer.php');
    exit;
}

// Fetch order info
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? LIMIT 1");
$stmt->bind_param('i', $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$order) {
    echo "<div class='container my-4'><p>Order not found.</p></div>";
    include('../includes/footer.php');
    exit;
}

// Fetch order items
$stmt_items = $conn->prepare("SELECT oi.*, b.title, b.price FROM order_items oi JOIN books b ON oi.book_id = b.id WHERE oi.order_id = ?");
$stmt_items->bind_param('i', $order_id);
$stmt_items->execute();
$items_res = $stmt_items->get_result();
$items = [];
while ($row = $items_res->fetch_assoc()) {
    $items[] = $row;
}
$stmt_items->close();
?>

<div class="container my-5">
    <h1 class="text-center mb-4">Receipt — Order #<?= $order_id ?></h1>

    <h4>Customer Info</h4>
    <p>Name: <?= htmlspecialchars($order['fullname']) ?><br>
       Email: <?= htmlspecialchars($order['email']) ?><br>
       Phone: <?= htmlspecialchars($order['phone']) ?><br>
       Address: <?= htmlspecialchars($order['address']) ?></p>

    <h4>Order Items</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Book</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
        <?php 
        $subtotal = 0;
        foreach ($items as $it): 
            $total = $it['price'] * $it['quantity'];
            $subtotal += $total;
        ?>
            <tr>
                <td><?= htmlspecialchars($it['title']) ?></td>
                <td><?= $it['quantity'] ?></td>
                <td>₱<?= number_format($it['price'],2) ?></td>
                <td>₱<?= number_format($total,2) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <p style="text-align:right;">Subtotal: <strong>₱<?= number_format($subtotal,2) ?></strong></p>
    <p style="text-align:right;">Shipping: <strong>₱<?= number_format($order['shipping'],2) ?></strong></p>
    <p style="text-align:right;">Grand Total: <strong>₱<?= number_format($subtotal + $order['shipping'],2) ?></strong></p>
</div>

<?php include('../includes/footer.php'); ?>
