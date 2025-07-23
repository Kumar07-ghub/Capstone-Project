<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require 'vendor/autoload.php';
include 'includes/db.php';
include 'functions.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user']['id'] ?? 0;

// If order_id set (cash or interac), just show success message
if (isset($_SESSION['order_id'])) {
    $order_id = $_SESSION['order_id'];
    unset($_SESSION['order_id'], $_SESSION['cart_backup'], $_SESSION['stripe_checkout_id'], $_SESSION['billing_info'], $_SESSION['cart']);
    $order_success = true;
} else if (isset($_GET['session_id'])) {
    $session_id = $_GET['session_id'];

    // Check session ownership
    if ($session_id !== ($_SESSION['stripe_checkout_id'] ?? '')) {
        die("Invalid session.");
    }

\Stripe\Stripe::setApiKey('sk_test_51RgnJ8IK8JHviDvgBnLPmht2A1d8seVxkFz4nVoOkydrNa1XsclDADHztPImDtt5OvOOgbpIxkjblud8Su9pNYvQ00GsKeDCKm'); // Replace with your Stripe test secret key

    try {
        $checkout_session = \Stripe\Checkout\Session::retrieve($session_id);

        if ($checkout_session->payment_status !== 'paid') {
            die("Payment not completed.");
        }

        // Prevent duplicate insert on refresh
        if (!isset($_SESSION['order_inserted']) || $_SESSION['order_inserted'] !== $session_id) {
            $billing = $_SESSION['billing_info'] ?? null;

            if (!$billing) {
                die("Billing info missing.");
            }
            
            $customer_name = trim($billing['first_name'] . ' ' . $billing['last_name']);
            $email = $billing['email'];
            $phone = $billing['phone'];
            $address = $billing['address'];
            $city = $billing['city'];
            $province = $billing['province'];
            $postal_code = $billing['postal_code'];
            $country = $billing['country'];
            $instructions = $billing['instructions'] ?? '';
            $user_id = $billing['user_id'];
            $payment_method = 'card';

            // Calculate total from cart_backup
            $cart = $_SESSION['cart_backup'] ?? [];
            $total = 0;
            foreach ($cart as $pid => $qty) {
                $stmt = $conn->prepare("SELECT price FROM products WHERE id=?");
                $stmt->bind_param("i", $pid);
                $stmt->execute();
                $price = $stmt->get_result()->fetch_assoc()['price'];
                $total += $price * $qty;
            }
            $tax = round($total * 0.13, 2);
            $grand_total = round($total + $tax, 2);

            // Insert order
            $stmt = $conn->prepare("INSERT INTO orders (user_id, first_name, last_name, email, phone, address, city, province, postal_code, country, instructions, payment_method, total) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");
$stmt->bind_param("isssssssssssd", $user_id, $billing['first_name'], $billing['last_name'], $email, $phone, $address, $city, $province, $postal_code, $country, $instructions, $payment_method, $grand_total);

$stmt->execute();
            $order_id = $stmt->insert_id;

            foreach ($cart as $pid => $qty) {
                $stmt = $conn->prepare("SELECT price FROM products WHERE id=?");
                $stmt->bind_param("i", $pid);
                $stmt->execute();
                $price = $stmt->get_result()->fetch_assoc()['price'];

                $ins = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?,?,?,?)");
                $ins->bind_param("iiid", $order_id, $pid, $qty, $price);
                $ins->execute();
                
            }

            sendInvoice($order_id, $conn);

            $_SESSION['order_inserted'] = $session_id;
            $_SESSION['order_id'] = $order_id;

            // Clean up cart/session data after order placed
            unset($_SESSION['cart'], $_SESSION['cart_backup'], $_SESSION['stripe_checkout_id'], $_SESSION['billing_info']);
        } else {
            $order_id = $_SESSION['order_id'];
        }

        $order_success = true;
    } catch (Exception $e) {
        $error_message = $e->getMessage();
        $order_success = false;
    }
} else {
    // No order_id or session_id
    header("Location: products.php");
    exit;
}

include 'includes/header.php';
?>

<div class="container py-5">
  <?php if ($order_success): ?>
    <h2 class="text-success text-center mb-4">Thank you for your order!</h2>
    <p class="lead text-center">Your order has been placed successfully.</p>
    <p class="text-center">
      Thank you, <?= htmlspecialchars($customer_name ?? ($_SESSION['user']['name'] ?? 'Valued Customer')) ?>!
    </p>
    <div class="text-center mt-4">
      <a href="products.php" class="btn btn-primary">Continue Shopping</a>
      <a href="user_profile.php" class="btn btn-outline-secondary ms-2">View Your Orders</a>
      <?php
$invoice_file = "invoices/invoice_{$order_id}.pdf";
if (file_exists($invoice_file)):
?>
  <a href="<?= $invoice_file ?>" class="btn btn-sm btn-outline-danger mt-2" target="_blank">
    <i class="bi bi-file-earmark-pdf-fill"></i> Download Invoice
  </a>
<?php else: ?>
  <span class="badge bg-warning text-dark mt-2">Invoice not available</span>
<?php endif; ?>
    </div>
    
  <?php else: ?>
    <h2 class="text-danger text-center mb-4">Payment Error</h2>
    <p class="text-center"><?= htmlspecialchars($error_message ?? 'There was a problem processing your payment.') ?></p>
    <div class="text-center mt-4">
      <a href="checkout.php" class="btn btn-primary">Try Again</a>
    </div>
  <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
