<?php include 'config/database.php'; ?>

<?php include 'includes/header.php'; ?>

<div class="container" style="margin-top:2rem;">
    <h1>Welcome to Turning Page</h1>
    <p>Discover secondhand and collectible books at great prices.</p>
</div>

<div class="container" style="margin-top:2rem;">
    <h2>Available Books</h2>
    <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:1rem; margin-top:1rem;">
        <?php
        $query = "SELECT books.*, authors.name AS author_name FROM books JOIN authors ON books.author_id = authors.id";
        $result = $conn->query($query);
        while($row = $result->fetch_assoc()):
        ?>
            <div style="background:#fff; border-radius:10px; padding:1rem; box-shadow:0 2px 5px rgba(0,0,0,0.1);">
                <img src="../assets/uploads/<?= $row['image'] ?>" alt="<?= $row['title'] ?>" style="width:100%; height:250px; object-fit:cover; border-radius:8px;">
                <h3><?= $row['title'] ?></h3>
                <p>by <?= $row['author_name'] ?></p>
                <p><strong>₱<?= number_format($row['price'],2) ?></strong></p>
                <a href="book.php?id=<?= $row['id'] ?>" class="btn-login" style="display:inline-block; text-align:center;">View</a>
            </div>
        <?php endwhile; ?>
    </div>
</div>

    <?php include 'includes/footer.php'; ?>
