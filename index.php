<<<<<<< HEAD
<?php include 'includes/header.php'; ?>
<?php include 'includes/db.php'; ?>

<?php if (isset($_SESSION['user'])): ?>
  <div class="text-end small text-muted mt-2 mb-2 me-3 container">
      👋 Welcome, <?= htmlspecialchars($_SESSION['user']['name']) ?> |
      <a href="logout.php" class="text-decoration-none">Logout</a>
  </div>
<?php endif; ?>

<!-- 🔍 Search Bar -->
<div class="search-banner py-4">
  <div class="container">
    <form class="row g-2 align-items-center justify-content-center" action="products.php" method="GET">
      <div class="col-lg-8 col-md-10 col-12">
        <div class="input-group shadow rounded-pill overflow-hidden">
          <input type="text" name="query" class="form-control form-control-lg border-0 ps-4" placeholder="Search fruits, vegetables, dairy, and more..." required>
          <button class="btn btn-dark px-4" type="submit">
            <i class="bi bi-search me-1"></i> Search
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Hero Banner -->
<div id="heroCarousel" class="carousel slide mb-4 rounded-3" data-bs-ride="carousel" style="height: auto;">
  <div class="carousel-inner">

    <div class="carousel-item active">
      <div class="p-5 mb-4 text-white rounded-3" style="background: linear-gradient(to right, rgba(0,0,0,0.5), rgba(0,0,0,0.3)), url('img/food_section.jpg') center/cover no-repeat;">
        <div class="container py-5 text-center">
          <h1 class="display-4 fw-bold">Welcome to Grocery Store</h1>
          <p class="col-lg-8 mx-auto fs-4">Fresh produce, everyday essentials, and household items delivered to your doorstep.</p>
          <a href="products.php" class="btn btn-light btn-lg mt-3">Shop Now</a>
        </div>
      </div>
    </div>

    <div class="carousel-item">
      <div class="p-5 mb-4 text-white rounded-3" style="background: linear-gradient(to right, rgba(0,0,0,0.5), rgba(0,0,0,0.3)), url('img/shop_image.jpg') center/cover no-repeat;">
        <div class="container py-5 text-center">
          <h1 class="display-4 fw-bold">Fresh Vegetables Daily</h1>
          <p class="col-lg-8 mx-auto fs-4">Get farm fresh veggies delivered directly to your kitchen.</p>
          <a href="products.php" class="btn btn-light btn-lg mt-3">Shop Now</a>
        </div>
      </div>
    </div>

    <div class="carousel-item">
      <div class="p-5 mb-4 text-white rounded-3" style="background: linear-gradient(to right, rgba(0,0,0,0.5), rgba(0,0,0,0.3)), url('img/the-indian-super-market-banner-indian-grocery-store.webp') center/cover no-repeat;">
        <div class="container py-5 text-center">
          <h1 class="display-4 fw-bold">Seasonal Fruits</h1>
          <p class="col-lg-8 mx-auto fs-4">Sweet, juicy fruits picked at their peak for you.</p>
          <a href="products.php" class="btn btn-light btn-lg mt-3">Shop Now</a>
        </div>
      </div>
    </div>

  </div>

  <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
    <span class="carousel-control-prev-icon"></span>
    <span class="visually-hidden">Previous</span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
    <span class="carousel-control-next-icon"></span>
    <span class="visually-hidden">Next</span>
  </button>
</div>

<!-- Sales Advertisement Modal -->
<div class="modal fade" id="salesModal" tabindex="-1" aria-labelledby="salesModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-danger shadow-lg">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="salesModalLabel">🔥 Special Sale!</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center">
        <img src="img/sale-banner.jpg" alt="Sale" class="img-fluid mb-3 rounded-3">
        <p class="fw-bold text-danger fs-5">Get 20% OFF on all fresh fruits this weekend only!</p>
      </div>
      <div class="modal-footer justify-content-center">
        <a href="products.php" class="btn btn-danger">Shop Now</a>
      </div>
    </div>
  </div>
</div>

