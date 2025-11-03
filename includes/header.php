<?php
session_start();
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
$isLoggedIn = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Turning Page | <?php echo $pageTitle ?? 'Shop'; ?></title>
  <link rel="stylesheet" href="includes/style/styles.css">
</head>
<body>
  <header class="navbar">
    <div class="container">
      <div class="brand">
        <a href="/TurningPage/index.php">📚 Turning Page</a>
      </div>

      <nav class="nav-menu">
        <a href="/TurningPage/index.php">Home</a>
        <a href="/TurningPage/shop.php">Shop</a>
        <a href="/TurningPage/about.php">About</a>
        <a href="/TurningPage/contact.php">Contact</a>

        <?php if ($isLoggedIn): ?>
          <div class="dropdown">
            <button class="dropbtn"><?php echo $isAdmin ? 'Admin' : 'Account'; ?> ⌄</button>
            <div class="dropdown-content">
              <?php if ($isAdmin): ?>
                <a href="/TurningPage/admin/index.php">Items</a>
                <a href="/TurningPage/admin/orders.php">Orders</a>
                <a href="/TurningPage/admin/users.php">Users</a>
              <?php else: ?>
                <a href="/TurningPage/user/profile.php">Profile</a>
                <a href="/TurningPage/user/myorders.php">My Orders</a>
              <?php endif; ?>
            </div>
          </div>
        <?php endif; ?>
      </nav>

      <form action="/TurningPage/search.php" method="GET" class="search-form">
        <input type="text" name="search" placeholder="Search books..." required>
        <button type="submit">🔍</button>
      </form>

      <div class="auth-links">
        <?php if (!$isLoggedIn): ?>
          <a href="/TurningPage/user/login.php" class="btn-login">Login</a>
          <a href="/TurningPage/user/register.php" class="btn-register">Register</a>
        <?php else: ?>
          <span class="user-email"><?php echo htmlspecialchars($_SESSION['email']); ?></span>
          <a href="/TurningPage/user/logout.php" class="btn-logout">Logout</a>
        <?php endif; ?>
      </div>
    </div>
  </header>

  <main class="content">
