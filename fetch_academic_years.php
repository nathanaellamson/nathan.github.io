
<?php
require 'connection1.php';

$course = $_GET['course'];
$level = $_GET['level'];
$result = $conn->query("SELECT DISTINCT academic_year FROM students WHERE course = '$course' AND level = '$level'");
echo '<option value="">Select Academic Year</option>';
while ($row = $result->fetch_assoc()) {
    echo "<option value='{$row['academic_year']}'>{$row['academic_year']}</option>";
}
$conn->close();
?>
