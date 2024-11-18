
<?php
require 'connection1.php';

$course = $_GET['course'];
$result = $conn->query("SELECT DISTINCT level FROM students WHERE course = '$course'");
echo '<option value="">Select Level</option>';
while ($row = $result->fetch_assoc()) {
    echo "<option value='{$row['level']}'>{$row['level']}</option>";
}
$conn->close();
?>
