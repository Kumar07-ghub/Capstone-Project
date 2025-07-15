<?php
session_start();
include '../includes/db.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}
include '../includes/header.php';

$res = $conn->query("SELECT id,name,email,role,created_at FROM users ORDER BY id DESC");
?>

<div class="container mt-5">
  <h1>Manage Users</h1>
  <table class="table table-striped">
    <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Created</th></tr></thead>
    <tbody>
      <?php while($u = $res->fetch_assoc()): ?>
      <tr><td><?php echo $u['id'];?></td><td><?php echo htmlspecialchars($u['name']);?></td><td><?php echo htmlspecialchars($u['email']);?></td><td><?php echo $u['role'];?></td><td><?php echo $u['created_at'];?></td>
    <td>
    <a href="edit_user.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
    <?php if ($u['id'] != $_SESSION['user']['id']): ?>
        <a href="delete_user.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-danger"
           onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
    <?php endif; ?>
</td>
</tr>
      <?php endwhile;?>
    </tbody>
  </table>
</div>

<?php include '../includes/footer.php'; ?>