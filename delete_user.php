<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$user_id = intval($_GET['id'] ?? 0);

// Optional: Prevent admin from deleting themselves
if ($user_id == $_SESSION['user']['id']) {
    header("Location: admin_users.php");
    exit;
}

$conn->query("DELETE FROM users WHERE id = $user_id");

header("Location: admin_users.php");
exit;