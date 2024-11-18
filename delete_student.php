
<?php
require 'connection1.php'; // Include your database connection

// Check if an ID is provided
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Convert the ID to an integer for safety

    // Perform delete operation
    $query = "DELETE FROM students WHERE id = $id"; // Adjust based on your database structure
    $result = mysqli_query($conn, $query);

    // Check if the delete was successful
    if ($result) {
        header("Location: view_students.php?message=Student deleted successfully"); // Redirect back to student list with a success message
    } else {
        echo "Error deleting student: " . mysqli_error($conn);
    }
} else {
    echo "No student ID provided.";
}

mysqli_close($conn); // Close the database connection
?>
