
<?php
include('header.php');
require 'connection1.php'; // Include the database connection

// Check if ID is provided
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch module data
    $sql = "SELECT * FROM modules WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $module = $result->fetch_assoc();
    } else {
        echo "Module not found.";
        exit;
    }
} else {
    echo "Invalid request.";
    exit;
}

// Update module if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $module_code = $_POST['module_code'];
    $module_name = $_POST['module_name'];
    $course_name = $_POST['course_name'];
    $course_level = $_POST['course_level'];
    $year_of_study = $_POST['year_of_study'];
    $module_type = $_POST['module_type'];
    $module_semester = $_POST['semester'];

    $update_sql = "UPDATE modules SET module_code=?, module_name=?, course=?, level=?, year_of_study=?, module_type=?, semester=? WHERE id=?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sssssssi", $module_code, $module_name, $course_name, $course_level, $year_of_study, $module_type, $module_semester, $id);
    
    if ($update_stmt->execute()) {
        header("Location: view_modules.php?message=Module updated successfully.");
        exit;
    } else {
        echo "Error updating module.";
    }
}
?>

<div class="container">
<?php include('sidebar_academic.php'); ?>
    <h2>Edit Module</h2>
    <form action="edit_module.php?id=<?php echo $id; ?>" method="POST">
        <div class="form-group">
            <label for="module_code">Module Code:</label>
            <input type="text" name="module_code" id="module_code" class="form-control" value="<?php echo $module['module_code']; ?>" required>
        </div>
        <div class="form-group">
            <label for="module_name">Module Name:</label>
            <input type="text" name="module_name" id="module_name" class="form-control" value="<?php echo $module['module_name']; ?>" required>
        </div>
        <div class="form-group">
            <label for="course_name">Course Name:</label>
            <input type="text" name="course_name" id="course_name" class="form-control" value="<?php echo $module['course']; ?>" required>
        </div>
        <div class="form-group">
            <label for="course_level">Course Level:</label>
            <select name="course_level" id="course_level" class="form-control">
                <option value="certificate" <?php if ($module['level'] == 'certificate') echo 'selected'; ?>>Certificate</option>
                <option value="diploma" <?php if ($module['level'] == 'diploma') echo 'selected'; ?>>Diploma</option>
                <option value="bachelor" <?php if ($module['level'] == 'bachelor') echo 'selected'; ?>>Bachelor</option>
                <option value="masters" <?php if ($module['level'] == 'masters') echo 'selected'; ?>>Masters</option>
            </select>
        </div>
        <div class="form-group">
            <label for="year_of_study">Year of Study:</label>
            <select name="year_of_study" id="year_of_study" class="form-control">
                <option value="first" <?php if ($module['year_of_study'] == 'first') echo 'selected'; ?>>First</option>
                <option value="second" <?php if ($module['year_of_study'] == 'second') echo 'selected'; ?>>Second</option>
                <option value="third" <?php if ($module['year_of_study'] == 'third') echo 'selected'; ?>>Third</option>
                <option value="fourth" <?php if ($module['year_of_study'] == 'fourth') echo 'selected'; ?>>Fourth</option>
            </select>
        </div>
        <div class="form-group">
            <label for="module_type">Module Type:</label>
            <select name="module_type" id="module_type" class="form-control">
                <option value="core" <?php if ($module['module_type'] == 'core') echo 'selected'; ?>>Core</option>
                <option value="elective" <?php if ($module['module_type'] == 'elective') echo 'selected'; ?>>Elective</option>
            </select>
        </div>
        <div class="form-group">
            <label for="module_semester">Semester:</label>
            <select name="semester" id="module_semester" class="form-control">
                <option value="one" <?php if ($module['semester'] == 'one') echo 'selected'; ?>>One</option>
                <option value="two" <?php if ($module['semester'] == 'two') echo 'selected'; ?>>Two</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Update Module</button>
        <a href="view_modules.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php include('footer.php'); ?>
