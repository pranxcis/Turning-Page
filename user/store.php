<?php
session_start();
include("../config/database.php");  // Corrected path: go up one level to root
include("../includes/header.php");  // Corrected path: go up one level to root

// Handle form submission
if (isset($_POST['submit'])) {
    // Sanitize inputs
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirmPass = trim($_POST['confirmPass']);

    // Check required fields
    if (empty($email) || empty($password) || empty($confirmPass)) {
        $_SESSION['message'] = 'All fields are required.';
        header("Location: register.php");
        exit();
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['message'] = 'Invalid email format.';
        header("Location: register.php");
        exit();
    }

    // Confirm password match
    if ($password !== $confirmPass) {
        $_SESSION['message'] = 'Passwords do not match.';
        header("Location: register.php");
        exit();
    }

    // Check if email already exists
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

    // Hash password (consider upgrading to password_hash() for security)
    $hashedPass = sha1($password);

    // Insert into users
    $sql_user = "INSERT INTO users (email, password, username, role, status) VALUES (?, ?, '', 'customer', 'active')";
    $stmt_user = mysqli_prepare($conn, $sql_user);
    mysqli_stmt_bind_param($stmt_user, "ss", $email, $hashedPass);
    if (mysqli_stmt_execute($stmt_user)) {
        // Get user ID
        $user_id = mysqli_insert_id($conn);

        // Insert blank profile row
        $sql_profile = "INSERT INTO user_profiles (user_id, last_name, first_name, middle_initial, phone, address, town, zipcode, profile_picture) VALUES (?, '', '', '', '', '', '', '', '')";
        $stmt_profile = mysqli_prepare($conn, $sql_profile);
        mysqli_stmt_bind_param($stmt_profile, "i", $user_id);
        mysqli_stmt_execute($stmt_profile);

        // Set session for logged-in user (aligns with header.php expectations)
        $_SESSION['userId'] = $user_id; // For profile.php compatibility
        $_SESSION['user'] = [
            'id' => $user_id,
            'email' => $email,
            'role' => 'customer',
            'name' => '' // Username will be set in profile later
        ];

        // Redirect to profile page
        header("Location: profile.php");
        exit();
    } else {
        $_SESSION['message'] = "Failed to register user. Please try again.";
        header("Location: register.php");
        exit();
    }
} else {
    // If not a POST request, redirect back to register
    header("Location: register.php");
    exit();
}
?>