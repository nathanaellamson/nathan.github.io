<?php

// Check if the user is logged in
if (!isset($_SESSION['email']) || empty($_SESSION['email'])) {
    // If not logged in, redirect to login page
    header("Location: login.php");
    exit();
}
?>
<?php
require 'connection1.php'; // Include the database connection

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Delete module
    $sql = "DELETE FROM modules WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: view_modules.php?message=Module deleted successfully.");
        exit;
    } else {
        echo "Error deleting module.";
    }
} else {
    echo "Invalid request.";
}
?>
