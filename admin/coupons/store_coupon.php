<?php
session_start();
include('../../config/database.php');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    $_SESSION['message'] = "Access denied. Admins only.";
    header("Location: ../login.php");
    exit;
}

$code = trim($_POST['code'] ?? '');
$type = $_POST['type'] ?? '';
$amount = $_POST['amount'] ?? '';
$min_order = $_POST['min_order'] ?? '';
$expires_at = $_POST['expires_at'] ?? '';

$_SESSION['form_code'] = $code;
$_SESSION['form_type'] = $type;
$_SESSION['form_amount'] = $amount;
$_SESSION['form_min_order'] = $min_order;
$_SESSION['form_expires_at'] = $expires_at;

$errors = [];
if (!$code) $errors[] = "Code is required";
if (!$type) $errors[] = "Type is required";
if (!is_numeric($amount) || $amount < 0) $errors[] = "Amount must be a positive number";
if (!is_numeric($min_order) || $min_order < 0) $errors[] = "Minimum order must be a positive number";
if (!$expires_at) $errors[] = "Expiration date is required";

if ($errors) {
    $_SESSION['message'] = implode("<br>", $errors);
    header("Location: create_coupon.php");
    exit;
}

$stmt = $conn->prepare("INSERT INTO coupons (code, type, amount, min_order, expires_at) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("ssdds", $code, $type, $amount, $min_order, $expires_at);

if ($stmt->execute()) {
    unset($_SESSION['form_code'], $_SESSION['form_type'], $_SESSION['form_amount'], $_SESSION['form_min_order'], $_SESSION['form_expires_at']);
    $_SESSION['message'] = "Coupon added successfully!";
    header("Location: ../manage_coupons.php");
} else {
    $_SESSION['message'] = "Error: " . $stmt->error;
    header("Location: create_coupon.php");
}
