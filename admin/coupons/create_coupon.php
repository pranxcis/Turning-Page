<?php
session_start();
include('../../includes/header.php');
include('../../config/database.php');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    $_SESSION['message'] = "Access denied. Admins only.";
    header("Location: ../login.php");
    exit;
}

$code = $_SESSION['form_code'] ?? '';
$type = $_SESSION['form_type'] ?? '';
$amount = $_SESSION['form_amount'] ?? '';
$min_order = $_SESSION['form_min_order'] ?? '';
$expires_at = $_SESSION['form_expires_at'] ?? '';
unset($_SESSION['form_code'], $_SESSION['form_type'], $_SESSION['form_amount'], $_SESSION['form_min_order'], $_SESSION['form_expires_at']);
?>

<div class="container my-5">
    <h2>Add New Coupon</h2>
    <form method="POST" action="store_coupon.php">
        <div class="mb-3">
            <label>Code</label>
            <input type="text" name="code" class="form-control" value="<?= htmlspecialchars($code) ?>" required>
        </div>
        <div class="mb-3">
            <label>Type</label>
            <select name="type" class="form-control" required>
                <option value="">-- Select Type --</option>
                <option value="discount" <?= ($type=="discount")?"selected":"" ?>>Discount</option>
                <option value="free_shipping" <?= ($type=="free_shipping")?"selected":"" ?>>Free Shipping</option>
            </select>
        </div>
        <div class="mb-3">
            <label>Amount</label>
            <input type="number" step="1" name="amount" class="form-control" value="<?= htmlspecialchars($amount) ?>" required>
        </div>
        <div class="mb-3">
            <label>Minimum Order</label>
            <input type="number" step="1" name="min_order" class="form-control" value="<?= htmlspecialchars($min_order) ?>" required>
        </div>
        <div class="mb-3">
            <label>Expires At</label>
            <input type="date" name="expires_at" class="form-control" value="<?= htmlspecialchars($expires_at) ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Save Coupon</button>
        <a href="../manage_coupons.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php include('../../includes/footer.php'); ?>
