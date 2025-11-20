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

// Get author ID
$authorId = intval($_GET['id'] ?? 0);
if ($authorId <= 0) {
    $_SESSION['message'] = "Invalid author ID.";
    header("Location: ../manage_authors.php");
    exit;
}

// Fetch author info
$stmt = $conn->prepare("SELECT id, name, bio FROM authors WHERE id = ?");
$stmt->bind_param("i", $authorId);
$stmt->execute();
$result = $stmt->get_result();
$author = $result->fetch_assoc();
$stmt->close();

if (!$author) {
    $_SESSION['message'] = "Author not found.";
    header("Location: ../manage_authors.php");
    exit;
}

$nameValue = $_SESSION['form_name'] ?? $author['name'];
$bioValue  = $_SESSION['form_bio']  ?? $author['bio'];

unset($_SESSION['form_name'], $_SESSION['form_bio']);
?>

<div class="container my-5">
    <h2 class="mb-4">Edit Author</h2>

    <form method="POST" action="update_author.php">
        <input type="hidden" name="author_id" value="<?= $author['id'] ?>">

        <div class="mb-3">
            <label for="name" class="form-label">Author Name</label>
            <input type="text" name="name" class="form-control" id="name" value="<?= htmlspecialchars($nameValue) ?>">
            <small class="text-danger"><?= $_SESSION['err_name'] ?? '' ?></small>
            <?php unset($_SESSION['err_name']); ?>
        </div>

        <div class="mb-3">
            <label for="bio" class="form-label">Biography</label>
            <textarea name="bio" class="form-control" id="bio" rows="4"><?= htmlspecialchars($bioValue) ?></textarea>
        </div>

        <div class="mb-3">
            <button type="submit" class="btn btn-primary">Update Author</button>
            <a href="../manage_authors.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php include('../../includes/footer.php'); ?>
