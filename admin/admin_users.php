<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

include '../includes/header.php';

// Select first_name and last_name explicitly
$res = $conn->query("SELECT id, first_name, last_name, email, role, created_at FROM users ORDER BY id DESC");
?>

<div class="container mt-5">
  <h1>Manage Users</h1>
  <div class="table-responsive">
    <table class="table table-striped">
      <thead>
        <tr>
          <th>ID</th>
          <th>First Name</th>
          <th>Last Name</th>
          <th>Email</th>
          <th>Role</th>
          <th>Created</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php while($u = $res->fetch_assoc()): ?>
        <tr>
          <td><?= $u['id'] ?></td>
          <td><?= htmlspecialchars($u['first_name']) ?></td>
          <td><?= htmlspecialchars($u['last_name']) ?></td>
          <td><?= htmlspecialchars($u['email']) ?></td>
          <td><?= $u['role'] ?></td>
          <td><?= $u['created_at'] ?></td>
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
</div>

<?php include '../includes/footer.php'; ?>
