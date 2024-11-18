<?php

// Check if the user is logged in
if (!isset($_SESSION['email']) || empty($_SESSION['email'])) {
    // If not logged in, redirect to login page
    header("Location: login.php");
    exit();
}
?>