<?php
session_start();
include('../config/database.php');
include('../includes/mail.php');

if (!isset($_SESSION['user']['id'])) {
    $_SESSION['message'] = "Please login to continue to checkout.";
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];

$stmt = $conn->prepare("
    SELECT * FROM view_user_profile
    WHERE user_id = ?
    LIMIT 1
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

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
    $row['quantity'] = intval($row['quantity']);
    $row['price'] = floatval($row['price']);
    $row['line_total'] = $row['quantity'] * $row['price'];
    $subtotal += $row['line_total'];
    $cart_items[] = $row;
}
$stmt->close();

$shipping_method = 'Standard';
$payment_method  = 'COD';
$voucher_code    = '';
$shipping_fee    = 80;
$discount_amount = 0;
$shipping_address = $user['address'] ?? '';
$total = $subtotal + $shipping_fee;

function calculateTotals($subtotal, $shipping_method, $voucher_code, $conn, &$shipping_fee, &$discount_amount, &$total) {
    $shipping_fee = ($shipping_method === 'Express') ? 150 : (($shipping_method === 'Overnight') ? 250 : 80);

    $discount_amount = 0;

    if (!empty($voucher_code)) {
        $voucher_code = strtoupper(trim($voucher_code));

        $stmt = $conn->prepare("SELECT * FROM coupons WHERE code=? AND (expires_at IS NULL OR expires_at > NOW()) LIMIT 1");
        $stmt->bind_param("s", $voucher_code);
        $stmt->execute();
        $voucher = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($voucher) {
            if ($voucher['type'] === 'fixed') {
                $discount_amount = floatval($voucher['amount']);
            } else {
                $discount_amount = $subtotal * (floatval($voucher['amount']) / 100);
            }
        } else {
            $discount_amount = 0;
            $_SESSION['message'] = "Invalid voucher code.";
        }
    }

    $total = $subtotal + $shipping_fee - $discount_amount;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $shipping_method  = $_POST['shipping_method'] ?? 'Standard';
    $payment_method   = $_POST['payment_method'] ?? 'COD';
    $voucher_code     = trim($_POST['voucher_code'] ?? '');
    $shipping_address = trim($_POST['shipping_address'] ?? $user['address']);

    if (isset($_POST['update_totals'])) {
        calculateTotals(
            $subtotal,
            $shipping_method,
            $voucher_code,
            $conn,
            $shipping_fee,
            $discount_amount,
            $total
        );
    }

    if (isset($_POST['place_order'])) {

        calculateTotals(
            $subtotal,
            $shipping_method,
            $voucher_code,
            $conn,
            $shipping_fee,
            $discount_amount,
            $total
        );

        if (empty($cart_items)) {
            $_SESSION['message'] = "Your cart is empty.";
            header("Location: ../shop/index.php");
            exit;
        }

        $conn->begin_transaction();

        try {
            $stmt = $conn->prepare("
                INSERT INTO orders 
                (user_id, shipping_method, payment_method, voucher_code,
                 shipping_fee, subtotal, total, shipping_address, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");

            $stmt->bind_param(
                "isssddds",
                $user_id,
                $shipping_method,
                $payment_method,
                $voucher_code,
                $shipping_fee,
                $subtotal,
                $total,
                $shipping_address
            );

            $stmt->execute();
            $order_id = $stmt->insert_id;
            $stmt->close();

            $item_stmt = $conn->prepare("
                INSERT INTO order_items (order_id, book_id, quantity, price)
                VALUES (?, ?, ?, ?)
            ");

            $stock_stmt = $conn->prepare("
                UPDATE books 
                SET stock = stock - ? 
                WHERE id = ? AND stock >= ?
            ");

            foreach ($cart_items as $item) {

                $item_stmt->bind_param(
                    "iiid",
                    $order_id,
                    $item['book_id'],
                    $item['quantity'],
                    $item['price']
                );
                $item_stmt->execute();

                $stock_stmt->bind_param(
                    "iii",
                    $item['quantity'],
                    $item['book_id'],
                    $item['quantity']
                );
                $stock_stmt->execute();

                if ($stock_stmt->affected_rows === 0) {
                    throw new Exception("Insufficient stock for: " . $item['title']);
                }
            }

            $item_stmt->close();
            $stock_stmt->close();

            $clear = $conn->prepare("DELETE FROM cart_items WHERE user_id=?");
            $clear->bind_param("i", $user_id);
            $clear->execute();
            $clear->close();

            $conn->commit();

            $customerEmail = $user['email'];
            $customerName  = $user['first_name'] . ' ' . $user['last_name'];

            $orderHtml = "
                <h2 style='font-family: Arial; color:#333;'>Thank you for your order!</h2>
                <p style='font-family: Arial; font-size: 14px;'>Your order has been successfully placed.</p>

                <h3 style='font-family: Arial; color:#333;'>Order #$order_id</h3>

                <table border='1' cellpadding='6' cellspacing='0' width='100%' 
                       style='border-collapse: collapse; font-family: Arial; font-size: 14px;'>
                    <tr style='background: #f2f2f2;'>
                        <th align='left'>Title</th>
                        <th align='center'>Qty</th>
                        <th align='right'>Price</th>
                        <th align='right'>Total</th>
                    </tr>
            ";

            foreach ($cart_items as $item) {
                $orderHtml .= "
                    <tr>
                        <td>{$item['title']}</td>
                        <td align='center'>{$item['quantity']}</td>
                        <td align='right'>₱" . number_format($item['price'], 2) . "</td>
                        <td align='right'>₱" . number_format($item['line_total'], 2) . "</td>
                    </tr>
                ";
            }

            $orderHtml .= "
                </table>

                <p style='font-family: Arial; font-size: 14px; margin-top: 10px;'>
                    <strong>Subtotal:</strong> ₱" . number_format($subtotal, 2) . "<br>
                    <strong>Shipping Fee:</strong> ₱" . number_format($shipping_fee, 2) . "<br>
                    " . ($discount_amount > 0 ? "<strong>Discount:</strong> -₱" . number_format($discount_amount, 2) . "<br>" : "") . "
                    <strong>Total:</strong> ₱" . number_format($total, 2) . "
                </p>

                <p style='font-family: Arial; font-size: 14px;'>
                    <strong>Shipping Method:</strong> $shipping_method<br>
                    <strong>Payment Method:</strong> $payment_method<br>
                    <strong>Delivery Address:</strong> $shipping_address
                </p>
            ";

            smtp_send_mail(
                $customerEmail,
                "Order Confirmation #$order_id",
                $orderHtml
            );

            $_SESSION['message'] = "Order placed successfully!";
            header("Location: ../cart/receipt.php?order_id=$order_id");
            exit;

        } catch (Exception $e) {

            $conn->rollback();

            $_SESSION['message'] = "Order failed: " . $e->getMessage();
            header("Location: cart_view.php");
            exit;
        }
    }
}

include('../includes/header.php');
?>

<div class="container my-5">
    <h1 class="text-center mb-4">Checkout</h1>

    <?php if (!empty($cart_items)): ?>
    <form action="" method="POST">

        <div class="row">

            <div class="col-lg-7 mt-3">

                <div class="card shadow-sm p-4 mb-4">
                    <div class="d-flex">
                        <img src="../assets/images/users/<?= htmlspecialchars($user['profile_picture'] ?: 'default.png') ?>"
                             class="rounded-circle border me-4" width="130" height="130">

                        <div>
                            <h4><?= htmlspecialchars($user['first_name'] . " " . $user['last_name']) ?></h4>
                            <p><?= htmlspecialchars($user['email']) ?></p>
                            <a href="../user/profile.php" class="btn btn-outline-primary btn-sm">Edit Profile</a>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm p-4 mb-4">
                    <h4>Shipping Address</h4>
                    <textarea name="shipping_address" class="form-control" rows="3" required><?= htmlspecialchars($shipping_address) ?></textarea>
                </div>

                <div class="card shadow-sm p-4 mb-4">
                    <h4>Shipping Method</h4>
                    <?php
                    $shipping_options = [
                        'Standard'=>'Standard Shipping (₱80)',
                        'Express'=>'Express Shipping (₱150)',
                        'Overnight'=>'Overnight Shipping (₱250)'
                    ];
                    ?>
                    <?php foreach($shipping_options as $key=>$desc): ?>
                        <label class="d-block">
                            <input type="radio" name="shipping_method" value="<?= $key ?>" <?= $shipping_method==$key?'checked':'' ?>>
                            <?= $desc ?>
                        </label>
                    <?php endforeach; ?>
                </div>

                <div class="card shadow-sm p-4 mb-4">
                    <h4>Payment Method</h4>
                    <?php
                    $pay_opts = ['COD'=>'Cash on Delivery','GCash'=>'GCash','PayMaya'=>'PayMaya'];
                    ?>
                    <?php foreach($pay_opts as $key=>$desc): ?>
                        <label class="d-block">
                            <input type="radio" name="payment_method" value="<?= $key ?>" <?= $payment_method==$key?'checked':'' ?>>
                            <?= $desc ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="col-lg-5 mt-3">

                <div class="card shadow-sm p-4 mb-4" style="max-height:400px; overflow-y:auto;">
                    <h4>Cart Summary</h4>
                    <?php foreach($cart_items as $item): ?>
                        <div class="d-flex mb-3">
                            <img src="../assets/images/books/<?= htmlspecialchars($item['image'] ?? 'default_book.png') ?>"
                                 width="60" height="80" class="me-3">
                            <div>
                                <strong><?= htmlspecialchars($item['title']) ?></strong><br>
                                Qty: <?= $item['quantity'] ?><br>
                                Price: ₱<?= number_format($item['price'], 2) ?><br>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="card shadow-sm p-4 mb-4">
                    <h4>Order Summary</h4>

                    <label>Voucher Code</label>
                    <input type="text" name="voucher_code" class="form-control mb-3"
                           value="<?= htmlspecialchars($voucher_code) ?>">

                    <p>Subtotal: ₱<?= number_format($subtotal, 2) ?></p>
                    <p>Shipping: ₱<?= number_format($shipping_fee, 2) ?></p>
                    <p>Discount: ₱<?= number_format($discount_amount, 2) ?></p>
                    <hr>
                    <h5>Total: ₱<?= number_format($total, 2) ?></h5>

                    <div class="d-flex gap-2 mt-3">
                        <button type="submit" name="update_totals" value="1" class="btn btn-primary">Update</button>
                        <button type="submit" name="place_order" value="1" class="btn btn-success">Place Order</button>
                    </div>
                </div>

            </div>
        </div>

    </form>

    <?php else: ?>
        <div class="alert alert-info text-center">
            Your cart is empty. <a href="../shop/index.php">Shop now</a>
        </div>
    <?php endif; ?>

</div>

<?php include('../includes/footer.php'); ?>
