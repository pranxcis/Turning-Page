<?php
session_start();
include('../../config/database.php');

// Admin access only
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    $_SESSION['message'] = "Access denied. Admins only.";
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = intval($_POST['user_id']);
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = ($_POST['role'] === 'admin') ? 'admin' : 'customer';
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->bind_param("si", $email, $user_id);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $_SESSION['message'] = "Email already in use.";
        header("Location: edit_user.php?id=$user_id");
        exit;
    }
    $stmt->close();

    if (!empty($password)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET username=?, email=?, password=?, role=? WHERE id=?");
        $stmt->bind_param("ssssi", $username, $email, $hashed, $role, $user_id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET username=?, email=?, role=? WHERE id=?");
        $stmt->bind_param("sssi", $username, $email, $role, $user_id);
    }
    $stmt->execute();
    $stmt->close();

    $profile_picture = null;
    if (!empty($_FILES['profile_picture']['name'])) {
        $ext = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
        $profile_picture = "user_$user_id.".$ext;
        move_uploaded_file($_FILES['profile_picture']['tmp_name'], "../../assets/images/users/$profile_picture");

        $stmt = $conn->prepare("UPDATE user_profiles SET profile_picture=? WHERE user_id=?");
        $stmt->bind_param("si", $profile_picture, $user_id);
        $stmt->execute();
        $stmt->close();
    }

    $stmt = $conn->prepare("
        UPDATE user_profiles
        SET first_name=?, last_name=?, middle_initial=?, phone=?, address=?, town=?, zipcode=?
        WHERE user_id=?
    ");
    $stmt->bind_param(
        "sssssssi",
        $_POST['first_name'],
        $_POST['last_name'],
        $_POST['middle_initial'],
        $_POST['phone'],
        $_POST['address'],
        $_POST['town'],
        $_POST['zipcode'],
        $user_id
    );
    $stmt->execute();
    $stmt->close();

    $_SESSION['message'] = "User updated successfully.";
    header("Location: ../manage_users.php");
    exit;
}
