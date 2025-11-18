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
if ($orderId > 0) {
    // Delete order items first
    mysqli_query($conn, "DELETE FROM order_items WHERE order_id = $orderId");

    // Delete transaction
    mysqli_query($conn, "DELETE FROM transactions WHERE order_id = $orderId");

    // Delete order
    mysqli_query($conn, "DELETE FROM orders WHERE id = $orderId");

    $_SESSION['message'] = "Order #$orderId deleted successfully.";
} else {
    $_SESSION['message'] = "Invalid order ID.";
}

header("Location: ../admin/manage_orders.php");
exit;
?>
