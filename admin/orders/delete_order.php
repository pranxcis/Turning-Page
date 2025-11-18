<?php
session_start();
include('../../config/database.php');

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

// Delete order items first (foreign key safety)
$stmt = $conn->prepare("DELETE FROM order_items WHERE order_id=?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$stmt->close();

// Delete order itself
$stmt = $conn->prepare("DELETE FROM orders WHERE id=?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$stmt->close();

$_SESSION['message'] = "Order deleted successfully.";
header("Location: ../manage_orders.php");
exit;
