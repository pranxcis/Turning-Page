<?php
session_start();
include('../../config/database.php');

// Admin check
if(!isset($_SESSION['user']) || $_SESSION['user']['role']!=='admin'){
    $_SESSION['message'] = "Access denied. Admins only.";
    header("Location: ../login.php");
    exit;
}

// Collect POST data
$user_id = $_POST['user_id'];
$book_id = $_POST['book_id'];
$rating = $_POST['rating'];
$review_text = trim($_POST['review_text']);

// Save old values
$_SESSION['form_user_id'] = $user_id;
$_SESSION['form_book_id'] = $book_id;
$_SESSION['form_rating'] = $rating;
$_SESSION['form_review_text'] = $review_text;

// Basic validation
$errors = [];
if(!$user_id) $errors[] = "User is required";
if(!$book_id) $errors[] = "Book is required";
if(!is_numeric($rating) || $rating<1 || $rating>5) $errors[] = "Rating must be 1-5";
if(!$review_text) $errors[] = "Review text is required";

if($errors){
    $_SESSION['message'] = implode('<br>',$errors);
    header("Location: create.php");
    exit;
}

// Insert into DB
$stmt = $conn->prepare("INSERT INTO reviews (user_id, book_id, rating, review_text, created_at) VALUES (?,?,?,?,NOW())");
$stmt->bind_param("iiis", $user_id, $book_id, $rating, $review_text);

if($stmt->execute()){
    unset($_SESSION['form_user_id'], $_SESSION['form_book_id'], $_SESSION['form_rating'], $_SESSION['form_review_text']);
    $_SESSION['message'] = "Review added successfully!";
    header("Location: ../manage_reviews.php");
}else{
    $_SESSION['message'] = "Error: ".$stmt->error;
    header("Location: create.php");
}
