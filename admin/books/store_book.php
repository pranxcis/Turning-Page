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

// Store POST data in session to repopulate form if error occurs
$_SESSION['form_title']       = trim($_POST['title'] ?? '');
$_SESSION['form_author']      = $_POST['author_id'] ?? '';
$_SESSION['form_genre']       = trim($_POST['genre'] ?? '');
$_SESSION['form_set_price']   = trim($_POST['set_price'] ?? '');
$_SESSION['form_price']       = trim($_POST['price'] ?? '');
$_SESSION['form_description'] = trim($_POST['description'] ?? '');
$_SESSION['form_condition']   = $_POST['condition'] ?? '';
$_SESSION['form_stock']       = trim($_POST['stock'] ?? '');

// ------------------------
// VALIDATION
// ------------------------
$hasError = false;

if (empty($_SESSION['form_title'])) {
    $_SESSION['err_title'] = "Please enter book title";
    $hasError = true;
}
if (empty($_SESSION['form_author'])) {
    $_SESSION['err_author'] = "Please select an author";
    $hasError = true;
}
if (empty($_SESSION['form_genre'])) {
    $_SESSION['err_genre'] = "Please enter genre";
    $hasError = true;
}
if (!is_numeric($_SESSION['form_set_price']) || $_SESSION['form_set_price'] < 0) {
    $_SESSION['err_set_price'] = "Invalid set price";
    $hasError = true;
}
if (!is_numeric($_SESSION['form_price']) || $_SESSION['form_price'] < 0) {
    $_SESSION['err_price'] = "Invalid selling price";
    $hasError = true;
}
if (empty($_SESSION['form_description'])) {
    $_SESSION['err_description'] = "Please enter description";
    $hasError = true;
}
if (empty($_SESSION['form_condition'])) {
    $_SESSION['err_condition'] = "Select condition";
    $hasError = true;
}
if (!is_numeric($_SESSION['form_stock']) || $_SESSION['form_stock'] < 0) {
    $_SESSION['err_stock'] = "Invalid stock";
    $hasError = true;
}

// ------------------------
// IMAGE UPLOAD
// ------------------------
$target = '';
if (isset($_FILES['image']) && $_FILES['image']['error'] != 4) {
    $allowed = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    if (in_array($_FILES['image']['type'], $allowed)) {
        $target_dir = "../../assets/images/books/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

        // Generate unique filename
        $filename = uniqid() . '_' . $_FILES['image']['name'];
        $target = $target_dir . $filename;

        // Move uploaded file
        move_uploaded_file($_FILES['image']['tmp_name'], $target);

        // Only store filename in DB
        $target = $filename;

    } else {
        $_SESSION['err_image'] = "Wrong file type";
        $hasError = true;
    }
} else {
    $_SESSION['err_image'] = "Please upload a book image";
    $hasError = true;
}

// ------------------------
// REDIRECT IF ERROR
// ------------------------
if ($hasError) {
    header("Location: create_book.php");
    exit;
}

// ------------------------
// INSERT INTO DATABASE
// ------------------------
$sql = "INSERT INTO books (title, author_id, genre, set_price, price, description, image, `condition`, stock, created_at) 
        VALUES ('{$_SESSION['form_title']}', {$_SESSION['form_author']}, '{$_SESSION['form_genre']}', 
                {$_SESSION['form_set_price']}, {$_SESSION['form_price']}, '{$_SESSION['form_description']}', 
                '{$target}', '{$_SESSION['form_condition']}', {$_SESSION['form_stock']}, NOW())";

$result = mysqli_query($conn, $sql);

if ($result) {
    // Clear form session
    unset($_SESSION['form_title'], $_SESSION['form_author'], $_SESSION['form_genre'], $_SESSION['form_set_price'], $_SESSION['form_price'], $_SESSION['form_description'], $_SESSION['form_condition'], $_SESSION['form_stock']);
    $_SESSION['message'] = "Book added successfully!";
    header("Location: ../manage_books.php");
    exit;
} else {
    $_SESSION['message'] = "Database error: " . mysqli_error($conn);
    header("Location: create_book.php");
    exit;
}
