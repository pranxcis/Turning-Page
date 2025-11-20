<?php
session_start();
include('../includes/header.php');
include('../config/database.php');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    $_SESSION['message'] = "Access denied. Admins only.";
    header("Location: ../login.php");
    exit;
}

$keyword = strtolower(trim($_GET['search'] ?? ''));

$keyword_sql = '';
if ($keyword) {
    $keyword_sql = "WHERE LOWER(o.status) LIKE ? OR LOWER(u.email) LIKE ? OR o.id LIKE ?";
}

$sql = "
    SELECT 
        o.id, o.user_id, o.status, o.created_at,
        u.username, u.email,
        COUNT(oi.id) AS item_count,
        COALESCE(SUM(oi.price * oi.quantity),0) AS total_amount
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.id
    LEFT JOIN order_items oi ON o.id = oi.order_id
    $keyword_sql
    GROUP BY o.id
    ORDER BY o.created_at DESC
";

$stmt = $conn->prepare($sql);
if ($keyword) {
    $like_keyword = "%$keyword%";
    $stmt->bind_param("sss", $like_keyword, $like_keyword, $like_keyword);
}
$stmt->execute();
$result = $stmt->get_result();
$orderCount = $result->num_rows;
?>

<div class="d-flex">
    <?php include('../includes/admin_sidebar.php'); ?>

    <div class="container my-5">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Orders Management (<?= $orderCount ?>)</h2>
        </div>

        <form class="mb-4" method="GET" action="">
            <div class="input-group">
                <input 
                    type="text" 
                    name="search" 
                    class="form-control" 
                    placeholder="Search by Order ID, Customer Email, or Status..."
                    value="<?= htmlspecialchars($keyword) ?>"
                >
                <button class="btn btn-outline-secondary" type="submit">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </div>
        </form>

        <?php if ($orderCount > 0): ?>
            <div class="row g-4">
                <?php while ($order = $result->fetch_assoc()): ?>
                    <div class="col-12">
                        <div class="card p-3 shadow-sm d-flex flex-row justify-content-between align-items-center">

                            <div class="flex-grow-1">
                                <h5 class="mb-2">Order #<?= $order['id'] ?></h5>

                                <p class="mb-1">
                                    <strong>Customer:</strong>
                                    <?= htmlspecialchars($order['username']) ?> 
                                    (<?= htmlspecialchars($order['email']) ?>)
                                </p>

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

                            <div class="d-flex flex-column ms-3">
                                <a href="orders/view_order.php?id=<?= $order['id'] ?>" 
                                   class="btn btn-outline-primary btn-sm mb-2">
                                    <i class="fa-regular fa-eye"></i> View
                                </a>

                                <a href="orders/edit_order.php?id=<?= $order['id'] ?>" 
                                   class="btn btn-outline-warning btn-sm mb-2">
                                    <i class="fa-regular fa-pen-to-square"></i> Update
                                </a>

                                <a href="orders/delete_order.php?id=<?= $order['id'] ?>" 
                                   class="btn btn-outline-danger btn-sm"
                                   onclick="return confirm('Are you sure you want to delete this order?');">
                                    <i class="fa-solid fa-trash"></i> Delete
                                </a>
                            </div>

                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-warning text-center">No orders found.</div>
        <?php endif; ?>

    </div>
</div>

<?php
$stmt->close();
include('../includes/footer.php');
?>
