<?php
session_start();

include('../config/database.php');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    $_SESSION['message'] = "Access denied. Admins only.";
    header("Location: ../user/login.php");
    exit;
}

$keyword = '';
if(isset($_GET['search'])) {
    $keyword = strtolower(trim($_GET['search']));
}

$filter = $_GET['filter'] ?? 'all';

$sql = "SELECT a.id, a.name, a.bio, b.title AS book_title
        FROM authors a
        LEFT JOIN books b ON b.author_id = a.id
        WHERE 1 ";

if($keyword) {
    $sql .= " AND LOWER(a.name) LIKE '%{$keyword}%' ";
}

if($filter === 'with_books') {
    $sql .= " AND b.id IS NOT NULL ";
} elseif($filter === 'no_books') {
    $sql .= " AND b.id IS NULL ";
}

$sql .= " ORDER BY a.name, b.title ";

$result = mysqli_query($conn, $sql);

$authors = [];
while ($row = mysqli_fetch_assoc($result)) {
    $authorId = $row['id'];
    if (!isset($authors[$authorId])) {
        $authors[$authorId] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'bio' => $row['bio'],
            'books' => []
        ];
    }
    if ($row['book_title']) {
        $authors[$authorId]['books'][] = $row['book_title'];
    }
}

$authorCount = count($authors);
include('../includes/header.php');
?>

<div class="d-flex">
    <?php include('../includes/admin_sidebar.php'); ?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Authors (<?= $authorCount ?>)</h2>
        <a href="authors/create_author.php" class="btn btn-primary btn-lg">
            <i class="fa-solid fa-plus"></i> Add Author
        </a>
    </div>

    <div class="d-flex mb-4 align-items-center">
        <div class="me-3">
            <a href="?filter=all&search=<?= urlencode($keyword) ?>" class="btn <?= ($filter ?? 'all') === 'all' ? 'btn-primary' : 'btn-outline-primary' ?> me-2">All</a>
            <a href="?filter=with_books&search=<?= urlencode($keyword) ?>" class="btn <?= ($filter ?? '') === 'with_books' ? 'btn-primary' : 'btn-outline-primary' ?> me-2">With Books</a>
            <a href="?filter=no_books&search=<?= urlencode($keyword) ?>" class="btn <?= ($filter ?? '') === 'no_books' ? 'btn-primary' : 'btn-outline-primary' ?>">No Books</a>
        </div>

        <form class="flex-grow-1" method="GET" action="">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search by author name..." value="<?= htmlspecialchars($keyword) ?>">
                <button class="btn btn-outline-secondary" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
            </div>
        </form>
    </div>

    <?php if($authorCount > 0): ?>
        <div class="row g-4">
            <?php foreach($authors as $author): ?>
                <div class="col-12">
                    <div class="card shadow-sm p-4 border-1">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="flex-grow-1">
                                <h5 class="mb-2"><?= htmlspecialchars($author['name']) ?> <small class="text-muted">(ID: <?= $author['id'] ?>)</small></h5>
                                <?php if($author['bio']): ?>
                                    <p class="mb-1"><strong>Bio:</strong> <?= htmlspecialchars($author['bio']) ?></p>
                                <?php endif; ?>
                                <?php if($author['books']): ?>
                                    <p class="mb-0"><strong>Works:</strong> <?= htmlspecialchars(implode(", ", $author['books'])) ?></p>
                                <?php else: ?>
                                    <p class="mb-0 text-muted"><em>No works yet.</em></p>
                                <?php endif; ?>
                            </div>

                            <div class="d-flex flex-column ms-3">
                                <a href="authors/edit_author.php?id=<?= $author['id'] ?>" class="btn btn-outline-primary btn-sm mb-2">
                                    <i class="fa-regular fa-pen-to-square"></i> Edit
                                </a>
                                <a href="authors/delete_author.php?id=<?= $author['id'] ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to delete this author?');">
                                    <i class="fa-solid fa-trash"></i> Delete
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-warning text-center">No authors found.</div>
    <?php endif; ?>
</div>
</div>

<?php include('../includes/footer.php'); ?>
