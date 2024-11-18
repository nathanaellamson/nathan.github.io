
<?php
// Include your database connection file
require 'connection1.php';

// Check if the department ID is set
if (isset($_GET['id'])) {
    $department_id = $_GET['id'];

    // Prepare the SQL query to delete the department
    $stmt = $conn->prepare("DELETE FROM departments WHERE id = ?");
    $stmt->bind_param("i", $department_id);

    // Execute the query and check for success
    if ($stmt->execute()) {
        header("Location: view_departments.php?message=Department deleted successfully!");
    } else {
        header("Location: view_departments.php?message=Error deleting department: " . $stmt->error);
    }

    // Close the statement
    $stmt->close();
}

// Close the database connection
$conn->close();
?>
