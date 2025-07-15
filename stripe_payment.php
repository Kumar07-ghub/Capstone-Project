<?php
session_start();
require 'vendor/autoload.php'; // Adjust path if needed
require 'includes/db.php';

\Stripe\Stripe::setApiKey('sk_test_51RgnJ8IK8JHviDvgBnLPmht2A1d8seVxkFz4nVoOkydrNa1XsclDADHztPImDtt5OvOOgbpIxkjblud8Su9pNYvQ00GsKeDCKm'); // Replace with your Stripe test secret key

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['full_name'];
    $email = $_POST['email'];

    // Build line items from cart
    $line_items = [];
    foreach ($_SESSION['cart'] as $productId => $qty) {
        $stmt = $conn->prepare("SELECT name, price FROM products WHERE id = ?");
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $line_items[] = [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => ['name' => $row['name']],
                    'unit_amount' => $row['price'] * 100, // in cents
                ],
                'quantity' => $qty,
            ];
        }
    }

    try {
        $checkout_session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => $line_items,
            'mode' => 'payment',
            'customer_email' => $email,
            'success_url' => 'http://localhost/order-success.php?session_id={CHECKOUT_SESSION_ID}', // Update domain
            'cancel_url' => 'http://localhost/checkout.php?canceled=true',
        ]);

        header("Location: " . $checkout_session->url);
        exit;
    } catch (Exception $e) {
        echo 'Stripe Error: ' . $e->getMessage();
    }
} else {
    echo "Invalid request method.";
}
?>