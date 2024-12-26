<?php
// db_connect.php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cafebliss";

// Enable MySQLi error reporting to throw exceptions
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
