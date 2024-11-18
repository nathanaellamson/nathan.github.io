
<?php
require 'connection1.php'; // Database connection

if (isset($_POST['course_level']) && isset($_POST['year_of_study'])) {
    $course_level = $_POST['course_level'];
    $year_of_study = $_POST['year_of_study'];

    // Query to fetch courses based on course level and year of study
    $query = "SELECT course_name FROM courses WHERE course_level = ? AND duration >= ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('si', $course_level, $year_of_study);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo '<option value="">Select Course</option>';
        while ($row = $result->fetch_assoc()) {
            echo '<option value="' . $row['course_name'] . '">' . $row['course_name'] . '</option>';
        }
    } else {
        echo '<option value="">No Courses Available</option>';
    }
}
?>
