<?php
session_start();
include('../config/database.php');

if (!isset($_SESSION['user'])) {
    $_SESSION['message'] = "Please login to edit a review.";
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];
$review_id = isset($_GET['review_id']) ? intval($_GET['review_id']) : 0;

if ($review_id <= 0) {
    echo "<div class='container my-4'><p>Invalid review selected.</p></div>";
    include('../includes/footer.php');
    exit;
}

// Fetch review and book info
$sql = "
    SELECT r.rating, r.review_text, b.id AS book_id, b.title, b.image
    FROM reviews r
    INNER JOIN books b ON r.book_id = b.id
    WHERE r.id = ? AND r.user_id = ?
    LIMIT 1
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $review_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<div class='container my-4'><div class='alert alert-danger'>Review not found or access denied.</div></div>";
    include('../includes/footer.php');
    exit;
}

$review = $result->fetch_assoc();

// Handle form submission
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating = intval($_POST['rating']);
    $review_text = trim($_POST['review_text']);

    if ($rating < 1 || $rating > 5) {
        $message = "Rating must be between 1 and 5.";
    } elseif (empty($review_text)) {
        $message = "Please enter a review.";
    } else {
        $sql_update = "UPDATE reviews SET rating = ?, review_text = ?, created_at = NOW() WHERE id = ? AND user_id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("isii", $rating, $review_text, $review_id, $user_id);
        if ($stmt_update->execute()) {
            $_SESSION['message'] = "Review updated successfully!";
            header("Location: review_history.php");
            exit;
        } else {
            $message = "Failed to update review. Please try again.";
        }
    }
}

include('../includes/header.php');
?>

<div class="container my-5 mb-4">
    <h2 class="mb-4">Edit Review for: <?= htmlspecialchars($review['title']) ?></h2>

    <?php if ($message): ?>
        <div class="alert alert-warning"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <div class="row">
        <!-- Left: Book Image -->
        <div class="col-md-4 mb-3 pt-5 text-center">
            <?php if ($review['image']): ?>
                <img src="../assets/images/books/<?= htmlspecialchars($review['image']) ?>" 
                     alt="<?= htmlspecialchars($review['title']) ?>" 
                     class="img-fluid rounded" style="max-height: 300px;">
            <?php endif; ?>
        </div>

                <!-- Right: Edit Form -->
        <div class="col-md-8">
            <form method="POST" action="process_review.php">
                <!-- Hidden inputs -->
                <input type="hidden" name="review_id" value="<?= $review_id ?>">
                <input type="hidden" name="book_id" value="<?= $review['book_id'] ?>">

                <!-- Rating field -->
                <div class="mb-3">
                    <label for="rating" class="form-label">Rating (1-5)</label>
                    <select name="rating" id="rating" class="form-select" required>
                        <?php for($i=1; $i<=5; $i++): ?>
                            <option value="<?= $i ?>" <?= $i == $review['rating'] ? 'selected' : '' ?>>
                                <?= $i ?> star<?= $i > 1 ? 's' : '' ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>

                <!-- Review textarea -->
                <div class="mb-3">
                    <label for="review_text" class="form-label">Review</label>
                    <textarea name="review_text" id="review_text" rows="6" class="form-control" required><?= htmlspecialchars($review['review_text']) ?></textarea>
                </div>

                <!-- Buttons -->
                <button type="submit" class="btn btn-primary">Update Review</button>
                <a href="review_history.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>

    </div>
</div>

<?php
$stmt->close();
if (isset($stmt_update)) $stmt_update->close();
include('../includes/footer.php');
?>
