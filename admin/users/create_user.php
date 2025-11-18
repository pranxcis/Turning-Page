<?php
session_start();
$pageTitle = "Add User";
include('../../config/database.php');

// Admin access only
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    $_SESSION['message'] = "Access denied. Admins only.";
    header("Location: ../login.php");
    exit;
}

include('../../includes/header.php');
?>

<div class="container my-5">
    <h1><?= $pageTitle ?></h1>
    <form action="store_user.php" method="POST" enctype="multipart/form-data" class="mt-4">
        <div class="row g-3">

            <div class="col-md-5">
                <label class="form-label">First Name</label>
                <input type="text" name="first_name" class="form-control" required>
            </div>

            <div class="col-md-5">
                <label class="form-label">Last Name</label>
                <input type="text" name="last_name" class="form-control" required>
            </div>

            <div class="col-md-2">
                <label class="form-label">Middle Initial</label>
                <input type="text" name="middle_initial" class="form-control" maxlength="1">
            </div>

            <div class="col-md-12">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Role</label>
                <select name="role" class="form-select" required>
                    <option value="customer">Customer</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" class="form-control">
            </div>

            <div class="col-md-6">
                <label class="form-label">Profile Picture</label>
                <input type="file" name="profile_picture" class="form-control">
            </div>

            <div class="col-12">
                <label class="form-label">Address</label>
                <input type="text" name="address" class="form-control">
            </div>

            <div class="col-md-6">
                <label class="form-label">Town</label>
                <input type="text" name="town" class="form-control">
            </div>

            <div class="col-md-6">
                <label class="form-label">Zipcode</label>
                <input type="text" name="zipcode" class="form-control">
            </div>

            <div class="col-12 mt-3">
                <button type="submit" class="btn btn-primary">Create User</button>
                <a href="../manage_users.php" class="btn btn-secondary">Cancel</a>
            </div>

        </div>
    </form>
</div>

<?php include('../../includes/footer.php'); ?>
