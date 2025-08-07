<?php
// Detect if the current script is in the admin area
if (!isset($is_admin)) {
    $is_admin = strpos($_SERVER['PHP_SELF'], '/admin/') !== false;
}
$prefix = $is_admin ? '../' : '';
?>

</div> <!-- container -->

<!-- Responsive Footer --> 
<footer style="background-color: #2C3E50;" class="bg-dark text-white pt-5 pb-4 mt-5">
    <div class="container">
        <div class="row">

            <!-- About -->
            <div class="col-12 col-md-6 col-lg-3 mb-4">
                <h5 class="text-uppercase mb-4 fw-bold text-white">The Indian Supermarket</h5>
                <p class="text-white">Your one-stop shop for daily essentials, fresh produce, and home needs. Fast delivery, great prices,
                    and premium quality products.</p>
            </div>

            <!-- Quick Links -->
            <div class="col-12 col-md-6 col-lg-2 mb-4 mx-auto text-center">
                <h5 class="text-uppercase mb-4 fw-bold text-white">Quick Links</h5>
                <ul class="list-unstyled">
                    <li><a href="<?= $prefix ?>index.php" class="text-white text-decoration-none">Home</a></li>
                    <li><a href="<?= $prefix ?>products.php" class="text-white text-decoration-none">Products</a></li>
                    <li><a href="#" class="text-white text-decoration-none">About</a></li>
                    <li><a href="<?= $prefix ?>contact.php" class="text-white text-decoration-none">Contact</a></li>
                </ul>
            </div>

            <!-- Contact -->
            <div class="col-12 col-md-6 col-lg-3 mb-4">
                <h5 class="text-uppercase mb-4 fw-bold text-white">Contact</h5>
                <p class="text-white"><span class="bi bi-geo-alt-fill me-2"></span>509 Wilson Ave #20, Kitchener, ON N2C 2M4</p>
                <p class="text-white"><span class="bi bi-envelope-fill me-2"></span>support@indiansupermarket.com</p>
                <p class="text-white"><span class="bi bi-phone-fill me-2"></span>+1 (519) 893-8444</p>
            </div>

            <!-- Google Map -->
            <div class="col-12 col-md-6 col-lg-4 mb-4">
                <h5 class="text-uppercase mb-4 fw-bold text-white">Find Us on Map</h5>
                <div class="ratio ratio-16x9 rounded shadow">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2824.3814104827113!2d-80.41963992441626!3d43.419998071117616!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x882bf5198baf3d09%3A0x2073eb98e300576!2s509%20Wilson%20Ave%20%2320%2C%20Kitchener%2C%20ON%20N2C%202M4%2C%20Canada!5e0!3m2!1sen!2sca!4v1717522314573"
                        style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>
        </div>

        <hr class="my-4 text-white">

        <!-- Copyright & Social -->
        <div class="row align-items-center text-center text-md-start">
            <div class="col-md-6">
                <p class="mb-0 text-white">© 2025 The Indian Supermarket. All rights reserved.</p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <a href="#" class="text-white me-3" aria-label="Follow us on Facebook"><span class="bi bi-facebook fs-5" aria-hidden="true"></span></a>
                <a href="#" class="text-white me-3" aria-label="Follow us on Instagram"><span class="bi bi-instagram fs-5" aria-hidden="true"></span></a>
                <a href="#" class="text-white" aria-label="Follow us on Twitter"><span class="bi bi-twitter-x fs-5" aria-hidden="true"></span></a>
            </div>
        </div>
    </div>
</footer>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>
  AOS.init();
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  const nav = document.querySelector('nav.navbar');
  window.addEventListener('scroll', function() {
    if (window.scrollY > 50) {
      nav.classList.add('shrink');
    } else {
      nav.classList.remove('shrink');
    }
  });
});
</script>
<script src="<?= $prefix ?>js/script.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<!-- Bootstrap JS -->
</body>
</html>