
<?php
require 'connection1.php';

$course = $_GET['course'];
$level = $_GET['level'];
$academic_year = $_GET['academic_year'];
$result = $conn->query("SELECT DISTINCT year_of_study FROM students WHERE course = '$course' AND level = '$level' AND academic_year = '$academic_year'");
echo '<option value="">Select Year of Study</option>';
while ($row = $result->fetch_assoc()) {
    echo "<option value='{$row['year_of_study']}'>{$row['year_of_study']}</option>";
}
$conn->close();
?>
