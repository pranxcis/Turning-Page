<?php
session_start();
include('../../config/database.php');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    $_SESSION['message'] = "Access denied. Admins only.";
    header("Location: ../login.php");
    exit;
}

$authorId = intval($_POST['author_id'] ?? 0);
$name = trim($_POST['name'] ?? '');
$bio  = trim($_POST['bio'] ?? '');

$hasError = false;

if ($authorId <= 0) {
    $_SESSION['message'] = "Invalid author ID.";
    header("Location: ../manage_authors.php");
    exit;
}

if (empty($name)) {
    $_SESSION['err_name'] = "Author name is required.";
    $hasError = true;
}

if ($hasError) {
    $_SESSION['form_name'] = $name;
    $_SESSION['form_bio']  = $bio;
    header("Location: ../edit_author.php?id={$authorId}");
    exit;
}

$stmt = $conn->prepare("UPDATE authors SET name = ?, bio = ? WHERE id = ?");
$stmt->bind_param("ssi", $name, $bio, $authorId);

if ($stmt->execute()) {
    $_SESSION['message'] = "Author updated successfully.";
} else {
    $_SESSION['message'] = "Error updating author.";
}

$stmt->close();
header("Location: ../manage_authors.php");
exit;
