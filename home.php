<?php include 'includes/header.php'; ?>
<?php include 'config/database.php'; ?>

<!-- HERO -->
<div class="hero text-center p-5 rounded" style="overflow:hidden; margin: 10rem 0;">
    <h1 class="mb-3" style="font-size:3rem; margin-top:5rem;">Turning Page</h1>
    <p class="mb-4" style="font-size:1.2rem;">Discover secondhand and collectible books at great prices.</p>
    <form action="shop/index.php" method="GET" class="d-flex mx-auto" style="max-width:500px; margin: 5rem;">
        <input type="text" name="search" placeholder="Search books..." class="form-control rounded-0 rounded-start">
        <button type="submit" class="btn btn-dark rounded-0 rounded-end">Search</button>
    </form>
</div>

<div class="container my-5">

<!-- NEW RELEASES -->
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
        ?>
            <li class="col-md-3 mb-4">
                <div class="card h-100 shadow-sm" style="cursor:pointer;" onclick="window.location='shop/book_info.php?id=<?= $row['id'] ?>';">
                    <img src="assets/images/books/<?= $row['image'] ?? 'default.jpg' ?>" class="card-img-top" alt="<?= htmlspecialchars($row['title']) ?>" style="height:400px; object-fit:cover; border-radius:8px;">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($row['title']) ?></h5>
                        <p class="card-text">by <?= htmlspecialchars($row['author_name']) ?></p>
                        <p class="card-text"><strong>₱<?= number_format($row['price'],2) ?></strong></p>
                        <a href="shop/book_info.php?id=<?= $row['id'] ?>" class="btn btn-primary">View</a>
                    </div>
                </div>
            </li>
        <?php endwhile; ?>
    </ul>
</div>

<!-- BEST SELLERS -->
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
        ?>
            <li class="col-md-3 mb-4">
                <div class="card h-100 shadow-sm" style="cursor:pointer;" onclick="window.location='shop/book_info.php?id=<?= $row['id'] ?>';">
                    <img src="assets/images/books/<?= $row['image'] ?? 'default.jpg' ?>" class="card-img-top" alt="<?= htmlspecialchars($row['title']) ?>" style="height:400px; object-fit:cover; border-radius:8px;">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($row['title']) ?></h5>
                        <p class="card-text">by <?= htmlspecialchars($row['author_name']) ?></p>
                        <p class="card-text"><strong>₱<?= number_format($row['price'],2) ?></strong></p>
                        <a href="shop/book_info.php?id=<?= $row['id'] ?>" class="btn btn-primary">View</a>
                    </div>
                </div>
            </li>
        <?php endwhile; ?>
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
        ?>
            <li class="col-md-3 mb-4">
                <div class="card h-100 shadow-sm" style="cursor:pointer;" onclick="window.location='shop/book_info.php?id=<?= $row['id'] ?>';">
                    <img src="assets/images/books/<?= $row['image'] ?? 'default.jpg' ?>" class="card-img-top" alt="<?= htmlspecialchars($row['title']) ?>" style="height:400px; object-fit:cover; border-radius:8px;">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($row['title']) ?></h5>
                        <p class="card-text">by <?= htmlspecialchars($row['author_name']) ?></p>
                        <p class="card-text"><strong>₱<?= number_format($row['price'],2) ?></strong></p>
                        <a href="shop/book_info.php?id=<?= $row['id'] ?>" class="btn btn-primary">View</a>
                    </div>
                </div>
            </li>
        <?php endwhile; ?>
    </ul>
</div>

</div>

<?php include 'includes/footer.php'; ?>
