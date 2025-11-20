<?php
session_start();
include('../includes/header.php');
include('../config/database.php');

if (!isset($_SESSION['user'])) {
    $_SESSION['message'] = "Please login to view your review history.";
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];

$sql = "
    SELECT r.id AS review_id, r.rating, r.review_text, r.created_at,
           b.id AS book_id, b.title, b.image
    FROM reviews r
    INNER JOIN books b ON r.book_id = b.id
    WHERE r.user_id = ?
    ORDER BY r.created_at DESC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$reviewCount = $result->num_rows;
?>

<div class="container my-5">
    <h2 class="mb-4">My Review History (<?= $reviewCount ?>)</h2>

    <?php if ($reviewCount > 0): ?>
        <div class="row g-4">
            <?php while ($review = $result->fetch_assoc()): ?>
                <div class="col-12">
                    <div class="card p-3 shadow-sm d-flex flex-row align-items-start gap-3">

                        <div class="flex-shrink-0">
                            <?php if ($review['image']): ?>
                                <img src="../assets/images/books/<?= htmlspecialchars($review['image']) ?>" 
                                     alt="<?= htmlspecialchars($review['title']) ?>" 
                                     style="height:100px; width:auto;" class="rounded">
                            <?php else: ?>
                                <div class="bg-secondary text-white d-flex align-items-center justify-content-center" 
                                     style="width:100px; height:100px;">No Image</div>
                            <?php endif; ?>
                        </div>

                        <div class="flex-grow-1">
                            <h5 class="mb-1"><?= htmlspecialchars($review['title']) ?></h5>

                            <p class="mb-1">
                                <strong>Rating:</strong> <?= $review['rating'] ?> star<?= $review['rating'] > 1 ? 's' : '' ?>
                            </p>

                            <p class="mb-1"><strong>Review:</strong> <?= htmlspecialchars($review['review_text']) ?></p>

                            <p class="mb-0 text-muted"><small>Reviewed on <?= date("F d, Y h:i A", strtotime($review['created_at'])) ?></small></p>
                        </div>

                        <div class="flex-shrink-0 d-flex flex-column gap-2">
                            <a href="add_review.php?book_id=<?= $review['book_id'] ?>" class="btn btn-sm btn-outline-success">
                                Add Another Review
                            </a>
                            <a href="edit_review.php?review_id=<?= $review['review_id'] ?>" class="btn btn-sm btn-outline-primary">
                                Edit Review
                            </a>
                        </div>

                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-warning text-center">You haven't submitted any reviews yet.</div>
    <?php endif; ?>
</div>

<?php
$stmt->close();
include('../includes/footer.php');
?>
