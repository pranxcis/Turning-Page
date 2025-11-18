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

// Get author ID
$authorId = intval($_GET['id'] ?? 0);

if ($authorId <= 0) {
    $_SESSION['message'] = "Invalid author ID.";
    header("Location: ../manage_authors.php");
    exit;
}

// Check if author has books
$stmt = $conn->prepare("SELECT COUNT(*) FROM books WHERE author_id = ?");
$stmt->bind_param("i", $authorId);
$stmt->execute();
$stmt->bind_result($bookCount);
$stmt->fetch();
$stmt->close();

if ($bookCount > 0) {
    $_SESSION['message'] = "Cannot delete author. Remove their books first.";
    header("Location: ../manage_authors.php");
    exit;
}

// Delete author
$stmt = $conn->prepare("DELETE FROM authors WHERE id = ?");
$stmt->bind_param("i", $authorId);

if ($stmt->execute()) {
    $_SESSION['message'] = "Author deleted successfully.";
} else {
    $_SESSION['message'] = "Error deleting author.";
}

$stmt->close();
header("Location: ../manage_authors.php");
exit;
