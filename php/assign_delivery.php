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
    $delivery_person = trim($_POST['delivery_person']);

    $stmt = $conn->prepare("UPDATE orders SET delivery_person = ? WHERE order_id = ?");
    $stmt->bind_param("si", $delivery_person, $order_id);
    $stmt->execute();
    $stmt->close();
    $conn->close();

    header("Location: ../html/employee_dashboard.php");
    exit();
} else {
    header("Location: ../html/employee_dashboard.php");
    exit();
}
