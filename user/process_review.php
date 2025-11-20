<?php
session_start();
include('../config/database.php');

if (!isset($_SESSION['user'])) {
    $_SESSION['message'] = "Please login to add a review.";
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];
$book_id = isset($_POST['book_id']) ? intval($_POST['book_id']) : 0;
$rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
$review_text = isset($_POST['review_text']) ? trim($_POST['review_text']) : '';

if ($book_id <= 0 || $rating < 1 || $rating > 5 || empty($review_text)) {
    $_SESSION['message'] = "Invalid review data.";
    header("Location: add_review.php?book_id=$book_id");
    exit;
}

$bad_words = [
    'Tangina',
    'Putangina',
    'Bobo',
    'Tanga',
    'Gago',
    'Puta',
    'Fuck',
    'Fucker',
    'Motherfucker'
];

$pattern = '/' . implode('|', array_map('preg_quote', $bad_words)) . '/i';

$review_text_filtered = preg_replace($pattern, '****', $review_text);

$sql_insert = "INSERT INTO reviews (user_id, book_id, rating, review_text, created_at) VALUES (?, ?, ?, ?, NOW())";
$stmt_insert = $conn->prepare($sql_insert);
$stmt_insert->bind_param("iiis", $user_id, $book_id, $rating, $review_text_filtered);

if ($stmt_insert->execute()) {
    $_SESSION['message'] = "Review added successfully!";
} else {
    $_SESSION['message'] = "Failed to add review. Please try again.";
}

$stmt_insert->close();
header("Location: review_history.php");
exit;
?>
