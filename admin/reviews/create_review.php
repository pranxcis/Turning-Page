<?php
session_start();
include('../../includes/header.php');
include('../../config/database.php');

// Admin check
if(!isset($_SESSION['user']) || $_SESSION['user']['role']!=='admin'){
    $_SESSION['message'] = "Access denied. Admins only.";
    header("Location: ../login.php");
    exit;
}

// Load old input values
$user_id = $_SESSION['form_user_id'] ?? '';
$book_id = $_SESSION['form_book_id'] ?? '';
$rating = $_SESSION['form_rating'] ?? '';
$review_text = $_SESSION['form_review_text'] ?? '';

unset($_SESSION['form_user_id'], $_SESSION['form_book_id'], $_SESSION['form_rating'], $_SESSION['form_review_text']);

// Fetch users and books for dropdowns
$users = $conn->query("SELECT id, username FROM users ORDER BY username ASC");
$books = $conn->query("SELECT id, title FROM books ORDER BY title ASC");
?>

<div class="container my-5">
    <h2>Add New Review</h2>
    <form method="POST" action="store.php">
        <div class="mb-3">
            <label>User</label>
            <select name="user_id" class="form-control" required>
                <option value="">-- Select User --</option>
                <?php while($u = $users->fetch_assoc()): ?>
                    <option value="<?= $u['id'] ?>" <?= ($user_id==$u['id'])?"selected":"" ?>><?= htmlspecialchars($u['username']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label>Book</label>
            <select name="book_id" class="form-control" required>
                <option value="">-- Select Book --</option>
                <?php while($b = $books->fetch_assoc()): ?>
                    <option value="<?= $b['id'] ?>" <?= ($book_id==$b['id'])?"selected":"" ?>><?= htmlspecialchars($b['title']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label>Rating (1-5)</label>
            <input type="number" min="1" max="5" name="rating" class="form-control" value="<?= htmlspecialchars($rating) ?>" required>
        </div>

        <div class="mb-3">
            <label>Review Text</label>
            <textarea name="review_text" class="form-control" rows="4" required><?= htmlspecialchars($review_text) ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Save Review</button>
        <a href="../manage_reviews.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php include('../../includes/footer.php'); ?>
