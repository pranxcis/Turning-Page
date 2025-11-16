<?php
session_start();
include("includes/header.php");
?>

<div class="container">
    <div class="row align-items-center" style="min-height: 80vh;">
        <!-- Left Column: Title and Description -->
        <div class="col-md-6 px-5" style="color: #222;">
            <h1 class="display-4 fw-bold">Welcome</h1>
            <p class="lead" style="color: #444;">
                It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using
            </p>
            <div class="d-flex gap-3 mt-4">
                <a href="#" style="color: #222;" class="fs-4"><i class="fab fa-facebook-f"></i></a>
                <a href="#" style="color: #222;" class="fs-4"><i class="fab fa-twitter"></i></a>
                <a href="#" style="color: #222;" class="fs-4"><i class="fab fa-instagram"></i></a>
                <a href="#" style="color: #222;" class="fs-4"><i class="fab fa-youtube"></i></a>
            </div>
        </div>

        <!-- Right Column: Register Form -->
        <div class="col-md-6 px-5" style="color: #222;">
            <?php include("includes/alert.php"); ?>
            <h2 class="mb-4">Register Now</h2>

            <form action="store.php" method="POST" novalidate>
                <div class="mb-3">
                    <label for="email" class="form-label" style="color: #222;">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" required autocomplete="email" />
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label" style="color: #222;">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required autocomplete="new-password" />
                </div>

                <div class="mb-3">
                    <label for="confirmPass" class="form-label" style="color: #222;">Confirm Password</label>
                    <input type="password" id="confirmPass" name="confirmPass" class="form-control" required autocomplete="new-password" />
                </div>

                <button type="submit" name="submit" class="btn" style="background-color: #000; color: #fff; width: 100%; font-weight: 700; margin-bottom: 1rem;">
                    Register
                </button>

            </form>
        </div>
    </div>
</div>
