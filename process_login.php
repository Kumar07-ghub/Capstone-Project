<<<<<<< HEAD
<?php
session_start();
include 'includes/db.php';

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => false,  // Set to false for local testing, true for production
    'httponly' => true,
    'samesite' => 'Strict'
]);

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['email']) || empty($_POST['password'])) {
    $_SESSION['login_error'] = "Please enter both email and password.";
    header("Location: login.php");
    exit;
}

$email = trim($_POST['email']);
$password = $_POST['password'];

// Query to check user credentials
$sql = "SELECT id, first_name, last_name, email, password, role, created_at FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // Debugging output
    error_log("Entered password: " . $password);
    error_log("Hashed password from DB: " . $user['password']);

    // Verify password
    if (password_verify($password, $user['password'])) {
        session_regenerate_id(true); // Regenerate session ID to prevent session fixation

        $_SESSION['user'] = [
            'id' => $user['id'],
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'email' => $user['email'],
            'role' => $user['role'],
            'created_at' => $user['created_at']
        ];

        // Redirect based on user role (Admin or User)
        if ($user['role'] === 'admin') {
            header("Location: admin/admin_dashboard.php");
        } else {
            $redirect = $_SESSION['redirect_after_login'] ?? 'index.php';
            unset($_SESSION['redirect_after_login']);
            header("Location: $redirect");
        }

        exit;
    } else {
        $_SESSION['login_error'] = "Invalid credentials.";
        error_log("Password verification failed.");
    }
} else {
    $_SESSION['login_error'] = "No user found with that email.";
}

header("Location: login.php");
exit;
=======
<?php
session_start();
include 'includes/db.php';

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => false,  // Set to false for local testing, true for production
    'httponly' => true,
    'samesite' => 'Strict'
]);

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['email']) || empty($_POST['password'])) {
    $_SESSION['login_error'] = "Please enter both email and password.";
    header("Location: login.php");
    exit;
}

$email = trim($_POST['email']);
$password = $_POST['password'];

// Query to check user credentials
$sql = "SELECT id, name, email, password, role, created_at FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // Debugging output
    error_log("Entered password: " . $password);
    error_log("Hashed password from DB: " . $user['password']);

    // Verify password
    if (password_verify($password, $user['password'])) {
        session_regenerate_id(true); // Regenerate session ID to prevent session fixation

        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
            'created_at' => $user['created_at']
        ];

        // Redirect based on user role (Admin or User)
        if ($user['role'] === 'admin') {
            header("Location: admin/admin_dashboard.php");
        } else {
            $redirect = $_SESSION['redirect_after_login'] ?? 'index.php';
            unset($_SESSION['redirect_after_login']);
            header("Location: $redirect");
        }

        exit;
    } else {
        $_SESSION['login_error'] = "Invalid credentials.";
        error_log("Password verification failed.");
    }
} else {
    $_SESSION['login_error'] = "No user found with that email.";
}

header("Location: login.php");
exit;
>>>>>>> 07d8e6ccfd579de17e14e08d58f8a836c66fbf26
