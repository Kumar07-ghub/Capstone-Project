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
            border: none;
            border-radius: 1rem;
            padding: 2rem 1.5rem;
            transition: all 0.3s ease-in-out;
            background-color: #f8f9fa;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        }

        .dashboard-icon {
            font-size: 3.5rem;
            margin-bottom: 1rem;
        }

        .card-title {
            font-weight: 600;
        }

        .dashboard-section {
            padding: 4rem 1rem;
        }
    </style>
</head>
<body>

<?php include '../includes/header.php'; ?>

<div class="container dashboard-section">
    <div class="text-center mb-5">
        <h1 class="display-5 fw-bold">Admin Dashboard</h1>
        <p class="lead text-muted">Manage everything from one place — products, orders, and users.</p>
    </div>

    <div class="row g-4 justify-content-center">
        <!-- Manage Products -->
        <div class="col-md-4 col-sm-6">
            <a href="admin_products.php" class="text-decoration-none text-dark">
                <div class="dashboard-card text-center border-start border-4 border-primary">
                    <i class="bi bi-boxes dashboard-icon text-primary"></i>
                    <h5 class="card-title">Manage Products</h5>
                    <p class="card-text text-secondary">Add, edit or remove items from your inventory.</p>
                </div>
            </a>
        </div>

        <!-- Manage Orders -->
        <div class="col-md-4 col-sm-6">
            <a href="admin_orders.php" class="text-decoration-none text-dark">
                <div class="dashboard-card text-center border-start border-4 border-success">
                    <i class="bi bi-receipt-cutoff dashboard-icon text-success"></i>
                    <h5 class="card-title">Manage Orders</h5>
                    <p class="card-text text-secondary">Monitor, process, and fulfill customer orders.</p>
                </div>
            </a>
        </div>

        <!-- Manage Users -->
        <div class="col-md-4 col-sm-6">
            <a href="admin_users.php" class="text-decoration-none text-dark">
                <div class="dashboard-card text-center border-start border-4 border-warning">
                    <i class="bi bi-people-fill dashboard-icon text-warning"></i>
                    <h5 class="card-title">Manage Users</h5>
                    <p class="card-text text-secondary">Edit roles or remove users from the system.</p>
                </div>
            </a>
        </div>
    </div>

    <!-- Go to normal site -->
    <div class="text-center mt-5">
        <a href="../index.php" class="btn btn-outline-dark px-4 py-2">
            <i class="bi bi-arrow-left me-1"></i> Return to Store
        </a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>