<?php
// Ensure session_start() is at the very beginning of the file, without any whitespace before it
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set up cart count and check if user is logged in
$cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
$current_page = basename($_SERVER['PHP_SELF']);  // Get the current page name (e.g., products.php)
$is_admin_area = strpos($_SERVER['PHP_SELF'], '/admin/') !== false;  // Check if it's the admin area
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>The Indian Supermarket</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="<?= htmlspecialchars($meta_description ?? 'Shop fresh groceries online including fruits, vegetables, and dairy delivered to your door.') ?>" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />

    <!-- Custom styles -->
    <link rel="stylesheet" href="<?= $is_admin_area ? '../css/style.css' : 'css/style.css' ?>" />

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
            font-weight: 700;
            color: #fff;
        }
        .nav-link {
            color: #fdfdfd !important;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        .nav-link:hover, .nav-link:focus {
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
            font-weight: 700;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
    <div class="container px-4">
        <!-- Brand / Logo -->
        <a class="navbar-brand d-flex align-items-center" href="<?= $is_admin_area ? '../index.php' : 'index.php' ?>">
            <img src="<?= $is_admin_area ? '../img/indian-supermarket-logo.svg' : 'img/indian-supermarket-logo.svg' ?>" alt="The Indian Supermarket Logo" />
            <span>The Indian Supermarket</span>
        </a>

        <!-- Hamburger toggle for mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown"
                aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navigation Links -->
        <div class="collapse navbar-collapse justify-content-end" id="navbarNavDropdown">
            <ul class="navbar-nav align-items-lg-center me-3">
                <?php if (!$is_admin_area): ?>
                    <!-- Public site navigation -->
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page === 'index.php' ? 'active' : '' ?>" href="index.php">
                            <i class="bi bi-house-door me-1"></i>Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page === 'products.php' ? 'active' : '' ?>" href="products.php">
                            <i class="bi bi-basket me-1"></i>Products
                        </a>
                    </li>
                    <li class="nav-item">
                            <a class="nav-link <?= $current_page === 'contact.php' ? 'active' : '' ?>" href="contact.php">
                                <i class="bi bi-telephone me-2"></i>Contact
                            </a>
                        </li>
                <?php endif; ?>

                <?php if (isset($_SESSION['user'])): ?>
                    <?php if (!$is_admin_area): ?>
                        <!-- User profile link for normal users -->
                        <li class="nav-item">
                            <a class="nav-link" href="user_profile.php" title="Profile">
                                <i class="bi bi-person-circle me-1"></i><?= htmlspecialchars(trim(($_SESSION['user']['first_name'] ?? '') . ' ' . ($_SESSION['user']['last_name'] ?? ''))) ?>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin'): ?>

                        <?php if ($is_admin_area): ?>
                            <!-- Admin area navigation -->
                            <li class="nav-item">
                                <a class="nav-link" href="admin_dashboard.php"><i class="bi bi-ui-checks-grid me-1"></i>Dashboard</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="admin_users.php"><i class="bi bi-person-lines-fill me-1"></i>Manage Users</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="admin_orders.php"><i class="bi bi-file-earmark-ruled me-1"></i>Manage Orders</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="admin_products.php"><i class="bi bi-boxes me-1"></i>Manage Products</a>
                            </li>
                            <li class="nav-item">
                                <a href="../index.php" class="btn btn-secondary ms-2">Go to Normal Site</a>
                            </li>
                        <?php else: ?>
                            <!-- Quick link to admin dashboard for normal site admin -->
                            <li class="nav-item">
                                <a href="admin/admin_dashboard.php" class="btn btn-warning ms-2">Go to Admin Dashboard</a>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>

                    <!-- Logout button -->
                    <li class="nav-item">
                        <a href="<?= $is_admin_area ? '../logout.php' : 'logout.php' ?>" class="btn btn-outline-danger ms-lg-2" title="Logout">
                            <i class="bi bi-box-arrow-right me-1"></i>Logout
                        </a>
                    </li>
                <?php else: ?>
                    <!-- Guest users see login -->
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page === 'login.php' ? 'active' : '' ?>" href="login.php">
                            <i class="bi bi-box-arrow-in-right me-1"></i>Login
                        </a>
                    </li>
                <?php endif; ?>
            </ul>

            <!-- Cart Button: Only on non-admin pages -->
            <?php if (!$is_admin_area): ?>
                <a href="cart.php" class="btn btn-outline-light cart-btn position-relative" aria-label="View Cart">
                    <i class="bi bi-cart fs-5"></i>
                    <?php if ($cart_count > 0): ?>
                        <span class="cart-badge" aria-live="polite" aria-atomic="true"><?= $cart_count ?></span>
                    <?php endif; ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<!-- Bootstrap JS Bundle with Popper (for navbar toggle) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
