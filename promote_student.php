
<?php
session_start();
require 'connection1.php';
include 'header.php'; 
// Ensure the user is logged in as H.O.D
if (!isset($_SESSION['department'])) {
    die("Access denied. Only H.O.D can perform this action.");
}

$department_id = $_SESSION['department'];
$results_message = "";
$error_message = "";
$current_academic_year = "";

// Function to update academic year and year of study
function updateAcademicYearAndYearOfStudy($registration_number, $current_academic_year, $current_year_of_study) {
    global $conn;

    // Split the academic year (e.g., "2023/2024")
    $years = explode("/", $current_academic_year);
    $first_year = (int)$years[0];
    $second_year = (int)$years[1];

    // Increment years for the new academic year
    $new_first_year = $first_year + 1;
    $new_second_year = $second_year + 1;
    $new_academic_year = $new_first_year . '/' . $new_second_year;

    // Increment the year of study
    $new_year_of_study = $current_year_of_study + 1;

    // Prepare the update statement
    $update_query = "UPDATE students SET academic_year = ?, year_of_study = ? WHERE registration_number = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("sis", $new_academic_year, $new_year_of_study, $registration_number);
    
    return $update_stmt->execute();
}

// Handle the update process for individual student
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['registration_number'])) {
        $registration_number = $_POST['registration_number'];

        // Check the student's current academic year and year of study
        $query = "
            SELECT s.academic_year, s.year_of_study
            FROM students AS s
            LEFT JOIN ue_results AS ue ON s.registration_number = ue.registration_number
            LEFT JOIN coursework_results AS cw ON s.registration_number = cw.registration_number
            LEFT JOIN sup_results AS sup ON s.registration_number = sup.registration_number
            WHERE s.registration_number = ?
              AND s.department = ?
              AND (ue.remarks != 'fail' OR ue.remarks IS NULL)
              AND (cw.remarks != 'fail' OR cw.remarks IS NULL)
              AND (sup.remarks != 'fail' OR sup.remarks IS NULL)
        ";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $registration_number, $department_id);
        $stmt->execute();
        $student = $stmt->get_result()->fetch_assoc();

        // If student is eligible for update, proceed
        if ($student) {
            $current_academic_year = $student['academic_year'];
            $current_year_of_study = $student['year_of_study'];

            // Update the student's academic year and year of study
            if (updateAcademicYearAndYearOfStudy($registration_number, $current_academic_year, $current_year_of_study)) {
                $results_message = "Student's academic year successfully updated.";
            } else {
                $error_message = "Error updating the student's academic year.";
            }
        } else {
            $error_message = "Student not found or is not eligible for update.";
        }
    } else {
        $error_message = "Please enter a registration number.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Academic Year</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php include 'sidebar_hod.php'; ?>
<main>
    <h2>Update Individual Student's Academic Year and Year of Study</h2>

    <?php if (!empty($results_message)): ?>
        <div class="results-message"><?php echo htmlspecialchars($results_message); ?></div>
    <?php endif; ?>

    <?php if (!empty($error_message)): ?>
        <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="registration_number">Registration Number:</label>
        <input type="text" id="registration_number" name="registration_number" required>
        <button type="submit" onclick="return confirm('Are you sure you want to update this student\'s academic year?');">
            Update Academic Year
        </button>
    </form>

    <a href="promote_students.php" class="link">Update all student</a>
</main>
<?php include 'footer.php'; ?>
</body>
</html>
