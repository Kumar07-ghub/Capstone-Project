<<<<<<< HEAD
<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php?message=login_required");
    exit;
}

$user_id = $_SESSION['user']['id'];
$product_id = intval($_POST['product_id'] ?? 0);
$rating = intval($_POST['rating'] ?? 0);
$review = trim($_POST['review'] ?? '');

if ($product_id <= 0 || $rating < 1 || $rating > 5) {
    header("Location: product_details.php?id=$product_id&error=invalid");
    exit;
}

// Optional: Prevent duplicate rating
$stmt = $conn->prepare("SELECT id FROM product_ratings WHERE user_id = ? AND product_id = ?");
$stmt->bind_param("ii", $user_id, $product_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    // Already rated
    header("Location: product_details.php?id=$product_id&error=already_rated");
    exit;
}

// Insert new review
$stmt = $conn->prepare("INSERT INTO product_ratings (user_id, product_id, rating, review, created_at)
                        VALUES (?, ?, ?, ?, NOW())");
$stmt->bind_param("iiis", $user_id, $product_id, $rating, $review);
$stmt->execute();

header("Location: product_details.php?id=$product_id&success=1");
exit;
=======
<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php?message=login_required");
    exit;
}

$user_id = $_SESSION['user']['id'];
$product_id = intval($_POST['product_id'] ?? 0);
$rating = intval($_POST['rating'] ?? 0);
$review = trim($_POST['review'] ?? '');

if ($product_id <= 0 || $rating < 1 || $rating > 5) {
    header("Location: product_details.php?id=$product_id&error=invalid");
    exit;
}

// Optional: Prevent duplicate rating
$stmt = $conn->prepare("SELECT id FROM product_ratings WHERE user_id = ? AND product_id = ?");
$stmt->bind_param("ii", $user_id, $product_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    // Already rated
    header("Location: product_details.php?id=$product_id&error=already_rated");
    exit;
}

// Insert new review
$stmt = $conn->prepare("INSERT INTO product_ratings (user_id, product_id, rating, review, created_at)
                        VALUES (?, ?, ?, ?, NOW())");
$stmt->bind_param("iiis", $user_id, $product_id, $rating, $review);
$stmt->execute();

header("Location: product_details.php?id=$product_id&success=1");
exit;
>>>>>>> 07d8e6ccfd579de17e14e08d58f8a836c66fbf26
