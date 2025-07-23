<?php
session_start();
include 'includes/header.php';
include 'includes/db.php';

if (!isset($_SESSION['cart']) || count($_SESSION['cart']) === 0) {
    $_SESSION['cart'] = [];
}
// Handle quantity update or remove product
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['product_id'], $_POST['quantity'])) {
        $product_id = $_POST['product_id'];
        $quantity = max(1, intval($_POST['quantity']));
        $_SESSION['cart'][$product_id] = $quantity;
        echo "<script>setTimeout(() => { window.location.href = 'cart.php'; }, 0);</script>";
        exit;
    } elseif (isset($_POST['remove_product'])) {
        $remove_id = $_POST['remove_product'];
        unset($_SESSION['cart'][$remove_id]);
        echo "<script>setTimeout(() => { window.location.href = 'cart.php'; }, 0);</script>";
        exit;
    }
}

$cart_items = $_SESSION['cart'];
$total = 0;
?>

<div class="container mt-5">
    <h2 class="mb-4 text-center fw-bold text-success">🛒 Your Shopping Cart</h2>
</div>


<div class="container mb-5">
    <div class="table-responsive">
        <table class="table table-bordered table-hover text-center align-middle shadow-sm">
            <thead class="table-success">
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th style="width: 160px;">Quantity</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (count($cart_items) === 0) {
                    echo '<tr><td colspan="5" class="text-muted py-4">Your cart is empty.</td></tr>';
                } else {
                    foreach ($cart_items as $id => $qty) {
                        $result = $conn->query("SELECT * FROM products WHERE id = $id");
                        if ($result->num_rows > 0) {
                            $product = $result->fetch_assoc();
                            $subtotal = $product['price'] * $qty;
                            $total += $subtotal;
                            echo '<tr>
                                <td>' . $product['name'] . '</td>
                                <td>$' . number_format($product['price'], 2) . '</td>
                                <td>
                                    <form method="POST" class="d-flex justify-content-center align-items-center gap-2">
                                        <input type="hidden" name="product_id" value="' . $id . '">
                                        <input 
                                            type="number" 
                                            name="quantity" 
                                            value="' . $qty . '" 
                                            class="form-control text-center quantity-input" 
                                            min="1"
                                            onchange="this.form.submit()"
                                        >
                                    </form>
                                </td>
                                <td>$' . number_format($subtotal, 2) . '</td>
                                <td>
                                    <form method="POST">
                                        <input type="hidden" name="remove_product" value="' . $id . '">
                                        <button type="submit" class="btn btn-sm btn-danger">Remove</button>
                                    </form>
                                </td>
                            </tr>';
                        }
                    }

                    // Tax and Grand Total
                    $tax = $total * 0.13;
                    $grand_total = $total + $tax;

                    echo '
                    <tr class="table-light fw-semibold">
                        <td colspan="4" class="text-end">Subtotal</td>
                        <td>$' . number_format($total, 2) . '</td>
                    </tr>
                    <tr class="table-light fw-semibold">
                        <td colspan="4" class="text-end">HST</td>
                        <td>$' . number_format($tax, 2) . '</td>
                    </tr>
                    <tr class="table-success fw-bold">
                        <td colspan="4" class="text-end">Grand Total</td>
                        <td>$' . number_format($grand_total, 2) . '</td>
                    </tr>';
                }
                ?>
            </tbody>
        </table>
    </div>

    <?php if (count($cart_items) > 0): ?>
        <div class="text-end mt-4">
            <a href="checkout.php" class="btn checkout-btn">
                <i class="bi bi-bag-check-fill me-2"></i> Checkout Securely
            </a>
        </div>
    <?php endif; ?>
</div>

<!-- Unique Checkout Button Styles -->
<style>
.checkout-btn {
    display: inline-block;
    padding: 12px 28px;
    font-size: 1.1rem;
    font-weight: 600;
    color: #fff;
    border: none;
    border-radius: 50px;
    background: linear-gradient(to right, #28a745, #218838);
    box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
    text-decoration: none;
    transition: all 0.3s ease;
}
.checkout-btn:hover {
    background: linear-gradient(to right, #218838, #1e7e34);
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(33, 136, 56, 0.4);
}
</style>

<?php include 'includes/footer.php'; ?>