<!-- Categories -->
<div class="container">
  <h2 class="mt-5 mb-4 text-center">Shop by Category</h2>
  <div class="row text-center g-4">
    <div class="col-12 col-md-4">
      <div class="card py-4 h-100">
        <div class="card-body">
          <i class="bi bi-basket-fill display-4 text-success mb-3"></i>
          <h5 class="card-title">Fresh Vegetables</h5>
          <p class="card-text">Organic and farm-picked vegetables delivered fresh daily.</p>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-4">
      <div class="card py-4 h-100">
        <div class="card-body">
          <i class="bi bi-apple display-4 text-warning mb-3"></i>
          <h5 class="card-title">Fruits</h5>
          <p class="card-text">Juicy seasonal fruits with unbeatable freshness.</p>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-4">
      <div class="card py-4 h-100">
        <div class="card-body">
          <i class="bi bi-cup-straw display-4 text-primary mb-3"></i>
          <h5 class="card-title">Dairy</h5>
          <p class="card-text">Milk, butter, cheese, and more from trusted brands.</p>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Latest Arrivals -->
<div class="container mt-5">
  <h2 class="mb-4 text-center">Latest Arrivals</h2>
  <div class="row g-4">
    <?php
      $sql = "SELECT * FROM products ORDER BY id DESC LIMIT 3";
      $result = $conn->query($sql);
      if ($result && $result->num_rows > 0) {
        while ($product = $result->fetch_assoc()) {
          // Get rating for current product
          $ratingSql = "SELECT AVG(rating) AS avg_rating, COUNT(*) AS rating_count FROM product_ratings WHERE product_id = " . $product['id'];
          $ratingRes = $conn->query($ratingSql);
          $rating = $ratingRes && $ratingRes->num_rows > 0 ? $ratingRes->fetch_assoc() : ['avg_rating' => 0, 'rating_count' => 0];
          $avgRating = round($rating['avg_rating'], 1);
          $ratingCount = $rating['rating_count'];

          echo '
          <div class="col-12 col-md-4">
            <div class="card h-100 shadow-sm">
              <img src="img/' . htmlspecialchars($product['image']) . '" class="card-img-top img-fluid" alt="' . htmlspecialchars($product['name']) . '">
              <div class="card-body d-flex flex-column">
                <h5 class="card-title">' . htmlspecialchars($product['name']) . '</h5>';

                // Star rating display
                echo '<div class="star-rating mb-2">';
                for ($i = 1; $i <= 5; $i++) {
                  echo '<span>' . ($i <= round($avgRating) ? '★' : '☆') . '</span>';
                }
                if ($ratingCount > 0) {
                  echo '<small class="text-muted ms-2">(' . $avgRating . ')</small>';
                } else {
                  echo '<small class="text-muted ms-2">No ratings</small>';
                }
                echo '</div>';

                echo '
                <p class="card-text text-muted">' . htmlspecialchars($product['description']) . '</p>
                <div class="mt-auto">
                  <p class="fw-bold mb-2">$' . number_format($product['price'], 2) . ' <span class="text-muted small">/ ' . htmlspecialchars($product['unit']) . '</span></p>
                  <a href="product_details.php?id=' . $product['id'] . '" class="btn btn-success w-100">View Product</a>
                </div>
              </div>
            </div>
          </div>';
        }
      } else {
        echo '<p class="text-center text-muted">No products available at the moment.</p>';
      }
    ?>
  </div>
</div>

<!-- Brands -->
<div class="container mt-5">
  <h2 class="mb-4 text-center">Our Trusted Brands</h2>
  <div class="row justify-content-center text-center g-3">
    <div class="col-6 col-md-3">
      <img src="img/brand-amul.jpg" class="img-fluid" alt="Amul">
    </div>
    <div class="col-6 col-md-3">
      <img src="img/brand-haldiram.webp" class="img-fluid" alt="Haldiram's">
    </div>
    <div class="col-6 col-md-3">
      <img src="img/brand-Mtr.png" class="img-fluid" alt="MTR">
    </div>
    <div class="col-6 col-md-3">
      <img src="img/brand-patanjali.png" class="img-fluid" alt="Patanjali">
    </div>
  </div>
