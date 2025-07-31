<?php

session_start();

include '../includes/db.php';



if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {

    header("Location: ../login.php");

    exit;

}



$order_id = intval($_GET['id'] ?? 0);



if ($order_id > 0) {

    $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");

    $stmt->bind_param("i", $order_id);

    $stmt->execute();

    $stmt->close();

}



header("Location: admin_orders.php");

exit;