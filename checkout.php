<?php
session_start();

if (!isset($_SESSION['user'])) {
    $_SESSION['redirect_after_login'] = 'checkout.php';
    header("Location: login.php?message=login_required");
    exit;
}

include 'includes/db.php';
include 'includes/header.php';

$errors = [];
$field_errors = [];

if (!isset($_SESSION['cart']) || count($_SESSION['cart']) === 0) {
    echo '<div class="container mt-5 text-center"><h4>Your cart is empty.</h4><a href="products.php" class="btn btn-success mt-3">Go Shopping</a></div>';
    include 'includes/footer.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $required_fields = ['full_name', 'email', 'phone', 'address', 'city', 'province', 'postal_code', 'country', 'payment_method'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $field_errors[$field] = ucfirst(str_replace('_', ' ', $field)) . " is required.";
        }
    }

    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $field_errors['email'] = "Invalid email address.";
    }

    if (!preg_match("/^\d{10,15}$/", preg_replace("/\D/", "", $_POST['phone']))) {
        $field_errors['phone'] = "Invalid phone number.";
    }

    if (empty($field_errors)) {
        $name = htmlspecialchars(trim($_POST['full_name']));
        $email = htmlspecialchars(trim($_POST['email']));
        $phone = htmlspecialchars(trim($_POST['phone']));
        $address = htmlspecialchars(trim($_POST['address']));
        $city = htmlspecialchars(trim($_POST['city']));
        $province = htmlspecialchars(trim($_POST['province']));
        $postal_code = htmlspecialchars(trim($_POST['postal_code']));
        $country = htmlspecialchars(trim($_POST['country']));
        $instructions = htmlspecialchars(trim($_POST['instructions']));
        $payment_method = htmlspecialchars(trim($_POST['payment_method']));
        $user_id = $_SESSION['user']['id'] ?? null;

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

        $stmt = $conn->prepare("INSERT INTO orders (user_id, name, email, phone, address, city, province, postal_code, country, instructions, payment_method, total)
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssssssssd", $user_id, $name, $email, $phone, $address, $city, $province, $postal_code, $country, $instructions, $payment_method, $grand_total);
        $stmt->execute();
        $order_id = $stmt->insert_id;

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

        $to = $email;
        $subject = "Order Confirmation - The Indian Supermarket";
        $headers = "From: no-reply@theindiansupermarket.com\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $message = "Hi $name,\n\nThank you for your order!\n\nOrder ID: #$order_id\nSubtotal: $" . number_format($total, 2) . "\nTax (13%): $" . number_format($tax, 2) . "\nTotal: $" . number_format($grand_total, 2) . "\n\nWe’ll process your order shortly.\n\nRegards,\nThe Indian Supermarket";

        mail($to, $subject, $message, $headers);

        $_SESSION['order_id'] = $order_id;
        $_SESSION['cart'] = [];
        header("Location: order-success.php");
        exit;
    }
}
?>

<div class="container py-5">
  <h2 class="mb-4 fw-bold text-success text-center">Checkout</h2>

  <div class="row">
    <div class="col-md-8">
      <form method="POST" novalidate>
        <div class="card shadow border-0 rounded-4 mb-4">
          <div class="card-body p-4">
            <h4 class="mb-3 fw-semibold">Billing & Shipping Details</h4>
            <div class="row g-3">
              <?php
              function showField($name, $label, $type = 'text') {
                  global $field_errors;
                  $value = htmlspecialchars($_POST[$name] ?? '');
                  echo "<div class='col-md-6'>
                          <label class='form-label'>$label</label>
                          <input type='$type' name='$name' class='form-control' value='$value'>
                          " . (isset($field_errors[$name]) ? "<div class='text-danger small'>{$field_errors[$name]}</div>" : "") . "
                        </div>";
              }

              showField('full_name', 'Full Name');
              showField('email', 'Email', 'email');
              showField('phone', 'Phone Number', 'tel');
              showField('country', 'Country');
              showField('province', 'Province');
              showField('city', 'City');
              showField('postal_code', 'Postal Code');
              ?>

              <div class="col-md-12">
                <label class="form-label">Address</label>
                <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($_POST['address'] ?? '') ?>">
                <?php if (isset($field_errors['address'])): ?>
                  <div class="text-danger small"><?= $field_errors['address'] ?></div>
                <?php endif; ?>
              </div>

              <div class="col-md-12">
                <label class="form-label">Delivery Instructions (Optional)</label>
                <textarea name="instructions" class="form-control" rows="2"><?= htmlspecialchars($_POST['instructions'] ?? '') ?></textarea>
              </div>

              <div class="col-md-12">
                <label class="form-label">Payment Method</label>
                <select name="payment_method" class="form-select">
                  <option value="">Choose...</option>
                  <option value="cash" <?= ($_POST['payment_method'] ?? '') === 'cash' ? 'selected' : '' ?>>Cash on Delivery</option>
                  <option value="card" <?= ($_POST['payment_method'] ?? '') === 'card' ? 'selected' : '' ?>>Credit/Debit Card</option>
                  <option value="interac" <?= ($_POST['payment_method'] ?? '') === 'interac' ? 'selected' : '' ?>>Interac E-transfer</option>
                </select>
                <?php if (isset($field_errors['payment_method'])): ?>
                  <div class="text-danger small"><?= $field_errors['payment_method'] ?></div>
                <?php endif; ?>
              </div>

              <div class="col-12 form-check mt-3">
                <input class="form-check-input" type="checkbox" id="termsCheck" required>
                <label class="form-check-label" for="termsCheck">
                  I agree to the <a href="#">terms and conditions</a>.
                </label>
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
