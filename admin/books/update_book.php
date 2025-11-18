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

// Get book ID from hidden input
$bookId = intval($_POST['book_id'] ?? 0);
if ($bookId <= 0) {
    $_SESSION['message'] = "Invalid book ID.";
    header("Location: ../manage_books.php");
    exit;
}

// Collect inputs
$title       = trim($_POST['title'] ?? '');
$authorId    = intval($_POST['author_id'] ?? 0);
$genre       = trim($_POST['genre'] ?? '');
$setPrice    = floatval($_POST['set_price'] ?? 0);
$price       = floatval($_POST['price'] ?? 0);
$condition   = trim($_POST['condition'] ?? '');
$stock       = intval($_POST['stock'] ?? 0);
$description = trim($_POST['description'] ?? '');

// Handle image upload
$image_sql = '';
if (isset($_FILES['image']) && $_FILES['image']['error'] != 4) {
    $allowed = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    if (in_array($_FILES['image']['type'], $allowed)) {
        $target_dir = "../../assets/images/books/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

        $image = uniqid() . '_' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $target_dir . $image);

        $image_sql = ", image='" . mysqli_real_escape_string($conn, $image) . "'";
    } else {
        $_SESSION['err_image'] = "Wrong file type";
        header("Location: edit_book.php?id=$bookId");
        exit;
    }
}

// Update book (simple method)
$sql = "UPDATE books SET
        title='" . mysqli_real_escape_string($conn, $title) . "',
        author_id=$authorId,
        genre='" . mysqli_real_escape_string($conn, $genre) . "',
        set_price=$setPrice,
        price=$price,
        `condition`='" . mysqli_real_escape_string($conn, $condition) . "',
        stock=$stock,
        description='" . mysqli_real_escape_string($conn, $description) . "' 
        $image_sql
        WHERE id=$bookId";

if (mysqli_query($conn, $sql)) {
    $_SESSION['message'] = "Book updated successfully.";
} else {
    $_SESSION['message'] = "Error updating book: " . mysqli_error($conn);
}

header("Location: ../manage_books.php");
exit;
