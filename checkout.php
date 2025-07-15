<?php
session_start();  // Ensure this is at the top of the file, before any HTML output

require 'vendor/autoload.php';
include 'includes/db.php';
include 'functions.php';

if (!isset($_SESSION['user'])) {
    $_SESSION['redirect_after_login'] = 'checkout.php';
    header("Location: login.php?message=login_required"); // This is fine because it's at the top
    exit;
}

if (!isset($_SESSION['cart']) || count($_SESSION['cart']) === 0) {
    header("Location: products.php"); // Same here
    exit;
}

// Include Stripe
\Stripe\Stripe::setApiKey('sk_test_51RgnJ8IK8JHviDvgBnLPmht2A1d8seVxkFz4nVoOkydrNa1XsclDADHztPImDtt5OvOOgbpIxkjblud8Su9pNYvQ00GsKeDCKm');


// Handle the form submission
$errors = $field_errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $required_fields = ['full_name','email','phone','address','city','province','postal_code','country','payment_method'];
    foreach ($required_fields as $f) {
        if (empty($_POST[$f])) $field_errors[$f] = ucfirst(str_replace('_',' ',$f)) . " is required.";
    }
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) $field_errors['email'] = "Invalid email.";
    if (!preg_match("/^\d{10,15}$/", preg_replace("/\D/", "", $_POST['phone']))) $field_errors['phone'] = "Invalid phone.";

    if (empty($field_errors)) {
        extract(array_map('htmlspecialchars', $_POST), EXTR_OVERWRITE);
        $user_id = $_SESSION['user']['id'];
        $total = 0;
        $line_items = [];

        // Calculate cart total and prepare Stripe line items
        foreach ($_SESSION['cart'] as $pid => $qty) {
            $stmt = $conn->prepare("SELECT name, price FROM products WHERE id=?");
            $stmt->bind_param("i",$pid);
            $stmt->execute();
            $row = $stmt->get_result()->fetch_assoc();
            $subtotal = $row['price'] * $qty;
            $total += $subtotal;
            $line_items[] = [
                'price_data'=>[
                    'currency'=>'cad',
                    'product_data'=>['name'=>$row['name']],
                    'unit_amount'=>intval($row['price']*100),
                ],
                'quantity'=>$qty
            ];
        }

        $tax = round($total * 0.13, 2);
        $grand_total = round($total + $tax, 2);

        // Stripe payment processing
        if ($payment_method === 'card') {
            // Append tax as a line item
            $line_items[] = [
                'price_data'=>[
                    'currency'=>'cad',
                    'product_data'=>['name'=>'Tax (13%)'],
                    'unit_amount'=>intval($tax * 100),
                ],
                'quantity'=>1
            ];

            // Save billing info & cart
            $_SESSION['billing_info'] = compact(
                'full_name','email','phone','address','city','province','postal_code','country','instructions','user_id'
            );
            $_SESSION['cart_backup'] = $_SESSION['cart'];

            $session = \Stripe\Checkout\Session::create([
                'payment_method_types'=>['card'],
                'line_items'=>$line_items,
                'mode'=>'payment',
                'customer_email'=>$email,
                'success_url'=>'http://' . $_SERVER['HTTP_HOST'] . '/grocery-store/order-success.php?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url'=>'http://' . $_SERVER['HTTP_HOST'] . '/grocery-store/checkout.php?payment=cancelled'
            ]);

            header("Location: " . $session->url);
            exit;
        }

        // Process cash/interac orders
        $stmt = $conn->prepare("INSERT INTO orders (user_id, name, email, phone, address, city, province, postal_code, country, instructions, payment_method, total) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param("issssssssssd", $user_id, $full_name, $email, $phone, $address, $city, $province, $postal_code, $country, $instructions, $payment_method, $grand_total);
        $stmt->execute();
        $order_id = $stmt->insert_id;

        foreach ($_SESSION['cart'] as $pid => $qty) {
            $stmt = $conn->prepare("SELECT price FROM products WHERE id=?");
            $stmt->bind_param("i",$pid);
            $stmt->execute();
            $price = $stmt->get_result()->fetch_assoc()['price'];
            $ins = $conn->prepare("INSERT INTO order_items (order_id,product_id,quantity,price) VALUES (?,?,?,?)");
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
?>
<?php include 'includes/header.php'; ?>

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
