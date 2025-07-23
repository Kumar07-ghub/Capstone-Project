<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$order_id = intval($_GET['id'] ?? 0);
$result = $conn->query("SELECT * FROM orders WHERE id = $order_id");

if (!$result || $result->num_rows === 0) {
    header("Location: admin_orders.php");
    exit;
}

$order = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'];
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $order_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Order #$order_id updated successfully.";
        header("Location: admin_orders.php");
        exit;
    } else {
        $error = "Failed to update order.";
    }
}

include '../includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Edit Order #<?= $order['id'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>

<div class="container mt-5">
    <h2>Edit Order #<?= $order['id'] ?></h2>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" class="mt-3" style="max-width: 400px;">
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select id="status" name="status" class="form-select" required>
                <?php
                $statuses = ['Pending', 'Shipped', 'Delivered', 'Cancelled'];
                foreach ($statuses as $s) {
                    $selected = ($order['status'] === $s) ? 'selected' : '';
                    echo "<option value=\"$s\" $selected>$s</option>";
                }
                ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Update Order</button>
        <a href="admin_orders.php" class="btn btn-secondary ms-2">Cancel</a>
    </form>
</div>

<?php include '../includes/footer.php'; ?>

</body>
</html>
