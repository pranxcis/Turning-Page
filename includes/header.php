<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$base_url = '/TurningPage/'; 


// Login + Role
$isLoggedIn = isset($_SESSION['user']);
$isAdmin   = $isLoggedIn && ($_SESSION['user']['role'] ?? '') === 'admin';
$userName  = $isLoggedIn ? ($_SESSION['user']['name'] ?? '') : '';
$userRole  = $isLoggedIn ? ($_SESSION['user']['role'] ?? '') : '';

// Page title
$pageTitle = $pageTitle ?? 'Shop';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Turning Page | <?= htmlspecialchars($pageTitle) ?></title>

  <!-- Bootswatch Theme -->
  <link href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.2/dist/lux/bootstrap.min.css" rel="stylesheet">

  <!-- Font Awesome -->
  <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />

  <!-- Custom CSS -->
  <link rel="stylesheet" href="/TurningPage/assets/css/style.css">

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
<nav class="navbar navbar-expand-lg bg-body-tertiary">
  <div class="container-fluid">

    <!-- Brand -->
    <a class="navbar-brand" href="/TurningPage/home.php" style="padding-left:50px; padding-right:50px;">
      <i class="fa-solid fa-book-open-reader me-2"></i>Turning Page
    </a>

    <!-- Toggler -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarSupportedContent">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">

      <!-- LEFT NAV -->
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">

        <!-- Home -->
        <li class="nav-item">
          <a class="nav-link" href="/TurningPage/home.php">Home</a>
        </li>

        <!-- Shop Dropdown -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
            Shop
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="/TurningPage/shop/index.php?filter=all">Books</a></li>
            <li><a class="dropdown-item" href="/TurningPage/shop/index.php?filter=authors">Authors</a></li>
            <li><a class="dropdown-item" href="/TurningPage/shop/index.php?filter=new_arrivals">New Arrival</a></li>
            <li><a class="dropdown-item" href="/TurningPage/shop/index.php?filter=top_picks">Top Picks</a></li>
            <li><a class="dropdown-item" href="/TurningPage/shop/index.php?filter=availability">In Stock</a></li>
            <li><a class="dropdown-item" href="/TurningPage/shop/index.php?filter=rating_5">Rating</a></li>
            <li><a class="dropdown-item" href="/TurningPage/shop/index.php?filter=genre">Genre</a></li>

            <?php if ($isLoggedIn): ?>
              <li><a class="dropdown-item" href="/TurningPage/user/order_history.php">Order History</a></li>
            <?php endif; ?>
          </ul>
        </li>

         <!-- CART ICON -->
        <li class="nav-item me-3 position-relative">
          <a class="nav-link" href="/TurningPage/cart/cart_view.php" title="Cart">
            <i class="fa-solid fa-cart-shopping">   Cart</i>

            <?php
            $cartCount = isset($_SESSION['cart'])
                ? array_sum(array_column($_SESSION['cart'], 'quantity'))
                : 0;

            if ($cartCount > 0): ?>
              <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                <?= $cartCount ?>
              </span>
            <?php endif; ?>
          </a>
        </li>

      </ul>

      <!--
      <form action="/TurningPage/home.php" method="GET" class="d-flex me-3">
        <input class="form-control me-2" type="search" placeholder="Search books..." name="search">
        <button class="btn btn-outline-success" type="submit">Search</button>
      </form>
            -->

      <!-- RIGHT SIDE (User) -->
      <ul class="navbar-nav ms-auto">

        <!-- IF LOGGED IN — ACCOUNT DROPDOWN -->
        <?php if ($isLoggedIn): ?>

          <?php
          $avatar = !empty($_SESSION['user']['profile_image'])
            ? "/TurningPage/assets/images/users/" . $_SESSION['user']['profile_image']
            : "https://ui-avatars.com/api/?name=" . urlencode($userName) . "&background=0D6EFD&color=fff";
          ?>

          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center"
               href="#" role="button" data-bs-toggle="dropdown" title="Account">

              <!-- Avatar -->
              <img src="<?= $avatar ?>"
                   alt="Avatar"
                   class="rounded-circle me-2"
                   style="width:32px; height:32px; object-fit:cover;">

              <?= htmlspecialchars($userName) ?>
            </a>

            <ul class="dropdown-menu dropdown-menu-end" style="min-width:260px;">

              <!-- User Info -->
              <li class="px-3 py-2">
                <div class="d-flex align-items-center">
                  <img src="<?= $avatar ?>"
                       class="rounded-circle me-2"
                       style="width:45px; height:45px; object-fit:cover;">
                  <div>
                    <div class="fw-bold"><?= htmlspecialchars($userName) ?></div>
                    <small class="text-muted"><?= htmlspecialchars($userRole) ?></small>
                  </div>
                </div>
              </li>

              <li><hr class="dropdown-divider"></li>

              <!-- Account Stuff -->
              <li><a class="dropdown-item" href="/TurningPage/user/profile.php">
                <i class="fa-solid fa-user me-2"></i> Profile</a></li>

              <li><a class="dropdown-item" href="/TurningPage/user/order_history.php">
                <i class="fa-solid fa-box me-2"></i> Orders</a></li>

              <li><a class="dropdown-item" href="/TurningPage/user/review_history.php">
                <i class="fa-solid fa-star me-2"></i> Reviews</a></li>

              <!-- ADMIN DASHBOARD inside Account dropdown -->
              <?php if ($isAdmin): ?>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="/TurningPage/admin/dashboard.php">
                  <i class="fa-solid fa-toolbox me-2"></i> Admin Dashboard</a></li>
              <?php endif; ?>

              <li><hr class="dropdown-divider"></li>

              <!-- Logout -->
              <li><a class="dropdown-item" href="/TurningPage/user/logout.php">
                <i class="fa-solid fa-right-from-bracket me-2"></i> Logout</a></li>
            </ul>
          </li>

        <?php else: ?>

          <!-- NOT LOGGED IN: Login / Register -->
          <li class="nav-item">
            <a class="nav-link" href="/TurningPage/user/login.php">Login</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/TurningPage/user/register.php">Register</a>
          </li>

        <?php endif; ?>

      </ul>
    </div>
  </div>
</nav>
