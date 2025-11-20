<?php
session_start();
include('../../config/database.php');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    $_SESSION['message'] = "Access denied. Admins only.";
    header("Location: ../login.php");
    exit;
}

$id = intval($_POST['id']);
$code = trim($_POST['code']);
$type = $_POST['type'];
$amount = $_POST['amount'];
$min_order = $_POST['min_order'];
$expires_at = $_POST['expires_at'];

$errors = [];
if(!$code) $errors[] = "Code is required";
if(!$type) $errors[] = "Type is required";
if(!is_numeric($amount) || $amount<0) $errors[] = "Amount must be positive";
if(!is_numeric($min_order) || $min_order<0) $errors[] = "Minimum order must be positive";
if(!$expires_at) $errors[] = "Expiry date is required";

if($errors){
    $_SESSION['message'] = implode('<br>', $errors);
    header("Location: edit.php?id={$id}");
    exit;
}

$stmt = $conn->prepare("UPDATE coupons SET code=?, type=?, amount=?, min_order=?, expires_at=? WHERE id=?");
$stmt->bind_param("ssddsi",$code,$type,$amount,$min_order,$expires_at,$id);
if($stmt->execute()){
    $_SESSION['message'] = "Coupon updated successfully!";
    header("Location: ../manage_coupons.php");
}else{
    $_SESSION['message'] = "Error: ".$stmt->error;
    header("Location: edit.php?id={$id}");
}
