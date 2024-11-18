
<?php
session_start();
require 'connection1.php';
include 'header.php'; 
// Ensure the user is logged in as H.O.D
if (!isset($_SESSION['department'])) {
    die("Access denied. Only H.O.D can perform this action.");
}

$department_id = $_SESSION['department'];

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

// Handle the update process when form is submitted
$results_message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $query = "
        SELECT DISTINCT s.registration_number, s.academic_year, s.year_of_study
        FROM students AS s
        LEFT JOIN ue_results AS ue ON s.registration_number = ue.registration_number
        LEFT JOIN coursework_results AS cw ON s.registration_number = cw.registration_number
        LEFT JOIN sup_results AS sup ON s.registration_number = sup.registration_number
        WHERE s.department = ? 
          AND (ue.remarks != 'fail' OR ue.remarks IS NULL)
          AND (cw.remarks != 'fail' OR cw.remarks IS NULL)
          AND (sup.remarks != 'fail' OR sup.remarks IS NULL)
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $department_id);
    $stmt->execute();
    $eligible_students = $stmt->get_result();

    // Process each eligible student
    $updated_students = 0;
    while ($student = $eligible_students->fetch_assoc()) {
        if (updateAcademicYearAndYearOfStudy($student['registration_number'], $student['academic_year'], $student['year_of_study'])) {
            $updated_students++;
        }
    }

    $results_message = $updated_students . " students' academic years successfully updated.";
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
    <h2>Update Students' Academic Year and Year of Study</h2>

    <?php if (!empty($results_message)): ?>
        <div class="results-message"><?php echo htmlspecialchars($results_message); ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <button type="submit" onclick="return confirm('Are you sure you want to update the academic year for all eligible students?');">
            Update Academic Year
        </button>
    </form>
    
    <a href="promote_student.php" class="link">Update individual student</a>
</main>
<?php include 'footer.php'; ?>

</body>
</html>
