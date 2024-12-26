<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

require '../php/db_connect.php';

// Fetch Users
$users = [];
$ures = $conn->query("SELECT user_id, full_name, email, user_type FROM users ORDER BY user_type, full_name");
while ($urow = $ures->fetch_assoc()) {
    $users[] = $urow;
}

// Fetch Menu Items with category and stock
$menu_items = [];
$mres = $conn->query("SELECT m.item_id, m.item_name, m.price, m.image_path, m.stock_quantity, c.category_name 
FROM menu_items m 
JOIN categories c ON m.category_id = c.category_id
ORDER BY m.item_name");
while ($mrow = $mres->fetch_assoc()) {
    $menu_items[] = $mrow;
}

// Fetch categories for adding new items
$cats = [];
$cat_res = $conn->query("SELECT category_id, category_name FROM categories ORDER BY category_name");
while ($cat_row = $cat_res->fetch_assoc()) {
    $cats[] = $cat_row;
}

// Reporting date range
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

$report_sql = "SELECT COUNT(*) as total_orders, SUM(total_amount) as total_sales FROM orders";
$params = [];
$types = '';

if (!empty($start_date) && !empty($end_date)) {
    $report_sql .= " WHERE order_date BETWEEN ? AND ?";
    $params[] = $start_date." 00:00:00";
    $params[] = $end_date." 23:59:59";
    $types = 'ss';
}
$stmt = $conn->prepare($report_sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$repres = $stmt->get_result();
$rep = $repres->fetch_assoc();
$stmt->close();

// Top item in given range
$top_item_sql = "SELECT m.item_name, SUM(oi.quantity) as qty 
FROM order_items oi 
JOIN menu_items m ON oi.item_id=m.item_id";
if (!empty($start_date) && !empty($end_date)) {
    $top_item_sql .= " JOIN orders o ON oi.order_id=o.order_id 
    WHERE o.order_date BETWEEN ? AND ?
    GROUP BY oi.item_id ORDER BY qty DESC LIMIT 1";
    $stmt = $conn->prepare($top_item_sql);
    $stmt->bind_param("ss", $start_date." 00:00:00", $end_date." 23:59:59");
} else {
    $top_item_sql .= " GROUP BY oi.item_id ORDER BY qty DESC LIMIT 1";
    $stmt = $conn->prepare($top_item_sql);
}
$stmt->execute();
$top_res = $stmt->get_result();
$top_item = $top_res->fetch_assoc();
$stmt->close();

// Data for charts

// Items sold distribution
$item_chart_sql = "SELECT m.item_name, SUM(oi.quantity) as qty 
FROM order_items oi 
JOIN menu_items m ON oi.item_id = m.item_id 
GROUP BY oi.item_id ORDER BY qty DESC";
$items_res = $conn->query($item_chart_sql);
$item_labels = [];
$item_data = [];
while ($ir = $items_res->fetch_assoc()) {
    $item_labels[] = $ir['item_name'];
    $item_data[] = (int)$ir['qty'];
}

// Employees processed orders
$emp_chart_sql = "SELECT u.full_name, COUNT(o.order_id) as count 
FROM orders o 
JOIN users u ON o.modified_by=u.user_id 
WHERE u.user_type='Employee' 
GROUP BY u.user_id ORDER BY count DESC";
$emp_res = $conn->query($emp_chart_sql);
$emp_labels = [];
$emp_data = [];
while ($er = $emp_res->fetch_assoc()) {
    $emp_labels[] = $er['full_name'];
    $emp_data[] = (int)$er['count'];
}

// Top customers by spending
$cust_chart_sql = "SELECT u.full_name, SUM(o.total_amount) as spent 
FROM orders o 
JOIN users u ON o.user_id=u.user_id 
WHERE u.user_type='Customer'
GROUP BY u.user_id 
ORDER BY spent DESC LIMIT 10";
$cust_res = $conn->query($cust_chart_sql);
$cust_labels = [];
$cust_data = [];
while ($cr = $cust_res->fetch_assoc()) {
    $cust_labels[] = $cr['full_name'];
    $cust_data[] = (float)$cr['spent'];
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Cafe Bliss</title>
    <link rel="stylesheet" href="../bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/global.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Admin Dashboard</h2>
        <ul class="nav nav-tabs" id="adminTabs" role="tablist">
            <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#user_mgmt">User Management</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#menu_mgmt">Menu Management</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#reporting">Order Reporting</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#statistics">Statistics</a></li>
        </ul>

        <div class="tab-content mt-3">
            <!-- User Management -->
            <div class="tab-pane fade show active" id="user_mgmt">
                <h4>User Management</h4>
                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>User ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>User Type</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($users as $u): ?>
                            <tr>
                                <td><?php echo $u['user_id']; ?></td>
                                <td><?php echo htmlspecialchars($u['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($u['email']); ?></td>
                                <td><?php echo htmlspecialchars($u['user_type']); ?></td>
                                <td>
                                    <form action="../php/manage_users.php" method="POST">
                                        <input type="hidden" name="user_id" value="<?php echo $u['user_id']; ?>">
                                        <select name="new_type" class="form-select mb-2">
                                            <option value="Customer" <?php echo ($u['user_type']=='Customer')?'selected':''; ?>>Customer</option>
                                            <option value="Employee" <?php echo ($u['user_type']=='Employee')?'selected':''; ?>>Employee</option>
                                            <option value="Admin" <?php echo ($u['user_type']=='Admin')?'selected':''; ?>>Admin</option>
                                        </select>
                                        <button type="submit" class="btn btn-primary btn-sm w-100">Update</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Menu Management -->
            <div class="tab-pane fade" id="menu_mgmt">
                <h4>Menu Management</h4>
                <form action="../php/manage_menu.php" method="POST" enctype="multipart/form-data" class="mb-4">
                    <div class="row g-2">
                        <div class="col-md-3">
                            <input type="text" name="item_name" class="form-control" placeholder="Item Name" required>
                        </div>
                        <div class="col-md-2">
                            <input type="number" step="0.01" name="price" class="form-control" placeholder="Price" required>
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="stock_quantity" class="form-control" placeholder="Stock" min="0" required>
                        </div>
                        <div class="col-md-2">
                            <select name="category_id" class="form-select" required>
                                <?php foreach ($cats as $c): ?>
                                    <option value="<?php echo $c['category_id']; ?>"><?php echo htmlspecialchars($c['category_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="file" name="image" class="form-control" required>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-12 mb-2">
                            <textarea name="description" class="form-control" placeholder="Description of the item" rows="2" required></textarea>
                        </div>
                        <div class="col-md-12">
                            <button type="submit" name="add_item" class="btn btn-success w-100">Add Menu Item</button>
                        </div>
                    </div>
                </form>

                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Item ID</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Price (BDT)</th>
                            <th>Stock</th>
                            <th>Image</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($menu_items as $mi): ?>
                            <tr>
                                <td><?php echo $mi['item_id']; ?></td>
                                <td><?php echo htmlspecialchars($mi['item_name']); ?></td>
                                <td><?php echo htmlspecialchars($mi['category_name']); ?></td>
                                <td><?php echo number_format($mi['price'], 2); ?></td>
                                <td><?php echo $mi['stock_quantity']; ?></td>
                                <td><img src="../images/<?php echo htmlspecialchars($mi['image_path']); ?>" style="width:50px;height:50px;object-fit:cover;"></td>
                                <td>
                                    <form action="../php/manage_menu.php" method="POST" class="d-inline">
                                        <input type="hidden" name="item_id" value="<?php echo $mi['item_id']; ?>">
                                        <button type="submit" name="delete_item" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                    <form action="../php/manage_menu.php" method="POST" class="d-inline ms-1">
                                        <input type="hidden" name="item_id" value="<?php echo $mi['item_id']; ?>">
                                        <button type="submit" name="restock_item" class="btn btn-warning btn-sm">Restock</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Order Reporting -->
            <div class="tab-pane fade" id="reporting">
                <h4>Order Reporting</h4>
                <form class="row mb-4" method="GET">
                    <div class="col-md-4 mb-2">
                        <input type="date" name="start_date" class="form-control" value="<?php echo htmlspecialchars($start_date); ?>">
                    </div>
                    <div class="col-md-4 mb-2">
                        <input type="date" name="end_date" class="form-control" value="<?php echo htmlspecialchars($end_date); ?>">
                    </div>
                    <div class="col-md-4 mb-2">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                </form>
                <p><strong>Total Orders:</strong> <?php echo $rep['total_orders'] ?? 0; ?></p>
                <p><strong>Total Sales (BDT):</strong> <?php echo number_format($rep['total_sales'] ?? 0, 2); ?></p>

                <?php if ($top_item): ?>
                    <p><strong>Top Selling Item:</strong> <?php echo htmlspecialchars($top_item['item_name']); ?> (<?php echo $top_item['qty']; ?> sold)</p>
                <?php else: ?>
                    <p>No sales data found for this period.</p>
                <?php endif; ?>
            </div>

            <!-- Statistics -->
            <div class="tab-pane fade" id="statistics">
                <h4>Statistics</h4>
                <p>Visual representation of Items Sold, Employees Processing Orders, and Top Customers.</p>
                <div class="row">
                    <div class="col-md-4">
                        <h5>Items Sold</h5>
                        <canvas id="itemsChart"></canvas>
                        <p class="mt-2">Displays distribution of items sold overall.</p>
                    </div>
                    <div class="col-md-4">
                        <h5>Employees Processed Orders</h5>
                        <canvas id="employeesChart"></canvas>
                        <p class="mt-2">Shows how many orders each employee processed.</p>
                    </div>
                    <div class="col-md-4">
                        <h5>Top Customers by Spending</h5>
                        <canvas id="customersChart"></canvas>
                        <p class="mt-2">Shows which customers spent the most.</p>
                    </div>
                </div>
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    // Data from PHP
    const itemLabels = <?php echo json_encode($item_labels); ?>;
    const itemData = <?php echo json_encode($item_data); ?>;

    const empLabels = <?php echo json_encode($emp_labels); ?>;
    const empData = <?php echo json_encode($emp_data); ?>;

    const custLabels = <?php echo json_encode($cust_labels); ?>;
    const custData = <?php echo json_encode($cust_data); ?>;

    // Items Chart
    new Chart(document.getElementById('itemsChart'), {
        type: 'pie',
        data: {
            labels: itemLabels,
            datasets: [{
                data: itemData,
                backgroundColor: ['#F9A870','#f7b48f','#f5cc9b','#f3e4a8','#f1fbc0']
            }]
        }
    });

    // Employees Chart
    new Chart(document.getElementById('employeesChart'), {
        type: 'pie',
        data: {
            labels: empLabels,
            datasets: [{
                data: empData,
                backgroundColor: ['#F9A870','#f7b48f','#f5cc9b','#f3e4a8','#f1fbc0']
            }]
        }
    });

    // Customers Chart
    new Chart(document.getElementById('customersChart'), {
        type: 'pie',
        data: {
            labels: custLabels,
            datasets: [{
                data: custData,
                backgroundColor: ['#F9A870','#f7b48f','#f5cc9b','#f3e4a8','#f1fbc0']
            }]
        }
    });
    </script>
</body>
</html>
