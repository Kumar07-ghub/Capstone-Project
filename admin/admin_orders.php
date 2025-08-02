<?php
session_start();
include '../includes/db.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['role']!=='admin') {
  header("Location: ../login.php");
  exit;
}
include '../includes/header.php';

// Pull the legacy `name` column
$res = $conn->query("
SELECT 
  id,
  first_name,
  last_name,
  email,
  total,
  status,
  created_at
FROM orders
  ORDER BY created_at DESC
") or die($conn->error);
?>
<div class="container mt-5">
  <h1 class="h3 mb-4">Manage Orders</h1>
  <div class="table-responsive">
    <table class="table table-hover table-bordered align-middle">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>First Name</th>
          <th>Last Name</th>
          <th>Email</th>
          <th>Total (₹)</th>
          <th>Status</th>
          <th>Created</th>
          <th class="text-center">Actions</th>
        </tr>
      </thead>
      <tbody>
      <?php while($o = $res->fetch_assoc()): 
          $fn = $o['first_name'];
          $ln = $o['last_name'];

      ?>
        <tr>
          <td><?= $o['id'] ?></td>
          <td><?= htmlspecialchars($fn) ?></td>
          <td><?= htmlspecialchars($ln) ?></td>
          <td><?= htmlspecialchars($o['email']) ?></td>
          <td><?= number_format($o['total'],2) ?></td>
          <td>
            <?php
switch ($o['status']) {
  case 'Pending':
    $badge = 'bg-warning text-dark';
    break;
  case 'Shipped':
    $badge = 'bg-info text-dark';
    break;
  case 'Delivered':
    $badge = 'bg-success';
    break;
  case 'Cancelled':
    $badge = 'bg-danger';
    break;
  default:
    $badge = 'bg-secondary';
}
?>
            <span class="badge <?= $badge ?>">
              <?= htmlspecialchars($o['status']) ?>
            </span>
          </td>
          <td><?= htmlspecialchars($o['created_at']) ?></td>
          <td class="text-center">
            <a href="edit_order.php?id=<?= $o['id'] ?>" class="btn btn-sm btn-warning me-1">
              <i class="bi bi-pencil-square"></i>
            </a>
            <a href="delete_order.php?id=<?= $o['id'] ?>" class="btn btn-sm btn-danger"
               onclick="return confirm('Delete this order?')">
              <i class="bi bi-trash"></i>
            </a>
          </td>
        </tr>
      <?php endwhile; ?>
      <?php if($res->num_rows===0): ?>
        <tr><td colspan="8" class="text-center">No orders found.</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include '../includes/footer.php'; ?>