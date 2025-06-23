<?php
session_start();
include 'includes/header.php';
?>

<div class="container text-center py-5">
  <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
  <h2 class="fw-bold mt-3 text-success">Thank you for your order!</h2>
  <p class="lead mt-2">Your order has been placed successfully. We’ll contact you when it ships.</p>

  <div class="mt-4">
    <a href="index.php" class="btn btn-outline-success rounded-pill px-4 me-2">
      <i class="bi bi-house-door-fill me-1"></i> Home
    </a>
    <a href="products.php" class="btn btn-success rounded-pill px-4">
      <i class="bi bi-cart-plus-fill me-1"></i> Continue Shopping
    </a>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
