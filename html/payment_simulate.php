<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// This is a dummy page to simulate payment confirmation
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Simulation - Cafe Bliss</title>
    <link rel="stylesheet" href="../bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/global.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-5">
        <h2 class="text-center mb-4">Simulate Payment</h2>
        <div class="card p-4">
            <p>Enter your payment details (fake):</p>
            <form>
                <div class="mb-3">
                    <input type="text" class="form-control" placeholder="Bkash/Bank Reference">
                </div>
                <button type="submit" class="btn btn-primary w-100">Confirm Payment</button>
            </form>
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
