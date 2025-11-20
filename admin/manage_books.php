<?php
session_start();
include('../includes/header.php');
include('../config/database.php');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    $_SESSION['message'] = "Access denied. Admins only.";
    header("Location: ../login.php");
    exit;
}

$keyword = '';
if(isset($_GET['search'])) {
    $keyword = strtolower(trim($_GET['search']));
}

if ($keyword) {
    $sql = "SELECT b.id, b.title, b.genre, b.set_price, b.price, b.stock, b.image, a.name AS author_name 
            FROM books b 
            LEFT JOIN authors a ON b.author_id = a.id
            WHERE LOWER(b.title) LIKE '%{$keyword}%'";
} else {
    $sql = "SELECT b.id, b.title, b.genre, b.set_price, b.price, b.stock, b.image, a.name AS author_name 
            FROM books b 
            LEFT JOIN authors a ON b.author_id = a.id";
}

$result = mysqli_query($conn, $sql);
$bookCount = mysqli_num_rows($result);
?>

<div class="d-flex">
    <?php include('../includes/admin_sidebar.php'); ?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Books Inventory (<?= $bookCount ?>)</h2>
        <a href="books/create_book.php" class="btn btn-primary btn-lg">
            <i class="fa-solid fa-plus"></i> Add Book
        </a>
    </div>

    <form class="mb-4" method="GET" action="">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search by book title..." value="<?= htmlspecialchars($keyword) ?>">
            <button class="btn btn-outline-secondary" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
        </div>
    </form>

    <?php if($bookCount > 0): ?>
        <div class="row g-4">
            <?php while ($book = mysqli_fetch_assoc($result)): ?>
                <div class="col-12">
                    <div class="card flex-row align-items-center p-3 shadow-sm">
                        <div class="book-img me-3">
                            <?php if($book['image'] && file_exists("../assets/images/books/".$book['image'])): ?>
                                <img src="../assets/images/books/<?= $book['image'] ?>" alt="<?= htmlspecialchars($book['title']) ?>" class="img-thumbnail" style="width:120px; height:150px; object-fit:cover;">
                            <?php else: ?>
                                <div class="d-flex align-items-center justify-content-center bg-light text-muted" style="width:120px; height:150px;">No Image</div>
                            <?php endif; ?>
                        </div>

                        <div class="flex-grow-1">
                            <h5 class="card-title mb-1"><?= htmlspecialchars($book['title']) ?></h5>
                            <p class="mb-1"><strong>Author:</strong> <?= htmlspecialchars($book['author_name']) ?></p>
                            <p class="mb-1"><strong>Genre:</strong> <?= htmlspecialchars($book['genre']) ?></p>
                            <p class="mb-1"><strong>Set Price:</strong> ₱<?= number_format($book['set_price'],2) ?> | <strong>Selling Price:</strong> ₱<?= number_format($book['price'],2) ?></p>
                            <p class="mb-1"><strong>Stock:</strong> <?= $book['stock'] ?></p>
                        </div>

                        <div class="d-flex flex-column ms-3">
                            <a href="books/edit_book.php?id=<?= $book['id'] ?>" class="btn btn-outline-primary btn-sm mb-2">
                                <i class="fa-regular fa-pen-to-square"></i> Edit
                            </a>
                            <a href="books/delete_book.php?id=<?= $book['id'] ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to delete this book?');">
                                <i class="fa-solid fa-trash"></i> Delete
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-warning text-center">No books found.</div>
    <?php endif; ?>
</div>
</div>

<?php include('../includes/footer.php'); ?>
