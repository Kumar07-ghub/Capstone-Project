<?php
session_start();
include 'includes/header.php';
include 'includes/db.php';

// Validate product ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<div class='container mt-5'><h4 class='text-danger'>Invalid Product ID</h4></div>";
    include 'includes/footer.php';
    exit;
}

$product_id = intval($_GET['id']);
$sql = "SELECT * FROM products WHERE id = $product_id";
$result = $conn->query($sql);

$page_title = $product['name'] ?? 'Product Details';
$meta_description = substr(strip_tags($product['description'] ?? ''), 0, 160);
if ($result->num_rows === 0) {
    echo "<div class='container mt-5'><h4 class='text-danger'>Product Not Found</h4></div>";
    include 'includes/footer.php';
    exit;
}

$product = $result->fetch_assoc();
?>

<style>
    .product-image {
        width: 100%;
        height: auto;
        max-height: 450px;
        object-fit: cover;
        border-radius: 16px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }

    .product-details {
        padding: 20px 30px;
    }

    .product-details h2 {
        font-weight: 700;
    }

    .product-details p {
        font-size: 1.1rem;
        color: #555;
    }

    .price-highlight {
        font-size: 2rem;
        color: #28a745;
        font-weight: 700;
    }

    .stock-status {
        font-size: 1rem;
        color: #0d6efd;
    }

    .btn-back {
        margin-top: 20px;
        border-radius: 30px;
    }
</style>

<div class="container my-5">
    <div class="row align-items-center g-5">
        <!-- Left: Product Image -->
        <div class="col-md-6 text-center">
            <img src="img/<?= $product['image'] ?>" alt="<?= $product['name'] ?>" class="product-image">
        </div>

        <!-- Right: Product Info -->
        <div class="col-md-6 product-details">
            <h2><?= $product['name'] ?></h2>
            <p><strong>Category:</strong> <?= $product['category'] ?></p>
            <p><strong>Unit:</strong> <?= $product['unit'] ?></p>
            <p class="stock-status"><strong>Stock:</strong> <?= $product['stock_quantity'] ?> available</p>
            <p class="price-highlight">$<?= number_format($product['price'], 2) ?> CAD</p>
            <p class="mt-4"><?= nl2br($product['description']) ?></p>
            <?php if (!empty($product['long_description'])): ?>
                <hr>
                <h5>More About This Product</h5>
                <p><?= nl2br($product['long_description']) ?></p>
            <?php endif; ?>


            <!-- Add to Cart Form -->
            <form method="POST" action="products.php" class="mt-4">
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                <div class="input-group mb-3" style="max-width: 200px;">
                    <input type="number" name="quantity" value="1" min="1" max="<?= $product['stock_quantity'] ?>"
                        class="form-control" required>
                    <button type="submit" class="btn btn-outline-success">🛒 Add to Cart</button>
                </div>
            </form>

            <a href="products.php" class="btn btn-outline-secondary btn-back">← Back to Products</a>

        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>