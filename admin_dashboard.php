<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    $_SESSION['login_error'] = "You must be an admin to access this page.";
    header("Location: ../login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - The Indian Supermarket</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .dashboard-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .dashboard-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }
    </style>
</head>
<body>

<?php include '../includes/header.php'; ?>

<div class="container mt-5">
    <div class="text-center mb-5">
        <h1 class="display-5 fw-bold">Admin Dashboard</h1>
        <p class="lead">Manage everything from one place — products, orders, and users.</p>
    </div>

    <div class="row g-4 justify-content-center">
        <!-- Manage Products -->
        <div class="col-md-4">
            <a href="admin_products.php" class="text-decoration-none">
                <div class="card text-center dashboard-card border-primary">
                    <div class="card-body">
                        <i class="bi bi-boxes display-4 text-primary mb-3"></i>
                        <h5 class="card-title">Manage Products</h5>
                        <p class="card-text">View, edit, add or delete store products.</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Manage Orders -->
        <div class="col-md-4">
            <a href="admin_orders.php" class="text-decoration-none">
                <div class="card text-center dashboard-card border-success">
                    <div class="card-body">
                        <i class="bi bi-receipt-cutoff display-4 text-success mb-3"></i>
                        <h5 class="card-title">Manage Orders</h5>
                        <p class="card-text">Track and update customer orders.</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Manage Users -->
        <div class="col-md-4">
            <a href="admin_users.php" class="text-decoration-none">
                <div class="card text-center dashboard-card border-warning">
                    <div class="card-body">
                        <i class="bi bi-people-fill display-4 text-warning mb-3"></i>
                        <h5 class="card-title">Manage Users</h5>
                        <p class="card-text">Edit or remove user accounts and roles.</p>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Go to normal site -->
    <div class="text-center mt-5">
        <a href="../index.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Go to Normal Site
        </a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>
<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    $_SESSION['login_error'] = "You must be an admin to access this page.";
    header("Location: ../login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - The Indian Supermarket</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .dashboard-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .dashboard-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }
    </style>
</head>
<body>

<?php include '../includes/header.php'; ?>

<div class="container mt-5">
    <div class="text-center mb-5">
        <h1 class="display-5 fw-bold">Admin Dashboard</h1>
        <p class="lead">Manage everything from one place — products, orders, and users.</p>
    </div>

    <div class="row g-4 justify-content-center">
        <!-- Manage Products -->
        <div class="col-md-4">
            <a href="admin_products.php" class="text-decoration-none">
                <div class="card text-center dashboard-card border-primary">
                    <div class="card-body">
                        <i class="bi bi-boxes display-4 text-primary mb-3"></i>
                        <h5 class="card-title">Manage Products</h5>
                        <p class="card-text">View, edit, add or delete store products.</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Manage Orders -->
        <div class="col-md-4">
            <a href="admin_orders.php" class="text-decoration-none">
                <div class="card text-center dashboard-card border-success">
                    <div class="card-body">
                        <i class="bi bi-receipt-cutoff display-4 text-success mb-3"></i>
                        <h5 class="card-title">Manage Orders</h5>
                        <p class="card-text">Track and update customer orders.</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Manage Users -->
        <div class="col-md-4">
            <a href="admin_users.php" class="text-decoration-none">
                <div class="card text-center dashboard-card border-warning">
                    <div class="card-body">
                        <i class="bi bi-people-fill display-4 text-warning mb-3"></i>
                        <h5 class="card-title">Manage Users</h5>
                        <p class="card-text">Edit or remove user accounts and roles.</p>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Go to normal site -->
    <div class="text-center mt-5">
        <a href="../index.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Go to Normal Site
        </a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>
