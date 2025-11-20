<?php
session_start();
include("../config/database.php"); 
    
if (isset($_POST['submit'])) {
    $email = trim($_POST['email']);
    $pass = sha1(trim($_POST['password']));

    $stmt = $conn->prepare("SELECT id, username, role FROM users WHERE email=? AND password=? LIMIT 1");
    $stmt->bind_param("ss", $email, $pass);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($user_id, $username, $role);

    if ($stmt->num_rows === 1) {
        $stmt->fetch();
        $_SESSION['user'] = [
            'id' => $user_id,
            'name' => $username,
            'role' => $role
        ];
        header("Location: ../home.php"); 
        exit();
    } else {
        $_SESSION['message'] = 'Wrong email or password';
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
