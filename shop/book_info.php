<?php
session_start();
include('../includes/header.php');
include('../config/database.php');

$book_id = isset($_GET['book_id']) ? intval($_GET['book_id']) : 0;

if ($book_id <= 0) {
    echo "<div class='container my-4'><p>Invalid book selected.</p></div>";
    include('../includes/footer.php');
    exit;
}

// Fetch book details
$sql = "
    SELECT 
        b.id AS bookId,
        b.title,
        b.price,
        b.stock,
        b.description,
        a.name AS author,
        COALESCE(bi.image_path, 'default.jpg') AS image_path
    FROM books b
    LEFT JOIN authors a ON a.id = b.author_id
    LEFT JOIN book_images bi ON bi.book_id = b.id
    WHERE b.id = $book_id
    GROUP BY b.id
";

$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) == 0) {
    echo "<div class='container my-4'><p>Book not found.</p></div>";
    include('../includes/footer.php');
    exit;
}

$book = mysqli_fetch_assoc($result);
?>

<div class="container my-5">
    <div class="row">
        <!-- Book Image -->
        <div class="col-md-4 text-center">
            <img src="../assets/images/books/<?= htmlspecialchars($book['image_path']); ?>" 
                 alt="<?= htmlspecialchars($book['title']); ?>" 
                 style="width:100%; max-width:400px; height:550px; object-fit:fill;">
        </div>

        <!-- Book Details -->
        <div class="col-md-8">
            <h2><?= htmlspecialchars($book['title']); ?></h2>
            <p><strong>Author:</strong> <?= htmlspecialchars($book['author']); ?></p>
            <p><strong>Price:</strong> ₱<?= number_format($book['price'], 2); ?></p>
            <p><strong>Stock:</strong> <?= $book['stock']; ?> available</p>
            <?php if (!empty($book['description'])): ?>
                <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($book['description'])); ?></p>
            <?php endif; ?>

            <!-- Add to Cart -->
            <form method="POST" action="../cart/cart_update.php" class="mb-3">
                <label class="form-label">Quantity:</label>
                <input type="number" name="item_qty" class="form-control mb-2" value="1" min="1" max="<?= $book['stock']; ?>">
                <input type="hidden" name="item_id" value="<?= $book['bookId']; ?>">
                <input type="hidden" name="type" value="add">
                <button type="submit" class="btn btn-primary">Add to Cart</button>
            </form>
        </div>
    </div>

    <!-- Reviews -->
    <div class="row mt-5">
        <div class="col-12">
            <h4>Reviews</h4>
            <?php
            $review_sql = "
                SELECT r.rating, r.review_text, u.email
                FROM reviews r
                JOIN users u ON u.id = r.user_id
                WHERE r.book_id = $book_id
                ORDER BY r.created_at DESC
            ";
            $review_result = mysqli_query($conn, $review_sql);

            if ($review_result && mysqli_num_rows($review_result) > 0) {
                echo '<ul class="list-group">';
                while ($review = mysqli_fetch_assoc($review_result)) {
                    echo '<li class="list-group-item">';
                    echo '<strong>' . htmlspecialchars($review['email']) . '</strong> - ';
                    echo str_repeat('★', intval($review['rating'])) . str_repeat('☆', 5 - intval($review['rating'])) . '<br>';
                    echo htmlspecialchars($review['review_text']);
                    echo '</li>';
                }
                echo '</ul>';
            } else {
                echo '<p>No reviews yet for this book.</p>';
            }
            ?>
        </div>
    </div>
</div>

<?php include('../includes/footer.php'); ?>
