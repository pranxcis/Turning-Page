<?php
session_start();
include('../../config/database.php');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    $_SESSION['message'] = "Access denied. Admins only.";
    header("Location: ../login.php");
    exit;
}

$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($user_id <= 0) {
    $_SESSION['message'] = "Invalid user ID.";
    header("Location: ../manage_users.php");
    exit;
}

$stmt = $conn->prepare("SELECT profile_picture FROM user_profiles WHERE user_id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($profile_picture);
$stmt->fetch();
$stmt->close();

$stmt = $conn->prepare("DELETE FROM user_profiles WHERE user_id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->close();

$stmt = $conn->prepare("DELETE FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->close();

if ($profile_picture && file_exists("../assets/images/users/$profile_picture")) {
    unlink("../assets/images/users/$profile_picture");
}

$_SESSION['message'] = "User deleted successfully.";
header("Location: ../manage_users.php");
exit;
