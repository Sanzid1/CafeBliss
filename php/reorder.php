<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../html/login.php");
    exit();
}

if (!isset($_GET['order_id'])) {
    header("Location: ../html/dashboard.php");
    exit();
}

$order_id = (int)$_GET['order_id'];
$user_id = $_SESSION['user_id'];

// Verify the order belongs to this user
$stmt = $conn->prepare("SELECT user_id FROM orders WHERE order_id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows == 0) {
    // No such order
    $stmt->close();
    $conn->close();
    header("Location: ../html/dashboard.php");
    exit();
}
$stmt->bind_result($o_user_id);
$stmt->fetch();
$stmt->close();

if ($o_user_id != $user_id) {
    // This order does not belong to the current user
    $conn->close();
    header("Location: ../html/dashboard.php");
    exit();
}

// Fetch order items
$stmt = $conn->prepare("SELECT item_id, quantity FROM order_items WHERE order_id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$reorder_items = [];
while ($row = $result->fetch_assoc()) {
    $reorder_items[] = $row;
}
$stmt->close();
$conn->close();

// Add items to cart
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

foreach ($reorder_items as $ri) {
    $id = $ri['item_id'];
    $qty = $ri['quantity'];
    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id] += $qty;
    } else {
        $_SESSION['cart'][$id] = $qty;
    }
}

// Redirect to cart with a message
header("Location: ../html/cart.php?reorder=success");
exit();
