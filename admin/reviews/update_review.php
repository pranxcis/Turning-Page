<?php
session_start();
include('../../config/database.php');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    $_SESSION['message'] = "Access denied. Admins only.";
    header("Location: ../../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['review_id'])) {
    $_SESSION['message'] = "Invalid request.";
    header("Location: manage_reviews.php");
    exit;
}

$review_id   = intval($_POST['review_id']);
$rating      = intval($_POST['rating']);
$review_text = trim($_POST['review_text']);

$bad_words = ['Tangina','Putangina','Bobo','Tanga','Gago','Puta','Fuck','Fucker','Motherfucker'];
$pattern = '/\b(' . implode('|', $bad_words) . ')\b/i';

if ($rating < 1 || $rating > 5 || empty($review_text) || preg_match($pattern, $review_text)) {
    $_SESSION['message'] = "Invalid input or contains inappropriate words.";
    header("Location: edit_review.php?id={$review_id}");
    exit;
}

$stmt = $conn->prepare("SELECT id FROM reviews WHERE id = ?");
$stmt->bind_param("i", $review_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['message'] = "Review not found.";
    $stmt->close();
    header("Location: manage_reviews.php");
    exit;
}
$stmt->close();

$stmt = $conn->prepare("UPDATE reviews SET rating = ?, review_text = ? WHERE id = ?");
$stmt->bind_param("isi", $rating, $review_text, $review_id);

if ($stmt->execute()) {
    $_SESSION['message'] = "Review updated successfully!";
} else {
    $_SESSION['message'] = "Failed to update review.";
}

$stmt->close();
header("Location: ../manage_reviews.php");
exit;
