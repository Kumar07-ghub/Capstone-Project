<?php
session_start();
include 'includes/header.php';
include 'includes/db.php';

// Initialize cart
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle Add to Cart form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $id = $_POST['product_id'];
    $qty = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + $qty;
    $_SESSION['success'] = "✅ Product added to cart successfully!";
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}
?>

<h2 class="mb-4 text-center fw-bold text-success">Shop Fresh Groceries</h2>

<!-- ✅ Success Message -->
<?php if (isset($_SESSION['success'])): ?>
    <div id="successMsg" class="alert alert-success text-center fw-semibold">
        <?= $_SESSION['success']; unset($_SESSION['success']); ?>
    </div>
<?php endif; ?>

<!-- 🔍 Filter Section -->
<div class="row mb-5 justify-content-center">
    <div class="col-md-4 mb-2">
        <input type="text" id="searchBar" class="form-control form-control-lg shadow-sm" placeholder="🔍 Search products...">
    </div>
    <div class="col-md-3 mb-2">
        <select id="categoryFilter" class="form-select form-select-lg shadow-sm">
            <option value="">All Categories</option>
            <option value="Fruits">Fruits</option>
            <option value="Vegetables">Vegetables</option>
            <option value="Dairy">Dairy</option>
        </select>
    </div>
</div>

<!-- 🛒 Product Grid -->
<div class="row" id="productList">
<?php
$sql = "SELECT * FROM products";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '
        <div class="col-lg-4 col-md-6 mb-4 product-item" data-name="' . strtolower($row["name"]) . '" data-category="' . $row["category"] . '">
            <div class="card h-100 shadow-lg border-0 rounded-4 product-card">
                <img src="img/' . $row["image"] . '" class="card-img-top rounded-top-4" alt="' . $row["name"] . '" style="height: 250px; object-fit: cover;">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title fw-semibold">' . $row["name"] . '</h5>
                    <p class="card-text text-muted mb-3">$<strong>' . $row["price"] . '</strong></p>
                    <form method="POST">
                        <input type="hidden" name="product_id" value="' . $row["id"] . '">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="btn btn-outline-primary mt-auto rounded-pill w-100">
                            🛒 Add to Cart
                        </button>
                    </form>
                </div>
            </div>
        </div>';
    }
} else {
    echo "<p>No products found.</p>";
}
?>
</div>

<!-- 📢 Call to Action -->
<div class="bg-light p-5 text-center mt-5 rounded-4 shadow-sm">
    <h4 class="fw-bold">Can't find what you're looking for?</h4>
    <p>Contact us or visit our store for more exclusive deals and bulk offers!</p>
    <a href="contact.php" class="btn btn-success btn-lg rounded-pill px-5">Contact Us</a>
</div>

<!-- 🔧 JS for filters & success message -->
<script>
    const searchBar = document.getElementById('searchBar');
    const categoryFilter = document.getElementById('categoryFilter');
    const productItems = document.querySelectorAll('.product-item');

    function filterProducts() {
        const searchQuery = searchBar.value.toLowerCase();
        const selectedCategory = categoryFilter.value;

        productItems.forEach(item => {
            const matchesSearch = item.getAttribute('data-name').includes(searchQuery);
            const matchesCategory = selectedCategory === "" || item.getAttribute('data-category') === selectedCategory;
            item.style.display = matchesSearch && matchesCategory ? 'block' : 'none';
        });
    }

    searchBar.addEventListener('input', filterProducts);
    categoryFilter.addEventListener('change', filterProducts);

    // 🕒 Auto-hide success message
    setTimeout(() => {
        const successMsg = document.getElementById('successMsg');
        if (successMsg) {
            successMsg.style.opacity = '0';
            setTimeout(() => successMsg.remove(), 500);
        }
    }, 3000);
</script>

<?php include 'includes/footer.php'; ?>
