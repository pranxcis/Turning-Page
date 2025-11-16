    <?php
    session_start();

    include('../includes/header.php');
    include('../config/database.php');

    echo "<pre>";
    echo "</pre>";

    if (isset($_SESSION["cart_products"]) && count($_SESSION["cart_products"]) > 0) {
        echo '<div class="container my-4">';
        echo '<h3>Your Shopping Cart</h3>';
        echo '<form method="POST" action="../cart/cart_update.php" class="table-responsive">';
        echo '<table class="table table-bordered align-middle">';
        echo '<tbody>';

        $total = 0;

foreach ($_SESSION["cart_products"] as $cart_itm) {
    $product_name  = $cart_itm["book_title"];
    $product_qty   = $cart_itm["quantity"];
    $product_price = $cart_itm["price"];
    $product_code  = $cart_itm["book_id"];

    echo "<tr style='cursor:pointer;' onclick=\"window.location='../shop/book_info.php?book_id={$product_code}';\">";
    echo "<td width='120'>Qty 
            <input type='number' min='1' name='product_qty[$product_code]' 
                value='{$product_qty}' class='form-control' />
          </td>";
    echo "<td><strong>{$product_name}</strong></td>";
    echo '<td width="150"><input type="checkbox" name="remove_code[]" value="'.$product_code.'" /> Remove</td>';
    echo "</tr>";

    $subtotal = $product_qty * $product_price;
    $total += $subtotal;
}


        echo "<tr><td colspan='3' class='text-end'>";
        echo '<button type="submit" class="btn btn-primary btn-sm">Update</button> ';
        echo '<a href="../cart/view_cart.php" class="btn btn-success btn-sm">Checkout</a>';
        echo "</td></tr>";

        echo '</tbody>';
        echo '</table>';
        echo "</form>";
        echo '</div>';
    }


    $sql = "
        SELECT 
            b.id AS bookId,
            b.title,
            b.price,
            b.stock,
            COALESCE(bi.image_path, 'default.jpg') AS image_path
        FROM books b
        LEFT JOIN book_images bi ON bi.book_id = b.id
        GROUP BY b.id
        ORDER BY b.id ASC
    ";

    $results = mysqli_query($conn, $sql);

    if ($results) {

        echo '<div class="container my-4">';
        echo '<ul class="row list-unstyled">';

        while ($row = mysqli_fetch_assoc($results)) {

$products_item = <<<EOT
<li class="col-md-3 mb-4">
    <div class="card h-100 shadow-sm" style="cursor:pointer;" onclick="window.location='../shop/book_info.php?book_id={$row['bookId']}';">
        <img src="../assets/images/books/{$row['image_path']}" class="card-img-top" style="height:400px; object-fit:cover; margin:auto;">
        <div class="card-body">
            <h5 class="card-title">{$row['title']}</h5>
            <p class="card-text">₱{$row['price']}</p>

            <form method="POST" action="../cart/cart_update.php">
                <label class="form-label">Quantity</label>
                <input type="number" name="item_qty" class="form-control mb-2"
                    value="1" min="1" max="{$row['stock']}">

                <input type="hidden" name="item_id" value="{$row['bookId']}">
                <input type="hidden" name="type" value="add">

                <button type="submit" class="btn btn-primary w-100">Add to Cart</button>
            </form>
        </div>
    </div>
</li>
EOT;


            echo $products_item;
        }

        echo '</ul>';
        echo '</div>';
    }

    include('../includes/footer.php');
    ?>
