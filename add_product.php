<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

require '../includes/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $category = trim($_POST['category'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $unit = trim($_POST['unit'] ?? '');
    $stock_quantity = intval($_POST['stock_quantity'] ?? 0);

    $imagePath = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = basename($_FILES['image']['name']);
        $fileType = mime_content_type($fileTmpPath);

        if (in_array($fileType, $allowedTypes)) {
            $uploadsDir = '../uploads/products/';
            if (!is_dir($uploadsDir)) mkdir($uploadsDir, 0755, true);
            $newFileName = uniqid('prod_', true) . '.' . pathinfo($fileName, PATHINFO_EXTENSION);
            $destination = $uploadsDir . $newFileName;
            if (move_uploaded_file($fileTmpPath, $destination)) {
                $imagePath = 'uploads/products/' . $newFileName;
            } else {
                $error = 'Failed to move uploaded file.';
            }
        } else {
            $error = 'Invalid image type. Allowed: jpg, png, gif.';
        }
    } else {
        $error = 'Please upload a product image.';
    }

    if (!$error) {
        $stmt = $conn->prepare("INSERT INTO products (name, price, image, category, description, unit, stock_quantity) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('sdssssi', $name, $price, $imagePath, $category, $description, $unit, $stock_quantity);

        if ($stmt->execute()) {
            header('Location: admin_products.php');
            exit();
        } else {
            $error = 'Database error: ' . $stmt->error;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Add Product - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background-color: #f8f9fa;
        }
        .page-header {
            margin: 3rem 0 2rem;
            text-align: center;
        }
        .card {
            max-width: 900px;
            margin: auto;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .img-preview {
            max-height: 180px;
            object-fit: contain;
            margin-top: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 0.25rem;
        }
        @media (min-width: 768px) {
            .form-row {
                display: flex;
                gap: 1.5rem;
            }
            .form-col {
                flex: 1;
            }
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container">
        <h1 class="page-header">Add New Product</h1>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="card p-4 mb-5 bg-white rounded">
            <form method="POST" enctype="multipart/form-data" novalidate>
                <div class="form-row mb-3">
                    <div class="form-col">
                        <label for="name" class="form-label fw-semibold">Product Name</label>
                        <input type="text" id="name" name="name" class="form-control" required
                               value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '' ?>" placeholder="E.g., Mango" />
                    </div>
                    <div class="form-col">
                        <label for="price" class="form-label fw-semibold">Price (₹)</label>
                        <input type="number" id="price" name="price" class="form-control" required step="0.01" min="0"
                               value="<?= isset($_POST['price']) ? htmlspecialchars($_POST['price']) : '' ?>" placeholder="E.g., 50.00" />
                    </div>
                </div>

                <div class="mb-3">
                    <label for="image" class="form-label fw-semibold">Product Image</label>
                    <input type="file" id="image" name="image" class="form-control" accept="image/png, image/jpeg, image/gif" required />
                    <img id="imagePreview" src="#" alt="Image Preview" class="img-preview d-none" />
                </div>

                <div class="form-row mb-3">
                    <div class="form-col">
                        <label for="category" class="form-label fw-semibold">Category</label>
                        <input type="text" id="category" name="category" class="form-control" required
                               value="<?= isset($_POST['category']) ? htmlspecialchars($_POST['category']) : '' ?>" placeholder="E.g., Fruits" />
                    </div>
                    <div class="form-col">
                        <label for="unit" class="form-label fw-semibold">Unit</label>
                        <input type="text" id="unit" name="unit" class="form-control" required
                               value="<?= isset($_POST['unit']) ? htmlspecialchars($_POST['unit']) : '' ?>" placeholder="E.g., kg, litre" />
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label fw-semibold">Description</label>
                    <textarea id="description" name="description" rows="3" class="form-control" required
                              placeholder="Write a short description"><?= isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '' ?></textarea>
                </div>

                <div class="mb-4">
                    <label for="stock_quantity" class="form-label fw-semibold">Stock Quantity</label>
                    <input type="number" id="stock_quantity" name="stock_quantity" class="form-control" required min="0"
                           value="<?= isset($_POST['stock_quantity']) ? htmlspecialchars($_POST['stock_quantity']) : '' ?>" placeholder="E.g., 100" />
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="admin_products.php" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Add Product</button>
                </div>
            </form>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Image preview
        document.getElementById('image').addEventListener('change', function(event) {
            const preview = document.getElementById('imagePreview');
            const file = event.target.files[0];

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('d-none');
                }
                reader.readAsDataURL(file);
            } else {
                preview.src = '#';
                preview.classList.add('d-none');
            }
        });
    </script>
</body>
</html>
