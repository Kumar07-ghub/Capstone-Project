<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Variables
$cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
$current_page = basename($_SERVER['PHP_SELF']);
$is_admin_area = strpos($_SERVER['PHP_SELF'], '/admin/') !== false;
$logout_url = $is_admin_area ? '../logout.php' : 'logout.php';
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
    <!-- AOS Animation Library -->
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <!-- Custom styles -->
    <link rel="stylesheet" href="<?= $is_admin_area ? '../css/style.css' : 'css/style.css' ?>" />

    <style>
        .navbar {
            background: linear-gradient(to right, #0f2027, #203a43, #2c5364);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }
        .navbar-brand img { height: 50px; margin-right: 10px; transition: all 0.3s ease; }
        .navbar-brand span { font-size: 1rem; font-weight: 700; color: #fff; }
        .nav-link {
            color: #ffffff !important;
            font-weight: 500;
            margin: 0 0.6rem;
            transition: color 0.3s ease, font-size 0.3s ease;
        }
        .nav-link:hover, .nav-link:focus { color: #ffd700 !important; }
        .nav-link.active {
            color: #ffd700 !important;
            text-shadow: 0 0 4px rgba(255, 215, 0, 0.4);
        }
        .cart-btn { position: relative; }
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
        nav.navbar.shrink {
            padding-top: 6px !important;
            padding-bottom: 6px !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
        nav.navbar.shrink .navbar-brand img { height: 30px; }
        nav.navbar.shrink .nav-link { font-size: 0.9rem; }
        .btn-outline-light, .btn-outline-danger {
            font-size: 0.9rem;
            padding: 0.45rem 0.9rem;
        }
        @media (max-width: 991px) {
            .navbar .btn { margin-top: 0.5rem; }
        }
    </style>
</head>
<body>

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
    <div class="container px-4">
        <!-- Logo -->
        <a class="navbar-brand d-flex align-items-center" href="<?= $is_admin_area ? '../index.php' : 'index.php' ?>" aria-label="Go to homepage">
            <img src="<?= $is_admin_area ? '../img/indian-supermarket-logo.svg' : 'img/indian-supermarket-logo.svg' ?>" alt="The Indian Supermarket logo" height="50" />
            <span>The Indian Supermarket</span>
        </a>

        <!-- Toggler for mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown"
                aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navigation Items -->
        <div class="collapse navbar-collapse justify-content-end" id="navbarNavDropdown">
            <ul class="navbar-nav align-items-lg-center me-3">

                <?php if (!$is_admin_area): ?>
                    <li class="nav-item"><a class="nav-link <?= $current_page === 'index.php' ? 'active' : '' ?>" href="index.php" aria-label="Home"><span class="bi bi-house-door me-2" aria-hidden="true"></span>Home</a></li>
                    <li class="nav-item"><a class="nav-link <?= $current_page === 'products.php' ? 'active' : '' ?>" href="products.php" aria-label="Products"><span class="bi bi-basket me-2" aria-hidden="true"></span>Products</a></li>
                    <li class="nav-item"><a class="nav-link <?= $current_page === 'contact.php' ? 'active' : '' ?>" href="contact.php" aria-label="Contact us"><span class="bi bi-telephone me-2" aria-hidden="true"></span>Contact</a></li>
                <?php endif; ?>

                <?php if (isset($_SESSION['user'])): ?>
                    <?php if (!$is_admin_area): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= $current_page === 'user_profile.php' ? 'active' : '' ?>" href="user_profile.php" aria-label="Your profile">
                                <span class="bi bi-person-circle me-2" aria-hidden="true"></span><?= htmlspecialchars(trim(($_SESSION['user']['first_name'] ?? '') . ' ' . ($_SESSION['user']['last_name'] ?? ''))) ?>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                        <?php if ($is_admin_area): ?>
                            <li class="nav-item"><a class="nav-link <?= $current_page === 'admin_dashboard.php' ? 'active' : '' ?>" href="admin_dashboard.php" aria-label="Admin dashboard"><span class="bi bi-speedometer2 me-2" aria-hidden="true"></span>Dashboard</a></li>
                            <li class="nav-item"><a class="nav-link <?= $current_page === 'admin_users.php' ? 'active' : '' ?>" href="admin_users.php" aria-label="Admin users"><span class="bi bi-people me-2" aria-hidden="true"></span>Users</a></li>
                            <li class="nav-item"><a class="nav-link <?= $current_page === 'admin_orders.php' ? 'active' : '' ?>" href="admin_orders.php" aria-label="Admin orders"><span class="bi bi-card-checklist me-2" aria-hidden="true"></span>Orders</a></li>
                            <li class="nav-item"><a class="nav-link <?= $current_page === 'admin_products.php' ? 'active' : '' ?>" href="admin_products.php" aria-label="Admin products"><span class="bi bi-box-seam me-2" aria-hidden="true"></span>Products</a></li>
                            <li class="nav-item ms-lg-3"><a href="../index.php" class="btn btn-outline-light"><span class="bi bi-box-arrow-left me-1" aria-hidden="true"></span>Go to Store</a></li>
                        <?php else: ?>
                            <li class="nav-item ms-lg-2"><a href="admin/admin_dashboard.php" class="btn btn-warning" aria-label="Go to Admin Panel"><span class="bi bi-speedometer2 me-1" aria-hidden="true"></span>Admin Panel</a></li>
                        <?php endif; ?>
                    <?php endif; ?>

                    <!-- ✅ Logout for all users -->
                    <li class="nav-item ms-2">
                        <a href="<?= $logout_url ?>" class="btn btn-outline-danger" aria-label="Logout">
                            <span class="bi bi-box-arrow-right me-1" aria-hidden="true"></span>Logout
                        </a>
                    </li>

                <?php else: ?>
                    <!-- Guest -->
                    <li class="nav-item"><a class="nav-link <?= $current_page === 'login.php' ? 'active' : '' ?>" href="login.php" aria-label="Login"><span class="bi bi-box-arrow-in-right me-2" aria-hidden="true"></span>Login</a></li>
                <?php endif; ?>
            </ul>

            <!-- Cart Icon -->
            <?php if (!$is_admin_area): ?>
                <a href="cart.php" class="btn btn-outline-light cart-btn" aria-label="View Cart">
                    <span class="bi bi-cart fs-5" aria-hidden="true"></span>
                    <?php if ($cart_count > 0): ?>
                        <span class="cart-badge"><?= $cart_count ?></span>
                    <?php endif; ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
</nav>
