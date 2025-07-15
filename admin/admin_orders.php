<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$res = $conn->query("SELECT * FROM orders ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Manage Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
</head>
<body>

<?php include '../includes/header.php'; ?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Manage Orders</h1>
    </div>

    <div class="table-responsive">
        <table class="table table-hover table-bordered align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Total (₹)</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($o = $res->fetch_assoc()): ?>
                <tr>
                    <td><?= $o['id'] ?></td>
                    <td><?= htmlspecialchars($o['name']) ?></td>
                    <td><?= htmlspecialchars($o['email']) ?></td>
                    <td><?= number_format($o['total'], 2) ?></td>
                    <td>
                        <span class="badge 
                            <?= 
                                $o['status'] === 'Pending' ? 'bg-warning text-dark' : 
                                ($o['status'] === 'Shipped' ? 'bg-info text-dark' : 
                                ($o['status'] === 'Delivered' ? 'bg-success' : 
                                ($o['status'] === 'Cancelled' ? 'bg-danger' : 'bg-secondary')))
                            ?>">
                            <?= htmlspecialchars($o['status']) ?>
                        </span>
                    </td>
                    <td><?= $o['created_at'] ?></td>
                    <td class="text-center">
                        <a href="edit_order.php?id=<?= $o['id'] ?>" class="btn btn-sm btn-warning me-1" title="Edit Order">
                            <i class="bi bi-pencil-square"></i>
                        </a>
                        <a href="delete_order.php?id=<?= $o['id'] ?>" class="btn btn-sm btn-danger" title="Delete Order"
                           onclick="return confirm('Are you sure you want to delete this order?');">
                            <i class="bi bi-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
                <?php if ($res->num_rows === 0): ?>
                <tr>
                    <td colspan="7" class="text-center">No orders found.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

</body>
</html>
