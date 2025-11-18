<?php
session_start();
$pageTitle = "My Book Cart";
include('../includes/header.php');
include('../config/database.php');

// Must be logged in
if (!isset($_SESSION['user']['id'])) {
    $_SESSION['message'] = "Please login to view your cart.";
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];

// Fetch cart items from database
$stmt = $conn->prepare("
    SELECT c.book_id, c.quantity, b.title, b.description, b.price, b.stock, b.image
    FROM cart_items c
    JOIN books b ON b.id = c.book_id
    WHERE c.user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$cart_items = [];
$subtotal = 0;

while ($row = $result->fetch_assoc()) {
    $row['quantity'] = intval($row['quantity']);
    $row['line_total'] = $row['quantity'] * floatval($row['price']);
    $subtotal += $row['line_total'];
    $cart_items[] = $row;
}

$stmt->close();
?>

<div class="container my-5">
    <h1 class="text-center mb-4"><?= $pageTitle ?></h1>

    <?php if (!empty($cart_items)): ?>
        <form method="POST" action="../cart/cart_update.php">
            <div class="row">
                <!-- LEFT SIDE: Cart items -->
                <div class="col-lg-8">
                    <?php foreach ($cart_items as $cart_itm): ?>
                        <?php
                            $book_id = intval($cart_itm['book_id']);
                            $title = htmlspecialchars($cart_itm['title']);
                            $desc = htmlspecialchars($cart_itm['description'] ?? '');
                            $price = floatval($cart_itm['price']);
                            $qty = intval($cart_itm['quantity']);
                            $stock = intval($cart_itm['stock']);
                            $image = !empty($cart_itm['image']) ? $cart_itm['image'] : 'default_book.png';
                            $line_total = $cart_itm['line_total'];
                        ?>
                        <div class="card mb-3">
                            <div class="row g-0 align-items-center">
                                <div class="col-md-3 text-center">
                                    <img src="../assets/images/books/<?= $image ?>" class="img-fluid p-2" alt="<?= $title ?>" style="max-height:180px; object-fit:cover;">
                                </div>
                                <div class="col-md-9">
                                    <div class="card-body">
                                        <h5 class="card-title"><?= $title ?></h5>
                                        <?php if(!empty($desc)): ?>
                                            <p class="card-text mb-1"><strong>Description:</strong> <?= $desc ?></p>
                                        <?php endif; ?>
                                        <p class="card-text mb-1"><strong>Stock:</strong> <?= $stock ?></p>
                                        <p class="card-text mb-1"><strong>Price:</strong> ₱ <?= number_format($price,2) ?></p>
                                        <div class="d-flex align-items-center mb-2">
                                            <label for="qty_<?= $book_id ?>" class="me-2">Qty:</label>
                                            <input type="number" id="qty_<?= $book_id ?>" name="product_qty[<?= $book_id ?>]" value="<?= $qty ?>" min="1" max="<?= $stock ?>" class="form-control" style="width:80px;">
                                        </div>
                                        <p class="card-text mb-1"><strong>Subtotal:</strong> ₱ <?= number_format($line_total,2) ?></p>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="remove_code[]" value="<?= $book_id ?>" id="remove_<?= $book_id ?>">
                                            <label class="form-check-label" for="remove_<?= $book_id ?>">Remove</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- RIGHT SIDE: Summary -->
                <div class="col-lg-4">
                    <div class="card p-3">
                        <h4>Order Summary</h4>
                        <hr>
                        <p><strong>Subtotal:</strong> ₱ <?= number_format($subtotal,2) ?></p>
                        <p><strong>Shipping:</strong> ₱ <?= number_format(50,2) ?> <!-- placeholder --></p>
                        <p><strong>Voucher:</strong> ₱ <?= number_format(0,2) ?> <!-- placeholder --></p>
                        <hr>
                        <h5>Total: ₱ <?= number_format($subtotal + 50,2) ?></h5>
                        <button type="submit" class="btn btn-primary w-100 mb-2">Update Cart</button>
                        <a href="../cart/cart_checkout.php" class="btn btn-success w-100">Proceed to Checkout</a>
                    </div>
                </div>
            </div>
        </form>
    <?php else: ?>
        <div class="alert alert-info text-center">
            Your cart is empty. <a href="../shop/index.php">Go shopping</a>.
        </div>
    <?php endif; ?>
</div>

<?php include('../includes/footer.php'); ?>
