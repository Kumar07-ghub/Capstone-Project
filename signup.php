<?php
session_start();
include 'includes/header.php';
?>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
      <div class="card shadow border-0 rounded-4">
        <div class="card-body p-4">
          <h3 class="mb-4 text-center text-success">Create an Account</h3>

          <!-- ✅ Show error if email already exists -->
          <?php if (isset($_SESSION['signup_error'])): ?>
            <div class="alert alert-danger text-center">
              <?= $_SESSION['signup_error']; unset($_SESSION['signup_error']); ?>
            </div>
          <?php endif; ?>

          <form action="process_signup.php" method="POST">
            <div class="mb-3">
              <label for="name" class="form-label">Full Name</label>
              <input type="text" class="form-control" name="name" id="name" required>
            </div>
            <div class="mb-3">
              <label for="email" class="form-label">Email address</label>
              <input type="email" class="form-control" name="email" id="email" required>
            </div>
            <div class="mb-3">
              <label for="password" class="form-label">Password</label>
              <input type="password" class="form-control" name="password" id="password" required>
            </div>
            <button type="submit" class="btn btn-success w-100">Sign Up</button>
          </form>

          <div class="mt-3 text-center">
            <small>Already have an account? <a href="login.php" class="text-decoration-none text-dark">Login here</a></small>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