</div>

<!-- Testimonials -->
<div class="container mt-5">
  <h2 class="text-center mb-4">What Our Customers Say</h2>
  <div class="row g-4 justify-content-center">
    <div class="col-12 col-md-4">
      <div class="card shadow text-center border-0 p-3">
        <img src="img/customer1.webp" class="rounded-circle mx-auto mb-3 img-fluid" style="width: 100px; height: 100px; object-fit: cover;">
        <div class="card-body">
          <p class="card-text">"Great service and amazing quality vegetables every time!"</p>
          <h6 class="fw-bold mb-0">Priya S.</h6>
          <small class="text-muted">Guelph, ON</small>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-4">
      <div class="card shadow text-center border-0 p-3">
        <img src="img/customer2.jpg" class="rounded-circle mx-auto mb-3 img-fluid" style="width: 100px; height: 100px; object-fit: cover;">
        <div class="card-body">
          <p class="card-text">"Their dairy section is the best in town. Fresh and affordable."</p>
          <h6 class="fw-bold mb-0">Alex M.</h6>
          <small class="text-muted">Waterloo, ON</small>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-4">
      <div class="card shadow text-center border-0 p-3">
        <img src="img/customer3.jpg" class="rounded-circle mx-auto mb-3 img-fluid" style="width: 100px; height: 100px; object-fit: cover;">
        <div class="card-body">
          <p class="card-text">"Always on-time delivery. My go-to store now!"</p>
          <h6 class="fw-bold mb-0">Ravi</h6>
          <small class="text-muted">Waterloo, ON</small>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Benefits -->
<div class="container py-5">
  <div class="row text-center g-4">
    <div class="col-6 col-md-3">
      <i class="bi bi-truck fs-1 text-success"></i>
      <h5 class="mt-3">Free Delivery</h5>
      <p>On orders above $50 across Ontario.</p>
    </div>
    <div class="col-6 col-md-3">
      <i class="bi bi-award fs-1 text-success"></i>
      <h5 class="mt-3">Premium Quality</h5>
      <p>Hand-picked products from trusted farms & brands.</p>
    </div>
    <div class="col-6 col-md-3">
      <i class="bi bi-cash-coin fs-1 text-success"></i>
      <h5 class="mt-3">Best Prices</h5>
      <p>Competitive pricing with regular discounts.</p>
    </div>
    <div class="col-6 col-md-3">
      <i class="bi bi-clock fs-1 text-success"></i>
      <h5 class="mt-3">Same-Day Delivery</h5>
      <p>Available in select cities when ordered before 2 PM.</p>
    </div>
  </div>
</div>

<style>
  .star-rating span {
    font-size: 1.6rem;
    color: orange;
    margin-right: 2px;
  }
</style>

=======
<?php include 'includes/header.php'; ?>
<?php include 'includes/db.php'; ?>

<?php if (isset($_SESSION['user'])): ?>
  <div class="text-end small text-muted mt-2 mb-2 me-3 container">
      👋 Welcome, <?= htmlspecialchars($_SESSION['user']['name']) ?> |
      <a href="logout.php" class="text-decoration-none">Logout</a>
  </div>
<?php endif; ?>

<!-- 🔍 Search Bar -->
<div class="search-banner py-4">
  <div class="container">
    <form class="row g-2 align-items-center justify-content-center" action="products.php" method="GET">
      <div class="col-lg-8 col-md-10 col-12">
        <div class="input-group shadow rounded-pill overflow-hidden">
          <input type="text" name="query" class="form-control form-control-lg border-0 ps-4" placeholder="Search fruits, vegetables, dairy, and more..." required>
          <button class="btn btn-dark px-4" type="submit">
            <i class="bi bi-search me-1"></i> Search
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Hero Banner -->
<div class="p-5 mb-4 text-white rounded-3" style="background: linear-gradient(to right, rgba(0,0,0,0.5), rgba(0,0,0,0.3)), url('img/the-indian-super-market-banner-indian-grocery-store.webp') center/cover no-repeat;">
  <div class="container py-5 text-center">
    <h1 class="display-4 fw-bold">Welcome to Grocery Store</h1>
    <p class="col-lg-8 mx-auto fs-4">Fresh produce, everyday essentials, and household items delivered to your doorstep.</p>
    <a href="products.php" class="btn btn-light btn-lg mt-3">Shop Now</a>
  </div>
