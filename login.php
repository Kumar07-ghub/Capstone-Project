<?php
session_start();
include 'includes/header.php';
?>

<div class="container py-5">
  <?php if (isset($_GET['message']) && $_GET['message'] === 'login_required'): ?>
    <div class="alert alert-warning text-center">
      🔒 Please log in to proceed to checkout.
    </div>
  <?php endif; ?>

  <div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
      <div class="card shadow border-0 rounded-4">
        <div class="card-body p-4">
          <h3 class="mb-4 text-center text-success">Login to Your Account</h3>

          <?php if (isset($_SESSION['login_error'])): ?>
            <div class="alert alert-danger text-center">
              <?= $_SESSION['login_error']; unset($_SESSION['login_error']); ?>
            </div>
          <?php endif; ?>

          <form action="process_login.php" method="POST">
            <div class="mb-3">
              <label for="email" class="form-label">Email address</label>
              <input type="email" class="form-control" name="email" id="email" required>
            </div>
            <div class="mb-3">
              <label for="password" class="form-label">Password</label>
              <input type="password" class="form-control" name="password" id="password" required>
            </div>
            <button type="submit" class="btn btn-dark w-100">Login</button>
          </form>

          <div class="mt-3 text-center">
            <small>Don't have an account?
              <a href="signup.php" class="text-decoration-none text-success">Sign up here</a>
            </small>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
