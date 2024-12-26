<?php
session_start();
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_type'], ['Employee','Admin'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['order_id'])) {
    header("Location: employee_dashboard.php");
    exit();
}

$order_id = (int)$_GET['order_id'];

require '../php/db_connect.php';

// Verify order exists
$stmt = $conn->prepare("SELECT order_id, user_id, order_date, delivery_type, order_status, payment_method, total_amount, delivery_address 
FROM orders WHERE order_id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order_result = $stmt->get_result();
if ($order_result->num_rows == 0) {
    $stmt->close();
    $conn->close();
    header("Location: employee_dashboard.php");
    exit();
}
$order = $order_result->fetch_assoc();
$stmt->close();

// Fetch order items
$stmt = $conn->prepare("SELECT oi.item_id, oi.quantity, oi.price, m.item_name
FROM order_items oi
JOIN menu_items m ON oi.item_id = m.item_id
WHERE oi.order_id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$items_result = $stmt->get_result();
$order_items = [];
while ($row = $items_result->fetch_assoc()) {
    $order_items[] = $row;
}
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Details - Cafe Bliss</title>
    <link rel="stylesheet" href="../bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/global.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-5">
        <h2 class="mb-4">Order #<?php echo $order_id; ?> Details</h2>
        <p><strong>Date:</strong> <?php echo htmlspecialchars($order['order_date']); ?></p>
        <p><strong>Delivery Type:</strong> <?php echo htmlspecialchars($order['delivery_type']); ?></p>
        <p><strong>Status:</strong> <?php echo htmlspecialchars($order['order_status']); ?></p>
        <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($order['payment_method']); ?></p>
        <?php if ($order['delivery_type'] === 'Delivery'): ?>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($order['delivery_address']); ?></p>
        <?php endif; ?>
        <p><strong>Total (BDT):</strong> <?php echo number_format($order['total_amount'], 2); ?></p>

        <h4 class="mt-4">Items in this Order</h4>
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Item</th>
                    <th>Price (BDT)</th>
                    <th>Quantity</th>
                    <th>Subtotal (BDT)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order_items as $i): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($i['item_name']); ?></td>
                        <td><?php echo htmlspecialchars($i['price']); ?></td>
                        <td><?php echo $i['quantity']; ?></td>
                        <td><?php echo number_format($i['price'] * $i['quantity'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <a href="employee_dashboard.php" class="btn btn-secondary mt-3">Back to Employee Dashboard</a>
    </div>

    <div class="footer">
        <p><strong>Café Bliss</strong></p>
        <p>123 Coffee Street, Bean Town, Dhaka 1215</p>
        <p>Open Daily: 7 AM - 8 PM</p>
        <p>Contact: (+880) 123-4567</p>
    </div>

    <script src="../bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/global.js"></script>
</body>
</html>
