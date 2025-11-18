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

// Get book ID
$bookId = intval($_GET['id'] ?? 0);

if ($bookId <= 0) {
    $_SESSION['message'] = "Invalid book ID.";
    header("Location: ../manage_books.php");
    exit;
}

// Get book image to delete file
$stmt = $conn->prepare("SELECT image FROM books WHERE id = ?");
$stmt->bind_param("i", $bookId);
$stmt->execute();
$stmt->bind_result($image);
$stmt->fetch();
$stmt->close();

// Delete book from database
$stmt = $conn->prepare("DELETE FROM books WHERE id = ?");
$stmt->bind_param("i", $bookId);
if ($stmt->execute()) {
    // Delete image file if exists
    if ($image && file_exists("../assets/images/books/" . $image)) {
        unlink("../assets/images/books/" . $image);
    }
    $_SESSION['message'] = "Book deleted successfully.";
} else {
    $_SESSION['message'] = "Error deleting book.";
}
$stmt->close();

header("Location: ../manage_books.php");
exit;
