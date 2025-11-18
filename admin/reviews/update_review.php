<?php
session_start();
include('../../config/database.php');

// Admin check
if(!isset($_SESSION['user']) || $_SESSION['user']['role']!=='admin'){
    $_SESSION['message'] = "Access denied. Admins only.";
    header("Location: ../login.php");
    exit;
}

$id = intval($_POST['id']);
$user_id = $_POST['user_id'];
$book_id = $_POST['book_id'];
$rating = $_POST['rating'];
$review_text = trim($_POST['review_text']);

// Validation
$errors = [];
if(!$user_id) $errors[] = "User is required";
if(!$book_id) $errors[] = "Book is required";
if(!is_numeric($rating) || $rating<1 || $rating>5) $errors[] = "Rating must be 1-5";
if(!$review_text) $errors[] = "Review text is required";

if($errors){
    $_SESSION['message'] = implode('<br>',$errors);
    header("Location: edit.php?id={$id}");
    exit;
}

// Update
$stmt = $conn->prepare("UPDATE reviews SET user_id=?, book_id=?, rating=?, review_text=? WHERE id=?");
$stmt->bind_param("iiisi", $user_id, $book_id, $rating, $review_text, $id);

if($stmt->execute()){
    $_SESSION['message'] = "Review updated successfully!";
    header("Location: ../manage_reviews.php");
}else{
    $_SESSION['message'] = "Error: ".$stmt->error;
    header("Location: edit.php?id={$id}");
}
    