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
