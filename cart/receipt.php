<?php
session_start();
include('../config/database.php');

if (!isset($_SESSION['user']['id'])) {
    $_SESSION['message'] = "Please login to view your order receipt.";
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];

$order_id = intval($_GET['order_id'] ?? 0);
if ($order_id <= 0) {
    $_SESSION['message'] = "Invalid order.";
    header("Location: ../shop/index.php");
    exit;
}

$stmt = $conn->prepare("
    SELECT o.*, up.first_name, up.last_name, u.username, u.email
    FROM orders o
    JOIN users u ON o.user_id = u.id
    LEFT JOIN user_profiles up ON u.id = up.user_id
    WHERE o.id = ? AND o.user_id = ?
    LIMIT 1
");
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$order) {
    $_SESSION['message'] = "Order not found.";
    header("Location: ../shop/index.php");
    exit;
}

$stmt = $conn->prepare("
    SELECT oi.*, b.title, b.image
    FROM order_items oi
    JOIN books b ON oi.book_id = b.id
    WHERE oi.order_id = ?
");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order_items = [];
while ($row = $result->fetch_assoc()) {
    $row['line_total'] = $row['quantity'] * $row['price'];
    $order_items[] = $row;
}
$stmt->close();

include('../includes/header.php');
?>

<div class="container my-5">
    <div class="text-center mb-4">
        <h1 class="text-success">Your Order Was Successful!</h1>
        <p>Thank you for shopping with Turning Page. Your order has been placed successfully.</p>
        <h4>Order #<?= $order['id'] ?></h4>
        <p>Status: <strong><?= htmlspecialchars($order['status']) ?></strong></p>
    </div>

    <div class="card shadow-sm p-4 mb-4">
        <h4>Shipping Information</h4>
        <hr>
        <p><strong>Name:</strong> <?= htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($order['email']) ?></p>
        <p><strong>Shipping Address:</strong> <?= htmlspecialchars($order['shipping_address']) ?></p>
        <p><strong>Shipping Method:</strong> <?= htmlspecialchars($order['shipping_method']) ?></p>
    </div>

    <div class="card shadow-sm p-4 mb-4">
        <h4>Order Items</h4>
        <hr>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Book</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Line Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($order_items as $item): ?>
                <tr>
                    <td>
                        <img src="../assets/images/books/<?= htmlspecialchars($item['image'] ?? 'default_book.png') ?>" 
                             style="width:50px;height:70px;object-fit:cover;" class="me-2">
                        <?= htmlspecialchars($item['title']) ?>
                    </td>
                    <td><?= $item['quantity'] ?></td>
                    <td>₱<?= number_format($item['price'], 2) ?></td>
                    <td>₱<?= number_format($item['line_total'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="text-end mt-3">
            <p>Subtotal: ₱<?= number_format($order['subtotal'], 2) ?></p>
            <p>Shipping: ₱<?= number_format($order['shipping_fee'], 2) ?></p>
            <p>Discount: ₱<?= number_format($order['subtotal'] + $order['shipping_fee'] - $order['total'], 2) ?></p>
            <h5>Total: ₱<?= number_format($order['total'], 2) ?></h5>
        </div>
    </div>

    <div class="text-center">
        <a href="../shop/index.php" class="btn btn-primary">Continue Shopping</a>
        <a href="../user/order_history.php" class="btn btn-secondary">View Order History</a>
    </div>
</div>

<?php include('../includes/footer.php'); ?>
