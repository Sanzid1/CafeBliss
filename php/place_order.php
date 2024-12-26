<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../html/login.php");
    exit();
}

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    // If cart is empty, redirect to dashboard
    header("Location: ../html/dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $delivery_type = $_POST['delivery_type'];
    $payment_method = $_POST['payment_method'];
    $delivery_address = isset($_POST['delivery_address']) ? trim($_POST['delivery_address']) : null;

    // Calculate total again for security
    $cart_items = $_SESSION['cart'];
    $item_ids = array_keys($cart_items);
    $total = 0.0;

    if (count($item_ids) > 0) {
        $placeholders = implode(',', array_fill(0, count($item_ids), '?'));
        $stmt = $conn->prepare("SELECT item_id, price FROM menu_items WHERE item_id IN ($placeholders)");
        $types = str_repeat('i', count($item_ids));
        $stmt->bind_param($types, ...$item_ids);
        $stmt->execute();
        $result = $stmt->get_result();

        $prices = [];
        while ($row = $result->fetch_assoc()) {
            $prices[$row['item_id']] = $row['price'];
        }
        $stmt->close();

        foreach ($cart_items as $id => $qty) {
            $subtotal = $prices[$id] * $qty;
            $total += $subtotal;
        }
    }

    // Insert into orders table
    $order_status = 'Pending';
    $stmt = $conn->prepare("INSERT INTO orders (user_id, delivery_type, order_status, payment_method, total_amount, delivery_address) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssds", $user_id, $delivery_type, $order_status, $payment_method, $total, $delivery_address);
    $stmt->execute();
    $order_id = $stmt->insert_id;
    $stmt->close();

    // Insert into order_items table
    $stmt = $conn->prepare("INSERT INTO order_items (order_id, item_id, quantity, price) VALUES (?, ?, ?, ?)");
    foreach ($cart_items as $id => $qty) {
        $price = $prices[$id];
        $stmt->bind_param("iiid", $order_id, $id, $qty, $price);
        $stmt->execute();
    }
    $stmt->close();

    // Clear cart
    unset($_SESSION['cart']);

    $conn->close();

    // Redirect to order confirmation page
    header("Location: ../html/order_confirm.php?order_id=" . $order_id);
    exit();
} else {
    header("Location: ../html/dashboard.php");
    exit();
}
