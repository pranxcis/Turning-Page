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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $user_id = intval($_POST['user_id']);
    $total_amount = floatval($_POST['total_amount']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    $sql = "UPDATE orders 
            SET user_id=$user_id, total_amount=$total_amount, status='$status' 
            WHERE id=$id";

    if (mysqli_query($conn, $sql)) {
        $_SESSION['message'] = "Order #$id updated successfully.";
    } else {
        $_SESSION['message'] = "Error updating order: " . mysqli_error($conn);
    }
}

header("Location: ../admin/manage_orders.php");
exit;
?>
