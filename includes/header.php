<?php
session_start();

// Check login and role status
$isLoggedIn = isset($_SESSION['user']);
$isAdmin = $isLoggedIn && isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Turning Page | <?= $pageTitle ?? 'Shop'; ?></title>
  <link rel="stylesheet" href="assets/css/styles.css">
</head>

<body>
  <header class="navbar">
    <div class="container">
      <!-- Brand -->
      <div class="brand">
        <a href="home.php">Turning Page</a>
      </div>

      <!-- Navigation Menu -->
      <nav class="nav-menu">
        <a href="home.php">Home</a>
        <a href="shop/cart.php">Cart</a>
        <a href="shop/checkout.php">Checkout</a>

        <!-- Dropdown for logged-in users -->
        <?php if ($isLoggedIn): ?>
          <div class="dropdown">
            <button class="dropbtn"><?= $isAdmin ? 'Admin' : 'Account'; ?> ⌄</button>
            <div class="dropdown-content">
              <?php if ($isAdmin): ?>
                <a href="admin/index.php">Manage Books</a>
                <a href="admin/orders.php">Orders</a>
                <a href="admin/users.php">Users</a>
              <?php else: ?>
                <a href="user/profile.php">Profile</a>
                <a href="user/myorders.php">My Orders</a>
              <?php endif; ?>
            </div>
          </div>
        <?php endif; ?>
      </nav>

      <!-- Search Bar -->
      <form class="search-form" method="GET" action="search.php">
        <input type="text" name="search" placeholder="Search books..." required>
        <button type="submit">Search</button>
      </form>

      <!-- Auth Links --> 
      <div class="auth-links">
        <?php if (!$isLoggedIn): ?>
          <a href="login.php" class="btn-login">Login</a>
          <a href="register.php" class="btn-register">Register</a>
        <?php else: ?>
          <span class="user-email"><?= htmlspecialchars($_SESSION['user']['email']); ?></span>
          <a href="logout.php" class="btn-logout">Logout</a>
        <?php endif; ?>
      </div>
    </div>
  </header>

  <main class="content">
