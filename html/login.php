<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Login - Cafe Bliss</title>
    <link rel="stylesheet" href="../bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/global.css">


    <link rel="stylesheet" href="../css/login.css">
</head>
<body>
    <!-- Navigation Bar -->
    <?php include 'navbar.php'; ?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 bg-dark p-4 rounded">
                <h2 class="text-center mb-4 text-warning">Login to Your Account</h2>
                <?php
                if (isset($_SESSION['errors'])) {
                    echo '<div class="alert alert-danger">';
                    foreach ($_SESSION['errors'] as $error) {
                        echo '<p>' . htmlspecialchars($error) . '</p>';
                    }
                    echo '</div>';
                    unset($_SESSION['errors']);
                }
                ?>
                <form action="../php/login_process.php" method="POST" id="loginForm">
                    <div class="mb-3">
                        <label for="loginEmail" class="form-label text-light">Email address</label>
                        <input type="email" class="form-control" id="loginEmail" name="email" required>
                        <div id="loginEmailFeedback" class="invalid-feedback">
                            Please enter your email.
                        </div>
                    </div>
                    <div class="mb-3 position-relative">
                        <label for="loginPassword" class="form-label text-light">Password</label>
                        <input type="password" class="form-control" id="loginPassword" name="password" required>
                        <span class="toggle-password" toggle="#loginPassword"><i class="bi bi-eye-slash"></i></span>
                        <div id="loginPasswordFeedback" class="invalid-feedback">
                            Please enter your password.
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                    <p class="text-center mt-3 text-light">Don't have an account? <a href="reg.php" class="text-warning">Register here</a></p>
                </form>
            </div>
        </div>
    </div>

    <script src="../bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/global.js" defer></script>
    <script src="../js/login.js"></script>

    <div class="footer">
    <p><strong>Café Bliss</strong></p>
    <p>123 Coffee Street, Bean Town, Dhaka 1215</p>
    <p>Open Daily: 7 AM - 8 PM</p>
    <p>Contact: (+880) 123-4567</p>
</div>

</body>
</html>
