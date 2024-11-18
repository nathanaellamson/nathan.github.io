<?php
session_start();
require 'connection1.php'; // Include your database connection

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Fetch the username and password from the form
    $student_username = mysqli_real_escape_string($conn, $_POST['student_username']); 
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Query to find the student using registration_number
    $query = "SELECT * FROM students WHERE registration_number = '$student_username'";
    $result = mysqli_query($conn, $query);

    // Check if student exists
    if (mysqli_num_rows($result) == 1) {
        $student = mysqli_fetch_assoc($result);

        // Check if the password matches
        if ($student['password'] == $password) {
            // Set session variables
            $_SESSION['registration_number'] = $student['registration_number'];
            $_SESSION['student_name'] = $student['first_name'] . ' ' . $student['last_name'];

            // Redirect to student dashboard
            header("Location: student_dashboard.php");
            exit();
        } else {
            // Store error message in session
            $_SESSION['message'] = "Invalid password!";
            $_SESSION['msg_type'] = "danger"; // Bootstrap's 'danger' class for red alert
            header("Location: index.php");
            exit();
        }
    } else {
        // Store error message in session
        $_SESSION['message'] = "No student found with that registration number!";
        $_SESSION['msg_type'] = "danger";
        header("Location: index.php?error=invalid credentials");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
