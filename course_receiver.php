
<?php
session_start();
require 'connection1.php'; // Include the database connection

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $course_level = $_POST['course_level'];
    $course_name = $_POST['course_name'];
    $duration = $_POST['duration'];
    $student_limit = $_POST['student_limit'];

    // Prepare and execute SQL statement
    $stmt = $conn->prepare("INSERT INTO courses (course_level, course_name, duration, student_limit) VALUES (?, ?, ?, ?)");
    
    if ($stmt->execute([$course_level, $course_name, $duration, $student_limit])) {
        $_SESSION['message'] = "New course added successfully!";
        $_SESSION['msg_type'] = "success";
    } else {
        $_SESSION['message'] = "Error adding course.";
        $_SESSION['msg_type'] = "error";
    }
    
    // Redirect to the form
    header("Location: add_courseform.php");
    exit();
}
?>


<!-- In your HTML file (add_course.php) -->

