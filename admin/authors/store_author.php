<?php
session_start();
include('../../config/database.php');

// ------------------------
// ADMIN ACCESS ONLY
// ------------------------
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    $_SESSION['message'] = "Access denied. Admins only.";
    header("Location: ../login.php");
    exit;
}

// Get and trim POST data
$name = trim($_POST['name'] ?? '');
$bio  = trim($_POST['bio'] ?? '');

// Store old values in session for repopulating form if needed
$_SESSION['form_name'] = $name;
$_SESSION['form_bio']  = $bio;

$hasError = false;

if (empty($name)) {
    $_SESSION['err_name'] = "Please enter the author's name.";
    $hasError = true;
}

if (empty($bio)) {
    $_SESSION['err_bio'] = "Please enter a short biography.";
    $hasError = true;
}

if ($hasError) {
    header("Location: create_author.php");
    exit;
}

$stmt = $conn->prepare("INSERT INTO authors (name, bio) VALUES (?, ?)");
$stmt->bind_param("ss", $name, $bio);

if ($stmt->execute()) {
    unset($_SESSION['form_name'], $_SESSION['form_bio']);
    $_SESSION['message'] = "Author added successfully!";
    header("Location: ../manage_authors.php");
    exit;
} else {
    $_SESSION['err_name'] = "Database error: Could not save author.";
    header("Location: create_author.php");
    exit;
}
