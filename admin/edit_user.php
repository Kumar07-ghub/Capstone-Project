<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$user_id = intval($_GET['id'] ?? 0);
$result = $conn->query("SELECT * FROM users WHERE id = $user_id");

if (!$result || $result->num_rows === 0) {
    header("Location: admin_users.php");
    exit;
}

$user = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email address.";
    } else {
        $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, role = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $first_name, $last_name, $email, $role, $user_id);
        if ($stmt->execute()) {
            header("Location: admin_users.php");
            exit;
        } else {
            $error = "Failed to update user.";
        }
    }
}

include '../includes/header.php';
?>

<div class="container mt-5">
    <h2>Edit User #<?= $user['id'] ?></h2>

    <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

    <form method="post" id="editUserForm" novalidate>
        <div class="mb-3">
            <label for="first_name">First Name <span class="text-danger">*</span></label>
            <input name="first_name" id="first_name" class="form-control" value="<?= htmlspecialchars($user['first_name']) ?>" required placeholder="Enter first name">
            <div class="invalid-feedback">First name is required.</div>
        </div>

        <div class="mb-3">
            <label for="last_name">Last Name <span class="text-danger">*</span></label>
            <input name="last_name" id="last_name" class="form-control" value="<?= htmlspecialchars($user['last_name']) ?>" required placeholder="Enter last name">
            <div class="invalid-feedback">Last name is required.</div>
        </div>

        <div class="mb-3">
            <label for="email">Email <span class="text-danger">*</span></label>
            <input type="email" name="email" id="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required placeholder="e.g. name@example.com">
            <div class="invalid-feedback">Please enter a valid email address.</div>
        </div>

        <div class="mb-3">
            <label for="role">Role <span class="text-danger">*</span></label>
            <select name="role" id="role" class="form-select" required>
                <option value="">-- Select Role --</option>
                <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
            </select>
            <div class="invalid-feedback">Please select a role.</div>
        </div>

        <button class="btn btn-primary" type="submit">Update</button>
        <a href="admin_users.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<script>
// Real-time validation
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('editUserForm');
    form.addEventListener('submit', function (e) {
        if (!form.checkValidity()) {
            e.preventDefault();
            form.classList.add('was-validated');
        }
    });

    ['first_name', 'last_name', 'email', 'role'].forEach(id => {
        document.getElementById(id).addEventListener('input', function () {
            this.classList.remove('is-invalid');
            this.classList.remove('is-valid');
            if (this.checkValidity()) {
                this.classList.add('is-valid');
            } else {
                this.classList.add('is-invalid');
            }
        });
    });
});
</script>

<?php include '../includes/footer.php'; ?>
