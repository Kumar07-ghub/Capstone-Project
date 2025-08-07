<?php
session_start();
if ($_SESSION['user']['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}
require '../includes/db.php';

$product_id = $_GET['id'] ?? null;
if (!$product_id) {
    echo "Invalid product ID.";
    exit();
}

$result = $conn->query("SELECT * FROM products WHERE id = '$product_id'");
if (!$result || $result->num_rows === 0) {
    echo "Product not found.";
    exit();
}
$product = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $image = $_POST['image'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $unit = $_POST['unit'];
    $stock_quantity = $_POST['stock_quantity'];

    $update_query = "UPDATE products SET 
        name='$name', price='$price', image='$image', category='$category',
        description='$description', unit='$unit', stock_quantity='$stock_quantity'
        WHERE id='$product_id'";

    if ($conn->query($update_query)) {
        header("Location: admin_products.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include '../includes/header.php'; ?>

<div class="container mt-5">
    <h1 class="mb-4">Edit Product</h1>
    <form method="POST" class="needs-validation" novalidate>

        <div class="mb-3">
            <label for="name" class="form-label">Product Name *</label>
            <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>
            <div class="invalid-feedback">Product name is required.</div>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description *</label>
            <textarea class="form-control" id="description" name="description" rows="3" required><?= htmlspecialchars($product['description']) ?></textarea>
            <div class="invalid-feedback">Description is required.</div>
        </div>

        <div class="mb-3">
            <label for="price" class="form-label">Price ($) *</label>
            <input type="number" class="form-control" id="price" name="price" value="<?= htmlspecialchars($product['price']) ?>" min="0.01" step="0.01" required>
            <div class="invalid-feedback">Please enter a valid price.</div>
        </div>

        <div class="mb-3">
            <label for="stock_quantity" class="form-label">Quantity *</label>
            <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" value="<?= htmlspecialchars($product['stock_quantity']) ?>" min="1" required>
            <div class="invalid-feedback">Quantity must be at least 1.</div>
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">Image URL *</label>
            <input type="text" class="form-control" id="image" name="image" value="<?= $product['image'] ?>" required>
            <div class="invalid-feedback">A valid image URL is required.</div>
        </div>

        <div class="mb-3">
            <label for="category" class="form-label">Category *</label>
            <input type="text" class="form-control" id="category" name="category" value="<?= htmlspecialchars($product['category']) ?>" required>
            <div class="invalid-feedback">Category is required.</div>
        </div>

        <div class="mb-3">
            <label for="unit" class="form-label">Unit *</label>
            <input type="text" class="form-control" id="unit" name="unit" value="<?= htmlspecialchars($product['unit']) ?>" required>
            <div class="invalid-feedback">Unit is required.</div>
        </div>

        <button type="submit" class="btn btn-success">Update Product</button>
        <a href="admin_products.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<script>
    (() => {
        'use strict';
        const form = document.querySelector('.needs-validation');

        form.querySelectorAll('input, textarea').forEach(input => {
            input.addEventListener('input', () => {
                if (input.checkValidity()) {
                    input.classList.remove('is-invalid');
                    input.classList.add('is-valid');
                } else {
                    input.classList.remove('is-valid');
                    input.classList.add('is-invalid');
                }
            });
        });

        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    })();
</script>

<?php include '../includes/footer.php'; ?>
</body>
</html>
