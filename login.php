<?php
session_start();
include("includes/header.php");
include("config/database.php");

if (isset($_POST['submit'])) {
    $email = trim($_POST['email']);
    $pass = sha1(trim($_POST['password']));

    $stmt = $conn->prepare("SELECT id, role FROM users WHERE email=? AND password=? LIMIT 1");
    $stmt->bind_param("ss", $email, $pass);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($user_id, $role);

    if ($stmt->num_rows === 1) {
        $stmt->fetch();
        $_SESSION['email'] = $email;
        $_SESSION['user_id'] = $user_id;
        $_SESSION['role'] = $role;
        header("Location: index.php");
        exit();
    } else {
        $_SESSION['message'] = 'Wrong email or password';
    }
}
?>

<div class="container">
    <div class="row align-items-center" style="min-height: 80vh;">
        <!-- Left Column: Title and Description -->
        <div class="col-md-6 px-5" style="color: #222;">
            <h1 class="display-4 fw-bold">Welcome</h1>
            <p class="lead" style="color: #444;">
                It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using
            </p>
            <div class="d-flex gap-3 mt-4">
                <!-- Darker social icons with black tone -->
                <a href="#" style="color: #222;" class="fs-4"><i class="fab fa-facebook-f"></i></a>
                <a href="#" style="color: #222;" class="fs-4"><i class="fab fa-twitter"></i></a>
                <a href="#" style="color: #222;" class="fs-4"><i class="fab fa-instagram"></i></a>
                <a href="#" style="color: #222;" class="fs-4"><i class="fab fa-youtube"></i></a>
            </div>
        </div>

        <!-- Right Column: Sign In Form -->
        <div class="col-md-6 px-5" style="color: #222;">
            <?php include("includes/alert.php"); ?>
            <h2 class="mb-4">Sign In Now</h2>

            <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" novalidate>
                <div class="mb-3">
                    <label for="email" class="form-label" style="color: #222;">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" required autocomplete="email" />
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label" style="color: #222;">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required autocomplete="current-password" />
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox" id="remember" name="remember" class="form-check-input" />
                    <label for="remember" class="form-check-label" style="color: #222;">Remember Me</label>
                </div>

                <!-- Black button -->
                <button type="submit" name="submit" class="btn" style="background-color: #000; color: #fff; width: 100%; font-weight: 700; margin-bottom: 1rem;">
                    Sign in
                </button>
            </form>
        </div>
    </div>
</div>
