<?php
session_start();
include('../../config/database.php');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    $_SESSION['message'] = "Access denied. Admins only.";
    header("Location: ../login.php");
    exit;
}

$bookId = intval($_GET['id'] ?? 0);

if ($bookId <= 0) {
    $_SESSION['message'] = "Invalid book ID.";
    header("Location: ../manage_books.php");
    exit;
}

$stmt = $conn->prepare("SELECT image FROM books WHERE id = ?");
$stmt->bind_param("i", $bookId);
$stmt->execute();
$stmt->bind_result($image);
$stmt->fetch();
$stmt->close();

$stmt = $conn->prepare("DELETE FROM books WHERE id = ?");
$stmt->bind_param("i", $bookId);
if ($stmt->execute()) {
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
