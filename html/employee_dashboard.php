<?php
session_start();

// Check if employee or admin
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_type'], ['Employee','Admin'])) {
    header("Location: login.php");
    exit();
}

require '../php/db_connect.php';

// Function to fetch orders by type
function fetch_orders_by_type($conn, $type) {
    $stmt = $conn->prepare("SELECT order_id, user_id, order_date, order_status, total_amount, delivery_address FROM orders WHERE delivery_type = ? ORDER BY order_date DESC");
    $stmt->bind_param("s", $type);
    $stmt->execute();
    $result = $stmt->get_result();
    $orders = [];
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
    $stmt->close();
    return $orders;
}

$dine_in_orders = fetch_orders_by_type($conn, 'Dine-in');
$pickup_orders = fetch_orders_by_type($conn, 'Pickup');
$delivery_orders = fetch_orders_by_type($conn, 'Delivery');

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employee Dashboard - Cafe Bliss</title>
    <link rel="stylesheet" href="../bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/global.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-5">
        <h2 class="text-center mb-4">Order Management</h2>
        <ul class="nav nav-tabs" id="orderTabs" role="tablist">
            <li class="nav-item"><a class="nav-link active" id="dinein-tab" data-bs-toggle="tab" href="#dinein" role="tab">Dine-in</a></li>
            <li class="nav-item"><a class="nav-link" id="pickup-tab" data-bs-toggle="tab" href="#pickup" role="tab">Pickup</a></li>
            <li class="nav-item"><a class="nav-link" id="delivery-tab" data-bs-toggle="tab" href="#delivery" role="tab">Delivery</a></li>
        </ul>
        <div class="tab-content mt-3">
            <!-- Dine-in Orders -->
            <div class="tab-pane fade show active" id="dinein" role="tabpanel">
                <?php if (empty($dine_in_orders)): ?>
                    <div class="alert alert-info">No Dine-in orders.</div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach($dine_in_orders as $o): ?>
                            <div class="col-md-4 mb-4">
                                <div class="card p-3">
                                    <h5>Order #<?php echo $o['order_id']; ?></h5>
                                    <p><strong>Status:</strong> <?php echo htmlspecialchars($o['order_status']); ?></p>
                                    <p><strong>Total:</strong> BDT <?php echo number_format($o['total_amount'], 2); ?></p>
                                    <p><strong>Date:</strong> <?php echo htmlspecialchars($o['order_date']); ?></p>
                                    <form action="../php/update_order_status.php" method="POST" class="mb-2">
                                        <input type="hidden" name="order_id" value="<?php echo $o['order_id']; ?>">
                                        <select name="order_status" class="form-select mb-2">
                                            <option value="Pending">Pending</option>
                                            <option value="Preparing">Preparing</option>
                                            <option value="Ready for Pickup">Ready for Pickup</option>
                                            <option value="Delivered">Delivered</option>
                                        </select>
                                        <button type="submit" class="btn btn-primary btn-sm w-100">Update Status</button>
                                    </form>
                                    <a href="order_details.php?order_id=<?php echo $o['order_id']; ?>" class="btn btn-secondary btn-sm w-100 mt-2">View Details</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Pickup Orders -->
            <div class="tab-pane fade" id="pickup" role="tabpanel">
                <?php if (empty($pickup_orders)): ?>
                    <div class="alert alert-info">No Pickup orders.</div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach($pickup_orders as $o): ?>
                            <div class="col-md-4 mb-4">
                                <div class="card p-3">
                                    <h5>Order #<?php echo $o['order_id']; ?></h5>
                                    <p><strong>Status:</strong> <?php echo htmlspecialchars($o['order_status']); ?></p>
                                    <p><strong>Total:</strong> BDT <?php echo number_format($o['total_amount'], 2); ?></p>
                                    <p><strong>Date:</strong> <?php echo htmlspecialchars($o['order_date']); ?></p>
                                    <form action="../php/update_order_status.php" method="POST" class="mb-2">
                                        <input type="hidden" name="order_id" value="<?php echo $o['order_id']; ?>">
                                        <select name="order_status" class="form-select mb-2">
                                            <option value="Pending">Pending</option>
                                            <option value="Preparing">Preparing</option>
                                            <option value="Ready for Pickup">Ready for Pickup</option>
                                            <option value="Delivered">Delivered</option>
                                        </select>
                                        <button type="submit" class="btn btn-primary btn-sm w-100">Update Status</button>
                                    </form>
                                    <a href="order_details.php?order_id=<?php echo $o['order_id']; ?>" class="btn btn-secondary btn-sm w-100 mt-2">View Details</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Delivery Orders -->
            <div class="tab-pane fade" id="delivery" role="tabpanel">
                <?php if (empty($delivery_orders)): ?>
                    <div class="alert alert-info">No Delivery orders.</div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach($delivery_orders as $o): ?>
                            <div class="col-md-4 mb-4">
                                <div class="card p-3">
                                    <h5>Order #<?php echo $o['order_id']; ?></h5>
                                    <p><strong>Status:</strong> <?php echo htmlspecialchars($o['order_status']); ?></p>
                                    <p><strong>Total:</strong> BDT <?php echo number_format($o['total_amount'], 2); ?></p>
                                    <p><strong>Date:</strong> <?php echo htmlspecialchars($o['order_date']); ?></p>
                                    <p><strong>Address:</strong> <?php echo htmlspecialchars($o['delivery_address']); ?></p>
                                    <form action="../php/update_order_status.php" method="POST" class="mb-2">
                                        <input type="hidden" name="order_id" value="<?php echo $o['order_id']; ?>">
                                        <select name="order_status" class="form-select mb-2">
                                            <option value="Pending">Pending</option>
                                            <option value="Preparing">Preparing</option>
                                            <option value="Out for Delivery">Out for Delivery</option>
                                            <option value="Delivered">Delivered</option>
                                        </select>
                                        <button type="submit" class="btn btn-primary btn-sm w-100 mb-2">Update Status</button>
                                    </form>
                                    <form action="../php/assign_delivery.php" method="POST">
                                        <input type="hidden" name="order_id" value="<?php echo $o['order_id']; ?>">
                                        <input type="text" name="delivery_person" class="form-control mb-2" placeholder="Delivery Person Name">
                                        <button type="submit" class="btn btn-secondary btn-sm w-100">Assign Delivery</button>
                                    </form>
                                    <a href="order_details.php?order_id=<?php echo $o['order_id']; ?>" class="btn btn-secondary btn-sm w-100 mt-2">View Details</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
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
