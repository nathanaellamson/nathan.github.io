
<!-- add-course.php -->
<?php
session_start();
include('header.php'); ?>
<div class="container">
    <?php include('sidebar_academic.php'); ?>
    <main>
        <h2>Add New Course</h2>
  <?php      // Display flash message
if (isset($_SESSION['message'])) {
    echo '<div class="alert ' . $_SESSION['msg_type'] . '">' . $_SESSION['message'] . '</div>';
    unset($_SESSION['message']); // Clear message after displaying
}
?>
        <form action="course_receiver.php" method="POST">
            <div class="form-section">
                <!-- First row with two columns -->
                <div class="form-row">
                    <div class="form-column">
                        <label for="course-level">Course Level:</label>
                        <select id="course-level" name="course_level" required>
                            <option value="">Select Level</option>
                            <option value="certificate">Certificate</option>
                            <option value="diploma">Diploma</option>
                            <option value="bachelor">Bachelor</option>
                            <option value="masters">Masters</option>
                        </select>
                    </div>
                    <div class="form-column">
                        <label for="course-name">Course Name:</label>
                        <input type="text" id="course-name" name="course_name" required>
                    </div>
                </div>
                <!-- Second row with two columns -->
                <div class="form-row">
                    <div class="form-column">
                        <label for="duration">Duration (in years):</label>
                        <input type="number" id="duration" name="duration" min="1" required>
                    </div>
                    <div class="form-column">
                        <label for="student-limit">Student Limit:</label>
                        <input type="number" id="student-limit" name="student_limit" min="1" required>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="form-row">
                    <button type="submit" class="submit-btn">Add Course</button>
                </div>
            </div>
        </form>
        <?php
?>
    </main>
</div>
<?php include('footer.php'); ?>
