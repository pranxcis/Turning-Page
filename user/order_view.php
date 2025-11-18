<?php
session_start();
include('../includes/header.php');
include('../config/database.php');

// ------------------------
// USER MUST BE LOGGED IN
// ------------------------
if (!isset($_SESSION['user'])) {
    $_SESSION['message'] = "Please login to view your order.";
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];

// ------------------------
// VALIDATE ORDER ID
// ------------------------
$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($order_id <= 0) {
    echo "<div class='container my-4'><p>Invalid order selected.</p></div>";
    include('../includes/footer.php');
    exit;
}

// ------------------------
// FETCH ORDER DETAILS
// ------------------------
$sql_order = "
    SELECT * 
    FROM orders
    WHERE id = ? AND user_id = ?
    LIMIT 1
";
$stmt_order = $conn->prepare($sql_order);
$stmt_order->bind_param("ii", $order_id, $user_id);
$stmt_order->execute();
$result_order = $stmt_order->get_result();

if ($result_order->num_rows === 0) {
    echo "<div class='container my-4'><div class='alert alert-danger'>Order not found or access denied.</div></div>";
    include('../includes/footer.php');
    exit;
}

$order = $result_order->fetch_assoc();

// ------------------------
// FETCH ORDER ITEMS (include book_id for review)
// ------------------------
$sql_items = "
    SELECT oi.quantity, oi.price, b.id AS book_id, b.title, b.image
    FROM order_items oi
    LEFT JOIN books b ON oi.book_id = b.id
    WHERE oi.order_id = ?
";
$stmt_items = $conn->prepare($sql_items);
$stmt_items->bind_param("i", $order_id);
$stmt_items->execute();
$result_items = $stmt_items->get_result();
?>

<div class="container my-5">
    <h2 class="mb-4">Order #<?= $order['id'] ?></h2>

    <p><strong>Status:</strong> 
        <?php
            $status = $order['status'];
            switch($status) {
                case 'Pending': $badgeClass = 'bg-warning text-dark'; break;
                case 'Paid': $badgeClass = 'bg-primary'; break;
                case 'Shipped': $badgeClass = 'bg-info text-dark'; break;
                case 'Delivered': $badgeClass = 'bg-success'; break;
                case 'Cancelled': $badgeClass = 'bg-danger'; break;
                default: $badgeClass = 'bg-secondary';
            }
        ?>
        <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($status) ?></span>
    </p>

    <p><strong>Order Date:</strong> <?= date("F d, Y h:i A", strtotime($order['created_at'])) ?></p>

    <h4 class="mt-4">Items</h4>
    <div class="table-responsive">
        <table class="table table-bordered mt-2">
            <thead class="table-light">
                <tr>
                    <th>Book</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Subtotal</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $total = 0;
                while ($item = $result_items->fetch_assoc()): 
                    $subtotal = $item['quantity'] * $item['price'];
                    $total += $subtotal;
                ?>
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <?php if($item['image']): ?>
                                <img src="../assets/images/books/<?= htmlspecialchars($item['image']) ?>" alt="Book Image" width="50" height="70">
                            <?php endif; ?>
                            <?= htmlspecialchars($item['title']) ?>
                        </div>
                    </td>
                    <td><?= $item['quantity'] ?></td>
                    <td>₱<?= number_format($item['price'],2) ?></td>
                    <td>₱<?= number_format($subtotal,2) ?></td>
                    <td>
                        <a href="add_review.php?book_id=<?= $item['book_id'] ?>" 
                           class="btn btn-sm btn-outline-success">
                            <i class="fa-regular fa-star"></i> Add Review
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" class="text-end">Total:</th>
                    <th>₱<?= number_format($total,2) ?></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<?php
$stmt_order->close();
$stmt_items->close();
include('../includes/footer.php');
?>
