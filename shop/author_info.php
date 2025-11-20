<?php
session_start();
include('../includes/header.php');
include('../config/database.php');

$author_id = isset($_GET['author_id']) ? intval($_GET['author_id']) : 0;
if ($author_id <= 0) {
    echo "<div class='container my-4'><p>Invalid author selected.</p></div>";
    include('../includes/footer.php');
    exit;
}

$stmt = $conn->prepare("
    SELECT id, name, bio
    FROM authors
    WHERE id = ?
");
$stmt->bind_param('i', $author_id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result || $result->num_rows === 0) {
    echo "<div class='container my-4'><p>Author not found.</p></div>";
    include('../includes/footer.php');
    exit;
}

$author = $result->fetch_assoc();
$stmt->close();

$books_stmt = $conn->prepare("
    SELECT b.id, b.title, b.genre, b.price, b.stock, 
           COALESCE(b.image, 'default.jpg') AS image
    FROM books b
    LEFT JOIN (
        SELECT book_id, image_path
        FROM book_images
        GROUP BY book_id
    ) bi ON bi.book_id = b.id
        WHERE b.author_id = ?
        ORDER BY b.created_at DESC
");
$books_stmt->bind_param('i', $author_id);
$books_stmt->execute();
$books_result = $books_stmt->get_result();
?>

<div class="container my-5">
    <div class="card shadow-sm p-4">
        <h2><?= htmlspecialchars($author['name']) ?></h2>
        <?php if (!empty($author['bio'])): ?>
            <p><strong>Bio:</strong> <?= nl2br(htmlspecialchars($author['bio'])) ?></p>
        <?php endif; ?>
    </div>

    <div class="mt-5">
        <h4>Books by <?= htmlspecialchars($author['name']) ?></h4>
        <?php if ($books_result && $books_result->num_rows > 0): ?>
            <ul class="row list-unstyled mt-3">
                <?php while ($book = $books_result->fetch_assoc()): ?>
                    <li class="col-md-3 mb-4">
                        <div class="card h-100 shadow-sm p-3">
                            <img src="../assets/images/books/<?= htmlspecialchars($book['image']) ?>" 
                                 class="card-img-top mb-2" 
                                 alt="<?= htmlspecialchars($book['title']) ?>" 
                                 style="height:250px; object-fit:cover; border-radius:8px;">
                            <h5 class="card-title"><?= htmlspecialchars($book['title']) ?></h5>
                            <p class="card-text"><strong>Genre:</strong> <?= htmlspecialchars($book['genre']) ?></p>
                            <p class="card-text"><strong>Price:</strong> ₱<?= number_format($book['price'], 2) ?></p>
                            <p class="card-text text-success">
                                <?= intval($book['stock']) > 0 ? intval($book['stock']).' in stock' : '<span class="text-danger">Out of stock</span>' ?>
                            </p>
                            <a href="book_info.php?book_id=<?= $book['id'] ?>" class="btn btn-outline-primary w-100 mt-auto">View Details</a>
                        </div>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>No books found for this author.</p>
        <?php endif; ?>
    </div>
</div>

<?php
$books_stmt->close();
include('../includes/footer.php');
?>
