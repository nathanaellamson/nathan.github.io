
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
    $module_name = $_POST['module_name'];
    $module_code = $_POST['module_code'];
    $module_type = $_POST['module_type'];
    $year_of_study = $_POST['year_of_study'];
    $semester = $_POST['semester'];

    // Prepare and execute SQL statement
    $stmt = $conn->prepare("INSERT INTO modules (level, course, module_name, module_code, module_type, year_of_study, semester) VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    if ($stmt->execute([$course_level, $course_name, $module_name, $module_code, $module_type, $year_of_study, $semester])) {
        $_SESSION['message'] = "New module added successfully!";
        $_SESSION['msg_type'] = "success";
    } else {
        $_SESSION['message'] = "Error adding module.";
        $_SESSION['msg_type'] = "error";
    }
    
    // Redirect to the add module form
    header("Location: add_moduleform.php");
    exit();
}
?>
