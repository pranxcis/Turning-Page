<?php
session_start();

// Example variables
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
$isLoggedIn = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Turning Page | <?php echo $pageTitle ?? 'Home'; ?></title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <header class="site-header">
    <div class="container">
      <div class="logo">
        📚 <a href="index.php">Turning Page</a>
      </div>

      <nav class="nav-links">
        <a href="index.php">Home</a>
        <a href="shop.php">Shop</a>
        <a href="about.php">About</a>
        <a href="contact.php">Contact</a>

        <?php if ($isLoggedIn): ?>
          <div class="dropdown">
            <button class="dropbtn">
              <?php echo htmlspecialchars($_SESSION['username']); ?> ⌄
            </button>
            <div class="dropdown-content">
              <a href="profile.php">Profile</a>
              <a href="orders.php">My Orders</a>
              <?php if ($isAdmin): ?>
                <a href="admin/dashboard.php">Admin Dashboard</a>
              <?php endif; ?>
              <a href="logout.php" class="logout">Logout</a>
            </div>
          </div>
          <a href="cart.php" class="cart-link">🛒 Cart</a>
        <?php else: ?>
          <a href="login.php" class="btn-login">Login</a>
          <a href="register.php" class="btn-register">Register</a>
        <?php endif; ?>
      </nav>
    </div>
  </header>

  <main class="content">
