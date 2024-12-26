<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require '../php/db_connect.php';

// If cart is empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    // Redirect to dashboard if cart is empty
    header("Location: dashboard.php");
    exit();
}

// Fetch cart details
$cart_items = $_SESSION['cart'];
$item_ids = array_keys($cart_items);
$total = 0.0;
$cart_details = array();

if (count($item_ids) > 0) {
    $placeholders = implode(',', array_fill(0, count($item_ids), '?'));
    $stmt = $conn->prepare("SELECT item_id, item_name, price, image_path FROM menu_items WHERE item_id IN ($placeholders)");
    $types = str_repeat('i', count($item_ids));
    $stmt->bind_param($types, ...$item_ids);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $id = $row['item_id'];
        $quantity = $cart_items[$id];
        $subtotal = $row['price'] * $quantity;
        $total += $subtotal;
        $cart_details[] = array(
            'item_id' => $row['item_id'],
            'item_name' => $row['item_name'],
            'price' => $row['price'],
            'image_path' => $row['image_path'],
            'quantity' => $quantity,
            'subtotal' => $subtotal
        );
    }
    $stmt->close();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout - Cafe Bliss</title>
    <link rel="stylesheet" href="../bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/global.css">


    <link rel="stylesheet" href="../css/checkout.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-5">
        <h2 class="text-center mb-4">Checkout</h2>
        <?php if (empty($cart_details)): ?>
            <div class="alert alert-info text-center">
                Your cart is empty. <a href="dashboard.php">Browse the menu</a> to add items.
            </div>
        <?php else: ?>
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
                    <?php foreach ($cart_details as $item): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="../images/<?php echo htmlspecialchars($item['image_path']); ?>" alt="<?php echo htmlspecialchars($item['item_name']); ?>" style="width:50px; height:50px; object-fit:cover; margin-right:10px;">
                                    <?php echo htmlspecialchars($item['item_name']); ?>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($item['price']); ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td><?php echo number_format($item['subtotal'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="3" class="text-end"><strong>Total:</strong></td>
                        <td><strong><?php echo number_format($total, 2); ?> BDT</strong></td>
                    </tr>
                </tbody>
            </table>

            <form action="../php/place_order.php" method="POST" id="checkoutForm">
                <div class="mb-3">
                    <label class="form-label">Delivery Type</label>
                    <select name="delivery_type" class="form-select" id="deliveryType" required>
                        <option value="Dine-in">Dine-in</option>
                        <option value="Pickup">Pickup</option>
                        <option value="Delivery">Delivery</option>
                    </select>
                </div>

                <div class="mb-3" id="addressField" style="display: none;">
                    <label class="form-label">Delivery Address</label>
                    <input type="text" name="delivery_address" class="form-control" placeholder="Enter delivery address if delivery selected">
                </div>

                <div class="mb-3">
                    <label class="form-label">Payment Method</label>
                    <select name="payment_method" class="form-select" required>
                        <option value="Cash on Delivery">Cash on Delivery</option>
                        <option value="Bank Transfer">Bank Transfer</option>
                        <option value="Bkash">Bkash</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-success w-100">Place Order</button>
            </form>
        <?php endif; ?>
    </div>

    <script src="../bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/global.js" defer></script>
    <script src="../js/checkout.js"></script>

    <div class="footer">
    <p><strong>Café Bliss</strong></p>
    <p>123 Coffee Street, Bean Town, Dhaka 1215</p>
    <p>Open Daily: 7 AM - 8 PM</p>
    <p>Contact: (+880) 123-4567</p>
</div>

</body>
</html>