</div>

<!-- Sales Advertisement Modal -->
<div class="modal fade" id="salesModal" tabindex="-1" aria-labelledby="salesModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-danger shadow-lg">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="salesModalLabel">🔥 Special Sale!</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center">
        <img src="img/sale-banner.jpg" alt="Sale" class="img-fluid mb-3 rounded-3">
        <p class="fw-bold text-danger fs-5">Get 20% OFF on all fresh fruits this weekend only!</p>
      </div>
      <div class="modal-footer justify-content-center">
        <a href="products.php" class="btn btn-danger">Shop Now</a>
      </div>
    </div>
  </div>
</div>

<!-- Categories -->
<div class="container">
  <h2 class="mt-5 mb-4 text-center">Shop by Category</h2>
  <div class="row text-center g-4">
    <div class="col-12 col-md-4">
      <div class="card py-4 h-100">
        <div class="card-body">
          <i class="bi bi-basket-fill display-4 text-success mb-3"></i>
          <h5 class="card-title">Fresh Vegetables</h5>
          <p class="card-text">Organic and farm-picked vegetables delivered fresh daily.</p>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-4">
      <div class="card py-4 h-100">
        <div class="card-body">
          <i class="bi bi-apple display-4 text-warning mb-3"></i>
          <h5 class="card-title">Fruits</h5>
          <p class="card-text">Juicy seasonal fruits with unbeatable freshness.</p>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-4">
      <div class="card py-4 h-100">
        <div class="card-body">
          <i class="bi bi-cup-straw display-4 text-primary mb-3"></i>
          <h5 class="card-title">Dairy</h5>
          <p class="card-text">Milk, butter, cheese, and more from trusted brands.</p>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- 🆕 Latest Arrivals -->
<div class="container mt-5">
  <h2 class="mb-4 text-center">Latest Arrivals</h2>
  <div class="row g-4">
    <?php
      $sql = "SELECT * FROM products ORDER BY id DESC LIMIT 3";
      $result = $conn->query($sql);
      if ($result && $result->num_rows > 0) {
        while ($product = $result->fetch_assoc()) {
          // Get rating for current product
          $ratingSql = "SELECT AVG(rating) AS avg_rating, COUNT(*) AS rating_count FROM product_ratings WHERE product_id = " . $product['id'];
          $ratingRes = $conn->query($ratingSql);
          $rating = $ratingRes && $ratingRes->num_rows > 0 ? $ratingRes->fetch_assoc() : ['avg_rating' => 0, 'rating_count' => 0];
          $avgRating = round($rating['avg_rating'], 1);
          $ratingCount = $rating['rating_count'];

          echo '
          <div class="col-12 col-md-4">
            <div class="card h-100 shadow-sm">
              <img src="img/' . htmlspecialchars($product['image']) . '" class="card-img-top img-fluid" alt="' . htmlspecialchars($product['name']) . '">
              <div class="card-body d-flex flex-column">
                <h5 class="card-title">' . htmlspecialchars($product['name']) . '</h5>';

                // Star rating display
                echo '<div class="star-rating mb-2">';
                for ($i = 1; $i <= 5; $i++) {
                  echo '<span>' . ($i <= round($avgRating) ? '★' : '☆') . '</span>';
                }
                if ($ratingCount > 0) {
                  echo '<small class="text-muted ms-2">(' . $avgRating . ')</small>';
                } else {
                  echo '<small class="text-muted ms-2">No ratings</small>';
                }
                echo '</div>';

                echo '
                <p class="card-text text-muted">' . htmlspecialchars($product['description']) . '</p>
                <div class="mt-auto">
                  <p class="fw-bold mb-2">$' . number_format($product['price'], 2) . ' <span class="text-muted small">/ ' . htmlspecialchars($product['unit']) . '</span></p>
                  <a href="product_details.php?id=' . $product['id'] . '" class="btn btn-success w-100">View Product</a>
                </div>
              </div>
            </div>
          </div>';
        }
      } else {
        echo '<p class="text-center text-muted">No products available at the moment.</p>';
      }
    ?>
  </div>
