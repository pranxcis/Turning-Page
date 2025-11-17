<?php
// 1️⃣ Start session at the very top
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2️⃣ Include header and DB
include("../includes/header.php");
include("../config/database.php");

// Check if user is logged in
if (!isset($_SESSION['userId']) && !isset($_SESSION['user']['id'])) {
    header("Location: ../login.php");
    exit();
}

// Use user ID from session
$user_id = $_SESSION['userId'] ?? $_SESSION['user']['id'];
$pageTitle = "My Profile"; // For header

// 3️⃣ Fetch user info
$sql_user = "SELECT username, email FROM users WHERE id = ? LIMIT 1";
$stmt_user = mysqli_prepare($conn, $sql_user);
mysqli_stmt_bind_param($stmt_user, "i", $user_id);
mysqli_stmt_execute($stmt_user);
$res_user = mysqli_stmt_get_result($stmt_user);
$user = mysqli_fetch_assoc($res_user);
$username = $user['username'] ?? "";
$email = $user['email'] ?? "";

// 4️⃣ Fetch user profile
$sql_profile = "SELECT last_name, first_name, middle_initial, phone, address, town, zipcode, profile_picture FROM user_profiles WHERE user_id = ? LIMIT 1";
$stmt_p = mysqli_prepare($conn, $sql_profile);
mysqli_stmt_bind_param($stmt_p, "i", $user_id);
mysqli_stmt_execute($stmt_p);
$res_p = mysqli_stmt_get_result($stmt_p);
$profile = mysqli_fetch_assoc($res_p);
$last_name = $profile['last_name'] ?? "";
$first_name = $profile['first_name'] ?? "";
$middle_initial = $profile['middle_initial'] ?? "";
$phone = $profile['phone'] ?? "";
$address = $profile['address'] ?? "";
$town = $profile['town'] ?? "";
$zipcode = $profile['zipcode'] ?? "";
$profile_picture = $profile['profile_picture'] ?? "";

// 5️⃣ Set $_SESSION['user'] if not already set (for header.php compatibility)
if (!isset($_SESSION['user'])) {
    $_SESSION['user'] = [
        'id' => $user_id,
        'email' => $email,
        'role' => 'customer', // Assuming all registered users are customers
        'name' => $username
    ];
}

// 6️⃣ Handle profile picture upload
if (isset($_POST['upload_pic']) && !empty($_FILES['profile_picture']['name'])) {
    $uploadDir = "../assets/images/users/";
    $fileName = time() . "_" . basename($_FILES["profile_picture"]["name"]);
    $target = $uploadDir . $fileName;
    if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);
    if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target)) {
        $sql_pic = "UPDATE user_profiles SET profile_picture=? WHERE user_id=?";
        $stmt_pic = mysqli_prepare($conn, $sql_pic);
        mysqli_stmt_bind_param($stmt_pic, "si", $fileName, $user_id);
        mysqli_stmt_execute($stmt_pic);
        $_SESSION['success'] = "Profile picture updated.";
        $profile_picture = $fileName; // Update for immediate display
    } else {
        $_SESSION['message'] = "Failed to upload profile picture.";
    }
}

// 7️⃣ Handle full profile update
if (isset($_POST['submit'])) {
    $username = trim($_POST['username']);
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $middle_initial = trim($_POST['middle_initial']);
    $address = trim($_POST['address']);
    $town = trim($_POST['town']);
    $zipcode = trim($_POST['zipcode']);
    $phone = trim($_POST['phone']);

    // Update username
    $sql_u = "UPDATE users SET username=? WHERE id=?";
    $stmt_u = mysqli_prepare($conn, $sql_u);
    mysqli_stmt_bind_param($stmt_u, "si", $username, $user_id);
    mysqli_stmt_execute($stmt_u);

    // Update profile
    $sql_pu = "UPDATE user_profiles SET last_name=?, first_name=?, middle_initial=?, phone=?, address=?, town=?, zipcode=? WHERE user_id=?";
    $stmt_pu = mysqli_prepare($conn, $sql_pu);
    mysqli_stmt_bind_param($stmt_pu, "sssssssi", $last_name, $first_name, $middle_initial, $phone, $address, $town, $zipcode, $user_id);
    mysqli_stmt_execute($stmt_pu);

    // Update session name
    $_SESSION['user']['name'] = $username;

    $_SESSION['success'] = "Profile updated successfully.";
}
?>

<div class="container mt-5 mb-5">
    <?php include("../includes/alert.php"); ?>
    <div class="row">
        <!-- LEFT COLUMN: Profile Picture -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">Profile Picture</div>
                <div class="card-body text-center">
                    <form action="" method="POST" enctype="multipart/form-data">
                        <img src="<?php echo $profile_picture ? '../assets/images/users/' . $profile_picture : 'https://bootdey.com/img/Content/avatar/avatar1.png'; ?>" class="rounded-circle mb-3" style="width:150px; height:150px; object-fit:cover;">
                        <input type="file" name="profile_picture" class="form-control mb-3">
                        <button class="btn btn-dark btn-sm w-100" type="submit" name="upload_pic"> Upload New Picture </button>
                    </form>
                </div>
            </div>
        </div>
        <!-- RIGHT COLUMN: Account Details -->
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">Account Details</div>
                <div class="card-body">
                    <form action="" method="POST">
                        <!-- Username -->
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($username); ?>">
                        </div>
                        <!-- Name Fields -->
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Last Name</label>
                                <input type="text" name="last_name" class="form-control" value="<?php echo htmlspecialchars($last_name); ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">First Name</label>
                                <input type="text" name="first_name" class="form-control" value="<?php echo htmlspecialchars($first_name); ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Middle Initial</label>
                                <input type="text" name="middle_initial" maxlength="3" class="form-control" value="<?php echo htmlspecialchars($middle_initial); ?>">
                            </div>
                        </div>
                        <!-- Address -->
                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <input type="text" name="address" class="form-control" value="<?php echo htmlspecialchars($address); ?>">
                        </div>
                        <!-- Town & Zipcode -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Town</label>
                                <input type="text" name="town" class="form-control" value="<?php echo htmlspecialchars($town); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Zipcode</label>
                                <input type="text" name="zipcode" class="form-control" value="<?php echo htmlspecialchars($zipcode); ?>">
                            </div>
                        </div>
                        <!-- Phone -->
                        <div class="mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($phone); ?>">
                        </div>
                        <button type="submit" name="submit" class="btn btn-dark w-100"> Save Changes </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>