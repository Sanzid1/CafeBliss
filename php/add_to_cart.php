<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // If not logged in, redirect to login
    header("Location: ../html/login.php");
    exit();
}

// Retrieve item_id and quantity from POST
if (isset($_POST['item_id']) && isset($_POST['quantity'])) {
    $item_id = (int)$_POST['item_id'];
    $quantity = (int)$_POST['quantity'];

    // Ensure quantity is at least 1
    if ($quantity < 1) {
        $quantity = 1;
    }

    // Initialize cart if not set
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }

    // If item already in cart, increment quantity, otherwise set it
    if (isset($_SESSION['cart'][$item_id])) {
        $_SESSION['cart'][$item_id] += $quantity;
    } else {
        $_SESSION['cart'][$item_id] = $quantity;
    }

    // Redirect back to the dashboard with a success message parameter
    header("Location: ../html/dashboard.php?cart=added");
    exit();
} else {
    // If item_id or quantity is not provided, redirect back without success parameter
    header("Location: ../html/dashboard.php");
    exit();
}
