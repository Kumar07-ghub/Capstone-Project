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

          <?php if (isset($_SESSION['signup_error'])): ?>
            <div class="alert alert-danger text-center">
              <?= $_SESSION['signup_error']; unset($_SESSION['signup_error']); ?>
            </div>
          <?php endif; ?>

          <form id="signupForm" action="process_signup.php" method="POST" novalidate>
            <div class="mb-3">
              <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control" name="first_name" id="first_name" placeholder="John" required>
              <div class="invalid-feedback"></div>
            </div>

            <div class="mb-3">
              <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control" name="last_name" id="last_name" placeholder="Doe" required>
              <div class="invalid-feedback"></div>
            </div>

            <div class="mb-3">
              <label for="email" class="form-label">Email address <span class="text-danger">*</span></label>
              <input type="email" class="form-control" name="email" id="email" placeholder="email@example.com" required>
              <div class="invalid-feedback"></div>
            </div>

            <div class="mb-3">
              <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
              <div class="input-group">
                <input type="password" class="form-control" name="password" id="password" minlength="8" required>
                <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                  Show
                </button>
              </div>
              <small class="form-text text-muted">At least 8 characters</small>
              <div class="invalid-feedback"></div>
            </div>

            <button type="submit" class="btn btn-success w-100" id="submitBtn" disabled>Sign Up</button>
          </form>

          <div class="mt-3 text-center">
            <small>Already have an account? <a href="login.php" class="text-decoration-none text-dark">Login here</a></small>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  // Real-time validation
 (() => {
  const form = document.getElementById('signupForm');
  const submitBtn = document.getElementById('submitBtn');

  const validators = {
    first_name: (val) => val.trim() !== '',
    last_name: (val) => val.trim() !== '',
    email: (val) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val),
    password: (val) => val.length >= 8
  };

  const messages = {
    first_name: "First name is required.",
    last_name: "Last name is required.",
    email: "Please enter a valid email address.",
    password: "Password must be at least 8 characters."
  };

  const touched = {
    first_name: false,
    last_name: false,
    email: false,
    password: false
  };

  function validateField(field) {
    const val = field.value;
    const name = field.name;
    const isValid = validators[name](val);
    const feedback = field.closest('.mb-3').querySelector('.invalid-feedback');

    if (!touched[name]) {
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
    ['first_name', 'last_name', 'email', 'password'].forEach(name => {
      const field = form.elements[name];
      if (!validateField(field)) valid = false;
    });
    submitBtn.disabled = !valid;
  }

  form.addEventListener('input', e => {
    if (validators[e.target.name]) {
      touched[e.target.name] = true;
      validateField(e.target);
      validateForm();
    }
  });

  form.addEventListener('submit', e => {
    Object.keys(touched).forEach(name => touched[name] = true);
    validateForm();
    if (submitBtn.disabled) {
      e.preventDefault();
    }
  });

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
