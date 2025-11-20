<?php
session_start();
include("../config/database.php"); 

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email === '') {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if ($password === '') {
        $errors[] = "Password is required.";
    }

    if (empty($errors)) {

        $stmt = $conn->prepare("
            SELECT id, username, email, password, role, status 
            FROM users 
            WHERE email = ? LIMIT 1
        ");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {

                if ($user['status'] === 'inactive') {
                    $errors[] = "Your account has been deactivated.";
                } else {
                    $_SESSION['user'] = [
                        'id'    => $user['id'],
                        'name'  => $user['username'],
                        'email' => $user['email'],
                        'role'  => $user['role']
                    ];

                    header("Location: ../home.php");
                    exit;
                }

            } else {
                $errors[] = "Incorrect password.";
            }

        } else {
            $errors[] = "Email not found.";
        }
    }
}

include("../includes/header.php");
?>

    <div class="container">
        <div class="row align-items-center" style="min-height: 80vh;">

            <div class="col-md-6 px-5" style="color: #222;">
                <h1 class="display-4 fw-bold">Welcome to<br>Turning Page</h1>
                <p class="lead" style="color: #444;">
                    "I surrender who I've been for who you are<br>
                    For nothing makes me stronger than your fragile heart<br>
                    If I had only felt how it feels to be yours<br>
                    I would have known what I've been living for all along"
                </p>
                <div class="d-flex gap-3 mt-4">
                    <a href="#" style="color: #222;" class="fs-4"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" style="color: #222;" class="fs-4"><i class="fab fa-twitter"></i></a>
                    <a href="#" style="color: #222;" class="fs-4"><i class="fab fa-instagram"></i></a>
                    <a href="#" style="color: #222;" class="fs-4"><i class="fab fa-youtube"></i></a>
                </div>
            </div>

            <div class="col-md-6 px-5" style="color: #222;">
                <?php include("../includes/alert.php"); ?>
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

                    <button type="submit" name="submit" class="btn" style="background-color: #000; color: #fff; width: 100%; font-weight: 700; margin-bottom: 1rem;">
                        Sign in
                    </button>
                </form>
            </div>
        </div>
    </div>
