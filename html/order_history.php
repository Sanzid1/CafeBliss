<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require '../php/db_connect.php';

$user_id = $_SESSION['user_id'];

// Fetch user orders
$stmt = $conn->prepare("SELECT order_id, order_date, delivery_type, order_status, payment_method, total_amount FROM orders WHERE user_id = ? ORDER BY order_date DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order History - Cafe Bliss</title>
    <link rel="stylesheet" href="../bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/global.css">


</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-5">
        <h2 class="text-center mb-4">Your Order History</h2>
        <?php if (empty($orders)): ?>
            <div class="alert alert-info text-center">You have not placed any orders yet.</div>
        <?php else: ?>
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Order ID</th>
                        <th>Date</th>
                        <th>Delivery Type</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th>Total (BDT)</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $o): ?>
                        <tr>
                            <td><?php echo $o['order_id']; ?></td>
                            <td><?php echo htmlspecialchars($o['order_date']); ?></td>
                            <td><?php echo htmlspecialchars($o['delivery_type']); ?></td>
                            <td><?php echo htmlspecialchars($o['order_status']); ?></td>
                            <td><?php echo htmlspecialchars($o['payment_method']); ?></td>
                            <td><?php echo number_format($o['total_amount'], 2); ?></td>
                            <td>
                                <a href="order_confirm.php?order_id=<?php echo $o['order_id']; ?>" class="btn btn-secondary btn-sm">View</a>
                                <a href="../php/reorder.php?order_id=<?php echo $o['order_id']; ?>" class="btn btn-primary btn-sm">Reorder</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
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
