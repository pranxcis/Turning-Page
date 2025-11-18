<?php
session_start();
include('../../config/database.php');

// ADMIN ONLY
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    $_SESSION['message'] = "Access denied. Admins only.";
    header("Location: ../login.php");
    exit;
}

// Collect form inputs
$title        = trim($_POST['title'] ?? '');
$author_id    = trim($_POST['author_id'] ?? '');
$genre        = trim($_POST['genre'] ?? '');
$set_price    = trim($_POST['set_price'] ?? '');
$price        = trim($_POST['price'] ?? '');
$description  = trim($_POST['description'] ?? '');
$condition    = trim($_POST['condition'] ?? '');
$stock        = trim($_POST['stock'] ?? '');

// Basic validation (simplified)
$errors = [];

if ($title === '')         $errors['err_title'] = "Title is required.";
if ($author_id === '')     $errors['err_author'] = "Author is required.";
if ($genre === '')         $errors['err_genre'] = "Genre is required.";
if ($set_price === '')     $errors['err_set_price'] = "Set price required.";
if ($price === '')         $errors['err_price'] = "Selling price required.";
if ($condition === '')     $errors['err_condition'] = "Condition required.";
if ($stock === '')         $errors['err_stock'] = "Stock required.";
if ($description === '')   $errors['err_description'] = "Description required.";
if (empty($_FILES['image']['name'])) {
    $errors['err_image'] = "Main image is required.";
}

if (!empty($errors)) {
    foreach ($errors as $k => $v) $_SESSION[$k] = $v;

    // persist input values
    $_SESSION['form_title']      = $title;
    $_SESSION['form_author']     = $author_id;
    $_SESSION['form_genre']      = $genre;
    $_SESSION['form_set_price']  = $set_price;
    $_SESSION['form_price']      = $price;
    $_SESSION['form_description']= $description;
    $_SESSION['form_condition']  = $condition;
    $_SESSION['form_stock']      = $stock;

    header("Location: add_book.php");
    exit;
}

// ----------------------
// UPLOAD: MAIN IMAGE
// ----------------------
$mainImageName = null;

if (!empty($_FILES['image']['name'])) {
    $uploadDir = "../../assets/uploads/books/";

    // Create folder if not exists
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $mainImageName = time() . "_" . basename($_FILES['image']['name']);
    $targetFile = $uploadDir . $mainImageName;

    move_uploaded_file($_FILES['image']['tmp_name'], $targetFile);
}

// ----------------------
// INSERT BOOK MAIN INFO
// ----------------------
$stmt = $conn->prepare("INSERT INTO books 
    (title, author_id, genre, set_price, price, description, image, `condition`, stock, created_at)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");

$stmt->bind_param(
    "sissssssi",
    $title,
    $author_id,
    $genre,
    $set_price,
    $price,
    $description,
    $mainImageName,
    $condition,
    $stock
);

$stmt->execute();
$book_id = $stmt->insert_id;
$stmt->close();

// ----------------------
// UPLOAD MULTIPLE IMAGES
// ----------------------
if (!empty($_FILES['additional_images']['name'][0])) {

    $uploadDir = "../../assets/uploads/book_images/";
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    foreach ($_FILES['additional_images']['name'] as $index => $filename) {
        if ($filename == '') continue;

        $newName = time() . "_" . $filename;
        $targetFile = $uploadDir . $newName;

        move_uploaded_file(
            $_FILES['additional_images']['tmp_name'][$index],
            $targetFile
        );

        // Save to book_images table
        $stmt = $conn->prepare("INSERT INTO book_images (book_id, image_path) VALUES (?, ?)");
        $stmt->bind_param("is", $book_id, $newName);
        $stmt->execute();
        $stmt->close();
    }
}

$_SESSION['message'] = "Book added successfully!";
header("Location: ../manage_books.php");
exit;

?>
