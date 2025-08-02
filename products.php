<<<<<<< HEAD
<?php
session_start();
include 'includes/db.php';
// Initialize cart if not already set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle Add to Cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $id = $_POST['product_id'];
    $qty = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + $qty;
    $_SESSION['success'] = "✅ Product added to cart successfully!";
    header('Location: ' . $_SERVER['PHP_SELF'] . (isset($_GET['query']) ? '?query=' . urlencode($_GET['query']) : ''));
    exit;
}

// Search Query Handling
$query = isset($_GET['query']) ? trim($_GET['query']) : '';
$searchCondition = $query ? "WHERE name LIKE '%" . $conn->real_escape_string($query) . "%'" : '';
$sql = "SELECT * FROM products $searchCondition";
$result = $conn->query($sql);

// Include header (this is after the header-related logic)
include 'includes/header.php';
?>

<div class="container mt-4">
    <h2 class="mb-4 text-center fw-bold text-success">Shop Fresh Groceries</h2>

    <!-- Display success message if added to cart -->
    <?php if (isset($_SESSION['success'])): ?>
        <div id="successMsg" class="alert alert-success text-center fw-semibold">
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <div class="row mb-4 justify-content-center">
        <!-- Search Bar -->
        <div class="col-md-5 mb-2">
            <form method="GET" action="products.php" class="input-group shadow-sm">
                <input type="text" name="query" value="<?= htmlspecialchars($query) ?>" class="form-control form-control-lg" placeholder="🔍 Search for products...">
                <button class="btn btn-dark px-4" type="submit">Search</button>
            </form>
        </div>
        <!-- Category Filter Dropdown -->
        <div class="col-md-3 mb-2">
            <select id="categoryFilter" class="form-select form-select-lg shadow-sm">
                <option value="">All Categories</option>
                <option value="Fruits">Fruits</option>
                <option value="Vegetables">Vegetables</option>
                <option value="Dairy">Dairy</option>
            </select>
        </div>
    </div>

    <!-- Display search query results -->
    <?php if ($query): ?>
        <div class="mb-3 text-center text-muted fst-italic">Showing results for: "<strong><?= htmlspecialchars($query) ?></strong>"</div>
    <?php endif; ?>

    <div class="row" id="productList">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $productId = intval($row['id']);
                $ratingSql = "SELECT AVG(rating) AS avg_rating, COUNT(*) AS rating_count FROM product_ratings WHERE product_id = $productId";
                $ratingResult = $conn->query($ratingSql);
                $ratingData = $ratingResult->fetch_assoc();
                $avgRating = round($ratingData['avg_rating'] ?? 0, 1);
                $ratingCount = $ratingData['rating_count'] ?? 0;

                echo '
                <div class="col-lg-4 col-md-6 mb-4 product-item" data-name="' . strtolower($row["name"]) . '" data-category="' . $row["category"] . '">
                    <div class="card h-100 shadow-lg border-0 product-card">
                        <a href="product_details.php?id=' . $row["id"] . '">
                            <img src="img/' . $row["image"] . '" class="img-fluid" alt="' . htmlspecialchars($row["name"]) . '" onerror="this.src=\'img/default.png\';">
                        </a>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-semibold">
                                <a href="product_details.php?id=' . $row["id"] . '" class="text-decoration-none text-dark">' . htmlspecialchars($row["name"]) . '</a>
                            </h5>';

                // Star rating display
                echo '<div class="star-rating mb-1">';
                for ($i = 1; $i <= 5; $i++) {
                    echo '<span>' . ($i <= round($avgRating) ? '★' : '☆') . '</span>';
                }
                echo '</div>';

                if ($ratingCount > 0) {
                    echo '<small class="text-muted">(' . $avgRating . ' / 5 from ' . $ratingCount . ' ratings)</small>';
                } else {
                    echo '<small class="text-muted">No ratings yet</small>';
                }

                echo '<p class="card-text text-muted mb-1"><strong>Price:</strong> ₹' . number_format($row["price"], 2) . '</p>
                            <p class="card-text text-muted mb-1"><strong>Unit:</strong> ' . htmlspecialchars($row["unit"]) . '</p>
                            <p class="card-text text-muted mb-1"><strong>In Stock:</strong> ' . intval($row["stock_quantity"]) . '</p>
                            <p class="card-text small text-muted mb-3">' . htmlspecialchars(substr($row["description"], 0, 80)) . '...</p>
                            <form method="POST">
                                <input type="hidden" name="product_id" value="' . intval($row["id"]) . '">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="btn btn-outline-primary mt-auto rounded-pill w-100">🛒 Add to Cart</button>
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
</div>

<style>
.star-rating span {
    font-size: 1.8rem;
    color: orange;
    margin-right: 2px;
}
</style>

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

    // Auto fade out success message
    setTimeout(() => {
        const successMsg = document.getElementById('successMsg');
        if (successMsg) {
            successMsg.style.opacity = '0';
            setTimeout(() => successMsg.remove(), 500);
        }
    }, 3000);
</script>

<?php include 'includes/footer.php'; ?>
=======
<?php
session_start();
include 'includes/db.php';

