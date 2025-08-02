<?php

session_start();

if ($_SESSION['user']['role'] !== 'admin') {

    header('Location: ../index.php');

    exit();

}

require '../includes/db.php';

$query = "SELECT * FROM products";

$result = $conn->query($query);

?>



<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>Manage Products</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

</head>

<body>



<?php include '../includes/header.php'; ?>



<div class="container mt-5">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h1 class="h3">Manage Products</h1>

        <a href="add_product.php" class="btn btn-success"><i class="bi bi-plus-circle me-1"></i> Add New Product</a>

    </div>



    <div class="table-responsive">

        <table class="table table-hover table-bordered align-middle">

            <thead class="table-dark">

                <tr>

                    <th>ID</th>

                    <th>Name</th>

                    <th>Category</th>

                    <th>Price (₹)</th>

                    <th>Stock</th>

                    <th class="text-center">Actions</th>

                </tr>

            </thead>

            <tbody>

                <?php while ($row = $result->fetch_assoc()) { ?>

                    <tr>

                        <td><?= $row['id'] ?></td>

                        <td><?= htmlspecialchars($row['name']) ?></td>

                        <td><?= htmlspecialchars($row['category']) ?></td>

                        <td><?= number_format($row['price'], 2) ?></td>

                        <td><?= $row['stock_quantity'] ?></td>

                        <td class="text-center">

                            <a href="edit_product.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm me-1"><i class="bi bi-pencil-square"></i> Edit</a>

                            <a href="delete_product.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this product?');"><i class="bi bi-trash"></i> Delete</a>

                        </td>

                    </tr>

                <?php } ?>

            </tbody>

        </table>

    </div>

</div>



<?php include '../includes/footer.php'; ?>

</body>

</html>

