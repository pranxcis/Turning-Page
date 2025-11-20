<?php
session_start();
$pageTitle = "Home";

include 'includes/header.php';
include 'config/database.php';
?>

<!-- Para sa Mainpage Home With Search Bar -->
<div class="hero text-center p-5 rounded" style="overflow:hidden; margin: 10rem 0;">
    <h1 class="mb-3" style="font-size:3rem; margin-top:5rem;">Turning Page</h1>
    <p class="mb-4" style="font-size:1.2rem;">Discover secondhand and collectible books at great prices.</p>
    <form action="shop/index.php" method="GET" class="d-flex mx-auto" style="max-width:500px; margin: 5rem;">
        <input type="text" name="search" placeholder="Search books..." class="form-control rounded-0 rounded-start">
        <button type="submit" class="btn btn-dark rounded-0 rounded-end">Search</button>
    </form>
</div>

<div class="container my-5">

<?php

function renderBookCard($row) {
    $imagePath = 'assets/images/books/';

    $image = (isset($row['image']) && !empty($row['image'])) ? $row['image'] : 'default.jpg';

    ?>
    <li class="col-md-3 mb-4">
        <a href="shop/book_info.php?book_id=<?php echo $row['id']; ?>" class="text-decoration-none text-dark">
            <div class="card h-100 shadow-sm">
                <img src="<?php echo $imagePath . $image; ?>" 
                     class="card-img-top" 
                     alt="<?php echo htmlspecialchars($row['title']); ?>" 
                     style="height:400px; object-fit:cover; border-radius:8px;">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h5>
                    <p class="card-text">by <?php echo htmlspecialchars($row['author_name']); ?></p>
                    <p class="card-text"><strong>₱<?php echo number_format($row['price'], 2); ?></strong></p>
                    <button class="btn btn-primary">View</button>
                </div>
            </div>
        </a>
    </li>
    <?php
}

?>

<!-- NEW RELEASE na books -->
<div class="my-5">
    <h2>New Releases</h2>
    <ul class="row list-unstyled mt-3">
        <?php
        $query = "SELECT b.*, a.name AS author_name
                  FROM books b
                  JOIN authors a ON b.author_id = a.id
                  ORDER BY b.created_at DESC
                  LIMIT 8";
        $result = $conn->query($query);
        while ($row = $result->fetch_assoc()):
            renderBookCard($row);
        endwhile;
        ?>
    </ul>
</div>

<!-- BEST SELLERS na books -->
<div class="my-5">
    <h2>Best Sellers</h2>
    <ul class="row list-unstyled mt-3">
        <?php
        $query = "SELECT b.*, a.name AS author_name, SUM(oi.quantity) AS sold
                  FROM books b
                  JOIN authors a ON b.author_id = a.id
                  JOIN order_items oi ON b.id = oi.book_id
                  GROUP BY b.id
                  ORDER BY sold DESC
                  LIMIT 8";
        $result = $conn->query($query);
        while ($row = $result->fetch_assoc()):
            renderBookCard($row);
        endwhile;
        ?>
    </ul>
</div>

<!-- ALL BOOKS -->
<div class="my-5">
    <h2>All Books</h2>
    <ul class="row list-unstyled mt-3">
        <?php
        $query = "SELECT b.*, a.name AS author_name
                  FROM books b
                  JOIN authors a ON b.author_id = a.id
                  ORDER BY b.title ASC";
        $result = $conn->query($query);
        while ($row = $result->fetch_assoc()):
            renderBookCard($row);
        endwhile;
        ?>
    </ul>
</div>

</div>

<?php include 'includes/footer.php'; ?>
