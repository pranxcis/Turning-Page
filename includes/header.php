<?php
// Check login and role status
$isLoggedIn = isset($_SESSION['user']);
$isAdmin = $isLoggedIn && isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin';
$userName = $isLoggedIn ? $_SESSION['user']['name'] : '';
$userRole = $isLoggedIn ? $_SESSION['user']['role'] : '';
$pageTitle = $pageTitle ?? 'Shop';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Turning Page | <?= htmlspecialchars($pageTitle); ?></title>

  <!-- Bootswatch Lux Theme (Bootstrap 5.3.2) -->
  <link href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.2/dist/lux/bootstrap.min.css" rel="stylesheet">

  <!-- Font Awesome Icons -->
  <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
        integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

  <!-- Custom CSS (follow Turning Page structure) -->
  <link rel="stylesheet" href="/TurningPage/assets/css/style.css">

  <!-- Bootstrap Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
          integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
          crossorigin="anonymous"></script>
</head>

<body>
<nav class="navbar navbar-expand-lg bg-body-tertiary">
  <div class="container-fluid">
    <!-- Brand -->
    <a class="navbar-brand" href="/TurningPage/home.php" style="padding-left: 50px; padding-right: 50px;">
      <i class="fa-solid fa-book-open-reader me-2"></i>Turning Page
    </a>

    <!-- Toggler -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" 
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Navbar Links -->
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <!-- Home -->
        <li class="nav-item">
          <a class="nav-link" aria-current="page" href="/TurningPage/home.php">Home</a>
        </li>

        <!-- Shop Dropdown -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Shop
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="/TurningPage/shop/index.php">All Books</a></li>
            <li><a class="dropdown-item" href="/TurningPage/shop/index.php">New Release</a></li>
            <li><a class="dropdown-item" href="/TurningPage/shop/categories.php">Categories</a></li>
            <li><a class="dropdown-item" href="/TurningPage/cart/cart.php">View Cart</a></li>
            <?php if ($isLoggedIn): ?>
              <li><a class="dropdown-item" href="/TurningPage/user/orders.php">Order History</a></li>
            <?php endif; ?>
          </ul>
        </li>

        <!-- Admin / Account Dropdown -->
        <?php if ($isLoggedIn): ?>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <?= $isAdmin ? 'Admin' : 'Account' ?>
          </a>
          <ul class="dropdown-menu">
            <?php if ($isAdmin): ?>
              <li><a class="dropdown-item" href="/TurningPage/admin/dashboard.php">Dashboard</a></li>
              <li><a class="dropdown-item" href="/TurningPage/admin/manage_books.php">Books</a></li>
              <li><a class="dropdown-item" href="/TurningPage/admin/manage_orders.php">Orders</a></li>
              <li><a class="dropdown-item" href="/TurningPage/admin/manage_users.php">Users</a></li>
              <li><a class="dropdown-item" href="/TurningPage/admin/manage_reviews.php">Reviews</a></li>
            <?php else: ?>
              <li class="dropdown-item-text">Hello, <strong><?= htmlspecialchars($userName) ?></strong></li>
              <li class="dropdown-item-text"><em>Role: <?= htmlspecialchars($userRole) ?></em></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="/TurningPage/user/profile.php">Profile</a></li>
              <li><a class="dropdown-item" href="/TurningPage/user/order_history.php">My Orders</a></li>
              <li><a class="dropdown-item" href="/TurningPage/user/reviews.php">My Reviews</a></li>
            <?php endif; ?>
          </ul>
        </li>
        <?php endif; ?>
      </ul>

      <!-- Search Form -->
      <form action="/TurningPage/home.php" method="GET" class="d-flex me-2">
        <input class="form-control me-2" type="search" placeholder="Search books..." aria-label="Search" name="search">
        <button class="btn btn-outline-success" type="submit">Search</button>
      </form>

      <!-- Login / Register / Logout -->
      <ul class="navbar-nav ms-auto">
        <?php if (!$isLoggedIn): ?>
          <li class="nav-item"><a class="nav-link" href="/TurningPage/login.php">Login</a></li>
          <li class="nav-item"><a class="nav-link" href="/TurningPage/register.php">Register</a></li>
        <?php else: ?>
          <li class="nav-item">
            <span class="navbar-text me-2"><?= htmlspecialchars($userName) ?></span>
          </li>
          <li class="nav-item"><a class="nav-link" href="/TurningPage/user/logout.php">Logout</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>