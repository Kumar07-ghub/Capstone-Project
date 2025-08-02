<?php
session_start();
require 'vendor/autoload.php';
include 'includes/db.php';
include 'functions.php';

if (!isset($_SESSION['user'])) {
    $_SESSION['redirect_after_login'] = 'checkout.php';
    header("Location: login.php?message=login_required");
    exit;
}

if (!isset($_SESSION['cart']) || count($_SESSION['cart']) === 0) {
    header("Location: products.php");
    exit;
}

// Include Stripe
\Stripe\Stripe::setApiKey('sk_test_51RgnJ8IK8JHviDvgBnLPmht2A1d8seVxkFz4nVoOkydrNa1XsclDADHztPImDtt5OvOOgbpIxkjblud8Su9pNYvQ00GsKeDCKm'); // Replace with your Stripe test secret key

$errors = $field_errors = [];

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF check
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token");
    }

    $required_fields = ['first_name', 'last_name', 'email', 'phone', 'address', 'city', 'province', 'postal_code', 'country', 'payment_method'];
    foreach ($required_fields as $f) {
        if (empty(trim($_POST[$f] ?? ''))) {
            $field_errors[$f] = ucfirst(str_replace('_',' ', $f)) . " is required.";
        }
    }

    // Terms acceptance check
    if (empty($_POST['termsCheck'])) {
        $field_errors['termsCheck'] = "You must accept the terms and conditions.";
    }

    if (!empty($_POST['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $field_errors['email'] = "Invalid email.";
    }

    if (!empty($_POST['phone'])) {
        $phone_clean = preg_replace("/\D/", "", $_POST['phone']);
        if (!preg_match("/^\d{10,15}$/", $phone_clean)) {
            $field_errors['phone'] = "Invalid phone.";
        }
    }

    if (empty($field_errors)) {
        // Sanitize inputs
        $first_name = htmlspecialchars(trim($_POST['first_name']));
        $last_name = htmlspecialchars(trim($_POST['last_name']));
        $email = htmlspecialchars(trim($_POST['email']));
        $phone = $phone_clean;
        $address = htmlspecialchars(trim($_POST['address']));
        $city = htmlspecialchars(trim($_POST['city']));
        $province = htmlspecialchars(trim($_POST['province']));
        $postal_code = htmlspecialchars(trim($_POST['postal_code']));
        $country = htmlspecialchars(trim($_POST['country']));
        $instructions = htmlspecialchars(trim($_POST['instructions'] ?? ''));
        $payment_method = $_POST['payment_method'];

        $user_id = $_SESSION['user']['id'];
        $total = 0;
        $line_items = [];

        foreach ($_SESSION['cart'] as $pid => $qty) {
            $stmt = $conn->prepare("SELECT name, price FROM products WHERE id=?");
            $stmt->bind_param("i", $pid);
            $stmt->execute();
            $row = $stmt->get_result()->fetch_assoc();
            $subtotal = $row['price'] * $qty;
            $total += $subtotal;
            $line_items[] = [
                'price_data' => [
                    'currency' => 'cad',
                    'product_data' => ['name' => $row['name']],
                    'unit_amount' => intval($row['price'] * 100),
                ],
                'quantity' => $qty
            ];
        }

        $tax = round($total * 0.13, 2);
        $grand_total = round($total + $tax, 2);

        if ($payment_method === 'card') {
            // Add tax as line item for Stripe
            $line_items[] = [
                'price_data' => [
                    'currency' => 'cad',
                    'product_data' => ['name' => 'Tax (13%)'],
                    'unit_amount' => intval($tax * 100),
                ],
                'quantity' => 1
            ];

            // Save billing info + instructions + user ID in session for order_success.php
            $_SESSION['billing_info'] = compact(
                'first_name', 'last_name', 'email', 'phone', 'address', 'city', 'province', 'postal_code', 'country', 'instructions', 'user_id'
            );
            $_SESSION['cart_backup'] = $_SESSION['cart'];

            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => $line_items,
                'mode' => 'payment',
                'customer_email' => $email,
                'success_url' => 'https://' . $_SERVER['HTTP_HOST'] . '/order-success.php?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => 'https://' . $_SERVER['HTTP_HOST'] . '/checkout.php?payment=cancelled',
            ]);

            $_SESSION['stripe_checkout_id'] = $session->id;

            header("Location: " . $session->url);
            exit;
        }

        // For cash insert order immediately
         $stmt = $conn->prepare("INSERT INTO orders (user_id, first_name, last_name, email, phone, address, city, province, postal_code, country, instructions, payment_method, total) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param("isssssssssssd", $user_id, $first_name, $last_name, $email, $phone, $address, $city, $province, $postal_code, $country, $instructions, $payment_method, $grand_total);
        $stmt->execute();
        $order_id = $stmt->insert_id;

        foreach ($_SESSION['cart'] as $pid => $qty) {
            $stmt = $conn->prepare("SELECT price FROM products WHERE id=?");
            $stmt->bind_param("i", $pid);
            $stmt->execute();
            $price = $stmt->get_result()->fetch_assoc()['price'];

            $ins = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?,?,?,?)");
            $ins->bind_param("iiid", $order_id, $pid, $qty, $price);
            $ins->execute();
        }

        sendInvoice($order_id, $conn);
        unset($_SESSION['cart']);
        $_SESSION['order_id'] = $order_id;

        header("Location: order-success.php");
        exit;
    }
}

include 'includes/header.php';
?>

