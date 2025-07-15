<?php
session_start();
include 'includes/header.php';
include 'includes/db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<div class='container mt-5'><h4 class='text-danger'>Invalid Product ID</h4></div>";
    include 'includes/footer.php';
    exit;
}

$product_id = intval($_GET['id']);
$result = $conn->query("SELECT * FROM products WHERE id = $product_id");

if ($result->num_rows === 0) {
    echo "<div class='container mt-5'><h4 class='text-danger'>Product Not Found</h4></div>";
    include 'includes/footer.php';
    exit;
}

$product = $result->fetch_assoc();

// Rating summary
$ratingSql = "SELECT AVG(rating) AS avg_rating, COUNT(*) AS total_ratings FROM product_ratings WHERE product_id = $product_id";
$ratingResult = $conn->query($ratingSql);
$ratingData = $ratingResult->fetch_assoc();
$avgRating = round($ratingData['avg_rating'] ?? 0, 1);
$totalRatings = $ratingData['total_ratings'] ?? 0;
?>

<style>
    .product-image { width: 100%; max-height: 450px; object-fit: cover; border-radius: 16px; }
    .product-details { padding: 20px 30px; }
    .price-highlight { font-size: 2rem; color: #28a745; font-weight: 700; }
    .stock-status { color: #0d6efd; }
    .star-rating span { font-size: 1.8rem; color: orange; }
</style>

<div class="container my-5">
    <div class="row align-items-center g-5">
        <div class="col-md-6 text-center">
            <img src="img/<?= htmlspecialchars($product['image']) ?>" 
                 alt="<?= htmlspecialchars($product['name']) ?>" 
                 class="product-image" 
                 onerror="this.src='img/default.png';">
        </div>
        <div class="col-md-6 product-details">
            <h2><?= htmlspecialchars($product['name']) ?></h2>
            <div class="star-rating mb-2">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <span><?= $i <= round($avgRating) ? '★' : '☆' ?></span>
                <?php endfor; ?>
            </div>
            <small class="text-muted">
                <?= $totalRatings > 0 ? "($avgRating / 5 from $totalRatings ratings)" : "No ratings yet" ?>
            </small>

            <p><strong>Category:</strong> <?= htmlspecialchars($product['category']) ?></p>
            <p><strong>Unit:</strong> <?= htmlspecialchars($product['unit']) ?></p>
            <p class="stock-status"><strong>Stock:</strong> <?= intval($product['stock_quantity']) ?> available</p>
            <p class="price-highlight">$<?= number_format($product['price'], 2) ?> CAD</p>
            <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>

            <!-- Add to Cart -->
            <form method="POST" action="products.php" class="mt-4">
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                <div class="input-group mb-3" style="max-width: 200px;">
                    <input type="number" name="quantity" value="1" min="1" max="<?= intval($product['stock_quantity']) ?>" class="form-control" required>
                    <button type="submit" class="btn btn-outline-success">🛒 Add to Cart</button>
                </div>
            </form>

            <!-- Rate this product -->
            <?php if (isset($_SESSION['user'])): ?>
                <hr class="my-4">
                <h5>Rate this product</h5>
                <form method="POST" action="rate_product.php" style="max-width: 300px;">
                    <input type="hidden" name="product_id" value="<?= $product_id ?>">
                    <select name="rating" class="form-select mb-2" required>
                        <option value="">-- Choose a Rating --</option>
                        <option value="5">⭐⭐⭐⭐⭐ Excellent</option>
                        <option value="4">⭐⭐⭐⭐ Good</option>
                        <option value="3">⭐⭐⭐ Average</option>
                        <option value="2">⭐⭐ Poor</option>
                        <option value="1">⭐ Terrible</option>
                    </select>
                    <textarea name="review" class="form-control mb-2" rows="2" placeholder="Optional review..."></textarea>
                    <button type="submit" class="btn btn-primary">Submit Rating</button>
                </form>
            <?php else: ?>
                <p class="mt-4"><a href="login.php">Login</a> to rate this product.</p>
            <?php endif; ?>

            <a href="products.php" class="btn btn-outline-secondary mt-4">← Back to Products</a>
        </div>
    </div>

    <!-- Review Section -->
    <div class="mt-5">
        <h4>Customer Reviews</h4>
        <?php
        $reviewStmt = $conn->prepare("SELECT r.rating, r.review, r.created_at, u.name 
                                      FROM product_ratings r 
                                      JOIN users u ON r.user_id = u.id 
                                      WHERE r.product_id = ? 
                                      ORDER BY r.created_at DESC");
        $reviewStmt->bind_param("i", $product_id);
        $reviewStmt->execute();
        $reviews = $reviewStmt->get_result();

        if ($reviews->num_rows > 0):
            while ($review = $reviews->fetch_assoc()):
        ?>
                <div class="border rounded p-3 mb-3">
                    <strong><?= htmlspecialchars($review['name']) ?></strong>
                    <span class="text-warning"><?= str_repeat('★', $review['rating']) . str_repeat('☆', 5 - $review['rating']) ?></span>
                    <br><small class="text-muted"><?= date("F j, Y", strtotime($review['created_at'])) ?></small>
                    <?php if (!empty($review['review'])): ?>
                        <p class="mt-2"><?= nl2br(htmlspecialchars($review['review'])) ?></p>
                    <?php endif; ?>
                </div>
        <?php
            endwhile;
        else:
            echo "<p>No reviews yet.</p>";
        endif;
        ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
