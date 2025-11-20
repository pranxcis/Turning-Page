<?php
session_start();
include('../config/database.php');

if (!isset($_SESSION['user'])) {
    $_SESSION['message'] = "Please login to add a review.";
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];

$book_id = isset($_GET['book_id']) ? intval($_GET['book_id']) : 0;
if ($book_id <= 0) {
    echo "<div class='container my-4'><p>Invalid book selected.</p></div>";
    include('../includes/footer.php');
    exit;
}

$sql_purchase = "
    SELECT COUNT(*) as purchased
    FROM order_items oi
    INNER JOIN orders o ON oi.order_id = o.id
    WHERE o.user_id = ? AND oi.book_id = ?
";
$stmt_purchase = $conn->prepare($sql_purchase);
$stmt_purchase->bind_param("ii", $user_id, $book_id);
$stmt_purchase->execute();
$result_purchase = $stmt_purchase->get_result();
$row_purchase = $result_purchase->fetch_assoc();

if ($row_purchase['purchased'] == 0) {
    echo "<div class='container my-4'><div class='alert alert-danger'>You can only review books you purchased.</div></div>";
    include('../includes/footer.php');
    exit;
}

$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating = intval($_POST['rating']);
    $review_text = trim($_POST['review_text']);

    if ($rating < 1 || $rating > 5) {
        $message = "Rating must be between 1 and 5.";
    } elseif (empty($review_text)) {
        $message = "Please enter a review.";
    } else {
        $sql_insert = "INSERT INTO reviews (user_id, book_id, rating, review_text, created_at) VALUES (?, ?, ?, ?, NOW())";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("iiis", $user_id, $book_id, $rating, $review_text);
        if ($stmt_insert->execute()) {
            $_SESSION['message'] = "Review added successfully!";
            header("Location: order_history.php"); // redirect to order history
            exit;
        } else {
            $message = "Failed to add review. Please try again.";
        }
    }
}


$sql_book = "SELECT title, image FROM books WHERE id = ? LIMIT 1";
$stmt_book = $conn->prepare($sql_book);
$stmt_book->bind_param("i", $book_id);
$stmt_book->execute();
$result_book = $stmt_book->get_result();
$book = $result_book->fetch_assoc();

include('../includes/header.php');
?>

<div class="container my-5 mb-4">
    <h2 class="mb-4">Add Review for: <?= htmlspecialchars($book['title']) ?></h2>

    <?php if ($message): ?>
        <div class="alert alert-warning"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>


    <div class="row">
        <div class="col-md-4 mb-3 pt-5 text-center">
            <?php if ($book['image']): ?>
                <img src="../assets/images/books/<?= htmlspecialchars($book['image']) ?>" 
                     alt="<?= htmlspecialchars($book['title']) ?>" 
                     class="img-fluid rounded"
                     style="max-height: 300px;">
            <?php endif; ?>
        </div>

        <div class="col-md-8">
            <form method="POST" action="process_review.php">
                <input type="hidden" name="book_id" value="<?= $book_id ?>">

                <div class="mb-3">
                    <label for="rating" class="form-label">Rating (1-5)</label>
                    <select name="rating" id="rating" class="form-select" required>
                        <option value="">Select rating</option>
                        <?php for($i = 1; $i <= 5; $i++): ?>
                            <option value="<?= $i ?>"><?= $i ?> star<?= $i > 1 ? 's' : '' ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="review_text" class="form-label">Review</label>
                    <textarea name="review_text" id="review_text" rows="6" class="form-control" required></textarea>
                </div>

                <button type="submit" class="btn btn-success">Submit Review</button>
                <a href="order_history.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>

    </div>
</div>

<?php
$stmt_purchase->close();
$stmt_book->close();

// Only close $stmt_insert if it exists
if (isset($stmt_insert)) {
    $stmt_insert->close();
}

include('../includes/footer.php');
?>