<div class="container py-5">
  <h2 class="mb-4 fw-bold text-success text-center">Checkout</h2>

  <div class="row">
    <div class="col-md-8">
      <form method="POST" novalidate>
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <div class="card shadow border-0 rounded-4 mb-4">
          <div class="card-body p-4">
            <h4 class="mb-3 fw-semibold">Billing & Shipping Details</h4>
            <div class="row g-3">
              <?php
              function showField($name, $label, $type = 'text', $placeholder = '', $value = '') {
                  global $field_errors;
                  echo "<div class='col-md-6'>
                          <label class='form-label'>$label</label>
                          <input type='$type' name='$name' class='form-control' value='$value' placeholder='$placeholder' autocomplete='on'>
                          " . (isset($field_errors[$name]) ? "<div class='text-danger small'>{$field_errors[$name]}</div>" : "") . "
                        </div>";
              }

              // Retrieve the user's name and email from the session if they are logged in
              $user_name = $_SESSION['user']['name'] ?? '';  // Default to empty if not available
              $user_email = $_SESSION['user']['email'] ?? ''; // Default to empty if not available
              
              // Example in form:
              
              showField('first_name', 'First Name <span class="text-danger">*</span>', 'text', 'Enter your first name', $_POST['first_name'] ?? ($_SESSION
              ['user']['first_name'] ?? '') );
              showField('last_name', 'Last Name <span class="text-danger">*</span>', 'text', 'Enter your last name', $_POST['last_name'] ?? ($_SESSION                   ['user']['last_name'] ?? '') );
              showField('email', 'Email <span class="text-danger">*</span>', 'email', 'youremail@example.com', $user_email);
              showField('phone', 'Phone Number <span class="text-danger">*</span>', 'tel', 'Enter your phone number');
              showField('country', 'Country <span class="text-danger">*</span>', 'text', 'Enter your country');
              showField('province', 'Province <span class="text-danger">*</span>', 'text', 'Enter your province');
              showField('city', 'City <span class="text-danger">*</span>', 'text', 'Enter your city');
              showField('postal_code', 'Postal Code <span class="text-danger">*</span>', 'text', 'Enter your postal code');
              ?>

              <div class="col-md-12">
                <label class="form-label">Address <span class="text-danger">*</span></label>
                <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($_POST['address'] ?? '') ?>" placeholder="Street address, apartment, suite, etc." autocomplete="address-line1">
                <?php if (isset($field_errors['address'])): ?>
                  <div class="text-danger small"><?= $field_errors['address'] ?></div>
                <?php endif; ?>
              </div>

              <div class="col-md-12">
                <label class="form-label">Delivery Instructions (Optional)</label>
                <textarea name="instructions" class="form-control" rows="2" placeholder="Enter any specific delivery instructions"><?= htmlspecialchars($_POST['instructions'] ?? '') ?></textarea>
              </div>

              <div class="col-md-12">
                <label class="form-label">Payment Method<span class="text-danger">*</span></label>
                <select name="payment_method" class="form-select">
                  <option value="">Choose...</option>
                  <option value="cash" <?= ($_POST['payment_method'] ?? '') === 'cash' ? 'selected' : '' ?>>Cash on Delivery</option>
                  <option value="card" <?= ($_POST['payment_method'] ?? '') === 'card' ? 'selected' : '' ?>>Credit/Debit Card</option>
                </select>
                <?php if (isset($field_errors['payment_method'])): ?>
                  <div class="text-danger small"><?= $field_errors['payment_method'] ?></div>
                <?php endif; ?>
              </div>

              <div class="col-12 form-check mt-3">
                <input class="form-check-input" type="checkbox" id="termsCheck" name="termsCheck" required <?= isset($_POST['termsCheck']) ? 'checked' : '' ?>>
                <label class="form-check-label" for="termsCheck">
                  I agree to the <a href="#">terms and conditions</a>. <span class="text-danger">*</span>
                </label>
                <?php if (isset($field_errors['termsCheck'])): ?>
                  <div class="text-danger small"><?= $field_errors['termsCheck'] ?></div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
        <button type="submit" class="btn btn-dark btn-lg px-5 w-100">Place Order</button>
      </form>
    </div>

    <!-- Order Summary -->
    <div class="col-md-4">
      <div class="card shadow border-0 rounded-4">
        <div class="card-body p-4">
          <h4 class="mb-3 fw-semibold">Order Summary</h4>
          <ul class="list-group list-group-flush mb-3">
            <?php
            $total = 0;
            foreach ($_SESSION['cart'] as $productId => $qty) {
                $stmt = $conn->prepare("SELECT name, price FROM products WHERE id = ?");
                $stmt->bind_param("i", $productId);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($row = $result->fetch_assoc()) {
                    $subtotal = $row['price'] * $qty;
                    $total += $subtotal;
                    echo "<li class='list-group-item d-flex justify-content-between align-items-center'>
                            {$row['name']} x $qty
                            <span>$" . number_format($subtotal, 2) . "</span>
                          </li>";
                }
            }

            $tax = $total * 0.13;
            $grand_total = $total + $tax;
            ?>
            <li class="list-group-item d-flex justify-content-between">
              Subtotal
              <span>$<?= number_format($total, 2) ?></span>
            </li>
            <li class="list-group-item d-flex justify-content-between">
              Tax (13% HST)
              <span>$<?= number_format($tax, 2) ?></span>
            </li>
            <li class="list-group-item d-flex justify-content-between fw-bold">
              Total
              <span>$<?= number_format($grand_total, 2) ?></span>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>