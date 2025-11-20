<?php
session_start();
include('../../config/database.php');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    $_SESSION['message'] = "Access denied. Admins only.";
    header("Location: ../../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: create_review.php");
    exit;
}

$book_id = intval($_POST['book_id']);
$user_id = intval($_POST['user_id']);
$rating = intval($_POST['rating']);
$review_text = trim($_POST['review_text']);

$bad_words = ['Tangina','Putangina','Bobo','Tanga','Gago','Puta','Fuck','Fucker','Motherfucker'];
$pattern = '/\b(' . implode('|', $bad_words) . ')\b/i';

if ($book_id <= 0 || $user_id <= 0 || $rating < 1 || $rating > 5 || empty($review_text)) {
    $_SESSION['message'] = "Invalid input.";
    header("Location: create_review.php");
    exit;
}

if (preg_match($pattern, $review_text)) {
    $_SESSION['message'] = "Review contains inappropriate words.";
    header("Location: create_review.php");
    exit;
}

$stmt = $conn->prepare("INSERT INTO reviews (user_id, book_id, rating, review_text, created_at) VALUES (?, ?, ?, ?, NOW())");
$stmt->bind_param("iiis", $user_id, $book_id, $rating, $review_text);

if ($stmt->execute()) {
    $_SESSION['message'] = "Review created successfully!";
} else {
    $_SESSION['message'] = "Failed to create review.";
}

$stmt->close();
header("Location: manage_reviews.php");
exit;
