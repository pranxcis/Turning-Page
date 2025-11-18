<?php
session_start();
include('../../config/database.php');

// Admin check
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    $_SESSION['message'] = "Access denied. Admins only.";
    header("Location: ../login.php");
    exit;
}

$id = intval($_GET['id'] ?? 0);
if($id <= 0){
    $_SESSION['message'] = "Invalid coupon ID";
    header("Location: ../manage_coupons.php");
    exit;
}

// Delete
$stmt = $conn->prepare("DELETE FROM coupons WHERE id=?");
$stmt->bind_param("i",$id);
$stmt->execute();

$_SESSION['message'] = "Coupon deleted successfully!";
header("Location: ../manage_coupons.php");
