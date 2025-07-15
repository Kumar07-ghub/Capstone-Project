<?php
session_start();
if ($_SESSION['user']['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}
require '../includes/db.php';

$product_id = $_GET['id'];
$result = $conn->query("SELECT * FROM products WHERE id = '$product_id'");
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
    <form method="POST">
        <div class="row g-3">
            <div class="col-md-6">
                <label for="name" class="form-label">Product Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>
            </div>
            <div class="col-md-6">
                <label for="price" class="form-label">Price (₹)</label>
                <input type="number" class="form-control" id="price" name="price" value="<?= $product['price'] ?>" step="0.01" required>
            </div>
            <div class="col-md-6">
                <label for="image" class="form-label">Image URL</label>
                <input type="text" class="form-control" id="image" name="image" value="<?= $product['image'] ?>" required>
            </div>
            <div class="col-md-6">
                <label for="category" class="form-label">Category</label>
                <input type="text" class="form-control" id="category" name="category" value="<?= $product['category'] ?>" required>
            </div>
            <div class="col-12">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3" required><?= htmlspecialchars($product['description']) ?></textarea>
            </div>
            <div class="col-md-6">
                <label for="unit" class="form-label">Unit (e.g. kg, liter)</label>
                <input type="text" class="form-control" id="unit" name="unit" value="<?= $product['unit'] ?>" required>
            </div>
            <div class="col-md-6">
                <label for="stock_quantity" class="form-label">Stock Quantity</label>
                <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" value="<?= $product['stock_quantity'] ?>" required>
            </div>
        </div>
        <div class="mt-4">
            <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Update Product</button>
            <a href="admin_products.php" class="btn btn-secondary ms-2">Cancel</a>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>
