<<<<<<< HEAD
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
=======
<?php
session_start();
require 'vendor/autoload.php';
include 'includes/db.php';
include 'includes/header.php';

\Stripe\Stripe::setApiKey('sk_test_51RgnJ8IK8JHviDvgBnLPmht2A1d8seVxkFz4nVoOkydrNa1XsclDADHztPImDtt5OvOOgbpIxkjblud8Su9pNYvQ00GsKeDCKm'); // Replace with your Stripe test secret key

$order_id = $_SESSION['order_id'] ?? null;

if (isset($_GET['session_id'])) {
    $session_id = $_GET['session_id'];
    $session = \Stripe\Checkout\Session::retrieve($session_id);

    if ($session->payment_status === 'paid') {
        // Create order if not already created
        if (!$order_id && isset($_SESSION['cart']) && isset($_SESSION['user'])) {
            $user_id = $_SESSION['user']['id'];
            $email = $session->customer_email;
            $name = $_POST['full_name'] ?? 'Stripe User'; // Default fallback
            $address = $session->customer_details->address->line1 ?? '';
            $city = $session->customer_details->address->city ?? '';
            $province = $session->customer_details->address->state ?? '';
            $postal_code = $session->customer_details->address->postal_code ?? '';
            $country = $session->customer_details->address->country ?? '';
            $phone = $session->customer_details->phone ?? '';

            // Calculate total from session
            $total = 0;
            foreach ($_SESSION['cart'] as $productId => $qty) {
                $stmt = $conn->prepare("SELECT price FROM products WHERE id = ?");
                $stmt->bind_param("i", $productId);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($row = $result->fetch_assoc()) {
                    $total += $row['price'] * $qty;
                }
            }

            $tax = $total * 0.13;
            $grand_total = $total + $tax;

            // Insert order
            $stmt = $conn->prepare("INSERT INTO orders (user_id, name, email, phone, address, city, province, postal_code, country, instructions, payment_method, total)
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, '', 'card', ?)");
            $stmt->bind_param("isssssssssd", $user_id, $name, $email, $phone, $address, $city, $province, $postal_code, $country, $grand_total);
            $stmt->execute();
            $order_id = $stmt->insert_id;

            // Insert order items
            foreach ($_SESSION['cart'] as $productId => $qty) {
                $stmt = $conn->prepare("SELECT price FROM products WHERE id = ?");
                $stmt->bind_param("i", $productId);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($row = $result->fetch_assoc()) {
                    $price = $row['price'];
                    $insertItem = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
                    $insertItem->bind_param("iiid", $order_id, $productId, $qty, $price);
                    $insertItem->execute();
                }
            }

            // Generate invoice and send email (reuse your existing code)
            // You can move that block into a reusable function like generateInvoice($order_id)

            // Clear cart and set session
            unset($_SESSION['cart']);
            $_SESSION['order_id'] = $order_id;
        }
    } else {
        echo "<div class='alert alert-danger text-center'>Payment not successful. Please try again.</div>";
        include 'includes/footer.php';
        exit;
    }
}

// Show confirmation UI
?>

<div class="container text-center py-5">
  <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
  <h2 class="fw-bold mt-3 text-success">Thank you for your order!</h2>
  <p class="lead mt-2">Your order has been placed successfully. We’ll contact you when it ships.</p>

  <?php if (isset($_SESSION['order_id'])): ?>
    <div class="mt-3">
      <h5 class="text-secondary">Order ID: <strong>#<?= htmlspecialchars($_SESSION['order_id']) ?></strong></h5>
      <a href="invoices/invoice_<?= $_SESSION['order_id'] ?>.pdf" target="_blank" class="btn btn-outline-primary mt-3">
        📄 Download Invoice (PDF)
      </a>
    </div>
  <?php endif; ?>

  <div class="mt-5">
    <a href="index.php" class="btn btn-outline-success rounded-pill px-4 me-2">
      <i class="bi bi-house-door-fill me-1"></i> Home
    </a>
    <a href="products.php" class="btn btn-success rounded-pill px-4">
      <i class="bi bi-cart-plus-fill me-1"></i> Continue Shopping
    </a>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
>>>>>>> 07d8e6ccfd579de17e14e08d58f8a836c66fbf26
