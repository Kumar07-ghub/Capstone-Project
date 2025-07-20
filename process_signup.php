<?php
session_start();
include 'includes/db.php';

$name = trim($_POST['name']);
$email = trim($_POST['email']);
$password = password_hash($_POST['password'], PASSWORD_BCRYPT);

// Check if email already exists
$check_sql = "SELECT id FROM users WHERE email = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("s", $email);
$check_stmt->execute();
$check_stmt->store_result();

if ($check_stmt->num_rows > 0) {
    $_SESSION['signup_error'] = "❌ This email is already registered. Please use another email or <a href='login.php'>log in</a>.";
    header("Location: signup.php");
    exit;
}
$check_stmt->close();

// Insert new user
$sql = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $name, $email, $password);

if ($stmt->execute()) {
    $user_id = $stmt->insert_id;

    // 🔁 Fetch full user record including created_at
    $fetch = $conn->prepare("SELECT id, name, email, created_at FROM users WHERE id = ?");
    $fetch->bind_param("i", $user_id);
    $fetch->execute();
    $result = $fetch->get_result();
    $user = $result->fetch_assoc();

    $_SESSION['user'] = $user;

    header("Location: index.php");
    exit;
} else {
    $_SESSION['signup_error'] = "❌ Signup failed. Please try again." . $stmt->error;
    header("Location: signup.php");
    exit;
}
?>
