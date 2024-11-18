
<?php
include('header.php');
require 'connection1.php'; // Include the database connection

$message = ''; // To store success/error messages

// Check if the course ID is provided
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $course_id = $_GET['id'];

    // Fetch course data
    $sql = "SELECT * FROM courses WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $course = $result->fetch_assoc(); // Get course data
    } else {
        $message = '<div class="alert error">Course not found!</div>';
        exit;
    }
} else {
    $message = '<div class="alert error">Invalid course ID!</div>';
    exit;
}

// Handle form submission for updating the course
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_name = $_POST['course_name'];
    $course_level = $_POST['course_level'];
    $duration = $_POST['duration'];
    $student_limit = $_POST['student_limit'];

    // Update query
    $sql = "UPDATE courses SET course_name = ?, course_level = ?, duration = ?, student_limit = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssiii", $course_name, $course_level, $duration, $student_limit, $course_id);

    if ($stmt->execute()) {
        $message = '<div class="alert success">Course updated successfully!</div>';
    } else {
        $message = '<div class="alert error">Error updating course!</div>';
    }
}
?>

<div class="container">
<?php include('sidebar_academic.php'); ?>
    <main>
        <h2>Edit Course</h2>

        <?php echo $message; // Display success or error message ?>

        <form action="edit_course.php?id=<?php echo $course_id; ?>" method="POST">
            <div class="form-group">
                <label for="course_name">Course Name</label>
                <input type="text" name="course_name" id="course_name" class="form-control" value="<?php echo $course['course_name']; ?>" required>
            </div>

            <div class="form-group">
                <label for="course_level">Course Level</label>
                <select name="course_level" id="course_level" class="form-control" required>
                    <option value="certificate" <?php if ($course['course_level'] == 'certificate') echo 'selected'; ?>>Certificate</option>
                    <option value="diploma" <?php if ($course['course_level'] == 'diploma') echo 'selected'; ?>>Diploma</option>
                    <option value="bachelor" <?php if ($course['course_level'] == 'bachelor') echo 'selected'; ?>>Bachelor</option>
                    <option value="masters" <?php if ($course['course_level'] == 'masters') echo 'selected'; ?>>Masters</option>
                </select>
            </div>

            <div class="form-group">
                <label for="duration">Duration (Years)</label>
                <input type="number" name="duration" id="duration" class="form-control" value="<?php echo $course['duration']; ?>" required>
            </div>

            <div class="form-group">
                <label for="student_limit">Student Limit</label>
                <input type="number" name="student_limit" id="student_limit" class="form-control" value="<?php echo $course['student_limit']; ?>" required>
            </div>

            <button type="submit" class="btn btn-primary">Update Course</button>
            <a href="view_courses.php" class="btn btn-secondary">Cancel</a>
        </form>
    </main>
</div>

<?php include('footer.php'); ?>
