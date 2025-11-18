<?php
session_start();
include('../includes/header.php');
include('../config/database.php');

// ADMIN ONLY
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    $_SESSION['message'] = "Access denied. Admins only.";
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = intval($_POST['user_id']);
    $total_amount = floatval($_POST['total_amount']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    $sql = "INSERT INTO orders (user_id, total_amount, status, created_at) 
            VALUES ($user_id, $total_amount, '$status', NOW())";

    if (mysqli_query($conn, $sql)) {
        $_SESSION['message'] = "New order created successfully.";
        header("Location: ../admin/manage_orders.php");
        exit;
    } else {
        $error = mysqli_error($conn);
    }
}
?>

<div class="container my-5">
    <h2>Create New Order</h2>
    <?php if (!empty($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
    <form method="POST">
        <div class="mb-3">
            <label>User ID</label>
            <input type="number" name="user_id" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Total Amount</label>
            <input type="number" step="0.01" name="total_amount" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Status</label>
            <input type="text" name="status" class="form-control" required>
        </div>
        <button class="btn btn-primary">Create Order</button>
    </form>
</div>

<?php include('../includes/footer.php'); ?>
