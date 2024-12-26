<?php
session_start();
require '../php/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['order_id'])) {
    header("Location: dashboard.php");
    exit();
}

$order_id = (int)$_GET['order_id'];

// Fetch order details
$stmt = $conn->prepare("SELECT o.user_id, o.order_date, o.delivery_type, o.order_status, o.payment_method, o.total_amount, o.delivery_address, u.email
FROM orders o
JOIN users u ON o.user_id = u.user_id
WHERE o.order_id = ? AND o.user_id = ?");
$stmt->bind_param("ii", $order_id, $_SESSION['user_id']);
$stmt->execute();
$order_result = $stmt->get_result();

if ($order_result->num_rows == 0) {
    // No order found or not belongs to this user
    $stmt->close();
    $conn->close();
    header("Location: dashboard.php");
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
    <title>Order Confirmation - Cafe Bliss</title>
    <link rel="stylesheet" href="../bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/global.css">


</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-5">
        <h2 class="text-center mb-4">Order Confirmation</h2>
        <div class="alert alert-success text-center">
            Your order (#<?php echo $order_id; ?>) has been placed successfully!
        </div>

        <p><strong>Order Date:</strong> <?php echo htmlspecialchars($order['order_date']); ?></p>
        <p><strong>Delivery Type:</strong> <?php echo htmlspecialchars($order['delivery_type']); ?></p>
        <p><strong>Order Status:</strong> <?php echo htmlspecialchars($order['order_status']); ?></p>
        <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($order['payment_method']); ?></p>
        <?php if ($order['delivery_type'] === 'Delivery'): ?>
            <p><strong>Delivery Address:</strong> <?php echo htmlspecialchars($order['delivery_address']); ?></p>
        <?php endif; ?>

        <h4 class="mt-4">Order Items</h4>
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
                <tr>
                    <td colspan="3" class="text-end"><strong>Total:</strong></td>
                    <td><strong><?php echo number_format($order['total_amount'], 2); ?> BDT</strong></td>
                </tr>
            </tbody>
        </table>

        <?php if ($order['delivery_type'] === 'Dine-in'): ?>
            <p>For Dine-in, please note your order number: <strong><?php echo $order_id; ?></strong>. We will call your number when your order is ready.</p>
        <?php elseif ($order['delivery_type'] === 'Pickup'): ?>
            <p>Your order will be ready for pickup within 30-60 minutes. Please mention order number <strong><?php echo $order_id; ?></strong> at the counter.</p>
        <?php elseif ($order['delivery_type'] === 'Delivery'): ?>
            <p>Your order is being prepared and will be delivered soon. Keep your phone reachable for delivery updates.</p>
        <?php endif; ?>

        <p class="mt-3"><a href="dashboard.php" class="btn btn-primary">Back to Menu</a></p>
    </div>

    <script src="../bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/global.js" defer></script>

    <div class="footer">
    <p><strong>Café Bliss</strong></p>
    <p>123 Coffee Street, Bean Town, Dhaka 1215</p>
    <p>Open Daily: 7 AM - 8 PM</p>
    <p>Contact: (+880) 123-4567</p>
</div>

</body>
</html>
