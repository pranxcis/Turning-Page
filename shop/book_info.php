<?php
session_start();
include('../includes/header.php');
include('../config/database.php');

// Validate book ID
$book_id = isset($_GET['book_id']) ? intval($_GET['book_id']) : 0;
if ($book_id <= 0) {
    echo "<div class='container my-4'><p>Invalid book selected.</p></div>";
    include('../includes/footer.php');
    exit;
}

// Fetch book info
$stmt = $conn->prepare("
    SELECT b.id AS book_id, b.title, b.price, b.stock, b.description,
           b.genre, b.condition, b.author_id, b.image AS main_image,
           a.name AS author
    FROM books b
    LEFT JOIN authors a ON a.id = b.author_id
    WHERE b.id = ?
");
$stmt->bind_param("i", $book_id);
$stmt->execute();
$book = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$book) {
    echo "<div class='container my-4'><p>Book not found.</p></div>";
    include('../includes/footer.php');
    exit;
}

// Fetch ALL images
$images = [];
$img_stmt = $conn->prepare("SELECT image_path FROM book_images WHERE book_id = ?");
$img_stmt->bind_param("i", $book_id);
$img_stmt->execute();
$img_res = $img_stmt->get_result();

while ($row = $img_res->fetch_assoc()) {
    if (!empty($row['image_path'])) {
        $images[] = $row['image_path'];
    }
}
$img_stmt->close();

$IMG_FOLDER = "../assets/images/books/";

// Combine images
$all_images = [];
$main_image = $book['main_image'] ?: "default.jpg";
$main_image_path = $IMG_FOLDER . $main_image;

// Fallback if missing
if (!file_exists($main_image_path)) {
    $main_image_path = $IMG_FOLDER . "default.jpg";
}
$all_images[] = $main_image_path;

// Add ALL book_images files
foreach ($images as $img) {
    $img_path = $IMG_FOLDER . basename($img);
    if (file_exists($img_path) && !in_array($img_path, $all_images)) {
        $all_images[] = $img_path;
    }
}

// Limit thumbnails to up to 4 (including main)
$thumbnail_images = array_slice($all_images, 0, 4);

