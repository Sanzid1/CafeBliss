<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Admin') {
    header("Location: ../html/login.php");
    exit();
}

// Add new item
if (isset($_POST['add_item'])) {
    $item_name = trim($_POST['item_name']);
    $price = (float)$_POST['price'];
    $stock_quantity = (int)$_POST['stock_quantity'];
    $category_id = (int)$_POST['category_id'];
    $description = trim($_POST['description']);

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $new_name = uniqid().'.'.$ext;
        move_uploaded_file($_FILES['image']['tmp_name'], "../images/$new_name");

        $stmt = $conn->prepare("INSERT INTO menu_items (item_name, price, image_path, stock_quantity, category_id, description) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sdsiis", $item_name, $price, $new_name, $stock_quantity, $category_id, $description);
        $stmt->execute();
        $stmt->close();
    }
    $conn->close();
    header("Location: ../html/admin_dashboard.php");
    exit();
}

// Delete item
if (isset($_POST['delete_item'])) {
    $item_id = (int)$_POST['item_id'];

    // Optional: delete image
    $stmt = $conn->prepare("SELECT image_path FROM menu_items WHERE item_id=?");
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $stmt->bind_result($image_path);
    if ($stmt->fetch()) {
        // @unlink("../images/$image_path"); // if you want to physically remove the file
    }
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM menu_items WHERE item_id = ?");
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $stmt->close();
    $conn->close();

    header("Location: ../html/admin_dashboard.php");
    exit();
}

// Restock item
if (isset($_POST['restock_item'])) {
    $item_id = (int)$_POST['item_id'];
    // Set stock_quantity to 50
    $stmt = $conn->prepare("UPDATE menu_items SET stock_quantity = 50 WHERE item_id = ?");
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $stmt->close();
    $conn->close();

    header("Location: ../html/admin_dashboard.php");
    exit();
}

$conn->close();
header("Location: ../html/admin_dashboard.php");
exit();
