<<<<<<< HEAD
<?php
session_start();
include 'includes/header.php';
?>

<style>
  @import url('https://fonts.googleapis.com/css2?family=Roboto&display=swap');
  
  body, input, label, button, small {
    font-family: 'Roboto', sans-serif !important;
  }
</style>

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

          <form action="process_login.php" method="POST" id="loginForm" novalidate>
            <div class="mb-3">
              <label for="email" class="form-label">Email address <span class="text-danger">*</span></label>
              <input type="email" class="form-control" name="email" id="email" placeholder="you@example.com" required>
              <div class="invalid-feedback"></div>
            </div>

            <div class="mb-3">
              <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
              <div class="input-group">
                <input type="password" class="form-control" name="password" id="password" placeholder="Minimum 8 characters" minlength="8" required>
                <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                  Show
                </button>
              </div>
              <div class="invalid-feedback"></div>
            </div>

            <button type="submit" class="btn btn-dark w-100" id="submitBtn" disabled>Login</button>
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

<script>
(() => {
  const form = document.getElementById('loginForm');
  const submitBtn = document.getElementById('submitBtn');

  const validators = {
    email: (val) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val),
    password: (val) => val.length >= 8
  };

  const messages = {
    email: "Please enter a valid email address.",
    password: "Password must be at least 8 characters."
  };

  // Track which fields the user has interacted with
  const touched = {
    email: false,
    password: false
  };

  function validateField(field) {
    const val = field.value;
    const name = field.name;
    const isValid = validators[name](val);
    const feedback = field.closest('.mb-3').querySelector('.invalid-feedback');

    if (!touched[name]) {
      // If user hasn't touched the field, don't show validation styles or messages
      field.classList.remove('is-invalid', 'is-valid');
      feedback.textContent = '';
      return isValid;
    }

    if (isValid) {
      field.classList.remove('is-invalid');
      field.classList.add('is-valid');
      feedback.textContent = '';
    } else {
      field.classList.add('is-invalid');
      field.classList.remove('is-valid');
      feedback.textContent = messages[name];
    }
    return isValid;
  }

  function validateForm() {
    let valid = true;
    ['email', 'password'].forEach(name => {
      const field = form.elements[name];
      if (!validateField(field)) valid = false;
    });
    submitBtn.disabled = !valid;
  }

  // When user types, mark field as touched, validate, and check form
  form.addEventListener('input', e => {
    if (validators[e.target.name]) {
      touched[e.target.name] = true;
      validateField(e.target);
      validateForm();
    }
  });

  form.addEventListener('submit', e => {
    // Mark all fields as touched on submit
    Object.keys(touched).forEach(name => touched[name] = true);
    validateForm();

    if (submitBtn.disabled) {
      e.preventDefault();
    }
  });

  // Initially disable submit
  submitBtn.disabled = true;
})();

// Toggle password visibility
document.getElementById('togglePassword').addEventListener('click', function () {
  const passwordInput = document.getElementById('password');
  const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
  passwordInput.setAttribute('type', type);
  this.textContent = type === 'password' ? 'Show' : 'Hide';
});

</script>

<?php include 'includes/footer.php'; ?>
=======
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
>>>>>>> 07d8e6ccfd579de17e14e08d58f8a836c66fbf26
