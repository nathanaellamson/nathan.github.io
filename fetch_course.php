
<?php
require 'connection1.php';

if (isset($_POST['course_level'])) {
    $level = $_POST['course_level'];
    $query = "SELECT DISTINCT course_name FROM courses WHERE course_level = '$level'"; // Ensure 'name' is the correct column
    $result = $conn->query($query);
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<option value='".$row['course_name']."'>".$row['course_name']."</option>";
        }
    } else {
        echo "<option value=''>No courses available</option>";
    }
}
?>
