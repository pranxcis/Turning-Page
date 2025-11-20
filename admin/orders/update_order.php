<?php
session_start();
include('../../config/database.php');
include('../../includes/mail.php'); 


if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    $_SESSION['message'] = "Access denied. Admins only.";
    header("Location: ../login.php");
    exit;
}

$order_id = intval($_POST['order_id'] ?? 0);
$new_status = $_POST['status'] ?? '';

$allowedStatuses = ['Pending','Processing','Paid','Shipped','Delivered','Cancelled'];

if ($order_id <= 0 || !in_array($new_status, $allowedStatuses)) {
    $_SESSION['message'] = "Invalid order ID or status.";
    header("Location: edit_order.php?id=$order_id");
    exit;
}

$stmt = $conn->prepare("
    SELECT o.*, u.email, u.username, up.first_name, up.last_name
    FROM orders o
    JOIN users u ON o.user_id = u.id
    LEFT JOIN user_profiles up ON u.id = up.user_id
    WHERE o.id = ?
");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$order) {
    $_SESSION['message'] = "Order not found.";
    header("Location: ../manage_orders.php");
    exit;
}

$old_status = $order['status']; 


if ($new_status === 'Cancelled' && in_array($old_status, ['Pending','Processing','Paid', 'Delivered'])) {

    $itemStmt = $conn->prepare("
        SELECT book_id, quantity 
        FROM order_items 
        WHERE order_id = ?
    ");
    $itemStmt->bind_param("i", $order_id);
    $itemStmt->execute();
    $items = $itemStmt->get_result();
    $itemStmt->close();

    while ($item = $items->fetch_assoc()) {
        $updateStock = $conn->prepare("
            UPDATE books SET stock = stock + ? WHERE id = ?
        ");
        $updateStock->bind_param("ii", $item['quantity'], $item['book_id']);
        $updateStock->execute();
        $updateStock->close();
    }
}

$stmt = $conn->prepare("UPDATE orders SET status=? WHERE id=?");
$stmt->bind_param("si", $new_status, $order_id);

if ($stmt->execute()) {
    $_SESSION['message'] = "Order #$order_id updated to '$new_status'.";

    $customerEmail = $order['email'];
    $customerName  = trim($order['first_name'] . ' ' . $order['last_name']);
    if (!$customerName) {
        $customerName = $order['username'];
    }

    $emailContent = "
        <h2>Order #{$order_id} Status Update</h2>
        <p>Hi <strong>{$customerName}</strong>,</p>
        <p>Your order <strong>#{$order_id}</strong> status has been updated to: <strong>{$new_status}</strong>.</p>
        <p>Order Total: ₱" . number_format($order['total'], 2) . "</p>
        <p>Shipping Address: " . htmlspecialchars($order['shipping_address']) . "</p>
        <p>We will notify you with further updates regarding your order.</p>
        <p>— Turning Page Team</p>
    ";

    $emailSend = smtp_send_mail(
        $customerEmail,
        "Order #{$order_id} Status Update — {$new_status}",
        $emailContent
    );

    if (!$emailSend['success']) {
        $_SESSION['message'] .= " (Email failed: " . htmlspecialchars($emailSend['error']) . ")";
    }

} else {
    $_SESSION['message'] = "Failed to update order status.";
}

$stmt->close();
header("Location: ../manage_orders.php");
exit;
?>

