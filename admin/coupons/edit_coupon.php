<?php
session_start();
include('../../includes/header.php');
include('../../config/database.php');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    $_SESSION['message'] = "Access denied. Admins only.";
    header("Location: ../login.php");
    exit;
}

$id = intval($_GET['id'] ?? 0);
if($id <= 0){
    $_SESSION['message'] = "Invalid coupon ID";
    header("Location: ../manage_coupons.php");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM coupons WHERE id=?");
$stmt->bind_param("i",$id);
$stmt->execute();
$result = $stmt->get_result();
$coupon = $result->fetch_assoc();
if(!$coupon){
    $_SESSION['message'] = "Coupon not found";
    header("Location: ../manage_coupons.php");
    exit;
}
?>

<div class="container my-5">
    <h2>Edit Coupon</h2>
    <form method="POST" action="update_coupon.php">
        <input type="hidden" name="id" value="<?= $coupon['id'] ?>">

        <div class="mb-3">
            <label>Code</label>
            <input type="text" name="code" class="form-control" value="<?= htmlspecialchars($coupon['code']) ?>" required>
        </div>

        <div class="mb-3">
            <label>Type</label>
            <select name="type" class="form-control" required>
                <option value="discount" <?= $coupon['type']=='discount'?'selected':'' ?>>Discount</option>
                <option value="free_shipping" <?= $coupon['type']=='free_shipping'?'selected':'' ?>>Free Shipping</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Amount</label>
            <input type="number" step="0.01" name="amount" class="form-control" value="<?= htmlspecialchars($coupon['amount']) ?>" required>
        </div>

        <div class="mb-3">
            <label>Minimum Order</label>
            <input type="number" step="0.01" name="min_order" class="form-control" value="<?= htmlspecialchars($coupon['min_order']) ?>" required>
        </div>

        <div class="mb-3">
            <label>Expires At</label>
            <input type="date" name="expires_at" class="form-control" value="<?= htmlspecialchars($coupon['expires_at']) ?>" required>
        </div>

        <button type="submit" class="btn btn-primary">Update Coupon</button>
        <a href="../manage_coupons.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php include('../../includes/footer.php'); ?>
