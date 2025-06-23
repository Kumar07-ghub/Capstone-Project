<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>The Indian Supermarket</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?= htmlspecialchars($meta_description ?? 'Shop fresh groceries online including fruits, vegetables, and dairy delivered to your door.') ?>">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">

    <style>
        .navbar {
            background: linear-gradient(to right, #2c3e50, #34495e);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        .navbar-brand img {
            height: 42px;
            margin-right: 10px;
        }
        .navbar-brand span {
            font-size: 1.1rem;
            font-weight: bold;
            color: #fff;
        }
        .nav-link {
            color: #fdfdfd !important;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        .nav-link:hover {
            color: #ffd700 !important;
        }
        .cart-btn {
            position: relative;
            padding: 0.5rem 0.75rem;
        }
        .cart-badge {
            position: absolute;
            top: -6px;
            right: -6px;
            font-size: 0.75rem;
            background-color: #fff;
            color: #e74c3c;
            border-radius: 50%;
            padding: 3px 6px;
            font-weight: bold;
            box-shadow: 0 0 5px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
    <div class="container px-4">
        <!-- Brand Logo & Name -->
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <img src="img/indian-supermarket-logo.svg" alt="Logo">
            <span>The Indian Supermarket</span>
        </a>

        <!-- Hamburger Menu -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown"
                aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar Links -->
        <div class="collapse navbar-collapse justify-content-end" id="navbarNavDropdown">
            <ul class="navbar-nav align-items-lg-center me-3">
                <li class="nav-item">
                    <a class="nav-link" href="index.php"><i class="bi bi-house-door me-1"></i>Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="products.php"><i class="bi bi-basket me-1"></i>Products</a>
                </li>
               <?php if (isset($_SESSION['user'])): ?>
    <!-- If user is logged in -->
    <li class="nav-item">
        <a class="nav-link" href="user_profile.php"><i class="bi bi-person-circle me-1"></i><?= htmlspecialchars($_SESSION['user']['name']) ?></a>
    </li>
    <li class="nav-item">
        <a href="logout.php" class="btn btn-outline-danger ms-lg-2">🚪 Logout</a>
    </li>
<?php else: ?>
    <!-- If user is not logged in -->
    <li class="nav-item">
        <a class="nav-link" href="login.php"><i class="bi bi-box-arrow-in-right me-1"></i>Login</a>
    </li>
<?php endif; ?>

                <li class="nav-item">
                    <a class="nav-link" href="user_profile.php"><i class="bi bi-person-circle me-1"></i>Profile</a>
                </li>
            </ul>

            <!-- Cart Button -->
            <a href="cart.php" class="btn btn-outline-light cart-btn">
                <i class="bi bi-cart fs-5"></i>
                <?php if ($cart_count > 0): ?>
                    <span class="cart-badge"><?= $cart_count ?></span>
                <?php endif; ?>
            </a>
        </div>
    </div>
</nav>
