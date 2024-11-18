
<?php
include('header.php');
require 'connection1.php'; // Include the database connection

$message = ''; // To store success/error messages

// Check if the course ID is provided
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $course_id = $_GET['id'];

    // Prepare delete query
    $sql = "DELETE FROM courses WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $course_id);

    // Execute the query
    if ($stmt->execute()) {
        $message = '<div class="alert success">Course deleted successfully!</div>';
        header("refresh:2;url=view_courses.php"); // Redirect back to the course view page after 2 seconds
        exit;
    } else {
        $message = '<div class="alert error">Error deleting course!</div>';
    }
} else {
    $message = '<div class="alert error">Invalid course ID!</div>';
}
?>

<div class="container">
    <main>
        <h2>Delete Course</h2>

        <?php echo $message; // Display success or error message ?>

        <p><a href="view_courses.php" class="btn btn-secondary">Back to Courses</a></p>
    </main>
</div>

<?php include('footer.php'); ?>
