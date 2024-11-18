
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
        header('Location: apload_sup_results.php'); // Update redirect to SUP results page
        exit;
    }

    $module_details = $result->fetch_assoc();
    $module_code = $module_details['module_code'];
    $module_name = $module_details['module_name'];
    $level = $module_details['level'];
    $year_of_study = $module_details['year_of_study'];
    $semester = $module_details['semester'];

    // Initialize the prepared statement for inserting results
    $stmt = $conn->prepare("
        INSERT INTO sup_results (registration_number, module_code, module_name, score, remarks, academic_year, year_of_study, semester) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    if (!$stmt) {
        $_SESSION['error'] = 'Error preparing statement: ' . $conn->error;
        header('Location: apload_sup_results.php'); // Update redirect to UE results page
        exit;
    }

    // Initialize an error message variable
    $error_message = '';

    foreach ($_POST['scores'] as $reg_number => $score) {
        $reg_number = trim(urldecode($reg_number));
        $score = (int)$score; // Ensure score is treated as an integer

        // Check for the existing combination of both registration_number and module_code
        $check_duplicate = $conn->prepare("SELECT 1 FROM sup_results WHERE registration_number = ? AND module_code = ?");
        $check_duplicate->bind_param("ss", $reg_number, $module_code);
        $check_duplicate->execute();
        $check_duplicate->store_result();

        if ($check_duplicate->num_rows > 0) {
            // Set error message for the first duplicate entry found
            $error_message = "Error uploading result! Duplicate entry for $reg_number and module code $module_code.";
            continue; // Skip insertion for this entry
        }

        // Fetch the current remarks for the module score in the ue_results table
        $remarks_query = $conn->prepare("SELECT remarks FROM ue_results WHERE registration_number = ? AND module_code = ? LIMIT 1");
        $remarks_query->bind_param("ss", $reg_number, $module_code);
        $remarks_query->execute();
        $remarks_result = $remarks_query->get_result();

        if ($remarks_result->num_rows > 0) {
            $remarks_row = $remarks_result->fetch_assoc();
            $module_remarks = $remarks_row['remarks'];

            // Only insert the SUP result if the score is greater than 0 and the module remarks is 'Fail'
            if ($score > 0 && $module_remarks === 'Fail') {
                // Store parameters in variables to pass by reference
                $score_param = $score;
                $remarks_param = 'SUP';
                $academic_year_param = $academic_year;
                $year_of_study_param = $year_of_study;
                $semester_param = $semester;

                // Bind parameters by reference
                $stmt->bind_param("sssissis", 
                    $reg_number, 
                    $module_code, 
                    $module_name, 
                    $score_param, // Use the variable
                    $remarks_param, // Use the variable
                    $academic_year_param, // Use the variable
                    $year_of_study_param, // Use the variable
                    $semester_param // Use the variable
                );

                if (!$stmt->execute()) {
                    $error_message = "Error for $reg_number: " . $stmt->error;
                    break; // Exit the loop on error
                }
            }
        }
        $remarks_query->close(); // Close the remarks query
    }

    // Set success or error message in session
    if (empty($error_message)) {
        $_SESSION['message'] = 'SUP results uploaded successfully with remarks!';
    } else {
        $_SESSION['error'] = $error_message;
    }

    // Close statements
    $stmt->close();
    $check_duplicate->close();
} else {
    $_SESSION['error'] = 'Invalid request method.';
}

// Redirect back to the UE results form page
header('Location: apload_sup_form.php'); // Update redirect to UE results page
exit;

$conn->close();
?>