// Initialize cart if not already set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle Add to Cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $id = $_POST['product_id'];
    $qty = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + $qty;
    $_SESSION['success'] = "✅ Product added to cart successfully!";
    header('Location: ' . $_SERVER['PHP_SELF'] . (isset($_GET['query']) ? '?query=' . urlencode($_GET['query']) : ''));
    exit;
}

// Search Query Handling
$query = isset($_GET['query']) ? trim($_GET['query']) : '';
$searchCondition = $query ? "WHERE name LIKE '%" . $conn->real_escape_string($query) . "%'" : '';
$sql = "SELECT * FROM products $searchCondition";
$result = $conn->query($sql);

// Include header (this is after the header-related logic)
include 'includes/header.php';
?>

<div class="container mt-4">
    <h2 class="mb-4 text-center fw-bold text-success">Shop Fresh Groceries</h2>

    <!-- Display success message if added to cart -->
    <?php if (isset($_SESSION['success'])): ?>
        <div id="successMsg" class="alert alert-success text-center fw-semibold">
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <div class="row mb-4 justify-content-center">
        <!-- Search Bar -->
        <div class="col-md-5 mb-2">
            <form method="GET" action="products.php" class="input-group shadow-sm">
                <input type="text" name="query" value="<?= htmlspecialchars($query) ?>" class="form-control form-control-lg" placeholder="🔍 Search for products...">
                <button class="btn btn-dark px-4" type="submit">Search</button>
            </form>
        </div>
        <!-- Category Filter Dropdown -->
        <div class="col-md-3 mb-2">
            <select id="categoryFilter" class="form-select form-select-lg shadow-sm">
                <option value="">All Categories</option>
                <option value="Fruits">Fruits</option>
                <option value="Vegetables">Vegetables</option>
                <option value="Dairy">Dairy</option>
            </select>
        </div>
    </div>

    <!-- Display search query results -->
    <?php if ($query): ?>
        <div class="mb-3 text-center text-muted fst-italic">Showing results for: "<strong><?= htmlspecialchars($query) ?></strong>"</div>
    <?php endif; ?>

    <div class="row" id="productList">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $productId = intval($row['id']);
                $ratingSql = "SELECT AVG(rating) AS avg_rating, COUNT(*) AS rating_count FROM product_ratings WHERE product_id = $productId";
                $ratingResult = $conn->query($ratingSql);
                $ratingData = $ratingResult->fetch_assoc();
                $avgRating = round($ratingData['avg_rating'] ?? 0, 1);
                $ratingCount = $ratingData['rating_count'] ?? 0;

                echo '
                <div class="col-lg-4 col-md-6 mb-4 product-item" data-name="' . strtolower($row["name"]) . '" data-category="' . $row["category"] . '">
                    <div class="card h-100 shadow-lg border-0 product-card">
                        <a href="product_details.php?id=' . $row["id"] . '">
                            <img src="img/' . $row["image"] . '" class="img-fluid" alt="' . htmlspecialchars($row["name"]) . '" onerror="this.src=\'img/default.png\';">
                        </a>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-semibold">
                                <a href="product_details.php?id=' . $row["id"] . '" class="text-decoration-none text-dark">' . htmlspecialchars($row["name"]) . '</a>
                            </h5>';

                // Star rating display
                echo '<div class="star-rating mb-1">';
                for ($i = 1; $i <= 5; $i++) {
                    echo '<span>' . ($i <= round($avgRating) ? '★' : '☆') . '</span>';
                }
                echo '</div>';

                if ($ratingCount > 0) {
                    echo '<small class="text-muted">(' . $avgRating . ' / 5 from ' . $ratingCount . ' ratings)</small>';
                } else {
                    echo '<small class="text-muted">No ratings yet</small>';
                }

                echo '<p class="card-text text-muted mb-1"><strong>Price:</strong> ₹' . number_format($row["price"], 2) . '</p>
                            <p class="card-text text-muted mb-1"><strong>Unit:</strong> ' . htmlspecialchars($row["unit"]) . '</p>
                            <p class="card-text text-muted mb-1"><strong>In Stock:</strong> ' . intval($row["stock_quantity"]) . '</p>
                            <p class="card-text small text-muted mb-3">' . htmlspecialchars(substr($row["description"], 0, 80)) . '...</p>
                            <form method="POST">
                                <input type="hidden" name="product_id" value="' . intval($row["id"]) . '">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="btn btn-outline-primary mt-auto rounded-pill w-100">🛒 Add to Cart</button>
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
</div>

<style>
.star-rating span {
    font-size: 1.8rem;
    color: orange;
    margin-right: 2px;
}
</style>

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

    // Auto fade out success message
    setTimeout(() => {
        const successMsg = document.getElementById('successMsg');
        if (successMsg) {
            successMsg.style.opacity = '0';
            setTimeout(() => successMsg.remove(), 500);
        }
    }, 3000);
</script>

<?php include 'includes/footer.php'; ?>
>>>>>>> 07d8e6ccfd579de17e14e08d58f8a836c66fbf26
