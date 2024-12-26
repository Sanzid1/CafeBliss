<?php
session_start();
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $errors = array();

    if (empty($email) || empty($password)) {
        $errors[] = "Email and password are required.";
    } else {
        $stmt = $conn->prepare("SELECT user_id, password, user_type FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($user_id, $hashed_password, $user_type);
            $stmt->fetch();
            if (password_verify($password, $hashed_password)) {
                // Login successful
                $_SESSION['user_id'] = $user_id;
                $_SESSION['user_type'] = $user_type;
                $_SESSION['email'] = $email;

                // Redirect based on user_type
                if ($user_type === 'Admin') {
                    header("Location: ../html/admin_dashboard.php");
                } elseif ($user_type === 'Employee') {
                    header("Location: ../html/employee_dashboard.php");
                } else {
                    // Default to customer dashboard
                    header("Location: ../html/dashboard.php");
                }
                exit();
            } else {
                $errors[] = "Incorrect password.";
            }
        } else {
            $errors[] = "No user found with that email.";
        }
        $stmt->close();
    }

    $conn->close();

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header("Location: ../html/login.php");
        exit();
    }
} else {
    header("Location: ../html/login.php");
    exit();
}
