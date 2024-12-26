<?php
session_start();
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $phone_number = trim($_POST['phone_number']);
    $address = trim($_POST['address']);

    $errors = array();

    if (empty($full_name)) {
        $errors[] = "Full Name is required.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid Email format.";
    }

    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    if (empty($phone_number)) {
        $errors[] = "Phone Number is required.";
    }

    if (empty($address)) {
        $errors[] = "Address is required.";
    }

    // Default user_type
    $user_type = 'Customer';

    if (count($errors) == 0) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, phone_number, address, user_type) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $full_name, $email, $hashed_password, $phone_number, $address, $user_type);
            $stmt->execute();

            // Registration successful
            $_SESSION['user_id'] = $stmt->insert_id;
            $_SESSION['user_type'] = $user_type;
            $_SESSION['email'] = $email;

            $stmt->close();
            $conn->close();

            // Redirect to dashboard after successful registration
            header("Location: ../html/dashboard.php");
            exit();
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1062) {
                $errors[] = "Email already registered.";
            } else {
                $errors[] = "Error: " . $e->getMessage();
            }
        }
    }

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $conn->close();
        header("Location: ../html/reg.php");
        exit();
    }
} else {
    header("Location: ../html/reg.php");
    exit();
}
