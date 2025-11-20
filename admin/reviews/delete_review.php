<?php
    session_start();
    include('../../config/database.php');

    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
        $_SESSION['message'] = "Access denied. Admins only.";
        header("Location: ../../login.php");
        exit;
    }

    $review_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    if ($review_id > 0) {
        $stmt = $conn->prepare("DELETE FROM reviews WHERE id = ?");
        $stmt->bind_param("i", $review_id);
        $stmt->execute();
        $stmt->close();
        $_SESSION['message'] = "Review deleted successfully.";
    }

    header("Location: ../manage_reviews.php");
exit;
