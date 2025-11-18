<?php
session_start();
$pageTitle = "Manage Users";
include('../config/database.php');

// Admin access only
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    $_SESSION['message'] = "Access denied. Admins only.";
    header("Location: ../login.php");
    exit;
}

// Handle role update or status toggle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_type'])) {
    $user_id = intval($_POST['user_id'] ?? 0);

    if ($user_id > 0) {
        $action = $_POST['action_type'];

        if ($action === 'update_role' && isset($_POST['role'])) {
            $role = $_POST['role'] === 'admin' ? 'admin' : 'customer';
            $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
            $stmt->bind_param("si", $role, $user_id);
            $stmt->execute();
            $stmt->close();
            $_SESSION['message'] = "User role updated.";
        }

        if ($action === 'toggle_status') {
            $stmt = $conn->prepare("UPDATE users SET status = NOT status WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $stmt->close();
            $_SESSION['message'] = "User status updated.";
        }
    }

    header("Location: manage_users.php");
    exit;
}

// Filter buttons
$filter = $_GET['filter'] ?? 'all';

// Build query
$where = "";
$params = [];
$types = "";

if ($filter === 'admin') {
    $where = "WHERE u.role = ?";
    $params[] = 'admin';
    $types .= 's';
} elseif ($filter === 'customer') {
    $where = "WHERE u.role = ?";
    $params[] = 'customer';
    $types .= 's';
} elseif ($filter === 'active') {
    $where = "WHERE u.status = 1";
} elseif ($filter === 'deactivated') {
    $where = "WHERE u.status = 0";
}

// Fetch users
$sql = "SELECT u.id, u.username, u.email, u.role, u.status, u.created_at,
               p.first_name, p.last_name, p.middle_initial, p.phone, p.address, p.town, p.zipcode, p.profile_picture
        FROM users u
        LEFT JOIN user_profiles p ON u.id = p.user_id
        $where
        ORDER BY u.created_at DESC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$users = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

include('../includes/header.php');
?>

<div class="d-flex">
    <?php include('../includes/admin_sidebar.php'); ?>

    <div class="container my-5">
        <!-- Title and Filters -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><?= $pageTitle ?></h1>
            <div class="d-flex align-items-center flex-wrap">
                <!-- Add User Button -->
                <a href="add_user.php" class="btn btn-primary me-3 mb-2">
                    <i class="fas fa-plus me-1"></i> Add User
                </a>

                <!-- Filter Buttons -->
                <?php
                $filters = ['all' => 'All', 'admin' => 'Admin', 'customer' => 'Customer', 'active' => 'Active', 'deactivated' => 'Deactivated'];
                foreach ($filters as $key => $label):
                    $activeClass = ($filter === $key) ? 'btn-primary' : 'btn-outline-primary';
                ?>
                    <a href="?filter=<?= $key ?>" class="btn <?= $activeClass ?> me-2 mb-2"><?= $label ?></a>
                <?php endforeach; ?>
            </div>
        </div>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= $_SESSION['message'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <!-- User Cards -->
        <div class="row g-3">
            <?php if (!empty($users)): ?>
                <?php foreach ($users as $user): ?>
                    <div class="col-12">
                        <div class="card shadow-sm p-4">
                            <div class="d-flex align-items-center mb-1">
                                <!-- Profile Image -->
                                <div class="text-center me-5 ms-3" style="width:250px; flex-shrink:0; padding:10px;">
                                    <img src="../assets/images/users/<?= $user['profile_picture'] ?: 'default.png' ?>" 
                                         class="rounded border" width="220" height="220" alt="Profile">
                                </div>

                                <!-- Name, Role, Address, Contact, Status/Joined -->
                                <div class="flex-grow-1 d-flex flex-column justify-content-between" style="min-height:180px;">
                                    <div>
                                        <h5 class="mb-1 fw-bold" style="font-size:1.35rem;">
                                            <?= htmlspecialchars($user['last_name'] . ', ' . $user['first_name']) ?>
                                            <small class="text-muted" style="font-size:0.95rem;">(<?= htmlspecialchars($user['username']) ?>)</small>
                                        </h5>

                                        <!-- Role Dropdown -->
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                            <input type="hidden" name="action_type" value="update_role">
                                            <select name="role" class="form-select form-select-sm" onchange="this.form.submit()" style="width:auto; display:inline-block; font-size:0.95rem;">
                                                <option value="customer" <?= $user['role'] === 'customer' ? 'selected' : '' ?>>Customer</option>
                                                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                            </select>
                                        </form>

                                        <!-- Address -->
                                        <p class="mb-1 mt-4" style="font-size:0.95rem;">
                                            <?= htmlspecialchars($user['address'] ?? '-') ?>, <?= htmlspecialchars($user['town'] ?? '-') ?>, <?= htmlspecialchars($user['zipcode'] ?? '-') ?>
                                        </p>

                                        <!-- Email | Phone -->
                                        <p class="mb-1" style="font-size:0.95rem;">
                                            <?= htmlspecialchars($user['email']) ?> | <?= htmlspecialchars($user['phone'] ?? '-') ?>
                                        </p>
                                    </div>

                                    <!-- Status | Joined -->
                                    <div class="d-flex justify-content-start gap-3 mt-2 align-items-center">
                                        <span class="fw-bold" style="font-size:0.95rem;">Status:</span>
                                        <span class="badge <?= $user['status'] ? 'bg-success' : 'bg-danger' ?>" style="font-size:0.85rem;">
                                            <?= $user['status'] ? 'Active' : 'Deactivated' ?>
                                        </span>
                                        <span class="fw-bold ms-3" style="font-size:0.95rem;">Joined:</span>
                                        <span style="font-size:0.9rem;"><?= date('F d, Y', strtotime($user['created_at'])) ?></span>
                                    </div>
                                </div>

                                <!-- Action Icons -->
                                <div class="d-flex flex-row align-items-center pe-3 ms-4" style="gap:10px; min-width:50px;">
                                    <a href="users/edit_user.php?id=<?= $user['id'] ?>" class="text-decoration-none text-dark pe-4" title="Edit">
                                        <i class="fas fa-edit fa-lg"></i>
                                    </a>
                                    <a href="users/delete_user.php?id=<?= $user['id'] ?>" class="text-decoration-none text-dark pe-4" title="Delete">
                                        <i class="fas fa-trash fa-lg"></i>
                                    </a>
                                    <form method="POST" class="m-0 p-0">
                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                        <input type="hidden" name="action_type" value="toggle_status">
                                        <button type="submit" class="btn p-0 border-0 text-dark pe-4" title="<?= $user['status'] ? 'Deactivate' : 'Activate' ?>">
                                            <i class="fas <?= $user['status'] ? 'fa-user-slash' : 'fa-user-check' ?> fa-lg"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info text-center">No users found.</div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    </div>
</div>

<?php include('../includes/footer.php'); ?>
