<?php
session_start();
if ($_SESSION['user']['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

require '../includes/db.php';

$product_id = $_GET['id'];
$query = "DELETE FROM products WHERE id = '$product_id'";

if ($conn->query($query)) {
    header("Location: admin_products.php");
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>
