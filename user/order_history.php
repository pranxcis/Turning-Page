<?php
session_start();
include('../includes/header.php');
include('../config/database.php');

// ------------------------
// USER MUST BE LOGGED IN
// ------------------------
if (!isset($_SESSION['user'])) {
    $_SESSION['message'] = "Please login to view your order history.";
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];

// ------------------------
// FETCH USER ORDERS WITH ITEM COUNT & TOTALS
// ------------------------
$sql = "
    SELECT 
        o.id, o.status, o.created_at,
        COUNT(oi.id) AS item_count,
        COALESCE(SUM(oi.price * oi.quantity),0) AS total_amount
    FROM orders o
    LEFT JOIN order_items oi ON o.id = oi.order_id
    WHERE o.user_id = ?
    GROUP BY o.id
    ORDER BY o.created_at DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$orderCount = $result->num_rows;
?>

<div class="container my-5">
    <h2 class="mb-4">My Order History (<?= $orderCount ?>)</h2>

    <?php if ($orderCount > 0): ?>
        <div class="row g-4">
            <?php while ($order = $result->fetch_assoc()): ?>
                <div class="col-12">
                    <div class="card p-3 shadow-sm d-flex flex-row justify-content-between align-items-center">

                        <!-- ORDER DETAILS -->
                        <div class="flex-grow-1">
                            <h5 class="mb-2">Order #<?= $order['id'] ?></h5>

                            <p class="mb-1"><strong>Items:</strong> <?= $order['item_count'] ?> item(s)</p>
                            <p class="mb-1"><strong>Total Amount:</strong> ₱<?= number_format($order['total_amount'], 2) ?></p>

                            <p class="mb-1">
                                <strong>Status:</strong>
                                <?php
                                    $status = $order['status'];

                                    switch($status) {
                                        case 'Pending':
                                            $badgeClass = 'bg-warning text-dark';
                                            break;
                                        case 'Paid':
                                            $badgeClass = 'bg-primary';
                                            break;
                                        case 'Shipped':
                                            $badgeClass = 'bg-info text-dark';
                                            break;
                                        case 'Delivered':
                                            $badgeClass = 'bg-success';
                                            break;
                                        case 'Cancelled':
                                            $badgeClass = 'bg-danger';
                                            break;
                                        default:
                                            $badgeClass = 'bg-secondary';
                                    }
                                ?>
                                <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($status) ?></span>
                            </p>

                            <p class="mb-1"><strong>Date:</strong> <?= date("F d, Y h:i A", strtotime($order['created_at'])) ?></p>
                        </div>

                        <!-- VIEW BUTTON -->
                        <div class="d-flex flex-column ms-3">
                            <a href="order_view.php?id=<?= $order['id'] ?>" 
                               class="btn btn-outline-primary btn-sm">
                                <i class="fa-regular fa-eye"></i> View
                            </a>
                        </div>

                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-warning text-center">You have no orders yet.</div>
    <?php endif; ?>
</div>

<?php
$stmt->close();
include('../includes/footer.php');
?>