</div>

<!-- Brands -->
<div class="container mt-5">
  <h2 class="mb-4 text-center">Our Trusted Brands</h2>
  <div class="row justify-content-center text-center g-3">
    <div class="col-6 col-md-3">
      <img src="img/brand-amul.jpg" class="img-fluid" alt="Amul">
    </div>
    <div class="col-6 col-md-3">
      <img src="img/brand-haldiram.webp" class="img-fluid" alt="Haldiram's">
    </div>
    <div class="col-6 col-md-3">
      <img src="img/brand-mtr.png" class="img-fluid" alt="MTR">
    </div>
    <div class="col-6 col-md-3">
      <img src="img/brand-patanjali.png" class="img-fluid" alt="Patanjali">
    </div>
  </div>
</div>

<!-- Testimonials -->
<div class="container mt-5">
  <h2 class="text-center mb-4">What Our Customers Say</h2>
  <div class="row g-4 justify-content-center">
    <div class="col-12 col-md-4">
      <div class="card shadow text-center border-0 p-3">
        <img src="img/customer1.webp" class="rounded-circle mx-auto mb-3 img-fluid" style="width: 100px; height: 100px; object-fit: cover;">
        <div class="card-body">
          <p class="card-text">"Great service and amazing quality vegetables every time!"</p>
          <h6 class="fw-bold mb-0">Priya S.</h6>
          <small class="text-muted">Guelph, ON</small>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-4">
      <div class="card shadow text-center border-0 p-3">
        <img src="img/customer2.jpg" class="rounded-circle mx-auto mb-3 img-fluid" style="width: 100px; height: 100px; object-fit: cover;">
        <div class="card-body">
          <p class="card-text">"Their dairy section is the best in town. Fresh and affordable."</p>
          <h6 class="fw-bold mb-0">Alex M.</h6>
          <small class="text-muted">Waterloo, ON</small>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-4">
      <div class="card shadow text-center border-0 p-3">
        <img src="img/customer3.jpg" class="rounded-circle mx-auto mb-3 img-fluid" style="width: 100px; height: 100px; object-fit: cover;">
        <div class="card-body">
          <p class="card-text">"Always on-time delivery. My go-to store now!"</p>
          <h6 class="fw-bold mb-0">Ravi</h6>
          <small class="text-muted">Waterloo, ON</small>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Benefits -->
<div class="container py-5">
  <div class="row text-center g-4">
    <div class="col-6 col-md-3">
      <i class="bi bi-truck fs-1 text-success"></i>
      <h5 class="mt-3">Free Delivery</h5>
      <p>On orders above $50 across Ontario.</p>
    </div>
    <div class="col-6 col-md-3">
      <i class="bi bi-award fs-1 text-success"></i>
      <h5 class="mt-3">Premium Quality</h5>
      <p>Hand-picked products from trusted farms & brands.</p>
    </div>
    <div class="col-6 col-md-3">
      <i class="bi bi-cash-coin fs-1 text-success"></i>
      <h5 class="mt-3">Best Prices</h5>
      <p>Competitive pricing with regular discounts.</p>
    </div>
    <div class="col-6 col-md-3">
      <i class="bi bi-clock fs-1 text-success"></i>
      <h5 class="mt-3">Same-Day Delivery</h5>
      <p>Available in select cities when ordered before 2 PM.</p>
    </div>
  </div>
</div>

<style>
  .star-rating span {
    font-size: 1.6rem;
    color: orange;
    margin-right: 2px;
  }
</style>

>>>>>>> 07d8e6ccfd579de17e14e08d58f8a836c66fbf26
<?php include 'includes/footer.php'; ?>