<?php
session_start();
include('../config/database.php');
include('../includes/mail.php');

// Redirect if user not logged in
if (!isset($_SESSION['user']['id'])) {
    $_SESSION['message'] = "Please login to continue to checkout.";
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];


// ----------------------
// Fetch user info & profile
// ----------------------
$stmt = $conn->prepare("
    SELECT *
    FROM view_user_profile
    WHERE user_id = ?
    LIMIT 1
");

$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// ----------------------
// Fetch cart items
// ----------------------
$stmt = $conn->prepare("
    SELECT *
    FROM view_cart_items
    WHERE user_id = ?
");

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$cart_items = [];
$subtotal = 0;
while ($row = $result->fetch_assoc()) {
    $quantity = isset($row['quantity']) ? intval($row['quantity']) : 1;
    $price = isset($row['price']) ? floatval($row['price']) : 0;
    $row['quantity'] = $quantity;
    $row['price'] = $price;
    $row['line_total'] = $quantity * $price;
    $subtotal += $row['line_total'];
    $cart_items[] = $row;
}
$stmt->close();

// ----------------------
// Defaults for display
// ----------------------
$shipping_method = 'Standard';
$payment_method = 'COD';
$voucher_code = '';
$shipping_fee = 80;
$discount_amount = 0;
$total = $subtotal + $shipping_fee - $discount_amount;

// ----------------------
// Handle POST requests
// ----------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // --- Function to recalc totals ---
    function calculateTotals($subtotal, $shipping_method, $voucher_code, $conn, &$shipping_fee, &$discount_amount, &$total) {
        // Shipping
        $shipping_fee = ($shipping_method === 'Express') ? 150 : (($shipping_method === 'Overnight') ? 250 : 80);

        // Voucher
        $discount_amount = 0;
        if (!empty($voucher_code)) {
            $voucher_code = strtoupper(trim($voucher_code));
            $stmt = $conn->prepare("SELECT * FROM coupons WHERE code=? AND (expires_at IS NULL OR expires_at>NOW()) LIMIT 1");
            $stmt->bind_param("s", $voucher_code);
            $stmt->execute();
            $voucher = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if ($voucher) {
                $discount_amount = ($voucher['type'] === 'fixed') ? floatval($voucher['amount']) : $subtotal * floatval($voucher['amount']) / 100;
            } else {
                $voucher_code = '';
                $discount_amount = 0;
                $_SESSION['message'] = "Invalid voucher code.";
            }
        }

        $total = $subtotal + $shipping_fee - $discount_amount;
    }

    // --- Update totals ---
    if (isset($_POST['update_totals'])) {
        $shipping_method = $_POST['shipping_method'] ?? 'Standard';
        $payment_method = $_POST['payment_method'] ?? 'COD';
        $voucher_code = $_POST['voucher_code'] ?? '';
        calculateTotals($subtotal, $shipping_method, $voucher_code, $conn, $shipping_fee, $discount_amount, $total);
    }

// --- Place order ---
if (isset($_POST['place_order'])) {
    $shipping_method = $_POST['shipping_method'] ?? 'Standard';
    $payment_method = $_POST['payment_method'] ?? 'COD';
    $voucher_code = $_POST['voucher_code'] ?? '';
    calculateTotals($subtotal, $shipping_method, $voucher_code, $conn, $shipping_fee, $discount_amount, $total);

    if (empty($cart_items)) {
        $_SESSION['message'] = "Your cart is empty.";
        header("Location: ../shop/index.php");
        exit;
    }

    // Insert order and order items inside a transaction
    $conn->begin_transaction();
    try {
        // Insert into orders
        $stmt = $conn->prepare("
            INSERT INTO orders (user_id, shipping_method, payment_method, voucher_code, shipping_fee, subtotal, total, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->bind_param("isssddd", $user_id, $shipping_method, $payment_method, $voucher_code, $shipping_fee, $subtotal, $total);
        $stmt->execute();
        $order_id = $stmt->insert_id;
        $stmt->close();

        // Insert order items and decrease stock
        $stmt = $conn->prepare("
            INSERT INTO order_items (order_id, book_id, quantity, price)
            VALUES (?, ?, ?, ?)
        ");
        $stock_stmt = $conn->prepare("
            UPDATE books SET stock = stock - ? WHERE id = ? AND stock >= ?
        ");
        foreach ($cart_items as $item) {
            // Insert order item
            $stmt->bind_param("iiid", $order_id, $item['book_id'], $item['quantity'], $item['price']);
            $stmt->execute();

            // Deduct stock safely
            $stock_stmt->bind_param("iii", $item['quantity'], $item['book_id'], $item['quantity']);
            $stock_stmt->execute();

            // Optional: check if stock update affected a row
            if ($stock_stmt->affected_rows === 0) {
                throw new Exception("Insufficient stock for " . $item['title']);
            }
        }
        $stmt->close();
        $stock_stmt->close();

        // Clear cart
        $stmt = $conn->prepare("DELETE FROM cart_items WHERE user_id=?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();

        $conn->commit();

                    // ----------------------
            // SEND ORDER EMAIL
            // ----------------------

            $customerEmail = $user['email'];
            $customerName  = $user['first_name'] . ' ' . $user['last_name'];

            $orderHtml  = "<h2>Thank you for your order!</h2>";
            $orderHtml .= "<p>Hi <strong>{$customerName}</strong>,</p>";
            $orderHtml .= "<p>Your order <strong>#{$order_id}</strong> has been received.</p>";

            $orderHtml .= "<h3>Order Items</h3>";
            $orderHtml .= "<table border='1' cellpadding='6' cellspacing='0' width='100%'>";
            $orderHtml .= "<tr><th>Title</th><th>Qty</th><th>Price</th><th>Total</th></tr>";

            foreach ($cart_items as $item) {
                $orderHtml .= "<tr>
                    <td>{$item['title']}</td>
                    <td>{$item['quantity']}</td>
                    <td>₱" . number_format($item['price'], 2) . "</td>
                    <td>₱" . number_format($item['line_total'], 2) . "</td>
                </tr>";
            }

            $orderHtml .= "</table>";

            $orderHtml .= "<p><strong>Subtotal:</strong> ₱" . number_format($subtotal, 2) . "</p>";
            $orderHtml .= "<p><strong>Shipping:</strong> ₱" . number_format($shipping_fee, 2) . " ({$shipping_method})</p>";
            $orderHtml .= "<p><strong>Discount:</strong> ₱" . number_format($discount_amount, 2) . "</p>";
            $orderHtml .= "<h3>Total: ₱" . number_format($total, 2) . "</h3>";

            $orderHtml .= "<p>We will notify you when your order ships.</p>";
            $orderHtml .= "<p>— Turning Page Team</p>";

            // Now send using your smtp_send_mail()
            $emailResult = smtp_send_mail(
                $customerEmail,
                "Your Order Confirmation — Order #{$order_id}",
                $orderHtml
            );

            if (!$emailResult['success']) {
                $_SESSION['message'] = "Order placed, but email failed: " . htmlspecialchars($emailResult['error']);
            } else {
                $_SESSION['message'] = "Order placed successfully! A confirmation email has been sent.";
            }

            header("Location: ../user/order_history.php");
            exit;


    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['message'] = "Error placing order: " . $e->getMessage();
        header("Location: cart_view.php");
        exit;
    }
}

} // end POST


// Now include header
include('../includes/header.php');

?>

<div class="container my-5">
    <h1 class="text-center mb-4">Checkout</h1>

    <?php if (!empty($cart_items)): ?>
    <form action="" method="POST" id="checkout-form"> <!-- For updates & place order, submit to same page -->

        <div class="row">
            <!-- LEFT SIDE -->
            <div class="col-lg-7 mb-1 mt-3">
                <div class="card shadow-sm p-4">
                    <h4>Order Summary</h4>
                </div>
                <!-- Customer Info Card -->
                <div class="card shadow-sm p-4 mb-4 mt-3 d-flex flex-row align-items-start">
                    <div class="text-center ms-4 me-5 mt-2 mb-2" style="width:180px; flex-shrink:0; padding:10px;">
                        <img src="../assets/images/users/<?= htmlspecialchars($user['profile_picture'] ?: 'default.png') ?>" 
                             class="rounded-circle border" width="150" height="150" alt="Profile">
                    </div>
                    <div class="flex-grow-1 mt-4 d-flex flex-column justify-content-between" style="min-height:170px;">
                        <div>
                            <h5 class="mb-1 fw-bold" style="font-size:1.35rem;">
                                <?= htmlspecialchars($user['last_name'] . ', ' . $user['first_name']) ?>
                                <small class="text-muted" style="font-size:0.95rem;">(<?= htmlspecialchars($user['username']) ?>)</small>
                            </h5>
                            <p class="mb-1 mt-3" style="font-size:0.95rem;">
                                <?= htmlspecialchars($user['address'] ?? '-') ?>, <?= htmlspecialchars($user['town'] ?? '-') ?>, <?= htmlspecialchars($user['zipcode'] ?? '-') ?>
                            </p>
                            <p class="mb-1" style="font-size:0.95rem;">
                                <?= htmlspecialchars($user['email']) ?> | <?= htmlspecialchars($user['phone'] ?? '-') ?>
                            </p>
                            <div class="d-flex justify-content-between align-items-center mt-3"> 
                                <a href="../user/profile.php" class="btn btn-outline-primary btn-sm">Edit Info</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Shipping Card -->
                <div class="card shadow-sm p-4 mb-4">
                    <h4>Shipping Method</h4>
                    <hr>
                    <?php
                        $shipping_options = [
                            'Standard'=>'Standard Shipping (₱80) - Delivery 3-5 days',
                            'Express'=>'Express Shipping (₱150) - Delivery 1-2 days',
                            'Overnight'=>'Overnight Shipping (₱250) - Delivery next day'
                        ];
                    ?>
                    <?php foreach($shipping_options as $key => $desc): ?>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="shipping_method" value="<?= $key ?>" <?= $shipping_method == $key ? 'checked' : '' ?>>
                            <label class="form-check-label"><?= $desc ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Payment Card -->
                <div class="card shadow-sm p-4 mb-4">
                    <h4>Payment Method</h4>
                    <hr>
                    <?php
                        $payment_options = [
                            'COD'=>'Cash on Delivery',
                            'GCash'=>'GCash',
                            'PayMaya'=>'PayMaya'
                        ];
                    ?>
                    <?php foreach($payment_options as $key => $desc): ?>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="payment_method" value="<?= $key ?>" <?= $payment_method == $key ? 'checked' : '' ?>>
                            <label class="form-check-label"><?= $desc ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- RIGHT SIDE -->
            <div class="col-lg-5 mt-3">
                <!-- Cart -->
                <div class="card shadow-sm p-4 mb-4" style="max-height:400px; overflow-y:auto;">
                    <h4 class="mb-3">Cart Items</h4>
                    <?php foreach($cart_items as $item): ?>
                        <div class="d-flex mb-3 align-items-center">
                            <img src="../assets/images/books/<?= htmlspecialchars($item['image'] ?? 'default_book.png') ?>" style="width:60px;height:80px;object-fit:cover;" class="me-3">
                            <div class="flex-grow-1">
                                <strong><?= htmlspecialchars($item['title']) ?></strong><br>
                                Quantity: <?= $item['quantity'] ?><br>
                                Price: ₱<?= number_format($item['price'], 2) ?><br>
                                Line Total: ₱<?= number_format($item['line_total'], 2) ?>
                            </div>
                        </div>
                        <hr>
                    <?php endforeach; ?>
                </div>

                <!-- Voucher & Order Summary -->
                <div class="card shadow-sm p-4 mb-4">
                    <h4>Order Summary</h4>
                    <div class="mb-3">
                        <label>Voucher Code</label>
                        <input type="text" name="voucher_code" class="form-control" value="<?= htmlspecialchars($voucher_code) ?>">
                    </div>
                    <p>Subtotal: ₱<?= number_format($subtotal, 2) ?></p>
                    <p>Shipping (<?= $shipping_method ?>): ₱<?= number_format($shipping_fee, 2) ?></p>
                    <p>Discount: ₱<?= number_format($discount_amount, 2) ?></p>
                    <hr>
                    <h5>Total: ₱<?= number_format($total, 2) ?></h5>

                    <!-- Buttons -->
                    <div class="d-flex gap-2">
                        <button type="submit" name="update_totals" class="btn btn-primary">Update</button>
                        <input type="hidden" name="place_order" value="1">
                        <button type="submit" name="place_order" class="btn btn-success">
                            Place Order
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <?php else: ?>
        <div class="alert alert-info text-center">Your cart is empty. <a href="../shop/index.php">Go shopping</a>.</div>
    <?php endif; ?>
</div>

<?php include('../includes/footer.php'); ?>
