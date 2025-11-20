<?php
session_start();
include('../../config/database.php');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    $_SESSION['message'] = "Access denied. Admins only.";
    header("Location: ../login.php");
    exit;
}

$bookId = intval($_POST['book_id'] ?? 0);
if ($bookId <= 0) {
    $_SESSION['message'] = "Invalid book ID.";
    header("Location: ../manage_books.php");
    exit;
}

$title       = trim($_POST['title'] ?? '');
$authorId    = intval($_POST['author_id'] ?? 0);
$genre       = trim($_POST['genre'] ?? ''); 
$setPrice    = floatval($_POST['set_price'] ?? 0);
$price       = floatval($_POST['price'] ?? 0);
$condition   = trim($_POST['condition'] ?? '');
$stock       = intval($_POST['stock'] ?? 0);
$description = trim($_POST['description'] ?? '');

$errors = [];
if ($title === '')      $errors['err_title'] = "Title is required.";
if ($authorId <= 0)     $errors['err_author'] = "Author is required.";
if ($genre === '')      $errors['err_genre'] = "Genre is required.";
if ($setPrice <= 0)     $errors['err_set_price'] = "Original price is required.";
if ($price <= 0)        $errors['err_price'] = "Selling price is required.";
if ($condition === '')  $errors['err_condition'] = "Condition is required.";
if ($stock < 0)         $errors['err_stock'] = "Stock is required.";
if ($description === '') $errors['err_description'] = "Description is required.";

if (!empty($errors)) {
    foreach ($errors as $k => $v) $_SESSION[$k] = $v;
    $_SESSION['form_title']       = $title;
    $_SESSION['form_author']      = $authorId;
    $_SESSION['form_genre']       = $genre;
    $_SESSION['form_set_price']   = $setPrice;
    $_SESSION['form_price']       = $price;
    $_SESSION['form_condition']   = $condition;
    $_SESSION['form_stock']       = $stock;
    $_SESSION['form_description'] = $description;

    header("Location: edit_book.php?id=$bookId");
    exit;
}

$image_sql = '';
if (isset($_FILES['image']) && $_FILES['image']['error'] != UPLOAD_ERR_NO_FILE) {
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    if (in_array($_FILES['image']['type'], $allowed_types)) {
        $target_dir = "../../assets/images/books/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

        $image = uniqid() . '_' . basename($_FILES['image']['name']);
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_dir . $image)) {
            $image_sql = ", image='" . mysqli_real_escape_string($conn, $image) . "'";
        } else {
            $_SESSION['message'] = "Failed to upload main image.";
            header("Location: edit_book.php?id=$bookId");
            exit;
        }
    } else {
        $_SESSION['err_image'] = "Invalid file type for main image.";
        header("Location: edit_book.php?id=$bookId");
        exit;
    }
}

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

if (!mysqli_query($conn, $sql)) {
    $_SESSION['message'] = "Error updating book: " . mysqli_error($conn);
    header("Location: ../manage_books.php");
    exit;
}

if (!empty($_FILES['additional_images']['name'][0])) {
    $target_dir = "../../assets/images/books/";
    foreach ($_FILES['additional_images']['tmp_name'] as $index => $tmpName) {
        $fileName = $_FILES['additional_images']['name'][$index];
        $fileType = $_FILES['additional_images']['type'][$index];

        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (in_array($fileType, $allowed_types)) {
            $newFile = uniqid() . '_' . basename($fileName);
            if (move_uploaded_file($tmpName, $target_dir . $newFile)) {
                $bookIdEscaped = mysqli_real_escape_string($conn, $bookId);
                mysqli_query($conn, "INSERT INTO book_images (book_id, image_path) VALUES ($bookIdEscaped, '" . mysqli_real_escape_string($conn, $newFile) . "')");
            }
        }
    }
}

$_SESSION['message'] = "Book updated successfully.";
header("Location: ../manage_books.php");
exit;
?>
