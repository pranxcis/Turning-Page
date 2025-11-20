<?php
session_start();
include("../config/database.php");
include("../includes/header.php");

if (!isset($_POST['submit'])) {
    header("Location: register.php");
    exit();
}

$email       = trim($_POST['email']);
$password    = trim($_POST['password']);
$confirmPass = trim($_POST['confirmPass']);

$_SESSION['errors'] = [];

if (empty($email)) {
    $_SESSION['errors']['email'] = "Email is required.";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['errors']['email'] = "Invalid email format.";
}

if (empty($password)) {
    $_SESSION['errors']['password'] = "Password is required.";
} elseif (strlen($password) < 6) {
    $_SESSION['errors']['password'] = "Password must be at least 6 characters.";
}

if ($password !== $confirmPass) {
    $_SESSION['errors']['confirmPass'] = "Passwords do not match.";
}

if (!empty($_SESSION['errors'])) {
    header("Location: register.php");
    exit();
}

$check = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
$check->bind_param("s", $email);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    $_SESSION['message'] = "Email already registered.";
    header("Location: register.php");
    exit();
}

$hashedPass = password_hash($password, PASSWORD_DEFAULT);

$username = strstr($email, "@", true);

$stmt = $conn->prepare("INSERT INTO users (email, password, username, role, status) VALUES (?, ?, ?, 'customer', 'active')");
$stmt->bind_param("sss", $email, $hashedPass, $username);

if (!$stmt->execute()) {
    $_SESSION['message'] = "Registration failed. Try again.";
    header("Location: register.php");
    exit();
}

$user_id = $stmt->insert_id;

$profile = $conn->prepare("
    INSERT INTO user_profiles 
    (user_id, last_name, first_name, middle_initial, phone, address, town, zipcode, profile_picture)
    VALUES (?, '', '', '', '', '', '', '', '')
");
$profile->bind_param("i", $user_id);
$profile->execute();

$_SESSION['user'] = [
    'id'    => $user_id,
    'email' => $email,
    'role'  => 'customer',
    'name'  => $username
];


header("Location: profile.php");
exit();
