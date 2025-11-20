<?php
session_start();
include('../../includes/header.php');
include('../../config/database.php');

// ------------------------
// ADMIN ACCESS ONLY
// ------------------------
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    $_SESSION['message'] = "Access denied. Admins only.";
    header("Location: ../login.php");
    exit;
}

// Get book ID
$bookId = intval($_GET['id'] ?? 0);
if ($bookId <= 0) {
    $_SESSION['message'] = "Invalid book ID.";
    header("Location: ../manage_books.php");
    exit;
}

$bookQuery = mysqli_query($conn, "SELECT * FROM books WHERE id = $bookId");
$book = mysqli_fetch_assoc($bookQuery);

if (!$book) {
    $_SESSION['message'] = "Book not found.";
    header("Location: ../manage_books.php");
    exit;
}

$authors = mysqli_query($conn, "SELECT id, name FROM authors ORDER BY name ASC");

$titleValue      = $_SESSION['form_title']       ?? $book['title'];
$authorValue     = $_SESSION['form_author']      ?? $book['author_id'];
$genreValue      = $_SESSION['form_genre']       ?? $book['genre']; 
$setPriceValue   = $_SESSION['form_set_price']   ?? $book['set_price'];
$priceValue      = $_SESSION['form_price']       ?? $book['price'];
$descValue       = $_SESSION['form_description'] ?? $book['description'];
$conditionValue  = $_SESSION['form_condition']   ?? $book['condition'];
$stockValue      = $_SESSION['form_stock']       ?? $book['stock'];

$selectedGenres = explode(',', $genreValue);

unset(
    $_SESSION['form_title'],
    $_SESSION['form_author'],
    $_SESSION['form_genre'],
    $_SESSION['form_set_price'],
    $_SESSION['form_price'],
    $_SESSION['form_description'],
    $_SESSION['form_condition'],
    $_SESSION['form_stock']
);
?>

<div class="container my-5">
    <h2 class="mb-4">Edit Book</h2>

    <form method="POST" action="update_book.php" enctype="multipart/form-data">
        <input type="hidden" name="book_id" value="<?= $book['id'] ?>">

        <div class="row g-3">
            <div class="col-12">
                <label>Book Title</label>
                <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($titleValue) ?>">
                <small class="text-danger"><?= $_SESSION['err_title'] ?? '' ?></small>
                <?php unset($_SESSION['err_title']); ?>
            </div>

            <div class="col-md-6">
                <label>Author</label>
                <select name="author_id" class="form-control">
                    <option value="">-- Select Author --</option>
                    <?php while ($row = mysqli_fetch_assoc($authors)): ?>
                        <option value="<?= $row['id'] ?>" <?= ($authorValue == $row['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($row['name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <small class="text-danger"><?= $_SESSION['err_author'] ?? '' ?></small>
                <?php unset($_SESSION['err_author']); ?>
            </div>

            <div class="col-md-6">
                <label>Genre</label>
                <select name="genre" class="form-control">
                    <option value="">-- Select Genre --</option>
                    <?php 
                        $genres = ["Fiction", "Non-Fiction", "None"];
                        foreach ($genres as $genre): 
                    ?>
                        <option value="<?= $genre ?>" <?= ($genreValue == $genre) ? "selected" : "" ?>><?= $genre ?></option>
                    <?php endforeach; ?>
                </select>
                <small class="text-danger"><?= $_SESSION['err_genre'] ?? '' ?></small>
                <?php unset($_SESSION['err_genre']); ?>
            </div>

            <div class="col-md-6">
                <label>Original Price (Set Price)</label>
                <input type="number" step="0.01" name="set_price" class="form-control" value="<?= htmlspecialchars($setPriceValue) ?>">
                <small class="text-danger"><?= $_SESSION['err_set_price'] ?? '' ?></small>
                <?php unset($_SESSION['err_set_price']); ?>
            </div>

            <div class="col-md-6">
                <label>Selling Price</label>
                <input type="number" step="0.01" name="price" class="form-control" value="<?= htmlspecialchars($priceValue) ?>">
                <small class="text-danger"><?= $_SESSION['err_price'] ?? '' ?></small>
                <?php unset($_SESSION['err_price']); ?>
            </div>

            <div class="col-md-6">
                <label>Condition</label>
                <select name="condition" class="form-control">
                    <option value="">-- Select Condition --</option>
                    <?php 
                        $conditions = ["New", "Used", "Collectible"];
                        foreach ($conditions as $cond): 
                    ?>
                        <option value="<?= $cond ?>" <?= ($conditionValue == $cond) ? "selected" : "" ?>><?= $cond ?></option>
                    <?php endforeach; ?>
                </select>
                <small class="text-danger"><?= $_SESSION['err_condition'] ?? '' ?></small>
                <?php unset($_SESSION['err_condition']); ?>
            </div>

            <div class="col-md-6">
                <label>Stock</label>
                <input type="number" name="stock" class="form-control" value="<?= htmlspecialchars($stockValue) ?>">
                <small class="text-danger"><?= $_SESSION['err_stock'] ?? '' ?></small>
                <?php unset($_SESSION['err_stock']); ?>
            </div>

            <div class="col-12">
                <label>Description</label>
                <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($descValue) ?></textarea>
                <small class="text-danger"><?= $_SESSION['err_description'] ?? '' ?></small>
                <?php unset($_SESSION['err_description']); ?>
            </div>

            <div class="col-12">
                <label>Main Book Image</label>
                <input type="file" name="image" class="form-control">
                <small class="text-danger"><?= $_SESSION['err_image'] ?? '' ?></small>
                <?php unset($_SESSION['err_image']); ?>
                <?php if($book['image'] && file_exists("../../assets/images/books/".$book['image'])): ?>
                    <div class="mt-2">
                        <img src="../../assets/images/books/<?= $book['image'] ?>" width="100" height="120" alt="<?= htmlspecialchars($book['title']) ?>">
                    </div>
                <?php endif; ?>
            </div>

            <div class="col-12">
                <label>Additional Images</label>
                <input type="file" name="additional_images[]" class="form-control" multiple>
            </div>

            <div class="col-12 mt-3">
                <button type="submit" class="btn btn-primary">Update Book</button>
                <a href="../manage_books.php" class="btn btn-secondary">Cancel</a>
            </div>
        </div>
    </form>
</div>

<?php include('../../includes/footer.php'); ?>
