<?php
session_start();
require 'db_connect.php';

// Check if employee or admin
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_type'], ['Employee','Admin'])) {
    header("Location: ../html/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = (int)$_POST['order_id'];
    $order_status = $_POST['order_status'];

    $stmt = $conn->prepare("UPDATE orders SET order_status = ? WHERE order_id = ?");
    $stmt->bind_param("si", $order_status, $order_id);
    $stmt->execute();
    $stmt->close();
    $conn->close();

    header("Location: ../html/employee_dashboard.php");
    exit();
} else {
    header("Location: ../html/employee_dashboard.php");
    exit();
}
