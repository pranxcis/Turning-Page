<?php
session_start();
include('../../includes/header.php');
include('../../config/database.php');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    $_SESSION['message'] = "Access denied. Admins only.";
    header("Location: ../login.php");
    exit;
}

$titleValue      = $_SESSION['form_title']      ?? '';
$authorValue     = $_SESSION['form_author']     ?? '';
$genreValue      = $_SESSION['form_genre']      ?? '';
$setPriceValue   = $_SESSION['form_set_price']  ?? '';
$priceValue      = $_SESSION['form_price']      ?? '';
$descValue       = $_SESSION['form_description']?? '';
$conditionValue  = $_SESSION['form_condition']  ?? '';
$stockValue      = $_SESSION['form_stock']      ?? '';

unset($_SESSION['form_title'], $_SESSION['form_author'], $_SESSION['form_genre'], $_SESSION['form_set_price'], $_SESSION['form_price'], $_SESSION['form_description'], $_SESSION['form_condition'], $_SESSION['form_stock']);

$authorQuery = $conn->query("SELECT id, name FROM authors ORDER BY name ASC");
?>

<div class="container my-5">
    <h2 class="mb-4">Add New Book</h2>

    <form method="POST" action="store_book.php" enctype="multipart/form-data">
        <div class="row g-3">

            <div class="col-12">
                <label>Book Title</label>
                <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($titleValue) ?>" />
                <small class="text-danger"><?= $_SESSION['err_title'] ?? '' ?></small>
                <?php unset($_SESSION['err_title']); ?>
            </div>

<div class="col-md-6">
    <label>Author</label>
    <select name="author_id" class="form-control">
        <option value="">-- Select Author --</option>
        <?php while ($row = $authorQuery->fetch_assoc()): ?>
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
        <option value="Fiction"     <?= ($genreValue=="Fiction") ? "selected":"" ?>>Fiction</option>
        <option value="Non-Fiction" <?= ($genreValue=="Non-Fiction") ? "selected":"" ?>>Non-Fiction</option>
        <option value="None"        <?= ($genreValue=="None") ? "selected":"" ?>>None</option>
    </select>
    <small class="text-danger"><?= $_SESSION['err_genre'] ?? '' ?></small>
    <?php unset($_SESSION['err_genre']); ?>
</div>

            <!-- PRICES: Set & Sell -->
            <div class="col-md-6">
                <label>Original Price (Set Price)</label>
                <input type="number" step="0.01" name="set_price" class="form-control" value="<?= htmlspecialchars($setPriceValue) ?>" />
                <small class="text-danger"><?= $_SESSION['err_set_price'] ?? '' ?></small>
                <?php unset($_SESSION['err_set_price']); ?>
            </div>

            <div class="col-md-6">
                <label>Selling Price</label>
                <input type="number" step="0.01" name="price" class="form-control" value="<?= htmlspecialchars($priceValue) ?>" />
                <small class="text-danger"><?= $_SESSION['err_price'] ?? '' ?></small>
                <?php unset($_SESSION['err_price']); ?>
            </div>
            
<div class="col-md-6">
    <label>Condition</label>
    <select name="condition" class="form-control">
        <option value="">-- Select Condition --</option>
        <option value="New"         <?= ($conditionValue=="New") ? "selected":"" ?>>New</option>
        <option value="Collectible" <?= ($conditionValue=="Collectible") ? "selected":"" ?>>Collectible</option>
        <option value="Used"        <?= ($conditionValue=="Used") ? "selected":"" ?>>Used</option>
    </select>
    <small class="text-danger"><?= $_SESSION['err_condition'] ?? '' ?></small>
    <?php unset($_SESSION['err_condition']); ?>
</div>

            <div class="col-md-6">
                <label>Stock</label>
                <input type="number" name="stock" class="form-control" value="<?= htmlspecialchars($stockValue) ?>" />
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
                <input type="file" name="image" class="form-control" />
                <small class="text-danger"><?= $_SESSION['err_image'] ?? '' ?></small>
                <?php unset($_SESSION['err_image']); ?>
            </div>

            <div class="col-12">
                <label>Additional Book Images</label>
                <input type="file" name="additional_images[]" class="form-control" multiple />
                <small class="text-danger"><?= $_SESSION['err_additional_images'] ?? '' ?></small>
                <?php unset($_SESSION['err_additional_images']); ?>
            </div>

            <div class="col-12 mt-3">
                <button type="submit" class="btn btn-primary">Save Book</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>

        </div>
    </form>
</div>

<?php include('../../includes/footer.php'); ?>
