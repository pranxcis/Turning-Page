<?php
session_start();
include("../config/database.php");  
include("../includes/header.php");  

if (isset($_POST['submit'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirmPass = trim($_POST['confirmPass']);

    if (empty($email) || empty($password) || empty($confirmPass)) {
        $_SESSION['message'] = 'All fields are required.';
        header("Location: register.php");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['message'] = 'Invalid email format.';
        header("Location: register.php");
        exit();
    }

    if ($password !== $confirmPass) {
        $_SESSION['message'] = 'Passwords do not match.';
        header("Location: register.php");
        exit();
    }

    $sql_check = "SELECT id FROM users WHERE email = ? LIMIT 1";
    $stmt_check = mysqli_prepare($conn, $sql_check);
    mysqli_stmt_bind_param($stmt_check, "s", $email);
    mysqli_stmt_execute($stmt_check);
    mysqli_stmt_store_result($stmt_check);
    if (mysqli_stmt_num_rows($stmt_check) > 0) {
        $_SESSION['message'] = 'Email already registered.';
        header("Location: register.php");
        exit();
    }

    $hashedPass = sha1($password);

    $sql_user = "INSERT INTO users (email, password, username, role, status) VALUES (?, ?, '', 'customer', 'active')";
    $stmt_user = mysqli_prepare($conn, $sql_user);
    mysqli_stmt_bind_param($stmt_user, "ss", $email, $hashedPass);
    if (mysqli_stmt_execute($stmt_user)) {
        $user_id = mysqli_insert_id($conn);

        $sql_profile = "INSERT INTO user_profiles (user_id, last_name, first_name, middle_initial, phone, address, town, zipcode, profile_picture) VALUES (?, '', '', '', '', '', '', '', '')";
        $stmt_profile = mysqli_prepare($conn, $sql_profile);
        mysqli_stmt_bind_param($stmt_profile, "i", $user_id);
        mysqli_stmt_execute($stmt_profile);

        $_SESSION['userId'] = $user_id; 
        $_SESSION['user'] = [
            'id' => $user_id,
            'email' => $email,
            'role' => 'customer',
            'name' => '' 
        ];

        header("Location: profile.php");
        exit();
    } else {
        $_SESSION['message'] = "Failed to register user. Please try again.";
        header("Location: register.php");
        exit();
    }
} else {
    header("Location: register.php");
    exit();
}
?>