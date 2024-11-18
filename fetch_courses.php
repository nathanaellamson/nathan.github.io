
<?php
session_start(); // Start the session to access session variables
require 'connection1.php';

// Ensure the department session variable is set
if (isset($_SESSION['department'])) {
    $hod_department = $_SESSION['department'];

    // Prepare the SQL query using the department session variable
    $stmt = $conn->prepare("SELECT DISTINCT course FROM students WHERE department = ?");
    $stmt->bind_param("s", $hod_department);
    $stmt->execute();
    $result = $stmt->get_result();

    echo '<option value="">Select Course</option>';
    while ($row = $result->fetch_assoc()) {
        echo "<option value='{$row['course']}'>{$row['course']}</option>";
    }

    $stmt->close();
} else {
    echo '<option value="">Department not set</option>';
}

$conn->close();
?>
