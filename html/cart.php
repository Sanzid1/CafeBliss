<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require '../php/db_connect.php';

// If cart is empty or not set
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    $cart_items = array();
} else {
    $cart_items = $_SESSION['cart'];
}

// Handle updates and removals
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update quantities
    if (isset($_POST['update_cart'])) {
        foreach ($_POST['quantity'] as $item_id => $qty) {
            $qty = (int)$qty;
            if ($qty > 0) {
                $_SESSION['cart'][$item_id] = $qty;
            } else {
                // If qty is zero or less, remove the item
                unset($_SESSION['cart'][$item_id]);
            }
        }
    }

    // Remove single item
    if (isset($_POST['remove_item'])) {
        $remove_id = (int)$_POST['remove_item'];
        if (isset($_SESSION['cart'][$remove_id])) {
            unset($_SESSION['cart'][$remove_id]);
        }
    }

    // After updating, refresh the page
    header("Location: cart.php");
    exit();
}

// Fetch item details for all items in the cart
$cart_details = array();
$total = 0.0;

if (!empty($cart_items)) {
    $item_ids = array_keys($cart_items);
    if (count($item_ids) > 0) {
        // Create a placeholder string for the query
        $placeholders = implode(',', array_fill(0, count($item_ids), '?'));
        $stmt = $conn->prepare("SELECT item_id, item_name, price, image_path FROM menu_items WHERE item_id IN ($placeholders)");

        // Bind parameters dynamically
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
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Cart - Cafe Bliss</title>
    <link rel="stylesheet" href="../bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/global.css">


    <link rel="stylesheet" href="../css/cart.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-5">
        <h2 class="text-center mb-4">Your Cart</h2>
        <?php if (empty($cart_details)): ?>
            <div class="alert alert-info text-center">
                Your cart is empty. <a href="dashboard.php">Browse the menu</a> to add items.
            </div>
        <?php else: ?>
            <form action="cart.php" method="POST">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Item</th>
                            <th>Price (BDT)</th>
                            <th>Quantity</th>
                            <th>Subtotal (BDT)</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart_details as $item): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="../images/<?php echo htmlspecialchars($item['image_path']); ?>" alt="<?php echo htmlspecialchars($item['item_name']); ?>" style="width: 50px; height:50px; object-fit:cover; margin-right:10px;">
                                        <?php echo htmlspecialchars($item['item_name']); ?>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($item['price']); ?></td>
                                <td style="width:120px;">
                                    <input type="number" name="quantity[<?php echo $item['item_id']; ?>]" class="form-control" value="<?php echo $item['quantity']; ?>" min="1" max="10">
                                </td>
                                <td><?php echo number_format($item['subtotal'], 2); ?></td>
                                <td>
                                    <button type="submit" name="remove_item" value="<?php echo $item['item_id']; ?>" class="btn btn-danger btn-sm">Remove</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <tr>
                            <td colspan="3" class="text-end"><strong>Total:</strong></td>
                            <td colspan="2"><strong><?php echo number_format($total, 2); ?> BDT</strong></td>
                        </tr>
                    </tbody>
                </table>
                <div class="d-flex justify-content-between">
                    <a href="dashboard.php" class="btn btn-secondary">Continue Shopping</a>
                    <div>
                        <button type="submit" name="update_cart" class="btn btn-primary">Update Cart</button>
                        <a href="checkout.php" class="btn btn-success">Proceed to Checkout</a>
                    </div>
                </div>
            </form>
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
