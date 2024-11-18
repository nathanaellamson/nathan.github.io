
<?php
require 'connection1.php';

// Check if the necessary parameters are set
if (isset($_GET['course']) && isset($_GET['level']) && isset($_GET['year_of_study'])) {
    $course = $_GET['course'];
    $level = $_GET['level'];
    $year_of_study = $_GET['year_of_study'];

    // Display parameters for debugging
    echo "Course: $course, Level: $level, Year of Study: $year_of_study<br>";

    // Prepare and execute the statement
    $stmt = $conn->prepare("SELECT DISTINCT semester FROM modules WHERE course = ? AND level = ? AND year_of_study = ?");
    $stmt->bind_param("sss", $course, $level, $year_of_study);
    
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        echo '<option value="">Select Semester</option>';

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<option value='{$row['semester']}'>{$row['semester']}</option>";
            }
        } else {
            echo '<option value="">No semester available</option>';
        }
    } else {
        // Error during query execution
        echo '<option value="">Error executing query</option>';
    }

    $stmt->close();
} else {
    // If parameters are missing
    echo '<option value="">Invalid parameters provided</option>';
}

$conn->close();
?>
