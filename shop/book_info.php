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

$stmt = $conn->prepare("
    SELECT b.id AS bookId, b.title, b.price, b.stock, b.description, b.genre, b.condition, b.author_id, a.name AS author
    FROM books b
    LEFT JOIN authors a ON a.id = b.author_id
    WHERE b.id = ?
");
$stmt->bind_param('i', $book_id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result || $result->num_rows === 0) {
    echo "<div class='container my-4'><p>Book not found.</p></div>";
    include('../includes/footer.php');
    exit;
}

$book = $result->fetch_assoc();
$stmt->close();

// Fetch all book images
$images_stmt = $conn->prepare("SELECT image_path FROM book_images WHERE book_id=?");
$images_stmt->bind_param('i', $book_id);
$images_stmt->execute();
$images_result = $images_stmt->get_result();

$book_images = [];
while ($img = $images_result->fetch_assoc()) {
    $book_images[] = $img['image_path'];
}
if (empty($book_images)) $book_images[] = 'default.jpg';
$images_stmt->close();
?>

<div class="container my-5">
    <div class="row">
        <!-- Book Images -->
        <div class="col-md-4 text-center mb-4">
            <?php if (count($book_images) > 4): ?>
                <!-- Carousel for >4 images -->
                <div id="bookCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <?php foreach ($book_images as $index => $img): ?>
                            <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                <img src="../assets/images/books/<?php echo htmlspecialchars($img); ?>" 
                                     class="d-block w-100" 
                                     style="height:550px; object-fit:cover; border-radius:8px;">
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#bookCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#bookCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                </div>
            <?php else: ?>
                <!-- Main Image + Thumbnails -->
                <img id="main-book-image" src="../assets/images/books/<?php echo htmlspecialchars($book_images[0]); ?>" 
                     alt="<?php echo htmlspecialchars($book['title']); ?>" 
                     style="width:100%; max-width:400px; height:550px; object-fit:cover; border-radius:8px; margin-bottom:10px;">
                <div class="d-flex justify-content-center gap-2 flex-wrap">
                    <?php foreach ($book_images as $img): ?>
                        <img src="../assets/images/books/<?php echo htmlspecialchars($img); ?>" 
                             style="width:80px; height:80px; object-fit:cover; border-radius:4px; cursor:pointer;"
                             onclick="document.getElementById('main-book-image').src=this.src;">
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Book Details -->
        <div class="col-md-8">
            <h2><?php echo htmlspecialchars($book['title']); ?></h2>
            <p><strong>Author:</strong> <?php echo htmlspecialchars($book['author']); ?></p>
            <p><strong>Genre:</strong> <?php echo htmlspecialchars($book['genre']); ?></p>
            <p><strong>Condition:</strong> <?php echo htmlspecialchars($book['condition']); ?></p>
            <p><strong>Price:</strong> ₱<?php echo number_format($book['price'], 2); ?></p>
            <p><strong>Stock:</strong> 
                <?php echo intval($book['stock']) > 0 ? intval($book['stock']).' available' : '<span class="text-danger">Out of Stock</span>'; ?>
            </p>
            <?php if (!empty($book['description'])): ?>
                <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($book['description'])); ?></p>
            <?php endif; ?>

            <!-- Add to Cart -->
            <form method="POST" action="../cart/cart_update.php" class="mb-3">
                <label class="form-label">Quantity:</label>
                <input type="number" name="item_qty" class="form-control mb-2" value="1" min="1" max="<?php echo intval($book['stock']); ?>">
                <input type="hidden" name="item_id" value="<?php echo intval($book['bookId']); ?>">
                <input type="hidden" name="type" value="add">
                <button type="submit" class="btn btn-primary" <?php echo intval($book['stock']) === 0 ? 'disabled' : ''; ?>>Add to Cart</button>
            </form>
        </div>
    </div>

    <!-- Reviews -->
    <div class="row mt-5">
        <div class="col-12">
            <h4>Reviews</h4>
            <?php
            $review_stmt = $conn->prepare("
                SELECT r.rating, r.review_text, u.email
                FROM reviews r
                JOIN users u ON u.id = r.user_id
                WHERE r.book_id = ?
                ORDER BY r.created_at DESC
            ");
            $review_stmt->bind_param('i', $book_id);
            $review_stmt->execute();
            $review_result = $review_stmt->get_result();

            if ($review_result && $review_result->num_rows > 0) {
                echo '<ul class="list-group">';
                while ($review = $review_result->fetch_assoc()) {
                    $rating = intval($review['rating']);
                    echo '<li class="list-group-item">';
                    echo '<strong>' . htmlspecialchars($review['email']) . '</strong> - ';
                    echo str_repeat('★', $rating) . str_repeat('☆', 5 - $rating) . '<br>';
                    echo htmlspecialchars($review['review_text']);
                    echo '</li>';
                }
                echo '</ul>';
            } else {
                echo '<p>No reviews yet for this book.</p>';
            }
            $review_stmt->close();
            ?>

            <!-- Review Submission Form -->
            <?php if (isset($_SESSION['userId']) || isset($_SESSION['user']['id'])): ?>
                <form method="POST" action="../reviews/submit_review.php" class="mt-3">
                    <input type="hidden" name="book_id" value="<?php echo intval($book['bookId']); ?>">
                    <div class="mb-2">
                        <label class="form-label">Rating (1-5):</label>
                        <select name="rating" class="form-select" required>
                            <?php for ($i=1;$i<=5;$i++): ?>
                                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Review:</label>
                        <textarea name="review_text" class="form-control" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-success">Submit Review</button>
                </form>
            <?php else: ?>
                <p class="mt-2">Please <a href="../login.php">login</a> to submit a review.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include('../includes/footer.php'); ?>
