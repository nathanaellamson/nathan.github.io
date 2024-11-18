
<?php 
session_start();
include('header.php'); 
require 'connection1.php'; // Include the database connection

// Fetch courses from the database
$courses = [];
$query = "SELECT * FROM courses"; // Make sure your table name is correct
$result = $conn->query($query);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $courses[] = $row; // Store each course row in an array
    }
}
?>
<div class="container">
    <?php include('sidebar_academic.php'); ?>
    <main>
        <h2>Add New Module</h2>
        <?php // Display flash message
        if (isset($_SESSION['message'])) {
            echo '<div class="alert ' . $_SESSION['msg_type'] . '">' . $_SESSION['message'] . '</div>';
            unset($_SESSION['message']); // Clear message after displaying
        }
        ?>
        <form action="module_receiver.php" method="POST">
            <div class="form-section">
                <!-- First row: Course Level and Year of Study -->
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
                        <label for="year-of-study">Year of Study:</label>
                        <select id="year-of-study" name="year_of_study" required>
                            <option value="1">First Year</option>
                            <option value="2">Second Year</option>
                            <option value="3">Third Year</option>
                            <option value="4">Fourth Year</option>
                        </select>
                    </div>
                </div>
                
                <!-- Second row: Course Name (Fetched Dynamically via AJAX) -->
                <div class="form-row">
                    <div class="form-column">
                        <label for="course-name">Course Name:</label>
                        <select id="course-name" name="course_name" required>
                            <option value="">Select Course</option>
                        </select>
                    </div>
                </div>

                <!-- Third row: Module Name and Module Code -->
                <div class="form-row">
                    <div class="form-column">
                        <label for="module-name">Module Name:</label>
                        <input type="text" id="module-name" name="module_name" required>
                    </div>
                    <div class="form-column">
                        <label for="module-code">Module Code:</label>
                        <input type="text" id="module-code" name="module_code" required>
                    </div>
                </div>

                <!-- Fourth row: Module Type and Semester -->
                <div class="form-row">
                    <div class="form-column">
                        <label for="module-type">Module Type:</label>
                        <select id="module-type" name="module_type" required>
                            <option value="core">Core</option>
                            <option value="elective">Elective</option>
                        </select>
                    </div>
                    <div class="form-column">
                        <label for="semester">Semester:</label>
                        <select id="semester" name="semester" required>
                            <option value="1">One</option>
                            <option value="2">Two</option>
                        </select>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="form-row">
                    <button type="submit" class="submit-btn">Add Module</button>
                </div>
            </div>
        </form>
    </main>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- Include jQuery -->
<script>
$(document).ready(function() {
    // Handle Course Level and Year of Study change to fetch Course Name
    $('#course-level, #year-of-study').change(function() {
        var courseLevel = $('#course-level').val();
        var yearOfStudy = $('#year-of-study').val();
        
        if (courseLevel && yearOfStudy) {
            $.ajax({
                type: 'POST',
                url: 'fetch_courses_by_year.php', // This file fetches courses based on year and level
                data: { course_level: courseLevel, year_of_study: yearOfStudy },
                success: function(html) {
                    $('#course-name').html(html); // Populate course dropdown
                }
            });
        } else {
            $('#course-name').html('<option value="">Select Course</option>');
        }
    });
});
</script>

<?php include('footer.php'); ?>
