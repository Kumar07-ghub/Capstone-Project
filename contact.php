<?php

session_start();

include 'includes/db.php';

?>

<!DOCTYPE html>

<html lang="en">

<head>

  <?php include 'includes/header.php'; ?>

  <link rel="preconnect" href="https://fonts.googleapis.com">

  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

  <link 

    href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" 

    rel="stylesheet">



  <!-- Override Bootstrap font stack -->

  <style>

    body,

    .form-label,

    .form-control,

    .btn,

    h2 {

      font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif;

    }

  </style>

</head>

<body>



<?php

$name    = $email    = $message = "";

$errors  = [];



if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (empty(trim($_POST["name"]))) {

        $errors["name"] = "Name is required.";

    } else {

        $name = htmlspecialchars(trim($_POST["name"]));

    }



    if (empty(trim($_POST["email"]))) {

        $errors["email"] = "Email is required.";

    } elseif (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {

        $errors["email"] = "Enter a valid email address.";

    } else {

        $email = htmlspecialchars(trim($_POST["email"]));

    }



    if (empty(trim($_POST["message"]))) {

        $errors["message"] = "Message is required.";

    } else {

        $message = htmlspecialchars(trim($_POST["message"]));

    }



    if (empty($errors)) {

        echo "<div class='alert alert-success container mt-4'>Thank you! Your message has been sent.</div>";

        $name = $email = $message = "";

    }

}

?>



<div class="container mt-5 mb-5">

  <h2 class="mb-4 text-center">Contact Us</h2>

  <form id="contactForm" method="POST" class="needs-validation" novalidate>

    <div class="mb-3">

      <label for="name" class="form-label">Name <span class="text-danger">*</span></label>

      <input type="text" class="form-control" id="name" name="name"

             placeholder="John Doe" value="<?= $name ?>" required autocomplete="name">

      <div class="valid-feedback">Looks good!</div>

      <div class="invalid-feedback"><?= $errors["name"] ?? "Please enter your full name." ?></div>

    </div>



    <div class="mb-3">

      <label for="email" class="form-label">Email <span class="text-danger">*</span></label>

      <input type="email" class="form-control" id="email" name="email"

             placeholder="john@example.com" value="<?= $email ?>" required autocomplete="email">

      <div class="valid-feedback">Valid email!</div>

      <div class="invalid-feedback"><?= $errors["email"] ?? "Enter a valid email address." ?></div>

    </div>



    <div class="mb-3">

      <label for="message" class="form-label">Message <span class="text-danger">*</span></label>

      <textarea class="form-control" id="message" name="message"

                placeholder="Write your message..." rows="5" required autocomplete="off"><?= $message ?></textarea>

      <div class="valid-feedback">Thanks for reaching out!</div>

      <div class="invalid-feedback"><?= $errors["message"] ?? "Please enter a message." ?></div>

    </div>



    <button type="submit" class="btn btn-success px-4">Send Message</button>

  </form>

</div>



<script>

  document.querySelectorAll('input, textarea').forEach(input => {

    input.addEventListener('input', function () {

      if (input.checkValidity()) {

        input.classList.add("is-valid");

        input.classList.remove("is-invalid");

      } else {

        input.classList.add("is-invalid");

        input.classList.remove("is-valid");

      }

    });

  });



  (() => {

    'use strict';

    const form = document.querySelector('.needs-validation');

    form.addEventListener('submit', event => {

      if (!form.checkValidity()) {

        event.preventDefault();

        event.stopPropagation();

      }

      form.classList.add('was-validated');

    }, false);

  })();

</script>



<?php include 'includes/footer.php'; ?>

</body>

</html>

