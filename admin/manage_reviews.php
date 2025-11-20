<?php
session_start();
$pageTitle = "Manage Reviews";
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

$sql = "SELECT r.id AS review_id, r.user_id, r.book_id, r.rating, r.review_text, r.created_at,
               b.title AS book_title, b.image AS book_image,
               u.username, p.first_name, p.last_name
        FROM reviews r
        LEFT JOIN books b ON r.book_id = b.id
        LEFT JOIN users u ON r.user_id = u.id
        LEFT JOIN user_profiles p ON u.id = p.user_id";

if($keyword) {
    $sql .= " WHERE LOWER(b.title) LIKE '%{$keyword}%' OR LOWER(u.username) LIKE '%{$keyword}%' OR LOWER(r.review_text) LIKE '%{$keyword}%'";
}

$sql .= " ORDER BY r.created_at DESC";

$result = mysqli_query($conn, $sql);
$reviewCount = mysqli_num_rows($result);
?>

<div class="d-flex">
    <?php include('../includes/admin_sidebar.php'); ?>

    <div class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><?= $pageTitle ?> (<?= $reviewCount ?>)</h2>
            <a href="reviews/create_review.php" class="btn btn-primary btn-lg">
                <i class="fas fa-plus"></i> Add Review
            </a>
        </div>

        <form class="mb-4" method="GET" action="">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search by book, user, or review..." value="<?= htmlspecialchars($keyword) ?>">
                <button class="btn btn-outline-secondary" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
            </div>
        </form>

        <?php if($reviewCount > 0): ?>
            <div class="row g-4">
                <?php while ($review = mysqli_fetch_assoc($result)): ?>
                    <div class="col-12">
                        <div class="card flex-row align-items-center p-3 shadow-sm">
                            <div class="me-3" style="width:120px; flex-shrink:0;">
                                <?php if($review['book_image'] && file_exists("../assets/images/books/".$review['book_image'])): ?>
                                    <img src="../assets/images/books/<?= $review['book_image'] ?>" alt="<?= htmlspecialchars($review['book_title']) ?>" class="img-thumbnail" style="width:100%; height:150px; object-fit:cover;">
                                <?php else: ?>
                                    <div class="d-flex align-items-center justify-content-center bg-light text-muted" style="width:100%; height:150px;">No Image</div>
                                <?php endif; ?>
                            </div>

                            <div class="flex-grow-1">
                                <h5 class="mb-1 fw-bold"><?= htmlspecialchars($review['book_title']) ?></h5>
                                <p class="mb-1 text-secondary">
                                    Reviewer: <?= htmlspecialchars($review['first_name'] . ' ' . $review['last_name']) ?> (<?= htmlspecialchars($review['username']) ?>)
                                </p>
                                <p class="mb-1">
                                    Rating: 
                                    <?php for($i=1; $i<=5; $i++): ?>
                                        <i class="fas fa-star<?= $i <= $review['rating'] ? '' : '-half-alt' ?>" style="color:gold;"></i>
                                    <?php endfor; ?>
                                </p>
                                <p class="mb-1"><?= nl2br(htmlspecialchars($review['review_text'])) ?></p>
                                <p class="text-muted mb-0" style="font-size:0.9rem;">
                                    Reviewed on <?= date('F d, Y', strtotime($review['created_at'])) ?>
                                </p>
                            </div>

                            <div class="d-flex flex-column align-items-center ms-3" style="gap:10px; min-width:50px;">
                                <a href="reviews/edit_review.php?id=<?= $review['review_id'] ?>" class="text-decoration-none text-dark" title="Edit">
                                    <i class="fas fa-edit fa-lg"></i>
                                </a>
                                <a href="reviews/delete_review.php?id=<?= $review['review_id'] ?>" class="text-decoration-none text-dark" title="Delete" onclick="return confirm('Are you sure you want to delete this review?');">
                                    <i class="fas fa-trash fa-lg"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-warning text-center">No reviews found.</div>
        <?php endif; ?>
    </div>
</div>

<?php include('../includes/footer.php'); ?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
