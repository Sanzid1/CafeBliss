<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Registration - Cafe Bliss</title>
    <link rel="stylesheet" href="../bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/reg.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 bg-dark p-4 rounded">
                <h2 class="text-center mb-4 text-warning">Create an Account</h2>
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
                <form action="../php/register.php" method="POST" id="registrationForm">
                    <div class="mb-3">
                        <label for="fullName" class="form-label text-light">Full Name</label>
                        <input type="text" class="form-control" id="fullName" name="full_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label text-light">Email address</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                        <div id="emailFeedback" class="invalid-feedback">
                            Please provide a valid email.
                        </div>
                    </div>
                    <div class="mb-3 position-relative">
                        <label for="password" class="form-label text-light">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <span class="toggle-password" toggle="#password"><i class="bi bi-eye-slash"></i></span>
                        <div id="passwordFeedback" class="invalid-feedback">
                            Password must be at least 6 characters.
                        </div>
                    </div>
                    <div class="mb-3 position-relative">
                        <label for="confirmPassword" class="form-label text-light">Confirm Password</label>
                        <input type="password" class="form-control" id="confirmPassword" name="confirm_password" required>
                        <span class="toggle-password" toggle="#confirmPassword"><i class="bi bi-eye-slash"></i></span>
                        <div id="confirmPasswordFeedback" class="invalid-feedback">
                            Passwords do not match.
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="phoneNumber" class="form-label text-light">Phone Number</label>
                        <input type="text" class="form-control" id="phoneNumber" name="phone_number" required>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label text-light">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                    </div>
                    <!-- User type selection removed -->
                    <button type="submit" class="btn btn-primary w-100">Register</button>
                    <p class="text-center mt-3 text-light">Already have an account? <a href="login.php" class="text-warning">Login here</a></p>
                </form>
            </div>
        </div>
    </div>

    <script src="../bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/reg.js"></script>
</body>
</html>
