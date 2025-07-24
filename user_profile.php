<<<<<<< HEAD
<?php
session_start();
include 'includes/db.php';
include 'includes/header.php';

if (!isset($_SESSION['user'])) {
    $_SESSION['redirect_after_login'] = 'user_profile.php';
    header("Location: login.php?message=login_required");
    exit;
}

$user = $_SESSION['user'];
$user_id = $user['id'];
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<div class="container py-5">
  <h2 class="text-center fw-bold text-success mb-5">👤 My Profile</h2>

  <div class="row g-4">
    <!-- Profile Card -->
    <div class="col-md-4">
      <div class="position-sticky" style="top: 100px;">
        <div class="card shadow-sm rounded-4 border-0">
          <div class="card-body text-center">
            <!-- Profile Photo -->
            <div class="mb-3 mx-auto" style="width: 150px; height: 150px; overflow: hidden; border-radius: 50%; border: 3px solid #198754; box-shadow: 0 0 8px rgba(25, 135, 84, 0.5);">
              <img src="<?= !empty($user['photo']) ? htmlspecialchars($user['photo']) : 'img/apples.jpg' ?>" alt="Profile photo" class="img-fluid" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            <h5 class="fw-semibold mb-1"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h5>
            <p class="text-muted mb-2"><?= htmlspecialchars($user['email']) ?></p>
            <span class="badge bg-success-subtle text-success">Customer</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Tabs and Content -->
    <div class="col-md-8">
      <ul class="nav nav-tabs mb-3" id="profileTabs" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active text-danger fw-bold" id="orders-tab" data-bs-toggle="tab" data-bs-target="#orders" type="button" role="tab" aria-controls="orders" aria-selected="true">📦 Orders</button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link text-danger fw-bold" id="account-tab" data-bs-toggle="tab" data-bs-target="#account" type="button" role="tab" aria-controls="account" aria-selected="false">⚙️ Account Info</button>
        </li>
      </ul>

      <div class="tab-content" id="profileTabsContent">
        <!-- Orders Tab -->
        <div class="tab-pane fade show active" id="orders" role="tabpanel" aria-labelledby="orders-tab">
          <div class="accordion" id="orderHistoryAccordion">
            <?php
            $stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $orders = $stmt->get_result();

            if ($orders->num_rows > 0):
              while ($order = $orders->fetch_assoc()):
                $order_id = $order['id'];
                ?>
                <div class="accordion-item mb-2">
                  <h2 class="accordion-header" id="heading-order-<?= $order_id ?>">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-order-<?= $order_id ?>" aria-expanded="false" aria-controls="collapse-order-<?= $order_id ?>">
                      🧾 Order #<?= $order['id'] ?> - <?= date("M d, Y", strtotime($order['created_at'])) ?>
                    </button>
                  </h2>
                  <div id="collapse-order-<?= $order_id ?>" class="accordion-collapse collapse" aria-labelledby="heading-order-<?= $order_id ?>" data-bs-parent="#orderHistoryAccordion">
                    <div class="accordion-body">
                      <p><strong>Total:</strong> $<?= number_format($order['total'], 2) ?> CAD</p>
                      <p><strong>Payment:</strong> <?= ucfirst($order['payment_method']) ?></p>
                      <p><strong>Shipping:</strong> <?= htmlspecialchars($order['address']) ?>, <?= htmlspecialchars($order['city']) ?>, <?= htmlspecialchars($order['province']) ?>, <?= htmlspecialchars($order['country']) ?></p>
               
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
                      
                      <?php
                      $item_stmt = $conn->prepare("
                          SELECT p.name, oi.quantity, oi.price
                          FROM order_items oi
                          JOIN products p ON oi.product_id = p.id
                          WHERE oi.order_id = ?
                      ");
                      $item_stmt->bind_param("i", $order_id);
                      $item_stmt->execute();
                      $items = $item_stmt->get_result();

                      if ($items->num_rows > 0): ?>
                        <ul class="list-group list-group-flush small">
                          <?php while ($item = $items->fetch_assoc()): ?>
                            <li class="list-group-item px-0 py-1 d-flex justify-content-between">
                              <span><?= htmlspecialchars($item['name']) ?> × <?= $item['quantity'] ?></span>
                              <span class="text-muted">$<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
                            </li>
                          <?php endwhile; ?>
                        </ul>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
              <?php endwhile;
            else: ?>
              <p class="text-muted">You haven’t placed any orders yet.</p>
            <?php endif; ?>
          </div>
        </div>

        <!-- Account Info Tab -->
        <div class="tab-pane fade" id="account" role="tabpanel" aria-labelledby="account-tab">
          <div class="card border-0 shadow-sm rounded-4 p-4">
            <h5 class="text-success mb-3">Update Account Info</h5>

            <form method="POST" action="update_profile.php" enctype="multipart/form-data">
              <div class="mb-3">
                <label for="first_name" class="form-label fw-semibold">First Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="first_name" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>" required>
              </div>
              
              <div class="mb-3">
                <label for="last_name" class="form-label fw-semibold">Last Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="last_name" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>" required>
              </div>

              <div class="mb-3">
                <label for="email" class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
              </div>

              <div class="mb-3">
                <label for="photo" class="form-label fw-semibold">Profile Photo</label>
                <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
              </div>

              <button type="submit" class="btn btn-danger">Save Changes</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  .badge.bg-success-subtle {
    background-color: #e6f4ea;
    font-weight: 500;
  }
  .accordion-button::after {
    transition: transform 0.3s ease;
  }
  .accordion-button:not(.collapsed)::after {
    transform: rotate(180deg);
  }

  .nav-tabs .nav-link {
    color: #dc3545 !important;
    font-weight: 700;
    background-color: transparent;
    border-color: transparent;
  }
  .nav-tabs .nav-link.active {
    color: #dc3545 !important;
    background-color: #fff !important;
    border-color: #dee2e6 #dee2e6 #fff !important;
  }
  .nav-tabs .nav-link:hover {
    color: #a71d2a !important;
    background-color: #f8d7da;
    border-color: transparent;
  }
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php include 'includes/footer.php'; ?>
=======
<?php
session_start();
include 'includes/db.php';
include 'includes/header.php';

if (!isset($_SESSION['user'])) {
    $_SESSION['redirect_after_login'] = 'user_profile.php';
    header("Location: login.php?message=login_required");
    exit;
}

$user = $_SESSION['user'];
$user_id = $user['id'];
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<div class="container py-5">
  <h2 class="text-center fw-bold text-success mb-5">👤 My Profile</h2>

  <div class="row g-4">
    <!-- Profile Card -->
    <div class="col-md-4">
      <div class="position-sticky" style="top: 100px;">
        <div class="card shadow-sm rounded-4 border-0">
          <div class="card-body text-center">
            <!-- Profile Photo -->
            <div class="mb-3 mx-auto" style="width: 150px; height: 150px; overflow: hidden; border-radius: 50%; border: 3px solid #198754; box-shadow: 0 0 8px rgba(25, 135, 84, 0.5);">
              <img src="<?= !empty($user['photo']) ? htmlspecialchars($user['photo']) : 'img/apples.jpg' ?>" alt="Profile photo" class="img-fluid" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            <h5 class="fw-semibold mb-1"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h5>
            <p class="text-muted mb-2"><?= htmlspecialchars($user['email']) ?></p>
            <span class="badge bg-success-subtle text-success">Customer</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Tabs and Content -->
    <div class="col-md-8">
      <ul class="nav nav-tabs mb-3" id="profileTabs" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active text-danger fw-bold" id="orders-tab" data-bs-toggle="tab" data-bs-target="#orders" type="button" role="tab" aria-controls="orders" aria-selected="true">📦 Orders</button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link text-danger fw-bold" id="account-tab" data-bs-toggle="tab" data-bs-target="#account" type="button" role="tab" aria-controls="account" aria-selected="false">⚙️ Account Info</button>
        </li>
      </ul>

      <div class="tab-content" id="profileTabsContent">
        <!-- Orders Tab -->
        <div class="tab-pane fade show active" id="orders" role="tabpanel" aria-labelledby="orders-tab">
          <div class="accordion" id="orderHistoryAccordion">
            <?php
            $stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $orders = $stmt->get_result();

            if ($orders->num_rows > 0):
              while ($order = $orders->fetch_assoc()):
                $order_id = $order['id'];
                ?>
                <div class="accordion-item mb-2">
                  <h2 class="accordion-header" id="heading-order-<?= $order_id ?>">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-order-<?= $order_id ?>" aria-expanded="false" aria-controls="collapse-order-<?= $order_id ?>">
                      🧾 Order #<?= $order['id'] ?> - <?= date("M d, Y", strtotime($order['created_at'])) ?>
                    </button>
                  </h2>
                  <div id="collapse-order-<?= $order_id ?>" class="accordion-collapse collapse" aria-labelledby="heading-order-<?= $order_id ?>" data-bs-parent="#orderHistoryAccordion">
                    <div class="accordion-body">
                      <p><strong>Total:</strong> $<?= number_format($order['total'], 2) ?> CAD</p>
                      <p><strong>Payment:</strong> <?= ucfirst($order['payment_method']) ?></p>
                      <p><strong>Shipping:</strong> <?= htmlspecialchars($order['address']) ?>, <?= htmlspecialchars($order['city']) ?>, <?= htmlspecialchars($order['province']) ?>, <?= htmlspecialchars($order['country']) ?></p>
               
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
                      
                      <?php
                      $item_stmt = $conn->prepare("
                          SELECT p.name, oi.quantity, oi.price
                          FROM order_items oi
                          JOIN products p ON oi.product_id = p.id
                          WHERE oi.order_id = ?
                      ");
                      $item_stmt->bind_param("i", $order_id);
                      $item_stmt->execute();
                      $items = $item_stmt->get_result();

                      if ($items->num_rows > 0): ?>
                        <ul class="list-group list-group-flush small">
                          <?php while ($item = $items->fetch_assoc()): ?>
                            <li class="list-group-item px-0 py-1 d-flex justify-content-between">
                              <span><?= htmlspecialchars($item['name']) ?> × <?= $item['quantity'] ?></span>
                              <span class="text-muted">$<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
                            </li>
                          <?php endwhile; ?>
                        </ul>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
              <?php endwhile;
            else: ?>
              <p class="text-muted">You haven’t placed any orders yet.</p>
            <?php endif; ?>
          </div>
        </div>

        <!-- Account Info Tab -->
        <div class="tab-pane fade" id="account" role="tabpanel" aria-labelledby="account-tab">
          <div class="card border-0 shadow-sm rounded-4 p-4">
            <h5 class="text-success mb-3">Update Account Info</h5>

            <form method="POST" action="update_profile.php" enctype="multipart/form-data">
              <div class="mb-3">
                <label for="first_name" class="form-label fw-semibold">First Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="first_name" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>" required>
              </div>
              
              <div class="mb-3">
                <label for="last_name" class="form-label fw-semibold">Last Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="last_name" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>" required>
              </div>

              <div class="mb-3">
                <label for="email" class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
              </div>

              <div class="mb-3">
                <label for="photo" class="form-label fw-semibold">Profile Photo</label>
                <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
              </div>

              <button type="submit" class="btn btn-danger">Save Changes</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  .badge.bg-success-subtle {
    background-color: #e6f4ea;
    font-weight: 500;
  }
  .accordion-button::after {
    transition: transform 0.3s ease;
  }
  .accordion-button:not(.collapsed)::after {
    transform: rotate(180deg);
  }

  .nav-tabs .nav-link {
    color: #dc3545 !important;
    font-weight: 700;
    background-color: transparent;
    border-color: transparent;
  }
  .nav-tabs .nav-link.active {
    color: #dc3545 !important;
    background-color: #fff !important;
    border-color: #dee2e6 #dee2e6 #fff !important;
  }
  .nav-tabs .nav-link:hover {
    color: #a71d2a !important;
    background-color: #f8d7da;
    border-color: transparent;
  }
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php include 'includes/footer.php'; ?>
>>>>>>> 07d8e6ccfd579de17e14e08d58f8a836c66fbf26
