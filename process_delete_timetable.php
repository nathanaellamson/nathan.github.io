
<?php
require 'connection1.php';

// Check if the ID is set
if (isset($_POST['id'])) {
    $id = $_POST['id'];

    // Delete the timetable entry
    $delete_query = "DELETE FROM timetable WHERE id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param('i', $id);

    if ($stmt->execute()) {
        // Redirect or show success message
        header('Location: view_timetable.php'); // Redirect after successful deletion
        exit();
    } else {
        echo "Error deleting timetable: " . $conn->error;
    }
}
