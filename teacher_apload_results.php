<?php
session_start(); // Start the session
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'connection1.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $module_id = $_POST['module'];
    $academic_year = $_POST['academic_year'];

    // Fetch module details using prepared statement
    $module_query = $conn->prepare("SELECT module_code, module_name, course, level, year_of_study, semester FROM modules WHERE id = ?");
    $module_query->bind_param("i", $module_id);
    $module_query->execute();
    $result = $module_query->get_result();

    if ($result->num_rows == 0) {
        $_SESSION['error'] = 'Error: Module not found.';
        header('Location: apload_coursework.php');
        exit;
    }

    $module_details = $result->fetch_assoc();
    $module_code = $module_details['module_code'];
    $module_name = $module_details['module_name'];
    $level = $module_details['level'];
    $year_of_study = $module_details['year_of_study'];
    $semester = $module_details['semester'];

    $stmt = $conn->prepare("
        INSERT INTO coursework_results (registration_number, module_code, module_name, score, remarks, academic_year, year_of_study, semester) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    if (!$stmt) {
        $_SESSION['error'] = 'Error preparing statement: ' . $conn->error;
        header('Location: apload_coursework.php');
        exit;
    }

    // Initialize an error message variable
    $error_message = '';

    foreach ($_POST['scores'] as $reg_number => $score) {
        $reg_number = trim(urldecode($reg_number));

        // Check for the existing combination of both registration_number and module_code
        $check_duplicate = $conn->prepare("SELECT 1 FROM coursework_results WHERE registration_number = ? AND module_code = ?");
        $check_duplicate->bind_param("ss", $reg_number, $module_code);
        $check_duplicate->execute();
        $check_duplicate->store_result();

        if ($check_duplicate->num_rows > 0) {
            // Set error message for the first duplicate entry found
            $error_message = "Error aploading result! 
            Duplicate entry for $reg_number and module code $module_code.";
            continue; // Skip insertion for this entry
        }

        $remarks = ($level == 'bachelor' && $score >= 16) || (($level == 'certificate' || $level == 'diploma') && $score >= 20) ? 'Pass' : 'Fail';

        $stmt->bind_param("sssissis", $reg_number, $module_code, $module_name, $score, $remarks, $academic_year, $year_of_study, $semester);

        if (!$stmt->execute()) {
            $error_message = "Error for $reg_number: " . $stmt->error;
            break; // Exit the loop on error
        }
    }

    // Set success or error message in session
    if (empty($error_message)) {
        $_SESSION['message'] = 'Results uploaded successfully with remarks!';
    } else {
        $_SESSION['error'] = $error_message;
    }

    // Close statements
    $stmt->close();
    $check_duplicate->close();
} else {
    $_SESSION['error'] = 'Invalid request method.';
}

// Redirect back to the form page
header('Location: teacher_apload_cw.php');
exit;

$conn->close();
?>
