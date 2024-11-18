<?php
session_start();
include('header.php');
require 'connection1.php'; // Database connection

// Check if the user is admin
if ($_SESSION['role'] !== 'Admin') {
    header('Location: login.php'); // Redirect non-admin users
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    
    // Auto-generate password (plain text)
    $password = bin2hex(random_bytes(2)); // 4-character random password

    // Insert new staff into the database with plain password
    $query = "INSERT INTO staff (full_name, email, password, role) VALUES ('$full_name', '$email', '$password', '$role')";
    if (mysqli_query($conn, $query)) {
        $_SESSION['message'] = "Staff member added successfully. The initial password is: $password";
        $_SESSION['msg_type'] = "success";
    } else {
        $_SESSION['message'] = "Error adding staff member: " . mysqli_error($conn);
        $_SESSION['msg_type'] = "error";
    }

    // Redirect to avoid form resubmission
    header('Location: add_staff.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Staff</title>
    <style>
        .container { max-width: 600px; margin: 50px auto; padding: 20px; background: #fff; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        h2 { text-align: center; }
        form { display: flex; flex-direction: column; gap: 15px; }
        input, select { padding: 10px; border-radius: 4px; border: 1px solid #ccc; }
        button { padding: 10px; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #218838; }
        .alert { padding: 10px; margin-bottom: 15px; }
        .success { background-color: #d4edda; color: #155724; }
        .error { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>

<div class="container">
<h2>Add New Staff</h2>
    <?php include "sidebar_admin.php"; ?>
    
    
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert <?= $_SESSION['msg_type']; ?>">
            <?= $_SESSION['message']; ?>
        </div>
    <?php 
        unset($_SESSION['message']); // Clear the message after displaying
        unset($_SESSION['msg_type']);
    endif; ?>

    <form action="add_staff.php" method="POST">
        <label for="full_name">Full Name:</label>
        <input type="text" id="full_name" name="full_name" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="role">Role:</label>
        <select id="role" name="role" required>
            <option value="Admin">Admin</option>
            <option value="Academic Head">Academic Head</option>
            <option value="Admission Officer">Admission Officer</option>
            <option value="Lecturer">Lecturer</option>
            <option value="Head of Department">Head of Department</option>
        </select>

        <button type="submit">Register Staff</button>
    </form>
</div>

</body>
</html>
