<?php
session_start();
include('../../includes/header.php');
include('../../config/database.php');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    $_SESSION['message'] = "Access denied. Admins only.";
    header("Location: ../login.php");
    exit;
}

$nameValue = $_SESSION['form_name'] ?? '';
$bioValue  = $_SESSION['form_bio'] ?? '';

unset($_SESSION['form_name'], $_SESSION['form_bio']);
?>

<div class="container my-5">
    <h2 class="mb-4">Add New Author</h2>

    <form method="POST" action="store_author.php">
        <div class="row g-3">

            <div class="col-12">
                <label>Author Name</label>
                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($nameValue) ?>" />
                <small class="text-danger"><?= $_SESSION['err_name'] ?? '' ?></small>
                <?php unset($_SESSION['err_name']); ?>
            </div>

            <div class="col-12">
                <label>Biography</label>
                <textarea name="bio" class="form-control" rows="4"><?= htmlspecialchars($bioValue) ?></textarea>
                <small class="text-danger"><?= $_SESSION['err_bio'] ?? '' ?></small>
                <?php unset($_SESSION['err_bio']); ?>
            </div>

            <div class="col-12 mt-3">
                <button type="submit" class="btn btn-primary">Save Author</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>

        </div>
    </form>
</div>

<?php include('../../includes/footer.php'); ?>