// Fetch other books by the same author (exclude current book)
$other_books_stmt = $conn->prepare("
    SELECT id, title, price, image
    FROM books
    WHERE author_id = ? AND id != ?
    LIMIT 8
");
$other_books_stmt->bind_param("ii", $book['author_id'], $book['book_id']);
$other_books_stmt->execute();
$other_books = $other_books_stmt->get_result();
$other_books_stmt->close();

// Fetch customer reviews
$review_stmt = $conn->prepare("
    SELECT r.rating, r.review_text, r.created_at, u.username
    FROM reviews r
    LEFT JOIN users u ON r.user_id = u.id
    WHERE r.book_id = ?
    ORDER BY r.created_at DESC
");
$review_stmt->bind_param("i", $book_id);
$review_stmt->execute();
$reviews = $review_stmt->get_result();
$review_stmt->close();
?>

<style>
.thumbnail-column img { width:75px;height:75px;object-fit:cover;border-radius:4px;cursor:pointer; }
.thumbnail-column img.active { border:2px solid #007bff; }
#mainImage { width:400px;height:520px;object-fit:cover;border-radius:6px; }
.sold-out-badge { position:absolute;top:10px;right:10px;background:#dc3545;color:#fff;padding:4px 10px;border-radius:4px;font-size:0.8rem;font-weight:700; }
.book-highlights li { margin-bottom:4px; }
.review-box { border:1px solid #ddd;padding:15px;border-radius:6px;margin-bottom:15px;background:#fafafa; }
.review-username { font-weight:700; }
.review-date { font-size:.85rem;color:#777; }
.review-stars { color:#f1c40f;font-size:1.1rem;margin-bottom:5px; }
</style>

<div class="container my-5 pt-5 pb-5">

<div class="image-gallery-flex d-flex gap-4 pb-5">

    <!-- Thumbnails -->
    <div class="thumbnail-column d-flex flex-column gap-2">
        <?php foreach ($thumbnail_images as $index => $img_path): ?>
            <img src="<?= htmlspecialchars($img_path); ?>"
                 class="<?= $index === 0 ? 'active' : '' ?>"
                 onclick="changeMainImage(this)"
                 onerror="this.src='../assets/images/books/default.jpg';">
        <?php endforeach; ?>
    </div>

    <!-- Main Image -->
    <div style="position:relative;">
        <img id="mainImage" src="<?= htmlspecialchars($thumbnail_images[0] ?? '../assets/images/books/default.jpg'); ?>" 
             onerror="this.src='../assets/images/books/default.jpg';">
        <?php if ($book['stock'] == 0): ?>
            <div class="sold-out-badge">Sold Out</div>
        <?php endif; ?>
    </div>

    <!-- Book Info -->
    <div class="flex-grow-1 d-flex flex-column ms-5">

        <h1 class="fw-bold"><?= htmlspecialchars(strtoupper($book['title'])); ?></h1>
        <h4 class="text-primary fw-bold">₱<?= number_format($book['price'], 2); ?></h4>

        <p class="mt-4"><strong>Author:</strong> 
            <a href="../shop/author_books.php?author_id=<?= $book['author_id']; ?>" class="text-decoration-none">
                <?= htmlspecialchars($book['author']); ?>
            </a>
        </p>

        <p><strong>Genre:</strong> <?= htmlspecialchars($book['genre']); ?></p>
        <p><strong>Condition:</strong> <?= htmlspecialchars($book['condition']); ?></p>
        <p><strong>Stock:</strong> <?= intval($book['stock']); ?></p>

        <div class="book-description mb-3">
            <?= nl2br(htmlspecialchars($book['description'])); ?>
        </div>

        <!-- Add to cart -->
        <form method="POST" action="../cart/cart_update.php" class="mt-auto d-flex flex-column gap-2">
            <input type="hidden" name="item_id" value="<?= $book['book_id']; ?>">
            <input type="hidden" name="type" value="add">

            <div class="d-flex align-items-center gap-2  mb-4">
                <label for="item_qty"><strong>Quantity:</strong></label>
                <input type="number" name="item_qty" value="1" min="1"
                       max="<?= intval($book['stock']); ?>" class="form-control" style="width:100px;"
                       <?= $book['stock'] == 0 ? 'disabled' : ''; ?>>
            </div>

            <button class="btn btn-primary w-100" <?= $book['stock'] == 0 ? 'disabled' : ''; ?>>
                Add To Cart
            </button>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= $_SESSION['message'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>
        </form>

    </div>

</div>

<!-- ========================= -->
<!-- Other books by this author -->
<!-- ========================= -->
<?php if ($other_books->num_rows > 0): ?>
    <div class="mt-5">
        <h5 class="fw-bold mb-3">Other books by <?= htmlspecialchars($book['author']); ?></h5>
        <div class="row row-cols-1 row-cols-md-4 g-3">
            <?php while ($ob = $other_books->fetch_assoc()): ?>
                <?php
                $ob_img = $IMG_FOLDER . ($ob['image'] ?: 'default.jpg');
                if (!file_exists($ob_img)) $ob_img = $IMG_FOLDER . 'default.jpg';
                ?>
                <div class="col">
                    <div class="card h-100">
                        <img src="<?= htmlspecialchars($ob_img); ?>" class="card-img-top" style="height:400px; object-fit:cover;" 
                             onerror="this.src='../assets/images/books/default.jpg';">
                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title mb-1"><?= htmlspecialchars($ob['title']); ?></h6>
                            <p class="text-primary fw-bold mb-0">₱<?= number_format($ob['price'],2); ?></p>
                            <a href="book_info.php?book_id=<?= $ob['id']; ?>" class="mt-auto btn btn-sm btn-outline-primary w-100">View</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
<?php endif; ?>

<!-- ========================= -->
<!-- CUSTOMER REVIEWS SECTION -->
<!-- ========================= -->
<div class="mt-5">
    <h4 class="fw-bold mb-3">Customer Reviews</h4>

    <?php if ($reviews->num_rows > 0): ?>
        <?php while ($rev = $reviews->fetch_assoc()): ?>
            <div class="review-box">
                <div class="review-stars">
                    <?= str_repeat("★", intval($rev['rating'])); ?>
                    <?= str_repeat("☆", 5 - intval($rev['rating'])); ?>
                </div>

                <div class="review-username"><?= htmlspecialchars($rev['username']); ?></div>
                <div class="review-date"><?= date("F d, Y", strtotime($rev['created_at'])); ?></div>
                <div class="review-text mt-2"><?= nl2br(htmlspecialchars($rev['review_text'])); ?></div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p class="text-muted">No reviews yet. Be the first to write one!</p>
    <?php endif; ?>
</div>

</div>

<script>
function changeMainImage(thumbnail) {
    document.getElementById('mainImage').src = thumbnail.src;
    document.querySelectorAll('.thumbnail-column img').forEach(i => i.classList.remove('active'));
    thumbnail.classList.add('active');
}
</script>

<?php
include('../includes/footer.php');
$conn->close();
?>
