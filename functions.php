<?php
function sendInvoice($order_id, $conn) {
    // Example logic — customize as needed
    $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();

    if (!$order) return;

    // Create a PDF invoice or send an email, etc.
    // For now, just simulate:
    error_log("Sending invoice for order #" . $order_id . " to " . $order['email']);
}