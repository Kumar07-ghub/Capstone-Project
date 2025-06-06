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
    header('Location: ' . $_SERVER['PHP_SELF'] . (isset($_GET['query']) ? '?query=' . urlencode($_GET['query']) : ''));
    exit;
}

// Handle search query
$query = isset($_GET['query']) ? trim($_GET['query']) : '';
$searchCondition = $query ? "WHERE name LIKE '%" . $conn->real_escape_string($query) . "%'" : '';
$sql = "SELECT * FROM products $searchCondition";
$result = $conn->query($sql);
?>

<div class="container mt-4">
    <h2 class="mb-4 text-center fw-bold text-success">Shop Fresh Groceries</h2>

    <?php if (isset($_SESSION['success'])): ?>
        <div id="successMsg" class="alert alert-success text-center fw-semibold">
            <?= $_SESSION['success'];
            unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <!-- 🔍 Filter/Search Bar -->
    <div class="row mb-4 justify-content-center">
        <div class="col-md-5 mb-2">
            <form method="GET" action="products.php" class="input-group shadow-sm">
                <input type="text" name="query" value="<?= htmlspecialchars($query) ?>"
                    class="form-control form-control-lg" placeholder="🔍 Search for products...">
                <button class="btn btn-dark px-4" type="submit">Search</button>
            </form>
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

    <?php if ($query): ?>
        <div class="mb-3 text-center text-muted fst-italic">Showing results for:
            "<strong><?= htmlspecialchars($query) ?></strong>"</div>
    <?php endif; ?>

    <!-- 🛒 Product Grid -->
    <div class="row" id="productList">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '
                <div class="col-lg-4 col-md-6 mb-4 product-item" data-name="' . strtolower($row["name"]) . '" data-category="' . $row["category"] . '">
                    <div class="card h-100 shadow-lg border-0 product-card">
                        <a href="product_details.php?id=' . $row["id"] . '">
                           <img src="img/' . $row["image"] . '" class="img-fluid" alt="' . htmlspecialchars($row["name"]) . '" onerror="this.src=\'img/default.png\';">
                        </a>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-semibold">
                                <a href="product_details.php?id=' . $row["id"] . '" class="text-decoration-none text-dark">' . htmlspecialchars($row["name"]) . '</a>
                            </h5>
                            <p class="card-text text-muted mb-1"><strong>Price:</strong> $' . number_format($row["price"], 2) . '</p>
                            <p class="card-text text-muted mb-1"><strong>Unit:</strong> ' . htmlspecialchars($row["unit"]) . '</p>
                            <p class="card-text text-muted mb-1"><strong>In Stock:</strong> ' . intval($row["stock_quantity"]) . '</p>
                            <p class="card-text small text-muted mb-3">' . htmlspecialchars(substr($row["description"], 0, 80)) . '...</p>
                            <form method="POST">
                                <input type="hidden" name="product_id" value="' . intval($row["id"]) . '">
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
            echo "<p class='text-center text-muted'>No products found.</p>";
        }
        ?>
    </div>

    <!-- 📢 Call to Action -->
    <div class="bg-light p-5 text-center mt-5 rounded-4 shadow-sm">
        <h4 class="fw-bold">Can't find what you're looking for?</h4>
        <p>Contact us or visit our store for more exclusive deals and bulk offers!</p>
        <a href="contact.php" class="btn btn-success btn-lg rounded-pill px-5">Contact Us</a>
    </div>
</div>

<!-- 🔧 JS for filters & success message -->
<script>
    const searchBar = document.querySelector('input[name="query"]');
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

    setTimeout(() => {
        const successMsg = document.getElementById('successMsg');
        if (successMsg) {
            successMsg.style.opacity = '0';
            setTimeout(() => successMsg.remove(), 500);
        }
    }, 3000);
</script>

<?php include 'includes/footer.php'; ?>
