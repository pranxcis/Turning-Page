<?php
session_start();       
$pageTitle = "Shop";         
include('../includes/header.php');
include('../config/database.php');
?>

<div class="container my-5">

    <!-- FILTERS ON TOP -->
    <div class="d-flex flex-wrap justify-content-start align-items-center gap-2 mb-5">

        <!-- Group 1: Books & Authors -->
        <div class="d-flex flex-wrap gap-2">
            <a href="index.php?filter=all" class="btn btn-outline-primary">Books</a>
            <a href="index.php?filter=authors" class="btn btn-outline-primary">Authors</a>
        </div>

        <!-- Group 2: New Arrival, Top Picks, In Stock -->
        <div class="d-flex flex-wrap gap-2">
            <a href="index.php?filter=new_arrivals" class="btn btn-outline-primary">New Arrival</a>
            <a href="index.php?filter=top_picks" class="btn btn-outline-primary">Top Picks</a>
            <a href="index.php?filter=availability" class="btn btn-outline-primary">In Stock</a>
        </div>

        <!-- Group 3: Rating Dropdown -->
        <div class="dropdown">
            <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                Rating
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="index.php?filter=rating_5">5 Stars</a></li>
                <li><a class="dropdown-item" href="index.php?filter=rating_4">4 Stars & Up</a></li>
                <li><a class="dropdown-item" href="index.php?filter=rating_3">3 Stars & Up</a></li>
            </ul>
        </div>

        <!-- Group 4: Genre Dropdown -->
        <div class="dropdown">
            <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                Genre
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="index.php?filter=fiction">Fiction</a></li>
                <li><a class="dropdown-item" href="index.php?filter=nonfiction">Non-Fiction</a></li>
            </ul>
        </div>

        <!-- Group 5: Search -->
        <form action="index.php" method="GET" class="d-flex ms-auto" style="max-width: 300px;">
            <input type="text" name="search" class="form-control rounded-0 rounded-start" placeholder="Search books..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            <button type="submit" class="btn btn-dark rounded-0 rounded-end">Search</button>
        </form>

    </div>

<?php
$filter = $_GET['filter'] ?? 'all';
$search = trim($_GET['search'] ?? '');

if ($filter === 'authors') {
    // Show authors list
    $sql = "SELECT * FROM authors";
    if (!empty($search)) {
        $searchEscaped = $conn->real_escape_string($search);
        $sql .= " WHERE name LIKE '%$searchEscaped%' OR bio LIKE '%$searchEscaped%'";
        $pageHeading = "Author Search Results for '".htmlspecialchars($search)."'";
    } else {
        $pageHeading = "Authors";
    }
    $sql .= " ORDER BY name ASC";
    $result = $conn->query($sql);
    ?>

    <h2 class="mb-4"><?= $pageHeading ?></h2>

    <?php if ($result && $result->num_rows > 0): ?>
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-6 g-4">
        <?php while ($author = $result->fetch_assoc()): ?>
            <div class="col">
                <div class="card h-100 shadow-sm">
                    <div class="card-body d-flex flex-column">
                        <h4 class="card-title"><?= htmlspecialchars($author['name']) ?></h4>
                        <p class="card-text"><?= nl2br(htmlspecialchars($author['bio'])) ?></p>
                        <a href="author_info.php?author_id=<?= $author['id'] ?>" class="btn btn-outline-primary mt-auto">View Books</a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
    <?php else: ?>
        <p class="text-center">No authors found.</p>
    <?php endif; ?>

<?php
} else {
    // Show books
    $sql = "
        SELECT b.*, a.name AS author_name, COALESCE(AVG(r.rating),0) AS avg_rating
        FROM books b
        JOIN authors a ON b.author_id = a.id
        LEFT JOIN reviews r ON r.book_id = b.id
    ";
    $conditions = [];
    $having = [];
    $orderBy = "b.title ASC";

    // Filters
    switch ($filter) {
        case 'fiction': $conditions[]="b.genre='Fiction'"; $pageHeading="Fiction"; break;
        case 'nonfiction': $conditions[]="b.genre='Non-Fiction'"; $pageHeading="Non-Fiction"; break;
        case 'availability': $conditions[]="b.stock>0"; $pageHeading="In Stock"; break;
        case 'top_picks': $sql.=" JOIN order_items oi ON b.id=oi.book_id"; $orderBy="SUM(oi.quantity) DESC"; $pageHeading="Top Picks"; break;
        case 'new_arrivals': $orderBy="b.created_at DESC"; $pageHeading="New Arrival"; break;
        case 'rating_5': $having[]="AVG(r.rating)=5"; $pageHeading="5 Stars"; break;
        case 'rating_4': $having[]="AVG(r.rating)>=4"; $pageHeading="4 Stars & Up"; break;
        case 'rating_3': $having[]="AVG(r.rating)>=3"; $pageHeading="3 Stars & Up"; break;
        case 'all':
        default: $pageHeading="All Books"; break;
    }

    // Simple search: title, author, genre, description
    if (!empty($search)) {
        $searchEscaped = $conn->real_escape_string($search);
        $conditions[] = "(b.title LIKE '%$searchEscaped%' OR a.name LIKE '%$searchEscaped%' OR b.genre LIKE '%$searchEscaped%' OR b.description LIKE '%$searchEscaped%')";
        $pageHeading = "Search Results for '".htmlspecialchars($search)."'";
    }

    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }

    $sql .= " GROUP BY b.id";

    if (!empty($having)) {
        $sql .= " HAVING " . implode(" AND ", $having);
    }

    $sql .= " ORDER BY $orderBy";

    $result = $conn->query($sql);
    ?>

    <h2 class="mb-4"><?= $pageHeading ?></h2>

    <?php if ($result && $result->num_rows > 0): ?>
    <ul class="row list-unstyled mt-3">
        <?php while ($row = $result->fetch_assoc()): ?>
            <li class="col-md-3 mb-4">
                <div class="card h-100 shadow-sm">
                    <img src="../assets/images/books/<?= $row['image'] ?? 'default.jpg' ?>" 
                         class="card-img-top" 
                         alt="<?= htmlspecialchars($row['title']) ?>" 
                         style="height:400px; object-fit:cover; border-radius:8px;">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?= htmlspecialchars($row['title']) ?></h5>
                        <p class="card-text">by <?= htmlspecialchars($row['author_name']) ?></p>
                        <p class="card-text"><strong>₱<?= number_format($row['price'],2) ?></strong></p>
                        <p class="card-text text-success"><?= $row['stock'] > 0 ? $row['stock'].' in stock' : '<span class="text-danger">Out of stock</span>' ?></p>
                        <p class="card-text">Rating: <?= number_format($row['avg_rating'],1) ?> ⭐</p>

                        <?php if($row['stock'] > 0): ?>
                        <form method="POST" action="../cart/cart_update.php" class="mt-auto">
                            <input type="number" name="item_qty" class="form-control mb-2" value="1" min="1" max="<?= $row['stock'] ?>">
                            <input type="hidden" name="item_id" value="<?= $row['id'] ?>">
                            <input type="hidden" name="type" value="add">
                            <button type="submit" class="btn btn-primary w-100">Add to Cart</button>
                        </form>
                        <?php endif; ?>

                        <a href="book_info.php?book_id=<?= $row['id'] ?>" class="btn btn-outline-secondary w-100 mt-2">View Details</a>
                    </div>
                </div>
            </li>
        <?php endwhile; ?>
    </ul>
    <?php else: ?>
        <p class="text-center">No books found.</p>
    <?php endif; ?>
<?php
}
?>

</div>

<?php include('../includes/footer.php'); ?>
