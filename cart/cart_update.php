<?php
session_start();
include('../config/database.php');

// Must be logged in
if (!isset($_SESSION['user']['id'])) {
    $_SESSION['message'] = "Please login to use the cart.";
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];

/* -----------------------------------------------------
   1. ADD TO CART (convert session version → database)
------------------------------------------------------ */
if (isset($_POST["type"]) && $_POST["type"] == 'add' && isset($_POST["item_qty"]) && $_POST["item_qty"] > 0) {

    $item_id = intval($_POST['item_id']);
    $item_qty = intval($_POST['item_qty']);

    // Check if book exists in DB
    $stmt = $conn->prepare("SELECT id FROM books WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $book_res = $stmt->get_result();

    if ($book_res->num_rows > 0) {

        // Check if item already in cart
        $check = $conn->prepare("SELECT id, quantity FROM cart_items WHERE user_id = ? AND book_id = ?");
        $check->bind_param("ii", $user_id, $item_id);
        $check->execute();
        $c = $check->get_result();

        if ($c->num_rows > 0) {
            // Update quantity
            $row = $c->fetch_assoc();
            $new_qty = $row['quantity'] + $item_qty;

            $update = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE id = ?");
            $update->bind_param("ii", $new_qty, $row['id']);
            $update->execute();

        } else {
            // Insert as new cart item
            $insert = $conn->prepare("INSERT INTO cart_items (user_id, book_id, quantity) VALUES (?, ?, ?)");
            $insert->bind_param("iii", $user_id, $item_id, $item_qty);
            $insert->execute();
        }
    }
}


/* -----------------------------------------------------
   2. UPDATE QUANTITY (database version)
------------------------------------------------------ */
if (isset($_POST["product_qty"]) && is_array($_POST["product_qty"])) {

    foreach ($_POST["product_qty"] as $book_id => $qty) {
        $qty = intval($qty);

        if ($qty > 0) {
            $update = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE user_id = ? AND book_id = ?");
            $update->bind_param("iii", $qty, $user_id, $book_id);
            $update->execute();
        }
    }
}


/* -----------------------------------------------------
   3. REMOVE ITEMS (database version)
------------------------------------------------------ */
if (isset($_POST["remove_code"]) && is_array($_POST["remove_code"])) {

    foreach ($_POST["remove_code"] as $book_id) {
        $del = $conn->prepare("DELETE FROM cart_items WHERE user_id = ? AND book_id = ?");
        $del->bind_param("ii", $user_id, $book_id);
        $del->execute();
    }
}


/* -----------------------------------------------------
   REDIRECT
------------------------------------------------------ */
$redirect = $_SERVER['HTTP_REFERER'] ?? '../shop/index.php';
header("Location: $redirect");
exit;

?>
