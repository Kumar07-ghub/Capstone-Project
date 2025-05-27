<?php include 'includes/header.php'; ?>

<!-- Hero Banner -->
<div class="p-5 mb-4 text-white rounded-3" style="
    background: linear-gradient(to right, rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.3)),
                url('img/the-indian-super-market-banner-indian-grocery-store.webp') center/cover no-repeat;
">
  <div class="container py-5">
    <h1 class="display-4 fw-bold">Welcome to Grocery Store</h1>
    <p class="col-md-8 fs-4">Fresh produce, everyday essentials, and household items delivered to your doorstep.</p>
    <a href="products.php" class="btn btn-light btn-lg mt-3">Shop Now</a>
  </div>
</div>

<!-- Featured Categories -->
<h2 class="mt-5 mb-4 text-center">Shop by Category</h2>
<div class="row">
  <div class="col-md-4">
    <div class="card">
      <img src="img/fresh-organic-vegetables-basket.png" class="card-img-top" alt="Vegetables">
      <div class="card-body">
        <h5 class="card-title">Fresh Vegetables</h5>
        <p class="card-text">Organic and farm-picked vegetables delivered fresh daily.</p>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card">
      <img src="img/fresh-organic-fruits-basket-supermarket.png" class="card-img-top" alt="A colorful basket of fresh organic fruits including apples, oranges, strawberries, bananas, grapes, and mangoes">
      <div class="card-body">
        <h5 class="card-title">Fruits</h5>
        <p class="card-text">Juicy seasonal fruits with unbeatable freshness.</p>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card">
      <img src="img/fresh-dairy-products-milk-cheese-butter.png" class="card-img-top" alt="Dairy">
      <div class="card-body">
        <h5 class="card-title">Dairy</h5>
        <p class="card-text">Milk, butter, cheese, and more from trusted brands.</p>
      </div>
    </div>
  </div>
</div>

<!-- Testimonials -->
<h2 class="mt-5 text-center">What Our Customers Say</h2>
<div class="row justify-content-center mt-4">

  <div class="col-md-4 mb-4">
    <div class="card shadow text-center border-0 p-3">
      <img src="img/customer1.webp" class="rounded-circle mx-auto mb-3" alt="Customer Priya" style="width: 100px; height: 100px; object-fit: cover;">
      <div class="card-body">
        <p class="card-text">"Great service and amazing quality vegetables every time!"</p>
        <h6 class="mb-0 fw-bold">Priya S.</h6>
        <small class="text-muted">Guelph, ON</small>
      </div>
    </div>
  </div>

  <div class="col-md-4 mb-4">
    <div class="card shadow text-center border-0 p-3">
      <img src="img/customer2.jpg" class="rounded-circle mx-auto mb-3" alt="Customer Alex" style="width: 100px; height: 100px; object-fit: cover;">
      <div class="card-body">
        <p class="card-text">"Their dairy section is the best in town. Fresh and affordable."</p>
        <h6 class="mb-0 fw-bold">Alex M.</h6>
        <small class="text-muted">Waterloo, ON</small>
      </div>
    </div>
  </div>

  <div class="col-md-4 mb-4">
    <div class="card shadow text-center border-0 p-3">
      <img src="img/customer3.jpg" class="rounded-circle mx-auto mb-3" alt="Customer Ravi" style="width: 100px; height: 100px; object-fit: cover;">
      <div class="card-body">
        <p class="card-text">"Always on-time delivery. My go-to store now!"</p>
        <h6 class="mb-0 fw-bold">Ravi K.</h6>
        <small class="text-muted">Kitchener, ON</small>
      </div>
    </div>
  </div>

</div>


<!-- Newsletter Signup -->
<div class="bg-success text-white p-5 mt-5 rounded">
  <h3>Subscribe to Our Newsletter</h3>
  <p>Stay updated on offers, new products, and discounts.</p>
  <form class="row g-3">
    <div class="col-md-8">
      <input type="email" class="form-control" placeholder="Enter your email">
    </div>
    <div class="col-md-4">
      <button type="submit" class="btn btn-light w-100">Subscribe</button>
    </div>
  </form>
</div>

<?php include 'includes/footer.php'; ?>
