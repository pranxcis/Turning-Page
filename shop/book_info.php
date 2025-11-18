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

// Fetch main book info
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

// Fetch additional images (limit 4)
$images = [];
$img_stmt = $conn->prepare("SELECT image_path FROM book_images WHERE book_id = ? LIMIT 4");
$img_stmt->bind_param("i", $book_id);
$img_stmt->execute();
$img_res = $img_stmt->get_result();
while ($row = $img_res->fetch_assoc()) {
    if (!empty($row['image_path'])) $images[] = $row['image_path'];
}
$img_stmt->close();

// Folder for images
$IMG_FOLDER = "../assets/images/books/";

// Prepare images for display
$all_images = [];

// Main image first
$main_image = $book['main_image'] ?: "default.jpg";
$main_image_path = file_exists($IMG_FOLDER . $main_image) ? $IMG_FOLDER . $main_image : $IMG_FOLDER . "default.jpg";
$all_images[] = $main_image_path;

// Add additional images (exclude main image)
foreach ($images as $img) {
    $img_file = basename($img);
    $img_path = $IMG_FOLDER . $img_file;
    if (file_exists($img_path) && $img_path !== $main_image_path && count($all_images) < 5) { // max 1 main + 4 additional
        $all_images[] = $img_path;
    }
}
?>

<div class="container my-5">
    <div class="row">
        <!-- Image Gallery -->
        <div class="col-md-4 text-center mb-4">
            <!-- Main Image -->
            <img id="mainImage" src="<?php echo $all_images[0]; ?>"
                 style="width:100%; max-width:400px; height:520px; object-fit:cover; border-radius:8px; margin-bottom:10px;"
                 onerror="this.src='../assets/images/books/default.jpg';">

            <!-- Thumbnails -->
            <?php if(count($all_images) > 1): ?>
            <div class="d-flex justify-content-center gap-2 flex-wrap mt-3">
                <?php
                // Skip main image
                $thumbnail_images = array_slice($all_images, 1);
                foreach ($thumbnail_images as $img): ?>
                    <img src="<?php echo $img; ?>"
                         onclick="document.getElementById('mainImage').src='<?php echo $img; ?>'"
                         style="width:75px; height:75px; object-fit:cover; border-radius:4px; cursor:pointer;"
                         onerror="this.src='../assets/images/books/default.jpg';">
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
            <p><strong>Price:</strong> ₱<?php echo number_format($book['price'],2); ?></p>
            <p><strong>Stock:</strong>
                <?php echo $book['stock']>0 ? $book['stock']." available" : "<span class='text-danger'>Out of Stock</span>"; ?>
            </p>
            <?php if(!empty($book['description'])): ?>
                <p><strong>Description:</strong><br><?php echo nl2br(htmlspecialchars($book['description'])); ?></p>
            <?php endif; ?>

            <!-- Add to Cart -->
            <form method="POST" action="../cart/cart_update.php">
                <label class="form-label">Quantity:</label>
                <input type="number" name="item_qty" value="1" min="1"
                       max="<?php echo intval($book['stock']); ?>" class="form-control mb-2">
                <input type="hidden" name="item_id" value="<?php echo $book['book_id']; ?>">
                <input type="hidden" name="type" value="add">
                <button type="submit" class="btn btn-primary"
                        <?php echo $book['stock']==0?"disabled":""; ?>>
                    Add to Cart
                </button>
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
            if($review_result->num_rows>0){
                echo '<ul class="list-group">';
                while($review = $review_result->fetch_assoc()){
                    echo '<li class="list-group-item">';
                    echo '<strong>'.htmlspecialchars($review['email']).'</strong> — ';
                    echo str_repeat('★',$review['rating']).str_repeat('☆',5-$review['rating']);
                    echo '<br>'.nl2br(htmlspecialchars($review['review_text']));
                    echo '</li>';
                }
                echo '</ul>';
            } else {
                echo '<p>No reviews yet for this book.</p>';
            }
            $review_stmt->close();
            ?>
        </div>
    </div>
</div>

<?php include('../includes/footer.php'); ?>
