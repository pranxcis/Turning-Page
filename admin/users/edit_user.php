<?php
session_start();
$pageTitle = "Edit User";
include('../../config/database.php');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    $_SESSION['message'] = "Access denied. Admins only.";
    header("Location: ../login.php");
    exit;
}

$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($user_id <= 0) {
    $_SESSION['message'] = "Invalid user ID.";
    header("Location: ../manage_users.php");
    exit;
}

$stmt = $conn->prepare("
    SELECT u.id, u.username, u.email, u.role, u.status,
           p.first_name, p.last_name, p.middle_initial, p.phone, p.address, p.town, p.zipcode, p.profile_picture
    FROM users u
    LEFT JOIN user_profiles p ON u.id = p.user_id
    WHERE u.id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    $_SESSION['message'] = "User not found.";
    header("Location: ../manage_users.php");
    exit;
}

include('../../includes/header.php');
?>

<div class="container my-5">
    <h1><?= $pageTitle ?></h1>
    <form action="update_user.php" method="POST" enctype="multipart/form-data" class="mt-4">
        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">

        <div class="row g-3">

            <div class="col-md-5">
                <label class="form-label">First Name</label>
                <input type="text" name="first_name" class="form-control" required value="<?= htmlspecialchars($user['first_name']) ?>">
            </div>

            <div class="col-md-5">
                <label class="form-label">Last Name</label>
                <input type="text" name="last_name" class="form-control" required value="<?= htmlspecialchars($user['last_name']) ?>">
            </div>

            <div class="col-md-2">
                <label class="form-label">Middle Initial</label>
                <input type="text" name="middle_initial" class="form-control" maxlength="1" value="<?= htmlspecialchars($user['middle_initial']) ?>">
            </div>

            <div class="col-md-12">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required value="<?= htmlspecialchars($user['username']) ?>">
            </div>

            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($user['email']) ?>">
            </div>

            <div class="col-md-6">
                <label class="form-label">Password (leave blank to keep current)</label>
                <input type="password" name="password" class="form-control">
            </div>

            <div class="col-md-6">
                <label class="form-label">Role</label>
                <select name="role" class="form-select" required>
                    <option value="customer" <?= $user['role'] === 'customer' ? 'selected' : '' ?>>Customer</option>
                    <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone']) ?>">
            </div>

            <div class="col-md-6">
                <label class="form-label">Profile Picture</label>
                <?php if ($user['profile_picture']): ?>
                    <div class="mb-2">
                        <img src="../../assets/images/users/<?= htmlspecialchars($user['profile_picture']) ?>" alt="Profile Picture" width="80">
                    </div>
                <?php endif; ?>
                <input type="file" name="profile_picture" class="form-control">
            </div>

            <div class="col-12">
                <label class="form-label">Address</label>
                <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($user['address']) ?>">
            </div>

            <div class="col-md-6">
                <label class="form-label">Town</label>
                <input type="text" name="town" class="form-control" value="<?= htmlspecialchars($user['town']) ?>">
            </div>

            <div class="col-md-6">
                <label class="form-label">Zipcode</label>
                <input type="text" name="zipcode" class="form-control" value="<?= htmlspecialchars($user['zipcode']) ?>">
            </div>

            <div class="col-12 mt-3">
                <button type="submit" class="btn btn-primary">Update User</button>
                <a href="../manage_users.php" class="btn btn-secondary">Cancel</a>
            </div>

        </div>
    </form>
</div>

<?php include('../../includes/footer.php'); ?>
