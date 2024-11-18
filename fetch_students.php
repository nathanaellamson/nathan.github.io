
<?php
require 'connection1.php';

// Check if all necessary parameters are set
if (isset($_GET['course'], $_GET['level'], $_GET['academic_year'], $_GET['year_of_study'], $_GET['module'])) {
    $course = $_GET['course'];
    $level = $_GET['level'];
    $academic_year = $_GET['academic_year'];
    $year_of_study = $_GET['year_of_study'];
    $module = $_GET['module'];

    // Prepare the SQL query with placeholders for binding
    $query = "SELECT registration_number, first_name, middle_name 
              FROM students 
              WHERE course = ? 
                AND level = ? 
                AND year_of_study = ?
                AND academic_year = ?"; // Include academic_year in the WHERE clause

    // Prepare and bind the parameters securely
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssss", $course, $level, $year_of_study, $academic_year);
    $stmt->execute();
    $result = $stmt->get_result();

    // Display the results in a table
    if ($result->num_rows > 0) {
        echo '<table>';
        echo '<tr><th>Registration Number</th><th>Name</th><th>Score</th></tr>';
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row['registration_number']}</td>";
            echo "<td>{$row['first_name']} {$row['middle_name']}</td>";
            echo "<td><input type='text' name='scores[{$row['registration_number']}]' /></td>";
            echo "</tr>";
        }
        echo '</table>';
    } else {
        echo "<p>No students found for the selected parameters.</p>";
    }

    // Close the statement
    $stmt->close();
} else {
    echo "Error: Missing parameters.";
}

$conn->close();
?>
