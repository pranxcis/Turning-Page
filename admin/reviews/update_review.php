<?php
session_start();
include('../../config/database.php');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    $_SESSION['message'] = "Access denied. Admins only.";
    header("Location: ../../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['review_id'])) {
    header("Location: manage_reviews.php");
    exit;
}

$review_id = intval($_POST['review_id']);
$rating = intval($_POST['rating']);
$review_text = trim($_POST['review_text']);

// Bad words list
$bad_words = ['Tangina','Putangina','Bobo','Tanga','Gago','Puta','Fuck','Fucker','Motherfucker'];
$pattern = '/\b(' . implode('|', $bad_words) . ')\b/i';

if ($rating < 1 || $rating > 5 || empty($review_text) || preg_match($pattern, $review_text)) {
    $_SESSION['message'] = "Invalid input or contains inappropriate words.";
    header("Location: edit_review.php?id={$review_id}");
    exit;
}

$stmt = $conn->prepare("UPDATE reviews SET rating = ?, review_text = ?, created_at = NOW() WHERE id = ?");
$stmt->bind_param("isi", $rating, $review_text, $review_id);

if ($stmt->execute()) {
    $_SESSION['message'] = "Review updated successfully!";
} else {
    $_SESSION['message'] = "Failed to update review.";
}

$stmt->close();
header("Location: ../manage_reviews.php");
exit;
