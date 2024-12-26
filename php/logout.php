<?php
// logout.php
session_start();
session_destroy();
header("Location: ../html/login.php");
exit();
?>
