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

$id = intval($_GET['id'] ?? 0);
if($id<=0){
    $_SESSION['message'] = "Invalid review ID";
    header("Location: ../manage_reviews.php");
    exit;
}

// Fetch review
$stmt = $conn->prepare("SELECT * FROM reviews WHERE id=?");
$stmt->bind_param("i",$id);
$stmt->execute();
$result = $stmt->get_result();
$review = $result->fetch_assoc();
if(!$review){
    $_SESSION['message'] = "Review not found";
    header("Location: ../manage_reviews.php");
    exit;
}

// Fetch users and books for dropdowns
$users = $conn->query("SELECT id, username FROM users ORDER BY username ASC");
$books = $conn->query("SELECT id, title FROM books ORDER BY title ASC");
?>

<div class="container my-5">
    <h2>Edit Review</h2>
    <form method="POST" action="update.php">
        <input type="hidden" name="id" value="<?= $review['id'] ?>">

        <div class="mb-3">
            <label>User</label>
            <select name="user_id" class="form-control" required>
                <?php while($u=$users->fetch_assoc()): ?>
                    <option value="<?= $u['id'] ?>" <?= $review['user_id']==$u['id']?"selected":"" ?>><?= htmlspecialchars($u['username']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label>Book</label>
            <select name="book_id" class="form-control" required>
                <?php while($b=$books->fetch_assoc()): ?>
                    <option value="<?= $b['id'] ?>" <?= $review['book_id']==$b['id']?"selected":"" ?>><?= htmlspecialchars($b['title']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label>Rating (1-5)</label>
            <input type="number" min="1" max="5" name="rating" class="form-control" value="<?= htmlspecialchars($review['rating']) ?>" required>
        </div>

        <div class="mb-3">
            <label>Review Text</label>
            <textarea name="review_text" class="form-control" rows="4" required><?= htmlspecialchars($review['review_text']) ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Update Review</button>
        <a href="../manage_reviews.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php include('../../includes/footer.php'); ?>
