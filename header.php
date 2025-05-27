<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Count number of unique products in the cart
$cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <header>
    <meta charset="UTF-8">
    <title>Grocery Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        .navbar-brand img {
            height: 40px;
            margin-right: 10px;
        }

        .header {

            background-color: 2C3E50;
            
        }

        .navbar {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .cart-badge {
            position: absolute;
            top: 0;
            right: 0;
            font-size: 0.75rem;
            background-color: white;
            color: #198754; /* Bootstrap green */
            border-radius: 50%;
            padding: 2px 7px;
            font-weight: bold;
            transform: translate(25%, -25%);
        }
    </style>
    </header>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-success sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <img src="img/indian-supermarket-logo.svg" alt="Logo">
            <span>The Indian Supermarket</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto me-3">
                <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="products.php">Products</a></li>
            </ul>
            <form class="d-flex align-items-center" role="search">
                <input class="form-control me-2" type="search" placeholder="Search products" aria-label="Search">
                <button class="btn btn-outline-light me-3" type="submit">Search</button>
                <a href="cart.php" class="btn btn-outline-light position-relative">
                    <i class="bi bi-cart"></i>
                    <?php if ($cart_count > 0): ?>
                        <span class="cart-badge"><?= $cart_count ?></span>
                    <?php endif; ?>
                </a>
            </form>
        </div>
    </div>
</nav>
<div class="container mt-4">
