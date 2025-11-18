<?php
session_start();
$pageTitle = "Create Review";

include('../../config/database.php');

// ------------------------
// ADMIN ACCESS ONLY
// ------------------------
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    $_SESSION['message'] = "Access denied. Admins only.";
    header("Location: ../../login.php");
    exit;
}

// ------------------------
// FETCH USERS
// ------------------------
$sql_users = "SELECT u.id, u.username, p.first_name, p.last_name
              FROM users u
              LEFT JOIN user_profiles p ON u.id = p.user_id
              ORDER BY u.username ASC";
$result_users = $conn->query($sql_users);

// ------------------------
// FETCH BOOKS
// ------------------------
$sql_books = "SELECT id, title FROM books ORDER BY title ASC";
$result_books = $conn->query($sql_books);

// ------------------------
// HANDLE FORM SUBMISSION
// ------------------------
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = intval($_POST['user_id']);
    $book_id = intval($_POST['book_id']);
    $rating = intval($_POST['rating']);
    $review_text = trim($_POST['review_text']);

    // Bad words list
    $badWords = ['Tangina','Putangina','Bobo','Tanga','Gago','Puta','Fuck','Fucker','Motherfucker'];
    $pattern = '/\b(' . implode('|', array_map('preg_quote', $badWords)) . ')\b/i';

    if ($rating < 1 || $rating > 5) {
        $message = "Rating must be between 1 and 5.";
    } elseif (empty($review_text)) {
        $message = "Please enter a review.";
    } elseif (preg_match($pattern, $review_text)) {
        $message = "Your review contains inappropriate language.";
    } else {
        $stmt_insert = $conn->prepare("INSERT INTO reviews (user_id, book_id, rating, review_text, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt_insert->bind_param("iiis", $user_id, $book_id, $rating, $review_text);
        if ($stmt_insert->execute()) {
            $_SESSION['message'] = "Review created successfully!";
            header("Location: ../manage_reviews.php");
            exit;
        } else {
            $message = "Failed to create review. Please try again.";
        }
    }
}
include('../../includes/header.php');
?>

<div class="container my-5">
    <h2 class="mb-4"><?= $pageTitle ?></h2>

    <?php if ($message): ?>
        <div class="alert alert-warning"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <!-- Select User -->
        <div class="mb-3">
            <label for="user_id" class="form-label">User</label>
            <select name="user_id" id="user_id" class="form-select" required>
                <option value="">Select User</option>
                <?php while($user = $result_users->fetch_assoc()): ?>
                    <option value="<?= $user['id'] ?>">
                        <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name'] . ' (' . $user['username'] . ')') ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <!-- Select Book -->
        <div class="mb-3">
            <label for="book_id" class="form-label">Book</label>
            <select name="book_id" id="book_id" class="form-select" required>
                <option value="">Select Book</option>
                <?php while($book = $result_books->fetch_assoc()): ?>
                    <option value="<?= $book['id'] ?>"><?= htmlspecialchars($book['title']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <!-- Rating -->
        <div class="mb-3">
            <label for="rating" class="form-label">Rating (1-5)</label>
            <select name="rating" id="rating" class="form-select" required>
                <option value="">Select Rating</option>
                <?php for($i=1; $i<=5; $i++): ?>
                    <option value="<?= $i ?>"><?= $i ?> star<?= $i>1?'s':'' ?></option>
                <?php endfor; ?>
            </select>
        </div>

        <!-- Review Text -->
        <div class="mb-3">
            <label for="review_text" class="form-label">Review</label>
            <textarea name="review_text" id="review_text" rows="5" class="form-control" required></textarea>
        </div>

        <button type="submit" class="btn btn-success">Create Review</button>
        <a href="../manage_reviews.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php include('../../includes/footer.php'); ?>
