<?php
session_start();
include('../../config/database.php');
include('../../includes/mail.php'); // your smtp_send_mail or mail function

// ------------------------
// ADMIN ACCESS ONLY
// ------------------------
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    $_SESSION['message'] = "Access denied. Admins only.";
    header("Location: ../login.php");
    exit;
}

// Validate POST
$order_id = intval($_POST['order_id'] ?? 0);
$status = $_POST['status'] ?? '';
$allowedStatuses = ['Pending','Processing','Paid','Shipped','Delivered','Cancelled'];

if ($order_id <= 0 || !in_array($status, $allowedStatuses)) {
    $_SESSION['message'] = "Invalid order ID or status.";
    header("Location: edit_order.php?id=$order_id");
    exit;
}

// Fetch order & user info
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

// Update order status
$stmt = $conn->prepare("UPDATE orders SET status=? WHERE id=?");
$stmt->bind_param("si", $status, $order_id);

if ($stmt->execute()) {
    $_SESSION['message'] = "Order #$order_id status updated to '$status'.";

    // --- SEND EMAIL ---
    $customerEmail = $order['email'];
    $customerName  = trim($order['first_name'] . ' ' . $order['last_name']);
    if (!$customerName) $customerName = $order['username'];

    $statusHtml = "<h2>Order #{$order_id} Status Update</h2>";
    $statusHtml .= "<p>Hi <strong>{$customerName}</strong>,</p>";
    $statusHtml .= "<p>Your order <strong>#{$order_id}</strong> status has been updated to: <strong>{$status}</strong>.</p>";

    // Optional: Include order summary
    $statusHtml .= "<p>Order Total: ₱" . number_format($order['total'], 2) . "</p>";
    $statusHtml .= "<p>Shipping Address: " . htmlspecialchars($order['shipping_address']) . "</p>";

    $statusHtml .= "<p>We will notify you with further updates regarding your order.</p>";
    $statusHtml .= "<p>— Turning Page Team</p>";

    $emailResult = smtp_send_mail(
        $customerEmail,
        "Order #{$order_id} Status Update — {$status}",
        $statusHtml
    );

    if (!$emailResult['success']) {
        $_SESSION['message'] .= " However, email notification failed: " . htmlspecialchars($emailResult['error']);
    }

} else {
    $_SESSION['message'] = "Failed to update order status. Please try again.";
}

$stmt->close();
header("Location: ../manage_orders.php");
exit;
?>
