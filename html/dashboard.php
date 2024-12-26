<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require '../php/db_connect.php';

$user_id = $_SESSION['user_id'];
$email = $_SESSION['email'];

// Fetch categories
$categories = [];
$cat_result = $conn->query("SELECT category_id, category_name FROM categories");
if ($cat_result->num_rows > 0) {
    while ($cat_row = $cat_result->fetch_assoc()) {
        $categories[] = $cat_row;
    }
}

// Handle search and filter
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category_id = isset($_GET['category']) && $_GET['category'] !== '' ? (int)$_GET['category'] : null;

$sql = "SELECT * FROM menu_items WHERE 1=1";
$params = [];
$types = '';

if (!empty($search)) {
    $sql .= " AND item_name LIKE ?";
    $params[] = '%' . $search . '%';
    $types .= 's';
}

if (!empty($category_id)) {
    $sql .= " AND category_id = ?";
    $params[] = $category_id;
    $types .= 'i';
}

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$menu_items = [];
while ($row = $result->fetch_assoc()) {
    $menu_items[] = $row;
}

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Cafe Bliss</title>
    <link rel="stylesheet" href="../bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/global.css">


    <link rel="stylesheet" href="../css/dashboard.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-5">
        <h2 class="text-center">Welcome to Cafe Bliss, <?php echo htmlspecialchars($email); ?>!</h2>
        <p class="text-center">Explore our menu and place your order.</p>

        <!-- Search and Filter Form -->
        <form class="row mb-4" method="GET" action="dashboard.php">
            <div class="col-md-4 mb-2">
                <input type="text" name="search" class="form-control" placeholder="Search items..." value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="col-md-4 mb-2">
                <select name="category" class="form-select">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['category_id']; ?>" <?php echo ($category_id == $cat['category_id'])?'selected':''; ?>>
                            <?php echo htmlspecialchars($cat['category_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4 mb-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </form>

        <!-- Alert for item added to cart -->
        <?php if (isset($_GET['cart']) && $_GET['cart'] === 'added'): ?>
            <div id="cartAlert" class="alert alert-success text-center" role="alert">
                Item added to cart!
            </div>
        <?php endif; ?>

        <div class="row">
            <?php if (empty($menu_items)): ?>
                <div class="alert alert-info text-center">No menu items found matching your criteria.</div>
            <?php else: ?>
                <?php foreach ($menu_items as $item): ?>
                    <div class="col-md-3 mb-4">
                        <div class="card h-100">
                            <img src="../images/<?php echo htmlspecialchars($item['image_path']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($item['item_name']); ?>">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?php echo htmlspecialchars($item['item_name']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($item['description']); ?></p>
                                <p class="text-muted mt-auto">Price: BDT <?php echo htmlspecialchars($item['price']); ?></p>
                            </div>
                            <div class="card-footer">
                                <form action="../php/add_to_cart.php" method="POST">
                                    <input type="hidden" name="item_id" value="<?php echo $item['item_id']; ?>">
                                    <div class="input-group">
                                        <button type="button" class="btn btn-outline-secondary minus-btn">-</button>
                                        <input type="number" name="quantity" class="form-control quantity-field" value="1" min="1" max="10">
                                        <button type="button" class="btn btn-outline-secondary plus-btn">+</button>
                                        <button type="submit" class="btn btn-primary">Add to Cart</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script src="../bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/global.js" defer></script>
    <script>
    // Hide cart alert after 3 seconds
    const cartAlert = document.getElementById('cartAlert');
    if (cartAlert) {
        setTimeout(() => {
            cartAlert.style.display = 'none';
        }, 3000);
    }

    // Handle plus/minus buttons for quantity fields
    document.querySelectorAll('.minus-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const input = this.parentElement.querySelector('.quantity-field');
            let value = parseInt(input.value);
            if (value > 1) {
                value--;
                input.value = value;
            }
        });
    });

    document.querySelectorAll('.plus-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const input = this.parentElement.querySelector('.quantity-field');
            let value = parseInt(input.value);
            const max = parseInt(input.getAttribute('max')) || 10;
            if (value < max) {
                value++;
                input.value = value;
            }
        });
    });
    </script>
    
    <div class="footer">
    <p><strong>Café Bliss</strong></p>
    <p>123 Coffee Street, Bean Town, Dhaka 1215</p>
    <p>Open Daily: 7 AM - 8 PM</p>
    <p>Contact: (+880) 123-4567</p>
</div>

</body>
</html>
