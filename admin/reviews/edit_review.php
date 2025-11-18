<?php
session_start();
include('../../config/database.php');
include('../../includes/header.php');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    $_SESSION['message'] = "Access denied. Admins only.";
    header("Location: ../../login.php");
    exit;
}

$review_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($review_id <= 0) {
    echo "<div class='container my-4 alert alert-danger'>Invalid review selected.</div>";
    include('../../includes/footer.php');
    exit;
}

// Fetch review + book info
$sql = "SELECT r.id AS review_id, r.rating, r.review_text, b.id AS book_id, b.title, b.image
        FROM reviews r
        INNER JOIN books b ON r.book_id = b.id
        WHERE r.id = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $review_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<div class='container my-4 alert alert-danger'>Review not found.</div>";
    include('../../includes/footer.php');
    exit;
}

$review = $result->fetch_assoc();
?>

<div class="container my-5">
    <h2>Edit Review for: <?= htmlspecialchars($review['title']) ?></h2>

    <div class="row">
        <!-- Book image -->
        <div class="col-md-4 mb-3 mt-4 text-center">
            <?php if ($review['image']): ?>
                <img src="../../assets/images/books/<?= htmlspecialchars($review['image']) ?>" class="img-fluid rounded" style="max-height: 300px;">
            <?php endif; ?>
        </div>

        <!-- Edit form -->
        <div class="col-md-8">
            <form method="POST" action="update_review.php">
                <input type="hidden" name="review_id" value="<?= $review['review_id'] ?>">

                <div class="mb-3">
                    <label for="rating" class="form-label">Rating (1-5)</label>
                    <select name="rating" id="rating" class="form-select" required>
                        <?php for ($i=1; $i <= 5; $i++): ?>
                            <option value="<?= $i ?>" <?= $i == $review['rating'] ? 'selected' : '' ?>><?= $i ?> star<?= $i>1?'s':'' ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="review_text" class="form-label">Review</label>
                    <textarea name="review_text" id="review_text" rows="6" class="form-control" required><?= htmlspecialchars($review['review_text']) ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Update Review</button>
                <a href="../manage_reviews.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>

<?php
$stmt->close();
include('../../includes/footer.php');
?>
